<?php
/**
 * Output the WP Accessibility Settings.
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

add_action( 'admin_enqueue_scripts', 'wpa_admin_styles' );
/**
 * Enqueue admin stylesheets.
 */
function wpa_admin_styles() {
	$screen        = get_current_screen();
	$is_stats_type = ( 'wpa-stats' === $screen->id || 'edit-wpa-stats' === $screen->id ) ? true : false;
	if ( $is_stats_type || 'dashboard' === $screen->base || ( isset( $_GET['page'] ) && ( 'wp-accessibility' === $_GET['page'] || 'wp-accessibility-help' === $_GET['page'] ) ) ) {
		$version = wpa_check_version();
		if ( WP_DEBUG ) {
			$version = $version . '-' . wp_rand( 10000, 50000 );
		}
		wp_register_style( 'ui-font', plugins_url( 'toolbar/fonts/css/a11y-toolbar.css', __FILE__ ), array(), $version );
		wp_enqueue_style( 'ui-font' );

		wp_enqueue_style( 'wpa-styles', plugins_url( 'css/wpa-styles.css', __FILE__ ), array( 'farbtastic' ), $version );
		wp_enqueue_style( 'wp-color-picker' );
		// Enqueue WP Accessibility admin scripts.
		wp_enqueue_script( 'wpa-admin', plugins_url( 'js/wpa-admin.js', __FILE__ ), array( 'wp-color-picker' ), $version, true );
	}
}

/**
 * Update WP Accessibility settings.
 *
 * @return string Update confirmation message.
 */
function wpa_update_settings() {
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

			return "<div class='updated'><p>" . $message . '</p></div>';
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

			return "<div class='updated'><p>" . $message . "</p>$notice</div>";
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

			return "<div class='updated'><p>" . $message . '</p></div>';
		}

		if ( isset( $_POST['action'] ) && 'tools' === $_POST['action'] ) {
			$wpa_search_alt         = ( isset( $_POST['wpa_search_alt'] ) ) ? 'on' : '';
			$wpa_diagnostics        = ( isset( $_POST['wpa_diagnostics'] ) ) ? 'on' : '';
			$wpa_disable_fullscreen = ( isset( $_POST['wpa_disable_fullscreen'] ) ) ? 'on' : '';
			$wpa_track_stats        = ( isset( $_POST['wpa_track_stats'] ) ) ? sanitize_text_field( $_POST['wpa_track_stats'] ) : '';
			update_option( 'wpa_search_alt', $wpa_search_alt );
			update_option( 'wpa_diagnostics', $wpa_diagnostics );
			update_option( 'wpa_disable_fullscreen', $wpa_disable_fullscreen );
			update_option( 'wpa_track_stats', $wpa_track_stats );
			$message = __( 'Accessibility Tools Updated', 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . '</p></div>';
		}

		if ( isset( $_POST['action'] ) && 'misc' === $_POST['action'] ) {
			$wpa_target      = ( isset( $_POST['wpa_target'] ) ) ? 'on' : '';
			$wpa_search      = ( isset( $_POST['wpa_search'] ) ) ? 'on' : '';
			$wpa_tabindex    = ( isset( $_POST['wpa_tabindex'] ) ) ? 'on' : '';
			$wpa_underline   = ( isset( $_POST['wpa_underline'] ) ) ? 'on' : '';
			$wpa_more        = ( isset( $_POST['wpa_more'] ) ) ? 'on' : '';
			$wpa_focus       = ( isset( $_POST['wpa_focus'] ) ) ? 'on' : '';
			$wpa_focus_color = ( isset( $_POST['wpa_focus_color'] ) ) ? str_replace( '#', '', $_POST['wpa_focus_color'] ) : '';
			$wpa_continue    = ( isset( $_POST['wpa_continue'] ) ) ? sanitize_text_field( $_POST['wpa_continue'] ) : __( 'Continue Reading', 'wp-accessibility' );
			update_option( 'wpa_target', $wpa_target );
			update_option( 'wpa_search', $wpa_search );
			update_option( 'wpa_tabindex', $wpa_tabindex );
			update_option( 'wpa_underline', $wpa_underline );
			update_option( 'wpa_more', $wpa_more );
			update_option( 'wpa_focus', $wpa_focus );
			update_option( 'wpa_focus_color', $wpa_focus_color );
			update_option( 'wpa_continue', $wpa_continue );
			$message = __( 'Miscellaneous Accessibility Settings Updated', 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . '</p></div>';
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

			return "<div class='updated'><p>" . $message . '</p></div>';
		}
	} else {
		return;
	}
}

