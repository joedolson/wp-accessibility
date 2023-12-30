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
		'supports'            => array( 'title' ),
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
			'label'        => __( 'Stats Types', 'wp-accessibility' ),
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
 * @param string     $title Title for the stats record. Usually a relative URL for page views.
 * @param string     $type Type of stat: view or action.
 * @param int|string $post_ID ID of the post if singular.
 */
function wpa_add_stats( $stats, $title, $type = 'view', $post_ID = 0 ) {
	$admin_only = ( '' === get_option( 'wpa_track_stats' ) ) ? true : false;
	if ( $admin_only && ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$stats  = json_encode( $stats );
	$title  = str_replace( home_url(), '', $title );
	$exists = wpa_get_post_by_title( $title );
	if ( $exists ) {
		if ( 'action' === $type ) {
			// Log all on/off states for toolbar.
			add_post_meta( $exists, '_wpa_event', $stats );
		} else {
			// Get most recent test results and compare to current.
			$history = get_post_meta( $exists, '_wpa_event' );
			if ( $history && is_array( $history ) ) {
				$old_stats = end( $history );
			} else {
				$old_stats = get_post( $exists )->post_content;
			}
			// Remove timestamp before comparing.
			$test_stats            = json_decode( $stats );
			$test_stats->timestamp = '';
			$test_old              = json_decode( $old_stats );
			$test_old->timestamp   = '';
			if ( json_encode( $test_stats ) !== json_encode( $test_old ) ) {
				add_post_meta( $exists, '_wpa_old_event', array( $test_stats, $test_old ) );
				// stats have changed; record the change.
				add_post_meta( $exists, '_wpa_event', $stats );
			}
		}
		/**
		 * Run when a statistic that has already been recorded in the database is updated.
		 *
		 * @hook wpa_save_stats_update
		 *
		 * @param {int}   $exists Existing post ID.
		 * @param {array} $stats Stats data sent via AJAX.
		 */
		do_action( 'wpa_save_stats_update', $exists, $stats );
		return array( $stats );
	} else {
		$post  = array(
			'post_title'   => $title,
			'post_content' => $stats,
			'post_status'  => 'publish',
			'post_name'    => sanitize_title( $title ),
			'post_date'    => current_time( 'Y-m-d H:i:s' ),
			'post_type'    => 'wpa-stats',
		);
		$stat  = wp_insert_post( $post );
		$terms = array( $type );
		require_once( ABSPATH . '/wp-admin/includes/dashboard.php' );
		$browser = wp_check_browser_version();
		add_post_meta( $stat, '_wpa_browser', json_encode( $browser ) );
		$terms[] = $browser['name'];
		$terms[] = $browser['platform'];
		wp_set_object_terms( $stat, $terms, 'wpa-stats-type' );
		if ( $post_ID ) {
			// Set up relationships between stats and posts.
			update_post_meta( $stat, '_wpa_post_id', $post_ID );
			update_post_meta( $post, '_wpa_stat_id', $stat );
		}
		/**
		 * Run when a new statistic is recorded.
		 *
		 * @hook wpa_save_stats_post
		 *
		 * @param {int}   $stat New post ID.
		 * @param {array} $stats Stats data sent via AJAX.
		 * @param {int}   $post_ID Related post ID or false if a non-singular screen.
		 */
		do_action( 'wpa_save_stats_post', $stat, $stats, $post_ID );
		return array();
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
			wp_die();
		}
		$stats   = map_deep( $_REQUEST['stats'], 'sanitize_text_field' );
		$post_id = absint( $_REQUEST['post_id'] );
		$title   = ( wpa_is_url( $_REQUEST['title'] ) ) ? esc_url( $_REQUEST['title'] ) : sanitize_text_field( $_REQUEST['title'] );
		$type    = ( 'view' === $_REQUEST['type'] ) ? 'view' : 'event';
		// Add timestamp for this stat.
		$stats['timestamp'] = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

		$response = wpa_add_stats( $stats, $title, $type, $post_id );
		wp_send_json( $response );
	}
}
add_action( 'wp_ajax_wpa_stats_action', 'wpa_stats_action' );
add_action( 'wp_ajax_nopriv_wpa_stats_action', 'wpa_stats_action' );

/**
 * Register the WP Accessibility dashboard stats widget.
 *
 * @return void
 */
function wp_accessibility_dashboard_widget() {
	wp_add_dashboard_widget( 'wpa_dashboard_widget_stats', __( 'WP Accessibility Stats', 'wp-accessibility' ), 'wpa_dashboard_widget_stats_handler' );
}
add_action( 'wp_dashboard_setup', 'wp_accessibility_dashboard_widget' );

/**
 * Output the WP Accessibility stats widget.
 *
 * @return void
 */
