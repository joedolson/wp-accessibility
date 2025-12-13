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
		if ( isset( $_POST['action'] ) && 'rta' === $_POST['action'] ) {
			$rta_from_tag_clouds = ( isset( $_POST['rta_from_tag_clouds'] ) ) ? 'on' : '';
			update_option( 'rta_from_tag_clouds', $rta_from_tag_clouds );

			$message = __( 'Remove Title Attributes Settings Updated', 'wp-accessibility' );

			return "<div class='notice notice-success'><p>" . $message . '</p></div>';
		}
		if ( isset( $_POST['action'] ) && 'asl' === $_POST['action'] ) {
			$asl_enable         = ( isset( $_POST['asl_enable'] ) ) ? 'on' : '';
			$asl_content        = ( isset( $_POST['asl_content'] ) ) ? sanitize_text_field( $_POST['asl_content'] ) : '';
			$asl_navigation     = ( isset( $_POST['asl_navigation'] ) ) ? sanitize_text_field( $_POST['asl_navigation'] ) : '';
			$asl_sitemap        = ( isset( $_POST['asl_sitemap'] ) ) ? sanitize_text_field( $_POST['asl_sitemap'] ) : '';
			$asl_extra_target   = ( isset( $_POST['asl_extra_target'] ) ) ? sanitize_text_field( $_POST['asl_extra_target'] ) : '';
			$asl_extra_text     = ( isset( $_POST['asl_extra_text'] ) ) ? sanitize_text_field( $_POST['asl_extra_text'] ) : '';
			$asl_visible        = ( isset( $_POST['asl_visible'] ) ) ? 'on' : '';
			$asl_default_styles = ( isset( $_POST['asl_default_styles'] ) ) ? 'true' : '';
			$asl_styles         = ( isset( $_POST['asl_styles'] ) ) ? wp_filter_nohtml_kses( $_POST['asl_styles'] ) : '';
			update_option( 'asl_enable', $asl_enable );
			update_option( 'asl_content', $asl_content );
			update_option( 'asl_navigation', $asl_navigation );
			update_option( 'asl_sitemap', $asl_sitemap );
			update_option( 'asl_extra_target', $asl_extra_target );
			update_option( 'asl_extra_text', $asl_extra_text );
			update_option( 'asl_visible', $asl_visible );
			update_option( 'asl_default_styles', $asl_default_styles );
			$notice = ( 'asl' === $asl_visible ) ? '<p>' . __( 'WP Accessibility does not provide any styles for visible skiplinks. You can still set the look of the links using the textareas provided, but all other layout must be assigned in your theme.', 'wp-accessibility' ) . '</p>' : '';

			update_option( 'asl_styles', $asl_styles );
			$message = __( 'Skiplinks Settings Updated', 'wp-accessibility' );

			return "<div class='notice notice-success'><p>" . $message . "</p>$notice</div>";
		}

		if ( isset( $_POST['action'] ) && 'features' === $_POST['action'] ) {
			$wpa_show_alt          = ( isset( $_POST['wpa_show_alt'] ) ) ? 'on' : 'off';
			$wpa_longdesc          = ( isset( $_POST['wpa_longdesc'] ) ) ? sanitize_text_field( $_POST['wpa_longdesc'] ) : 'false';
			$wpa_longdesc_featured = ( isset( $_POST['wpa_longdesc_featured'] ) ) ? sanitize_text_field( $_POST['wpa_longdesc_featured'] ) : 'false';
			$wpa_post_types        = ( isset( $_POST['wpa_post_types'] ) ) ? map_deep( $_POST['wpa_post_types'], 'sanitize_text_field' ) : array();
			update_option( 'wpa_show_alt', $wpa_show_alt );
			update_option( 'wpa_longdesc', $wpa_longdesc );
			update_option( 'wpa_longdesc_featured', $wpa_longdesc_featured );
			update_option( 'wpa_post_types', $wpa_post_types );
			$message = __( 'Accessibility Features Updated', 'wp-accessibility' );

			return "<div class='notice notice-success'><p>" . $message . '</p></div>';
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

		if ( isset( $_POST['action'] ) && 'misc' === $_POST['action'] ) {
			$wpa_target        = ( isset( $_POST['wpa_target'] ) ) ? 'on' : '';
			$wpa_search        = ( isset( $_POST['wpa_search'] ) ) ? 'on' : '';
			$wpa_tabindex      = ( isset( $_POST['wpa_tabindex'] ) ) ? 'on' : '';
			$wpa_labels        = ( isset( $_POST['wpa_labels'] ) ) ? 'on' : 'off';
			$wpa_remove_titles = ( isset( $_POST['wpa_remove_titles'] ) ) ? 'on' : 'off';
			$wpa_underline     = ( isset( $_POST['wpa_underline'] ) ) ? 'on' : '';
			$wpa_videos        = ( isset( $_POST['wpa_videos'] ) ) ? 'on' : '';
			$wpa_more          = ( isset( $_POST['wpa_more'] ) ) ? 'on' : '';
			$wpa_focus         = ( isset( $_POST['wpa_focus'] ) ) ? 'on' : '';
			$wpa_focus_color   = ( isset( $_POST['wpa_focus_color'] ) ) ? str_replace( '#', '', $_POST['wpa_focus_color'] ) : '';
			$wpa_continue      = ( isset( $_POST['wpa_continue'] ) ) ? sanitize_text_field( $_POST['wpa_continue'] ) : __( 'Continue Reading', 'wp-accessibility' );
			update_option( 'wpa_target', $wpa_target );
			update_option( 'wpa_search', $wpa_search );
			update_option( 'wpa_tabindex', $wpa_tabindex );
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

		if ( isset( $_POST['action'] ) && 'toolbar' === $_POST['action'] ) {
			$wpa_toolbar            = ( isset( $_POST['wpa_toolbar'] ) ) ? 'on' : '';
			$wpa_toolbar_size       = ( isset( $_POST['wpa_toolbar_size'] ) ) ? sanitize_text_field( $_POST['wpa_toolbar_size'] ) : '';
			$wpa_alternate_fontsize = ( isset( $_POST['wpa_alternate_fontsize'] ) ) ? 'on' : '';
			$wpa_widget_toolbar     = ( isset( $_POST['wpa_widget_toolbar'] ) ) ? 'on' : '';
			$wpa_toolbar_gs         = ( isset( $_POST['wpa_toolbar_gs'] ) ) ? 'on' : 'off';
			$wpa_toolbar_fs         = ( isset( $_POST['wpa_toolbar_fs'] ) ) ? 'on' : 'off';
			$wpa_toolbar_ct         = ( isset( $_POST['wpa_toolbar_ct'] ) ) ? 'on' : 'off';
			$wpa_toolbar_default    = ( isset( $_POST['wpa_toolbar_default'] ) ) ? sanitize_text_field( $_POST['wpa_toolbar_default'] ) : '';
			$wpa_toolbar_right      = ( isset( $_POST['wpa_toolbar_right'] ) ) ? 'on' : '';
			$wpa_toolbar_mobile     = ( isset( $_POST['wpa_toolbar_mobile'] ) ) ? 'on' : '';
			update_option( 'wpa_toolbar', $wpa_toolbar );
			update_option( 'wpa_toolbar_size', $wpa_toolbar_size );
			update_option( 'wpa_alternate_fontsize', $wpa_alternate_fontsize );
			update_option( 'wpa_widget_toolbar', $wpa_widget_toolbar );
			update_option( 'wpa_toolbar_gs', $wpa_toolbar_gs );
			update_option( 'wpa_toolbar_fs', $wpa_toolbar_fs );
			update_option( 'wpa_toolbar_ct', $wpa_toolbar_ct );
			update_option( 'wpa_toolbar_default', $wpa_toolbar_default );
			update_option( 'wpa_toolbar_right', $wpa_toolbar_right );
			update_option( 'wpa_toolbar_mobile', $wpa_toolbar_mobile );
			$message = __( 'Toolbar Settings Updated', 'wp-accessibility' );

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
								<hr>
								<form method="post" action="<?php echo admin_url( 'admin.php?page=wp-accessibility-overlay' ); ?>">
									<ul>
										<?php
										if ( ! wpa_accessible_theme() ) {
											?>
										<li>
											<input type="checkbox" id="wpa_more" name="wpa_more" <?php checked( get_option( 'wpa_more' ), 'on' ); ?>/>
											<label for="wpa_more"><?php _e( 'Add post title to "more" links.', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<label for="wpa_continue"><?php _e( 'Continue reading prefix text', 'wp-accessibility' ); ?></label><br />
											<input type="text" id="wpa_continue" name="wpa_continue" value="<?php echo esc_attr( get_option( 'wpa_continue', __( 'Continue Reading', 'wp-accessibility' ) ) ); ?>"/>
										</li>
											<?php
										} else {
											?>
											<li><div class="notice notice-info"><p><?php _e( '<strong>Three disabled features:</strong> Site language, continue reading text and standard form labels are provided in your <code>accessibility-ready</code> theme.', 'wp-accessibility' ); ?></p></div></li>
											<?php
										}
										?>
										<li>
											<input type="checkbox" id="wpa_target" name="wpa_target" <?php checked( get_option( 'wpa_target' ), 'on' ); ?>/>
											<label for="wpa_target"><?php _e( 'Prevent links from opening in new windows', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_search" name="wpa_search" <?php checked( get_option( 'wpa_search' ), 'on' ); ?> aria-describedby="wpa-search-note" />
											<label for="wpa_search"><?php _e( 'Force search error on empty search submission', 'wp-accessibility' ); ?></label> <em id="wpa-search-note" class="wpa-note"><?php _e( 'Your theme must have a search.php template', 'wp-accessibility' ); ?></em>
										</li>
										<li>
											<input type="checkbox" id="wpa_tabindex" name="wpa_tabindex" <?php checked( get_option( 'wpa_tabindex' ), 'on' ); ?>/>
											<label for="wpa_tabindex"><?php _e( 'Remove tabindex from focusable elements', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_labels" name="wpa_labels" <?php checked( get_option( 'wpa_labels', 'on' ), 'on' ); ?>/>
											<label for="wpa_labels"><?php _e( 'Add missing labels to search and comment forms', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_remove_titles" name="wpa_remove_titles" <?php checked( get_option( 'wpa_remove_titles', 'on' ), 'on' ); ?>/>
											<label for="wpa_remove_titles"><?php _e( 'Remove title attributes', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_underline" aria-describedby="wpa-underline-note" name="wpa_underline" <?php checked( get_option( 'wpa_underline' ), 'on' ); ?>/>
											<label for="wpa_underline"><?php _e( 'Force underline on links', 'wp-accessibility' ); ?></label> <em id="wpa-underline-note" class="wpa-note"><?php _e( 'Excludes links inside <code>nav</code> elements.', 'wp-accessibility' ); ?></em>
										</li>
										<li>
											<input type="checkbox" id="wpa_videos" aria-describedby="wpa-videos-note" name="wpa_videos" <?php checked( get_option( 'wpa_videos' ), 'on' ); ?>/>
											<label for="wpa_videos"><?php _e( 'Insert play/pause button on autoplay videos', 'wp-accessibility' ); ?></label> <em id="wpa-underline-note" class="wpa-note"><?php _e( 'Only effects videos with <code>autoplay</code> enabled and <code>controls</code> disabled.', 'wp-accessibility' ); ?></em>
										</li>
										<li>
											<input type="checkbox" id="wpa_focus" name="wpa_focus" <?php checked( get_option( 'wpa_focus' ), 'on' ); ?>/>
											<label for="wpa_focus"><?php _e( 'Add outline to elements on keyboard focus', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<label for="wpa_focus_color"><?php _e( 'Outline color (hexadecimal, optional)', 'wp-accessibility' ); ?></label><br />
											<input type="text" id="wpa_focus_color" name="wpa_focus_color" value="#<?php echo esc_attr( str_replace( '#', '', get_option( 'wpa_focus_color' ) ) ); ?>"/>
										</li>
									</ul>
									<p>
										<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>" />
										<input type="hidden" name="action" value="misc" />
									</p>

									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Miscellaneous Settings', 'wp-accessibility' ); ?>"/></p>
								</form>
							</div>
						</div>
						<div class="postbox">
							<h2 class='hndle'><?php _e( 'Remove Title Attributes', 'wp-accessibility' ); ?></h2>

							<div class="inside">
								<?php wpa_accessible_theme(); ?>
								<form method="post" action="<?php echo admin_url( 'admin.php?page=wp-accessibility-overlay' ); ?>">
									<ul>
										<li><input type="checkbox" id="rta_from_tag_clouds" name="rta_from_tag_clouds" <?php checked( get_option( 'rta_from_tag_clouds' ), 'on' ); ?>/>
										<label for="rta_from_tag_clouds"><?php _e( 'Remove title attributes from:', 'wp-accessibility' ); ?> <?php _e( 'Tag clouds', 'wp-accessibility' ); ?></label>
										</li>
									</ul>
									<p>
										<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>" />
										<input type="hidden" name="action" value="rta" />
									</p>

									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Title Attribute Settings', 'wp-accessibility' ); ?>"/></p>
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
