<?php
/**
 * Output the WP Accessibility admin and author Settings.
 *
 * @category Settings
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv3
 * @link     https://www.joedolson.com/wp-accessibility/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update WP Accessibility admin & author settings.
 *
 * @return string Update confirmation message.
 */
function wpa_update_admin_settings() {
	wpa_check_version();
	if ( ! empty( $_POST ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpa-nonce' ) ) {
			wp_die( 'WP Accessibility: Security check failed' );
		}
		if ( isset( $_POST['action'] ) && 'tools' === $_POST['action'] ) {
			$wpa_search_alt         = ( isset( $_POST['wpa_search_alt'] ) ) ? 'on' : '';
			$wpa_diagnostics        = ( isset( $_POST['wpa_diagnostics'] ) ) ? 'on' : '';
			$wpa_disable_fullscreen = ( isset( $_POST['wpa_disable_fullscreen'] ) ) ? 'on' : '';
			$wpa_disable_file_embed = ( isset( $_POST['wpa_disable_file_embed'] ) ) ? 'on' : '';
			$wpa_allow_h1           = ( isset( $_POST['wpa_allow_h1'] ) ) ? 'on' : '';
			$wpa_disable_logout     = ( isset( $_POST['wpa_disable_logout'] ) ) ? 'on' : '';
			$wpa_track_stats        = ( isset( $_POST['wpa_track_stats'] ) ) ? sanitize_text_field( $_POST['wpa_track_stats'] ) : '';
			update_option( 'wpa_search_alt', $wpa_search_alt );
			update_option( 'wpa_diagnostics', $wpa_diagnostics );
			update_option( 'wpa_disable_fullscreen', $wpa_disable_fullscreen );
			update_option( 'wpa_disable_file_embed', $wpa_disable_file_embed );
			update_option( 'wpa_allow_h1', $wpa_allow_h1 );
			update_option( 'wpa_track_stats', $wpa_track_stats );
			update_option( 'wpa_disable_logout', $wpa_disable_logout );
			$message = __( 'Accessibility Tools Updated', 'wp-accessibility' );

			return "<div class='notice notice-success'><p>" . $message . '</p></div>';
		}
	} else {
		return;
	}
}

/**
 * Display settings admin page.
 */
