<?php
/**
 * Generate Toolbar HTML & JS
 *
 * @category Toolbar
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-accessibility/
 */

add_shortcode( 'wpa_toolbar', 'wpa_toolbar_shortcode' );
/**
 * Output Toolbar shortcode
 *
 * @param array  $atts Shortcode attributes.
 * @param string $content Contained content.
 *
 * @return string
 */
function wpa_toolbar_shortcode( $atts, $content ) {
	$args = shortcode_atts(
		array(
			'type'    => 'widget',
			'control' => 'button',
		),
		$atts,
		'wpa_toolbar'
	);

	return wpa_toolbar_html( $args['type'], $args['control'] );
}

/**
 * Generate Toolbar as HTML.
 *
 * @param string $type widget, shortcode, js generated.
 * @param string $control Control type: button or not.
 *
 * @return string HTML.
 */
function wpa_toolbar_html( $type = 'widget', $control = 'button' ) {
	$contrast         = __( 'Toggle High Contrast', 'wp-accessibility' );
	$grayscale        = __( 'Toggle Grayscale', 'wp-accessibility' );
	$fontsize         = __( 'Toggle Font size', 'wp-accessibility' );
	$enable_grayscale = ( 'on' === get_option( 'wpa_toolbar_gs' ) && current_user_can( 'manage_options' ) ) ? true : false;
	$enable_contrast  = ( 'off' === get_option( 'wpa_toolbar_ct' ) ) ? false : true;
	$enable_fontsize  = ( 'off' === get_option( 'wpa_toolbar_fs' ) ) ? false : true;
	$responsive       = ( 'on' === get_option( 'wpa_toolbar_mobile' ) ) ? 'a11y-responsive ' : '';
	$is_rtl           = ( is_rtl() ) ? ' rtl' : ' ltr';
	$is_right         = ( 'on' === get_option( 'wpa_toolbar_right' ) ) ? ' right' : ' left';
	$toolbar_type     = ( 'widget' === $type ) ? 'a11y-toolbar-widget' : 'a11y-toolbar';
	$control_type     = ( 'button' !== $control ) ? 'a href="#" role="button"' : 'button type="button"'; // button control does not work in Edge.
	$closure          = ( 'button' !== $control ) ? 'a' : 'button';  // button control does not work in Edge.
	$toolbar          = '
<!-- a11y toolbar widget -->
<div class="' . $responsive . ' ' . $is_rtl . ' ' . $is_right . ' ' . $toolbar_type . '">
	<ul>';
	if ( $enable_contrast ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle-contrast toggle-contrast" id="is_normal_contrast" aria-pressed="false"><span class="offscreen">' . $contrast . '</span> <span class="aticon aticon-adjust" aria-hidden="true"></span></' . $closure . '></li>';
	}
	if ( $enable_grayscale ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle-grayscale toggle-grayscale" id="is_normal_color" aria-pressed="false"><span class="offscreen">' . $grayscale . '</span> <span class="aticon aticon-tint" aria-hidden="true"></span></' . $closure . '></li>';
	}
	if ( $enable_fontsize ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle-fontsize toggle-fontsize" id="is_normal_fontsize" aria-pressed="false"><span class="offscreen">' . $fontsize . '</span> <span class="aticon aticon-font" aria-hidden="true"></span></' . $closure . '></li>';
	}
	$toolbar .= '
	</ul>
</div>
<!-- // a11y toolbar widget -->';

	return $toolbar;
}

/**
 * Generate Toolbar variables for localization in JS.
 */
function wpa_toolbar_js() {
	// Toolbar does not work on Edge. Disable unless I solve the issue.
	$default    = ( false !== (bool) get_option( 'wpa_toolbar_default' ) ) ? get_option( 'wpa_toolbar_default' ) : 'body';
	$location   = apply_filters( 'wpa_move_toolbar', $default );
	$is_rtl     = ( is_rtl() ) ? ' rtl' : ' ltr';
	$is_right   = ( 'on' === get_option( 'wpa_toolbar_right' ) ) ? ' right' : ' left';
	$responsive = ( 'on' === get_option( 'wpa_toolbar_mobile' ) ) ? 'a11y-responsive ' : 'a11y-non-responsive ';

	$contrast         = __( 'Toggle High Contrast', 'wp-accessibility' );
	$grayscale        = __( 'Toggle Grayscale', 'wp-accessibility' );
	$fontsize         = __( 'Toggle Font size', 'wp-accessibility' );
	$enable_grayscale = ( 'on' === get_option( 'wpa_toolbar_gs' ) && current_user_can( 'manage_options' ) ) ? 'true' : 'false';
	$enable_fontsize  = ( 'off' === get_option( 'wpa_toolbar_fs' ) ) ? 'false' : 'true';
	$enable_contrast  = ( 'off' === get_option( 'wpa_toolbar_ct' ) ) ? 'false' : 'true';

	return array(
		'location'         => $location,
		'is_rtl'           => $is_rtl,
		'is_right'         => $is_right,
		'responsive'       => $responsive,
		'contrast'         => $contrast,
		'grayscale'        => $grayscale,
		'fontsize'         => $fontsize,
		'enable_grayscale' => $enable_grayscale,
		'enable_fontsize'  => $enable_fontsize,
		'enable_contrast'  => $enable_contrast,
	);
}
