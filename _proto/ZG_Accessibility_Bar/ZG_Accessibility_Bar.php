<?php
/**
 * Description of ZG_Accessibility_Bar
 *
 * @author Tom Auger
 * @version 1.1
 * 
 * Changelog:
 * -------------------------------------
 * 1.1
 * - Tomas added high contrast mode. In Development.
 * 
 * 1.0.1
 * - Eric added documentation for Accessibility bar functions
 * 
 * 1.0
 * - Tom finalized the accessibility bar code, and improved several aspects.
 */

/**
 * Display the accessibility bar expand and collapse buttons.
 *
 * You don't need to call this from your theme, unless you have explicitly set the USE_EXPAND_COLLAPSE_BUTTONS to false
 * (usually done using the filter ''zg_accessibility_bar-use_expand_collapse_buttons'). By default, these buttons
 * are added to the accessibility bar itself within accessibility_bar.php.
 */
function the_accessibility_bar_expand_collapse_buttons(){
	 ZG_Accessibility_Bar::the_expand_collapse_buttons( true );
}

class ZG_Accessibility_Bar {
	const PLUGIN_NAME = 'zg-accessibility-bar';
	const TD = "zg-accessibility-bar-textdomain";

	// Configuration constants
	const BAR_INITIALLY_CLOSED = true; // can be overridden with the filter 'zg_accessibility_bar-initially_closed'
	const USE_EXPAND_COLLAPSE_BUTTONS = true; // can be overridden with the filter 'zg_accessibility_bar-use_expand_collapse_buttons'

	const COLLAPSED_CLASS = 'collapsed'; // class added to the accessibility bar and the expand/collapse buttons to indicate the open/closed state of the bar

	const FONT_MAX_SIZE = 5; // em ( x 100% )
	const FONT_MIN_SIZE = 1; // almost always 1
	const FONT_SCALE_FACTOR = .20; // percentage that FONT_MIN_SIZE is scaled, compared to FONT_MAX_SIZE
	const FONT_SCALE_INCREMENT = .5; // amount (x 100%) by which font size increases / decreases when you click the button once

	const COOKIE_PREFIX = 'zeitguys-accessibility-prefs_';
    const COOKIE_HIDE_ACCESSIBILITY_BAR = 'hide-accessibility-bar';
	const COOKIE_FONT_BASE_SIZE = 'font-base-size'; //The base font size value stored in a cookie
	const COOKIE_FONT_SCALED_SIZE = 'font-scaled-size'; //The scaled font size value stored in a cookie
	const COOKIE_HIGH_CONTRAST_MODE_CHECK = 'high-contrast-mode-check'; // Cookie that determines if high contrast mode is enabled or not.
	const COOKIE_EXPIRY_LENGTH = 30; // 30 days (good for a month!)

	const USE_COOKIE_CRUMBS = true; // requires in the zeitguys_cookiecrumbs

	private static $has_accessibility_bar = false; // Gets set so we can't have more than one AB on screen at a time!

	protected static $include_expand_collapse_buttons;
	protected static $expand_button_img_src;
	protected static $collapse_button_img_src;

	/**
	 * Display the close box for the accessibility bar
	 * @param string $image_uri
	 */
	public static function the_close_box( $image_uri = "" ){
		$default_image = "accessibility_close_box.png";

		if ( empty( $image_uri ) ){
			$image_uri = plugin_dir_path( __FILE__ ) . 'theme' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $default_image;
		}
		/** @TODO test to see whether the provided image uri is valid **/

		$hide_text = __( 'Hide', self::TD );

		echo '<img class="accessibility-bar-close-box" src="' . $image_uri . '" alt="' . $hide_text . '" />';
	}

