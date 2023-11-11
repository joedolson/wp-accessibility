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
		'name'               => __( 'WP Accessibility Stats Record', 'wp-accessibility' ),
		'singular_name'      => __( 'Stats', 'wp-accessibility' ),
		'menu_name'          => __( 'Accessibility Stats', 'wp-accessibility' ),
		'add_new'            => __( 'Add New', 'wp-accessibility' ),
		'add_new_item'       => __( 'Create New', 'wp-accessibility' ),
		'edit_item'          => __( 'Edit Stats', 'wp-accessibility' ),
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
		'supports'            => array( 'title', 'custom-fields' ),
		'capabilities'        => array(
			'create_posts' => false,
		),
		'map_meta_cap'        => true,
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
			'post_title'   => str_replace( home_url(), '', $title ),
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
	echo '<p>';
	_e( 'WP Accessibility tracks accessibility changes it makes to your site, and records when vistors change font size and high contrast options. No personally identifying data is stored.', 'wp-accessibility' );
	echo ' <a href="' . admin_url( 'edit.php?post_type=wpa-stats' ) . '">' . __( 'More stats', 'wp-accessibility' ) . '</a>';
	echo '</p>';
	wpa_get_stats( 'view' );
	wpa_get_stats( 'event' );
	wpa_edac_promotion( 'small' );
}

/**
 * Get stats data for WP Accessibility.
 *
 * @param string $type Type of stats to fetch. 'view' or 'event'. Default 'view'.
 * @param int    $count Number of results to fetch. Default 1.
 *
 * @return void
 */
