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

/**
 * Toolbar class.
 */
require_once( dirname( __FILE__ ) . '/class-wp-accessibility-toolbar.php' );

add_action( 'widgets_init', 'wpa_register_toolbar_widget' );
/**
 * Register toolbar widget.
 */
function wpa_register_toolbar_widget() {
	register_widget( 'Wp_Accessibility_Toolbar' );
}

add_action( 'wp_enqueue_scripts', 'wpa_register_scripts' );
/**
 * Register jQuery scripts.
 */
function wpa_register_scripts() {
	$wpa_version = ( SCRIPT_DEBUG ) ? wp_rand( 10000, 100000 ) : wpa_check_version();
	$wpatb       = ( SCRIPT_DEBUG ) ? plugins_url( 'js/wpa-toolbar.js', __FILE__ ) : plugins_url( 'js/wpa-toolbar.min.js', __FILE__ );
	wp_register_script( 'wpa-toolbar', $wpatb, array(), $wpa_version, true );
	$wpaui = ( SCRIPT_DEBUG ) ? plugins_url( 'js/a11y.js', __FILE__ ) : plugins_url( 'js/a11y.min.js', __FILE__ );
	wp_register_script( 'ui-a11y', $wpaui, array( 'jquery' ), $wpa_version, true );
}

add_action( 'wp_enqueue_scripts', 'wpa_toolbar_enqueue_scripts' );
/**
 * Enqueue Toolbar scripts dependent on options.
 */
function wpa_toolbar_enqueue_scripts() {
	$wpa_version = ( SCRIPT_DEBUG ) ? wp_rand( 10000, 100000 ) : wpa_check_version();
	wp_enqueue_script( 'jquery' );
	if ( 'on' === get_option( 'wpa_toolbar' ) ) {
		// Enqueue Toolbar JS if enabled.
		wp_enqueue_script( 'wpa-toolbar' );
		wp_localize_script( 'wpa-toolbar', 'wpatb', wpa_toolbar_js() );
	}
	wp_enqueue_script( 'ui-a11y' );

	// High Contrast CSS.
	$plugin_path = plugins_url( 'toolbar/css/a11y-contrast.css', __FILE__ );
	if ( file_exists( get_stylesheet_directory() . '/a11y-contrast.css' ) ) {
		$plugin_path = get_stylesheet_directory_uri() . '/a11y-contrast.css';
	}
	$plugin_path = array(
		'path' => $plugin_path,
	);
	wp_localize_script( 'ui-a11y', 'wpa11y', $plugin_path );

	// Font files for toolbar.
	wp_register_style( 'ui-font', plugins_url( 'toolbar/fonts/css/a11y-toolbar.css', __FILE__ ), array(), $wpa_version );

	// Toolbar CSS.
	/**
	 * Filter URL for toolbar CSS.
	 *
	 * @hook wpa_toolbar_css
	 *
	 * @param {string} $url URL to stylesheet for accessibility toolbar.
	 *
	 * @return string
	 */
	$toolbar_styles = apply_filters( 'wpa_toolbar_css', plugins_url( 'toolbar/css/a11y.css', __FILE__ ) );
	wp_register_style( 'ui-a11y', $toolbar_styles, array( 'ui-font' ), $wpa_version );

	// Font resizing stylesheet.
	$fontsize_stylesheet = ( 'on' === get_option( 'wpa_alternate_fontsize' ) ) ? 'a11y-fontsize-alt' : 'a11y-fontsize';
	/**
	 * Filter the URL to the stylesheet controlling large font views.
	 *
	 * @hook wpa_fontsize_css
	 *
	 * @param {string} $stylesheet URL for increased font size stylesheet.
	 *
	 * @return string
	 */
	$fontsize = apply_filters( 'wpa_fontsize_css', plugins_url( 'toolbar/css/' . $fontsize_stylesheet . '.css', __FILE__ ) );
	wp_register_style( 'ui-fontsize.css', $fontsize, array(), $wpa_version );

	// Control toolbar font size.
	$toolbar_size = get_option( 'wpa_toolbar_size' );
	$toolbar_size = ( false === stripos( $toolbar_size, 'em' ) ) ? $toolbar_size . 'px' : $toolbar_size;
	// Only enable styles when required by options.
	if ( get_option( 'wpa_toolbar_size' ) && 'on' === get_option( 'wpa_toolbar' ) ) {
		wp_add_inline_style( 'ui-a11y', '.a11y-toolbar ul li button { font-size: ' . $toolbar_size . ' !important; }' );
	}
	if ( $toolbar_styles && $fontsize ) {
		wp_enqueue_style( 'ui-a11y' );
		wp_enqueue_style( 'ui-fontsize.css' );
	}
}

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
	$contrast         = esc_html__( 'Toggle High Contrast', 'wp-accessibility' );
	$grayscale        = esc_html__( 'Toggle Grayscale', 'wp-accessibility' );
	$fontsize         = esc_html__( 'Toggle Font size', 'wp-accessibility' );
	$enable_grayscale = ( 'on' === get_option( 'wpa_toolbar_gs' ) && current_user_can( 'manage_options' ) ) ? true : false;
	$enable_contrast  = ( 'off' === get_option( 'wpa_toolbar_ct' ) ) ? false : true;
	$enable_fontsize  = ( 'off' === get_option( 'wpa_toolbar_fs' ) ) ? false : true;
	$responsive       = ( 'on' === get_option( 'wpa_toolbar_mobile' ) ) ? 'a11y-responsive ' : '';
	$is_rtl           = ( is_rtl() ) ? ' rtl' : ' ltr';
	$is_reversed      = ( 'on' === get_option( 'wpa_toolbar_right' ) ) ? ' reverse' : ' default';
	$toolbar_type     = ( 'widget' === $type ) ? 'a11y-toolbar-widget' : 'a11y-toolbar';
	$control_type     = ( 'button' !== $control ) ? 'a href="#" role="button"' : 'button type="button"'; // button control does not work in Edge.
	$closure          = ( 'button' !== $control ) ? 'a' : 'button';  // button control does not work in Edge.
	$toolbar          = '