/**
 * Display settings admin page.
 */
function wpa_admin_settings() {
	echo wpa_update_settings();
	?>
	<div class="wrap">
		<h1><?php _e( 'WP Accessibility Settings', 'wp-accessibility' ); ?></h1>
		<div class="wpa-settings-wrapper">
			<div id="wpa_settings_page" class="postbox-container">
				<div class="metabox-holder">
					<div class="ui-sortable meta-box-sortables">
						<div class="postbox">
							<h2 class='hndle'><?php _e( 'Add Skiplinks', 'wp-accessibility' ); ?></h2>

							<div class="inside">
								<?php
								if ( wpa_accessible_theme() && 'on' !== get_option( 'asl_enable' ) ) {
									?>
								<p>
									<?php _e( 'Your <code>accessibility-ready</code> theme has skip links built in.', 'wp-accessibility' ); ?>
								</p>
									<?php
								} else {
									?>
								<form method="post" action="<?php echo admin_url( 'admin.php?page=wp-accessibility' ); ?>">
									<fieldset>
										<legend><?php _e( 'Configure Skiplinks', 'wp-accessibility' ); ?></legend>
										<ul>
											<li>
												<input type="checkbox" id="asl_enable" name="asl_enable" <?php checked( get_option( 'asl_enable' ), 'on' ); ?>/>
												<label for="asl_enable"><?php _e( 'Enable Skiplinks', 'wp-accessibility' ); ?></label>
											</li>
											<li>
												<input type="checkbox" id="asl_visible" name="asl_visible" <?php checked( get_option( 'asl_visible' ), 'on' ); ?>/>
												<label for="asl_visible"><?php _e( 'Skiplinks always visible', 'wp-accessibility' ); ?></label>
											</li>
											<li>
												<label for="asl_content"><?php _e( 'Skip to Content link target', 'wp-accessibility' ); ?></label><br />
												<input type="text" id="asl_content" name="asl_content" placeholder="#" size="30" aria-describedby="asl_content_description" value="<?php echo esc_attr( get_option( 'asl_content' ) ); ?>"/> <span id="asl_content_description"><?php _e( 'ID attribute starting with <code>#</code>', 'wp-accessibility' ); ?></span>
											</li>
											<li>
												<label for="asl_navigation"><?php _e( 'Skip to Navigation link target', 'wp-accessibility' ); ?></label><br />
												<input type="text" id="asl_navigation" name="asl_navigation" placeholder="#" size="30" aria-describedby="asl_navigation_description" value="<?php echo esc_attr( get_option( 'asl_navigation' ) ); ?>"/> <span id="asl_navigation_description"><?php _e( 'ID attribute starting with <code>#</code>', 'wp-accessibility' ); ?></span>
											</li>
											<?php
											/**
											 * Customize the default value for sitemap skiplink. Turns on sitemap skiplink options in WP Accessibility versions > 1.9.0.
											 *
											 * @hook asl_sitemap
											 * @param {string} Value to use as a default for the sitemap.
											 *
											 * @return {string}
											 */
											$default_sitemap = apply_filters( 'asl_sitemap', '' );
											if ( '' !== get_option( 'asl_sitemap', $default_sitemap ) ) {
												?>
											<li>
												<label for="asl_sitemap"><?php _e( 'Site Map link target (URL for your site map)', 'wp-accessibility' ); ?></label><br />
												<input type="text" id="asl_sitemap" name="asl_sitemap" size="44" value="<?php echo esc_attr( get_option( 'asl_sitemap', $default_sitemap ) ); ?>"/>
											</li>
												<?php
											}
											/**
											 * Customize the default value for extra skiplink. Turns on extra skiplink options in WP Accessibility versions > 1.9.0.
											 *
											 * @hook asl_extra_target
											 * @param {string} Value to use as a default for the extra skiplink target.
											 *
											 * @return {string}
											 */
											$default_extra = apply_filters( 'asl_extra_target', '' );
											if ( '' !== get_option( 'asl_extra_target', $default_extra ) ) {
												?>
											<li>
												<label for="asl_extra_target"><?php _e( 'Add your own link (link or container ID)', 'wp-accessibility' ); ?></label><br />
												<input type="text" id="asl_extra_target" name="asl_extra_target" size="44" value="<?php echo esc_attr( get_option( 'asl_extra_target', $default_extra ) ); ?>"/>
											</li>
											<li>
												<label for="asl_extra_text"><?php _e( 'Link text for your link', 'wp-accessibility' ); ?></label><br />
												<input type="text" id="asl_extra_text" name="asl_extra_text" size="44" value="<?php echo esc_attr( get_option( 'asl_extra_text' ) ); ?>"/>
											</li>
												<?php
											}
											$use_defaults = get_option( 'asl_default_styles', '' );
											?>
											<li>
												<label for="asl_default_styles"><?php _e( 'Use default Skiplink CSS', 'wp-accessibility' ); ?></label>
												<input type="checkbox" id="asl_default_styles" name="asl_default_styles" value="true" <?php checked( get_option( 'asl_default_styles' ), 'true' ); ?> />
											</li>
											<?php
											if ( 'true' !== $use_defaults ) {
												$styles = wpa_skiplink_css();
												?>
											<li>
												<label for="asl_styles"><?php _e( 'Styles for Skiplinks', 'wp-accessibility' ); ?></label><br/>
												<textarea name='asl_styles' id='asl_styles' cols='60' rows='4'><?php echo esc_textarea( stripcslashes( $styles ) ); ?></textarea>
											</li>
												<?php
											} else {
												$styles = wpa_skiplink_css( true );
												echo '<pre id="wpa_default_css">' . esc_html( stripcslashes( $styles ) ) . '</pre>';
											}
											?>
										</ul>
									</fieldset>
									<p>
										<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
										<input type="hidden" name="action" value="asl"/>
									</p>
									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php esc_html_e( 'Update Skiplink Settings', 'wp-accessibility' ); ?>"/></p>
								</form>
									<?php
								}
								?>
							</div>
						</div>
						<div class="postbox">
							<h2 id="toolbar" class='hndle'><?php esc_html_e( 'Accessibility Toolbar', 'wp-accessibility' ); ?></h2>
							<div class="inside">
								<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wp-accessibility' ) ); ?>">
									<ul>
										<li class="wpa-checkboxes">
											<input type="checkbox" id="wpa_toolbar" name="wpa_toolbar" <?php checked( get_option( 'wpa_toolbar' ), 'on' ); ?>/>
											<label for="wpa_toolbar"><?php _e( 'Enable Accessibility toolbar', 'wp-accessibility' ); ?></label>
											<ul>
												<li>
													<input type="checkbox" id="wpa_toolbar_fs" name="wpa_toolbar_fs" <?php checked( get_option( 'wpa_toolbar_fs', '' ), 'on' ); ?> value='on' />
													<label for="wpa_toolbar_fs"><?php _e( 'Font size', 'wp-accessibility' ); ?></label>
												</li>
												<li>
													<input type="checkbox" id="wpa_toolbar_ct" name="wpa_toolbar_ct" <?php checked( get_option( 'wpa_toolbar_ct', '' ), 'on' ); ?> value='on' />
													<label for="wpa_toolbar_ct"><?php _e( 'Contrast', 'wp-accessibility' ); ?></label>
												</li>
												<li>
													<input type="checkbox" aria-describedby="wpa_toolbar_gs_note" id="wpa_toolbar_gs" name="wpa_toolbar_gs" <?php checked( get_option( 'wpa_toolbar_gs' ), 'on' ); ?> />
													<label for="wpa_toolbar_gs"><?php _e( 'Grayscale', 'wp-accessibility' ); ?></label> <em class="wpa-note" id="wpa_toolbar_gs_note"><?php _e( 'Grayscale is intended for testing, and will appear only for logged-in administrators', 'wp-accessibility' ); ?></em>
												</li>
											</ul>
										</li>
										<li>
											<label for="wpa_toolbar_default"><?php _e( 'Toolbar location (optional)', 'wp-accessibility' ); ?></label><br />
											<input type="text" id="wpa_toolbar_default" name="wpa_toolbar_default" aria-describedby="wpa_toolbar_default_description" placeholder="#" value="<?php echo esc_attr( get_option( 'wpa_toolbar_default' ) ); ?>" /> <span id="wpa_toolbar_default_description"><?php _e( 'ID attribute starting with <code>#</code>', 'wp-accessibility' ); ?></span>
										</li>
										<?php
										$size = absint( get_option( 'wpa_toolbar_size' ) );
										?>
										<li>
											<label for="wpa_toolbar_size"><?php _e( 'Toolbar font size', 'wp-accessibility' ); ?></label><br />
											<select name='wpa_toolbar_size' id='wpa_toolbar_size'>
												<option value=''><?php _e( 'Default size', 'wp-accessibility' ); ?></option>
												<?php
												for ( $i = 1.6; $i <= 3.8; ) {
													$val           = ( $i * 10 ) + 2;
													$current       = absint( $val );
													$selected_size = ( $current === $size ) ? ' selected="selected"' : '';
													echo "<option value='$val'$selected_size>$val px</option>";
													$i = $i + .1;
												}
												?>
											</select>
										</li>
										<li>
											<input type="checkbox" id="wpa_alternate_fontsize" name="wpa_alternate_fontsize" <?php checked( get_option( 'wpa_alternate_fontsize' ), 'on' ); ?>/>
											<label for="wpa_alternate_fontsize"><?php _e( 'Use alternate font resizing stylesheet', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_widget_toolbar" name="wpa_widget_toolbar" <?php checked( get_option( 'wpa_widget_toolbar' ), 'on' ); ?>/>
											<label for="wpa_widget_toolbar"><?php _e( 'Support Accessibility toolbar as shortcode or widget', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_toolbar_right" name="wpa_toolbar_right" <?php checked( get_option( 'wpa_toolbar_right' ), 'on' ); ?>/>
											<label for="wpa_toolbar_right"><?php _e( 'Place toolbar on opposite side of screen.', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_toolbar_mobile" name="wpa_toolbar_mobile" <?php checked( get_option( 'wpa_toolbar_mobile' ), 'on' ); ?>/>
											<label for="wpa_toolbar_mobile"><?php _e( 'Hide toolbar on small screens.', 'wp-accessibility' ); ?></label>
										</li>
									</ul>
									<p>
										<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
										<input type="hidden" name="action" value="toolbar" />
									</p>

									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Toolbar Settings', 'wp-accessibility' ); ?>"/></p>
								</form>
							</div>
						</div>
						<div class="postbox">
							<h2 id="accessibility-settings" class='hndle'><?php _e( 'Accessibility Fixes', 'wp-accessibility' ); ?></h2>

							<div class="inside">
								<p><?php _e( 'Settings that fix potential accessibility issues on your site.', 'wp-accessibility' ); ?></p>
								<hr>
								<form method="post" action="<?php echo admin_url( 'admin.php?page=wp-accessibility' ); ?>">
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
											<li><?php _e( '<strong>Three disabled features:</strong> Site language, continue reading text and standard form labels are defined in your <code>accessibility-ready</code> theme.', 'wp-accessibility' ); ?></li>
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
											<input type="checkbox" id="wpa_underline" aria-describedby="wpa-underline-note" name="wpa_underline" <?php checked( get_option( 'wpa_underline' ), 'on' ); ?>/>
											<label for="wpa_underline"><?php _e( 'Force underline on links', 'wp-accessibility' ); ?></label> <em id="wpa-underline-note" class="wpa-note"><?php _e( 'Excludes links inside <code>nav</code> elements.', 'wp-accessibility' ); ?></em>
										</li>
										<li>
											<input type="checkbox" id="wpa_focus" name="wpa_focus" <?php checked( get_option( 'wpa_focus' ), 'on' ); ?>/>
											<label for="wpa_focus"><?php _e( 'Add outline to elements on keyboard focus', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<label for="wpa_focus_color"><?php _e( 'Outline color (hexadecimal, optional)', 'wp-accessibility' ); ?></label><br />
											<input type="text" id="wpa_focus_color" name="wpa_focus_color" value="#<?php echo esc_attr( get_option( 'wpa_focus_color' ) ); ?>"/>
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
							<h2 class="hndle"><?php _e( 'Accessibility Features', 'wp-accessibility' ); ?></h2>
							<div class="inside">
								<p><?php _e( 'Enable content features to improve site accessibility.', 'wp-accessibility' ); ?></p>
								<hr>
								<form method="post" action="<?php echo admin_url( 'admin.php?page=wp-accessibility' ); ?>">
									<ul>
										<li>
											<label for="wpa_longdesc"><?php _e( 'Long Description UI', 'wp-accessibility' ); ?></label><br />
											<select id="wpa_longdesc" name="wpa_longdesc">
												<option value='false'<?php selected( get_option( 'wpa_longdesc' ), 'false' ); ?>><?php _e( 'None', 'wp-accessibility' ); ?></option>
												<option value='link'<?php selected( get_option( 'wpa_longdesc' ), 'link' ); ?>><?php _e( 'Link to description', 'wp-accessibility' ); ?></option>
												<option value='jquery'<?php selected( get_option( 'wpa_longdesc' ), 'jquery' ); ?>><?php _e( 'Button trigger to overlay image', 'wp-accessibility' ); ?></option>
											</select>
										</li>
										<li>
											<input type="checkbox" id="wpa_longdesc_featured" name="wpa_longdesc_featured" <?php checked( get_option( 'wpa_longdesc_featured' ), 'on' ); ?>/>
											<label for="wpa_longdesc_featured"><?php _e( 'Support <code>longdesc</code> on featured images', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_show_alt" name="wpa_show_alt" <?php checked( get_option( 'wpa_show_alt' ), 'on' ); ?>/>
											<label for="wpa_show_alt"><?php _e( 'Add toggle to view image <code>alt</code> text in comments and post content.', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<fieldset>
												<legend><?php _e( 'Enable Content Summaries', 'wp-accessibility' ); ?></legend>
												<ul class="checkboxes">
												<?php
												$enabled    = get_option( 'wpa_post_types', array() );
												$post_types = get_post_types(
													array(
														'show_ui' => true,
														'public'  => true,
													),
													'objects'
												);
												foreach ( $post_types as $type ) {
													$id      = $type->name;
													$name    = $type->labels->singular_name;
													$checked = ( in_array( $id, $enabled, true ) ) ? ' checked="checked"' : '';

													echo '<li><input type="checkbox" name="wpa_post_types[]" id="wpa_post_types_' . $id . '" value="' . $id . '"' . $checked . '/> <label for="wpa_post_types_' . $id . '">' . $name . '</label></li>';
												}
												?>
												</ul>
											</fieldset>
										</li>
									</ul>
									<p>
										<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>" />
										<input type="hidden" name="action" value="features" />
									</p>
									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Accessibility Features', 'wp-accessibility' ); ?>"/></p>
								</form>
							</div>
						</div>
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
											<input type="checkbox" id="wpa_disable_fullscreen" name="wpa_disable_fullscreen" <?php checked( get_option( 'wpa_disable_fullscreen' ), 'on' ); ?>/>
											<label for="wpa_disable_fullscreen"><?php _e( 'Disable fullscreen block editor by default', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<input type="checkbox" id="wpa_diagnostics" name="wpa_diagnostics" <?php checked( get_option( 'wpa_diagnostics' ), 'on' ); ?>/>
											<label for="wpa_diagnostics"><?php _e( 'Enable diagnostic CSS', 'wp-accessibility' ); ?></label>
										</li>
										<li>
											<fieldset>
												<legend><?php _e( 'Page Statistics Tracking', 'wp-accessibility' ); ?></legend>
												<ul>
													<li>
														<input type="radio" id="wpa_track_stats_none" value="off" name="wpa_track_stats" <?php checked( get_option( 'wpa_track_stats' ), 'off' ); ?>/>
														<label for="wpa_track_stats_none"><?php _e( 'None', 'wp-accessibility' ); ?></label>
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
							<h2 class='hndle'><?php _e( 'Remove Title Attributes', 'wp-accessibility' ); ?></h2>

							<div class="inside">
								<?php wpa_accessible_theme(); ?>
								<form method="post" action="<?php echo admin_url( 'admin.php?page=wp-accessibility' ); ?>">
									<fieldset>
										<legend><?php _e( 'Remove title attributes from:', 'wp-accessibility' ); ?></legend>
										<ul>
											<li><input type="checkbox" id="rta_from_tag_clouds" name="rta_from_tag_clouds" <?php checked( get_option( 'rta_from_tag_clouds' ), 'on' ); ?>/>
											<label for="rta_from_tag_clouds"><?php _e( 'Tag clouds', 'wp-accessibility' ); ?></label>
											</li>
										</ul>
									</fieldset>
									<p>
										<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>" />
										<input type="hidden" name="action" value="rta" />
									</p>

									<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Title Attribute Settings', 'wp-accessibility' ); ?>"/></p>
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
									$results = "<div class='updated notice'>";
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

/**
 * Show admin sidebar.
 */
function wpa_admin_sidebar() {
	?>
	<div class="postbox-container" id="wpa-sidebar">
		<div class="metabox-holder">
			<div class="ui-sortable meta-box-sortables">
				<div class="postbox wpa-support-me promotion">
					<h2 class='hndle'><?php _e( 'Support this Plugin', 'wp-accessibility' ); ?></h2>

					<div class="inside">
						<p>
							<a href="https://twitter.com/intent/follow?screen_name=joedolson" class="twitter-follow-button" data-size="small" data-related="joedolson">Follow @joedolson</a>
							<script>!function (d, s, id) {
									var js, fjs = d.getElementsByTagName(s)[0];
									if (!d.getElementById(id)) {
										js = d.createElement(s);
										js.id = id;
										js.src = "https://platform.twitter.com/widgets.js";
										fjs.parentNode.insertBefore(js, fjs);
									}
								}(document, "script", "twitter-wjs");</script>
						</p>
						<div class="wpa-flex">
							<img src="<?php echo plugins_url( 'imgs/awd-logo-disc.png', __FILE__ ); ?>" alt="Joe Dolson Accessible Web Design" />
							<p class="small">
								<?php _e( "If you find WP Accessibility useful, please <a href='https://wordpress.org/plugins/wp-accessibility/'>rate it five stars</a> or <a href='https://translate.wordpress.org/projects/wp-plugins/wp-accessibility'>help with translation</a>.", 'wp-accessibility' ); ?>
							</p>
						</div>
						<p>
							<a href="https://github.com/sponsors/joedolson"><?php _e( 'Support WP Accessibility', 'wp-accessibility' ); ?></a>
						</p>
					</div>
				</div>
				<?php wpa_edac_promotion(); ?>

				<div class="postbox">
					<h2 class='hndle'><?php _e( 'Accessibility References', 'wp-accessibility' ); ?></h2>

					<div class="inside">
						<ul>
							<li><a href="https://docs.joedolson.com/wp-accessibility/">Plugin Documentation</a></li>
							<li><a href="http://make.wordpress.org/accessibility/">Make WordPress: Accessibility</a></li>
							<li><a href="https://make.wordpress.org/themes/handbook/review/accessibility/">WordPress Theme Accessibility Guidelines</a></li>
							<li><a href="https://www.joedolson.com/tools/color-contrast.php">Color Contrast Testing</a></li>
							<li><a href="http://wave.webaim.org/">WAVE: Web accessibility evaluation tool</a></li>
							<li><a href="https://www.linkedin.com/learning/wordpress-accessibility-2/">WordPress Accessibility course at LinkedIn Learning</a></li>
						</ul>
					</div>
				</div>

				<div class="postbox">
					<h2 class='hndle'><?php _e( 'Customization', 'wp-accessibility' ); ?></h2>

					<div class="inside">
						<ul>
							<li>
							<?php _e( 'Custom high-contrast styles go in <code>a11y-contrast.css</code> in your Theme\'s stylesheet directory.', 'wp-accessibility' ); ?>
							</li>
							<li>
							<?php _e( 'Set custom styles for large print using the body class <code>.fontsize</code> in your theme styles or the customizer.', 'wp-accessibility' ); ?>
							</li>
							<li>
							<?php _e( 'Set a custom long description template by adding <code>longdesc-template.php</code> to your theme directory.', 'wp-accessibility' ); ?>
							</li>
							<li>
							<?php _e( 'The <a href="#wpa_widget_toolbar">shortcode for the Accessibility toolbar</a> is <code>[wpa_toolbar]</code>', 'wp-accessibility' ); ?>
							</li>
						</ul>
					</div>
				</div>

			<?php if ( wpa_accessible_theme() ) { ?>
				<div class="postbox">
					<h2 class='hndle'><?php _e( 'Your Theme', 'wp-accessibility' ); ?></h2>

					<div class="inside">
						<p>
						<?php _e( "You're using a theme reviewed as <code>accessibility-ready</code> by the WordPress theme review team. Some options have been disabled in WP Accessibility.", 'wp-accessibility' ); ?>
						</p>
						<p>
						<?php
						// Translators: URL to read about the accessibility ready tag requirements.
						printf( __( 'Read more about the <a href="%s">WordPress accessibility-ready tag</a>', 'wp-accessibility' ), 'https://make.wordpress.org/themes/handbook/review/accessibility/' );
						?>
						</p>
					</div>
				</div>
			<?php } ?>

				<div class="postbox" id="privacy">
					<h2 class='hndle'><?php _e( 'Privacy', 'wp-accessibility' ); ?></h2>

					<div class="inside">
						<h3><?php _e( 'Cookies', 'wp-accessibility' ); ?></h3>
						<p><?php _e( 'The accessibility toolbar sets cookies to maintain awareness of the user\'s selected accessibility options. If the toolbar is not in use, WP Accessibility does not set any cookies.', 'wp-accessibility' ); ?></p>
						<h3><?php _e( 'Information collected by WP Accessibility', 'wp-accessibility' ); ?></h3>
						<p><?php _e( 'WP Accessibility does not collect any personally identifying information about users or visitors.', 'wp-accessibility' ); ?></p>
						<p><?php _e( 'User statistics (toolbar actions) collected by WP Accessibility are tracked using browser fingerprinting, and no identifiable information is stored at any time.', 'wp-accessibility' ); ?></p>
						<p><?php _e( 'Page statistics are collected when the page is initially viewed, then again if the accessibility values have changed since the last check.', 'wp-accessibility' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Show Equalize Digital Accessibility Checker promotion.
 *
 * @param string $type Promo size.
 */
function wpa_edac_promotion( $type = 'large' ) {
	$pro  = false;
	$edac = false;
	if ( function_exists( 'edac_check_plugin_active' ) ) {
		$pro  = edac_check_plugin_active( 'accessibility-checker-pro/accessibility-checker-pro.php' );
		$edac = true;
	}
	if ( ! $pro ) {
		if ( 'large' === $type ) {
			$promo_text = ( $edac ) ? __( 'Finding Accessibility Checker useful? Go Pro for more advanced accessibility testing options!', 'wp-accessibility' ) : __( 'Try Accessibility Checker by Equalize Digital - fast and efficient accessibility testing for your site!', 'wp-accessibility' );
			?>
		<div class="postbox equalize-digital-promotion promotion">
			<h2 class='hndle'><?php _e( 'Ready to fix your site?', 'wp-accessibility' ); ?></h2>

			<div class="inside">
				<div class="wpa-flex">
					<img src="<?php echo plugins_url( 'imgs/Equalize-Digital-Accessibility-Emblem-400x400-1.png', __FILE__ ); ?>" alt="Accessibility Checker" />
					<p class="small">
						<?php echo esc_html( $promo_text ); ?>
					</p>
				</div>
				<p class="coupon small"><strong><?php _e( 'Use coupon code <code>WPAccessibility</code> for 20% off!', 'wp-accessibility' ); ?></strong></p>
				<p class="wpa-affiliate">
					<a href="https://equalizedigital.com/accessibility-checker/pricing/?ref=joedolson&discount=WPAccessibility&campaign=wpaccessibility" aria-describedby="wpa-affiliate-notice"><?php _e( 'Get Accessibility Checker', 'wp-accessibility' ); ?></a>
				</p>
				<p class="wpa-affiliate-notice" id="wpa-affiliate-notice">
					(<?php _e( 'Affiliate Link', 'wp-accessibility' ); ?>)
				</p>
			</div>
		</div>
			<?php
		} else {
			?>
		<div class="wpad-small-promotion">
			<h3>Need help fixing accessibility issues?</h3>
			<p class="coupon small"><?php _e( 'Use coupon code <code>WPAccessibility</code> for 20% off <strong>Accessibility Checker Pro</strong> at <a href="https://equalizedigital.com/?ref=joedolson&discount=WPAccessibility&campaign=wpaccessibility" aria-describedby="wpa-affiliate-notice">Equalize Digital</a>!', 'wp-accessibility' ); ?></p>
			<div class="wpa-flex">
				<p class="wpa-affiliate">
					<a href="https://equalizedigital.com/accessibility-checker/pricing/?ref=joedolson&discount=WPAccessibility&campaign=wpaccessibility" aria-describedby="wpa-affiliate-notice"><?php _e( 'Buy Accessibility Checker', 'wp-accessibility' ); ?></a>
				</p>
				<p class="wpa-affiliate-notice" id="wpa-affiliate-notice">
					<em><?php _e( 'Affiliate Links', 'wp-accessibility' ); ?></em>
				</p>
			</div>
		</div>
			<?php
		}
	}
}

// Use Codemirror for Skiplink style fields.
add_action(
	'admin_enqueue_scripts',
	function() {
		if ( ! function_exists( 'wp_enqueue_code_editor' ) ) {
			return;
		}
		if ( 'toplevel_page_wp-accessibility' !== get_current_screen()->id ) {
			return;
		}

		// Enqueue code editor and settings for manipulating CSS.
		$settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

		// Bail if user disabled CodeMirror or using default styles.
		if ( false === $settings || 'true' === get_option( 'asl_default_styles' ) ) {
			return;
		}
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "asl_styles", %s ); } );',
				wp_json_encode( $settings )
			)
		);
	}
);