function wpa_get_stats( $type = 'view', $count = 1 ) {
	$query = array(
		'post_type'   => 'wpa-stats',
		'numberposts' => $count,
		'orderby'     => 'date',
		'order'       => 'desc',
		'tax_query'   => array(
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
		$data_point = wpa_stats_data_point( $post, $type );
		echo $data_point;
	
	}
	echo '</ul></div>';
}

/**
 * Format a single stats data point.
 *
 * @param object $post WP Post.
 * @param string $type Stats type.
 *
 * @return string
 */
function wpa_stats_data_point( $post, $type ) {
	$output   = '';
	$post_ID  = $post->ID;
	$data     = json_decode( $post->post_content );
	$history  = get_post_meta( $post_ID, 'wpa_event' );
	$relative = get_post_meta( $post_ID, '_wpa_post_id', true );
	$output  .= '<li><div class="wpa-header"><h3><strong>' . gmdate( get_option( 'date_format' ), $data->timestamp ) . '</strong><br />' . gmdate( get_option( 'time_format' ), $data->timestamp ) . '</h3>';

	$post_title = ( $relative ) ? get_the_title( $relative ) : 'User action';
	$post_link  = ( $relative ) ? get_the_permalink( $relative ) : '';
	$append     = '';
	if ( 'event' === $type ) {
		// translators: Post ID.
		$append = ' / ' . sprintf( __( 'User %s', 'wp-accessibility' ), '<code>' . $post->ID . '</code>' );
	}
	if ( $post_link ) {
		$output .= '<p><a href="' . esc_url( $post_link ) . '">' . $post_title . '</a>' . $append . '</p>';
	} else {
		$output .= '<p><strong>' . $post_title . '</strong>' . $append . '</p>';
	}
	$output .= '</div>';

	$line = '';
	if ( 'event' === $type ) {
		// translators: date changed, time changed.
		$hc_text_enabled = __( 'High contrast enabled on %1$s at %2$s', 'wp-accessibility' );
		// translators: date changed, time changed.
		$lf_text_enabled = __( 'Large font size enabled on %1$s at %2$s', 'wp-accessibility' );
		// translators: date changed, time changed.
		$hc_text_disabled = __( 'High contrast disabled on %1$s at %2$s', 'wp-accessibility' );
		// translators: date changed, time changed.
		$lf_text_disabled = __( 'Large font size disabled on %1$s at %2$s', 'wp-accessibility' );
		$param            = ( property_exists( $data, 'contrast' ) ) ? 'contrast' : 'fontsize';
		if ( 'contrast' === $param ) {
			switch ( $data->contrast ) {
				case 'enabled':
					$text = $hc_text_enabled;
					break;
				case 'disabled':
					$text = $hc_text_disabled;
			}
		} else {
			switch ( $data->fontsize ) {
				case 'enabled':
					$text = $lf_text_enabled;
					break;
				case 'disabled':
					$text = $lf_text_disabled;
			}
		}
		$date = gmdate( 'Y-m-d', $data->timestamp );
		$time = gmdate( 'H:i', $data->timestamp );
		$line = '<li>' . sprintf( $text, $date, $time ) . '</li>';
		foreach ( $history as $h ) {
			$h             = json_decode( $h );
			$has_font_size = ( property_exists( $h, 'fontsize' ) ) ? $h->fontsize : false;
			$has_contrast  = ( property_exists( $h, 'contrast' ) ) ? $h->contrast : false;
			$change_date   = gmdate( 'Y-m-d', $h->timestamp );
			$change_time   = gmdate( 'H:i', $h->timestamp );
			if ( $has_font_size ) {
				$lf_text = ( 'enabled' === $has_font_size ) ? $lf_text_enabled : $lf_text_disabled;
				$line   .= '<li>' . sprintf( $lf_text, $change_date, $change_time ) . '</li>';
			} elseif ( $has_contrast ) {
				$hc_text = ( 'enabled' === $has_contrast ) ? $hc_text_enabled : $hc_text_disabled;
				$line   .= '<li>' . sprintf( $hc_text, $change_date, $change_time ) . '</li>';
			}
		}
	} else {
		foreach ( $data as $d ) {
			if ( is_array( $d ) ) {
				$key   = wpa_map_key( $d[0] );
				$count = $d[1];
				$stat  = "<span class='wpa-item-count'>$count</span> " . $key;
			} else {
				// If this is the timestamp, don't display here.
				if ( is_numeric( $d ) ) {
					continue;
				}
				$stat = wpa_map_key( $d );
			}
			$line .= '<li>' . $stat . '</li>';
		}
	}
	$output .= '<ul class="stats">' . $line . '</ul></li>';

	return $output;
}

/**
 * Map an array key to a text string.
 *
 * @param string $key Stats array key.
 *
 * @return string
 */
function wpa_map_key( $key ) {
	$strings = array(
		'link-add-tabindex'   => __( '<code>tabindex</code> was removed from a link.', 'wp-accessibility' ),
		'button-add-tabindex' => __( '<code>tabindex</code> was added to a fake button.', 'wp-accessibility' ),
		'control-tabindex'    => __( '<code>tabindex</code> were removed from links, inputs, and buttons.', 'wp-accessibility' ),
		'link-targets'        => __( '<code>target</code> attributes were removed from links.', 'wp-accessibility' ),
		'input-titles'        => __( '<code>title</code> attributes were removed from inputs.', 'wp-accessibility' ),
		'control-titles'      => __( '<code>title</code> attributes removed from inputs and buttons.', 'wp-accessibility' ),
		'images-titles'       => __( '<code>title</code> attributes removed from images.', 'wp-accessibility' ),
		'implicit-label'      => __( 'Implicit <code>label</code> elements added to inputs.', 'wp-accessibility' ),
		'explicit-label'      => __( 'Explicit <code>label</code> elements added to inputs.', 'wp-accessibility' ),
		'aria-current'        => __( '<code>aria-current</code> was assigned to a link.', 'wp-accessibility' ),
		'skiplinks'           => __( 'Skiplinks added to the page.', 'wp-accessibility' ),
		'viewport-maxscale'   => __( 'The viewport maximum scale was fixed.', 'wp-accessibility' ),
		'viewport-scalable'   => __( 'The scalability of the viewport was fixed.', 'wp-accessibility' ),
		'html-lang-direction' => __( 'The language direction was set.', 'wp-accessibility' ),
		'html-lang'           => __( 'The language of the page was set.', 'wp-accessibility' ),
	);
	$string  = ( isset( $strings[ $key ] ) ) ? $strings[ $key ] : $key;

	return $string;
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

/**
 * Filter post titles for user stats to display post ID.
 *
 * @param string $post_title The post title.
 * @param int    $post_id The post ID.
 *
 * @return string
 */
function wpa_stats_title( $post_title, $post_id ) {
	if ( 'wpa-stats' === get_post_type( $post_id ) && has_term( 'event', 'wpa-stats-type', $post_id ) ) {
		// translators: Post ID.
		return sprintf( __( 'User: %d', 'wp-accessibility' ), $post_id );
	} elseif ( 'wpa-stats' === get_post_type( $post_id ) ) {
		// translators: URL path.
		return sprintf( __( 'Page view: %s', 'wp-accessibility' ), $post_title );
	}

	return $post_title;
}
add_filter( 'the_title', 'wpa_stats_title', 10, 2 );


// Actions/Filters for various tables and the css output.
add_action( 'admin_init', 'wpa_add' );
/**
 * Add custom columns to payments post type page.
 */
function wpa_add() {
	add_filter( 'manage_wpa-stats_posts_columns', 'wpa_column' );
	add_action( 'manage_wpa-stats_posts_custom_column', 'wpa_custom_column', 10, 2 );
}

/**
 * Add columns to WPA stats post type.
 *
 * @param array $cols All columns.
 *
 * @return mixed
 */
function wpa_column( $cols ) {
	$cols['wpa_type'] = __( 'Statistic Type', 'my-tickets' );
	$cols['wpa_last'] = __( 'Last Action', 'wp-accessibility' );

	return $cols;
}

/**
 * Show data for stats post type.
 *
 * @param string $column_name Name of the current column.
 * @param int    $id Post ID.
 */
function wpa_custom_column( $column_name, $post_id ) {
	$stat = ( has_term( 'event', 'wpa-stats-type', $post_id ) ) ? 'event' : 'view';
	switch ( $column_name ) {
		case 'wpa_type':
			$type = ( 'event' === $stat ) ? __( 'User Action', 'wp-accessibility' ) : __( 'Page View', 'wp-accessibility' );
			echo '<span class="dashicons dashicons-admin-page" aria-hidden="true"></span> ' . $type;
			break;
		case 'wpa_last':
			$events = get_post_meta( $post_id, 'wpa_event' );
			if ( $events && is_array( $events ) ) {
				$event  = json_decode( end( $events ) );
			} else {
				$event = json_decode( get_post( $post_id )->post_content );
			}
			if ( 'event' === $stat ) {
				$data  = ( property_exists( $event, 'contrast' ) ) ? 'contrast' : 'fontsize';
				$icon  = ( 'contrast' === $data ) ? ' aticon aticon-adjust' : ' aticon aticon-font';
				$label = ( property_exists( $event, 'contrast' ) ) ? __( 'High Contrast', 'wp-accessibility' ) : __( 'Large Font Size', 'wp-accessibility' );
				// translators: Action taken. High Contrast or Large Font Size.
				$last_action = (  'enabled' === $event->{$data} ) ? sprintf( __( '%s enabled', 'wp-accessibility' ), $label ) : sprintf( __( '%s disabled', 'wp-accessibility' ), $label );
			} else {
				if ( ! $events ) {
					$icon        = 'download';
					$last_action = __( 'Page loaded', 'wp-accessibility' );
				} else {
					$icon = 'update';
					// translators: Date of change.
					$last_action = sprintf( __( 'Accessibility Fixes changed on %s', 'wp-accessibility' ), gmdate( 'Y-m-d', $event->timestamp ) );
				}
			}
			echo '<span class="dashicons dashicons-' . $icon . '" aria-hidden="true"></span> ' . $last_action;
			break;
	}
}

add_action( 'add_meta_boxes', 'wpa_add_meta_boxes' );
/**
 * Load  meta boxes for tickets.
 *
 * @return void
 */
function wpa_add_meta_boxes() {
	add_meta_box( 'wpa_stats', __( 'WP Accessibility Statistics', 'my-tickets' ), 'wpa_display_stats', 'wpa-stats', 'normal', 'high' );
}

/**
 * Display stats data.
 */
function wpa_display_stats() {
	global $post;
	$type = ( has_term( 'event', 'wpa-stats-type', $post->ID ) ) ? 'event' : 'view';
	echo '<div class="activity-block"><ul>' . wpa_stats_data_point( $post, $type ) . '</ul></div>';
}