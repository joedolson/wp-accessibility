<?php
/**
 * WP Accessibility
 *
 * @package     WP Accessibility
 * @author      Joe Dolson
 * @copyright   2012-2024 Joe Dolson
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WP Accessibility
 * Plugin URI: http://www.joedolson.com/wp-accessibility/
 * Description: Helps improve accessibility in your WordPress site, like removing title attributes.
 * Author: Joe Dolson
 * Author URI: http://www.joedolson.com/
 * Text Domain: wp-accessibility
 * Domain Path: /lang
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/license/gpl-2.0.txt
 * Version: 2.1.7
 */

/*
	Copyright 2012-2024  Joe Dolson (email : joe@joedolson.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( 'on' === get_option( 'wpa_toolbar' ) || 'on' === get_option( 'wpa_widget_toolbar' ) ) {
	require_once( dirname( __FILE__ ) . '/wp-accessibility-toolbar.php' );
}
require_once( dirname( __FILE__ ) . '/wp-accessibility-longdesc.php' );
require_once( dirname( __FILE__ ) . '/wp-accessibility-alt.php' );
require_once( dirname( __FILE__ ) . '/wp-accessibility-contrast.php' );
require_once( dirname( __FILE__ ) . '/wp-accessibility-settings.php' );
require_once( dirname( __FILE__ ) . '/wp-accessibility-help.php' );
if ( 'off' !== get_option( 'wpa_track_stats' ) ) {
	require_once( dirname( __FILE__ ) . '/wp-accessibility-stats.php' );
}

register_activation_hook( __FILE__, 'wpa_install' );

add_action( 'plugins_loaded', 'wpa_load_textdomain' );
/**
 * Load internationalization.
 */
function wpa_load_textdomain() {
	load_plugin_textdomain( 'wp-accessibility' );
}

add_action( 'admin_menu', 'wpa_admin_menu' );
/**
 * Set up admin menu.
 */
function wpa_admin_menu() {
	add_menu_page( 'WP Accessibility', 'WP Accessibility', 'manage_options', 'wp-accessibility', 'wpa_admin_settings', 'dashicons-universal-access' );
	add_submenu_page( 'wp-accessibility', 'WP Accessibility - Help', 'Get Help', 'manage_options', 'wp-accessibility-help', 'wpa_help_screen' );
}

/**
 * Install on activation.
 */
function wpa_install() {
	$wpa_version = '2.1.7';
	if ( 'true' !== get_option( 'wpa_installed' ) ) {
		add_option( 'rta_from_tag_clouds', 'on' );
		add_option( 'asl_styles_focus', '' );
		add_option( 'asl_styles_passive', '' );
		add_option( 'asl_default_styles', 'true' );
		add_option( 'wpa_target', 'on' );
		add_option( 'wpa_search', 'on' );
		add_option( 'wpa_tabindex', 'on' );
		add_option( 'wpa_continue', 'Continue Reading' );
		add_option( 'wpa_focus', '' );
		add_option( 'wpa_installed', 'true' );
		add_option( 'wpa_version', $wpa_version );
		add_option( 'wpa_longdesc', 'jquery' );
		add_option( 'wpa_post_types', array( 'post' ) );
	} else {
		wpa_check_version();
		update_option( 'wpa_version', $wpa_version );
	}
}

/**
 * Check current version and upgrade if needed.
 *
 * @return string
 */
function wpa_check_version() {
	// upgrade for version 1.3.0.
	$version = get_option( 'wpa_version' );
	if ( version_compare( $version, '1.3.0', '<' ) ) {
		add_option( 'wpa_longdesc', 'jquery' );
	}
	// upgrade for version 1.9.0.
	if ( version_compare( $version, '1.9.0', '<' ) ) {
		add_option( 'wpa_post_types', array( 'post' ) );
		$wpa_toolbar_fs = get_option( 'wpa_toolbar_fs', '' );
		$wpa_toolbar_ct = get_option( 'wpa_toolbar_ct', '' );
		if ( '' === $wpa_toolbar_fs ) {
			update_option( 'wpa_toolbar_fs', 'on' );
		}
		if ( '' === $wpa_toolbar_ct ) {
			update_option( 'wpa_toolbar_ct', 'on' );
		}
	}

	return $version;
}

add_filter( 'plugin_action_links', 'wpa_plugin_action', 10, 2 );
/**
 * Add plugin action links.
 *
 * @param array  $links Existing links.
 * @param string $file File name.
 */
function wpa_plugin_action( $links, $file ) {
	if ( plugin_basename( dirname( __FILE__ ) . '/wp-accessibility.php' ) === $file ) {
		$admin_url = admin_url( 'admin.php?page=wp-accessibility' );
		$links[]   = "<a href='$admin_url'>" . __( 'Accessibility Settings', 'wp-accessibility' ) . '</a>';
	}

	return $links;
}

