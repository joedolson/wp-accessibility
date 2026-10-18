<?php
/**
 * Output the WP Accessibility Help Screen.
 *
 * @category Settings
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2
 * @link     https://www.joedolson.com/wp-accessibility/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Help screen.
 */
function wpa_help_screen() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'WP Accessibility Help', 'wp-accessibility' ); ?></h1>
		<div class="wpa-settings-wrapper">
			<div id="wpa_settings_page" class="postbox-container">
				<div class="metabox-holder">
					<div class="ui-sortable meta-box-sortables">
						<div class="postbox" id="get-support">
							<h2 class='hndle'><?php esc_html_e( 'Get Plug-in Support', 'wp-accessibility' ); ?></h2>

							<div class="inside">
							<div class='wpa-support-me promotion'>
								<p>
								<?php
								// Translators: URL to donate.
								echo wp_kses_post( sprintf( __( 'Please, consider <a href="%s">making a donation</a> to support WP Accessibility!', 'wp-accessibility' ), 'https://www.joedolson.com/donate/' ) );
								?>
								</p>
							</div>
							<?php wpa_get_support_form(); ?>
							</div>
						</div>
					</div>
					<div class="postbox">
						<h2 class='hndle'><?php esc_html_e( 'Customization', 'wp-accessibility' ); ?></h2>

						<div class="inside">
							<p>
								<?php echo wp_kses_post( __( 'Custom high-contrast styles go in <code>a11y-contrast.css</code> in your Theme\'s stylesheet directory.', 'wp-accessibility' ) ); ?>
							</p>
							<p>
								<?php echo wp_kses_post( __( 'Set custom styles for large print using the body class <code>.fontsize</code> in your theme styles or the customizer.', 'wp-accessibility' ) ); ?>
							</p>
							<p>
								<?php echo wp_kses_post( __( 'Set a custom long description template by adding <code>longdesc-template.php</code> to your theme directory.', 'wp-accessibility' ) ); ?>
							</p>
							<p>
								<?php echo wp_kses_post( __( 'The <a href="#wpa_widget_toolbar">shortcode for the Accessibility toolbar</a> is <code>[wpa_toolbar]</code>', 'wp-accessibility' ) ); ?>
							</p>
						</div>
					</div>
					<div class="postbox" id="privacy">
						<h2 class='hndle'><?php esc_html_e( 'Privacy', 'wp-accessibility' ); ?></h2>

						<div class="inside">
							<h3><?php esc_html_e( 'Cookies', 'wp-accessibility' ); ?></h3>
							<p><?php esc_html_e( 'The accessibility toolbar sets cookies to maintain awareness of the user\'s selected accessibility options. If the toolbar is not in use, WP Accessibility does not set any cookies.', 'wp-accessibility' ); ?></p>
							<h3><?php esc_html_e( 'Information collected by WP Accessibility', 'wp-accessibility' ); ?></h3>
							<p><?php esc_html_e( 'WP Accessibility does not collect any personally identifying information about users or visitors.', 'wp-accessibility' ); ?>
							<?php esc_html_e( 'User statistics (toolbar actions) collected by WP Accessibility are tracked using browser fingerprinting, and no identifiable information is stored at any time.', 'wp-accessibility' ); ?>
							<?php esc_html_e( 'Page statistics are collected when the page is initially viewed, then again if the accessibility values have changed since the last check.', 'wp-accessibility' ); ?></p>
						</div>
					</div>
				</div>
			</div>
			<?php wpa_admin_sidebar(); ?>
		</div>
	</div>
	<?php
}