function wpa_dashboard_widget_stats_handler() {
	echo '<p>';
	_e( 'WP Accessibility tracks accessibility changes it makes to your site, and records when visitors change font size and high contrast options. No personally identifying data is stored.', 'wp-accessibility' );
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
		'post_type'      => 'wpa-stats',
		'numberposts'    => $count,
		'posts_per_page' => $count,
		'orderby'        => 'date',
		'order'          => 'desc',
		'tax_query'      => array(
			array(
				'taxonomy' => 'wpa-stats-type',
				'field'    => 'slug',
				'terms'    => $type,
			),
		),
	);

	$posts = new WP_Query( $query );
	if ( 'view' === $type ) {
		echo '<div class="activity-block"><div class="wpa-stats-heading"><h3>' . __( 'Pages Viewed', 'wp-accessibility' ) . '</h3><a href="' . add_query_arg( 'wpa-stats-type', $type, admin_url( 'edit.php?post_type=wpa-stats' ) ) . '">' . __( 'Page Stats', 'wp-accessibility' ) . '</a></div><ul>';
	} else {
		echo '<div class="activity-block"><div class="wpa-stats-heading"><h3>' . __( 'User Actions', 'wp-accessibility' ) . '</h3><a href="' . add_query_arg( 'wpa-stats-type', $type, admin_url( 'edit.php?post_type=wpa-stats' ) ) . '">' . __( 'User Stats', 'wp-accessibility' ) . '</a></div><ul>';
	}

	foreach ( $posts->posts as $post ) {
		$data_point = wpa_stats_data_point( $post, $type );
		echo $data_point['html'];
	}
	echo '</ul></div>';
}

/**
 * Format a single stats data point.
 *
 * @param object $post WP Post.
 * @param string $type Stats type.
 * @param int    $limit Number of stat points to show.
 *
 * @return string
 */
function wpa_stats_data_point( $post, $type, $limit = 5 ) {
	$output   = '';
	$post_ID  = $post->ID;
	$data     = json_decode( $post->post_content );
	$history  = get_post_meta( $post_ID, '_wpa_event' );
	$relative = get_post_meta( $post_ID, '_wpa_post_id', true );
	$output  .= '<li><div class="wpa-header"><h3><strong>' . gmdate( get_option( 'date_format' ), $data->timestamp ) . '</strong><br />' . gmdate( get_option( 'time_format' ), $data->timestamp ) . '</h3>';

	$url = ( str_contains( $post->post_title, '/' ) ) ? home_url( $post->post_title ) : '';
	// translators: path stats are related to.
	$post_title = ( $relative ) ? get_the_title( $relative ) : sprintf( __( 'View: %s', 'wp-accessibility' ), $url );
	$post_link  = ( $relative ) ? get_the_permalink( $relative ) : $url;
	$append     = '';
	if ( 'event' === $type ) {
		$browser = wpa_get_browser_stat( $post_ID );
		// translators: Post ID.
		$append = ' / ' . sprintf( __( 'User %s', 'wp-accessibility' ), '<code>' . $post->ID . '</code>' ) . '<br />' . $browser;
	}
	if ( $post_link ) {
		$output .= '<p><a href="' . esc_url( $post_link ) . '">' . $post_title . '</a>' . $append . '</p>';
	} else {
		$output .= '<p><strong>' . $post_title . '</strong>' . $append . '</p>';
	}
	$output .= '</div>';

	$line  = '';
	$total = 0;
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
		if ( property_exists( $data, 'alttext' ) ) {
			$image_link = '<a href="' . esc_url( add_query_arg( 'item', $data->alttext, admin_url( 'upload.php' ) ) ) . '">' . esc_html( $data->alttext ) . '</a>';
			// translators: 1) image link. 2) date 3) time.
			$text = sprintf( __( 'Alt text expanded on image %1$s on %2$s at %3$s', 'wp-accessibility' ), $image_link, $date, $time );
		}
		if ( property_exists( $data, 'longdesc' ) ) {
			$image_link = '<a href="' . esc_url( add_query_arg( 'item', $data->longdesc, admin_url( 'upload.php' ) ) ) . '">' . esc_html( $data->longdesc ) . '</a>';
			// translators: 1) image link. 2) date 3) time.
			$text = sprintf( __( 'Long description expanded on image %1$s on %2$s at %3$s', 'wp-accessibility' ), $image_link, $date, $time );
		}
		$line = '<li>' . sprintf( $text, $date, $time ) . '</li>';
		if ( 'all' === $limit ) {
			$limit = count( $history ) + 1;
		}
		$i = $limit;
		foreach ( $history as $h ) {
			$i --;
			if ( $i < 1 ) {
				$line .= '<li><a href="' . get_edit_post_link( $post_ID ) . '">' . __( 'View full record', 'wp-accessibility' ) . '</a></li>';
				break;
			}
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
			} else {
				$text = 'no';
				if ( property_exists( $h, 'alttext' ) ) {
					$image_link = '<a href="' . esc_url( add_query_arg( 'item', $h->alttext, admin_url( 'upload.php' ) ) ) . '">' . esc_html( $h->alttext ) . '</a>';
					// translators: 1) image ID. 2) date 3) time.
					$text = sprintf( __( 'Alt text expanded on image %1$s on %2$s at %3$s', 'wp-accessibility' ), $image_link, $change_date, $change_time );
				}
				if ( property_exists( $h, 'longdesc' ) ) {
					$image_link = '<a href="' . esc_url( add_query_arg( 'item', $h->longdesc, admin_url( 'upload.php' ) ) ) . '">' . esc_html( $h->longdesc ) . '</a>';
					// translators: 1) image link. 2) date 3) time.
					$text = sprintf( __( 'Long description expanded on image %1$s on %2$s at %3$s', 'wp-accessibility' ), $image_link, $change_date, $change_time );
				}
				$line .= '<li>' . $text . '</li>';
			}
		}
	} else {
		$info  = wpa_parse_view_data( $data );
		$line  = $info['html'];
		$total = $info['count'];
		// handle additional.
		if ( $history ) {
			foreach ( $history as $h ) {
				$h     = json_decode( $h );
				$head  = '</ul><div class="wpa-header"><h3>' . gmdate( get_option( 'date_format' ), $h->timestamp ) . '<br />' . gmdate( get_option( 'time_format' ), $h->timestamp ) . '</h3></div>
				<ul class="stats">';
				$line .= $head . wpa_parse_view_data( $h )['html'];
			}
		}
	}
	$output .= '<ul class="stats">' . $line . '</ul></li>';

	$return = array(
		'count' => $total,
		'html'  => $output,
	);
	/**
	 * Filter the data displayed about a given stats point.
	 *
	 * @hook wpa_stats_data_point
	 *
	 * @param {array}   $return Array with an `html` key containing HTML and a `count` string with the number of issues to display.
	 * @param {WP_Post} $post WordPress post object.
	 * @param {string}  $type Type of stat; 'event' or 'view'.
	 *
	 * @return {array}
	 */
	$return = apply_filters( 'wpa_stats_data_point', $return, $post, $type );
	return $return;
}

