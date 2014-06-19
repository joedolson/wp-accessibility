<?php

/*
 * Default template that displays the ZG Accessibility Bar. Over-ride this template in your own theme or child theme if you need to change the
 * contents or the structure. If all you wish to do is re-skin it, consider modifying the accessibility-bar.css file in your own theme, or
 * simply override the classes defined therein.
 *
 * This template also gets the following variables:
 * @param Boolean $collapsed_class Class that is added to the accessibility bar when the bar is collapsed.
 */
$locale = get_locale();
 ?>
		<div id="accessibilityBar" role="toolbar"<?php echo $collapsed_class ?>>
			<div id="accessibilityBarContent">
				<div id="skip-to">
					<label for="quickaccess">
						<strong><span class="accessibility-title-visible aria-hidden" aria-hidden="true"><?php _e( 'Accessibility', ZG_Accessibility_Bar::TD )?></span><span class="accessibility-title-nonvisible accessibility-nonvisible"> <?php _ex( 'Skip to', 'Screen reader text', ZG_Accessibility_Bar::TD )?></span>:</strong>
					</label>
					<ul id="quickaccess">
						<li><a href="<?php echo home_url( _x( 'accessibility-policy', 'Page slug', ZG_Accessibility_Bar::TD ) ) ?>" accesskey="0" title="<?php _ex( 'Access key: ', 'Link title attr', ZG_Accessibility_Bar::TD )?>Alt+0"><?php _e( 'Accessibility Policy', ZG_Accessibility_Bar::TD )?></a></li>
						<li><a href="#content" accesskey="1" title="<?php _ex( 'Access key: ', 'Link title attr', ZG_Accessibility_Bar::TD )?>Alt+1"><?php _e( 'Main Content', ZG_Accessibility_Bar::TD ) ?></a></li>
						<li><a href="#site-navigation" accesskey="2" title="<?php _ex( 'Access key: ', 'Link title attr', ZG_Accessibility_Bar::TD )?>Alt+2"><?php _e( 'Main Menu', ZG_Accessibility_Bar::TD ) ?></a></li>
						<li><a href="<?php echo home_url( _x( 'contact', 'Page slug', ZG_Accessibility_Bar::TD ) ) ?>" accesskey="3" title="<?php _ex( 'Access key: ', 'Link title attr', ZG_Accessibility_Bar::TD )?>Alt+3"><?php _e( 'Contact', ZG_Accessibility_Bar::TD )?></a></li>
					</ul>
				</div>


				<div id="controlsMenu">
					<label for="controls">
						<strong><?php _e( 'Text size', ZG_Accessibility_Bar::TD ) ?></strong>
					</label>
					<ul id="accessibilityControls">
						<li id="textLarge"><a href="#" title="<?php _e( 'increase text size', ZG_Accessibility_Bar::TD )?>">+A</a></li>
						<li id="textSmall"><a href="#" title="<?php _e( 'decrease text size', ZG_Accessibility_Bar::TD )?>">-A</a></li>
						<li id="textReload"><a href="#" title="<?php _e ( 'reset text size', ZG_Accessibility_Bar::TD )?>">100%</a></li>
						<li class="contrastToggle" id="high_contrast_off"><a href="#"  title="<?php _e ( 'toggle high contrast mode', ZG_Accessibility_Bar::TD )?>">High Contrast Mode</a></li>
					</ul>
				</div>

				<?php if ( function_exists( 'the_cookie_crumb' ) ) { ?>
				<div id="cookie-crumb">
					<label for="zg-crumb">
						<strong><?php _e( 'History: ', ZG_Accessibility_Bar::TD )?></strong>
					</label>
					<?php the_cookie_crumb();  // uses ZG_CookieCrumb plugin ?>
				</div>
				<?php } ?>
			</div><!-- /#accessibilityBarContent -->

			<?php ZG_Accessibility_Bar::the_expand_collapse_buttons(); ?>
		</div><!-- #accessibilityBar -->