=== WP Accessibility ===
Contributors: joedolson
Donate link: https://www.joedolson.com/donate/
Tags: accessibility, wcag, a11y, section508, alt text
Requires at least: 5.9
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.2.2
Text Domain: wp-accessibility
License: GPLv3

WP Accessibility fixes common accessibility issues in your WordPress site.

== Description ==

This plug-in helps with a variety of common accessibility problems in WordPress themes. While most accessibility issues can't be addressed without directly changing your theme, WP Accessibility adds a number of helpful accessibility features with a minimum amount of setup or expert knowledge.

WP Accessibility is not intended to make your site compatible with any accessibility guidelines.

All features can be disabled according to your theme's needs. For advanced users, all of the functions based on modifying stylesheets can be customized using your own custom styles by placing the appropriate stylesheet in your theme directory.

= Theme Accessibility Features added by WP Accessibility =

These are features that address issues caused by inaccessible themes.

* Add skip links with user-defined targets. (Customizable targets and appearance.)
* Add language and text direction attributes to your HTML attribute if missing.
* Add an outline to the keyboard focus state for focusable elements.
* Add a long description to images. Use the image's "Description" field to add long descriptions.
* Enforcement for alt attributes on images in the Classic editor.
* Identify images without alt attributes in the Media Library
* Add labels to standard WordPress form fields (search, comments)
* Add post titles to "read more" links.
* Remove tabindex from elements that are focusable. (Also fixes plugin-caused problems.)
* Remove user-scalable=no to allow resizing.

= WordPress Core Accessibility Issues fixed by WP Accessibility =

These are features that address issues caused by current or past WordPress core accessibility issues. (Issues added in content, such as target or title attributes, are persistent even when WordPress is updated.)

* Force a search page error when a search is made with an empty text string. (If your theme has a search.php template.)
* Remove redundant title attributes from tag clouds.
* Disable default enabling of full screen block editor.

Content specific fixes:

* Strip title attributes from images inserted into content.
* Remove the target attribute from links.

= Accessibility Tools in WP Accessibility: =

These are tools provided to help you identify issues you may need to fix.

* Test the color contrast between two provided hexadecimal color values.
* Enable diagnostic CSS to show CSS-detectable problems in visual editor or on front-end of site.
* Search your media library on content in alt text fields.

Learn more! <a href="https://docs.joedolson.com/wp-accessibility/">Read about the accessibility issues corrected</a> by WP Accessibility!

The plug-in is intended to help with deficiencies commonly found in themes and to solve some issues in WordPress core. It can't correct every problem (by a long shot), but provides tools to fix some issues, supplement the accessibility of your site, or identify problems.

= Statistics Collection =

WP Accessibility includes a statistics collection feature to help you identify how WP Accessibility is used on your site. This package does not collect any personally identifying data.

<a href="https://docs.joedolson.com/wp-accessibility/2023/11/16/wp-accessibility-statistics/">Learn more about WP Accessibility statistics</a>.

= Contribute! =

* <a href="https://translate.wordpress.org/projects/wp-plugins/wp-accessibility">Help Translate WP Accessibility</a>
* <a href="https://github.com/joedolson/wp-accessibility">Contribute to WP Accessibility</a>

== Installation ==

1. Download the plugin's zip file, extract the contents, and upload them to your wp-content/plugins folder.
2. Login to your WordPress dashboard, click "Plugins", and activate WP Accessibility.
3. Customise your settings on the Settings > WP Accessibility page.

== Changelog ==

= Future =

