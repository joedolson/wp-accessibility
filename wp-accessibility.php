<?php
/*
Plugin Name: WP Accessibility
Plugin URI: http://www.joedolson.com/wp-accessibility/
Description: Helps improve accessibility in your WordPress site, like removing title attributes.
Version: 1.5.6
Author: Joe Dolson
Text Domain: wp-accessibility
Domain Path: /lang
Author URI: http://www.joedolson.com/

    Copyright 2012-2016 Joe Dolson (joe@joedolson.com)

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
register_activation_hook( __FILE__, 'wpa_install' );

// Enable internationalisation
add_action( 'plugins_loaded', 'wpa_load_textdomain' );
function wpa_load_textdomain() {
	load_plugin_textdomain( 'wp-accessibility', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

// ADMIN MENU
add_action( 'admin_menu', 'add_wpa_admin_menu' );
function add_wpa_admin_menu() {
	add_action( 'admin_print_footer_scripts', 'wpa_write_js' );
	add_options_page( 'WP Accessibility', 'WP Accessibility', 'manage_options', __FILE__, 'wpa_admin_menu' );
}

// ACTIVATION
function wpa_install() {
	$wpa_version = '1.5.6';
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

function wpa_check_version() {
	// upgrade for version 1.3.0
	if ( version_compare( get_option( 'wpa_version' ), '1.3.0', '<' ) ) {
		add_option( 'wpa_longdesc', 'jquery' );
	}
}

function wpa_plugin_action( $links, $file ) {
	if ( $file == plugin_basename( dirname( __FILE__ ) . '/wp-accessibility.php' ) ) {
		$admin_url = admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' );
		$links[]   = "<a href='$admin_url'>" . __( 'Accessibility Settings', 'wp-accessibility', 'wp-accessibility' ) . "</a>";
	}

	return $links;
}

//Add Plugin Actions to WordPress
add_filter( 'plugin_action_links', 'wpa_plugin_action', 10, 2 );
add_action( 'wp_enqueue_scripts', 'wpa_register_scripts' );
add_action( 'admin_menu', 'wpa_javascript' );

/**
 * Enqueue color picker for contrast testing
 **/
function wpa_javascript() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'wp-accessibility/wp-accessibility.php' ) {
		wp_enqueue_script( 'farbtastic' );
	}
}

function wpa_admin_stylesheet() {
	if ( get_option( 'wpa_admin_css' ) == 'on' ) {
		if ( file_exists( get_stylesheet_directory() . '/wp-admin.css' ) ) {
			$file = get_stylesheet_directory_uri() . '/wp-admin.css';
		} else {
			$file = plugins_url( 'css/wp-admin.css', __FILE__ );
		}
		wp_register_style( 'wp-a11y-css', $file );
		wp_enqueue_style( 'wp-a11y-css' );
	}
	if ( get_option( 'wpa_row_actions' ) == 'on' ) {
		if ( file_exists( get_stylesheet_directory() . '/wp-admin-row-actions.css' ) ) {
			$file = get_stylesheet_directory_uri() . '/wp-admin-row-actions.css';
		} else {
			$file = plugins_url( 'css/wp-admin-row-actions.css', __FILE__ );
		}
		wp_register_style( 'wp-row-actions', $file );
		wp_enqueue_style( 'wp-row-actions' );
	}
}

function wpa_admin_js() {
} // just a placeholder

add_action( 'admin_head', 'wpa_admin_stylesheet' );
add_action( 'admin_head', 'wpa_admin_js' );

