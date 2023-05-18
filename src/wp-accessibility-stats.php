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

add_action( 'init', 'wpa_post_type' );
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

/**
 * Register statistics.
 *
 * @param array      $stats Stats data for a page. Array of tests & results.
 * @param string     $url URL of the page tested.
 * @param int|string $post_ID ID of the post if singular.
 */
function wpa_add_stats( $stats, $url, $post_ID ) {
	$stats  = json_encode( $stats, 'sanitize_text_field' );
	$title  = $url;
	$exists = wpa_get_post_by_title( $title );
	if ( $exists ) {
		$old_stats = get_post( $exists )->post_content;
		if ( $stats !== $old_stats ) {
			// stats have changed; record the change.
			add_post_meta( $exists, 'wpa_results', $stats );
		}
	}
	$post = array(
		'post_title'   => $title,
		'post_content' => $stats,
		'post_status'  => 'publish',
		'post_name'    => sanitize_title( $title ),
		'post_date'    => current_time( 'Y-m-d H:i:s' ),
		'post_type'    => 'wpa-stats',
	);
	$stat = wp_insert_post( $post );
	if ( $post_ID ) {
		// Set up relationships between stats and posts.
		update_post_meta( $stat, '_wpa_post_id', $post_ID );
		update_post_meta( $post, '_wpa_stat_id', $stat );
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
			wp_die( 'WP Accessibility: stats request not validated.' );
		}
		$stats   = map_deep( $_REQUEST['stats'], 'sanitize_text_field' );
		$post_id = absint( $_REQUEST['post_id'] );
		$url     = esc_url_raw( $_REQUEST['url'] );

		wpa_add_stats( $stats, $url, $post_id );
	}
}
add_action( 'wp_ajax_wpa_stats_action', 'wpa_stats_action' );
add_action( 'wp_ajax_nopriv_wpa_stats_action', 'wpa_stats_action' );