add_action( 'wp_enqueue_scripts', 'wpa_stylesheet' );
/**
 * Enqueue stylesheets for WP Accessibility.
 */
function wpa_stylesheet() {
	$version = ( SCRIPT_DEBUG ) ? wp_rand( 10000, 100000 ) : wpa_check_version();
	wp_register_style( 'wpa-style', plugins_url( 'css/wpa-style.css', __FILE__ ), array(), $version );
	if ( 'link' === get_option( 'wpa_longdesc' ) || 'jquery' === get_option( 'wpa_longdesc' ) || 'on' === get_option( 'asl_enable' ) || ! empty( get_option( 'wpa_post_types', array() ) ) ) {
		wp_enqueue_style( 'wpa-style' );
		// these styles are derived from the WordPress skip link defaults.
		$top = '7px';
		if ( is_admin_bar_showing() ) {
			$top = '37px';
		}
		$add_css    = ( ! wpa_accessible_theme() ) ? wpa_css() : '';
		$custom_css = ':root { --admin-bar-top : ' . $top . '; }';
		wp_add_inline_style( 'wpa-style', wp_filter_nohtml_kses( stripcslashes( $add_css . $custom_css ) ) );
	}
	if ( current_user_can( 'edit_files' ) && 'on' === get_option( 'wpa_diagnostics' ) ) {
		wp_register_style( 'diagnostic', plugins_url( 'css/diagnostic.css', __FILE__ ) );
		wp_register_style( 'diagnostic-head', plugins_url( 'css/diagnostic-head.css', __FILE__ ) );
		wp_enqueue_style( 'diagnostic' );
		wp_enqueue_style( 'diagnostic-head' );
	}
}

add_action( 'admin_head', 'wpa_admin_stylesheet' );
/**
 * Enqueue admin stylesheets if enabled
 */
function wpa_admin_stylesheet() {
	// Used to provide an admin CSS from plug-in, now only enqueue if custom provided in theme.
	if ( file_exists( get_stylesheet_directory() . '/wp-admin.css' ) ) {
		$file = get_stylesheet_directory_uri() . '/wp-admin.css';
		wp_register_style( 'wp-a11y-css', $file );
		wp_enqueue_style( 'wp-a11y-css' );
	}
}

/**
 * Generate skiplink CSS.
 *
 * @param bool $defaults 'true' to return defaults regardless of settings.
 *
 * @return string
 */
function wpa_skiplink_css( $defaults = false ) {
	$use_defaults = get_option( 'asl_default_styles', '' );
	$styles       = '';
	$focus        = '';
	$passive      = '';
	$visibility   = ( 'on' === get_option( 'asl_visible' ) ) ? 'wpa-visible' : 'wpa-hide';
	$is_rtl       = ( is_rtl() ) ? '-rtl' : '-ltr';
	$off_vis      = ( 'on' === get_option( 'asl_visible' ) ) ? 'wpa-hide' : 'wpa-visible';
	$off_rtl      = ( is_rtl() ) ? '-ltr' : '-rtl';
	// If not using default styles.
	if ( 'true' !== $use_defaults && ! $defaults ) {
		$default_focus   = '';
		$default_passive = '';
		if ( '' !== get_option( 'asl_styles' ) ) {
			$styles = wp_filter_nohtml_kses( get_option( 'asl_styles' ) );
			// Ensure custom styles match settings.
			$styles = str_replace( array( $off_vis, $off_rtl ), array( $visibility, $is_rtl ), $styles );
			// If custom styles contain #skiplinks, we can just return this; it's probably a fully realized selector.
			if ( false !== stripos( $styles, '#skiplinks' ) ) {
				return $styles;
			}
		} else {
			$focus   = wp_filter_nohtml_kses( get_option( 'asl_styles_focus' ) );
			$passive = wp_filter_nohtml_kses( get_option( 'asl_styles_passive' ) );
		}
	} else {
		// these styles are derived from the WordPress skip link defaults.
		$default_focus = 'background-color: #f1f1f1;
	box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
	clip: auto;
	color: #0073aa;
	display: block;
	font-weight: 600;
	height: auto;
	line-height: normal;
	padding: 15px 23px 14px;
	position: absolute;
	left: 6px;
	top: var(--admin-bar-top);
	text-decoration: none;
	text-transform: none;
	width: auto;
	z-index: 100000;';

		// Passive default styles derived from WordPress default focus styles.
		$default_passive = 'background-color: #fff;
	box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.2);
	clip: auto;
	color: #333;
	display: block;
	font-weight: 600;
	height: auto;
	line-height: normal;
	padding: 15px 23px 14px;
	position: absolute;
	left: 6px;
	top: var(--admin-bar-top);
	text-decoration: none;
	text-transform: none;
	width: auto;
	z-index: 100000;';
	}
	if ( is_admin() && '' !== $styles ) {
		return $styles;
	}

	if ( ! $focus ) {
		$focus = $default_focus;
	} else {
		$focus = $default_focus . $focus;
	}

	$vis   = '';
	$invis = '';
	$class = '.' . $visibility . $is_rtl;
	// If links are visible, "hover" is a focus style, otherwise, it's a passive style.
	if ( 'on' === get_option( 'asl_visible' ) ) {
		$vis     = $class . '#skiplinks a:hover,';
		$passive = $default_passive . $passive;
	} else {
		$invis   = $class . '#skiplinks a:hover,';
		$passive = '';
	}
	$styles .= "