[Suggest a change!](https://github.com/joedolson/wp-accessibility/issues/)

= 2.2.2 =

* Bug fix: Rewrite in JS changed the data format sent to the server for stats.
* Bug fix: Incorrect selector for tracking stats on fontsize switcher.
* Bug fix: Internal label in stats incorrect for contrast changes.

= 2.2.1 =

* Bug fix: Undeclared variable in JS.
* Bug fix: Incorrect variable used to report errors in console.
* Change: Only add longdesc image block variation if a UI is enabled.

= 2.2.0 =

* Feature: Option for automatic insertion of play/pause button on autoplay videos without controls.
* Feature: Automatically pause autoplay videos if prefers-reduced-motion enabled.
* Change: Refactor all front-end JS to remove jQuery dependency.
* Change: Design changes to alt text and long description buttons for consistency.
* Change: Expand focusable element selector list: more thorough, now does not select the a11y toolbar itself.
* Change: Add design CSS for longdesc link.
* Change: Omit buttons with `role="button"` from fake button selector.
* Change: Keep title attributes on images if they are different from the alt text.
* Change: Also remove target attributes on URL fragments.
* Change: Find non-link elements with `role="link"` and make focusable.
* Change: Make links using `a` with `role="link"` and no href focusable.
* Change: Add link styles on `role="link"`.
* Bug Fix: Apply grayscale on `html` element to prevent shifts in position.
* Bug Fix: Modernize screen reader text classes.
* Bug Fix: Change how version number is handled. Automatic updates don't execute activation, so version wasn't getting incremented.

= 2.1.19 =

* Bug fix: Don't load admin JS outside of WP Accessibility settings.
* Bug fix: Allow text selection on headings.
* Bug fix: If toolbar custom location is invalid, change attachment to body.
* Change: Remove an extraneous fieldset in settings.
* Change: Change download icon to universal access in stats.
* Change: Set all WP A11y scripts to use the `defer` loading strategy.

= 2.1.18 =

* Feature: On block themes, add `aria-describedby` to continue reading links to provide expanded context.
* Bug fix: Only fetch toolbar bounds when the toolbar is present.
* Bug fix: Use `autorefresh` parameter on codemirror field to fix rendering.

= 2.1.17 =

* Bug fix: When using the default attachment location, passed empty class to classList.
* Bug fix: When using a custom attachment location, do not adjust scroll position.

= 2.1.16 =

* Bug fix: Missed class change caused tooltips to not be shown when display side changed.
* Bug fix: Override fixed positioning if a custom attachment location is set.
* Bug fix: Update longdesc.js minified version, missed in 2.1.14.
* Build tools: Update esbuild targets from edge16 to edge18.

= 2.1.15 =

* Bug fix: Wrap block editor assets in `is_admin()` conditional to prevent front-end rendering.

= 2.1.14 =

* Add Bluesky & set `aria-hidden` on social SVGs.
* Fix unclosed `span`.
* Bug fix fetching ID for alt attribute toggles on pages.
* Disable `h1` in block editor by default; add setting to enable.
* Add `font-display: swap` for Accessibility icons.
* Change `enqueue_editor_block_assets` action to `enqueue_block_assets`
* Verify that `$browser` is an object when rendering browser stats.
* Replace `json_encode` with `wp_json_encode`.
* Fix bug with translation used before `init`.
* Fix duplicate ID in content summary container. Props @ryokuhi.

= 2.1.13 =

* Remove textdomain loader and translations. These have been out of date for years.
* Don't throw an error if 'ltr' not set for 'dir' attribute, as that is the default value.
* Remove Twitter follow button
* Add updated socials
* Add LinkedIn course
* Update tested to for 6.7.

= 2.1.12 =

* Bug fix: Update editor styles to have less impact on block editor styles.
* Bug fix: Log out text was not internationalized.
* Bug fix: Default CSS toggle broken.
* Add: Support for classic editor galleries with alt text warnings.
* Change: Update URLs in various locations to https references.
* Change: New setting to disable admin logout link.
* Update: Misc. minor admin settings design changes.

= 2.1.11 =

* Bug fix: Don't strip target=_blank on Facebook links.
* Change: Minor text change in accessibility stats settings.
* Accessibility: auto-scroll if focus lands on element obsucred by toolbar.

= 2.1.10 =

* Bug fix: Fix errors thrown in stats reporting.
* Bug fix: Version the high contrast CSS.
* Bug fix: Add override specificity for block theme colors.
* Change: Update font-resizing values to make changes more consistent.

= 2.1.9 =

* Bug fix: Let Google Translate plugin keep title attributes that are used for CSS hooks.
* Bug fix: Don't double-add '#' in focus color input.
* Change: Use CSS variables for font resizing.
* Feature: Add label fix when `label` element present but has no text.
* Feature: Live Preview support via Playground.

= 2.1.8 =

* Update to PHPCS 3
* Fix error thrown if json_decode returns null value.

= 2.1.7 =

* Bug fix: Two incorrect placeholder formats in `sprintf` call.

=  2.1.5, 2.1.6 =

* Security fix: XSS vulnerability caused by a debugging statement left in place. Props Joshua Bixler.

= 2.1.4 =

* Bug fix: Incorrect type check caused most stats to display as 'no data'.
* Change: Only show first 5 changes on user stats in dashboard.

= 2.1.3 =

* Bug fix: Error thrown if a 3rd party is using `the_title` filters improperly.
* Bug fix: Handle case if passed data is invalid.
* Bug fix: Change dashboard widget function name to minimize conflict.
* Bug fix: Setting stats to 'none' should fully disable stats collection.
* Bug fix: Ensure that admin status is verified on server side when stats set to 'admin'.

= 2.1.2 =

* Bug fix: Build error caused JS errors when not running in SCRIPT_DEBUG.

= 2.1.1 =

* Bug fix: Comparison of new stats to old stats didn't remove timestamp, so comparison was always false.

= 2.1.0 =

* Bug fix: Fix label `for` attributes. Props @sabernhardt.
* Bug fix: Fix position of image alt attribute warnings.
* Bug fix: Prevent existence of alt warnings from breaking captions.
* Change: Render toolbar location relatively instead of absolutely.
* Change: Improve alt text tests.
* Change: Record count of occurrences for fake button/link tests.
* Change: Set `wpa_lang` option to on by default.
* Change: Switch auto labeling to always one.
* Change: Switch skiplink CSS to use default by default.
* Change: Remove setting for filtering title attributes and turn on by default.
* Change: Consolidate and minify JS.
* Feature: Add stats collection to provide view into what WP Accessibility is doing.

= 2.0.1 =

* Add `.et_smooth_scroll_disabled` to skip links to override Divi's inaccessible smooth scrolling.
* Add promotional affiliate links for Equalize Digital's Accessibility Checker

= 2.0.0 =

* New feature: Show alt attributes toggle on content images.
* New feature: Ensure users can adjust scale even if maximum-scale set to 1.0.
* New feature: Alt enforcement indicators now present in block editor.
* New feature: Flag missing captions or subtitles in uploaded videos in editor.
* Updated feature: [Remove title attributes](https://docs.joedolson.com/wp-accessibility/2022/10/29/remove-title-attributes/) now more intelligent.
* Updated feature: Modernized alt attribute enforcement tools.
* Change: Combine remediation scripting in wp-accessibility.js.
* Change: Disable accessibility-ready duplicating features if theme changed to accessibility-ready.
* Bug fix: aria-expanded missing in some cases for longdesc disclosures.
* Bug fix: Main JS file did not have a version number.
* Bug fix: Run feature JS after running remediation JS.
* Bug fix: Fix DOM ordering with image disclosure buttons.
* Retired language directory call. Translation files haven't been updated since version Oct 2014.

== Frequently Asked Questions ==

= WP Accessibility is inserting some information via javascript. Is this really accessible? =

Yes. It does require that the user is operating a device that has javascript support, but that encompasses the vast majority of devices and browsers today, including screen readers.

= I installed WP Accessibility and ran some tests, but I'm still getting errors WP Accessibility is supposed to correct. =

Even if WP Accessibility is running correctly, not all accessibility testing tools will be aware of the fixes. Here's a resource for more information: [Mother Effing Tool Confuser](http://mothereffingtoolconfuser.com/).

== Screenshots ==

1. Settings Page

== Upgrade Notice ==

* 2.1.6 Security Fix. Please update promptly.