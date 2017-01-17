<?php

add_shortcode( 'wpa_toolbar', 'wpa_toolbar_html' );
function wpa_toolbar_html() {
	$contrast         = __( 'Toggle High Contrast', 'wp-accessibility' );
	$grayscale        = __( 'Toggle Grayscale', 'wp-accessibility' );
	$fontsize         = __( 'Toggle Font size', 'wp-accessibility' );
	$enable_grayscale = ( get_option( 'wpa_toolbar_gs' ) == 'on' ) ? true : false;
	$enable_contrast  = ( get_option( 'wpa_toolbar_ct' ) == 'off' ) ? false : true;
	$enable_fontsize  = ( get_option( 'wpa_toolbar_fs' ) == 'off' ) ? false : true;
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
	// Toolbar does not work on Edge. Disable unless I solve the issue.
	$user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if ( preg_match( '/Edge/i', $user_agent ) ) {
		return;
	}
	
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
	insert_a11y_toolbar += '<div class=\"$responsive a11y-toolbar$is_rtl$is_right\">';
	insert_a11y_toolbar += '<ul class=\"a11y-toolbar-list\">';";
	if ( get_option( 'wpa_toolbar' ) == 'on' && $enable_contrast ) {	
		echo "insert_a11y_toolbar += '<li class=\"a11y-toolbar-list-item\"><button type=\"button\" class=\"a11y-toggle-contrast toggle-contrast\" id=\"is_normal_contrast\" aria-pressed=\"false\"><span class=\"offscreen\">$contrast</span><span class=\"aticon aticon-adjust\" aria-hidden=\"true\"></span></button></li>';";
	}
	if ( get_option( 'wpa_toolbar' ) == 'on' && $enable_grayscale ) {
		echo "insert_a11y_toolbar += '<li class=\"a11y-toolbar-list-item\"><button type=\"button\" class=\"a11y-toggle-grayscale toggle-grayscale\" id=\"is_normal_color\" aria-pressed=\"false\"><span class=\"offscreen\">$grayscale</span><span class=\"aticon aticon-tint\" aria-hidden=\"true\"></span></button></li>';";
	}
	if ( get_option( 'wpa_toolbar' ) == 'on' && $enable_fontsize ) {
		echo "insert_a11y_toolbar += '<li class=\"a11y-toolbar-list-item\"><button type=\"button\" class=\"a11y-toggle-fontsize toggle-fontsize\" id=\"is_normal_fontsize\" aria-pressed=\"false\"><span class=\"offscreen\">$fontsize</span><span class=\"aticon aticon-font\" aria-hidden=\"true\"></span></button></li>';";
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