$class#skiplinks a, $invis $class#skiplinks a:visited {
	$passive
}
$class#skiplinks a:active, $vis $class#skiplinks a:focus {
	$focus
}
	";
	/**
	 * Filter CSS styles output on front-end for skip links.
	 *
	 * @hook wpa_skiplink_styles
	 *
	 * @param {string} $styles Styles configured by settings.
	 *
	 * @return {string}
	 */
	$styles = apply_filters( 'wpa_skiplink_styles', $styles );

	return $styles;
}

/**
 * Generate styles needed for WP Accessibility options.
 */
function wpa_css() {
	$styles = '';
	if ( get_option( 'asl_enable' ) === 'on' ) {
		$styles .= wpa_skiplink_css();
	}
	if ( 'on' === get_option( 'wpa_focus' ) ) {
		$color   = ( false !== (bool) get_option( 'wpa_focus_color' ) ) ? ' #' . get_option( 'wpa_focus_color' ) : '#233c7f';
		$styles .= "
		:focus { outline: 2px solid$color!important; outline-offset: 2px !important; }
		";
	}

	return $styles;
}

/**
 * Test whether a URL is validly structured.
 *
 * @param string $url A purported URL.
 *
 * @return mixed URL if valid, false if not.
 */
function wpa_is_url( $url ) {
	return preg_match( '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url );
}

add_action( 'wp_enqueue_scripts', 'wpa_jquery_asl', 100 );
/**
 * Enqueue JS needed for WP Accessibility options.
 */
function wpa_jquery_asl() {
	$version       = ( SCRIPT_DEBUG ) ? wp_rand( 10000, 100000 ) : wpa_check_version();
	$longdesc_type = false;
	if ( 'link' === get_option( 'wpa_longdesc' ) ) {
		$longdesc_type = 'link';
	} elseif ( 'jquery' === get_option( 'wpa_longdesc' ) ) {
		$longdesc_type = 'jquery';
	}
	if ( $longdesc_type ) {
		$wpald = ( SCRIPT_DEBUG ) ? plugins_url( 'js/longdesc.js', __FILE__ ) : plugins_url( 'js/longdesc.min.js', __FILE__ );
		wp_enqueue_script( 'wpa.longdesc', $wpald, array( 'jquery' ), $version, true );
		wp_localize_script(
			'wpa.longdesc',
			'wpald',
			array(
				'url'  => get_rest_url( null, 'wp/v2/media' ),
				'type' => $longdesc_type,
				'text' => '<span class="dashicons dashicons-media-text" aria-hidden="true"></span><span class="screen-reader">' . __( 'Long Description', 'wp-accessibility' ) . '</span>',
			)
		);
	}
	if ( 'on' === get_option( 'wpa_show_alt' ) ) {
		/**
		 * Modify the selector used to attach the alt attribute toggle button on images. Default `.hentry img[alt!=""], .comment-content img[alt!=""]`.
		 *
		 * @hook wpa_show_alt_selector
		 *
		 * @since 2.0.0
		 *
		 * @param {string} $selector Valid jQuery selector string.
		 *
		 * @return {string}
		 */
		$selector = apply_filters( 'wpa_show_alt_selector', '.hentry img[alt!=""], .comment-content img[alt!=""]' );
		$wpaab    = ( SCRIPT_DEBUG ) ? plugins_url( 'js/alt.button.js', __FILE__ ) : plugins_url( 'js/alt.button.min.js', __FILE__ );
		wp_enqueue_script( 'wpa.alt', $wpaab, array( 'jquery' ), $version, true );
		wp_localize_script(
			'wpa.alt',
			'wpalt',
			array(
				'selector' => $selector,
			)
		);
	}
	$visibility = ( 'on' === get_option( 'asl_visible' ) ) ? 'wpa-visible' : 'wpa-hide';
	$output     = '';
	if ( 'on' === get_option( 'asl_enable' ) && ! wpa_accessible_theme() ) {
		$html = '';
		/**
		 * Customize the default value for extra skiplink. Turns on extra skiplink options in WP Accessibility versions > 1.9.0.
		 *
		 * @hook asl_extra_target
		 * @param {string} Value to use as a default for the extra skiplink.
		 *
		 * @return {string}
		 */
		$default_extra = apply_filters( 'asl_extra_target', '' );
		$extra         = get_option( 'asl_extra_target', $default_extra );
		$extra         = ( wpa_is_url( $extra ) ) ? esc_url( $extra ) : str_replace( '#', '', esc_attr( $extra ) );
		if ( '' !== $extra && ! wpa_is_url( $extra ) ) {
			$extra = "#$extra";
		}
		$extra_text = (string) stripslashes( get_option( 'asl_extra_text' ) );
		$content    = str_replace( '#', '', esc_attr( get_option( 'asl_content' ) ) );
		$nav        = str_replace( '#', '', esc_attr( get_option( 'asl_navigation' ) ) );
		/**
		 * Customize the default value for sitemap skiplink. Turns on sitemap skiplink options in WP Accessibility versions > 1.9.0.
		 *
		 * @hook asl_sitemap
		 * @param {string} Value to use as a default for the sitemap.
		 *
		 * @return {string}
		 */
		$default_sitemap = apply_filters( 'asl_sitemap', '' );
		$sitemap         = esc_url( get_option( 'asl_sitemap', $default_sitemap ) );
		$html           .= ( '' !== $content ) ? "<a href=\"#$content\" class='no-scroll et_smooth_scroll_disabled'>" . __( 'Skip to Content', 'wp-accessibility' ) . '</a> ' : '';
		$html           .= ( '' !== $nav ) ? "<a href=\"#$nav\" class='no-scroll et_smooth_scroll_disabled'>" . __( 'Skip to navigation', 'wp-accessibility' ) . '</a> ' : '';
		$html           .= ( '' !== $sitemap ) ? "<a href=\"$sitemap\" class='no-scroll et_smooth_scroll_disabled'>" . __( 'Site map', 'wp-accessibility' ) . '</a> ' : '';
		$html           .= ( '' !== $extra && '' !== $extra_text ) ? "<a href=\"$extra\" class='no-scroll et_smooth_scroll_disabled'>$extra_text</a> " : '';
		$is_rtl          = ( is_rtl() ) ? '-rtl' : '-ltr';
		$skiplinks       = __( 'Skip links', 'wp-accessibility' );
		$output          = ( '' !== $html ) ? "<div class=\"$visibility$is_rtl\" id=\"skiplinks\" role=\"navigation\" aria-label=\"" . esc_attr( $skiplinks ) . "\">$html</div>" : '';
	}

	$labels = array(
		's'       => __( 'Search', 'wp-accessibility' ),
		'author'  => __( 'Name', 'wp-accessibility' ),
		'email'   => __( 'Email', 'wp-accessibility' ),
		'url'     => __( 'Website', 'wp-accessibility' ),
		'comment' => __( 'Comment', 'wp-accessibility' ),
	);
	/**
	 * Customize labels passed to automatically label core WordPress fields.
	 *
	 * @hook wpa_labels
	 * @param {array} $labels Array of labels for search and comment fields.
	 *
	 * @return {array}
	 */
	$labels = apply_filters( 'wpa_labels', $labels );
	$dir    = ( is_rtl() ) ? 'rtl' : 'ltr';
	$lang   = get_bloginfo( 'language' );

	$wpafp = plugins_url( 'js/fingerprint.min.js', __FILE__ );
	wp_register_script( 'wpa-fingerprintjs', $wpafp, array(), $version );
	if ( SCRIPT_DEBUG ) {
		$wpajs = plugins_url( 'js/wp-accessibility.js', __FILE__ );
	} else {
		$wpajs = plugins_url( 'js/wp-accessibility.min.js', __FILE__ );
	}
	$deps     = array( 'jquery', 'wpa-fingerprintjs' );
	$longdesc = ( 'jquery' === get_option( 'wpa_longdesc' ) ) ? true : false;
	if ( 'jquery' === $longdesc ) {
		$deps[] = 'wpa.longdesc';
	}
	$alttext = ( 'on' === get_option( 'wpa_show_alt' ) ) ? true : false;
	if ( $alttext ) {
		$deps[] = 'wpa.alt';
	}
	wp_enqueue_script( 'wp-accessibility', $wpajs, $deps, $version, true );
	/**
	 * Filter target element selector for underlines. Default `a`.
	 *
	 * @hook wpa_underline_target
	 *
	 * @param {string} $el Target element selector.
	 *
	 * @return string
	 */
	$target = apply_filters( 'wpa_underline_target', 'a' );
	/**
	 * Filter whether console log messages about remediation actions will be sent.
	 *
	 * @hook wpa_view_remediation_logs
	 *
	 * @param {bool} $visible Default `true` if user is logged in and has capabilities to manage options.
	 *
	 * @return {bool}
	 */
	$errors_enabled = apply_filters( 'wpa_view_remediation_logs', current_user_can( 'manage_options' ) );
	$track          = ( '' === get_option( 'wpa_track_stats' ) ) ? current_user_can( 'manage_options' ) : true;
	$track          = ( 'off' === get_option( 'wpa_track_stats' ) ) ? false : $track;
	/**
	 * Filter whether data from views will be tracked.
	 *
	 * @hook wpa_track_view_statistics
	 *
	 * @param {bool} $visible Default `true` if user is logged in and has capabilities to manage options or if enabled in settings.
	 *
	 * @return {bool}
	 */
	$tracking_enabled = apply_filters( 'wpa_track_view_statistics', $track );
	/**
	 * Filter whether automatic labeling is enabled.
	 *
	 * @hook wpa_disable_labels
	 *
	 * @param {bool} $enabled True if labels are automatically added.
	 *
	 * @return {bool}
	 */
	$apply_labels = apply_filters( 'wpa_disable_labels', true );
	/**
	 * Filter whether title attributes are removed. Used to be image titles only, now applies buttons and links, as well.
	 *
	 * @hook wpa_remove_titles
	 *
	 * @param {bool} $enabled True if title attributes are removed.
	 *
	 * @return {bool}
	 */
	$remove_titles = apply_filters( 'wpa_remove_titles', true );
	wp_localize_script(
		'wp-accessibility',
		'wpa',
		array(
			'skiplinks' => array(
				'enabled' => ( 'on' === get_option( 'asl_enable' ) ) ? true : false,
				'output'  => $output,
			),
			'target'    => ( 'on' === get_option( 'wpa_target' ) ) ? true : false,
			'tabindex'  => ( 'on' === get_option( 'wpa_tabindex' ) ) ? true : false,
			'underline' => array(
				'enabled' => ( 'on' === get_option( 'wpa_underline' ) ) ? true : false,
				'target'  => $target,
			),
			'dir'       => $dir,
			'lang'      => $lang,
			'titles'    => $remove_titles,
			'labels'    => $apply_labels,
			'wpalabels' => $labels,
			'current'   => ( version_compare( $GLOBALS['wp_version'], '5.3', '<' ) ) ? true : false,
			'errors'    => ( $errors_enabled ) ? true : false,
			'tracking'  => ( $tracking_enabled ) ? true : false,
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'security'  => wp_create_nonce( 'wpa-stats-action' ),
			'action'    => 'wpa_stats_action',
			'url'       => ( function_exists( 'wpa_get_current_url' ) ) ? wpa_get_current_url() : 'disabled',
			'post_id'   => ( is_singular() ) ? get_the_ID() : '',
		)
	);
}

// courtesy of Graham Armfield (modified).
add_action( 'admin_bar_menu', 'wpa_logout_item', 11 );
/**
 * Add adminbar menu logout.
 *
 * @link http://www.coolfields.co.uk/2013/02/wordpress-permanently-visible-log-out-link-plugin-version-0-1/
 * @param object $admin_bar Admin bar object.
 */
function wpa_logout_item( $admin_bar ) {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$args = array(
		'id'    => 'wpa-logout',
		'title' => 'Log Out',
		'href'  => wp_logout_url(),
	);
	$admin_bar->add_node( $args );
}

add_filter( 'posts_clauses', 'wpa_search_attachment_alt', 20, 2 );
/**
 * Allow users to search alt attributes in the media library.
 *
 * @param array  $clauses WordPress post query clauses.
 * @param object $query WordPress query object.
 *
 * @return array
 */
function wpa_search_attachment_alt( $clauses, $query ) {
	if ( is_admin() && 'on' === get_option( 'wpa_search_alt' ) ) {
		global $wpdb;
		if ( isset( $query->query['post_type'] ) && 'attachment' === $query->query['post_type'] && '' !== $query->query_vars['s'] ) {
			$clauses['join'] = " LEFT JOIN {$wpdb->postmeta} AS sq1 ON ( {$wpdb->posts}.ID = sq1.post_id AND ( sq1.meta_key = '_wp_attached_file' OR sq1.meta_key = '_wp_attachment_image_alt' ) )";
		}
	}

	return $clauses;
}

add_filter( 'mce_css', 'wpa_diagnostic_css' );
/**
 * Add diagnostic CSS.
 *
 * @param string $mce_css Existing CSS.
 *
 * @return full string css.
 */
function wpa_diagnostic_css( $mce_css ) {
	if ( get_option( 'wpa_diagnostics' ) === 'on' ) {
		$mce_css .= ', ' . plugins_url( 'css/diagnostic.css', __FILE__ );
	}

	return $mce_css;
}

if ( 'on' === get_option( 'wpa_search' ) ) {
	add_filter( 'pre_get_posts', 'wpa_filter' );
}

/**
 * Filter search queries to ensure that an error page is returned if no results.
 *
 * @param object $query Main WP_Query object.
 *
 * @return $query.
 */
function wpa_filter( $query ) {
	if ( ! is_admin() ) {
		if ( isset( $_GET['s'] ) && '' === trim( $_GET['s'] ) && ( $query->is_main_query() ) ) {
			$query->query_vars['s'] = '&#32;';
			$query->set( 'is_search', 1 );
			add_action( 'template_include', 'wpa_search_error' );
		}
	}

	return $query;
}

/**
 * Locate template for the search error page.
 *
 * @param string $template Current template name.
 *
 * @return string New template name if changed.
 */
function wpa_search_error( $template ) {
	$search = locate_template( 'search.php' );
	if ( $search ) {
		return $search;
	}

	return $template;
}

if ( 'on' === get_option( 'wpa_more' ) && ! wpa_accessible_theme() ) {
	add_filter(
		'body_class',
		function( $classes ) {
			return array_merge( $classes, array( 'wpa-excerpt' ) );
		}
	);
	add_filter( 'get_the_excerpt', 'wpa_custom_excerpt_more', 100 );
	add_filter( 'excerpt_more', 'wpa_excerpt_more', 100 );
	add_filter( 'the_content_more_link', 'wpa_content_more', 100 );
}

/**
 * Custom "Continue Reading" with post title context.
 *
 * @param int $id Post ID.
 *
 * @return string HTML link & text.
 */
function wpa_continue_reading( $id ) {
	return '<a class="continue" href="' . esc_url( get_permalink( $id ) ) . '">' . get_option( 'wpa_continue' ) . '<span> ' . get_the_title( $id ) . '</span></a>';
}

/**
 * Add custom continue reading text to excerpts.
 *
 * @return Ellipsis + continue reading text.
 */
function wpa_excerpt_more() {
	global $id;

	return '&hellip; ' . wpa_continue_reading( $id );
}

/**
 * Add custom continue reading text to content.
 *
 * @return continue reading text.
 */
function wpa_content_more() {
	global $id;

	return wpa_continue_reading( $id );
}

/**
 * Add custom continue reading text to custom excerpts.
 *
 * @param string $output Existing content.
 *
 * @return continue reading text.
 */
function wpa_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		global $id;
		$output .= ' ' . wpa_continue_reading( $id ); // insert a blank space.
	}

	return $output;
}

