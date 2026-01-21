<?php
/**
 * Output the WP Accessibility Overlay Settings.
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
 * Update WP Accessibility settings.
 *
 * @return string Update confirmation message.
 */
function wpa_update_overlay_settings() {
	wpa_check_version();
	if ( ! empty( $_POST ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpa-nonce' ) ) {
			wp_die( 'WP Accessibility: Security check failed' );
		}

		if ( isset( $_POST['action'] ) && 'misc' === $_POST['action'] ) {
			$wpa_target        = ( isset( $_POST['wpa_target'] ) ) ? 'on' : '';
			$wpa_search        = ( isset( $_POST['wpa_search'] ) ) ? 'on' : '';
			$wpa_tabindex      = ( isset( $_POST['wpa_tabindex'] ) ) ? 'on' : '';
			$wpa_labels        = ( isset( $_POST['wpa_labels'] ) ) ? 'on' : 'off';
			$wpa_remove_titles = ( isset( $_POST['wpa_remove_titles'] ) ) ? 'on' : 'off';
			$wpa_viewport      = ( isset( $_POST['wpa_viewport'] ) ) ? 'on' : 'off';
			$wpa_underline     = ( isset( $_POST['wpa_underline'] ) ) ? 'on' : '';
			$wpa_videos        = ( isset( $_POST['wpa_videos'] ) ) ? 'on' : '';
			$wpa_more          = ( isset( $_POST['wpa_more'] ) ) ? 'on' : '';
			$wpa_focus         = ( isset( $_POST['wpa_focus'] ) ) ? 'on' : '';
			$wpa_focus_color   = ( isset( $_POST['wpa_focus_color'] ) ) ? str_replace( '#', '', $_POST['wpa_focus_color'] ) : '';
			$wpa_continue      = ( isset( $_POST['wpa_continue'] ) ) ? sanitize_text_field( $_POST['wpa_continue'] ) : __( 'Continue Reading', 'wp-accessibility' );
			if ( isset( $_POST['wpa_toggle_all'] ) && 'true' === $_POST['wpa_toggle_all'] ) {
				$wpa_target        = '';
				$wpa_search        = '';
				$wpa_tabindex      = '';
				$wpa_labels        = 'off';
				$wpa_remove_titles = 'off';
				$wpa_viewport      = 'off';
				$wpa_underline     = '';
				$wpa_videos        = '';
				$wpa_more          = '';
				$wpa_focus         = '';
				$wpa_focus_color   = '';
				$wpa_continue      = '';
			}
			update_option( 'wpa_target', $wpa_target );
			update_option( 'wpa_search', $wpa_search );
			update_option( 'wpa_tabindex', $wpa_tabindex );
			update_option( 'wpa_viewport', $wpa_viewport );
			update_option( 'wpa_labels', $wpa_labels );
			update_option( 'wpa_remove_titles', $wpa_remove_titles );
			update_option( 'wpa_underline', $wpa_underline );
			update_option( 'wpa_videos', $wpa_videos );
			update_option( 'wpa_more', $wpa_more );
			update_option( 'wpa_focus', $wpa_focus );
			update_option( 'wpa_focus_color', $wpa_focus_color );
			update_option( 'wpa_continue', $wpa_continue );
			$message = __( 'Miscellaneous Accessibility Settings Updated', 'wp-accessibility' );

			return "<div class='notice notice-success'><p>" . $message . '</p></div>';
		}
	} else {
		return;
	}
}

/**
 * Display settings admin page.
 */