function wpa_admin_admin_settings() {
	echo wpa_update_settings();
	?>
	<div class="wrap">
		<h1><?php _e( 'WP Accessibility Features', 'wp-accessibility' ); ?></h1>
		<p>
			<?php esc_html_e( 'WP Accessibility contains many features that enhance the accessibility of your site. Enable these features to improve access for users with disabilities.', 'wp-accessibility' ); ?>
		</p>
		<div class="wpa-settings-wrapper">
			<div id="wpa_settings_page" class="postbox-container">
				<div class="metabox-holder">
					<div class="ui-sortable meta-box-sortables">
						<div class="postbox">
							<h2 class="hndle"><?php _e( 'Testing & Admin Experience', 'wp-accessibility' ); ?></h2>

							<div class="inside">
								<p><?php _e( 'These change the admin experience or help with testing.', 'wp-accessibility' ); ?></p>
								<hr>
								<form method="post" action="<?php echo admin_url( 'admin.php?page=wp-accessibility' ); ?>">
									<ul>
										<li>
											<input type="checkbox" id="wpa_search_alt" name="wpa_search_alt" <?php checked( get_option( 'wpa_search_alt' ), 'on' ); ?> aria-describedby="wpa_search_alt_note" />
											<label for="wpa_search_alt"><?php _e( 'Include alt attribute in media library searches', 'wp-accessibility' ); ?></label> <em class="wpa-note" id="wpa_search_alt_note"><?php _e( '* May cause slow searches on large media libraries.', 'wp-accessibility' ); ?></em>
										</li>
										<li>
											<input type="checkbox" id="wpa_disable_logout" name="wpa_disable_logout" <?php checked( get_option( 'wpa_disable_logout' ), 'on' ); ?> aria-describedby="wpa_logout_note" />
											<label for="wpa_disable_logout"><?php _e( 'Disable top-level adminbar logout link', 'wp-accessibility' ); ?></label>  <em class="wpa-note" id="wpa_logout_note"><?php _e( '* Accessibility problems accessing adminbar dropdowns were fixed in WordPress 6.5.', 'wp-accessibility' ); ?></em>
										</li>
										<li>
											<input type="checkbox" id="wpa_disable_fullscreen" name="wpa_disable_fullscreen" <?php checked( get_option( 'wpa_disable_fullscreen' ), 'on' ); ?>/>
											<label for="wpa_disable_fullscreen"><?php _e( 'Disable fullscreen block editor by default', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_disable_file_embed" name="wpa_disable_file_embed" <?php checked( get_option( 'wpa_disable_file_embed' ), 'on' ); ?>/>
											<label for="wpa_disable_file_embed"><?php _e( 'Disable embed behavior as default on file block', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_allow_h1" name="wpa_allow_h1" <?php checked( get_option( 'wpa_allow_h1' ), 'on' ); ?>/>
											<label for="wpa_allow_h1"><?php _e( 'Allow <code>h1</code> in the headings block', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_diagnostics" name="wpa_diagnostics" <?php checked( get_option( 'wpa_diagnostics' ), 'on' ); ?>/>
											<label for="wpa_diagnostics"><?php _e( 'Enable diagnostic CSS', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<fieldset>
												<legend><?php _e( 'Statistics Tracking', 'wp-accessibility' ); ?></legend>
												<ul>
													<li>
														<input type="radio" id="wpa_track_stats_none" value="off" name="wpa_track_stats" <?php checked( get_option( 'wpa_track_stats' ), 'off' ); ?>/>
														<label for="wpa_track_stats_none"><?php _e( 'Disabled', 'wp-accessibility' ); ?></label>
													</li>
													<li>
														<input type="radio" id="wpa_track_stats_all" value="all" name="wpa_track_stats" <?php checked( get_option( 'wpa_track_stats' ), 'all' ); ?>/>
														<label for="wpa_track_stats_all"><?php _e( 'All Visitors', 'wp-accessibility' ); ?></label>
													</li>
													<li>
														<input type="radio" id="wpa_track_stats_admin" value="" name="wpa_track_stats" <?php checked( get_option( 'wpa_track_stats' ), '' ); ?>/>
														<label for="wpa_track_stats_admin"><?php _e( 'Site Administrators', 'wp-accessibility' ); ?></label>
													</li>
												</ul>
											</fieldset>
										</li>
									</ul>
									<p>
										<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>" />
										<input type="hidden" name="action" value="tools" />
									</p>
									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Accessibility Tools', 'wp-accessibility' ); ?>"/></p>
								</form>
							</div>
						</div>
						<div class="postbox">
							<h2 class='hndle'><?php _e( 'Color Contrast Tester', 'wp-accessibility' ); ?></h2>

							<div class="inside">
								<?php
								$colors = wpa_contrast();
								if ( $colors ) {
									$luminance_raw = wpa_luminosity( $colors['red1'], $colors['red2'], $colors['green1'], $colors['green2'], $colors['blue1'], $colors['blue2'] );
									$l_contrast    = $luminance_raw . ':1';
									$hex1          = esc_attr( $colors['hex1'] );
									$hex2          = esc_attr( $colors['hex2'] );
								} else {
									$hex1       = '';
									$hex2       = '';
									$l_contrast = false;
								}
								if ( $l_contrast ) {
									$results = "<div class='notice notice-info'>";
									// Translators: Contrast ratio, foreground Hex color, background Hex color.
									$results .= '<h2 class="stats wcag2">' . sprintf( __( 'Luminosity Contrast Ratio for %2$s and %3$s is %1$s (Threshold: greater than 7:1 for AAA, 4.5:1 for AA)', 'wp-accessibility' ), '<strong>' . $l_contrast . '</strong>', '<code>#' . $hex1 . '</code>', '<code>#' . $hex2 . '</code>' ) . '</h2><p>';
									if ( $luminance_raw >= 7 ) {
										$results .= __( 'The colors compared <strong>pass</strong> the relative luminosity test at level AAA.', 'wp-accessibility' );
									}
									if ( $luminance_raw >= 4.5 && $luminance_raw < 7 ) {
										$results .= __( 'The colors compared <strong>pass</strong> the relative luminosity test at level AA.', 'wp-accessibility' );
									}
									if ( $luminance_raw >= 3 && $luminance_raw < 4.5 ) {
										$results .= __( 'The colors compared pass the relative luminosity test <strong>only when used in large print</strong> situations (greater than 18pt (24px) text or 14pt (18.66px) bold text.)', 'wp-accessibility' );
									}
									if ( $luminance_raw < 3 ) {
										$results .= __( 'The colors compared <strong>do not pass</strong> the relative luminosity test.', 'wp-accessibility' );
									}
									$results .= " <a href='#contrast'>" . __( 'Test another set of colors', 'wp-accessibility' ) . '</a>';
									$results .= '</p>';
									$results .= "
							<div class=\"views\">
								<p class='large' style=\"color: #$hex1;background: #$hex2\">Large Text (24px)</p>
								<p class='small' style=\"color: #$hex1;background: #$hex2\">Standard Text (18px)</p>
								<p class='large' style=\"color: #$hex2;background: #$hex1\">Large Text (24px) (Inverted)</p>
								<p class='small' style=\"color: #$hex2;background: #$hex1\">Standard Text (18px) (Inverted)</p>
							</div>
						</div>";
									echo $results;
								}
								?>
								<form method="get" id="contrast" action="<?php echo admin_url( 'admin.php?page=wp-accessibility' ); ?>">
									<fieldset>
										<legend><?php _e( 'Test of relative luminosity', 'wp-accessibility' ); ?></legend>
										<ul id="contrast-tester">
											<li class='fore'>
												<div id="fore"></div>
												<label for="color1"><?php _e( 'Foreground color', 'wp-accessibility' ); ?></label><br/>
												<input type="text" class="wpa-color-input" name="color" value="#<?php echo esc_attr( $hex1 ); ?>" size="34" id="color1" />
											</li>
											<li class='back'>
												<div id="back"></div>
												<label for="color2"><?php _e( 'Background color', 'wp-accessibility' ); ?></label><br/>
												<input type="text" class="wpa-color-input" name="color2" value="#<?php echo esc_attr( $hex2 ); ?>" size="34" id="color2" />
											</li>
										</ul>
									</fieldset>
									<p>
										<input type="hidden" name="action" value="contrast" />
										<input type="hidden" name="page" value="wp-accessibility" />
									</p>

									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Check Color Contrast', 'wp-accessibility' ); ?>"/></p>
								</form>
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