if ( 'on' === get_option( 'rta_from_tag_clouds' ) ) {
	add_filter( 'wp_tag_cloud', 'wpa_remove_title_attributes' );
}

/**
 * Strip title attributes from tag clouds.
 *
 * @param string $output Tag Cloud.
 *
 * @return string Tag cloud without title attributes.
 */
function wpa_remove_title_attributes( $output ) {
	$output = preg_replace( '/\s*title\s*=\s*(["\']).*?\1/', '', $output );

	return $output;
}

/**
 * Create support form.
 */
function wpa_get_support_form() {
	global $current_user;
	$current_user = wp_get_current_user();
	$request      = '';
	$version      = wpa_check_version();
	// send fields for all plugins.
	$wp_version = get_bloginfo( 'version' );
	$home_url   = home_url();
	$wp_url     = site_url();
	$language   = get_bloginfo( 'language' );
	$charset    = get_bloginfo( 'charset' );
	// server.
	$php_version = phpversion();

	// theme data.
	$theme         = wp_get_theme();
	$theme_name    = $theme->get( 'Name' );
	$theme_uri     = $theme->get( 'ThemeURI' );
	$theme_parent  = $theme->get( 'Template' );
	$theme_version = $theme->get( 'Version' );

	// plugin data.
	$plugins        = get_plugins();
	$plugins_string = '';
	foreach ( array_keys( $plugins ) as $key ) {
		if ( is_plugin_active( $key ) ) {
			$plugin          =& $plugins[ $key ];
			$plugin_name     = $plugin['Name'];
			$plugin_uri      = $plugin['PluginURI'];
			$plugin_version  = $plugin['Version'];
			$plugins_string .= "$plugin_name: $plugin_version; $plugin_uri\n";
		}
	}
	$data = "
================ Installation Data ====================
==WP Accessibility==
Version: $version

==WordPress:==
Version: $wp_version
URL: $home_url
Install: $wp_url
Language: $language
Charset: $charset
Admin Email: $current_user->user_email

==Extra info:==
PHP Version: $php_version
Server Software: $_SERVER[SERVER_SOFTWARE]
User Agent: $_SERVER[HTTP_USER_AGENT]

==Theme:==
Name: $theme_name
URI: $theme_uri
Parent: $theme_parent
Version: $theme_version

==Active Plugins:==
$plugins_string
";
	if ( isset( $_POST['wpa_support'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpa-nonce' ) ) {
			wp_die( 'WP Accessibility: Security check failed' );
		}
		$request     = ( ! empty( $_POST['support_request'] ) ) ? sanitize_textarea_field( stripslashes( $_POST['support_request'] ) ) : false;
		$has_donated = ( 'on' === $_POST['has_donated'] ) ? 'Donor' : 'No donation';
		$subject     = "WP Accessibility support request. $has_donated";
		$message     = $request . "\n\n" . $data;
		// Get the site domain and get rid of www. from pluggable.php.
		$sitename = sanitize_text_field( strtolower( $_SERVER['SERVER_NAME'] ) );
		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}
		$from_email = 'wordpress@' . $sitename;
		$from       = "From: $current_user->display_name <$from_email>\r\nReply-to: $current_user->display_name <$current_user->user_email>\r\n";

		if ( ! $request ) {
			echo "<div class='message error'><p>" . __( 'Please describe your problem.', 'wp-accessibility' ) . '</p></div>';
		} else {
			wp_mail( 'plugins@joedolson.com', $subject, $message, $from );
			if ( 'Donor' === $has_donated ) {
				echo "<div class='message updated'><p>" . __( 'Thank you for supporting the continuing development of this plug-in! I\'ll get back to you as soon as I can.', 'wp-accessibility' ) . '</p></div>';
			} else {
				echo "<div class='message updated'><p>" . __( 'I cannot provide support, but will treat your request as a bug report, and will incorporate any permanent solutions I discover into the plug-in.', 'wp-accessibility' ) . '</p></div>';
			}
		}
	}
	$admin_url = admin_url( 'admin.php?page=wp-accessibility' );

	echo "
	<form method='post' action='" . esc_url( $admin_url ) . "'>
		<div><input type='hidden' name='_wpnonce' value='" . wp_create_nonce( 'wpa-nonce' ) . "' /></div>
		<div>";
	echo '
		<p>
		<code>' . __( 'From:', 'wp-accessibility' ) . " \"$current_user->display_name\" &lt;$current_user->user_email&gt;</code>
		</p>
		<p>
		<input type='checkbox' name='has_donated' id='has_donated' value='on' /> <label for='has_donated'>";
		// Translators: Donation URL.
	echo sprintf( __( 'I <a href="%s">made a donation</a> to help support this plugin', 'wp-accessibility' ), 'https://www.joedolson.com/donate/' ) . "</label>
		</p>
		<p>
		<label for='support_request'>" . __( 'Support Request:', 'wp-accessibility' ) . "</label><br /><textarea name='support_request' required id='support_request' cols='80' rows='10' class='widefat'>" . esc_textarea( stripslashes( $request ) ) . "</textarea>
		</p>
		<p>
		<input type='submit' value='" . __( 'Send Support Request', 'wp-accessibility' ) . "' name='wpa_support' class='button-primary' />
		</p>
		<p>" . __( 'The following additional information will be sent with your support request:', 'wp-accessibility' ) . "</p>
		<div class='wpa_support'>
		" . wpautop( $data ) . '
		</div>
		</div>
	</form>';
}

