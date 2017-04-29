<?php

add_shortcode( 'wpa_toolbar', 'wpa_toolbar_html' );
function wpa_toolbar_html( $type = 'widget' ) {
	$contrast         = __( 'Toggle High Contrast', 'wp-accessibility' );
	$grayscale        = __( 'Toggle Grayscale', 'wp-accessibility' );
	$fontsize         = __( 'Toggle Font size', 'wp-accessibility' );
	$enable_grayscale = ( get_option( 'wpa_toolbar_gs' ) == 'on' && current_user_can( 'manage_options' ) ) ? true : false;
	$enable_contrast  = ( get_option( 'wpa_toolbar_ct' ) == 'off' ) ? false : true;
	$enable_fontsize  = ( get_option( 'wpa_toolbar_fs' ) == 'off' ) ? false : true;
	$responsive       = ( get_option( 'wpa_toolbar_mobile' ) == 'on' ) ? 'a11y-responsive ' : '';
	$is_rtl           = ( is_rtl() ) ? ' rtl' : ' ltr';
	$is_right         = ( get_option( 'wpa_toolbar_right' ) == 'on' ) ? ' right' : ' left';
	$toolbar_type     = ( $type == 'widget' ) ? 'a11y-toolbar-widget' : 'a11y-toolbar';
	$control_type     = ( $type != 'button' ) ? 'a href="#" role="button"' : 'button type="button"'; // button control does not work in Edge.
	$closure          = ( $type != 'button' ) ? 'a' : 'button';  // button control does not work in Edge
	$toolbar          = '
<!-- a11y toolbar widget -->
<div class="' . $responsive . ' ' . $is_rtl . ' ' . $is_right . ' ' . $toolbar_type . '">
	<ul>';
	if ( $enable_contrast ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle-contrast toggle-contrast" id="is_normal_contrast" aria-pressed="false"><span class="offscreen">'.$contrast.'</span><span class="aticon aticon-adjust" aria-hidden="true"></span></' . $closure . '></li>';
	}
	if ( $enable_grayscale ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle-grayscale toggle-grayscale" id="is_normal_color" aria-pressed="false"><span class="offscreen">'.$grayscale.'</span><span class="aticon aticon-tint" aria-hidden="true"></span></' . $closure . '></li>';
	}
	if ( $enable_fontsize ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle-fontsize toggle-fontsize" id="is_normal_fontsize" aria-pressed="false"><span class="offscreen">'.$fontsize.'</span><span class="aticon aticon-font" aria-hidden="true"></span></' . $closure . '></li>';
	}
	$toolbar .= '
	</ul>
</div>
<!-- // a11y toolbar widget -->';

	return $toolbar;
}

function wpa_toolbar_js() {
	// Toolbar does not work on Edge. Disable unless I solve the issue.
	$default          = ( get_option( 'wpa_toolbar_default' ) != '' ) ? get_option( 'wpa_toolbar_default' ) : 'body';
	$location         = apply_filters( 'wpa_move_toolbar', $default );
	$user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	$is_rtl           = ( is_rtl() ) ? ' rtl' : ' ltr';
	$is_right         = ( get_option( 'wpa_toolbar_right' ) == 'on' ) ? ' right' : ' left';
	$responsive       = ( get_option( 'wpa_toolbar_mobile' ) == 'on' ) ? 'a11y-responsive ' : 'a11y-non-responsive ';	
		
	if ( preg_match( '/Edge/i', $user_agent ) ) {
		echo wpa_toolbar_html( 'js' );
		echo "<script type='text/javascript'>
		//<![CDATA[
		(function( $ ) { 'use strict';
			var toolbar = $( '.a11y-toolbar-widget' );
			toolbar.removeClass( 'a11y-toolbar-widget' );
			$( document ).find( '$location' ).prepend( toolbar );
		}(jQuery));
		//]]>
		</script>";
	} else {
	
		$contrast         = __( 'Toggle High Contrast', 'wp-accessibility' );
		$grayscale        = __( 'Toggle Grayscale', 'wp-accessibility' );
		$fontsize         = __( 'Toggle Font size', 'wp-accessibility' );
		$enable_grayscale = ( get_option( 'wpa_toolbar_gs' ) == 'on' && current_user_can( 'manage_options' ) ) ? true : false;
		$enable_fontsize  = ( get_option( 'wpa_toolbar_fs' ) == 'off' ) ? false : true;
		$enable_contrast  = ( get_option( 'wpa_toolbar_ct' ) == 'off' ) ? false : true;

		
		echo
		"
	<script type='text/javascript'>
	//<![CDATA[
	(function( $ ) { 'use strict';
		var insert_a11y_toolbar = '<!-- a11y toolbar -->';
		insert_a11y_toolbar += '<div class=\"" . $responsive . "a11y-toolbar$is_rtl$is_right\">';
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
}