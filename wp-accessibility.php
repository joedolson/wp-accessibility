<?php
/*
Plugin Name: WP Accessibility
Plugin URI: http://www.joedolson.com/wp-accessibility/
Description: Provides options to improve accessibility in your WordPress site, including removing title attributes.
Version: 1.4.5
Author: Joe Dolson
Text Domain: wp-accessibility
Author URI: http://www.joedolson.com/

    Copyright 2012-2015 Joe Dolson (joe@joedolson.com)

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

register_activation_hook( __FILE__, 'wpa_install' );
load_plugin_textdomain( 'wp-accessibility', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

// ADMIN MENU
add_action( 'admin_menu', 'add_wpa_admin_menu' );
function add_wpa_admin_menu() {
	add_action( 'admin_print_footer_scripts', 'wpa_write_js' );
	add_options_page( 'WP Accessibility', 'WP Accessibility', 'manage_options', __FILE__, 'wpa_admin_menu' );
}

// ACTIVATION
function wpa_install() {
	$wpa_version = '1.4.5';
	if ( get_option( 'wpa_installed' ) != 'true' ) {
		add_option( 'rta_from_nav_menu', 'on' );
		add_option( 'rta_from_page_lists', 'on' );
		add_option( 'rta_from_category_lists', 'on' );
		add_option( 'rta_from_archive_links', 'on' );
		add_option( 'rta_from_tag_clouds', 'on' );
		add_option( 'rta_from_category_links', 'on' );
		add_option( 'rta_from_post_edit_links', 'on' );
		add_option( 'rta_from_edit_comment_links', 'on' );
		add_option( 'asl_styles_focus', 'left: 1em; top: 1em; background: #f6f3fa; color: #00c; padding: 5px; border: 1px solid #357; box-shadow: 2px 2px 2px #777; border-radius: 5px; font-size: 1.4em' );
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
add_action( 'wp_enqueue_scripts', 'wpa_enqueue_scripts' );
add_action( 'wp_head', 'wpa_css' );
add_action( 'wp_enqueue_scripts', 'wpa_core_scripts' );
add_action( 'wp_enqueue_scripts', 'wpa_stylesheet' );

function wpa_core_scripts() {
	wp_enqueue_script( 'jquery' );
}

function wpa_enqueue_scripts() {
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
		parent::__construct( false, $name = __( 'Accessibility Toolbar', 'wp-accessibility' ) );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', ( empty( $instance['title'] ) ? false : $instance['title'] ), $instance, $args );
		echo $before_widget;
		echo ( $title ) ? $before_title . $title . $after_title : '';		
		echo wpa_toolbar_html();
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
	$toolbar          = '
<!-- a11y toolbar widget -->
<div class="a11y-toolbar-widget">
	<ul>
		<li><a href="#" class="a11y-toggle-contrast toggle-contrast" id="is_normal_contrast"><span class="offscreen">'.$contrast.'</span><span class="aticon aticon-adjust"></span></a></li>';
	if ( $enable_grayscale ) {
		$toolbar .= '<li><a href="#" class="a11y-toggle-grayscale toggle-grayscale" id="is_normal_color"><span class="offscreen">'.$grayscale.'</span><span class="aticon aticon-tint"></span></a></li>';
	}
	$toolbar .= '<li><a href="#" class="a11y-toggle-fontsize toggle-fontsize" id="is_normal_fontsize"><span class="offscreen">'.$fontsize.'</span><span class="aticon aticon-font"></span></a></li>
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
	$location         = apply_filters( 'wpa_move_toolbar', 'body' );
	$is_rtl           = ( is_rtl() ) ? ' rtl' : ' ltr';
	echo
	"
<script type='text/javascript'>
//<![CDATA[
(function( $ ) { 'use strict';
	var insert_a11y_toolbar = '<!-- a11y toolbar -->';
	insert_a11y_toolbar += '<div class=\"a11y-toolbar$is_rtl\">';
	insert_a11y_toolbar += '<ul>';
	insert_a11y_toolbar += '<li><a href=\"#\" role=\"button\" class=\"a11y-toggle-contrast toggle-contrast\" id=\"is_normal_contrast\" title=\"$contrast\"><span class=\"offscreen\">$contrast</span><span class=\"aticon aticon-adjust\" aria-hidden=\"true\"></span></a></li>';";
	if ( get_option( 'wpa_toolbar' ) == 'on' && $enable_grayscale ) {
		echo "insert_a11y_toolbar += '<li><a href=\"#\" role=\"button\" class=\"a11y-toggle-grayscale toggle-grayscale\" id=\"is_normal_color\" title=\"$grayscale\"><span class=\"offscreen\">$grayscale</span><span class=\"aticon aticon-tint\" aria-hidden=\"true\"></span></a></li>';";
	}
	echo "
	insert_a11y_toolbar += '<li><a href=\"#\" role=\"button\" class=\"a11y-toggle-fontsize toggle-fontsize\" id=\"is_normal_fontsize\" title=\"$fontsize\"><span class=\"offscreen\">$fontsize</span><span class=\"aticon aticon-font\" aria-hidden=\"true\"></span></a></li>';
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
		if ( ! $focus ) {
			$focus = "background-color: #f1f1f1; border-radius: 3px; box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6); clip: auto; color: #21759b; display: block; font-size: 14px; font-weight: bold; height: auto; line-height: normal; padding: 15px 23px 14px; position: absolute; left: 5px; top: 5px; text-decoration: none; text-transform: none; width: auto; z-index: 100000;";
		}
		$passive = get_option( 'asl_styles_passive' );
		$vis     = $invis = '';
		// if links are visible, "hover" is a focus style, otherwise, it's a passive style.
		if ( get_option( 'asl_visible' ) == 'on' ) {
			$vis = '#skiplinks a:hover,';
		} else {
			$invis = '#skiplinks a:hover,';
		}
		$styles .= "
		#skiplinks a, $invis #skiplinks a:visited { $passive }
		#skiplinks a:active, $vis #skiplinks a:focus { $focus  }
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
		$output = ( $html != '' ) ? "<div class=\"$visibility$is_rtl\" id=\"skiplinks\" role=\"navigation\">$html</div>" : '';
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
	$targets    = ( get_option( 'wpa_target' ) == 'on' ) ? "$('a').removeAttr('target');" : '';
	$tabindex   = ( get_option( 'wpa_tabindex' ) == 'on' ) ? "$('input,a,select,textarea,button').removeAttr('tabindex');" : '';
	$underlines = ( get_option( 'wpa_underline' ) == 'on' ) ? "$('a').css( 'text-decoration','underline' );$('a').on( 'focusin mouseenter', function() { $(this).css( 'text-decoration','none' ); });$('a').on( 'focusout mouseleave', function() { $(this).css( 'text-decoration','underline' ); } );" : '';
	
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
	$fontsize = apply_filters( 'wpa_fontsize_css', plugins_url( 'toolbar/css/a11y-fontsize.css', __FILE__ ) );
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

function wpa_update_settings() {
	wpa_check_version();
	if ( ! empty( $_POST ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpa-nonce' ) ) {
			die( "Security check failed" );
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'rta' ) {
			$rta_from_nav_menu           = ( isset( $_POST['rta_from_nav_menu'] ) ) ? 'on' : '';
			$rta_from_page_lists         = ( isset( $_POST['rta_from_page_lists'] ) ) ? 'on' : '';
			$rta_from_category_lists     = ( isset( $_POST['rta_from_category_lists'] ) ) ? 'on' : '';
			$rta_from_archive_links      = ( isset( $_POST['rta_from_archive_links'] ) ) ? 'on' : '';
			$rta_from_tag_clouds         = ( isset( $_POST['rta_from_tag_clouds'] ) ) ? 'on' : '';
			$rta_from_category_links     = ( isset( $_POST['rta_from_category_links'] ) ) ? 'on' : '';
			$rta_from_post_edit_links    = ( isset( $_POST['rta_from_post_edit_links'] ) ) ? 'on' : '';
			$rta_from_edit_comment_links = ( isset( $_POST['rta_from_edit_comment_links'] ) ) ? 'on' : '';
			update_option( 'rta_from_nav_menu', $rta_from_nav_menu );
			update_option( 'rta_from_page_lists', $rta_from_page_lists );
			update_option( 'rta_from_category_lists', $rta_from_category_lists );
			update_option( 'rta_from_archive_links', $rta_from_archive_links );
			update_option( 'rta_from_tag_clouds', $rta_from_tag_clouds );
			update_option( 'rta_from_category_links', $rta_from_category_links );
			update_option( 'rta_from_post_edit_links', $rta_from_post_edit_links );
			update_option( 'rta_from_edit_comment_links', $rta_from_edit_comment_links );
			$message = __( "Remove Title Attributes Settings Updated", 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . "</p></div>";
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'asl' ) {
			$asl_enable         = ( isset( $_POST['asl_enable'] ) ) ? 'on' : '';
			$asl_content        = ( isset( $_POST['asl_content'] ) ) ? $_POST['asl_content'] : '';
			$asl_navigation     = ( isset( $_POST['asl_navigation'] ) ) ? $_POST['asl_navigation'] : '';
			$asl_sitemap        = ( isset( $_POST['asl_sitemap'] ) ) ? $_POST['asl_sitemap'] : '';
			$asl_extra_target   = ( isset( $_POST['asl_extra_target'] ) ) ? $_POST['asl_extra_target'] : '';
			$asl_extra_text     = ( isset( $_POST['asl_extra_text'] ) ) ? $_POST['asl_extra_text'] : '';
			$asl_visible        = ( isset( $_POST['asl_visible'] ) ) ? 'on' : '';
			$asl_styles_focus   = ( isset( $_POST['asl_styles_focus'] ) ) ? $_POST['asl_styles_focus'] : '';
			$asl_styles_passive = ( isset( $_POST['asl_styles_passive'] ) ) ? $_POST['asl_styles_passive'] : '';
			update_option( 'asl_enable', $asl_enable );
			update_option( 'asl_content', $asl_content );
			update_option( 'asl_navigation', $asl_navigation );
			update_option( 'asl_sitemap', $asl_sitemap );
			update_option( 'asl_extra_target', $asl_extra_target );
			update_option( 'asl_extra_text', $asl_extra_text );
			update_option( 'asl_visible', $asl_visible );
			$notice = ( $asl_visible == 'on' ) ? "<p>" . __( 'WP Accessibility does not provide any styles for visible skiplinks. You can still set the look of the links using the textareas provided, but all other layout must be assigned in your theme.', 'wp-accessibility' ) . "</p>" : '';

			update_option( 'asl_styles_focus', $asl_styles_focus );
			update_option( 'asl_styles_passive', $asl_styles_passive );
			$message = __( "Add Skiplinks Settings Updated", 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . "</p>$notice</div>";
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'misc' ) {
			$wpa_lang                    = ( isset( $_POST['wpa_lang'] ) ) ? 'on' : '';
			$wpa_target                  = ( isset( $_POST['wpa_target'] ) ) ? 'on' : '';
			$wpa_labels                  = ( isset( $_POST['wpa_labels'] ) ) ? 'on' : '';
			$wpa_search                  = ( isset( $_POST['wpa_search'] ) ) ? 'on' : '';
			$wpa_tabindex                = ( isset ( $_POST['wpa_tabindex'] ) ) ? 'on' : '';
			$wpa_underline               = ( isset ( $_POST['wpa_underline'] ) ) ? 'on' : '';
			$wpa_longdesc                = ( isset ( $_POST['wpa_longdesc'] ) ) ? esc_attr( $_POST['wpa_longdesc'] ) : 'false';
			$wpa_image_titles            = ( isset ( $_POST['wpa_image_titles'] ) ) ? 'on' : '';
			$wpa_more                    = ( isset ( $_POST['wpa_more'] ) ) ? 'on' : '';
			$wpa_focus                   = ( isset ( $_POST['wpa_focus'] ) ) ? 'on' : '';
			$wpa_focus_color             = ( isset( $_POST['wpa_focus_color'] ) ) ? str_replace( '#', '', $_POST['wpa_focus_color'] ) : '';
			$wpa_continue                = ( isset( $_POST['wpa_continue'] ) ) ? $_POST['wpa_continue'] : 'Continue Reading';
			$wpa_toolbar                 = ( isset( $_POST['wpa_toolbar'] ) ) ? 'on' : '';
			$wpa_toolbar_size            = ( isset( $_POST['wpa_toolbar_size'] ) ) ? $_POST['wpa_toolbar_size'] : '';
			$wpa_widget_toolbar          = ( isset( $_POST['wpa_widget_toolbar'] ) ) ? 'on' : '';
			$wpa_toolbar_gs              = ( isset( $_POST['wpa_toolbar_gs'] ) ) ? 'on' : '';
			$wpa_admin_css               = ( isset( $_POST['wpa_admin_css'] ) ) ? 'on' : '';
			$wpa_row_actions             = ( isset( $_POST['wpa_row_actions'] ) ) ? 'on' : '';
			$wpa_diagnostics             = ( isset( $_POST['wpa_diagnostics'] ) ) ? 'on' : '';
			$wpa_insert_roles            = ( isset( $_POST['wpa_insert_roles'] ) ) ? 'on' : '';
			$wpa_complementary_container = ( isset( $_POST['wpa_complementary_container'] ) ) ? str_replace( '#', '', $_POST['wpa_complementary_container'] ) : '';
			update_option( 'wpa_lang', $wpa_lang );
			update_option( 'wpa_target', $wpa_target );
			update_option( 'wpa_labels', $wpa_labels );
			update_option( 'wpa_search', $wpa_search );
			update_option( 'wpa_tabindex', $wpa_tabindex );
			update_option( 'wpa_underline', $wpa_underline );
			update_option( 'wpa_longdesc', $wpa_longdesc );
			update_option( 'wpa_image_titles', $wpa_image_titles );
			update_option( 'wpa_more', $wpa_more );
			update_option( 'wpa_focus', $wpa_focus );
			update_option( 'wpa_toolbar', $wpa_toolbar );
			update_option( 'wpa_toolbar_size', $wpa_toolbar_size );
			update_option( 'wpa_widget_toolbar', $wpa_widget_toolbar );
			update_option( 'wpa_toolbar_gs', $wpa_toolbar_gs );
			update_option( 'wpa_focus_color', $wpa_focus_color );
			update_option( 'wpa_continue', $wpa_continue );
			update_option( 'wpa_admin_css', $wpa_admin_css );
			update_option( 'wpa_row_actions', $wpa_row_actions );
			update_option( 'wpa_diagnostics', $wpa_diagnostics );
			update_option( 'wpa_insert_roles', $wpa_insert_roles );
			update_option( 'wpa_complementary_container', $wpa_complementary_container );
			$message = __( "Miscellaneous Accessibility Settings Updated", 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . "</p></div>";
		}
	} else {
		return;
	}
}

if ( get_option( 'wpa_search' ) == 'on' ) {
	add_filter( 'pre_get_posts', 'wpa_filter' );
}

function wpa_filter( $query ) {
	if ( ! is_admin() ) {
		if ( isset( $_GET['s'] ) && $_GET['s'] == '' ) {
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

function wpa_admin_menu() {
	?>
	<?php echo wpa_update_settings(); ?>
	<div class="wrap">
<h2><?php _e( 'WP Accessibility: Settings', 'wp-accessibility' ); ?></h2>

<div id="wpa_settings_page" class="postbox-container" style="width: 70%">
	<div class="metabox-holder">
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Remove Title Attributes', 'wp-accessibility' ); ?></h3>

				<div class="inside">
				<?php wpa_accessible_theme(); ?>
					<p>
						<?php _e( 'As of WordPress 4.0, the only globally added title attributes are in the WordPress tag cloud, showing the number of posts with that tag, and on the categories list, if the category has a term description.', 'wp-accessibility' ); ?>
					</p>
					<form method="post"
					      action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Remove title attributes from:', 'wp-accessibility' ); ?></legend>
							<ul>
								<?php if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) { ?>
								<li>
									<input type="checkbox" id="rta_from_nav_menu"
										name="rta_from_nav_menu" <?php if ( get_option( 'rta_from_nav_menu' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_nav_menu"><?php _e( 'Nav menus', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>
								<li>
									<input type="checkbox" id="rta_from_page_lists"
										name="rta_from_page_lists" <?php if ( get_option( 'rta_from_page_lists' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_page_lists"><?php _e( 'Page lists', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>
								<li>
									<input type="checkbox" id="rta_from_category_links"
										name="rta_from_category_links" <?php if ( get_option( 'rta_from_category_links' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_category_links"><?php _e( 'Category links', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>
								<li>
									<input type="checkbox" id="rta_from_post_edit_links"
										name="rta_from_post_edit_links" <?php if ( get_option( 'rta_from_post_edit_links' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_post_edit_links"><?php _e( 'Post edit links', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>
								<li>
									<input type="checkbox" id="rta_from_edit_comment_links"
										name="rta_from_edit_comment_links" <?php if ( get_option( 'rta_from_edit_comment_links' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_edit_comment_links"><?php _e( 'Edit comment links', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>	
								<?php } ?>							
								<?php if ( version_compare( get_bloginfo( 'version' ), '4.0', '<' ) ) { ?>								
								<li><input type="checkbox" id="rta_from_category_lists"
								           name="rta_from_category_lists" <?php if ( get_option( 'rta_from_category_lists' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_category_lists"><?php _e( 'Category lists', 'wp-accessibility' ); ?></label>
								</li>
			
								<?php } ?>
								<li><input type="checkbox" id="rta_from_tag_clouds"
								           name="rta_from_tag_clouds" <?php if ( get_option( 'rta_from_tag_clouds' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_tag_clouds"><?php _e( 'Tag clouds', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="rta_from_archive_links"
								           name="rta_from_archive_links" <?php if ( get_option( 'rta_from_archive_links' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_archive_links"><?php _e( 'Archive links', 'wp-accessibility' ); ?></label>
								</li>	
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="rta"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Update Title Attribute Settings', 'wp-accessibility' ) ?>"/></p>
					</form>
				</div>
			</div>


			<div class="postbox">
				<h3><?php _e( 'Add Skiplinks', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<?php if ( wpa_accessible_theme() && get_option( 'asl_enable' ) != 'on' ) { ?>
						<p>
							<?php _e( 'Your <code>accessibility-ready</code> theme has skip links built in.', 'wp-accessibility' ); ?>
						</p>
					<?php } else { ?>				
					<form method="post"
					      action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Configure Skiplinks', 'wp-accessibility' ); ?></legend>
							<ul>
								<li><input type="checkbox" id="asl_enable"
								           name="asl_enable" <?php if ( get_option( 'asl_enable' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="asl_enable"><?php _e( 'Enable Skiplinks', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="asl_visible"
								           name="asl_visible" <?php if ( get_option( 'asl_visible' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="asl_visible"><?php _e( 'Skiplinks always visible', 'wp-accessibility' ); ?></label>
								</li>
								<li><label
										for="asl_content"><?php _e( 'Skip to Content link target (ID of your main content container)', 'wp-accessibility' ); ?></label>
									<input type="text" id="asl_content" name="asl_content"
									       value="<?php esc_attr_e( get_option( 'asl_content' ) ); ?>"/></li>
								<li><label
										for="asl_navigation"><?php _e( 'Skip to Navigation link target (ID of your main navigation container)', 'wp-accessibility' ); ?></label>
									<input type="text" id="asl_navigation" name="asl_navigation"
									       value="<?php esc_attr_e( get_option( 'asl_navigation' ) ); ?>"/></li>
								<li><label
										for="asl_sitemap"><?php _e( 'Site Map link target (URL for your site map)', 'wp-accessibility' ); ?></label><input
										type="text" id="asl_sitemap" name="asl_sitemap" size="44"
										value="<?php esc_attr_e( get_option( 'asl_sitemap' ) ); ?>"/></li>
								<li><label
										for="asl_extra_target"><?php _e( 'Add your own link (link or container ID)', 'wp-accessibility' ); ?></label>
									<input type="text" id="asl_extra_target" name="asl_extra_target"
									       value="<?php esc_attr_e( get_option( 'asl_extra_target' ) ); ?>"/> <label
										for="asl_extra_text"><?php _e( 'Link text for your link', 'wp-accessibility' ); ?></label>
									<input type="text" id="asl_extra_text" name="asl_extra_text"
									       value="<?php esc_attr_e( get_option( 'asl_extra_text' ) ); ?>"/></li>
								<li><label
										for="asl_styles_focus"><?php _e( 'Styles for Skiplinks when they have focus', 'wp-accessibility' ); ?></label><br/>
									<textarea name='asl_styles_focus' id='asl_styles_focus' cols='60'
									          rows='4'><?php esc_attr_e( stripslashes( get_option( 'asl_styles_focus' ) ) ); ?></textarea>
								</li>
								<?php if ( get_option( 'asl_visible' ) != 'on' ) {
									$disabled = " disabled='disabled' style='background: #eee;'";
									$note     = ' ' . __( '(Not currently visible)', 'wp-accessibility' );
								} else {
									$disabled = $note = '';
								} ?>
								<li><label
										for="asl_styles_passive"><?php _e( 'Styles for Skiplinks without focus', 'wp-accessibility' );
										echo $note; ?></label><br/>
									<textarea name='asl_styles_passive' id='asl_styles_passive' cols='60'
									          rows='4'<?php echo $disabled; ?>><?php echo stripslashes( get_option( 'asl_styles_passive' ) ); ?></textarea>
								</li>
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="asl"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Update Skiplink Settings', 'wp-accessibility' ) ?>"/></p>
					</form>
					<?php } ?>					
				</div>
			</div>
			<div class="postbox">
				<h3 id="contrast"><?php _e( 'Miscellaneous Accessibility Settings', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<form method="post"
					      action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Miscellaneous', 'wp-accessibility' ); ?></legend>
							<ul>
								<?php if ( !wpa_accessible_theme() ) { ?>
								<li><input type="checkbox" id="wpa_lang"
								           name="wpa_lang" <?php if ( get_option( 'wpa_lang' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_lang"><?php _e( 'Add Site Language and text direction to HTML element', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_more"
								           name="wpa_more" <?php if ( get_option( 'wpa_more' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_more"><?php _e( 'Add post title to "more" links.', 'wp-accessibility' ); ?></label>
									<label
										for="wpa_continue"><?php _e( 'Continue reading text', 'wp-accessibility' ); ?></label>
									<input type="text" id="wpa_continue" name="wpa_continue"
									       value="<?php esc_attr_e( get_option( 'wpa_continue' ) ); ?>"/></li>
										   								<li><input type="checkbox" id="wpa_insert_roles"
								           name="wpa_insert_roles" <?php if ( get_option( 'wpa_insert_roles' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_insert_roles"><?php _e( 'Add landmark roles to HTML5 structural elements', 'wp-accessibility' ); ?></label><br/><label
										for="wpa_complementary_container"><?php _e( 'ID for complementary role', 'wp-accessibility' ); ?></label><input
										type="text" id="wpa_complementary_container" name="wpa_complementary_container"
										value="#<?php esc_attr_e( get_option( 'wpa_complementary_container' ) ); ?>"/>
								</li>
								<li>
									<input type="checkbox" id="wpa_labels" name="wpa_labels" <?php checked( get_option( 'wpa_labels'), 'on' ); ?> /> <label for='wpa_labels'><?php _e( 'Automatically Label WordPress search form and comment forms', 'wp-accessibility' ); ?></label>
								</li>
								<?php } else { ?>
									<li><?php _e( '<strong>Four disabled features:</strong> Site language, continue reading text, landmark roles and standard form labels are defined in your <code>accessibility-ready</code> theme.', 'wp-accessibility' ); ?></li>
								<?php } ?>
								<li><input type="checkbox" id="wpa_target"
								           name="wpa_target" <?php if ( get_option( 'wpa_target' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_target"><?php _e( 'Remove target attribute from links', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_search"
								           name="wpa_search" <?php if ( get_option( 'wpa_search' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_search"><?php _e( 'Force search error on empty search submission (theme must have search.php template)', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_tabindex"
								           name="wpa_tabindex" <?php if ( get_option( 'wpa_tabindex' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_tabindex"><?php _e( 'Remove tabindex from focusable elements', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_underline"
								           name="wpa_underline" <?php if ( get_option( 'wpa_underline' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_underline"><?php _e( 'Force underline on all links', 'wp-accessibility' ); ?></label>
								</li>
								<li><label
										for="wpa_longdesc"><?php _e( 'Long Description UI', 'wp-accessibility' ); ?></label>
									<select id="wpa_longdesc" name="wpa_longdesc">
										<option value='link'<?php if ( get_option( 'wpa_longdesc' ) == "link" ) {
											echo 'selected="selected" ';
										} ?>><?php _e( 'Link to description', 'wp-accessibility' ); ?></option>
										<option value='jquery'<?php if ( get_option( 'wpa_longdesc' ) == "jquery" ) {
											echo 'selected="selected" ';
										} ?>><?php _e( 'Button trigger to overlay image', 'wp-accessibility' ); ?></option>
										<option value='false'
										        <?php if ( get_option( 'wpa_longdesc' ) == "false" || ! get_option( 'wpa_longdesc' ) ) {
											        echo 'selected="selected"';
										        } ?>><?php _e( 'Browser defaults only', 'wp-accessibility' ); ?></option>
									</select>
								</li>
								<li><input type="checkbox" id="wpa_admin_css"
								           name="wpa_admin_css" <?php if ( get_option( 'wpa_admin_css' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_admin_css"><?php _e( 'Enable WordPress Admin stylesheet', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_row_actions"
								           name="wpa_row_actions" <?php if ( get_option( 'wpa_row_actions' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_row_actions"><?php _e( 'Make row actions always visible', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_image_titles"
								           name="wpa_image_titles" <?php if ( get_option( 'wpa_image_titles' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_image_titles"><?php _e( 'Remove title attribute from images inserted into post content and featured images.', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_toolbar"
								           name="wpa_toolbar" <?php if ( get_option( 'wpa_toolbar' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_toolbar"><?php _e( 'Add Accessibility toolbar with fontsize adjustment and contrast toggle', 'wp-accessibility' ); ?></label>
								</li>
								<?php
								$size = get_option( 'wpa_toolbar_size' );
								?>
								<li><label
										for="wpa_toolbar_size"><?php _e( 'Toolbar font size', 'wp-accessibility' ); ?></label>
									<select name='wpa_toolbar_size' id='wpa_toolbar_size'>
										<option value=''><?php _e( 'Default size', 'wp-accessibility' ); ?></option>
										<?php
										for ( $i = 1; $i <= 2.5; ) {
											$current       = $i . 'em';
											$selected_size = ( $current == $size ) ? ' selected="selected"' : '';
											echo "<option value='$i" . "em'$selected_size>$i em</option>";
											$i = $i + .1;
										}
										?>
									</select>
								</li>
								<li><input type="checkbox" id="wpa_widget_toolbar"
								           name="wpa_widget_toolbar" <?php if ( get_option( 'wpa_widget_toolbar' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_widget_toolbar"><?php _e( 'Support Accessibility toolbar as shortcode or widget', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_toolbar_gs"
								           name="wpa_toolbar_gs" <?php if ( get_option( 'wpa_toolbar_gs' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_toolbar_gs"><?php _e( 'Include grayscale toggle with Accessibility toolbar', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_diagnostics"
								           name="wpa_diagnostics" <?php if ( get_option( 'wpa_diagnostics' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_diagnostics"><?php _e( 'Enable diagnostic CSS', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="wpa_focus"
								           name="wpa_focus" <?php if ( get_option( 'wpa_focus' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="wpa_focus"><?php _e( 'Add outline to elements on keyboard focus', 'wp-accessibility' ); ?></label>
									<label
										for="wpa_focus_color"><?php _e( 'Outline color (hexadecimal, optional)', 'wp-accessibility' ); ?></label><input
										type="text" id="wpa_focus_color" name="wpa_focus_color"
										value="#<?php esc_attr_e( get_option( 'wpa_focus_color' ) ); ?>"/></li>
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="misc"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Update Miscellaneous Settings', 'wp-accessibility' ) ?>"/></p>
					</form>
				</div>
			</div>
			<div class="postbox">
				<h3><?php _e( 'Color Contrast Tester', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<?php
					$colors = wpa_contrast();
					if ( $colors ) {
						$l_contrast    = wpa_luminosity( $colors['red1'], $colors['red2'], $colors['green1'], $colors['green2'], $colors['blue1'], $colors['blue2'] ) . ':1';
						$luminance_raw = wpa_luminosity( $colors['red1'], $colors['red2'], $colors['green1'], $colors['green2'], $colors['blue1'], $colors['blue2'] );
						$hex1          = esc_attr( $colors['hex1'] );
						$hex2          = esc_attr( $colors['hex2'] );
					} else {
						$hex1       = '';
						$hex2       = '';
						$l_contrast = false;
					}
					if ( $l_contrast ) {
						$results = "
			<div class='updated notice'>";
						$results .= "<p class=\"stats wcag2\">" . sprintf( __( 'Luminosity Contrast Ratio for <code>#%2$s</code> and <code>#%3$s</code> is <strong>%1$s</strong> (Threshold: greater than 7:1 for AAA, 4.5:1 for AA)', 'wp-accessibility' ), $l_contrast, $hex1, $hex2 ) . "</p><p>";
						if ( $luminance_raw >= 7 ) {
							$results .= __( "The colors compared <strong>pass</strong> the relative luminosity test at level AAA.", 'wp-accessibility' );
						}
						if ( $luminance_raw >= 4.5 && $luminance_raw < 7 ) {
							$results .= __( "The colors compared <strong>pass</strong> the relative luminosity test at level AA.", 'wp-accessibility' );
						}
						if ( $luminance_raw >= 3 && $luminance_raw < 4.5 ) {
							$results .= __( "The colors compared pass the relative luminosity test <strong>only when used in large print</strong> situations (greater than 18pt text or 14pt bold text.)", 'wp-accessibility' );
						}
						if ( $luminance_raw < 3 ) {
							$results .= __( "The colors compared <strong>do not pass</strong> the relative luminosity test.", 'wp-accessibility' );
						}
						$results .= " <a href='#contrast'>" . __( 'Test another set of colors', 'wp-accessibility' ) . "</a>";
						$results .= "</p>";
						$results .= "
				<div class=\"views\">
					<p class='large' style=\"font-size: 2em; line-height: 1.4;color: #$hex1;background: #$hex2;border: 3px solid #$hex1\">Large Print Example</p>
					<p class='small' style=\"font-size: .9em;color: #$hex1;background: #$hex2;border: 3px solid #$hex1\">Small Print Example</p>
					<p class='large' style=\"font-size: 2em; line-height: 1.4;color: #$hex2;background: #$hex1;border: 3px solid #$hex2\">Large Print Example (Inverted)</p>
					<p class='small' style=\"font-size: .9em;color: #$hex2;background: #$hex1;border: 3px solid #$hex2\">Small Print Example (Inverted)</p>
				</div>
			</div>";
						echo $results;
					}
					?>
					<form method="post"
					      action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Test of relative luminosity', 'wp-accessibility' ); ?></legend>
							<ul>
								<li class='fore'>
									<div id="fore"></div>
									<label
										for="color1"><?php _e( 'Foreground color', 'wp-accessibility' ); ?></label><br/><input
										type="text" name="color" value="#<?php esc_attr_e( $hex1 ); ?>" size="34" id="color1"/>
								</li>
								<li class='back'>
									<div id="back"></div>
									<label
										for="color2"><?php _e( 'Background color', 'wp-accessibility' ); ?></label><br/><input
										type="text" name="color2" value="#<?php esc_attr_e( $hex2 ); ?>" size="34" id="color2"/>
								</li>
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="contrast"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Check Color Contrast', 'wp-accessibility' ) ?>"/></p>
					</form>
				</div>
			</div>
			<div class="postbox" id="get-support">
				<h3><?php _e( 'Get Plug-in Support', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<?php wpa_get_support_form(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="postbox-container" style="width:20%">
	<div class="metabox-holder">
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Support this Plugin', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<p>
						<a href="https://twitter.com/intent/follow?screen_name=joedolson" class="twitter-follow-button"
						   data-size="small" data-related="joedolson">Follow @joedolson</a>
						<script>!function (d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (!d.getElementById(id)) {
									js = d.createElement(s);
									js.id = id;
									js.src = "https://platform.twitter.com/widgets.js";
									fjs.parentNode.insertBefore(js, fjs);
								}
							}(document, "script", "twitter-wjs");</script>
					</p>
					<p><?php _e( "If you've found WP Accessibility useful, then please consider <a href='http://wordpress.org/extend/plugins/wp-accessibility/'>rating it five stars</a>, <a href='http://www.joedolson.com/donate.php'>making a donation</a>, or <a href='http://translate.joedolson.com/projects/wp-accessibility'>helping with translation</a>.", 'wp-accessibility' ); ?></p>

					<div>
						<p><?php _e( '<a href="http://www.joedolson.com/donate.php">Make a donation today!</a> Your donation counts - donate $5, $20, or $100 and help me keep this plug-in running!', 'wp-to-twitter' ); ?></p>

						<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<div>
								<input type="hidden" name="cmd" value="_s-xclick"/>
								<input type="hidden" name="hosted_button_id" value="QK9MXYGQKYUZY"/>
								<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif"
								       name="submit" alt="Donate"/>
								<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php if ( wpa_accessible_theme() ) { ?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Your Theme', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<p>
					<?php _e( "You're using a theme reviewed as <code>accessibility-ready</code> by the WordPress theme review team. Some options have been disabled in WP Accessibility because your theme has taken care of that issue.", 'wp-accessibility' ); ?>
					</p>
					<p>
					<?php printf( __( 'Read more about the <a href="%s">WordPress accessibility-ready tag</a>', 'wp-accessibility' ), "https://make.wordpress.org/themes/handbook/review/accessibility/" ); ?>
					</p>
				</div>
			</div>
		</div>
		<?php } ?>
		
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Accessibility References', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<ul>
						<li><a href="http://make.wordpress.org/accessibility/">Make WordPress: Accessibility</a></li>
						<li><a href="http://codex.wordpress.org/Accessibility">WordPress Codex on Accessibility</a></li>
						<li><a href="http://make.wordpress.org/support/user-manual/web-publishing/accessibility/">WordPress
								User Manual: Accessibility</a></li>
						<li><a href="https://www.joedolson.com/tools/color-contrast.php">Test Color Contrast</a></li>
						<li><a href="http://wave.webaim.org/">WAVE: Web accessibility evaluation tool</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Contributing References', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<ul>
						<li>
							<a href="http://www.accessibleculture.org/articles/2010/08/continue-reading-links-in-wordpress/">Continue
								Reading Links in WordPress</a></li>
						<li><a href="http://urbanfuturistic.net/weblog/2012/02/27/incredible-failing-accesskey/">The
								Incredible Failing AccessKey</a></li>
						<li><a href="http://www.mothereffingtoolconfuser.com">Mother Effing Tool Confuser</a></li>
						<li><a href="http://wordpress.org/extend/plugins/remove-title-attributes/">Remove Title
								Attributes</a></li>
						<li><a href="http://accessites.org/site/2008/11/wordpress-and-accessibility/#comment-2926">WordPress
								and Accessibility (Comment)</a></li>
						<li><a href="http://wordpress.org/extend/plugins/img-title-removal/">IMG Title Removal</a></li>
						<li><a href="http://www.usableinteractions.com/2012/11/accessibility-toolbar/">Accessibility
								Toolbar</a></li>
						<li><a href="https://wordpress.org/plugins/long-description-for-image-attachments/">WordPress
								Long Description</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Customization Notes', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<p>
						<?php _e( 'It is almost impossible for the Accessibility Toolbar to guarantee a good result for large text or high contrast modes. Author your own high-contrast styles by placing a stylesheet called <code>a11y-contrast.css</code> in your Theme\'s stylesheet directory.', 'wp-accessibility' ); ?>
					</p>

					<p>
						<?php _e( 'Define custom styles for large print by assigning them in the body class <code>.fontsize</code> in your theme stylesheet.', 'wp-accessibility' ); ?>
					</p>

					<p>
						<?php _e( 'Define a custom long description template by adding the template "longdesc-template.php" to your theme directory.', 'wp-accessibility' ); ?>
					</p>

					<p>
						<?php _e( 'The <a href="#wpa_widget_toolbar">shortcode for the Accessibility toolbar</a> (if enabled) is <code>[wpa_toolbar]</code>', 'wp-accessibility' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>

	</div><?php
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

// The built-in Recent Posts widget hard-coded title attributes until 3.8.
class WP_Widget_Recent_Posts_No_Title_Attributes extends WP_Widget {

	function __construct() {
		parent::__construct( false, $name = __( 'WP A11Y: Recent Posts', 'wp-accessibility' ) );
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_recent_posts', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];

			return;
		}

		ob_start();
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Recent Posts' ) : $instance['title'], $instance, $args );
		if ( ! $number = (int) $instance['number'] ) {
			$number = 5;
		}

		$r = new WP_Query( array( 'showposts'           => $number,
		                          'nopaging'            => 0,
		                          'post_status'         => 'publish',
		                          'ignore_sticky_posts' => 1
			) );
		if ( $r->have_posts() ) :
			?>
			<?php echo $before_widget; ?>
			<?php if ( $title ) {
			echo $before_title . $title . $after_title;
		} ?>
			<?php echo wpa_deprecated_warning( 'recent_posts' ); ?>
			<ul>
				<?php while ( $r->have_posts() ) : $r->the_post(); ?>
					<li><a href="<?php the_permalink() ?>"><?php if ( get_the_title() ) {
								the_title();
							} else {
								the_ID();
							} ?> </a></li>
				<?php endwhile; ?>
			</ul>
			<?php echo $after_widget; ?>
			<?php
			wp_reset_query();  // Restore global post data stomped by the_post().
		endif;

		$cache[ $args['widget_id'] ] = ob_get_flush();
		wp_cache_add( 'widget_recent_posts', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = abs( (int) $new_instance['number'] );
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_recent_entries'] ) ) {
			delete_option( 'widget_recent_entries' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_recent_posts', 'widget' );
	}

	function form( $instance ) {
		$title = ( isset( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		if ( ! isset( $instance['number'] ) || $number != (int) $instance['number'] ) {
			$number = 5;
		}
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php esc_attr_e( $title ); ?>"/>
		</p>

		<p><label
				for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>"
			       name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php esc_attr_e( $number ); ?>"
			       size="3"/></p>
	<?php
	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Widget_Recent_Posts_No_Title_Attributes");' ) );

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

function wpa_get_support_form() {
	global $current_user, $wpa_version;
	get_currentuserinfo();
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
	if ( isset( $_POST['wpt_support'] ) ) {
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
				echo "<div class='message updated'><p>" . __( 'I cannot provide free support, but will treat your request as a bug report, and will incorporate any permanent solutions I discover into the plug-in.', 'wp-accessibility' ) . "</p></div>";
			}
		}
	}
	$admin_url = admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' );

	echo "
	<form method='post' action='$admin_url'>
		<div><input type='hidden' name='_wpnonce' value='" . wp_create_nonce( 'wpa-nonce' ) . "' /></div>
		<div>";
	echo "
		<p>" .
	     __( '<strong>Please note</strong>: I do keep records of those who have donated, but if your donation came from somebody other than your account at this web site, you must note this in your message.', 'wp-accessibility' )
	     . "</p>";
	echo "
		<p>
		<code>" . __( 'From:', 'wp-accessibility' ) . " \"$current_user->display_name\" &lt;$current_user->user_email&gt;</code>
		</p>
		<p>
		<input type='checkbox' name='has_read_faq' id='has_read_faq' value='on' /> <label for='has_read_faq'>" . sprintf( __( 'I have read <a href="%1$s">the FAQ for this plug-in</a> <span>(required)</span>', 'wp-accessibility' ), 'http://www.joedolson.com/wp-accessibility/faqs/' ) . "</label>
        </p>
        <p>
        <input type='checkbox' name='has_donated' id='has_donated' value='on' /> <label for='has_donated'>" . sprintf( __( 'I have <a href="%1$s">made a donation to help support this plug-in</a>', 'wp-accessibility' ), 'http://www.joedolson.com/donate.php' ) . "</label>
        </p>
        <p>
        <label for='support_request'>" . __( 'Support Request:', 'wp-accessibility' ) . "</label><br /><textarea name='support_request' required aria-required='true' id='support_request' cols='80' rows='10'>" . stripslashes( $request ) . "</textarea>
		</p>
		<p>
		<input type='submit' value='" . __( 'Send Support Request', 'wp-accessibility' ) . "' name='wpt_support' class='button-primary' />
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
		if ( isset( $_REQUEST['post_id'] ) ) {
			$args['referrer'] = (int) $_REQUEST['post_id'];
		}
		if ( ! empty( $image->post_content ) ) {
			$search  = '<img';
			$replace = $search . ' longdesc="' . esc_url( add_query_arg( $args, home_url() ) ) . '"';
			$html    = str_replace( $search, $replace, $html );
			$html .= '<a id="' . esc_attr( longdesc_return_anchor( $image->ID ) ) . '"></a>';
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