/**
 * Tests whether the current theme is labeled accessibility-ready
 *
 * @return boolean True if this theme has the tag 'accessibility-ready'.
 */
function wpa_accessible_theme() {
	// This is Oxygen Builder, and the active theme test is invalid.
	if ( defined( 'CT_VERSION' ) ) {
		return false;
	}
	$theme = wp_get_theme();
	$tags  = $theme->get( 'Tags' );
	if ( is_array( $tags ) && in_array( 'accessibility-ready', $tags, true ) ) {
		return true;
	}
	return false;
}

/**
 * Disable full screen block editor by default.
 */
function wpa_disable_editor_fullscreen_by_default() {
	if ( 'on' === get_option( 'wpa_disable_fullscreen' ) ) {
		$script = "window.onload = function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } }";
		wp_add_inline_script( 'wp-blocks', $script );
	}
}
add_action( 'enqueue_block_editor_assets', 'wpa_disable_editor_fullscreen_by_default' );

/**
 * Insert content summary at top of article content.
 *
 * @param string $content Post content.
 *
 * @return string
 */
function wpa_content_summary( $content ) {
	if ( is_singular() && wpa_in_post_type( get_queried_object_id() ) ) {
		$post_id = get_the_ID();
		$summary = wpa_get_content_summary( $post_id );
		if ( ! $summary ) {
			return $content;
		}
		$content = $summary . $content;
	}

	return $content;
}
add_filter( 'the_content', 'wpa_content_summary' );