<!-- a11y toolbar widget -->
<div class="' . $responsive . ' ' . $is_rtl . ' ' . $is_reversed . ' ' . $toolbar_type . '">
	<ul>';
	if ( $enable_contrast ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle a11y-toggle-contrast toggle-contrast" id="is_normal_contrast" aria-pressed="false"><span class="offscreen">' . $contrast . '</span> <span class="aticon aticon-adjust" aria-hidden="true"></span></' . $closure . '></li>';
	}
	if ( $enable_grayscale ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle a11y-toggle-grayscale toggle-grayscale" id="is_normal_color" aria-pressed="false"><span class="offscreen">' . $grayscale . '</span> <span class="aticon aticon-tint" aria-hidden="true"></span></' . $closure . '></li>';
	}
	if ( $enable_fontsize ) {
		$toolbar .= '<li><' . $control_type . ' class="a11y-toggle a11y-toggle-fontsize toggle-fontsize" id="is_normal_fontsize" aria-pressed="false"><span class="offscreen">' . $fontsize . '</span> <span class="aticon aticon-font" aria-hidden="true"></span></' . $closure . '></li>';
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
	$default = ( false !== (bool) trim( get_option( 'wpa_toolbar_default' ) ) ) ? get_option( 'wpa_toolbar_default' ) : 'body';
	/**
	 * Filter attachment location of the toolbar. Default `body`.
	 *
	 * @hook wpa_move_toolbar
	 *
	 * @param {string} $el Target element selector.
	 *
	 * @return string
	 */
	$location    = apply_filters( 'wpa_move_toolbar', $default );
	$is_rtl      = ( is_rtl() ) ? 'rtl' : 'ltr';
	$is_reversed = ( 'on' === get_option( 'wpa_toolbar_right' ) ) ? 'reversed' : 'default';
	$responsive  = ( 'on' === get_option( 'wpa_toolbar_mobile' ) ) ? 'a11y-responsive' : 'a11y-non-responsive';

	$contrast         = esc_html__( 'Toggle High Contrast', 'wp-accessibility' );
	$grayscale        = esc_html__( 'Toggle Grayscale', 'wp-accessibility' );
	$fontsize         = esc_html__( 'Toggle Font size', 'wp-accessibility' );
	$enable_grayscale = ( 'on' === get_option( 'wpa_toolbar_gs' ) && current_user_can( 'manage_options' ) ) ? 'true' : 'false';
	$enable_fontsize  = ( 'off' === get_option( 'wpa_toolbar_fs' ) ) ? 'false' : 'true';
	$enable_contrast  = ( 'off' === get_option( 'wpa_toolbar_ct' ) ) ? 'false' : 'true';

	return array(
		'location'         => $location,
		'is_rtl'           => $is_rtl,
		'is_right'         => $is_reversed,
		'responsive'       => $responsive,
		'contrast'         => $contrast,
		'grayscale'        => $grayscale,
		'fontsize'         => $fontsize,
		'enable_grayscale' => $enable_grayscale,
		'enable_fontsize'  => $enable_fontsize,
		'enable_contrast'  => $enable_contrast,
	);
}