function wpa_write_js() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'wp-accessibility/wp-accessibility.php' ) {
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

function wpa_register_scripts() {
	// register jQuery scripts;
	wp_register_script( 'skiplinks.webkit', plugins_url( 'wp-accessibility/js/skiplinks.webkit.js' ) );
	wp_register_script( 'ui-a11y.js', plugins_url( 'wp-accessibility/toolbar/js/a11y.js' ), array( 'jquery' ), '1.0', true );
	wp_register_script( 'scrollTo', plugins_url( 'wp-accessibility/toolbar/js/jquery.scrollto.min.js' ), array( 'jquery' ), '1.4.5', true );
}

add_action( 'wp_footer', 'wpa_jquery_asl', 100 );
add_action( 'wp_enqueue_scripts', 'wpacc_enqueue_scripts' );
add_action( 'wp_head', 'wpa_css' );
add_action( 'wp_enqueue_scripts', 'wpa_core_scripts' );
add_action( 'wp_enqueue_scripts', 'wpa_stylesheet' );

function wpa_core_scripts() {
	wp_enqueue_script( 'jquery' );
}

function wpacc_enqueue_scripts() {
	if ( get_option( 'asl_enable' ) == 'on' ) {
		wp_enqueue_script( 'skiplinks.webkit' );
	}
	if ( get_option( 'wpa_toolbar' ) == 'on' || get_option( 'wpa_widget_toolbar' ) == 'on' ) {
		wp_enqueue_script( 'scrollTo' );
		wp_enqueue_script( 'ui-a11y.js' );
		$plugin_path = plugins_url( 'wp-accessibility/toolbar/css/a11y-contrast.css' );
		if ( file_exists( get_stylesheet_directory() . '/a11y-contrast.css' ) ) {
			$plugin_path = get_stylesheet_directory_uri() . '/a11y-contrast.css';
		}
		wp_localize_script( 'ui-a11y.js', 'a11y_stylesheet_path', $plugin_path );
	}
	if ( get_option( 'wpa_insert_roles' ) == 'on' ) {
		wp_enqueue_script( 'wpa-complementary', plugins_url( 'js/roles.jquery.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		if ( get_option( 'wpa_complementary_container' ) ) {
			$wpa_comp = get_option( 'wpa_complementary_container' );
		} else {
			$wpa_comp = false;
		}
		wp_localize_script( 'wpa-complementary', 'wpaComplementary', $wpa_comp );
	}
	if ( get_option( 'wpa_labels' ) == 'on' ) {
		wp_enqueue_script( 'wpa-labels', plugins_url( 'js/wpa.labels.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		$labels = array( 
			's' => __( 'Search', 'wp-accessibility' ),
			'author' => __( 'Name', 'wp-accessibility' ),
			'email' => __( 'Email', 'wp-accessibility' ),
			'url' => __( 'Website', 'wp-accessibility' ),
			'comment' => __( 'Comment', 'wp-accessibility' )
		);
		wp_localize_script( 'wpa-labels', 'wpalabels', $labels );
	}
	if ( get_option( 'wpa_toolbar' ) == 'on' ) {
		add_action( 'wp_footer', 'wpa_toolbar_js' );
	}
	if ( get_option( 'wpa_longdesc' ) == 'link' ) {
		wp_enqueue_script( 'longdesc.link', plugins_url( 'js/longdesc.link.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	}
	if ( get_option( 'wpa_longdesc' ) == 'jquery' ) {
		wp_enqueue_script( 'longdesc.button', plugins_url( 'js/longdesc.button.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget("wp_accessibility_toolbar");' ) );
class wp_accessibility_toolbar extends WP_Widget {
	function __construct() {
		parent::__construct( false, $name = __( 'Accessibility Toolbar', 'wp-accessibility' ), array( 'customize_selective_refresh' => true ) );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', ( empty( $instance['title'] ) ? false : $instance['title'] ), $instance, $args );
		echo $before_widget;
		echo ( $title ) ? $before_title . $title . $after_title : '';		
		echo wpa_toolbar_html();
		echo $after_widget;
	}

	function form( $instance ) {
		$title = ( isset( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-accessibility' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php esc_attr_e( $title ); ?>"/>
		</p>
	<?php		
	}

	function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = strip_tags( $new_instance['title'] );

		return $instance;		
	}
}

add_shortcode( 'wpa_toolbar', 'wpa_toolbar_html' );
function wpa_toolbar_html() {
	$contrast         = __( 'Toggle High Contrast', 'wp-accessibility' );
	$grayscale        = __( 'Toggle Grayscale', 'wp-accessibility' );
	$fontsize         = __( 'Toggle Font size', 'wp-accessibility' );
	$enable_grayscale = ( get_option( 'wpa_toolbar_gs' ) == 'on' ) ? true : false;
	$enable_contrast  = ( get_option( 'wpa_toolbar_fs' ) == 'off' ) ? false : true;
	$enable_fontsize   = ( get_option( 'wpa_toolbar_ct' ) == 'off' ) ? false : true;
	$responsive       = ( get_option( 'wpa_toolbar_mobile' ) == 'on' ) ? 'a11y-responsive ' : '';
	$toolbar          = '
<!-- a11y toolbar widget -->
<div class="' . $responsive . 'a11y-toolbar-widget">
	<ul>';
	if ( $enable_contrast ) {
		$toolbar .= '<li><a href="#" class="a11y-toggle-contrast toggle-contrast" id="is_normal_contrast"><span class="offscreen">'.$contrast.'</span><span class="aticon aticon-adjust"></span></a></li>';
	}
	if ( $enable_grayscale ) {
		$toolbar .= '<li><a href="#" class="a11y-toggle-grayscale toggle-grayscale" id="is_normal_color"><span class="offscreen">'.$grayscale.'</span><span class="aticon aticon-tint"></span></a></li>';
	}
	if ( $enable_fontsize ) {
		$toolbar .= '<li><a href="#" class="a11y-toggle-fontsize toggle-fontsize" id="is_normal_fontsize"><span class="offscreen">'.$fontsize.'</span><span class="aticon aticon-font"></span></a></li>';
	}
	$toolbar .= '
	</ul>
</div>
<!-- // a11y toolbar widget -->';

	return $toolbar;
}

function wpa_toolbar_js() {
	$contrast         = __( 'Toggle High Contrast', 'wp-accessibility' );
	$grayscale        = __( 'Toggle Grayscale', 'wp-accessibility' );
	$fontsize         = __( 'Toggle Font size', 'wp-accessibility' );
	$enable_grayscale = ( get_option( 'wpa_toolbar_gs' ) == 'on' ) ? true : false;
	$enable_fontsize  = ( get_option( 'wpa_toolbar_fs' ) == 'off' ) ? false : true;
	$enable_contrast  = ( get_option( 'wpa_toolbar_ct' ) == 'off' ) ? false : true;
	$default          = ( get_option( 'wpa_toolbar_default' ) != '' ) ? get_option( 'wpa_toolbar_default' ) : 'body';
	$location         = apply_filters( 'wpa_move_toolbar', $default );
	$is_rtl           = ( is_rtl() ) ? ' rtl' : ' ltr';
	$is_right         = ( get_option( 'wpa_toolbar_right' ) == 'on' ) ? ' right' : '';
	$responsive       = ( get_option( 'wpa_toolbar_mobile' ) == 'on' ) ? 'a11y-responsive' : '';	
	echo
	"
<script type='text/javascript'>
//<![CDATA[
(function( $ ) { 'use strict';
	var insert_a11y_toolbar = '<!-- a11y toolbar -->';
	insert_a11y_toolbar += '<div class=\"$responsive a11y-toolbar$is_rtl$is_right\" role=\"menu\">';
	insert_a11y_toolbar += '<ul class=\"a11y-toolbar-list\">';";
	if ( get_option( 'wpa_toolbar' ) == 'on' && $enable_fontsize ) {	
		echo "insert_a11y_toolbar += '<li class=\"a11y-toolbar-list-item\" role=\"menuitem\"><button type=\"button\" class=\"a11y-toggle-contrast toggle-contrast\" id=\"is_normal_contrast\" aria-pressed=\"false\"><span class=\"offscreen\">$contrast</span><span class=\"aticon aticon-adjust\" aria-hidden=\"true\"></span></button></li>';";
	}
	if ( get_option( 'wpa_toolbar' ) == 'on' && $enable_grayscale ) {
		echo "insert_a11y_toolbar += '<li class=\"a11y-toolbar-list-item\" role=\"menuitem\"><button type=\"button\" class=\"a11y-toggle-grayscale toggle-grayscale\" id=\"is_normal_color\" aria-pressed=\"false\"><span class=\"offscreen\">$grayscale</span><span class=\"aticon aticon-tint\" aria-hidden=\"true\"></span></button></li>';";
	}
	if ( get_option( 'wpa_toolbar' ) == 'on' && $enable_contrast ) {
		echo "insert_a11y_toolbar += '<li class=\"a11y-toolbar-list-item\" role=\"menuitem\"><button type=\"button\" class=\"a11y-toggle-fontsize toggle-fontsize\" id=\"is_normal_fontsize\" aria-pressed=\"false\"><span class=\"offscreen\">$fontsize</span><span class=\"aticon aticon-font\" aria-hidden=\"true\"></span></button></li>';";
	}
	echo "
	insert_a11y_toolbar += '</ul>';
	insert_a11y_toolbar += '</div>';
	insert_a11y_toolbar += '<!-- // a11y toolbar -->';
	$( document ).find( '$location' ).prepend( insert_a11y_toolbar );
}(jQuery));
//]]>
</script>";
}

function wpa_css() {
	$styles = '';
	if ( get_option( 'asl_enable' ) == 'on' ) {
		$focus = get_option( 'asl_styles_focus' );
		// these styles are derived from the WordPress skip link defaults
		$default_focus = "background-color: #f1f1f1; box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6); clip: auto; color: #21759b; display: block; font-size: 14px; font-weight: bold; height: auto; line-height: normal; padding: 15px 23px 14px; position: absolute; left: 5px; top: 5px; text-decoration: none; text-transform: none; width: auto; z-index: 100000;";
		if ( ! $focus ) {
			$focus = $default_focus;
		} else {
			$focus = $default_focus . $focus;
		}
		$passive = get_option( 'asl_styles_passive' );
		$vis     = $invis = '';
		// if links are visible, "hover" is a focus style, otherwise, it's a passive style.
		if ( get_option( 'asl_visible' ) == 'on' ) {
			$vis = '#skiplinks a:hover,';
		} else {
			$invis = '#skiplinks a:hover,';
		}
		$visibility   = ( get_option( 'asl_visible' ) == 'on' ) ? 'wpa-visible' : 'wpa-hide';
		$is_rtl = ( is_rtl() ) ? '-rtl' : '-ltr';		
		$class = '.' . $visibility . $is_rtl;
		$styles .= "
		$class#skiplinks a, $invis $class#skiplinks a:visited { $passive }
		$class#skiplinks a:active, $vis $class#skiplinks a:focus { $focus  }
		";
	}
	if ( get_option( 'wpa_focus' ) == 'on' ) {
		$color = ( get_option( 'wpa_focus_color' ) != '' ) ? " #" . get_option( 'wpa_focus_color' ) : '';
		$styles .= "
		:focus { outline: 1px solid$color!important; }
		";
	}
	if ( $styles != '' ) {
		echo "
<style type='text/css'>
	$styles
</style>";
	}
}

function wpa_is_url( $url ) {
	return preg_match( '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url );
}

function wpa_jquery_asl() {
	$skiplinks_js = $targets = $lang_js = $tabindex = $longdesc = false;
	$visibility   = ( get_option( 'asl_visible' ) == 'on' ) ? 'wpa-visible' : 'wpa-hide';
	if ( get_option( 'asl_enable' ) == 'on' ) {
		$html = '';
		// set up skiplinks
		$extra = get_option( 'asl_extra_target' );
		$extra = ( wpa_is_url( $extra ) ) ? esc_url( $extra ) : str_replace( '#', '', esc_attr( $extra ) );
		if ( $extra != '' && ! wpa_is_url( $extra ) ) {
			$extra = "#$extra";
		}
		$extra_text = stripslashes( get_option( 'asl_extra_text' ) );
		$content    = str_replace( '#', '', esc_attr( get_option( 'asl_content' ) ) );
		$nav        = str_replace( '#', '', esc_attr( get_option( 'asl_navigation' ) ) );
		$sitemap    = esc_url( get_option( 'asl_sitemap' ) );
		$html .= ( $content != '' ) ? "<a href=\"#$content\">" . __( 'Skip to content', 'wp-accessibility' ) . "</a> " : '';
		$html .= ( $nav != '' ) ? "<a href=\"#$nav\">" . __( 'Skip to navigation', 'wp-accessibility' ) . "</a> " : '';
		$html .= ( $sitemap != '' ) ? "<a href=\"$sitemap\">" . __( 'Site map', 'wp-accessibility' ) . "</a> " : '';
		$html .= ( $extra != '' && $extra_text != '' ) ? "<a href=\"$extra\">$extra_text</a> " : '';
		$is_rtl = ( is_rtl() ) ? '-rtl' : '-ltr';
		$skiplinks = __( 'Skip links', 'wp-accessibility' );
		$output = ( $html != '' ) ? "<div class=\"$visibility$is_rtl\" id=\"skiplinks\" role=\"navigation\" aria-label=\"$skiplinks\">$html</div>" : '';
		// attach skiplinks HTML; set tabindex on #content area to -1
		$focusable = ( $content != '' ) ? "$('#$content').attr('tabindex','-1');" : '';
		$focusable .= ( $nav != '' ) ? "$('#$nav').attr('tabindex','-1');" : '';
		$skiplinks_js = ( $output ) ? "$('body').prepend('$output'); $focusable" : '';
	}
	// attach language to html element
	if ( get_option( 'wpa_lang' ) == 'on' ) {
		$lang    = get_bloginfo( 'language' );
		$dir     =  ( is_rtl() ) ? 'rtl' : 'ltr';
		$lang_js = "$('html').attr( 'lang','$lang' ); $('html').attr( 'dir','$dir' )";
	}
	// force links to open in the same window
	$underline_target = apply_filters( 'wpa_underline_target', 'a' );
	$targets    = ( get_option( 'wpa_target' ) == 'on' ) ? "$('a').removeAttr('target');" : '';
	$tabindex   = ( get_option( 'wpa_tabindex' ) == 'on' ) ? "$('input,a,select,textarea,button').removeAttr('tabindex');" : '';
	$underlines = ( get_option( 'wpa_underline' ) == 'on' ) ? "$('$underline_target').css( 'text-decoration','underline' );$('$underline_target').on( 'focusin mouseenter', function() { $(this).css( 'text-decoration','none' ); });$('$underline_target').on( 'focusout mouseleave', function() { $(this).css( 'text-decoration','underline' ); } );" : '';
	
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

// courtesy of Graham Armfield (modified)
// http://www.coolfields.co.uk/2013/02/wordpress-permanently-visible-log-out-link-plugin-version-0-1/
add_action( 'admin_bar_menu', 'wpa_logout_item', 11 );
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

function wpa_stylesheet() {
	// Respects SSL, Style.css is relative to the current file
	wp_register_style( 'wpa-style', plugins_url( 'css/wpa-style.css', __FILE__ ) );
	wp_register_style( 'ui-font.css', plugins_url( 'toolbar/fonts/css/a11y-toolbar.css', __FILE__ ) );
	$toolbar = apply_filters( 'wpa_toolbar_css', plugins_url( 'toolbar/css/a11y.css', __FILE__ ) );
	wp_register_style( 'ui-a11y.css', $toolbar, array( 'ui-font.css' ) );
	$fontsize_stylesheet = ( get_option( 'wpa_alternate_fontsize' ) == 'on' ) ? 'a11y-fontsize-alt' : 'a11y-fontsize';
	$fontsize = apply_filters( 'wpa_fontsize_css', plugins_url( 'toolbar/css/'. $fontsize_stylesheet . '.css', __FILE__ ) );
	wp_register_style( 'ui-fontsize.css', $fontsize );
	// only enable styles when required by options
	if ( get_option( 'wpa_toolbar_size' ) && get_option( 'wpa_toolbar' ) == 'on' ) {
		echo "
<style type='text/css'>
.a11y-toolbar ul li a {
	font-size: " . get_option( 'wpa_toolbar_size' ) . " !important;
}
</style>";
	}
	if ( get_option( 'wpa_longdesc' ) == 'link' || get_option( 'wpa_longdesc' ) == 'jquery' || get_option( 'asl_enable' ) == 'on' ) {
		wp_enqueue_style( 'wpa-style' );
	}
	if ( get_option( 'wpa_toolbar' ) == 'on' || get_option( 'wpa_widget_toolbar' ) == 'on' && ( $toolbar && $fontsize ) ) {
		wp_enqueue_style( 'ui-a11y.css' );
		wp_enqueue_style( 'ui-fontsize.css' );
	}
	if ( current_user_can( 'edit_files' ) && get_option( 'wpa_diagnostics' ) == 'on' ) {
		wp_register_style( 'diagnostic', plugins_url( 'css/diagnostic.css', __FILE__ ) );
		wp_register_style( 'diagnostic-head', plugins_url( 'css/diagnostic-head.css', __FILE__ ) );
		wp_enqueue_style( 'diagnostic' );
		wp_enqueue_style( 'diagnostic-head' );
	}
}

add_filter( 'mce_css', 'wp_diagnostic_css' );
function wp_diagnostic_css( $mce_css ) {
	if ( get_option( 'wpa_diagnostics' ) == 'on' ) {
		$mce_css .= ', ' . plugins_url( 'css/diagnostic.css', __FILE__ );
	}

	return $mce_css;
}

function wpa_luminosity( $r, $r2, $g, $g2, $b, $b2 ) {
	$RsRGB = $r / 255;
	$GsRGB = $g / 255;
	$BsRGB = $b / 255;
	$R     = ( $RsRGB <= 0.03928 ) ? $RsRGB / 12.92 : pow( ( $RsRGB + 0.055 ) / 1.055, 2.4 );
	$G     = ( $GsRGB <= 0.03928 ) ? $GsRGB / 12.92 : pow( ( $GsRGB + 0.055 ) / 1.055, 2.4 );
	$B     = ( $BsRGB <= 0.03928 ) ? $BsRGB / 12.92 : pow( ( $BsRGB + 0.055 ) / 1.055, 2.4 );

	$RsRGB2 = $r2 / 255;
	$GsRGB2 = $g2 / 255;
	$BsRGB2 = $b2 / 255;
	$R2     = ( $RsRGB2 <= 0.03928 ) ? $RsRGB2 / 12.92 : pow( ( $RsRGB2 + 0.055 ) / 1.055, 2.4 );
	$G2     = ( $GsRGB2 <= 0.03928 ) ? $GsRGB2 / 12.92 : pow( ( $GsRGB2 + 0.055 ) / 1.055, 2.4 );
	$B2     = ( $BsRGB2 <= 0.03928 ) ? $BsRGB2 / 12.92 : pow( ( $BsRGB2 + 0.055 ) / 1.055, 2.4 );

	if ( $r + $g + $b <= $r2 + $g2 + $b2 ) {
		$l2 = ( .2126 * $R + 0.7152 * $G + 0.0722 * $B );
		$l1 = ( .2126 * $R2 + 0.7152 * $G2 + 0.0722 * $B2 );
	} else {
		$l1 = ( .2126 * $R + 0.7152 * $G + 0.0722 * $B );
		$l2 = ( .2126 * $R2 + 0.7152 * $G2 + 0.0722 * $B2 );
	}
	$luminosity = round( ( $l1 + 0.05 ) / ( $l2 + 0.05 ), 2 );

	return $luminosity;
}

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

	$color = ( strlen( $r ) < 2 ? '0' : '' ) . $r;
	$color .= ( strlen( $g ) < 2 ? '0' : '' ) . $g;
	$color .= ( strlen( $b ) < 2 ? '0' : '' ) . $b;

	return '#' . $color;
}

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

function wpa_contrast() {
	if ( ! empty( $_POST ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpa-nonce' ) ) {
			die( "Security check failed" );
		}
		if ( isset( $_POST['color'] ) && $_POST['color'] != "" ) {
			$fore_color = $_POST['color'];
			if ( $fore_color[0] == "#" ) {
				$fore_color = str_replace( '#', '', $fore_color );
			}
			if ( strlen( $fore_color ) == 3 ) {
				$color6char = $fore_color[0] . $fore_color[0];
				$color6char .= $fore_color[1] . $fore_color[1];
				$color6char .= $fore_color[2] . $fore_color[2];
				$fore_color = $color6char;
			}
			if ( preg_match( '/^#?([0-9a-f]{1,2}){3}$/i', $fore_color ) ) {
				$echo_hex_fore = str_replace( '#', '', $fore_color );
			} else {
				$echo_hex_fore = 'FFFFFF';
			}
			if ( isset( $_POST['color2'] ) && $_POST['color2'] != "" ) {
				$back_color = $_POST['color2'];
				if ( $back_color[0] == "#" ) {
					$back_color = str_replace( '#', '', $back_color );
				}
				if ( strlen( $back_color ) == 3 ) {
					$color6char = $back_color[0] . $back_color[0];
					$color6char .= $back_color[1] . $back_color[1];
					$color6char .= $back_color[2] . $back_color[2];
					$back_color = $color6char;
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
				$colors = array( 'hex1'   => $echo_hex_fore,
				                 'hex2'   => $echo_hex_back,
				                 'red1'   => $rfore,
				                 'green1' => $gfore,
				                 'blue1'  => $bfore,
				                 'red2'   => $rback,
				                 'green2' => $gback,
				                 'blue2'  => $bback
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

function wpa_filter( $query ) {
	if ( ! is_admin() ) {
		if ( isset( $_GET['s'] ) && NULL == trim( $_GET['s'] ) && ( $query->is_main_query() ) ) {
			$query->query_vars['s'] = '&#32;';
			$query->set( 'is_search', 1 );
			add_action( 'template_include', 'wpa_search_error' );
		}
	}

	return $query;
}

function wpa_search_error( $template ) {
	$search = locate_template( 'search.php' );
	if ( $search ) {
		return $search;
	}

	return $template;
}

if ( get_option( 'wpa_image_titles' ) == 'on' ) {
	add_filter( 'the_content', 'wpa_image_titles', 100 );
	add_filter( 'post_thumbnail_html', 'wpa_image_titles', 100 );
	add_filter( 'wp_get_attachment_image', 'wpa_image_titles', 100 );
}

function wpa_image_titles( $content ) {
	$results = array();
	preg_match_all( '|title="[^"]*"|U', $content, $results );
	foreach ( $results[0] as $img ) {
		$content = str_replace( $img, '', $content );
	}

	return $content;
}

if ( get_option( 'wpa_more' ) == 'on' ) {
	add_filter( 'get_the_excerpt', 'wpa_custom_excerpt_more', 100 );
	add_filter( 'excerpt_more', 'wpa_excerpt_more', 100 );
	add_filter( 'the_content_more_link', 'wpa_content_more', 100 );
}

function wpa_continue_reading( $id ) {
	return '<a class="continue" href="' . get_permalink( $id ) . '">' . get_option( 'wpa_continue' ) . "<span> " . get_the_title( $id ) . "</span></a>";
}

function wpa_excerpt_more() {
	global $id;

	return '&hellip; ' . wpa_continue_reading( $id );
}

function wpa_content_more() {
	global $id;

	return wpa_continue_reading( $id );
}

function wpa_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		global $id;
		$output .= ' ' . wpa_continue_reading( $id ); // insert a blank space.
	}

	return $output;
}

add_action( "admin_head", 'wpa_admin_styles' );

function wpa_admin_styles() {
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'wp-accessibility/wp-accessibility.php' ) ) {
		wp_enqueue_style( 'farbtastic' );
		echo '<link type="text/css" rel="stylesheet" href="' . plugins_url( 'css/wpa-styles.css', __FILE__ ) . '" />';
	}
}

// Obsolete with 3.8: (nav menus, page lists, edit post links, edit comment links, category links)
if ( get_option( 'rta_from_nav_menu' ) == 'on' && version_compare( get_bloginfo( 'version' ), '3.8.0', '<' ) ) {
	add_filter( 'wp_nav_menu', 'wpa_remove_title_attributes' );
}
if ( get_option( 'rta_from_page_lists' ) == 'on' && version_compare( get_bloginfo( 'version' ), '3.8.0', '<' ) ) {
	add_filter( 'wp_list_pages', 'wpa_remove_title_attributes' );
}
if ( get_option( 'rta_from_category_lists' ) == 'on' ) {
	add_filter( 'wp_list_categories', 'wpa_remove_title_attributes' );
}
if ( get_option( 'rta_from_archive_links' ) == 'on' ) {
	add_filter( 'get_archives_link', 'wpa_remove_title_attributes' );
}
if ( get_option( 'rta_from_tag_clouds' ) == 'on' ) {
	add_filter( 'wp_tag_cloud', 'wpa_remove_title_attributes' );
}
if ( get_option( 'rta_from_category_links' && version_compare( get_bloginfo( 'version' ), '3.8.0', '<' ) ) == 'on' ) {
	add_filter( 'the_category', 'wpa_remove_title_attributes' );
}
if ( get_option( 'rta_from_post_edit_links' && version_compare( get_bloginfo( 'version' ), '3.8.0', '<' ) ) == 'on' ) {
	add_filter( 'edit_post_link', 'wpa_remove_title_attributes' );
}
if ( get_option( 'rta_from_edit_comment_links' && version_compare( get_bloginfo( 'version' ), '3.8.0', '<' ) ) == 'on' ) {
	add_filter( 'edit_comment_link', 'wpa_remove_title_attributes' );
}

function wpa_remove_title_attributes( $output ) {
	$output = preg_replace( '/\s*title\s*=\s*(["\']).*?\1/', '', $output );

	return $output;
}


/**
 * Reuse this function next time I deprecate a feature.
 
function wpa_deprecated_warning( $context ) {
	if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		switch ( $context ) {
			case 'recent_posts' :
				return __( 'The WP Accessibility recent posts widget is deprecated. The standard WordPress widget no longer uses title attributes, so this widget is no longer necessary. It will be removed without warning in a future version of WP Accessibility. This warning is only visible to administrators of your site.', 'wp-accessibility' );
			default:
				return;
		}
	}

	return;
}
*/

function wpa_get_support_form() {
	global $current_user, $wpa_version;
	$current_user = wp_get_current_user();
	$request = '';
	$version = $wpa_version;
	// send fields for all plugins
	$wp_version = get_bloginfo( 'version' );
	$home_url   = home_url();
	$wp_url     = site_url();
	$language   = get_bloginfo( 'language' );
	$charset    = get_bloginfo( 'charset' );
	// server
	$php_version = phpversion();

	// theme data
	$theme         = wp_get_theme();
	$theme_name    = $theme->Name;
	$theme_uri     = $theme->ThemeURI;
	$theme_parent  = $theme->Template;
	$theme_version = $theme->Version;

	// plugin data
	$plugins        = get_plugins();
	$plugins_string = '';
	foreach ( array_keys( $plugins ) as $key ) {
		if ( is_plugin_active( $key ) ) {
			$plugin         =& $plugins[ $key ];
			$plugin_name    = $plugin['Name'];
			$plugin_uri     = $plugin['PluginURI'];
			$plugin_version = $plugin['Version'];
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
			die( "Security check failed" );
		}
		$request      = ( ! empty( $_POST['support_request'] ) ) ? stripslashes( $_POST['support_request'] ) : false;
		$has_donated  = ( $_POST['has_donated'] == 'on' ) ? "Donor" : "No donation";
		$has_read_faq = ( $_POST['has_read_faq'] == 'on' ) ? "Read FAQ" : false;
		$subject      = "WP Accessibility support request. $has_donated";
		$message      = $request . "\n\n" . $data;
		// Get the site domain and get rid of www. from pluggable.php
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		$from_email = 'wordpress@' . $sitename;
		$from       = "From: \"$current_user->display_name\" <$from_email>\r\nReply-to: \"$current_user->display_name\" <$current_user->user_email>\r\n";

		if ( ! $has_read_faq ) {
			echo "<div class='message error'><p>" . __( 'Please read the FAQ and other Help documents before making a support request.', 'wp-accessibility' ) . "</p></div>";
		} else if ( ! $request ) {
			echo "<div class='message error'><p>" . __( 'Please describe your problem. I\'m not psychic.', 'wp-accessibility' ) . "</p></div>";
		} else {
			wp_mail( "plugins@joedolson.com", $subject, $message, $from );
			if ( $has_donated == 'Donor' ) {
				echo "<div class='message updated'><p>" . __( 'Thank you for supporting the continuing development of this plug-in! I\'ll get back to you as soon as I can.', 'wp-accessibility' ) . "</p></div>";
			} else {
				echo "<div class='message updated'><p>" . __( 'I cannot provide  support, but will treat your request as a bug report, and will incorporate any permanent solutions I discover into the plug-in.', 'wp-accessibility' ) . "</p></div>";
			}
		}
	}
	$admin_url = admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' );

	echo "
	<form method='post' action='$admin_url'>
		<div><input type='hidden' name='_wpnonce' value='" . wp_create_nonce( 'wpa-nonce' ) . "' /></div>
		<div>";
	echo "
		<p>
		<code>" . __( 'From:', 'wp-accessibility' ) . " \"$current_user->display_name\" &lt;$current_user->user_email&gt;</code>
		</p>
		<p>
		<input type='checkbox' name='has_read_faq' id='has_read_faq' value='on' /> <label for='has_read_faq'>" . sprintf( __( 'I have read <a href="%1$s">the FAQ for this plug-in</a> <span>(required)</span>', 'wp-accessibility' ), 'http://www.joedolson.com/wp-accessibility/faqs/' ) . "</label>
        </p>
        <p>
        <input type='checkbox' name='has_donated' id='has_donated' value='on' /> <label for='has_donated'>" . sprintf( __( 'I <a href="%1$s">made a donation to help support this plug-in</a>', 'wp-accessibility' ), 'http://www.joedolson.com/donate/' ) . "</label>
        </p>
        <p>
        <label for='support_request'>" . __( 'Support Request:', 'wp-accessibility' ) . "</label><br /><textarea name='support_request' required aria-required='true' id='support_request' cols='80' rows='10'>" . stripslashes( $request ) . "</textarea>
		</p>
		<p>
		<input type='submit' value='" . __( 'Send Support Request', 'wp-accessibility' ) . "' name='wpa_support' class='button-primary' />
		</p>
		<p>" .
	     __( 'The following additional information will be sent with your support request:', 'wp-accessibility' )
	     . "</p>
		<div class='wpa_support'>
		" . wpautop( $data ) . "
		</div>
		</div>
	</form>";
}


add_filter( 'wp_get_attachment_image_attributes', 'wpa_featured_longdesc', 10, 3 );
function wpa_featured_longdesc( $attr, $attachment, $size ) {
	if ( get_option( 'wpa_longdesc_featured' ) == 'on' ) {
		$attachment_id = $attachment->ID;
		$args = array( 'longdesc' => $attachment_id );
		/* The referrer is the post that the image is inserted into. */
		if ( isset( $_REQUEST['post_id'] ) || get_the_ID() ) {
			$id = ( isset( $_REQUEST['post_id'] ) ) ? $_REQUEST['post_id'] : get_the_ID();
			$args['referrer'] = intval( $id );
		}

		$target = add_query_arg( $args, home_url() );
		$id     = longdesc_return_anchor( $attachment_id );

		$attr['longdesc'] = $target;
		$attr['id']      = $id;
	}
	
	return $attr;
}


/* longdesc support, based on work by Michael Fields (http://wordpress.org/plugins/long-description-for-image-attachments/) */

define( 'WPA_TEMPLATES', trailingslashit( dirname( __FILE__ ) ) . 'templates/' );

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
 * @return    void
 *
 * @since     2010-09-26
 * @alter     2011-03-27
 */
function longdesc_template() {

	/* Return early if there is no reason to proceed. */
	if ( ! isset( $_GET['longdesc'] ) ) {
		return;
	}

	global $post;

	/* Get the image attachment's data. */
	$id   = absint( $_GET['longdesc'] );
	$post = get_post( $id );
	if ( is_object( $post ) ) {
		setup_postdata( $post );
	}

	/* Attachment must be an image. */
	if ( false === strpos( get_post_mime_type(), 'image' ) ) {
		header( 'HTTP/1.0 404 Not Found' );
		exit;
	}

	/* The whole point here is to NOT show an image :) */
	remove_filter( 'the_content', 'prepend_attachment' );

	/* Check to see if there is a template in the theme. */
	$template = locate_template( array( 'longdesc-template.php' ) );
	if ( ! empty( $template ) ) {
		require_once( $template );
		exit;
	} /* Use plugin's template file. */
	else {
		require_once( WPA_TEMPLATES . 'longdesc-template.php' );
		exit;
	}

	/* You've gone too far! */
	header( 'HTTP/1.0 404 Not Found' );
	exit;
}

add_action( 'template_redirect', 'longdesc_template' );

/**
 * Anchor.
 *
 * Create anchor id for linking from a Long Description to referring post.
 * Also creates an anchor to return from Long Description page.
 *
 * @param     int       ID of the post which contains an image with a longdesc attribute.
 *
 * @return    string
 *
 * @since     2010-09-26
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
 * @return    string
 *
 * @since     2010-09-20
 * @alter     2011-04-06
 */
function longdesc_add_attr( $html, $id, $caption, $title, $align, $url, $size, $alt ) {

	/* Get data for the image attachment. */
	$image = get_post( $id );
	global $post_ID;
	if ( isset( $image->ID ) && ! empty( $image->ID ) ) {
		$args = array( 'longdesc' => $image->ID );
		/* The referrer is the post that the image is inserted into. */
		if ( isset( $_REQUEST['post_id'] ) || get_the_ID() ) {
			$id = ( isset( $_REQUEST['post_id'] ) ) ? $_REQUEST['post_id'] : get_the_ID();
			$args['referrer'] = intval( $id );
		}
		if ( ! empty( $image->post_content ) ) {
			$search  = '<img ';
			$replace = '<img tabindex="-1" id="' . esc_attr( longdesc_return_anchor( $image->ID ) ) . '" longdesc="' . esc_url( add_query_arg( $args, home_url() ) ) . '"';
			$html    = str_replace( $search, $replace, $html );
		}
	}

	return $html;
}

add_filter( 'image_send_to_editor', 'longdesc_add_attr', 10, 8 );

/* Tests whether the current theme is labeled accessibility-ready */
function wpa_accessible_theme() {
	$theme = wp_get_theme();
	$tags = $theme->get( 'Tags' );
	if ( is_array( $tags ) && in_array( 'accessibility-ready', $tags ) ) {
		return true;
	}
	return false;
}

add_action( 'init', 'wpa_dismiss_notice' );
function wpa_dismiss_notice() {
	if ( isset( $_GET['dismiss'] ) && $_GET['dismiss'] == 'update' ) {
		update_option( 'wpa_update_notice', 1 );
	}
}

add_action( 'admin_notices', 'wpa_update_notice' );
function wpa_update_notice() {
	if ( current_user_can( 'activate_plugins' ) && get_option( 'wpa_update_notice' ) == 0 || ! get_option( 'wpa_update_notice' ) ) {
		$dismiss = admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php&dismiss=update' );
		$access_monitor = "https://wordpress.org/plugins/access-monitor/";
		echo "<div class='updated fade'><p>" . sprintf( __( 'Have you seen my new accessibility plug-in? <a href="%1$s">Check out Access Monitor</a>! &nbsp; &nbsp; <a href="%2$s">Dismiss Notice<span class="dashicons dashicons-no" aria-hidden="true"></span></a>', 'wp-accessibility' ), $access_monitor, $dismiss ) . "</p></div>";
	}
}

add_filter( 'manage_media_columns', 'wpa_media_columns' );
add_action( 'manage_media_custom_column', 'wpa_media_value', 10, 2 );

function wpa_media_columns( $columns ) {
	$columns['wpa_data'] = __( 'Accessibility', 'wp-accessibility' );
	return $columns;
}

function wpa_media_value( $column, $id ) {
	if ( $column == 'wpa_data' ) {
		$mime = get_post_mime_type( $id );
		switch ( $mime ) {     
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
				$no_alt = get_post_meta( $id, '_no_alt', true );
				if ( !$alt && !$no_alt ) {
					echo '<span class="missing"><span class="dashicons dashicons-no" aria-hidden="true"></span> <a href="'.get_edit_post_link( $id ).'#attachment_alt">'.__( 'Add <code>alt</code> text', 'wp-accessibility' ).'</a></span>';
				} else {
					if ( $no_alt == 1 ) {
						echo '<span class="ok"><span class="dashicons dashicons-yes" aria-hidden="true"></span> '.__( 'Decorative', 'wp-accessibility' ).'</span>';						
					} else {
						echo '<span class="ok"><span class="dashicons dashicons-yes" aria-hidden="true"></span> '.__( 'Has <code>alt</code>', 'wp-accessibility' ).'</span>';
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
function wpa_insert_alt_verification( $form_fields, $post ) {
	$mime = get_post_mime_type( $post->ID );
	if ( $mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif' ) {
		$no_alt = get_post_meta( $post->ID, '_no_alt', true );
		$alt = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
		$checked = checked( $no_alt, 1, false );
		$form_fields['no_alt'] = array( 
			'label' => __( 'Decorative', 'wp-accessibility' ),
			'input' => 'html',
			'value' => 1,
			'html'  => "<input name='attachments[$post->ID][no_alt]' id='attachments-$post->ID-no_alt' value='1' type='checkbox' aria-describedby='wpa_help' $checked /> <em class='help' id='wpa_help'>" . __( 'All images must either have an alt attribute or be declared as decorative.', 'wp-accessibility' ) . "</em>"
		);
	}
	return $form_fields;
}

add_filter( 'attachment_fields_to_save', 'wpa_save_alt_verification', 10, 2 );
function wpa_save_alt_verification( $post, $attachment ) {
	if ( isset( $attachment['no_alt'] ) ) {
		update_post_meta( $post['ID'], '_no_alt', 1 );
	} else {
		delete_post_meta( $post['ID'], '_no_alt' );
	}
	return $post;
}

add_filter( 'image_send_to_editor', 'wpa_alt_attribute', 10, 8 );
function wpa_alt_attribute( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	/* Get data for the image attachment. */
	$noalt = get_post_meta( $id, '_no_alt', true );
	/* Get the original title to compare to alt */
	$title = get_the_title( $id );
	$warning = false;
	if ( $noalt == 1 ) {
		$html = str_replace( 'alt="'.$alt.'"', 'alt=""', $html );
	}
	if ( ( $alt == '' || $alt == $title ) && $noalt != 1 ) {
		if ( $alt == $title ) {
			$warning = __( 'The alt text for this image is the same as the title. In most cases, that means that the alt attribute has been automatically provided from the image file name.', 'wp-accessibility' );
			$image = 'alt-same.png';
		} else {
			$warning = __( 'This image requires alt text, but the alt text is currently blank. Either add alt text or mark the image as decorative.', 'wp-accessibility' );
			$image = 'alt-missing.png';
		}
	}
	if ( $warning ) {
		return $html . "<img class='wpa-image-missing-alt size-" . esc_attr( $size ) . ' ' . esc_attr( $align ) . "' src='" . plugins_url( "imgs/$image", __FILE__ ) . "' alt='" . esc_attr( $warning ) . "' />";
	}
	return $html;
}

add_action( 'init', 'wpa_add_editor_styles' );
function wpa_add_editor_styles() {
    add_editor_style( plugins_url( 'css/editor-style.css', __FILE__ ) );
}