/**
 * Get a simplified summary for content.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function wpa_get_content_summary( $post_id ) {
	$summary = trim( get_post_meta( $post_id, '_wpa_content_summary', true ) );
	if ( ! $summary ) {
		return '';
	}
	/**
	 * Filter the heading text for content summaries. Default `Summary`.
	 *
	 * @hook wpa_summary_heading
	 *
	 * @param {string} $heading Heading text.
	 * @param {int}    $post_id Post ID.
	 *
	 * @return {string}
	 */
	$heading = apply_filters( 'wpa_summary_heading', __( 'Summary', 'wp-accessibility' ), $post_id );
	/**
	 * Filter the heading level for content summaries. Default `h2`.
	 *
	 * @hook wpa_summary_heading_level
	 *
	 * @param {string} $heading Element selector.
	 * @param {int}    $post_id Post ID.
	 *
	 * @return {string}
	 */
	$level = apply_filters( 'wpa_summary_heading_level', 'h2', $post_id );

	$heading = "<$level>" . $heading . "</$level>";
	$content = '<section class="wpa-content-summary" id="summary-' . absint( $post_id ) . '"><div class="wpa-content-summary-inner">' . $heading . wpautop( wp_kses_post( stripslashes( $summary ) ) ) . '</div></section>';

	return $content;
}

