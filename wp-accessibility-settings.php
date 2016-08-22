<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


function wpa_update_settings() {
	wpa_check_version();
	if ( ! empty( $_POST ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpa-nonce' ) ) {
			die( "Security check failed" );
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'rta' ) {
			$rta_from_nav_menu           = ( isset( $_POST['rta_from_nav_menu'] ) ) ? 'on' : '';
			$rta_from_page_lists         = ( isset( $_POST['rta_from_page_lists'] ) ) ? 'on' : '';
			$rta_from_category_lists     = ( isset( $_POST['rta_from_category_lists'] ) ) ? 'on' : '';
			$rta_from_archive_links      = ( isset( $_POST['rta_from_archive_links'] ) ) ? 'on' : '';
			$rta_from_tag_clouds         = ( isset( $_POST['rta_from_tag_clouds'] ) ) ? 'on' : '';
			$rta_from_category_links     = ( isset( $_POST['rta_from_category_links'] ) ) ? 'on' : '';
			$rta_from_post_edit_links    = ( isset( $_POST['rta_from_post_edit_links'] ) ) ? 'on' : '';
			$rta_from_edit_comment_links = ( isset( $_POST['rta_from_edit_comment_links'] ) ) ? 'on' : '';
			update_option( 'rta_from_nav_menu', $rta_from_nav_menu );
			update_option( 'rta_from_page_lists', $rta_from_page_lists );
			update_option( 'rta_from_category_lists', $rta_from_category_lists );
			update_option( 'rta_from_archive_links', $rta_from_archive_links );
			update_option( 'rta_from_tag_clouds', $rta_from_tag_clouds );
			update_option( 'rta_from_category_links', $rta_from_category_links );
			update_option( 'rta_from_post_edit_links', $rta_from_post_edit_links );
			update_option( 'rta_from_edit_comment_links', $rta_from_edit_comment_links );
			$message = __( "Remove Title Attributes Settings Updated", 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . "</p></div>";
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'asl' ) {
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
			$notice = ( $asl_visible == 'on' ) ? "<p>" . __( 'WP Accessibility does not provide any styles for visible skiplinks. You can still set the look of the links using the textareas provided, but all other layout must be assigned in your theme.', 'wp-accessibility' ) . "</p>" : '';

			update_option( 'asl_styles_focus', $asl_styles_focus );
			update_option( 'asl_styles_passive', $asl_styles_passive );
			$message = __( "Add Skiplinks Settings Updated", 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . "</p>$notice</div>";
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'misc' ) {
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
			$wpa_admin_css               = ( isset( $_POST['wpa_admin_css'] ) ) ? 'on' : '';
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
			update_option( 'wpa_admin_css', $wpa_admin_css );
			update_option( 'wpa_row_actions', $wpa_row_actions );
			update_option( 'wpa_diagnostics', $wpa_diagnostics );
			update_option( 'wpa_insert_roles', $wpa_insert_roles );
			update_option( 'wpa_complementary_container', $wpa_complementary_container );
			$message = __( "Miscellaneous Accessibility Settings Updated", 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . "</p></div>";
		}
		
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'toolbar' ) {
			$wpa_toolbar                 = ( isset( $_POST['wpa_toolbar'] ) ) ? 'on' : '';
			$wpa_toolbar_size            = ( isset( $_POST['wpa_toolbar_size'] ) ) ? $_POST['wpa_toolbar_size'] : '';
			$wpa_alternate_fontsize      = ( isset( $_POST['wpa_alternate_fontsize'] ) ) ? 'on' : '';
			$wpa_widget_toolbar          = ( isset( $_POST['wpa_widget_toolbar'] ) ) ? 'on' : '';
			$wpa_toolbar_gs              = ( isset( $_POST['wpa_toolbar_gs'] ) ) ? 'on' : '';
			$wpa_toolbar_fs              = ( isset( $_POST['wpa_toolbar_fs'] ) ) ? 'off' : '';
			$wpa_toolbar_ct              = ( isset( $_POST['wpa_toolbar_ct'] ) ) ? 'off' : '';
			$wpa_toolbar_default         = ( isset( $_POST['wpa_toolbar_default'] ) ) ? $_POST['wpa_toolbar_default'] : '';
			$wpa_toolbar_right           = ( isset( $_POST['wpa_toolbar_right'] ) ) ? 'on' : '';
			$wpa_toolbar_mobile          = ( isset( $_POST['wpa_toolbar_mobile'] ) ) ? 'on' : '';
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
			$message = __( "Toolbar Settings Updated", 'wp-accessibility' );

			return "<div class='updated'><p>" . $message . "</p></div>";
		}	
	} else {
		return;
	}
}

function wpa_admin_menu() {
	echo wpa_update_settings(); ?>
	<div class="wrap">
		<h2><?php _e( 'WP Accessibility: Settings', 'wp-accessibility' ); ?></h2>

<div id="wpa_settings_page" class="postbox-container" style="width: 70%">
	<div class="metabox-holder">
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Add Skiplinks', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<?php if ( wpa_accessible_theme() && get_option( 'asl_enable' ) != 'on' ) { ?>
						<p>
							<?php _e( 'Your <code>accessibility-ready</code> theme has skip links built in.', 'wp-accessibility' ); ?>
						</p>
					<?php } else { ?>				
					<form method="post"
					      action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Configure Skiplinks', 'wp-accessibility' ); ?></legend>
							<ul>
								<li><input type="checkbox" id="asl_enable"
								           name="asl_enable" <?php if ( get_option( 'asl_enable' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="asl_enable"><?php _e( 'Enable Skiplinks', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="asl_visible"
								           name="asl_visible" <?php if ( get_option( 'asl_visible' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="asl_visible"><?php _e( 'Skiplinks always visible', 'wp-accessibility' ); ?></label>
								</li>
								<li><label
										for="asl_content"><?php _e( 'Skip to Content link target (ID of your main content container)', 'wp-accessibility' ); ?></label>
									<input type="text" id="asl_content" name="asl_content"
									       value="<?php esc_attr_e( get_option( 'asl_content' ) ); ?>"/></li>
								<li><label
										for="asl_navigation"><?php _e( 'Skip to Navigation link target (ID of your main navigation container)', 'wp-accessibility' ); ?></label>
									<input type="text" id="asl_navigation" name="asl_navigation"
									       value="<?php esc_attr_e( get_option( 'asl_navigation' ) ); ?>"/></li>
								<li><label
										for="asl_sitemap"><?php _e( 'Site Map link target (URL for your site map)', 'wp-accessibility' ); ?></label><input
										type="text" id="asl_sitemap" name="asl_sitemap" size="44"
										value="<?php esc_attr_e( get_option( 'asl_sitemap' ) ); ?>"/></li>
								<li><label
										for="asl_extra_target"><?php _e( 'Add your own link (link or container ID)', 'wp-accessibility' ); ?></label>
									<input type="text" id="asl_extra_target" name="asl_extra_target"
									       value="<?php esc_attr_e( get_option( 'asl_extra_target' ) ); ?>"/> <label
										for="asl_extra_text"><?php _e( 'Link text for your link', 'wp-accessibility' ); ?></label>
									<input type="text" id="asl_extra_text" name="asl_extra_text"
									       value="<?php esc_attr_e( get_option( 'asl_extra_text' ) ); ?>"/></li>
								<li><label
										for="asl_styles_focus"><?php _e( 'Styles for Skiplinks when they have focus', 'wp-accessibility' ); ?></label><br/>
									<textarea name='asl_styles_focus' id='asl_styles_focus' cols='60'
									          rows='4'><?php esc_attr_e( stripslashes( get_option( 'asl_styles_focus' ) ) ); ?></textarea>
								</li>
								<?php if ( get_option( 'asl_visible' ) != 'on' ) {
									$disabled = " disabled='disabled' style='background: #eee;'";
									$note     = ' ' . __( '(Not currently visible)', 'wp-accessibility' );
								} else {
									$disabled = $note = '';
								} ?>
								<li><label
										for="asl_styles_passive"><?php _e( 'Styles for Skiplinks without focus', 'wp-accessibility' );
										echo $note; ?></label><br/>
									<textarea name='asl_styles_passive' id='asl_styles_passive' cols='60'
									          rows='4'<?php echo $disabled; ?>><?php echo stripslashes( get_option( 'asl_styles_passive' ) ); ?></textarea>
								</li>
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="asl"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Update Skiplink Settings', 'wp-accessibility' ) ?>"/></p>
					</form>
					<?php } ?>					
				</div>
			</div>
			<div class="postbox">
				<h3 id="toolbar"><?php _e( 'Accessibility Toolbar Settings', 'wp-accessibility' ); ?></h3>
				<div class="inside">
					<form method="post" action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<ul>
							<li><input type="checkbox" id="wpa_toolbar"
									   name="wpa_toolbar" <?php if ( get_option( 'wpa_toolbar' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_toolbar"><?php _e( 'Add Accessibility toolbar with fontsize adjustment and contrast toggle', 'wp-accessibility' ); ?></label>
								<ul>
									<li><input type="checkbox" id="wpa_toolbar_fs"
											   name="wpa_toolbar_fs" <?php if ( get_option( 'wpa_toolbar_fs' ) == "off" ) {
											echo 'checked="checked" ';
										} ?>/> <label
											for="wpa_toolbar_fs"><?php _e( 'Exclude font size toggle from Accessibility toolbar', 'wp-accessibility' ); ?></label>
									</li>
									<li><input type="checkbox" id="wpa_toolbar_ct"
											   name="wpa_toolbar_ct" <?php if ( get_option( 'wpa_toolbar_ct' ) == "on" ) {
											echo 'checked="checked" ';
										} ?>/> <label
											for="wpa_toolbar_ct"><?php _e( 'Exclude contrast toggle from Accessibility toolbar', 'wp-accessibility' ); ?></label>
									</li>
									<li><input type="checkbox" id="wpa_toolbar_gs"
											   name="wpa_toolbar_gs" <?php if ( get_option( 'wpa_toolbar_gs' ) == "on" ) {
											echo 'checked="checked" ';
										} ?>/> <label
											for="wpa_toolbar_gs"><?php _e( 'Include grayscale toggle with Accessibility toolbar', 'wp-accessibility' ); ?></label>
									</li>									
								</ul>
							</li>
							<li>
								<label for="wpa_toolbar_default"><?php _e( 'Toolbar location (ID attribute)', 'wp-accessibility' ); ?></label> <input type="text" id="wpa_toolbar_default" name="wpa_toolbar_default" value="<?php esc_attr_e( get_option( 'wpa_toolbar_default' ) ); ?>" />
							</li>							
							<?php
							$size = get_option( 'wpa_toolbar_size' );
							?>
							<li><label
									for="wpa_toolbar_size"><?php _e( 'Toolbar font size', 'wp-accessibility' ); ?></label>
								<select name='wpa_toolbar_size' id='wpa_toolbar_size'>
									<option value=''><?php _e( 'Default size', 'wp-accessibility' ); ?></option>
									<?php
									for ( $i = 1; $i <= 2.5; ) {
										$current       = $i . 'em';
										$selected_size = ( $current == $size ) ? ' selected="selected"' : '';
										echo "<option value='$i" . "em'$selected_size>$i em</option>";
										$i = $i + .1;
									}
									?>
								</select>
							</li>
							<li><input type="checkbox" id="wpa_alternate_fontsize"
									name="wpa_alternate_fontsize" <?php if ( get_option( 'wpa_alternate_fontsize' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
									for="wpa_alternate_fontsize"><?php _e( 'Use alternate font resizing stylesheet', 'wp-accessibility' ); ?></label>
							</li>								
							<li><input type="checkbox" id="wpa_widget_toolbar"
									   name="wpa_widget_toolbar" <?php if ( get_option( 'wpa_widget_toolbar' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_widget_toolbar"><?php _e( 'Support Accessibility toolbar as shortcode or widget', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_toolbar_right"
									   name="wpa_toolbar_right" <?php if ( get_option( 'wpa_toolbar_right' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_toolbar_right"><?php _e( 'Place toolbar on right side of screen.', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_toolbar_mobile"
									   name="wpa_toolbar_mobile" <?php if ( get_option( 'wpa_toolbar_mobile' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_toolbar_mobile"><?php _e( 'Hide toolbar on small screens.', 'wp-accessibility' ); ?></label>
							</li>								
						</ul>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="toolbar" />
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Update Toolbar Settings', 'wp-accessibility' ) ?>"/></p>
					</form>
				</div>
			</div>
			<div class="postbox">
				<h3 id="contrast"><?php _e( 'Miscellaneous Accessibility Settings', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<form method="post" action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<ul>
							<?php if ( !wpa_accessible_theme() ) { ?>
							<li><input type="checkbox" id="wpa_lang"
									   name="wpa_lang" <?php if ( get_option( 'wpa_lang' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_lang"><?php _e( 'Add Site Language and text direction to HTML element', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_more"
									   name="wpa_more" <?php if ( get_option( 'wpa_more' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_more"><?php _e( 'Add post title to "more" links.', 'wp-accessibility' ); ?></label>
								<label for="wpa_continue"><?php _e( 'Continue reading text', 'wp-accessibility' ); ?></label>
								<input type="text" id="wpa_continue" name="wpa_continue" value="<?php esc_attr_e( get_option( 'wpa_continue' ) ); ?>"/></li>
							<li><input type="checkbox" id="wpa_insert_roles"
									   name="wpa_insert_roles" <?php if ( get_option( 'wpa_insert_roles' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_insert_roles"><?php _e( 'Add landmark roles to HTML5 structural elements', 'wp-accessibility' ); ?></label><br/><label
									for="wpa_complementary_container"><?php _e( 'ID for complementary role', 'wp-accessibility' ); ?></label><input
									type="text" id="wpa_complementary_container" name="wpa_complementary_container"
									value="#<?php esc_attr_e( get_option( 'wpa_complementary_container' ) ); ?>"/>
							</li>
							<li>
								<input type="checkbox" id="wpa_labels" name="wpa_labels" <?php checked( get_option( 'wpa_labels'), 'on' ); ?> /> <label for='wpa_labels'><?php _e( 'Automatically Label WordPress search form and comment forms', 'wp-accessibility' ); ?></label>
							</li>
							<?php } else { ?>
								<li><?php _e( '<strong>Four disabled features:</strong> Site language, continue reading text, landmark roles and standard form labels are defined in your <code>accessibility-ready</code> theme.', 'wp-accessibility' ); ?></li>
							<?php } ?>
							<li><input type="checkbox" id="wpa_target"
									   name="wpa_target" <?php if ( get_option( 'wpa_target' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_target"><?php _e( 'Remove target attribute from links', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_search"
									   name="wpa_search" <?php if ( get_option( 'wpa_search' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_search"><?php _e( 'Force search error on empty search submission (theme must have search.php template)', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_tabindex"
									   name="wpa_tabindex" <?php if ( get_option( 'wpa_tabindex' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_tabindex"><?php _e( 'Remove tabindex from focusable elements', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_underline"
									   name="wpa_underline" <?php if ( get_option( 'wpa_underline' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_underline"><?php _e( 'Force underline on all links', 'wp-accessibility' ); ?></label>
							</li>
							<li><label
									for="wpa_longdesc"><?php _e( 'Long Description UI', 'wp-accessibility' ); ?></label>
								<select id="wpa_longdesc" name="wpa_longdesc">
									<option value='link'<?php if ( get_option( 'wpa_longdesc' ) == "link" ) {
										echo 'selected="selected" ';
									} ?>><?php _e( 'Link to description', 'wp-accessibility' ); ?></option>
									<option value='jquery'<?php if ( get_option( 'wpa_longdesc' ) == "jquery" ) {
										echo 'selected="selected" ';
									} ?>><?php _e( 'Button trigger to overlay image', 'wp-accessibility' ); ?></option>
									<option value='false'
											<?php if ( get_option( 'wpa_longdesc' ) == "false" || ! get_option( 'wpa_longdesc' ) ) {
												echo 'selected="selected"';
											} ?>><?php _e( 'Browser defaults only', 'wp-accessibility' ); ?></option>
								</select>
							</li>
							<li><input type="checkbox" id="wpa_longdesc_featured"
									   name="wpa_longdesc_featured" <?php if ( get_option( 'wpa_longdesc_featured' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_longdesc_featured"><?php _e( 'Support <code>longdesc</code> on featured images', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_admin_css"
									   name="wpa_admin_css" <?php if ( get_option( 'wpa_admin_css' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_admin_css"><?php _e( 'Enable WordPress Admin stylesheet', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_row_actions"
									   name="wpa_row_actions" <?php if ( get_option( 'wpa_row_actions' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_row_actions"><?php _e( 'Make row actions always visible', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_image_titles"
									   name="wpa_image_titles" <?php if ( get_option( 'wpa_image_titles' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_image_titles"><?php _e( 'Remove title attributes inserted into post content and featured images.', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_diagnostics"
									   name="wpa_diagnostics" <?php if ( get_option( 'wpa_diagnostics' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_diagnostics"><?php _e( 'Enable diagnostic CSS', 'wp-accessibility' ); ?></label>
							</li>
							<li><input type="checkbox" id="wpa_focus"
									   name="wpa_focus" <?php if ( get_option( 'wpa_focus' ) == "on" ) {
									echo 'checked="checked" ';
								} ?>/> <label
									for="wpa_focus"><?php _e( 'Add outline to elements on keyboard focus', 'wp-accessibility' ); ?></label>
								<label
									for="wpa_focus_color"><?php _e( 'Outline color (hexadecimal, optional)', 'wp-accessibility' ); ?></label><input
									type="text" id="wpa_focus_color" name="wpa_focus_color"
									value="#<?php esc_attr_e( get_option( 'wpa_focus_color' ) ); ?>"/></li>
						</ul>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="misc"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Update Miscellaneous Settings', 'wp-accessibility' ) ?>"/></p>
					</form>
				</div>
			</div>
			<div class="postbox">
				<h3><?php _e( 'Remove Title Attributes', 'wp-accessibility' ); ?></h3>

				<div class="inside">
				<?php wpa_accessible_theme(); ?>
					<p>
						<?php _e( 'As of WordPress 4.0, the only globally added title attributes are in the WordPress tag cloud, showing the number of posts with that tag, and on the categories list, if the category has a term description.', 'wp-accessibility' ); ?>
					</p>
					<form method="post"
					      action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Remove title attributes from:', 'wp-accessibility' ); ?></legend>
							<ul>
								<?php if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) { ?>
								<li>
									<input type="checkbox" id="rta_from_nav_menu"
										name="rta_from_nav_menu" <?php if ( get_option( 'rta_from_nav_menu' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_nav_menu"><?php _e( 'Nav menus', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>
								<li>
									<input type="checkbox" id="rta_from_page_lists"
										name="rta_from_page_lists" <?php if ( get_option( 'rta_from_page_lists' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_page_lists"><?php _e( 'Page lists', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>
								<li>
									<input type="checkbox" id="rta_from_category_links"
										name="rta_from_category_links" <?php if ( get_option( 'rta_from_category_links' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_category_links"><?php _e( 'Category links', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>
								<li>
									<input type="checkbox" id="rta_from_post_edit_links"
										name="rta_from_post_edit_links" <?php if ( get_option( 'rta_from_post_edit_links' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_post_edit_links"><?php _e( 'Post edit links', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>
								<li>
									<input type="checkbox" id="rta_from_edit_comment_links"
										name="rta_from_edit_comment_links" <?php if ( get_option( 'rta_from_edit_comment_links' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_edit_comment_links"><?php _e( 'Edit comment links', 'wp-accessibility' ); ?>
										(<?php echo ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) ? __( 'Obsolete since WordPress 3.8', 'wp-accessibility' ) : ''; ?>
										)</label></li>	
								<?php } ?>							
								<?php if ( version_compare( get_bloginfo( 'version' ), '4.0', '<' ) ) { ?>								
								<li><input type="checkbox" id="rta_from_category_lists"
								           name="rta_from_category_lists" <?php if ( get_option( 'rta_from_category_lists' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_category_lists"><?php _e( 'Category lists', 'wp-accessibility' ); ?></label>
								</li>
			
								<?php } ?>
								<li><input type="checkbox" id="rta_from_tag_clouds"
								           name="rta_from_tag_clouds" <?php if ( get_option( 'rta_from_tag_clouds' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_tag_clouds"><?php _e( 'Tag clouds', 'wp-accessibility' ); ?></label>
								</li>
								<li><input type="checkbox" id="rta_from_archive_links"
								           name="rta_from_archive_links" <?php if ( get_option( 'rta_from_archive_links' ) == "on" ) {
										echo 'checked="checked" ';
									} ?>/> <label
										for="rta_from_archive_links"><?php _e( 'Archive links', 'wp-accessibility' ); ?></label>
								</li>	
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="rta"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Update Title Attribute Settings', 'wp-accessibility' ) ?>"/></p>
					</form>
				</div>
			</div>			
			<div class="postbox">
				<h3><?php _e( 'Color Contrast Tester', 'wp-accessibility' ); ?></h3>

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
						$results = "
			<div class='updated notice'>";
						$results .= "<p class=\"stats wcag2\">" . sprintf( __( 'Luminosity Contrast Ratio for <code>#%2$s</code> and <code>#%3$s</code> is <strong>%1$s</strong> (Threshold: greater than 7:1 for AAA, 4.5:1 for AA)', 'wp-accessibility' ), $l_contrast, $hex1, $hex2 ) . "</p><p>";
						if ( $luminance_raw >= 7 ) {
							$results .= __( "The colors compared <strong>pass</strong> the relative luminosity test at level AAA.", 'wp-accessibility' );
						}
						if ( $luminance_raw >= 4.5 && $luminance_raw < 7 ) {
							$results .= __( "The colors compared <strong>pass</strong> the relative luminosity test at level AA.", 'wp-accessibility' );
						}
						if ( $luminance_raw >= 3 && $luminance_raw < 4.5 ) {
							$results .= __( "The colors compared pass the relative luminosity test <strong>only when used in large print</strong> situations (greater than 18pt text or 14pt bold text.)", 'wp-accessibility' );
						}
						if ( $luminance_raw < 3 ) {
							$results .= __( "The colors compared <strong>do not pass</strong> the relative luminosity test.", 'wp-accessibility' );
						}
						$results .= " <a href='#contrast'>" . __( 'Test another set of colors', 'wp-accessibility' ) . "</a>";
						$results .= "</p>";
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
					<form method="post"
					      action="<?php echo admin_url( 'options-general.php?page=wp-accessibility/wp-accessibility.php' ); ?>">
						<fieldset>
							<legend><?php _e( 'Test of relative luminosity', 'wp-accessibility' ); ?></legend>
							<ul>
								<li class='fore'>
									<div id="fore"></div>
									<label
										for="color1"><?php _e( 'Foreground color', 'wp-accessibility' ); ?></label><br/><input
										type="text" name="color" value="#<?php esc_attr_e( $hex1 ); ?>" size="34" id="color1"/>
								</li>
								<li class='back'>
									<div id="back"></div>
									<label
										for="color2"><?php _e( 'Background color', 'wp-accessibility' ); ?></label><br/><input
										type="text" name="color2" value="#<?php esc_attr_e( $hex2 ); ?>" size="34" id="color2"/>
								</li>
							</ul>
						</fieldset>
						<p>
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpa-nonce' ); ?>"/>
							<input type="hidden" name="action" value="contrast"/>
						</p>

						<p><input type="submit" name="wpa-settings" class="button-primary"
						          value="<?php _e( 'Check Color Contrast', 'wp-accessibility' ) ?>"/></p>
					</form>
				</div>
			</div>
			<div class="postbox" id="get-support">
				<h3><?php _e( 'Get Plug-in Support', 'wp-accessibility' ); ?></h3>

				<div class="inside">
				<div class='wpa-support-me'>
					<p>
						<?php printf(
							__( 'Please, consider <a href="%s">making a donation</a> to support WP Accessibility!', 'wp-accessibility' )
						, "https://www.joedolson.com/donate/" ); ?>
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
				<h3><?php _e( 'Support this Plugin', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<p>
						<a href="https://twitter.com/intent/follow?screen_name=joedolson" class="twitter-follow-button"
						   data-size="small" data-related="joedolson">Follow @joedolson</a>
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
								<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif"
								       name="submit" alt="Donate"/>
								<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Accessibility References', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<ul>
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
				<h3><?php _e( 'Contributing References', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<ul>
						<li><a href="http://www.accessibleculture.org/articles/2010/08/continue-reading-links-in-wordpress/">Continue Reading Links in WordPress</a></li>
						<li><a href="http://www.mothereffingtoolconfuser.com">Mother Effing Tool Confuser</a></li>
						<li><a href="http://wordpress.org/extend/plugins/remove-title-attributes/">Remove Title Attributes</a></li>
						<li><a href="http://accessites.org/site/2008/11/wordpress-and-accessibility/#comment-2926">WordPress and Accessibility (Comment)</a></li>
						<li><a href="http://wordpress.org/extend/plugins/img-title-removal/">IMG Title Removal</a></li>
						<li><a href="https://github.com/clrux/a11y">Accessibility Toolbar</a></li>
						<li><a href="https://wordpress.org/plugins/long-description-for-image-attachments/">WordPress Long Description</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e( 'Customization Notes', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<p>
						<?php _e( 'It is almost impossible for the Accessibility Toolbar to guarantee a good result for large text or high contrast modes. Author your own high-contrast styles by placing a stylesheet called <code>a11y-contrast.css</code> in your Theme\'s stylesheet directory.', 'wp-accessibility' ); ?>
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
				<h3><?php _e( 'Your Theme', 'wp-accessibility' ); ?></h3>

				<div class="inside">
					<p>
					<?php _e( "You're using a theme reviewed as <code>accessibility-ready</code> by the WordPress theme review team. Some options have been disabled in WP Accessibility because your theme has taken care of that issue.", 'wp-accessibility' ); ?>
					</p>
					<p>
					<?php printf( __( 'Read more about the <a href="%s">WordPress accessibility-ready tag</a>', 'wp-accessibility' ), "https://make.wordpress.org/themes/handbook/review/accessibility/" ); ?>
					</p>
				</div>
			</div>
		</div>
		<?php } ?>		
		
	</div>
</div>

	</div><?php
}