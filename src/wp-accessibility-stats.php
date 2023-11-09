<?php
/**
 * Track accessibility stats.
 *
 * @category Issues
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-accessibility/
 */

/**
 * Define Stats collection post type.
 */
function wpa_post_type() {
	$labels = array(
		'name'               => 'WP Accessibility Stats Record',
		'singular_name'      => 'Stats',
		'menu_name'          => 'Accessibility Stats',
		'add_new'            => __( 'Add New', 'wp-accessibility' ),
		'add_new_item'       => __( 'Create New', 'wp-accessibility' ),
		'edit_item'          => __( 'Modify', 'wp-accessibility' ),
		'new_item'           => __( 'New', 'wp-accessibility' ),
		'view_item'          => __( 'View', 'wp-accessibility' ),
		'search_items'       => __( 'Search', 'wp-accessibility' ),
		'not_found'          => __( 'No stats found', 'wp-accessibility' ),
		'not_found_in_trash' => __( 'No stats found in trash', 'wp-accessibility' ),
		'parent_item_colon'  => '',
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-universal-access',
		'query_var'           => true,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor', 'custom-fields' ),
	);
	register_post_type( 'wpa-stats', $args );
}
add_action( 'init', 'wpa_post_type' );

/**
 * Register taxonomies on WP Accessibility stats posts.
 */
function wpa_taxonomies() {
	register_taxonomy(
		'wpa-stats-type',
		// Internal name = machine-readable taxonomy name.
		array( 'wpa-stats' ),
		array(
			'hierarchical' => true,
			'label'        => __( 'WP Accessibility Statistic Type', 'wp-accessibility' ),
			'query_var'    => true,
			'rewrite'      => array( 'slug' => 'wpa-stats-type' ),
		)
	);
}
add_action( 'init', 'wpa_taxonomies', 0 );


/**
 * Register statistics.
 *
 * @param array      $stats Stats data for a page. Array of tests & results.
 * @param string     $title Title for the stats record. Usually a relative URL.
 * @param string     $type Type of stat: view or action.
 * @param int|string $post_ID ID of the post if singular.
 */
function wpa_add_stats( $stats, $title, $type = 'view', $post_ID = 0 ) {
	$stats  = json_encode( $stats );
	$exists = wpa_get_post_by_title( $title );
	if ( $exists ) {
		if ( 'action' === $type ) {
			// Log all on/off states for toolbar.
			add_post_meta( $exists, 'wpa_event', $stats );
		} else {
			$old_stats = get_post( $exists )->post_content;
			if ( $stats !== $old_stats ) {
				// stats have changed; record the change.
				add_post_meta( $exists, 'wpa_event', $stats );
			}
		}
		return array( 'metadata logged' );
	} else {
		$post = array(
			'post_title'   => $title,
			'post_content' => $stats,
			'post_status'  => 'publish',
			'post_name'    => sanitize_title( $title ),
			'post_date'    => current_time( 'Y-m-d H:i:s' ),
			'post_type'    => 'wpa-stats',
		);
		$stat = wp_insert_post( $post );
		wp_set_object_terms( $stat, array( $type ), 'wpa-stats-type' );
		if ( $post_ID ) {
			// Set up relationships between stats and posts.
			update_post_meta( $stat, '_wpa_post_id', $post_ID );
			update_post_meta( $post, '_wpa_stat_id', $stat );
		}
		return array( 'stats logged' );
	}
}

/**
 * Get stats post by title.
 *
 * @param mixed $title The title of a stats post.
 *
 * @return int
 */
function wpa_get_post_by_title( $title ) {
	global $wpdb;
	$post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='wpa-stats'", $title ) );
	if ( $post ) {
		return $post;
	}

	return false;
}

/**
 * Attempts to correctly identify the current URL.
 *
 * @return string
 */
function wpa_get_current_url() {
	if ( is_admin() ) {
		return '';
	}
	global $wp, $wp_rewrite;
	$args = array();
	if ( isset( $_GET['page_id'] ) ) {
		$args = array( 'page_id' => absint( $_GET['page_id'] ) );
	}
	$current_url = home_url( add_query_arg( $args, $wp->request ) );

	if ( $wp_rewrite->using_index_permalinks() && false === strpos( $current_url, 'index.php' ) ) {
		$current_url = str_replace( home_url(), home_url( '/' ) . 'index.php', $current_url );
	}

	if ( $wp_rewrite->using_permalinks() ) {
		$current_url = trailingslashit( $current_url );
	}
	/**
	 * Filter the URL returned for the current URL.
	 *
	 * @hook wpa_get_current_url
	 *
	 * @param {string} $current_url Current URL according to wp_rewrite.
	 *
	 * @return {string}
	 */
	$current_url = apply_filters( 'wpa_get_current_url', $current_url );

	return $current_url;
}

/**
 * Handle AJAX request to record stats.
 *
 * @return void
 */