	/**
	 * Display the expand and collapse buttons. If the $force_display argument is omitted, ZG_Accessibility_Bar will decide whether to display these
	 * using the constant, which can be overridden with the 'zg_accessibility_bar-use_expand_collapse_buttons' filter.
	 *
	 * @param boolean $force_display Optional. If set to true, will force the display of the buttons, regardless of any filters or defaults.
	 */
	public static function the_expand_collapse_buttons( $force_display = false ){
		$display = $force_display;

		if ( ! $force_display ) $display = apply_filters( 'zg_accessibility_bar-use_expand_collapse_buttons', static::$include_expand_collapse_buttons );

		if ( $display ){
			static::the_expand_button();
			static::the_collapse_button();
		}
	}

	/**
	 * Display the expand button for the accessibility bar
	 */
	public static function the_expand_button(){
		$content = __( 'Accessibility', ZG_Accessibility_Bar::TD );
		$class = static::the_collapsed_class( false );

		if ( ! empty( static::$expand_button_img_src ) ) {
			$content = "<img src='" . static::$expand_button_img_src . "' />";
			$class = preg_replace( '/\'$/', ' has-image\'', $class );
		}

		echo '<div id="accessibility-bar-expand"' . $class . '><a href="' . add_query_arg( false, false ) . '" title="' . __( 'Expand accessibility bar', ZG_Accessibility_Bar::TD ) . '">' . $content . '</a></div>';
	}

	/**
	 * Display the hide button for the accessibility bar
	 */
	public static function the_collapse_button(){
		$content = __( 'Hide', ZG_Accessibility_Bar::TD );
		$class = static::the_collapsed_class( false );

		if ( ! empty( static::$collapse_button_img_src ) ) {
			$content = "<img src='" . static::$collapse_button_img_src . "' />";
			$class = preg_replace( '/\'$/', ' has-image\'', $class );
		}
		
		echo '<div id="accessibility-bar-collapse"' . $class . '><a href="' . add_query_arg( false, false ) . '" title="' . __( 'Collapse accessibility bar', ZG_Accessibility_Bar::TD ) . '">' . $content . '</a></div>';
	}
	
	/**
	 * Optionality echo/return the collapsed class attribute, if the
	 * accessibility bar is collapsed.
	 * 
	 * @param bool $echo
	 * @return string
	 */
	public static function the_collapsed_class( $echo = true ){
		$class = static::is_accessibility_bar_closed() ? " class='" . static::COLLAPSED_CLASS . "'" : "";
		if ( $echo ) echo $class;
		return $class;
	}

	/**
	 *
	 * @param array $args {
	 * An array of arguments that initialize the way th accessibility bar works. Optional.
	 *
	 * @type string $expand_button_img_src Default none. Valid absolute URI to an image icon for the expand button
	 * @type string $collapse_button_img_src Defalt none. URI to the collapse button
	 * @type boolean $include_expand_collapse_buttons Default true. Whether to include the expand and collapse buttons within the accessibilityBar. Set to false if you want to define the buttons somewhere else in your template.
	 */
	public static function setDefaults( $args = array() ){
		$args = wp_parse_args( $args, array(
			'include_expand_collapse_buttons' => static::USE_EXPAND_COLLAPSE_BUTTONS
		) );
		foreach ( $args as $arg => $value ){
			if ( property_exists( __CLASS__, $arg ) ) static::${$arg} = $value;
		}
	}
	
	/**
	 * Utility function to check whether the acessibility bar is closed or not
	 * @return bool
	 */
	public static function is_accessibility_bar_closed(){
		$bar_closed_cookie_name = self::COOKIE_PREFIX . self::COOKIE_HIDE_ACCESSIBILITY_BAR;
		return isset( $_COOKIE[$bar_closed_cookie_name] ) ? $_COOKIE[$bar_closed_cookie_name] == 'true' : self::BAR_INITIALLY_CLOSED;
	}