/**
 * Check whether a given post is in an allowed post type for content summaries.
 *
 * @param integer $post_id Post ID.
 *
 * @return boolean True if post is allowed, false otherwise.
 */
function wpa_in_post_type( $post_id ) {
	$settings = get_option( 'wpa_post_types', array() );
	if ( is_array( $settings ) && ! empty( $settings ) ) {
		$type = get_post_type( $post_id );
		if ( in_array( $type, $settings, true ) ) {
			return true;
		}
	}

	return false;
}

add_action( 'admin_menu', 'wpa_add_outer_box' );
/**
 * Add metabox for content summaries.
 */
function wpa_add_outer_box() {
	$allowed = get_option( 'wpa_post_types', array() );
	if ( is_array( $allowed ) ) {
		foreach ( $allowed as $post_type ) {
			add_meta_box( 'wpa_content_summary', __( 'Content Summary', 'wp-accessibility' ), 'wpa_add_inner_box', $post_type, 'normal', 'high' );
		}
	}
}

/**
 * Render content summary form.
 */
function wpa_add_inner_box() {
	global $post;
	$summary = get_post_meta( $post->ID, '_wpa_content_summary', true );
	$nonce   = wp_nonce_field( 'wpa-nonce-field', 'wpa_nonce_name', true, false );
	echo $nonce;
	?>
	<p class='wpa-content-summary-field'>
		<label for="wpa_content_summary"><?php _e( 'Simple Content Summary', 'wp-accessibility' ); ?></label><br/>
		<textarea class="wpa-content-summary widefat" name="wpa_content_summary" id="wpa_content_summary" rows="4" cols="60" aria-describedy="content-summary-description"><?php echo esc_textarea( $summary ); ?></textarea>
		<span id="content-summary-description"><?php _e( 'Provide a simplified summary to aid comprehension of complex content.', 'wp-accessibility' ); ?></span>
	</p>
	<?php
}

/**
 * Save content summary from post meta box.
 *
 * @param int    $id Post ID.
 * @param object $post Post.
 *
 * @return int
 */
function wpa_save_content_summary( $id, $post ) {
	if ( empty( $_POST ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $id ) || isset( $_POST['_inline_edit'] ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! wpa_in_post_type( $id ) ) {
		return $id;
	}

	// verify this came from our screen and with proper authorization.
	// because save_post can be triggered at other times.
	if ( isset( $_POST['wpa_nonce_name'] ) ) {
		if ( ! wp_verify_nonce( $_POST['wpa_nonce_name'], 'wpa-nonce-field' ) ) {
			return $id;
		}
		$summary = isset( $_POST['wpa_content_summary'] ) ? wp_kses_post( $_POST['wpa_content_summary'] ) : '';
		update_post_meta( $id, '_wpa_content_summary', $summary );
	}

	return $id;
}
add_action( 'save_post', 'wpa_save_content_summary', 10, 2 );

