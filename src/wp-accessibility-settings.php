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

add_action( 'admin_head', 'wpa_admin_styles' );
/**
 * Enqueue admin stylesheets.
 */
function wpa_admin_styles() {
	if ( isset( $_GET['page'] ) && ( 'wp-accessibility/wp-accessibility.php' === $_GET['page'] ) ) {
		wp_enqueue_style( 'farbtastic' );
		echo '<link type="text/css" rel="stylesheet" href="' . plugins_url( 'css/wpa-styles.css', __FILE__ ) . '" />';
	}
}

/**
 * Write admin JS.
 */
function wpa_write_js() {
	global $current_screen;
	if ( 'settings_page_wp-accessibility/wp-accessibility' === $current_screen->base ) {
		?>
<script>
	//<![CDATA[
	(function ($) {
		'use strict';
		$('#fore').farbtastic('#color1');
		$('#back').farbtastic('#color2');
	}(jQuery));
	//]]>
</script>
		<?php
	}
}

add_action( 'admin_enqueue_scripts', 'wpa_admin_js' );
/**
 * Enqueue color picker for contrast testing
 **/
function wpa_admin_js() {
	global $current_screen;
	if ( 'settings_page_wp-accessibility/wp-accessibility' === $current_screen->base ) {
		wp_enqueue_script( 'farbtastic' );
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
			die( 'Security check failed' );
		}
		if ( isset( $_POST['action'] ) && 'rta' === $_POST['action'] ) {
			$rta_from_tag_clouds = ( isset( $_POST['rta_from_tag_clouds'] ) ) ? 'on' : '';
			update_option( 'rta_from_tag_clouds', $rta_from_tag_clouds );

			$message = __( 'Remove Title Attributes Settings Updated', 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . '</p></div>';
		}
		if ( isset( $_POST['action'] ) && 'asl' === $_POST['action'] ) {
			$asl_enable         = ( isset( $_POST['asl_enable'] ) ) ? 'on' : '';
			$asl_content        = ( isset( $_POST['asl_content'] ) ) ? $_POST['asl_content'] : '';
			$asl_navigation     = ( isset( $_POST['asl_navigation'] ) ) ? $_POST['asl_navigation'] : '';
			$asl_sitemap        = ( isset( $_POST['asl_sitemap'] ) ) ? $_POST['asl_sitemap'] : '';
			$asl_extra_target   = ( isset( $_POST['asl_extra_target'] ) ) ? $_POST['asl_extra_target'] : '';
			$asl_extra_text     = ( isset( $_POST['asl_extra_text'] ) ) ? $_POST['asl_extra_text'] : '';
			$asl_visible        = ( isset( $_POST['asl_visible'] ) ) ? 'on' : '';
			$asl_styles_focus   = ( isset( $_POST['asl_styles_focus'] ) ) ? $_POST['asl_styles_focus'] : '';
			$asl_styles_passive = ( isset( $_POST['asl_styles_passive'] ) ) ? $_POST['asl_styles_passive'] : '';
			update_option( 'asl_enable', $asl_enable );
			update_option( 'asl_content', $asl_content );
			update_option( 'asl_navigation', $asl_navigation );
			update_option( 'asl_sitemap', $asl_sitemap );
			update_option( 'asl_extra_target', $asl_extra_target );
			update_option( 'asl_extra_text', $asl_extra_text );
			update_option( 'asl_visible', $asl_visible );
			$notice = ( 'asl' === $asl_visible ) ? '<p>' . __( 'WP Accessibility does not provide any styles for visible skiplinks. You can still set the look of the links using the textareas provided, but all other layout must be assigned in your theme.', 'wp-accessibility' ) . '</p>' : '';

			update_option( 'asl_styles_focus', $asl_styles_focus );
			update_option( 'asl_styles_passive', $asl_styles_passive );
			$message = __( 'Add Skiplinks Settings Updated', 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . "</p>$notice</div>";
		}
		if ( isset( $_POST['action'] ) && 'misc' === $_POST['action'] ) {
			$wpa_lang                    = ( isset( $_POST['wpa_lang'] ) ) ? 'on' : '';
			$wpa_target                  = ( isset( $_POST['wpa_target'] ) ) ? 'on' : '';
			$wpa_labels                  = ( isset( $_POST['wpa_labels'] ) ) ? 'on' : '';
			$wpa_search                  = ( isset( $_POST['wpa_search'] ) ) ? 'on' : '';
			$wpa_tabindex                = ( isset( $_POST['wpa_tabindex'] ) ) ? 'on' : '';
			$wpa_underline               = ( isset( $_POST['wpa_underline'] ) ) ? 'on' : '';
			$wpa_longdesc                = ( isset( $_POST['wpa_longdesc'] ) ) ? esc_attr( $_POST['wpa_longdesc'] ) : 'false';
			$wpa_longdesc_featured       = ( isset( $_POST['wpa_longdesc_featured'] ) ) ? esc_attr( $_POST['wpa_longdesc_featured'] ) : 'false';
			$wpa_image_titles            = ( isset( $_POST['wpa_image_titles'] ) ) ? 'on' : '';
			$wpa_more                    = ( isset( $_POST['wpa_more'] ) ) ? 'on' : '';
			$wpa_focus                   = ( isset( $_POST['wpa_focus'] ) ) ? 'on' : '';
			$wpa_focus_color             = ( isset( $_POST['wpa_focus_color'] ) ) ? str_replace( '#', '', $_POST['wpa_focus_color'] ) : '';
			$wpa_continue                = ( isset( $_POST['wpa_continue'] ) ) ? $_POST['wpa_continue'] : 'Continue Reading';
			$wpa_row_actions             = ( isset( $_POST['wpa_row_actions'] ) ) ? 'on' : '';
			$wpa_diagnostics             = ( isset( $_POST['wpa_diagnostics'] ) ) ? 'on' : '';
			$wpa_insert_roles            = ( isset( $_POST['wpa_insert_roles'] ) ) ? 'on' : '';
			$wpa_complementary_container = ( isset( $_POST['wpa_complementary_container'] ) ) ? str_replace( '#', '', $_POST['wpa_complementary_container'] ) : '';
			update_option( 'wpa_lang', $wpa_lang );
			update_option( 'wpa_target', $wpa_target );
			update_option( 'wpa_labels', $wpa_labels );
			update_option( 'wpa_search', $wpa_search );
			update_option( 'wpa_tabindex', $wpa_tabindex );
			update_option( 'wpa_underline', $wpa_underline );
			update_option( 'wpa_longdesc', $wpa_longdesc );
			update_option( 'wpa_longdesc_featured', $wpa_longdesc_featured );
			update_option( 'wpa_image_titles', $wpa_image_titles );
			update_option( 'wpa_more', $wpa_more );
			update_option( 'wpa_focus', $wpa_focus );
			update_option( 'wpa_focus_color', $wpa_focus_color );
			update_option( 'wpa_continue', $wpa_continue );
			update_option( 'wpa_row_actions', $wpa_row_actions );
			update_option( 'wpa_diagnostics', $wpa_diagnostics );
			update_option( 'wpa_insert_roles', $wpa_insert_roles );
			update_option( 'wpa_complementary_container', $wpa_complementary_container );
			$message = __( 'Miscellaneous Accessibility Settings Updated', 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . '</p></div>';
		}

		if ( isset( $_POST['action'] ) && 'toolbar' === $_POST['action'] ) {
			$wpa_toolbar            = ( isset( $_POST['wpa_toolbar'] ) ) ? 'on' : '';
			$wpa_toolbar_size       = ( isset( $_POST['wpa_toolbar_size'] ) ) ? $_POST['wpa_toolbar_size'] : '';
			$wpa_alternate_fontsize = ( isset( $_POST['wpa_alternate_fontsize'] ) ) ? 'on' : '';
			$wpa_widget_toolbar     = ( isset( $_POST['wpa_widget_toolbar'] ) ) ? 'on' : '';
			$wpa_toolbar_gs         = ( isset( $_POST['wpa_toolbar_gs'] ) ) ? 'on' : '';
			$wpa_toolbar_fs         = ( isset( $_POST['wpa_toolbar_fs'] ) ) ? 'off' : '';
			$wpa_toolbar_ct         = ( isset( $_POST['wpa_toolbar_ct'] ) ) ? 'off' : '';
			$wpa_toolbar_default    = ( isset( $_POST['wpa_toolbar_default'] ) ) ? $_POST['wpa_toolbar_default'] : '';
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
		<h1><?php _e( 'WP Accessibility: Settings', 'wp-accessibility' ); ?></h1>

<div id="wpa_settings_page" class="postbox-container" style="width: 70%">
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
					<form method="post" action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
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
									<label for="asl_content"><?php _e( 'Skip to Content link target (ID of your main content container)', 'wp-accessibility' ); ?></label><br />
									<input type="text" id="asl_content" name="asl_content" size="44" value="<?php echo esc_attr( get_option( 'asl_content' ) ); ?>"/>
								</li>
								<li>
									<label for="asl_navigation"><?php _e( 'Skip to Navigation link target (ID of your main navigation container)', 'wp-accessibility' ); ?></label><br />
									<input type="text" id="asl_navigation" name="asl_navigation" size="44" value="<?php echo esc_attr( get_option( 'asl_navigation' ) ); ?>"/>
								</li>
								<li>
									<label for="asl_sitemap"><?php _e( 'Site Map link target (URL for your site map)', 'wp-accessibility' ); ?></label><br />
									<input type="text" id="asl_sitemap" name="asl_sitemap" size="44" value="<?php echo esc_attr( get_option( 'asl_sitemap' ) ); ?>"/>
								</li>
								<li>
									<label for="asl_extra_target"><?php _e( 'Add your own link (link or container ID)', 'wp-accessibility' ); ?></label><br />
									<input type="text" id="asl_extra_target" name="asl_extra_target" size="44" value="<?php echo esc_attr( get_option( 'asl_extra_target' ) ); ?>"/>
								</li>
								<li>
									<label for="asl_extra_text"><?php _e( 'Link text for your link', 'wp-accessibility' ); ?></label><br />
									<input type="text" id="asl_extra_text" name="asl_extra_text" size="44" value="<?php echo esc_attr( get_option( 'asl_extra_text' ) ); ?>"/>
								</li>
								<li>
									<label for="asl_styles_focus"><?php _e( 'Styles for Skiplinks when they have focus', 'wp-accessibility' ); ?></label><br/>
									<textarea name='asl_styles_focus' id='asl_styles_focus' cols='60' rows='4'><?php echo esc_attr( stripslashes( get_option( 'asl_styles_focus' ) ) ); ?></textarea>
								</li>
								<?php
								if ( 'on' !== get_option( 'asl_visible' ) ) {
									$disabled = " disabled='disabled' style='background: #eee;'";
									$note     = ' ' . __( '(Not currently visible)', 'wp-accessibility' );
								} else {
									$disabled = '';
									$note     = '';
								}
								?>
								<li>
									<label for="asl_styles_passive">
								<?php
								_e( 'Styles for Skiplinks without focus', 'wp-accessibility' );
								echo $note;
								?>
									</label><br/>
									<textarea name='asl_styles_passive' id='asl_styles_passive' cols='60' rows='4'<?php echo $disabled; ?>><?php echo stripslashes( get_option( 'asl_styles_passive' ) ); ?></textarea>
								</li>
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="asl"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Skiplink Settings', 'wp-accessibility' ); ?>"/></p>
					</form>
						<?php
					}
					?>
				</div>
			</div>
			<div class="postbox">
				<h2 id="toolbar" class='hndle'><?php _e( 'Accessibility Toolbar Settings', 'wp-accessibility' ); ?></h2>
				<div class="inside">
					<form method="post" action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<ul>
							<li>
								<input type="checkbox" id="wpa_toolbar" name="wpa_toolbar" <?php checked( get_option( 'wpa_toolbar' ), 'on' ); ?>/>
								<label for="wpa_toolbar"><?php _e( 'Add Accessibility toolbar with fontsize adjustment and contrast toggle', 'wp-accessibility' ); ?></label>
								<ul>
									<li>
										<input type="checkbox" id="wpa_toolbar_fs" name="wpa_toolbar_fs" <?php checked( get_option( 'wpa_toolbar_fs' ), 'off' ); ?>/>
										<label for="wpa_toolbar_fs"><?php _e( 'Exclude font size toggle from Accessibility toolbar', 'wp-accessibility' ); ?></label>
									</li>
									<li>
										<input type="checkbox" id="wpa_toolbar_ct" name="wpa_toolbar_ct" <?php checked( get_option( 'wpa_toolbar_ct' ), 'off' ); ?>/>
										<label for="wpa_toolbar_ct"><?php _e( 'Exclude contrast toggle from Accessibility toolbar', 'wp-accessibility' ); ?></label>
									</li>
									<li>
										<input type="checkbox" aria-describedby="wpa_toolbar_gs_note" id="wpa_toolbar_gs" name="wpa_toolbar_gs" <?php checked( get_option( 'wpa_toolbar_gs' ), 'on' ); ?> />
										<label for="wpa_toolbar_gs"><?php _e( 'Include grayscale toggle with Accessibility toolbar', 'wp-accessibility' ); ?></label><br /><em id="wpa_toolbar_gs_note"><?php _e( 'The grayscale toggle is only intended for testing, and will appear only for logged-in administrators', 'wp-accessibility' ); ?></em>
									</li>
								</ul>
							</li>
							<li>
								<label for="wpa_toolbar_default"><?php _e( 'Toolbar location (ID attribute, such as <code>#header</code>)', 'wp-accessibility' ); ?></label>
								<input type="text" id="wpa_toolbar_default" name="wpa_toolbar_default" value="<?php echo esc_attr( get_option( 'wpa_toolbar_default' ) ); ?>" />
							</li>
							<?php
							$size = absint( get_option( 'wpa_toolbar_size' ) );
							?>
							<li>
								<label for="wpa_toolbar_size"><?php _e( 'Toolbar font size', 'wp-accessibility' ); ?></label>
								<select name='wpa_toolbar_size' id='wpa_toolbar_size'>
									<option value=''><?php _e( 'Default size', 'wp-accessibility' ); ?></option>
									<?php
									for ( $i = 1.2; $i <= 3.8; ) {
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
								<label for="wpa_toolbar_right"><?php _e( 'Place toolbar on right side of screen.', 'wp-accessibility' ); ?></label>
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
				<h2 id="contrast" class='hndle'><?php _e( 'Miscellaneous Accessibility Settings', 'wp-accessibility' ); ?></h2>

				<div class="inside">
					<form method="post" action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<ul>
							<?php
							if ( ! wpa_accessible_theme() ) {
								?>
							<li>
								<input type="checkbox" id="wpa_lang" name="wpa_lang" <?php checked( get_option( 'wpa_lang' ), 'on' ); ?>/>
								<label for="wpa_lang"><?php _e( 'Add Site Language and text direction to HTML element', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<input type="checkbox" id="wpa_more" name="wpa_more" <?php checked( get_option( 'wpa_more' ), 'on' ); ?>/>
								<label for="wpa_more"><?php _e( 'Add post title to "more" links.', 'wp-accessibility' ); ?></label>
								<label for="wpa_continue"><?php _e( 'Continue reading text', 'wp-accessibility' ); ?></label>
								<input type="text" id="wpa_continue" name="wpa_continue" value="<?php echo esc_attr( get_option( 'wpa_continue' ) ); ?>"/>
							</li>
							<li>
								<input type="checkbox" id="wpa_insert_roles" name="wpa_insert_roles" <?php checked( get_option( 'wpa_insert_roles' ), 'on' ); ?>/>
								<label for="wpa_insert_roles"><?php _e( 'Add landmark roles to HTML5 structural elements', 'wp-accessibility' ); ?></label><br/>
								<label for="wpa_complementary_container"><?php _e( 'ID for complementary role', 'wp-accessibility' ); ?></label>
								<input type="text" id="wpa_complementary_container" name="wpa_complementary_container" value="#<?php echo esc_attr( get_option( 'wpa_complementary_container' ) ); ?>"/>
							</li>
							<li>
								<input type="checkbox" id="wpa_labels" name="wpa_labels" <?php checked( get_option( 'wpa_labels' ), 'on' ); ?> />
								<label for='wpa_labels'><?php _e( 'Automatically Label WordPress search form and comment forms', 'wp-accessibility' ); ?></label>
							</li>
								<?php
							} else {
								?>
								<li><?php _e( '<strong>Four disabled features:</strong> Site language, continue reading text, landmark roles and standard form labels are defined in your <code>accessibility-ready</code> theme.', 'wp-accessibility' ); ?></li>
								<?php
							}
							?>
							<li>
								<input type="checkbox" id="wpa_target" name="wpa_target" <?php checked( get_option( 'wpa_target' ), 'on' ); ?>/>
								<label for="wpa_target"><?php _e( 'Remove target attribute from links', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<input type="checkbox" id="wpa_search" name="wpa_search" <?php checked( get_option( 'wpa_search' ), 'on' ); ?>/>
								<label for="wpa_search"><?php _e( 'Force search error on empty search submission (theme must have search.php template)', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<input type="checkbox" id="wpa_tabindex" name="wpa_tabindex" <?php checked( get_option( 'wpa_tabindex' ), 'on' ); ?>/>
								<label for="wpa_tabindex"><?php _e( 'Remove tabindex from focusable elements', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<input type="checkbox" id="wpa_underline" name="wpa_underline" <?php checked( get_option( 'wpa_underline' ), 'on' ); ?>/>
								<label for="wpa_underline"><?php _e( 'Force underline on all links', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<label for="wpa_longdesc"><?php _e( 'Long Description UI', 'wp-accessibility' ); ?></label>
								<select id="wpa_longdesc" name="wpa_longdesc">
									<option value='false'<?php selected( get_option( 'wpa_longdesc' ), 'false' ); ?>><?php _e( 'Browser defaults only', 'wp-accessibility' ); ?></option>
									<option value='link'<?php selected( get_option( 'wpa_longdesc' ), 'link' ); ?>><?php _e( 'Link to description', 'wp-accessibility' ); ?></option>
									<option value='jquery'<?php selected( get_option( 'wpa_longdesc' ), 'jquery' ); ?>><?php _e( 'Button trigger to overlay image', 'wp-accessibility' ); ?></option>
								</select>
							</li>
							<li>
								<input type="checkbox" id="wpa_longdesc_featured" name="wpa_longdesc_featured" <?php checked( get_option( 'wpa_longdesc_featured' ), 'on' ); ?>/>
								<label for="wpa_longdesc_featured"><?php _e( 'Support <code>longdesc</code> on featured images', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<input type="checkbox" id="wpa_row_actions" name="wpa_row_actions" <?php checked( get_option( 'wpa_row_actions' ), 'on' ); ?>/>
								<label for="wpa_row_actions"><?php _e( 'Make admin row actions always visible', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<input type="checkbox" id="wpa_image_titles" name="wpa_image_titles" <?php checked( get_option( 'wpa_image_titles' ), 'on' ); ?>/>
								<label for="wpa_image_titles"><?php _e( 'Remove title attributes inserted into post content and featured images.', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<input type="checkbox" id="wpa_diagnostics" name="wpa_diagnostics" <?php checked( get_option( 'wpa_diagnostics' ), 'on' ); ?>/>
								<label for="wpa_diagnostics"><?php _e( 'Enable diagnostic CSS', 'wp-accessibility' ); ?></label>
							</li>
							<li>
								<input type="checkbox" id="wpa_focus" name="wpa_focus" <?php checked( get_option( 'wpa_focus' ), 'on' ); ?>/>
								<label for="wpa_focus"><?php _e( 'Add outline to elements on keyboard focus', 'wp-accessibility' ); ?></label>
								<label for="wpa_focus_color"><?php _e( 'Outline color (hexadecimal, optional)', 'wp-accessibility' ); ?></label>
								<input type="text" id="wpa_focus_color" name="wpa_focus_color" value="#<?php echo esc_attr( get_option( 'wpa_focus_color' ) ); ?>"/></li>
						</ul>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="misc"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Update Miscellaneous Settings', 'wp-accessibility' ); ?>"/></p>
					</form>
				</div>
			</div>
			<div class="postbox">
				<h2 class='hndle'><?php _e( 'Remove Title Attributes', 'wp-accessibility' ); ?></h2>

				<div class="inside">
					<?php wpa_accessible_theme(); ?>
					<form method="post" action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Remove title attributes from:', 'wp-accessibility' ); ?></legend>
							<ul>
								<li><input type="checkbox" id="rta_from_tag_clouds" name="rta_from_tag_clouds" <?php checked( get_option( 'rta_from_tag_clouds' ), 'on' ); ?>/>
								<label for="rta_from_tag_clouds"><?php _e( 'Tag clouds', 'wp-accessibility' ); ?></label>
								</li>
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="rta"/>
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
						$l_contrast    = wpa_luminosity( $colors['red1'], $colors['red2'], $colors['green1'], $colors['green2'], $colors['blue1'], $colors['blue2'] ) . ':1';
						$luminance_raw = wpa_luminosity( $colors['red1'], $colors['red2'], $colors['green1'], $colors['green2'], $colors['blue1'], $colors['blue2'] );
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
						$results .= '<p class="stats wcag2">' . sprintf( __( 'Luminosity Contrast Ratio for %2$s and %3$s is %1$s (Threshold: greater than 7:1 for AAA, 4.5:1 for AA)', 'wp-accessibility' ), '<strong>' . $l_contrast . '</strong>', '<code>#' . $hex1 . '</code>', '<code>#' . $hex2 . '</code>' ) . '</p><p>';
						if ( $luminance_raw >= 7 ) {
							$results .= __( 'The colors compared <strong>pass</strong> the relative luminosity test at level AAA.', 'wp-accessibility' );
						}
						if ( $luminance_raw >= 4.5 && $luminance_raw < 7 ) {
							$results .= __( 'The colors compared <strong>pass</strong> the relative luminosity test at level AA.', 'wp-accessibility' );
						}
						if ( $luminance_raw >= 3 && $luminance_raw < 4.5 ) {
							$results .= __( 'The colors compared pass the relative luminosity test <strong>only when used in large print</strong> situations (greater than 18pt text or 14pt bold text.)', 'wp-accessibility' );
						}
						if ( $luminance_raw < 3 ) {
							$results .= __( 'The colors compared <strong>do not pass</strong> the relative luminosity test.', 'wp-accessibility' );
						}
						$results .= " <a href='#contrast'>" . __( 'Test another set of colors', 'wp-accessibility' ) . '</a>';
						$results .= '</p>';
						$results .= "
				<div class=\"views\">
					<p class='large' style=\"font-size: 2em; line-height: 1.4;color: #$hex1;background: #$hex2;border: 3px solid #$hex1\">Large Print Example</p>
					<p class='small' style=\"font-size: .9em;color: #$hex1;background: #$hex2;border: 3px solid #$hex1\">Small Print Example</p>
					<p class='large' style=\"font-size: 2em; line-height: 1.4;color: #$hex2;background: #$hex1;border: 3px solid #$hex2\">Large Print Example (Inverted)</p>
					<p class='small' style=\"font-size: .9em;color: #$hex2;background: #$hex1;border: 3px solid #$hex2\">Small Print Example (Inverted)</p>
				</div>
			</div>";
						echo $results;
					}
					?>
					<form method="post" action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Test of relative luminosity', 'wp-accessibility' ); ?></legend>
							<ul>
								<li class='fore'>
									<div id="fore"></div>
									<label for="color1"><?php _e( 'Foreground color', 'wp-accessibility' ); ?></label><br/>
									<input type="text" name="color" value="#<?php echo esc_attr( $hex1 ); ?>" size="34" id="color1"/>
								</li>
								<li class='back'>
									<div id="back"></div>
									<label for="color2"><?php _e( 'Background color', 'wp-accessibility' ); ?></label><br/>
									<input type="text" name="color2" value="#<?php echo esc_attr( $hex2 ); ?>" size="34" id="color2"/>
								</li>
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="contrast"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary" value="<?php _e( 'Check Color Contrast', 'wp-accessibility' ); ?>"/></p>
					</form>
				</div>
			</div>
			<div class="postbox" id="privacy">
				<h2 class='hndle'><?php _e( 'Privacy', 'wp-accessibility' ); ?></h2>

				<div class="inside">
					<h3><?php _e( 'Cookies', 'wp-accessibility' ); ?></h3>
					<p><?php _e( 'The accessibility toolbar sets cookies to maintain awareness of the user\'s selected accessibility options. If the toolbar is not in use, WP Accessibility does not set any cookies.', 'wp-accessibility' ); ?></p>
					<h3><?php _e( 'Information Collected by WP Accessibility', 'wp-accessibility' ); ?></h3>
					<p><?php _e( 'WP Accessibility does not collect any private information about users or visitors.', 'wp-accessibility' ); ?></p>
				</div>
			</div>
			<div class="postbox" id="get-support">
				<h2 class='hndle'><?php _e( 'Get Plug-in Support', 'wp-accessibility' ); ?></h2>

				<div class="inside">
				<div class='wpa-support-me'>
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

<div class="postbox-container" style="width:20%">
	<div class="metabox-holder">
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
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
					<p><?php _e( "If you've found WP Accessibility useful, then please consider <a href='http://wordpress.org/extend/plugins/wp-accessibility/'>rating it five stars</a>, <a href='http://www.joedolson.com/donate/'>making a donation</a>, or <a href='https://translate.wordpress.org/projects/wp-plugins/wp-accessibility'>helping with translation</a>.", 'wp-accessibility' ); ?></p>

					<div>
						<p><?php _e( '<a href="http://www.joedolson.com/donate/">Make a donation today!</a> Your donation counts - donate $5, $20, or $100 and help me keep this plug-in running!', 'wp-accessibility' ); ?></p>

						<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<div>
								<input type="hidden" name="cmd" value="_s-xclick"/>
								<input type="hidden" name="hosted_button_id" value="QK9MXYGQKYUZY"/>
								<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="Donate"/>
								<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h2 class='hndle'><?php _e( 'Access Monitor', 'wp-accessibility' ); ?></h2>

				<div class="inside">
					<p>
					<?php
					// Translators: URL to visit Access Monitor at WordPress.org.
					printf( __( 'Try using <a href="%s">Access Monitor</a> to do scheduled and on-demand evaluations of your web site accessibility.', 'wp-accessibility' ), 'https://wordpress.org/plugins/access-monitor/' );
					?>
					</p>
				</div>
			</div>
		</div>

		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h2 class='hndle'><?php _e( 'Accessibility References', 'wp-accessibility' ); ?></h2>

				<div class="inside">
					<ul>
						<li><a href="http://make.wordpress.org/accessibility/wp-accessibility-plugin/">Documentation</a></li>
						<li><a href="http://make.wordpress.org/accessibility/">Make WordPress: Accessibility</a></li>
						<li><a href="https://make.wordpress.org/themes/handbook/review/accessibility/">WordPress Theme Accessibility Guidelines</a></li>
						<li><a href="http://make.wordpress.org/support/user-manual/web-publishing/accessibility/">WordPress
								User Manual: Accessibility</a></li>
						<li><a href="https://www.joedolson.com/tools/color-contrast.php">Test Color Contrast</a></li>
						<li><a href="http://wave.webaim.org/">WAVE: Web accessibility evaluation tool</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h2 class='hndle'><?php _e( 'Customization Notes', 'wp-accessibility' ); ?></h2>

				<div class="inside">
					<p>
					<?php _e( 'It is almost impossible for the Accessibility Toolbar to guarantee a good result for large text or high contrast modes.', 'wp-accessibility' ); ?>
					</p>
					<p>
					<?php _e( 'Author high-contrast styles by placing a stylesheet called <code>a11y-contrast.css</code> in your Theme\'s stylesheet directory.', 'wp-accessibility' ); ?>
					</p>
					<p>
					<?php _e( 'Define custom styles for large print by assigning them in the body class <code>.fontsize</code> in your theme stylesheet.', 'wp-accessibility' ); ?>
					</p>
					<p>
					<?php _e( 'Define a custom long description template by adding the template "longdesc-template.php" to your theme directory.', 'wp-accessibility' ); ?>
					</p>
					<p>
					<?php _e( 'The <a href="#wpa_widget_toolbar">shortcode for the Accessibility toolbar</a> (if enabled) is <code>[wpa_toolbar]</code>', 'wp-accessibility' ); ?>
					</p>
				</div>
			</div>
		</div>

		<?php if ( wpa_accessible_theme() ) { ?>
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h2 class='hndle'><?php _e( 'Your Theme', 'wp-accessibility' ); ?></h2>

				<div class="inside">
					<p>
					<?php _e( "You're using a theme reviewed as <code>accessibility-ready</code> by the WordPress theme review team. Some options have been disabled in WP Accessibility because your theme has taken care of that issue.", 'wp-accessibility' ); ?>
					</p>
					<p>
					<?php
					// Translators: URL to read about the accessibility ready tag requirements.
					printf( __( 'Read more about the <a href="%s">WordPress accessibility-ready tag</a>', 'wp-accessibility' ), 'https://make.wordpress.org/themes/handbook/review/accessibility/' );
					?>
					</p>
				</div>
			</div>
		</div>
		<?php } ?>


		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h2 class='hndle'><?php _e( 'Contributing References', 'wp-accessibility' ); ?></h2>

				<div class="inside">
					<ul>
						<li><a href="http://www.accessibleculture.org/articles/2010/08/continue-reading-links-in-wordpress/">Continue Reading Links in WordPress</a></li>
						<li><a href="http://www.mothereffingtoolconfuser.com">Mother Effing Tool Confuser</a></li>
						<li><a href="http://wordpress.org/extend/plugins/remove-title-attributes/">Remove Title Attributes</a></li>
						<li><a href="http://wordpress.org/extend/plugins/img-title-removal/">IMG Title Removal</a></li>
						<li><a href="https://wordpress.org/plugins/long-description-for-image-attachments/">WordPress Long Description</a></li>
					</ul>
				</div>
			</div>
		</div>


	</div>
</div>

	</div>
	<?php
}