function wpa_stats_action() {
	if ( isset( $_REQUEST['action'] ) && 'wpa_stats_action' === $_REQUEST['action'] ) {
		$security = $_REQUEST['security'];
		if ( ! wp_verify_nonce( $security, 'wpa-stats-action' ) ) {
			wp_send_json( array( 'failed' => $_REQUEST ) );
		}
		$stats   = map_deep( $_REQUEST['stats'], 'sanitize_text_field' );
		$post_id = absint( $_REQUEST['post_id'] );
		$title   = ( wpa_is_url( $_REQUEST['title'] ) ) ? esc_url( $_REQUEST['title'] ) : sanitize_text_field( $_REQUEST['title'] );
		$type    = ( 'view' === $_REQUEST['type'] ) ? 'view' : 'event';
		// Add timestamp for this stat.
		$stats['timestamp'] = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

		$response = wpa_add_stats( $stats, $title, $type, $post_id );
		wp_send_json( $response );
	} else {
		wp_send_json( array( 'no-request' => $_REQUEST ) );
	}
}
add_action( 'wp_ajax_wpa_stats_action', 'wpa_stats_action' );
add_action( 'wp_ajax_nopriv_wpa_stats_action', 'wpa_stats_action' );

/**
 * Register the WP Accessibility dashboard stats widget.
 *
 * @return void
 */
function wpa_dashboard_widget() {
	wp_add_dashboard_widget( 'wpa_dashboard_widget_stats', __( 'WP Accessibility Stats', 'wp-accessibility' ), 'wpa_dashboard_widget_stats_handler' );
}
add_action( 'wp_dashboard_setup', 'wpa_dashboard_widget' );

/**
 * Output the WP Accessibility stats widget.
 *
 * @return void
 */
function wpa_dashboard_widget_stats_handler() {
	echo '<p>' . __( 'WP Accessibility is tracking accessibility changes it makes to your site, and recording when vistors toggle the font size and high contrast options. No personally identifying data is stored.', 'wp-accessibility' ) . '</p>';
	wpa_get_stats( 'view' );
	wpa_get_stats( 'event' );
}

/**
 * Get stats data for WP Accessibility.
 *
 * @param string $type Type of stats to fetch. 'view' or 'event'.
 *
 * @return void
 */
function wpa_get_stats( $type = 'view' ) {
	$query = array(
		'post_type'  => 'wpa-stats',
		'numberpost' => 5,
		'orderby'    => 'date',
		'order'      => 'desc',
		'tax_query'  => array(
			array(
				'taxonomy' => 'wpa-stats-type',
				'field'    => 'slug',
				'terms'    => $type,
			),
		),
	);

	$posts = new WP_Query( $query );
	if ( 'view' === $type ) {
		echo '<div class="activity-block"><h3>' . __( 'Accessibility Fixes', 'wp-accessibility' ) . '</h3><ul>';
	} else {
		echo '<div class="activity-block"><h3>' . __( 'User Actions', 'wp-accessibility' ) . '</h3><ul>';
	}

	foreach ( $posts->posts as $post ) {
		$post_ID  = $post->ID;
		$data     = json_decode( $post->post_content );
		$history  = get_post_meta( $post_ID, 'wpa_event' );
		$relative = get_post_meta( $post_ID, '_wpa_post_id', true );
		echo '<li><div class="wpa-header"><h3>' . gmdate( get_option( 'date_format' ), $data->timestamp ) . '<br />' . gmdate( get_option( 'time_format' ), $data->timestamp ) . '</h3>';

		$post_title = ( $relative ) ? get_the_title( $relative ) : 'User action';
		echo '<p><strong>' . $post_title . '</strong></p></div>';

		$line = '';
		if ( 'event' === $type ) {
			$first = ( property_exists( $data, 'contrast' ) ) ? 'Contrast' : 'Font size';
			$date  = gmdate( 'Y-m-d H:i', $data->timestamp );
			// translators: Control type; date enabled.
			$line = '<li>' . sprintf( __( '%1$s enabled at %2$s', 'wp-accessibility' ), $first, $date ) . '</li>';
			foreach ( $history as $h ) {
				$has_font_size = ( property_exists( $h, 'fontsize' ) ) ? 'Font size' : false;
				$toggle_type   = ( property_exists( $h, 'contrast' ) ) ? 'Contrast' : $has_font_size;
				if ( $toggle_type ) {
					$change      = ( 'Contrast' === $toggle_type ) ? $h->contrast : $h->fontsize;
					$change_date = gmdate( 'Y-m-d H:i', $h->timestamp );
					$line       .= '<li>' . $toggle_type . ' ' . $change . ' at ' . $change_date . '</li>';
				}
			}
		} else {
			foreach ( $data as $d ) {
				if ( is_array( $d ) ) {
					$key   = $d[0];
					$count = $d[1];
					// translators: count of fixes, type of item fixed.
					$stat = sprintf( __( '%1$d %2$s fixed', 'wp-accessibility' ), $count, '<strong>' . $key . '</strong>' );
				} else {
					$key   = $d;
					$count = 1;
					// translators: type of item fixed.
					$stat = sprintf( __( '%s fixed', 'wp-accessibility' ), '<strong>' . $key . '</strong>' );
				}
				$line .= '<li>' . $stat . '</li>';
			}
		}

		echo '<ul class="stats">' . $line . '</ul></li>';
	}
	echo '</ul></div>';
}

/**
 * Format stats for brief display.
 *
 * @param string $type Type of stat to display.
 * @param array  $data Array of saved data.
 * @param array  $history History for this stat.
 *
 * @return string
 */
function wpa_format_stats( $type, $data, $history ) {
	if ( 'toolbar' === $type ) {
		$history = (array) $history;
		foreach ( $history as $k => $d ) {
			return '<strong>' . $k . '</strong>:' . print_r( $d, 1 );
		}
	} else {
		$data = (array) $data;
		$i    = 0;
		foreach ( $data as $k => $d ) {
			$i++;
		}
		return $i;
	}
	return '';
}