	public function __construct( $args = array() ){
		self::setDefaults( $args );

		// Checking for High Contrast Cookie.
		add_filter( 'body_class', array( $this, 'constrast_mode_check' ) , 1, 10 );
		//print_r( $_COOKIE[ static::COOKIE_PREFIX . static::COOKIE_HIGH_CONTRAST_MODE_CHECK ] );
				
		// Localize. Currently configured for mu-plugins
		$locale = apply_filters( 'plugin_locale', get_locale(), self::PLUGIN_NAME );
		$mofile = plugin_dir_path( __FILE__ ) . "lang/{$locale}.mo";
		load_textdomain( self::TD, $mofile );

		// There are multiple ways we can load in the accessibility bar.
		add_action( 'accessibility_bar', array( $this, 'do_accessibility_bar' ) );
		add_action( 'before', array( $this, 'do_accessibility_bar' ) ); // _s and derivatives

		// Set these up as actions, which we call from within the 'init' hook to allow plugin and theme authors to over-ride if necessary
		add_action( 'wp_enqueue_scripts', array( $this, 'setup_enqueue_scripts' ) );
		/** @TODO What is the best way to allow devs to easily remove our styles and scripts and replace with their own? **/

		// Print out stylesheets to prevent flicker on cookie close of the accessibility bar.
		add_action( 'wp_print_styles', array( $this, 'print_styles' ), 100 );

		// Do we load in the cookiecrumbs?
		if ( static::USE_COOKIE_CRUMBS && ! is_admin() ){
			add_action( 'plugins_loaded', array( $this, 'load_cookie_crumbs_plugin' ) );
		}
	}
	
	/**
	 * Checking for High Contrast Cookie. Adds a body class when Cookie is set to 'on'
	 * 
	 * @used-by add_filter( 'body_class' )
	 * @param array $classes
	 * @return array
	 */	
	function constrast_mode_check( $classes = array(), $class ) {
		if ( isset( $_COOKIE[ static::COOKIE_PREFIX . static::COOKIE_HIGH_CONTRAST_MODE_CHECK ] ) &&
			 $_COOKIE[ static::COOKIE_PREFIX . static::COOKIE_HIGH_CONTRAST_MODE_CHECK ] == 'on' 
		) {
			$classes[] = 'high-contrast-mode';
		}
		return $classes;
	}	
	
	/**
	 * Enqueue Accesibility front end scripts, and localize some values
	 * from PHP to use in the JS file.
	 */
	public function setup_enqueue_scripts(){
		// Jquery Cookie Plugin
		wp_enqueue_script( 'jquery-cookie', plugin_dir_url( __FILE__ ) . 'js/jquery.cookie.js', array( 'jquery' ) );
		// Accessibility Bar Javascript
		wp_enqueue_script( 'accessibility-bar', plugin_dir_url( __FILE__ ) . 'js/accessibility-bar.js', array( 'jquery', 'jquery-cookie' ) );
		// CamanJS Image Manipulation Library for High Contrast Mode
		wp_enqueue_script( 'caman-js', plugin_dir_url( __FILE__ ) . 'js/caman.full.min.js', array() );
		
		// The Accessibility Bar's CSS
		wp_enqueue_style ( 'accessibility-bar', plugin_dir_url( __FILE__ ) . 'css/accessibility-bar.css' );
		// The default high contrast mode CSS file
		wp_enqueue_style ( 'high-contrast-mode', plugin_dir_url( __FILE__ ) . 'css/high-constrast-mode.css' );
		// Localized Scripts to be used in accessibility-bar.js listed above
		wp_localize_script( 'accessibility-bar', 'ZG_ACCESSIBILITY_BAR', array(
			'BAR_INITIALLY_CLOSED' => apply_filters( 'zg_accessibility_bar-initially_closed', static::BAR_INITIALLY_CLOSED ),
			'COOKIE_HIDE_ACCESSIBILITY_BAR' => static::COOKIE_PREFIX . static::COOKIE_HIDE_ACCESSIBILITY_BAR,
			'COOKIE_FONT_BASE_SIZE' => static::COOKIE_PREFIX . static::COOKIE_FONT_BASE_SIZE,
			'COOKIE_FONT_SCALED_SIZE' => static::COOKIE_PREFIX . static::COOKIE_FONT_SCALED_SIZE,
			'COOKIE_EXPIRY_LENGTH' => static::COOKIE_EXPIRY_LENGTH,
			'COOKIE_HIGH_CONTRAST_MODE' => static::COOKIE_PREFIX . static::COOKIE_HIGH_CONTRAST_MODE_CHECK,
			'HIGH_CONTRAST_BG_COLOR' => '#FFFFFF', // Used in grayscaled BG images, currently in development
			'FONT_MAX_SIZE' => static::FONT_MAX_SIZE,
			'FONT_MIN_SIZE' => static::FONT_MIN_SIZE,
			'FONT_SCALE_FACTOR' => static::FONT_SCALE_FACTOR,
			'FONT_SCALE_INCREMENT' => static::FONT_SCALE_INCREMENT,
			'COLLAPSED_CLASS' => static::COLLAPSED_CLASS
		) );
	}