/**
 * Compare the most recent data to the previous data for page views.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function wpa_compare_views( $post_id ) {
	$content = get_post_field( 'post_content', $post_id );
	$history = get_post_meta( $post_id, '_wpa_event' );

	if ( empty( $history ) ) {
		$data = wpa_parse_view_data( json_decode( $content ) )['count'];
		// translators: Number of accessibility issues fixed.
		$info = sprintf( _n( '%d issue fixed', '%d issues fixed', $data, 'wp-accessibility' ), $data );

		return '<span class="dashicons dashicons-download" aria-hidden="true"></span> ' . $info;
	}
	if ( count( $history ) > 1 ) {
		$current  = array_pop( $history );
		$previous = array_pop( $history );
	} else {
		$current  = array_pop( $history );
		$previous = $content;
	}
	$count_current  = wpa_parse_view_data( json_decode( $current ) )['count'];
	$count_previous = wpa_parse_view_data( json_decode( $previous ) )['count'];

	// translators: Number of accessibility issues fixed.
	$info = sprintf( _n( '%d issue fixed', '%d issues fixed', $count_current, 'wp-accessibility' ), $count_current );
	if ( $count_current > $count_previous ) {
		return '<span class="dashicons dashicons-arrow-up-alt" aria-hidden="true"></span> ' . $info;
	} elseif ( $count_current < $count_previous ) {
		return '<span class="dashicons dashicons-arrow-down-alt" aria-hidden="true"></span> ' . $info;
	} else {
		return '<span class="dashicons dashicons-update" aria-hidden="true"></span> ' . $info;
	}
}

/**
 * Parse the data for a single page view.
 *
 * @param array $data Page view data.
 *
 * @return array
 */
function wpa_parse_view_data( $data ) {
	$total = 0;
	$line  = '';
	if ( ! is_object( $data ) ) {
		return array(
			'html'  => __( 'No data found', 'wp-accessibility' ),
			'count' => 0,
		);
	}
	foreach ( $data as $d ) {
		if ( is_array( $d ) ) {
			$key   = wpa_map_key( $d[0] );
			$count = absint( $d[1] );
			$stat  = "<span class='wpa-item-count'>$count</span> " . $key;
		} else {
			// If this is the timestamp, don't display here.
			if ( is_numeric( $d ) ) {
				continue;
			}
			$count = 1;
			$stat  = wpa_map_key( $d );
		}
		$total += $count;
		$line  .= '<li>' . $stat . '</li>';
	}

	return array(
		'html'  => $line,
		'count' => $total,
	);
}

