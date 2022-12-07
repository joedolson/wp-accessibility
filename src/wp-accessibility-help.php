<?php
/**
 * Output the WP Accessibility Help Screen.
 *
 * @category Settings
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2 or later
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
		<h1><?php _e( 'WP Accessibility Help', 'wp-accessibility' ); ?></h1>
		<div class="wpa-settings-wrapper">
			<div id="wpa_settings_page" class="postbox-container">
				<div class="metabox-holder">
					<div class="ui-sortable meta-box-sortables">
						<div class="postbox" id="get-support">
							<h2 class='hndle'><?php _e( 'Get Plug-in Support', 'wp-accessibility' ); ?></h2>

							<div class="inside">
							<div class='wpa-support-me promotion'>
								<p>
								<?php
								// Translators: URL to donate.
								printf( __( 'Please, consider <a href="%s">making a donation</a> to support WP Accessibility!', 'wp-accessibility' ), 'https://www.joedolson.com/donate/' );
								?>
								</p>
							</div>
							<?php wpa_get_support_form(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php wpa_admin_sidebar(); ?>
		</div>
	</div>
	<?php
}
