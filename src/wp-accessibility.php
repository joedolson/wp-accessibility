<?php
/**
 * WP Accessibility
 *
 * @package     WP Accessibility
 * @author      Joe Dolson
 * @copyright   2012-2020 Joe Dolson
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
 * Version: 1.7.3
 */

/*
	Copyright 2012-2020  Joe Dolson (email : joe@joedolson.com)

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

register_activation_hook( __FILE__, 'wpa_install' );

add_action( 'plugins_loaded', 'wpa_load_textdomain' );
/**
 * Load internationalization.
 */
function wpa_load_textdomain() {
	load_plugin_textdomain( 'wp-accessibility', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

add_action( 'admin_menu', 'wpa_admin_menu' );
/**
 * Set up admin menu.
 */
function wpa_admin_menu() {
	add_action( 'admin_print_footer_scripts', 'wpa_write_js' );
	add_options_page( 'WP Accessibility', 'WP Accessibility', 'manage_options', __FILE__, 'wpa_admin_settings' );
}

/**
 * Install on activation.
 */
function wpa_install() {
	$wpa_version = '1.7.3';
	if ( 'true' !== get_option( 'wpa_installed' ) ) {
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
	if ( plugin_basename( dirname( __FILE__ ) . '/wp-accessibility.php' ) === $file ) {
		$admin_url = admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' );
		$links[]   = "<a href='$admin_url'>" . __( 'Accessibility Settings', 'wp-accessibility' ) . '</a>';
	}

	return $links;
}

add_action( 'wp_enqueue_scripts', 'wpacc_enqueue_scripts' );
/**
 * Enqueue accessibility scripts dependent on options.
 */
function wpacc_enqueue_scripts() {
	wp_enqueue_script( 'jquery' );
	if ( 'on' === get_option( 'wpa_insert_roles' ) ) {
		wp_enqueue_script( 'wpa-complementary', plugins_url( 'js/roles.jquery.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		if ( get_option( 'wpa_complementary_container' ) ) {
			$wpa_comp = get_option( 'wpa_complementary_container' );
		} else {
			$wpa_comp = false;
		}
		wp_localize_script( 'wpa-complementary', 'wpaComplementary', $wpa_comp );
	}
	if ( 'on' === get_option( 'wpa_labels' ) ) {
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
	if ( 'link' === get_option( 'wpa_longdesc' ) ) {
		wp_enqueue_script( 'longdesc.link', plugins_url( 'js/longdesc.link.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	}
	if ( 'jquery' === get_option( 'wpa_longdesc' ) ) {
		wp_enqueue_script( 'longdesc.button', plugins_url( 'js/longdesc.button.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	}
	wp_enqueue_script( 'current.menu', plugins_url( 'js/current-menu-item.js', __FILE__ ), array( 'jquery' ), '1.0', true );
}

add_action( 'wp_enqueue_scripts', 'wpa_stylesheet' );
/**
 * Enqueue stylesheets for WP Accessibility.
 */
function wpa_stylesheet() {
	// Respects SSL, Style.css is relative to the current file.
	wp_register_style( 'wpa-style', plugins_url( 'css/wpa-style.css', __FILE__ ) );
	if ( 'link' === get_option( 'wpa_longdesc' ) || 'jquery' === get_option( 'wpa_longdesc' ) || 'on' === get_option( 'asl_enable' ) ) {
		wp_enqueue_style( 'wpa-style' );
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

	if ( 'on' === get_option( 'wpa_row_actions' ) ) {
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
	if ( get_option( 'asl_enable' ) === 'on' ) {
		$focus = wp_kses( get_option( 'asl_styles_focus' ), array(), array() );
		// these styles are derived from the WordPress skip link defaults.
		$top = '7px';
		if ( is_admin_bar_showing() ) {
			$top = '37px';
		}
		$default_focus = 'background-color: #f1f1f1; box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6); clip: auto; color: #0073aa; display: block; font-weight: 600; height: auto; line-height: normal; padding: 15px 23px 14px; position: absolute; left: 6px; top: ' . $top . '; text-decoration: none; text-transform: none; width: auto; z-index: 100000;';
		if ( ! $focus ) {
			$focus = $default_focus;
		} else {
			$focus = $default_focus . $focus;
		}
		// Passive default styles derived from WordPress default focus styles.
		$default_passive = 'background-color: #fff; box-shadow:  0 0 2px 2px rgba(0, 0, 0, 0.2); clip: auto; color: #333; display: block; font-weight: 600; height: auto; line-height: normal; padding: 15px 23px 14px; position: absolute; left: 6px; top: ' . $top . '; text-decoration: none; text-transform: none; width: auto; z-index: 100000;';

		$passive = wp_kses( get_option( 'asl_styles_passive' ), array(), array() );
		$vis     = '';
		$invis   = '';

		$visibility = ( 'on' === get_option( 'asl_visible' ) ) ? 'wpa-visible' : 'wpa-hide';
		$is_rtl     = ( is_rtl() ) ? '-rtl' : '-ltr';
		$class      = '.' . $visibility . $is_rtl;
		// If links are visible, "hover" is a focus style, otherwise, it's a passive style.
		if ( 'on' === get_option( 'asl_visible' ) ) {
			$vis     = $class . '#skiplinks a:hover,';
			$passive = $default_passive . $passive;
		} else {
			$invis   = $class . '#skiplinks a:hover,';
			$passive = '';
		}
		$styles .= "
		$class#skiplinks a, $invis $class#skiplinks a:visited { $passive }
		$class#skiplinks a:active, $vis $class#skiplinks a:focus { $focus  }
		";
	}
	if ( 'on' === get_option( 'wpa_focus' ) ) {
		$color   = ( false !== (bool) get_option( 'wpa_focus_color' ) ) ? ' #' . get_option( 'wpa_focus_color' ) : '';
		$styles .= "
		:focus { outline: 1px solid$color!important; }
		";
	}
	if ( '' !== $styles ) {
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
	$visibility   = ( 'on' === get_option( 'asl_visible' ) ) ? 'wpa-visible' : 'wpa-hide';
	if ( 'on' === get_option( 'asl_enable' ) ) {
		$html = '';
		// set up skiplinks.
		$extra = (string) get_option( 'asl_extra_target' );
		$extra = ( wpa_is_url( $extra ) ) ? esc_url( $extra ) : str_replace( '#', '', esc_attr( $extra ) );
		if ( '' !== $extra && ! wpa_is_url( $extra ) ) {
			$extra = "#$extra";
		}
		$extra_text = (string) stripslashes( get_option( 'asl_extra_text' ) );
		$content    = str_replace( '#', '', esc_attr( get_option( 'asl_content' ) ) );
		$nav        = str_replace( '#', '', esc_attr( get_option( 'asl_navigation' ) ) );
		$sitemap    = esc_url( get_option( 'asl_sitemap' ) );
		$html      .= ( '' !== $content ) ? "<a href=\"#$content\">" . __( 'Skip to content', 'wp-accessibility' ) . '</a> ' : '';
		$html      .= ( '' !== $nav ) ? "<a href=\"#$nav\">" . __( 'Skip to navigation', 'wp-accessibility' ) . '</a> ' : '';
		$html      .= ( '' !== $sitemap ) ? "<a href=\"$sitemap\">" . __( 'Site map', 'wp-accessibility' ) . '</a> ' : '';
		$html      .= ( '' !== $extra && '' !== $extra_text ) ? "<a href=\"$extra\">$extra_text</a> " : '';
		$is_rtl     = ( is_rtl() ) ? '-rtl' : '-ltr';
		$skiplinks  = __( 'Skip links', 'wp-accessibility' );
		$output     = ( '' !== $html ) ? "<div class=\"$visibility$is_rtl\" id=\"skiplinks\" role=\"navigation\" aria-label=\"$skiplinks\">$html</div>" : '';
		// Attach skiplinks HTML; set tab index on #content area to -1.
		$focusable    = ( '' !== $content ) ? "$('#$content').attr('tabindex','-1');" : '';
		$focusable   .= ( '' !== $nav ) ? "$('#$nav').attr('tabindex','-1');" : '';
		$skiplinks_js = ( $output ) ? "$('body').prepend('$output'); $focusable" : '';
	}
	// Attach language to html element.
	if ( 'on' === get_option( 'wpa_lang' ) ) {
		$lang    = get_bloginfo( 'language' );
		$dir     = ( is_rtl() ) ? 'rtl' : 'ltr';
		$lang_js = "$('html').attr( 'lang','$lang' ); $('html').attr( 'dir','$dir' )";
	}
	// Force links to open in the same window.
	$underline_target = apply_filters( 'wpa_underline_target', 'a' );
	$targets          = ( 'on' === get_option( 'wpa_target' ) ) ? "$('a').removeAttr('target');" : '';
	$tabindex         = ( 'on' === get_option( 'wpa_tabindex' ) ) ? "$('input,a,select,textarea,button').removeAttr('tabindex');" : '';
	$underlines       = ( 'on' === get_option( 'wpa_underline' ) ) ? "$('$underline_target').css( 'text-decoration','underline' );$('$underline_target').on( 'focusin mouseenter', function() { $(this).css( 'text-decoration','none' ); });$('$underline_target').on( 'focusout mouseleave', function() { $(this).css( 'text-decoration','underline' ); } );" : '';

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

if ( 'on' === get_option( 'wpa_image_titles' ) ) {
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
	preg_match_all( '|[\s]title="[^"]*"|U', $content, $results );
	foreach ( $results[0] as $img ) {
		$content = str_replace( $img, '', $content );
	}

	return $content;
}

if ( 'on' === get_option( 'wpa_more' ) ) {
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
		$has_donated  = ( 'on' === $_POST['has_donated'] ) ? 'Donor' : 'No donation';
		$has_read_faq = ( 'on' === $_POST['has_read_faq'] ) ? 'Read FAQ' : false;
		$subject      = "WP Accessibility support request. $has_donated";
		$message      = $request . "\n\n" . $data;
		// Get the site domain and get rid of www. from pluggable.php.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}
		$from_email = 'wordpress@' . $sitename;
		$from       = "From: $current_user->display_name <$from_email>\r\nReply-to: $current_user->display_name <$current_user->user_email>\r\n";

		if ( ! $has_read_faq ) {
			echo "<div class='message error'><p>" . __( 'Please read the FAQ and other Help documents before making a support request.', 'wp-accessibility' ) . '</p></div>';
		} elseif ( ! $request ) {
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

/**
 * Tests whether the current theme is labeled accessibility-ready
 *
 * @return boolean True if this theme has the tag 'accessibility-ready'.
 */
function wpa_accessible_theme() {
	$theme = wp_get_theme();
	$tags  = $theme->get( 'Tags' );
	if ( is_array( $tags ) && in_array( 'accessibility-ready', $tags, true ) ) {
		return true;
	}
	return false;
}