	/**
	 * Responsible for printing out <style> rules based on cookie settings
	 */
	public function print_styles(){
		$base_size_cookie_name = static::COOKIE_PREFIX . static::COOKIE_FONT_BASE_SIZE;
		$scaled_size_cookie_name = static::COOKIE_PREFIX . static::COOKIE_FONT_SCALED_SIZE;

		$font_base_size = isset( $_COOKIE[$base_size_cookie_name] ) ? $_COOKIE[$base_size_cookie_name] : self::FONT_MIN_SIZE;
		$font_scaled_size = isset( $_COOKIE[$scaled_size_cookie_name] ) ? $_COOKIE[$scaled_size_cookie_name] : self::FONT_MIN_SIZE;

		echo <<<PRINT_STYLES
			<style type="text/css">
				.resizeMax {
					font-size: {$font_base_size}em !important;
				}

				.resizeMin {
					font-size: {$font_scaled_size}em !important;
				}
			</style>
PRINT_STYLES;
	}

	/**
	 * Public static method, which can be used to include the accessibility bar, if you don't want to use an action (why?)
	 */
	public static function the_accessibility_bar(){
		static::do_accessibility_bar();
	}

	/**
	 * Action callback. Outputs the Accessibility Bar HTML. Generally, this action should be called from within your header.php file,
	 * <em>after</em> the <code>body</code> tag, often within the <code>header</code>.
	 *
	 * We hide this behind an action, so that if the plugin is ever disabled or deactivated, the theme code won't break.
	 *
	 * @TODO Think of a way to let the plugin auto-insert the accessibility bar by default, or allow it to be overridden by
	 * the theme developer.
	 *
	 * @used-by add_action( 'accessibility_bar' )
	 */
	public function do_accessibility_bar(){
		if ( ! self::$has_accessibility_bar ){
			// Locate the accessibility_bar.php file. Look in the child theme, the parent theme, and then fall back to ours.
			$template_name = 'accessibility_bar.php';

			if ( file_exists( get_stylesheet_directory() . DIRECTORY_SEPARATOR . $template_name)) {
				$located = get_stylesheet_directory . DIRECTORY_SEPARATOR . $template_name;
			} else if ( file_exists( get_template_directory() . DIRECTORY_SEPARATOR . $template_name) ) {
				$located = get_template_directory() . DIRECTORY_SEPARATOR . $template_name;
			} else {
				$located = plugin_dir_path( __FILE__ ) . 'theme' . DIRECTORY_SEPARATOR . $template_name;
			}

			// let's pass some vars in to load_template via $query_vars
			global $wp_query;
			$wp_query->set( "collapsed_class", static::the_collapsed_class( false ) );

			load_template( $located, true );
			self::$has_accessibility_bar = true;
		}
	}

	/**
	 * Check to see whether any plugin has defined 'the_cookie_crumb()'.
	 * If not, use ours.
	 *
	 * @used-by add_action( 'plugins_loaded' )
	 */
	public function load_cookie_crumbs_plugin(){
		if ( ! function_exists( 'the_cookie_crumb' ) ){
			require_once( plugin_dir_path( __FILE__ ) . '/include/zeitguys_cookiecrumbs.php' );
		}
	}
}
// Self initialize the "ZG_Accessibility_Bar" class
$zg_accessibility_bar_plugin = new ZG_Accessibility_Bar( array(
	'include_expand_collapse_buttons' => true
) );