function wpa_admin_overlay_settings() {
	echo wpa_update_overlay_settings();
	?>
	<div class="wrap">
		<h1><?php _e( 'WP Accessibility Fixes', 'wp-accessibility' ); ?></h1>
		<p>
			<?php esc_html_e( 'WP Accessibility contains features that remediate potential accessibility violations on your site. Enable these features to fix existing errors. Fixed errors are logged in statistics, when enabled.', 'wp-accessibility' ); ?>
		</p>
		<div class="wpa-settings-wrapper">
			<div id="wpa_settings_page" class="postbox-container">
				<div class="metabox-holder">
					<div class="ui-sortable meta-box-sortables">
						<div class="postbox">
							<h2 id="accessibility-settings" class='hndle'><?php _e( 'Accessibility Fixes', 'wp-accessibility' ); ?></h2>

							<div class="inside">
								<p>
									<?php
									// translators: link to accessibility fix documentation.
									printf( __( 'Settings that <a href="%s">fix potential accessibility issues</a> on your site.', 'wp-accessibility' ), 'https://docs.joedolson.com/wp-accessibility/category/remediation/' );
									?>
								</p>
								<form method="post" action="<?php echo admin_url( 'admin.php?page=wp-accessibility-overlay' ); ?>">
									<p class="wpa-toggle-all">
										<input type="checkbox" name="wpa_toggle_all" id="wpa_toggle_all" value="true"> <label for="wpa_toggle_all"><?php esc_html_e( 'Disable all automated accessibility fixes', 'wp-accessibility' ); ?></label>
									</p>
									<hr>
									<?php
									if ( ! wpa_accessible_theme() ) {
										?>
									<p>
										<input type="checkbox" id="wpa_more" name="wpa_more" <?php checked( get_option( 'wpa_more' ), 'on' ); ?>/>
										<label for="wpa_more"><?php _e( 'Add post title to "more" links.', 'wp-accessibility' ); ?></label>
									</p>
									<p>
										<label for="wpa_continue"><?php _e( 'Continue reading prefix text', 'wp-accessibility' ); ?></label><br />
										<input type="text" id="wpa_continue" name="wpa_continue" value="<?php echo esc_attr( get_option( 'wpa_continue', __( 'Continue Reading', 'wp-accessibility' ) ) ); ?>"/>
									</p>
										<?php
									} else {
										?>
										<div class="notice notice-info"><p><?php _e( '<strong>Three disabled features:</strong> Site language, continue reading text and standard form labels are provided in your <code>accessibility-ready</code> theme.', 'wp-accessibility' ); ?></p></div>
										<?php
									}
									?>
									<p>
										<input type="checkbox" id="wpa_target" name="wpa_target" <?php checked( get_option( 'wpa_target' ), 'on' ); ?>/>
										<label for="wpa_target"><?php _e( 'Prevent links from opening in new windows', 'wp-accessibility' ); ?></label>
									</p>
									<p>
										<input type="checkbox" id="wpa_search" name="wpa_search" <?php checked( get_option( 'wpa_search' ), 'on' ); ?> aria-describedby="wpa-search-note" />
										<label for="wpa_search"><?php _e( 'Force search error on empty search submission', 'wp-accessibility' ); ?></label> <em id="wpa-search-note" class="wpa-note"><?php _e( 'Your theme must have a search.php template', 'wp-accessibility' ); ?></em>
									</p>
									<p>
										<input type="checkbox" id="wpa_tabindex" name="wpa_tabindex" <?php checked( get_option( 'wpa_tabindex' ), 'on' ); ?>/>
										<label for="wpa_tabindex"><?php _e( 'Remove tabindex from focusable elements', 'wp-accessibility' ); ?></label>
									</p>
									<p>
										<input type="checkbox" id="wpa_viewport" name="wpa_viewport" <?php checked( get_option( 'wpa_viewport', 'on' ), 'on' ); ?>/>
										<label for="wpa_viewport"><?php _e( 'Ensure that viewport does not restrict zoom', 'wp-accessibility' ); ?></label>
									</p>
									<p>
										<input type="checkbox" id="wpa_labels" name="wpa_labels" <?php checked( get_option( 'wpa_labels', 'on' ), 'on' ); ?>/>
										<label for="wpa_labels"><?php _e( 'Add missing labels to search and comment forms', 'wp-accessibility' ); ?></label>
									</p>
									<p>
										<input type="checkbox" id="wpa_remove_titles" name="wpa_remove_titles" <?php checked( get_option( 'wpa_remove_titles', 'on' ), 'on' ); ?>/>
										<label for="wpa_remove_titles"><?php _e( 'Remove title attributes', 'wp-accessibility' ); ?></label>
									</p>
									<p>
										<input type="checkbox" id="wpa_videos" aria-describedby="wpa-videos-note" name="wpa_videos" <?php checked( get_option( 'wpa_videos' ), 'on' ); ?>/>
										<label for="wpa_videos"><?php _e( 'Insert play/pause button on autoplay videos', 'wp-accessibility' ); ?></label> <em id="wpa-underline-note" class="wpa-note"><?php _e( 'Only effects videos with <code>autoplay</code> enabled and <code>controls</code> disabled.', 'wp-accessibility' ); ?></em>
									</p>
									<p>
										<input type="checkbox" id="wpa_underline" aria-describedby="wpa-underline-note" name="wpa_underline" <?php checked( get_option( 'wpa_underline' ), 'on' ); ?>/>
										<label for="wpa_underline"><?php _e( 'Force underline on links', 'wp-accessibility' ); ?></label> <em id="wpa-underline-note" class="wpa-note"><?php _e( 'Excludes links inside <code>nav</code> elements.', 'wp-accessibility' ); ?></em>
									</p>
									<p>
										<input type="checkbox" id="wpa_focus" name="wpa_focus" <?php checked( get_option( 'wpa_focus' ), 'on' ); ?>/>
										<label for="wpa_focus"><?php _e( 'Add outline to elements on keyboard focus', 'wp-accessibility' ); ?></label>
									</p>
									<p>
										<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>" />
										<input type="hidden" name="action" value="misc" />
									</p>

									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Miscellaneous Settings', 'wp-accessibility' ); ?>"/></p>
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
