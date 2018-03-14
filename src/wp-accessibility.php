<?php
/**
 * WP Accessibility
 *
 * @package     WP Accessibility
 * @author      Joe Dolson
 * @copyright   2012-2018 Joe Dolson
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
 * Version: 1.6.1
 */

 /*
	Copyright 2012-2018  Joe Dolson (email : joe@joedolson.com)

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

include( dirname( __FILE__ ) . '/wp-accessibility-settings.php' );
include( dirname( __FILE__ ) . '/class-wp-accessibility-toolbar.php' );
include( dirname( __FILE__ ) . '/wp-accessibility-toolbar.php' );
register_activation_hook( __FILE__, 'wpa_install' );

add_action( 'plugins_loaded', 'wpa_load_textdomain' );
/**
 * Load internationalization.
 */
function wpa_load_textdomain() {
	load_plugin_textdomain( 'wp-accessibility', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

add_action( 'admin_menu', 'add_wpa_admin_menu' );
/**
 * Set up admin menu.
 */
function add_wpa_admin_menu() {
	add_action( 'admin_print_footer_scripts', 'wpa_write_js' );
	add_options_page( 'WP Accessibility', 'WP Accessibility', 'manage_options', __FILE__, 'wpa_admin_menu' );
}

/**
 * Write admin JS.
 */
function wpa_write_js() {
	global $current_screen;
	if ( 'settings_page_wp-accessibility/wp-accessibility' == $current_screen->base ) {
		?>
<script>
	//<![CDATA[
	(function ($) {
		'use strict';
		$('#fore').farbtastic('#color1');
		$('#back').farbtastic('#color2');
	}(jQuery));
	//]]>
</script>
	<?php
	}
}

/**
 * Install on activation.
 */
function wpa_install() {
	$wpa_version = '1.6.1';
	if ( get_option( 'wpa_installed' ) != 'true' ) {
		add_option( 'rta_from_nav_menu', 'on' );
		add_option( 'rta_from_page_lists', 'on' );
		add_option( 'rta_from_category_lists', 'on' );
		add_option( 'rta_from_archive_links', 'on' );
		add_option( 'rta_from_tag_clouds', 'on' );
		add_option( 'rta_from_category_links', 'on' );
		add_option( 'rta_from_post_edit_links', 'on' );
		add_option( 'rta_from_edit_comment_links', 'on' );
		add_option( 'asl_styles_focus', '' );
		add_option( 'asl_styles_passive', '' );
		add_option( 'wpa_target', 'on' );
		add_option( 'wpa_search', 'on' );
		add_option( 'wpa_tabindex', 'on' );
		add_option( 'wpa_continue', 'Continue Reading' );
		add_option( 'wpa_focus', '' );
		add_option( 'wpa_installed', 'true' );
		add_option( 'wpa_version', $wpa_version );
		add_option( 'wpa_longdesc', 'jquery' );
	} else {
		wpa_check_version();
		update_option( 'wpa_version', $wpa_version );
	}
}

/**
 * Check current version and upgrade if needed.
 */
function wpa_check_version() {
	// upgrade for version 1.3.0.
	if ( version_compare( get_option( 'wpa_version' ), '1.3.0', '<' ) ) {
		add_option( 'wpa_longdesc', 'jquery' );
	}
}

add_filter( 'plugin_action_links', 'wpa_plugin_action', 10, 2 );
/**
 * Add plugin action links.
 *
 * @param array  $links Existing links.
 * @param string $file File name.
 */
function wpa_plugin_action( $links, $file ) {
	if ( plugin_basename( dirname( __FILE__ ) . '/wp-accessibility.php' ) == $file ) {
		$admin_url = admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' );
		$links[]   = "<a href='$admin_url'>" . __( 'Accessibility Settings', 'wp-accessibility' ) . '</a>';
	}

	return $links;
}

add_action( 'admin_enqueue_scripts', 'wpa_admin_js' );
/**
 * Enqueue color picker for contrast testing
 **/
function wpa_admin_js() {
	global $current_screen;
	if ( 'settings_page_wp-accessibility/wp-accessibility' == $current_screen->base ) {
		wp_enqueue_script( 'farbtastic' );
	}
}

add_action( 'wp_enqueue_scripts', 'wpa_register_scripts' );
/**
 * Register jQuery scripts.
 */
function wpa_register_scripts() {
	wp_register_script( 'skiplinks.webkit', plugins_url( 'wp-accessibility/js/skiplinks.webkit.js' ) );
	wp_register_script( 'ui-a11y.js', plugins_url( 'wp-accessibility/toolbar/js/a11y.js' ), array( 'jquery' ), '1.0', true );
	wp_register_script( 'scrollTo', plugins_url( 'wp-accessibility/toolbar/js/jquery.scrollto.min.js' ), array( 'jquery' ), '1.4.5', true );
}

add_action( 'wp_enqueue_scripts', 'wpacc_enqueue_scripts' );
/**
 * Enqueue accessibility scripts dependent on options.
 */
function wpacc_enqueue_scripts() {
	wp_enqueue_script( 'jquery' );
	if ( 'on' == get_option( 'asl_enable' ) ) {
		wp_enqueue_script( 'skiplinks.webkit' );
	}
	if ( 'on' == get_option( 'wpa_toolbar' ) || 'on' == get_option( 'wpa_widget_toolbar' ) ) {
		wp_enqueue_script( 'scrollTo' );
		wp_enqueue_script( 'ui-a11y.js' );
		$plugin_path = plugins_url( 'wp-accessibility/toolbar/css/a11y-contrast.css' );
		if ( file_exists( get_stylesheet_directory() . '/a11y-contrast.css' ) ) {
			$plugin_path = get_stylesheet_directory_uri() . '/a11y-contrast.css';
		}
		wp_localize_script( 'ui-a11y.js', 'a11y_stylesheet_path', $plugin_path );
	}
	if ( 'on' == get_option( 'wpa_insert_roles' ) ) {
		wp_enqueue_script( 'wpa-complementary', plugins_url( 'js/roles.jquery.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		if ( get_option( 'wpa_complementary_container' ) ) {
			$wpa_comp = get_option( 'wpa_complementary_container' );
		} else {
			$wpa_comp = false;
		}
		wp_localize_script( 'wpa-complementary', 'wpaComplementary', $wpa_comp );
	}
	if ( 'on' == get_option( 'wpa_labels' ) ) {
		wp_enqueue_script( 'wpa-labels', plugins_url( 'js/wpa.labels.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		$labels = array(
			's'       => __( 'Search', 'wp-accessibility' ),
			'author'  => __( 'Name', 'wp-accessibility' ),
			'email'   => __( 'Email', 'wp-accessibility' ),
			'url'     => __( 'Website', 'wp-accessibility' ),
			'comment' => __( 'Comment', 'wp-accessibility' ),
		);
		wp_localize_script( 'wpa-labels', 'wpalabels', $labels );
	}
	if ( 'on' == get_option( 'wpa_toolbar' ) ) {
		add_action( 'wp_footer', 'wpa_toolbar_js' );
	}
	if ( 'link' == get_option( 'wpa_longdesc' ) ) {
		wp_enqueue_script( 'longdesc.link', plugins_url( 'js/longdesc.link.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	}
	if ( 'jquery' == get_option( 'wpa_longdesc' ) ) {
		wp_enqueue_script( 'longdesc.button', plugins_url( 'js/longdesc.button.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	}
	if ( 'on' == get_option( 'wpa_current_menu' ) ) {
		wp_enqueue_script( 'current.menu', plugins_url( 'js/current-menu-item.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	}
}

add_action( 'wp_enqueue_scripts', 'wpa_stylesheet' );
/**
 * Enqueue stylesheets for WP Accessibility.
 */
function wpa_stylesheet() {
	// Respects SSL, Style.css is relative to the current file.
	wp_register_style( 'wpa-style', plugins_url( 'css/wpa-style.css', __FILE__ ) );
	wp_register_style( 'ui-font.css', plugins_url( 'toolbar/fonts/css/a11y-toolbar.css', __FILE__ ) );
	$toolbar = apply_filters( 'wpa_toolbar_css', plugins_url( 'toolbar/css/a11y.css', __FILE__ ) );
	wp_register_style( 'ui-a11y.css', $toolbar, array( 'ui-font.css' ) );
	$fontsize_stylesheet = ( 'on' == get_option( 'wpa_alternate_fontsize' ) ) ? 'a11y-fontsize-alt' : 'a11y-fontsize';
	$fontsize            = apply_filters( 'wpa_fontsize_css', plugins_url( 'toolbar/css/' . $fontsize_stylesheet . '.css', __FILE__ ) );
	wp_register_style( 'ui-fontsize.css', $fontsize );
	// Only enable styles when required by options.
	if ( get_option( 'wpa_toolbar_size' ) && 'on' == get_option( 'wpa_toolbar' ) ) {
		echo "
<style type='text/css'>
.a11y-toolbar ul li button {
	font-size: " . get_option( 'wpa_toolbar_size' ) . ' !important;
}
</style>';
	}
	if ( 'link' == get_option( 'wpa_longdesc' ) || 'jquery' == get_option( 'wpa_longdesc' ) || 'on' == get_option( 'asl_enable' ) ) {
		wp_enqueue_style( 'wpa-style' );
	}
	if ( 'on' == get_option( 'wpa_toolbar' ) || 'on' == get_option( 'wpa_widget_toolbar' ) && ( $toolbar && $fontsize ) ) {
		wp_enqueue_style( 'ui-a11y.css' );
		wp_enqueue_style( 'ui-fontsize.css' );
	}
	if ( current_user_can( 'edit_files' ) && 'on' == get_option( 'wpa_diagnostics' ) ) {
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

	if ( 'on' == get_option( 'wpa_row_actions' ) ) {
		if ( file_exists( get_stylesheet_directory() . '/wp-admin-row-actions.css' ) ) {
			$file = get_stylesheet_directory_uri() . '/wp-admin-row-actions.css';
		} else {
			$file = plugins_url( 'css/wp-admin-row-actions.css', __FILE__ );
		}
		wp_register_style( 'wp-row-actions', $file );
		wp_enqueue_style( 'wp-row-actions' );
	}
}

add_action( 'wp_head', 'wpa_css' );
/**
 * Generate styles needed for WP Accessibility options.
 */
function wpa_css() {
	$styles = '';
	if ( get_option( 'asl_enable' ) == 'on' ) {
		$focus = get_option( 'asl_styles_focus' );
		// these styles are derived from the WordPress skip link defaults.
		$default_focus = 'background-color: #f1f1f1; box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6); clip: auto; color: #21759b; display: block; font-size: 14px; font-weight: bold; height: auto; line-height: normal; padding: 15px 23px 14px; position: absolute; left: 5px; top: 5px; text-decoration: none; text-transform: none; width: auto; z-index: 100000;';
		if ( ! $focus ) {
			$focus = $default_focus;
		} else {
			$focus = $default_focus . $focus;
		}
		$passive = get_option( 'asl_styles_passive' );
		$vis     = '';
		$invis   = '';
		// If links are visible, "hover" is a focus style, otherwise, it's a passive style.
		if ( 'on' == get_option( 'asl_visible' ) ) {
			$vis = '#skiplinks a:hover,';
		} else {
			$invis = '#skiplinks a:hover,';
		}
		$visibility = ( 'on' == get_option( 'asl_visible' ) ) ? 'wpa-visible' : 'wpa-hide';
		$is_rtl     = ( is_rtl() ) ? '-rtl' : '-ltr';
		$class      = '.' . $visibility . $is_rtl;
		$styles    .= "
		$class#skiplinks a, $invis $class#skiplinks a:visited { $passive }
		$class#skiplinks a:active, $vis $class#skiplinks a:focus { $focus  }
		";
	}
	if ( 'on' == get_option( 'wpa_focus' ) ) {
		$color   = ( '' != get_option( 'wpa_focus_color' ) ) ? ' #' . get_option( 'wpa_focus_color' ) : '';
		$styles .= "
		:focus { outline: 1px solid$color!important; }
		";
	}
	if ( '' != $styles ) {
		echo "
<style type='text/css'>
	$styles
</style>";
	}
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

add_action( 'wp_footer', 'wpa_jquery_asl', 100 );
/**
 * Generate JS needed for options.
 */
function wpa_jquery_asl() {
	$skiplinks_js = false;
	$targets      = false;
	$lang_js      = false;
	$tabindex     = false;
	$longdesc     = false;
	$visibility   = ( 'on' == get_option( 'asl_visible' ) ) ? 'wpa-visible' : 'wpa-hide';
	if ( 'on' == get_option( 'asl_enable' ) ) {
		$html = '';
		// set up skiplinks.
		$extra = get_option( 'asl_extra_target' );
		$extra = ( wpa_is_url( $extra ) ) ? esc_url( $extra ) : str_replace( '#', '', esc_attr( $extra ) );
		if ( '' != $extra && ! wpa_is_url( $extra ) ) {
			$extra = "#$extra";
		}
		$extra_text = stripslashes( get_option( 'asl_extra_text' ) );
		$content    = str_replace( '#', '', esc_attr( get_option( 'asl_content' ) ) );
		$nav        = str_replace( '#', '', esc_attr( get_option( 'asl_navigation' ) ) );
		$sitemap    = esc_url( get_option( 'asl_sitemap' ) );
		$html      .= ( '' != $content ) ? "<a href=\"#$content\">" . __( 'Skip to content', 'wp-accessibility' ) . '</a> ' : '';
		$html      .= ( '' != $nav ) ? "<a href=\"#$nav\">" . __( 'Skip to navigation', 'wp-accessibility' ) . '</a> ' : '';
		$html      .= ( '' != $sitemap ) ? "<a href=\"$sitemap\">" . __( 'Site map', 'wp-accessibility' ) . '</a> ' : '';
		$html      .= ( '' != $extra && '' != $extra_text ) ? "<a href=\"$extra\">$extra_text</a> " : '';
		$is_rtl     = ( is_rtl() ) ? '-rtl' : '-ltr';
		$skiplinks  = __( 'Skip links', 'wp-accessibility' );
		$output     = ( '' != $html ) ? "<div class=\"$visibility$is_rtl\" id=\"skiplinks\" role=\"navigation\" aria-label=\"$skiplinks\">$html</div>" : '';
		// Attach skiplinks HTML; set tab index on #content area to -1.
		$focusable    = ( '' != $content ) ? "$('#$content').attr('tabindex','-1');" : '';
		$focusable   .= ( '' != $nav ) ? "$('#$nav').attr('tabindex','-1');" : '';
		$skiplinks_js = ( $output ) ? "$('body').prepend('$output'); $focusable" : '';
	}
	// Attach language to html element.
	if ( 'on' == get_option( 'wpa_lang' ) ) {
		$lang    = get_bloginfo( 'language' );
		$dir     = ( is_rtl() ) ? 'rtl' : 'ltr';
		$lang_js = "$('html').attr( 'lang','$lang' ); $('html').attr( 'dir','$dir' )";
	}
	// Force links to open in the same window.
	$underline_target = apply_filters( 'wpa_underline_target', 'a' );
	$targets          = ( 'on' == get_option( 'wpa_target' ) ) ? "$('a').removeAttr('target');" : '';
	$tabindex         = ( 'on' == get_option( 'wpa_tabindex' ) ) ? "$('input,a,select,textarea,button').removeAttr('tabindex');" : '';
	$underlines       = ( 'on' == get_option( 'wpa_underline' ) ) ? "$('$underline_target').css( 'text-decoration','underline' );$('$underline_target').on( 'focusin mouseenter', function() { $(this).css( 'text-decoration','none' ); });$('$underline_target').on( 'focusout mouseleave', function() { $(this).css( 'text-decoration','underline' ); } );" : '';

	$display = ( $skiplinks_js || $targets || $lang_js || $tabindex || $longdesc ) ? true : false;
	if ( $display ) {
		$script = "
<script type='text/javascript'>
//<![CDATA[
(function( $ ) { 'use strict';
	$skiplinks_js
	$targets
	$lang_js
	$tabindex
	$underlines
}(jQuery));
//]]>
</script>";
		echo $script;
	}
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

add_filter( 'mce_css', 'wp_diagnostic_css' );
/**
 * Add diagnostic CSS.
 *
 * @param string $mce_css Existing CSS.
 *
 * @return full string css.
 */
function wp_diagnostic_css( $mce_css ) {
	if ( get_option( 'wpa_diagnostics' ) == 'on' ) {
		$mce_css .= ', ' . plugins_url( 'css/diagnostic.css', __FILE__ );
	}

	return $mce_css;
}

/**
 * Measure the relative luminosity between two RGB values.
 *
 * @param int $r Red value 1.
 * @param int $r2 Red value 2.
 * @param int $g Green value 1.
 * @param int $g2 Green value 2.
 * @param int $b Blue value 1.
 * @param int $b2 Blue value 2.
 *
 * @return luminosity ratio.
 */
function wpa_luminosity( $r, $r2, $g, $g2, $b, $b2 ) {
	$rs_rgb = $r / 255;
	$gs_rgb = $g / 255;
	$bs_rgb = $b / 255;
	$r_new  = ( $rs_rgb <= 0.03928 ) ? $rs_rgb / 12.92 : pow( ( $rs_rgb + 0.055 ) / 1.055, 2.4 );
	$g_new  = ( $gs_rgb <= 0.03928 ) ? $gs_rgb / 12.92 : pow( ( $gs_rgb + 0.055 ) / 1.055, 2.4 );
	$b_new  = ( $bs_rgb <= 0.03928 ) ? $bs_rgb / 12.92 : pow( ( $bs_rgb + 0.055 ) / 1.055, 2.4 );

	$rs_rgb2 = $r2 / 255;
	$gs_rgb2 = $g2 / 255;
	$bs_rgb2 = $b2 / 255;
	$r2_new  = ( $rs_rgb2 <= 0.03928 ) ? $rs_rgb2 / 12.92 : pow( ( $rs_rgb2 + 0.055 ) / 1.055, 2.4 );
	$g2_new  = ( $gs_rgb2 <= 0.03928 ) ? $gs_rgb2 / 12.92 : pow( ( $gs_rgb2 + 0.055 ) / 1.055, 2.4 );
	$b2_new  = ( $bs_rgb2 <= 0.03928 ) ? $bs_rgb2 / 12.92 : pow( ( $bs_rgb2 + 0.055 ) / 1.055, 2.4 );

	if ( $r + $g + $b <= $r2 + $g2 + $b2 ) {
		$l2 = ( .2126 * $r_new + 0.7152 * $g_new + 0.0722 * $b_new );
		$l1 = ( .2126 * $r2_new + 0.7152 * $b2_new + 0.0722 * $b2_new );
	} else {
		$l1 = ( .2126 * $r_new + 0.7152 * $g_new + 0.0722 * $b_new );
		$l2 = ( .2126 * $r2_new + 0.7152 * $g2_new + 0.0722 * $b2_new );
	}
	$luminosity = round( ( $l1 + 0.05 ) / ( $l2 + 0.05 ), 2 );

	return $luminosity;
}

/**
 * Convert an RGB value to a HEX value.
 *
 * @param int $r Red value.
 * @param int $g Green value.
 * @param int $b Blue value.
 *
 * @return Hexadecimal color equivalent.
 */
function wpa_rgb2hex( $r, $g = - 1, $b = - 1 ) {
	if ( is_array( $r ) && sizeof( $r ) == 3 ) {
		list( $r, $g, $b ) = $r;
	}
	$r = intval( $r );
	$g = intval( $g );
	$b = intval( $b );

	$r = dechex( $r < 0 ? 0 : ( $r > 255 ? 255 : $r ) );
	$g = dechex( $g < 0 ? 0 : ( $g > 255 ? 255 : $g ) );
	$b = dechex( $b < 0 ? 0 : ( $b > 255 ? 255 : $b ) );

	$color  = ( strlen( $r ) < 2 ? '0' : '' ) . $r;
	$color .= ( strlen( $g ) < 2 ? '0' : '' ) . $g;
	$color .= ( strlen( $b ) < 2 ? '0' : '' ) . $b;

	return '#' . $color;
}

/**
 * Convert a Hexadecimal color value to RGB.
 *
 * @param string $color Hexadecimal value for a color.
 *
 * @return array of RGB values in R,G,B order.
 */
function wpa_hex2rgb( $color ) {
	$color = str_replace( '#', '', $color );
	if ( strlen( $color ) != 6 ) {
		return array( 0, 0, 0 );
	}
	$rgb = array();
	for ( $x = 0; $x < 3; $x ++ ) {
		$rgb[ $x ] = hexdec( substr( $color, ( 2 * $x ), 2 ) );
	}

	return $rgb;
}

/**
 * Calculate the luminosity ratio between two color values.
 */
function wpa_contrast() {
	if ( ! empty( $_POST ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpa-nonce' ) ) {
			die( 'Security check failed' );
		}
		if ( isset( $_POST['color'] ) && '' != $_POST['color'] ) {
			$fore_color = $_POST['color'];
			if ( '#' == $fore_color[0] ) {
				$fore_color = str_replace( '#', '', $fore_color );
			}
			if ( 3 == strlen( $fore_color ) ) {
				$color6char  = $fore_color[0] . $fore_color[0];
				$color6char .= $fore_color[1] . $fore_color[1];
				$color6char .= $fore_color[2] . $fore_color[2];
				$fore_color  = $color6char;
			}
			if ( preg_match( '/^#?([0-9a-f]{1,2}){3}$/i', $fore_color ) ) {
				$echo_hex_fore = str_replace( '#', '', $fore_color );
			} else {
				$echo_hex_fore = 'FFFFFF';
			}
			if ( isset( $_POST['color2'] ) && '' != $_POST['color2'] ) {
				$back_color = $_POST['color2'];
				if ( '#' == $back_color[0] ) {
					$back_color = str_replace( '#', '', $back_color );
				}
				if ( 3 == strlen( $back_color ) ) {
					$color6char  = $back_color[0] . $back_color[0];
					$color6char .= $back_color[1] . $back_color[1];
					$color6char .= $back_color[2] . $back_color[2];
					$back_color  = $color6char;
				}
				if ( preg_match( '/^#?([0-9a-f]{1,2}){3}$/i', $back_color ) ) {
					$echo_hex_back = str_replace( '#', '', $back_color );
				} else {
					$echo_hex_back = 'FFFFFF';
				}
				$color  = wpa_hex2rgb( $echo_hex_fore );
				$color2 = wpa_hex2rgb( $echo_hex_back );
				$rfore  = $color[0];
				$gfore  = $color[1];
				$bfore  = $color[2];
				$rback  = $color2[0];
				$gback  = $color2[1];
				$bback  = $color2[2];
				$colors = array(
					'hex1'   => $echo_hex_fore,
					'hex2'   => $echo_hex_back,
					'red1'   => $rfore,
					'green1' => $gfore,
					'blue1'  => $bfore,
					'red2'   => $rback,
					'green2' => $gback,
					'blue2'  => $bback,
				);

				return $colors;
			} else {
				return false;
			}
		}
	}

	return false;
}

if ( 'on' == get_option( 'wpa_search' ) ) {
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
		if ( isset( $_GET['s'] ) && null == trim( $_GET['s'] ) && ( $query->is_main_query() ) ) {
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

if ( 'on' == get_option( 'wpa_image_titles' ) ) {
	add_filter( 'the_content', 'wpa_image_titles', 100 );
	add_filter( 'post_thumbnail_html', 'wpa_image_titles', 100 );
	add_filter( 'wp_get_attachment_image', 'wpa_image_titles', 100 );
}

/**
 * Filter out title attributes on images.
 *
 * @param string $content A block of content in an image, post thumbnail, or post content.
 *
 * @return string $content minus title attributes.
 */
function wpa_image_titles( $content ) {
	$results = array();
	preg_match_all( '|title="[^"]*"|U', $content, $results );
	foreach ( $results[0] as $img ) {
		$content = str_replace( $img, '', $content );
	}

	return $content;
}

if ( 'on' == get_option( 'wpa_more' ) ) {
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
	return '<a class="continue" href="' . get_permalink( $id ) . '">' . get_option( 'wpa_continue' ) . '<span> ' . get_the_title( $id ) . '</span></a>';
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

add_action( 'admin_head', 'wpa_admin_styles' );
/**
 * Enqueue admin stylesheets.
 */
function wpa_admin_styles() {
	if ( isset( $_GET['page'] ) && ( 'wp-accessibility/wp-accessibility.php' == $_GET['page'] ) ) {
		wp_enqueue_style( 'farbtastic' );
		echo '<link type="text/css" rel="stylesheet" href="' . plugins_url( 'css/wpa-styles.css', __FILE__ ) . '" />';
	}
}

if ( 'on' == get_option( 'rta_from_tag_clouds' ) ) {
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
	global $current_user, $wpa_version;
	$current_user = wp_get_current_user();
	$request      = '';
	$version      = $wpa_version;
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
			die( 'Security check failed' );
		}
		$request      = ( ! empty( $_POST['support_request'] ) ) ? stripslashes( $_POST['support_request'] ) : false;
		$has_donated  = ( 'on' == $_POST['has_donated'] ) ? 'Donor' : 'No donation';
		$has_read_faq = ( 'on' == $_POST['has_read_faq'] ) ? 'Read FAQ' : false;
		$subject      = "WP Accessibility support request. $has_donated";
		$message      = $request . "\n\n" . $data;
		// Get the site domain and get rid of www. from pluggable.php.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( 'www.' == substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}
		$from_email = 'wordpress@' . $sitename;
		$from       = "From: \"$current_user->display_name\" <$from_email>\r\nReply-to: \"$current_user->display_name\" <$current_user->user_email>\r\n";

		if ( ! $has_read_faq ) {
			echo "<div class='message error'><p>" . __( 'Please read the FAQ and other Help documents before making a support request.', 'wp-accessibility' ) . '</p></div>';
		} elseif ( ! $request ) {
			echo "<div class='message error'><p>" . __( 'Please describe your problem. I\'m not psychic.', 'wp-accessibility' ) . '</p></div>';
		} else {
			wp_mail( 'plugins@joedolson.com', $subject, $message, $from );
			if ( 'Donor' == $has_donated ) {
				echo "<div class='message updated'><p>" . __( 'Thank you for supporting the continuing development of this plug-in! I\'ll get back to you as soon as I can.', 'wp-accessibility' ) . '</p></div>';
			} else {
				echo "<div class='message updated'><p>" . __( 'I cannot provide support, but will treat your request as a bug report, and will incorporate any permanent solutions I discover into the plug-in.', 'wp-accessibility' ) . '</p></div>';
			}
		}
	}
	$admin_url = admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' );

	echo "
	<form method='post' action='$admin_url'>
		<div><input type='hidden' name='_wpnonce' value='" . wp_create_nonce( 'wpa-nonce' ) . "' /></div>
		<div>";
	echo '
		<p>
		<code>' . __( 'From:', 'wp-accessibility' ) . " \"$current_user->display_name\" &lt;$current_user->user_email&gt;</code>
		</p>
		<p>";
		// Translators: Frequently Asked Questions URL.
	echo "<input type='checkbox' name='has_read_faq' id='has_read_faq' value='on' /> <label for='has_read_faq'>" . sprintf( __( 'I have read <a href="%s">the FAQ for this plug-in</a> <span>(required)</span>', 'wp-accessibility' ), 'http://www.joedolson.com/wp-accessibility/faqs/' ) . "</label></p>
		<p>
		<input type='checkbox' name='has_donated' id='has_donated' value='on' /> <label for='has_donated'>";
		// Translators: Donation URL.
	echo sprintf( __( 'I <a href="%s">made a donation</a> to help support this plugin', 'wp-accessibility' ), 'https://www.joedolson.com/donate/' ) . "</label>
		</p>
		<p>
		<label for='support_request'>" . __( 'Support Request:', 'wp-accessibility' ) . "</label><br /><textarea name='support_request' required id='support_request' cols='80' rows='10' class='widefat'>" . stripslashes( $request ) . "</textarea>
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

add_filter( 'wp_get_attachment_image_attributes', 'wpa_featured_longdesc', 10, 3 );
/**
 * Get long descriptions for featured images.
 *
 * @param array              $attr Existing image attributes.
 * @param object             $attachment Current image attachment object.
 * @param mixed array/string $size Image size shown.
 *
 * @return New attributes array.
 */
function wpa_featured_longdesc( $attr, $attachment, $size ) {
	if ( 'on' == get_option( 'wpa_longdesc_featured' ) ) {
		$attachment_id = $attachment->ID;
		$args          = array( 'longdesc' => $attachment_id );
		// The referrer is the post that the image is inserted into.
		if ( isset( $_REQUEST['post_id'] ) || get_the_ID() ) {
			$id               = ( isset( $_REQUEST['post_id'] ) ) ? $_REQUEST['post_id'] : get_the_ID();
			$args['referrer'] = intval( $id );
		}

		$target = add_query_arg( $args, home_url() );
		$id     = longdesc_return_anchor( $attachment_id );

		$attr['longdesc'] = $target;
		$attr['id']       = $id;
	}

	return $attr;
}


// longdesc support, based on work by Michael Fields (http://wordpress.org/plugins/long-description-for-image-attachments/).
define( 'WPA_TEMPLATES', trailingslashit( dirname( __FILE__ ) ) . 'templates/' );
add_action( 'template_redirect', 'longdesc_template' );
/**
 * Load Template.
 *
 * The ID for an image attachment is expected to be
 * passed via $_GET['longdesc']. If this value exists
 * and a post is successfully queried, postdata will
 * be prepared and a template will be loaded to display
 * the post content.
 *
 * This template must be named "longdesc-template.php".
 *
 * First, this function will look in the child theme
 * then in the parent theme and if no template is found
 * in either theme, the default template will be loaded
 * from the plugin's folder.
 *
 * This function is hooked into the "template_redirect"
 * action and terminates script execution.
 *
 * @return void
 * @link http://wordpress.org/plugins/long-description-for-image-attachments/
 * @since 2010-09-26
 * @alter 2011-03-27
 */
function longdesc_template() {
	// Return early if there is no reason to proceed.
	if ( ! isset( $_GET['longdesc'] ) ) {
		return;
	}

	global $post;

	// Get the image attachment's data.
	$id   = absint( $_GET['longdesc'] );
	$post = get_post( $id );
	if ( is_object( $post ) ) {
		setup_postdata( $post );
	}

	// Attachment must be an image.
	if ( false === strpos( get_post_mime_type(), 'image' ) ) {
		header( 'HTTP/1.0 404 Not Found' );
		exit;
	}

	// The whole point here is to NOT show an image :).
	remove_filter( 'the_content', 'prepend_attachment' );

	// Check to see if there is a template in the theme.
	$template = locate_template( array( 'longdesc-template.php' ) );
	if ( ! empty( $template ) ) {
		require_once( $template );
		exit;
	} // Use plugin's template file.
	else {
		require_once( WPA_TEMPLATES . 'longdesc-template.php' );
		exit;
	}

	// You've gone too far. Error case.
	header( 'HTTP/1.0 404 Not Found' );
	exit;
}

/**
 * Anchor.
 *
 * Create anchor id for linking from a Long Description to referring post.
 * Also creates an anchor to return from Long Description page.
 *
 * @param int $id ID of the post which contains an image with a longdesc attribute.
 *
 * @return string
 * @since 2010-09-26
 */
function longdesc_return_anchor( $id ) {
	return 'longdesc-return-' . $id;
}

/**
 * Add Attribute.
 *
 * Add longdesc attribute when WordPress sends image to the editor.
 * Also creates an anchor to return from Long Description page.
 *
 * @param string $html Image HTML.
 * @param int    $id Post ID.
 * @param string $caption Caption text.
 * @param string $title Image title.
 * @param string $align Image alignment.
 * @param string $url Image URL.
 * @param array  $size Image size.
 * @param string $alt Image alt attribute.
 *
 * @return string
 *
 * @since 2010-09-20
 * @alter 2011-04-06
 */
function longdesc_add_attr( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	// Get data for the image attachment.
	$image = get_post( $id );
	global $post_ID;
	if ( isset( $image->ID ) && ! empty( $image->ID ) ) {
		$args = array( 'longdesc' => $image->ID );
		// The referrer is the post that the image is inserted into.
		if ( isset( $_REQUEST['post_id'] ) || get_the_ID() ) {
			$id               = ( isset( $_REQUEST['post_id'] ) ) ? $_REQUEST['post_id'] : get_the_ID();
			$args['referrer'] = intval( $id );
		}
		if ( ! empty( $image->post_content ) ) {
			$search  = '<img ';
			$replace = '<img tabindex="-1" id="' . esc_attr( longdesc_return_anchor( $image->ID ) ) . '" longdesc="' . esc_url( add_query_arg( $args, home_url() ) ) . '" ';
			$html    = str_replace( $search, $replace, $html );
		}
	}

	return $html;
}

add_filter( 'image_send_to_editor', 'longdesc_add_attr', 10, 8 );
/**
 * Tests whether the current theme is labeled accessibility-ready
 *
 * @return boolean True if this theme has the tag 'accessibility-ready'.
 */
function wpa_accessible_theme() {
	$theme = wp_get_theme();
	$tags  = $theme->get( 'Tags' );
	if ( is_array( $tags ) && in_array( 'accessibility-ready', $tags ) ) {
		return true;
	}
	return false;
}

add_filter( 'manage_media_columns', 'wpa_media_columns' );
add_action( 'manage_media_custom_column', 'wpa_media_value', 10, 2 );
/**
 * Add column to media column table view indicating images with no alt attribute not also checked as decorative.
 *
 * @param array $columns Current table view columns.
 *
 * @return columns.
 */
function wpa_media_columns( $columns ) {
	$columns['wpa_data'] = __( 'Accessibility', 'wp-accessibility' );

	return $columns;
}

/**
 * Get media values for current item to indicate alt status.
 *
 * @param array $column Name of column being checked.
 * @param int   $id ID of object thiss row belongs to.
 *
 * @return String alt attribute status for this object.
 */
function wpa_media_value( $column, $id ) {
	if ( 'wpa_data' == $column ) {
		$mime = get_post_mime_type( $id );
		switch ( $mime ) {
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				$alt    = get_post_meta( $id, '_wp_attachment_image_alt', true );
				$no_alt = get_post_meta( $id, '_no_alt', true );
				if ( ! $alt && ! $no_alt ) {
					echo '<span class="missing"><span class="dashicons dashicons-no" aria-hidden="true"></span> <a href="' . get_edit_post_link( $id ) . '#attachment_alt">' . __( 'Add <code>alt</code> text', 'wp-accessibility' ) . '</a></span>';
				} else {
					if ( 1 == $no_alt ) {
						echo '<span class="ok"><span class="dashicons dashicons-yes" aria-hidden="true"></span> ' . __( 'Decorative', 'wp-accessibility' ) . '</span>';
					} else {
						echo '<span class="ok"><span class="dashicons dashicons-yes" aria-hidden="true"></span> ' . __( 'Has <code>alt</code>', 'wp-accessibility' ) . '</span>';
					}
				}
				break;
			default:
				echo '<span class="non-image">' . __( 'N/A', 'wp-accessibility' ) . '</span>';
				break;
		}
	}
	return $column;
}

add_filter( 'attachment_fields_to_edit', 'wpa_insert_alt_verification', 10, 2 );
/**
 * Insert custom fields into attachment editor for alt verification.
 *
 * @param array  $form_fields Existing form fields.
 * @param object $post Media attachment object.
 *
 * @return array New form fields.
 */
function wpa_insert_alt_verification( $form_fields, $post ) {
	$mime = get_post_mime_type( $post->ID );
	if ( 'image/jpeg' == $mime || 'image/png' == $mime || 'image/gif' == $mime ) {
		$no_alt                = get_post_meta( $post->ID, '_no_alt', true );
		$alt                   = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
		$checked               = checked( $no_alt, 1, false );
		$form_fields['no_alt'] = array(
			'label' => __( 'Decorative', 'wp-accessibility' ),
			'input' => 'html',
			'value' => 1,
			'html'  => "<input name='attachments[$post->ID][no_alt]' id='attachments-$post->ID-no_alt' value='1' type='checkbox' aria-describedby='wpa_help' $checked /> <em class='help' id='wpa_help'>" . __( 'All images must either have an alt attribute or be declared as decorative.', 'wp-accessibility' ) . '</em>',
		);
	}
	return $form_fields;
}

add_filter( 'attachment_fields_to_save', 'wpa_save_alt_verification', 10, 2 );
/**
 * Save custom alt fields when attachment updated.
 *
 * @param array $post $post data.
 * @param array $attachment Attachment data.
 *
 * @return $post
 */
function wpa_save_alt_verification( $post, $attachment ) {
	if ( isset( $attachment['no_alt'] ) ) {
		update_post_meta( $post['ID'], '_no_alt', 1 );
	} else {
		delete_post_meta( $post['ID'], '_no_alt' );
	}

	return $post;
}

add_filter( 'image_send_to_editor', 'wpa_alt_attribute', 10, 8 );
/**
 * Filter output when image is submitted to the editor. Check for alt attributes, and modify output.
 *
 * @param string $html Image HTML.
 * @param int    $id Post ID.
 * @param string $caption Caption text.
 * @param string $title Image title.
 * @param string $align Image alignment.
 * @param string $url Image URL.
 * @param array  $size Image size.
 * @param string $alt Image alt attribute.
 *
 * @return string Image output.
 */
function wpa_alt_attribute( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	// Get data for the image attachment.
	$noalt = get_post_meta( $id, '_no_alt', true );
	// Get the original title to compare to alt.
	$title   = get_the_title( $id );
	$warning = false;
	if ( 1 == $noalt ) {
		$html = str_replace( 'alt="' . $alt . '"', 'alt=""', $html );
	}
	if ( ( '' == $alt || $alt == $title ) && 1 != $noalt ) {
		if ( $alt == $title ) {
			$warning = __( 'The alt text for this image is the same as the title. In most cases, that means that the alt attribute has been automatically provided from the image file name.', 'wp-accessibility' );
			$image   = 'alt-same.png';
		} else {
			$warning = __( 'This image requires alt text, but the alt text is currently blank. Either add alt text or mark the image as decorative.', 'wp-accessibility' );
			$image   = 'alt-missing.png';
		}
	}
	if ( $warning ) {
		return $html . "<img class='wpa-image-missing-alt size-" . esc_attr( $size ) . ' ' . esc_attr( $align ) . "' src='" . plugins_url( "imgs/$image", __FILE__ ) . "' alt='" . esc_attr( $warning ) . "' />";
	}
	return $html;
}

add_action( 'init', 'wpa_add_editor_styles' );
/**
 * Enqueue custom editor styles for WP Accessibility. Used in display of img replacements.
 */
function wpa_add_editor_styles() {
	add_editor_style( plugins_url( 'css/editor-style.css', __FILE__ ) );
}

add_action( 'widgets_init', 'wpa_register_toolbar_widget' );
/**
 * Register toolbar widget.
 */
function wpa_register_toolbar_widget() {
	register_widget( 'Wp_Accessibility_Toolbar' );
}