/**
 * Get the browser information for a stats record.
 *
 * @param int $post_ID Post ID for stats post.
 *
 * @return string
 */
function wpa_get_browser_stat( $post_ID ) {
	$browser = get_post_meta( $post_ID, '_wpa_browser', true );
	if ( $browser ) {
		$browser = json_decode( $browser );
		$browser = '<span class="wpa-browser"><img src="' . esc_url( $browser->img_src_ssl ) . '" alt="" width="20" height="20"> ' . esc_html( $browser->name . ' ' . $browser->version . '/' . $browser->platform ) . '</span>';
	} else {
		$browser = __( 'Unknown browser', 'wp-accessibility' );
	}

	return $browser;
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
function wpa_stats_title( $post_title, $post_id = false ) {
	// If `apply_filters( 'the_title' )` is called by a 3rd party without the post ID, return the original title.
	if ( ! $post_id ) {
		return $post_title;
	}
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
	$cols['wpa_type'] = __( 'Statistic Type', 'wp-accessibility' );
	$cols['wpa_last'] = __( 'Last Action', 'wp-accessibility' );
	$cols['wpa_info'] = __( 'Info', 'wp-accessibility' );

	return $cols;
}

/**
 * Show data for stats post type.
 *
 * @param string $column_name Name of the current column.
 * @param int    $post_id Post ID.
 */
function wpa_custom_column( $column_name, $post_id ) {
	$stat = ( has_term( 'event', 'wpa-stats-type', $post_id ) ) ? 'event' : 'view';
	switch ( $column_name ) {
		case 'wpa_type':
			$type = ( 'event' === $stat ) ? __( 'User Action', 'wp-accessibility' ) : __( 'Page View', 'wp-accessibility' );
			echo '<span class="dashicons dashicons-admin-page" aria-hidden="true"></span> ' . $type;
			break;
		case 'wpa_last':
			$events = get_post_meta( $post_id, '_wpa_event' );
			if ( $events && is_array( $events ) ) {
				$event = json_decode( end( $events ) );
			} else {
				$event = json_decode( get_post( $post_id )->post_content );
			}
			if ( 'event' === $stat ) {
				$data  = ( property_exists( $event, 'contrast' ) ) ? 'contrast' : 'fontsize';
				$icon  = ( 'contrast' === $data ) ? ' aticon aticon-adjust' : ' aticon aticon-font';
				$label = ( property_exists( $event, 'contrast' ) ) ? __( 'High Contrast', 'wp-accessibility' ) : __( 'Large Font Size', 'wp-accessibility' );
				// translators: Action taken. High Contrast or Large Font Size.
				if ( 'fontsize' === $data && property_exists( $data, 'fontsize' ) ) {
					// translators: Action enabled.
					$last_action = ( 'enabled' === $event->{$data} ) ? sprintf( __( '%s enabled', 'wp-accessibility' ), $label ) : sprintf( __( '%s disabled', 'wp-accessibility' ), $label );
				} else {
					$icon   = 'format-image';
					$action = ( property_exists( $data, 'alttext' ) ) ? '<code>alt</code>' : '<code>longdesc</code>';
					// translators: Data expanded; either `alt` or `longdesc`.
					$last_action = sprintf( __( '%s expanded on image.', 'wp-accessibility' ), $action );
				}
			} else {
				if ( ! $events ) {
					$icon        = 'download';
					$last_action = __( 'Page loaded', 'wp-accessibility' );
				} else {
					$icon = 'update';
					// translators: Date of change.
					$last_action = sprintf( __( 'Found issues changed on %s', 'wp-accessibility' ), gmdate( 'Y-m-d', $event->timestamp ) );
				}
			}
			echo '<span class="dashicons dashicons-' . $icon . '" aria-hidden="true"></span> ' . $last_action;
			break;
		case 'wpa_info':
			if ( 'event' === $stat ) {
				echo wpa_get_browser_stat( $post_id );
			} else {
				echo wpa_compare_views( $post_id );
			}
	}
}

add_action( 'add_meta_boxes', 'wpa_add_meta_boxes' );
/**
 * Load  meta boxes for tickets.
 *
 * @return void
 */
function wpa_add_meta_boxes() {
	add_meta_box( 'wpa_stats', __( 'WP Accessibility Statistics', 'wp-accessibility' ), 'wpa_display_stats', 'wpa-stats', 'normal', 'high' );
}

/**
 * Display stats data.
 */
function wpa_display_stats() {
	global $post;
	$type = ( has_term( 'event', 'wpa-stats-type', $post->ID ) ) ? 'event' : 'view';
	$data = wpa_stats_data_point( $post, $type, 'all' );
	echo '<ul>' . $data['html'] . '</ul>';
}
