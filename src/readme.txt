=== WP Accessibility ===
Contributors: joedolson
Donate link: http://www.joedolson.com/donate/
Tags: title, accessibility, accessible, navigation, wcag, a11y, section508, focus, alt text, labels, aria
Requires at least: 3.4.2
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 2.1.7
Text Domain: wp-accessibility
License: GPLv2 or later

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

= 1.9.2 =

* Bug fix: Alignment classes should only apply when wpa-ld class present.
* Bug fix: Escape widget title content.
* Bug fix: Set cookies with SameSite = Strict. Props [@ute-arbeit](https://github.com/joedolson/wp-accessibility/commits?author=ute-arbeit).
* Bug fix: Check that post_type param is in query object when searching alt attributes.

= 1.9.1 =

* Bug fix: Duplicate skiplink styles: if custom styles used, default settings could be appended to them as a duplicate.
* Filters: Add filters to configure deprecated skiplinks if necessary.
* Show default CSS when enabled for reference.

= 1.9.0 =

* Update: Significant reorganization of settings.
* Feature: Create & prepend simplified content summaries.
* Feature: Raise warning on long alt text.
* Feature: Generate color contrast via GET to make bookmarkable.
* Increase boldness of automatic focus outline.
* Set default focus outline color.
* Use wp_add_inline_styles for customizable CSS.
* Update automatic link underlines to skip `nav` elements.
* Bug fix: some incorrect textdomains.
* Bug fix: toolbar tooltips should be dismissable without changing focus or hover. Support `esc` key.
* Bug fix: Incorrect variable passed to color contrast, causing incorrect values.
* Auto generation of hook documentation: https://joedolson.github.io/wp-accessibility/

= 1.8.1 =

* Update: Fix accessibility issues in longdesc disclosure button.
* Add: style variations on longdesc disclosure to adjust position. (.top-left, .top-right, .bottom-right)
* Update tested to for WP 6.0.

= 1.8.0 =

* Update: tabindex check should not remove tabindex on `a` elements without `href`.
* Update: check for the button role on elements that are not focusable and add tabindex.
* Update: Larger longdesc button.
* Update admin script & css enqueuing.
* Change color picker.
* Make admin responsive.
* Admin text updates.
* Return false for accessible theme test if Oxygen Builder active.

= 1.7.14 =

* Bug fix: incorrectly called variable broke responsive toolbar.

= 1.7.13 =

* Bug fix: empty space in toolbar attachment point field created uncaught exception in JS.

= 1.7.12 =

* Bug fix: Improved automatic labeling; checks for existing aria-label or aria-labelledby values.
* Improved escaping and sanitizing.
* Change: add 'no-scroll' to skip links to prevent enfold from scrolling.
* Feature: Option to search media library on alternative text.
* Feature: Detect and replace user-scalable=no if found.

= 1.7.11 =

* Bug fix: Better handling of invalid toolbar attachment points.
* Change: Use title casing for Skip to Content link to clarify pronunciation by JAWS.
* New: Option to disable full screen block editor by default.

= 1.7.10 =

* Bug fix: If attachment ID not in classes, get from img ID attribute.
* Insert toolbar without jQuery.

= 1.7.9 =

* Bug fix: potentially undeclared variable in 1.7.8.

= 1.7.8 =

* Bug fix: Avoid showing longdesc interface if no description defined.
* Bug fix: Update screen reader text classes from antiquated versions.
* Removal: Adding role attributes to HTML5 structures is no longer significant.
* Removal: Remove option to make row actions visible. Now available via screen options in core.
* Removal: Remove tabindex=-1 on skiplink targets. No longer required.
* Improvement: Catch more potentially invalid or suspicious alt text patterns.
* Refactor: Move footer scripts into external JS file.

= 1.7.7 =

* Bug fix: Correct usage of wp_localize_script arguments.
* Change: Modernization of CSS for toolbar.
* Change: Add outline offset for forced focus states.
* Change: Tweaks for font resizing stylesheets.

= 1.7.6 =

* Bug fix: Broken class array query in longdesc.

= 1.7.5 =

* Bug fix: register_block_style only exists since WP 5.3; check function exists first.

= 1.7.4 =

* Change: mark `&nbsp;` as an invalid alt value.
* Remove .hndle class on admin headings.
* Fix jQuery incompatibility due to deprecated .toggle() event handler.
* Add support for long description in the block editor.

= 1.7.3 =

* Bug fix: script registration/enqueuing mis-ordered, breaking toolbar.

= 1.7.2 =

* Add style to div.aligncenter to control width

= 1.7.1 =

* Bump tested to value.

= 1.7.0 =

* Add: test for specific common types of invalid alt attribute and label as invalid.
* Change: Remove webkit skiplink polyfill (obsolete)
* Change: Move toolbar JS to external file.
* Change: Use wp inline style method for toolbar size.
* Bug fix: Incorrect variable type matching in toolbar size setting.
* Security: Minor authenticated XSS vulnerability in custom CSS.

= 1.6.10 =

* Based on support for screen-reader-text class and current support for aria-current, shifting to aria only.

= 1.6.9 =

* Different JS for aria-current; previous version could only work on a single element, not a collection.

= 1.6.8 =

* Update to RegEx filtering title attributes to avoid data-title attributes. Thanks to @nextendweb
* Update PHP test suite to eliminate PHP 5.3 tests.
* Higher min/max sizes for toolbar buttons
* Remove setting for aria-current; now always enabled. 

= 1.6.7 =

* Bug fix: error in generated CSS for skiplinks

= 1.6.6 =

* Per request, remove font sizing declaration for skip links. Inherit from site.
* Add default styles for visible skiplinks, rather than allowing to be unstyled.
* Remove tabindex application on section element from toolbar JS. (Not sure why it was there anyway.)
* Bug fix: automatic labeling injected double labels on fields with no ID and an implicit label.

= 1.6.5 =

* Bug fix: Widget output should not be styled.
* Bug fix: Remove duplicated (current) when both current-menu-item and -page-item present (props loopRW)

= 1.6.4 =

* Bug fix: Do not render longdesc UI when no longdesc provided.

= 1.6.3 =

* Bug fix: incorrect function name
* Bug fix: don't use POST data directly when value is known 

= 1.6.2 =

* Removed 'ScrollTo' JS.
* Changed toolbar font size setting from em to px for predictability.
* Updated 'diagnostic.css' to latest version.
* Removed 'create_function' for PHP 7.2 compatibility.
* Removed 'extract'
* Code updated to conform to WordPress PHP standards
* Code restructuring. 

= 1.6.1 =

* Fixed changelog, which read 1.5.11 instead of 1.6.0
* Fixed logic in widget version of toolbar so wouldn't use buttons unless requested
* Update 'tested to' version.

= 1.6.0 =

* Bug fix: incorrect CSS style reference on longdesc template
* Re-enable Toolbar on Edge (slightly different from other browsers, but functional.)
* Hide grayscale from non-admins.
* Add note reflecting above
* Add feature: identify current menu item

= 1.5.10 =

* Disable Toolbar on Edge due to page not found bug.
* Modifications to toolbar JS
* Remove title attribute settings remove attributes no longer being produced.
* Some code clean up and restructuring.

= 1.5.9 =

* Bug fix: another swapped options check, elsewhere. Sigh.

= 1.5.8 =

* Bug fix: swapped options check for fontsize & contrast toggles
* Compatibility checked with 4.7

= 1.5.7 =

* Remove menu role from toolbar
* Fixed toolbar button font size adjustment
* Updated screen reader text class to include clip-path and whitespace
* Minor toolbar design tweaks
* Removed WordPress admin stylesheet (obsolete)
* Misc display fixes in settings
* Remove 'Access Monitor' admin notice
* Bug fix: Missing space in featured img HTML
* Bug fix: Switch main heading to H1

= 1.5.6 =

* Feature: Add support for longdesc in featured images.
* Feature: Option to enable only a single element in toolbar

= 1.5.5 =

* Bug fix: use aria-pressed on toolbar buttons. Props Jose Manuel (https://github.com/joedolson/wp-accessibility/pull/7)
* Bug fix: Don't show logout link if user not logged in. Props @boonebgorges (https://github.com/joedolson/wp-accessibility/pull/9)
* Bug fix: Don't assume that images with long descriptions have classes
* Text change to better describe content title attribute stripping.
* Add aria-label to skip link navigation region.
* Add role=menu to toolbar.
* Update readme.txt

= 1.5.4 =

* Bug fix: .fontsize default classes used immediate children selector, which was incompatible with the switch to using html as the parent selector.
* Change: Added option to hide toolbar on small screens.
* UI Change: Split Toolbar & Miscellaneous settings into separate sections.
* Updated informational and contributing links
* Re-ordered settings groups to better reflect need.
* Split settings into separate plug-in file.

= 1.5.3 =

* Bug fix: incorrect assignment of fontsize class on subsequent pages.
* Removal of en_AU and nl_NL languages in favor of completed language packs

= 1.5.2 =

* Bug fix: Add RTL version of editor styles
* Bug fix: incorrect textdomain on donate request
* Bug fix: Issue with :focus state on skiplinks when not always visible
* Bug fix: Install with no default focus styles; add custom styles to end of default focus styles string
* Bug fix: search filter could prevent display of nav menus (props @jdelia & @GaryJones)
* Bug fix: extraneous anchor generated in longdesc format (https://github.com/joedolson/wp-accessibility/issues/4)
* Add alternate font resizing stylesheet for improved use with rems
* Add support for selective refresh in customizer

= 1.5.1 =

* Add: :hover/:focus text describing toolbar buttons; replaces title attributes removed in 1.5.0
* Missing: Add woff2 call
* Bug fix: Re-order font format imports

= 1.5.0 =

* Updated toolbar fonts
* Updated toolbar to use `button` instead of `a`
* Improved fontsize increase default CSS. Most themes will still benefit from custom styles.
* Added option to place toolbar on right side of screen.
* Update load method for text domain
* WP A11y recent posts widget removed.

= 1.4.6 =

* Bug fix: Don't create a duplicate ID when multiple longdesc attributes used on one page

= 1.4.5 =

* Add languages: Hungarian, English (Australian)
* Bug fix: Retain intended image when adding alt attribute missing warning.

= 1.4.4 =

* In WP 4.3, widgets with no settings are not saved. So...
* Added Title setting to WP Accessibility toolbar widget.

= 1.4.3 =

* Update widget constructors to use PHP5+ syntax.
* Add filter to disable or replace the fontsize styles for toolbar. 'wpa_fontsize_css'. Return false to disable; return stylesheet URL to replace.

= 1.4.2 =

* Language update: Norwegian
* New feature: Automatically add labels to WordPress standard search form & comment forms if missing.

= 1.4.1 =

* Bug fix: warning thrown if current theme does not have any tags.
* Change: Use image to show 'needs alt', for easier deletion.
* Fix: text_direction deprecated in bloginfo
* Language updates: Spanish, Polish

= 1.4.0 =

* Added enforcement tools for alt attribute usage in images.
* Media lists indicate whether an image is marked as decorative, has an alt attribute, or needs an alt attribute.
* Media editor shows checkbox to mark as decorative when editing image media types.
* If an image is inserted into a post without either being marked as decorative or having an alt attribute provided, HTML will also insert a notice indicating that the image needs an alt attribute.
* If an image is inserted and is checked as decorative, the alt attribute will be set to an empty value.
* Language update: Spanish

= 1.3.11 =

* Bug fix: Longdesc styles not automatically enabled with longdesc options.
* Added detection for whether current theme is accessibility-ready
* Disabled some options if current theme is accessibility-ready.
* Added notice to inform users about Access Monitor
* Language add: Portuguese (Brazil)
* Language update: German

= 1.3.10 =

* Switch order in which skiplinks/accessibility toolbar are loaded into the DOM so skiplinks load last/appear first.
* Hide fields for WordPress title attribute removal on versions where they don't apply. 
* Language updates: Russian, Hebrew, Bulgarian

= 1.3.9 =

* Bug fix: skiplinks JS targeting
* Bug fix: incorrect textdomain in longdesc template
* Update or add translations: Dutch, French, Finnish, Russian, Bulgarian

= 1.3.8 =

* Feature: iconfont toolbar icons so icons can be resized [Thanks Chris!](https://github.com/clrux/a11y)
* Feature: define size of toolbar icons 
* Feature: Assign tabindex=-1 to skiplink targets to ensure functionality of skiplinks.
* Bug fix: Switch template_redirect to template_include filter
* Redo file structure to move CSS into subdirectory.
* Update translations: French, German, Hebrew
* Add translations: Slovenian, Russian

= 1.3.7 =

* Typo fixed.
* Bug fix: If any skiplink field was filled in, skiplinks automatically displayed. 

= 1.3.6 =

* Bug fix: When disabled, lang toggle removed language from HTML element
* Add RTL styles for a11y toolbar.

= 1.3.5 =

* Bug fix: Toolbar shortcode didn't toggle Grayscale or Fontsize
* Documentation: Add documentation of shortcode/widget toolbar.

= 1.3.4 =

* Bug fix: Only enqueue stylesheets when settings require them.
* Bug fix: Search filter should only be applied on front-end
* Add SVG filter to provide grayscaling in Firefox
* Add Languages: Hebrew, Greek

= 1.3.3 =

* Bug fix: a11y.css issue collapsing toolbar buttons in Firefox
* Language: Update Italian.

= 1.3.2 =

* Feature: Check for HTML5 structural elements and insert ARIA landmark roles.
* Feature: Define ID of container to use for complementary role. 
* Feature: Add styles to make placeholder text high-contrast in high-contrast stylesheet.
* Feature: Add option to force underlines on links
* Bug fix: use reply-to header in email support requests
* Bug fix: proper variable set up for high contrast stylesheet path
* Language: Add Portuguese (Portugal)

= 1.3.1 =

* Emended a JS comment that some plug-ins were treating as a node...
* Updated .pot file with long description strings.
* Bug fix: longdesc attribute added even if description field empty.

= 1.3.0 =

* Add long description support. Requested by John Foliot; based on http://wordpress.org/plugins/long-description-for-image-attachments/

= 1.2.9 =

* Bug fix: :focus states for skiplinks broken in 1.2.8.
* Bug fix: WP Accessibility admin color issue in WP 3.8+
* Auto-hide grayscale in Firefox even if enabled. See: https://gist.github.com/amandavisconti/8455507
* Made accessibility toolbar available via widget or shortcode [wpa_toolbar]
* Add Language: Romanian, by Adrian Tamasan
* Updated language: Dutch

= 1.2.8 =

* Bug fix: support for languages with right-to-left reading order.

= 1.2.7 =

* For 3.8, eliminate outdated title attribute filters. (nav menus, page lists, edit post links, edit comment links, category links)
* Add Dutch translation by Rian Rietveld

= 1.2.6 =

* Truly hides grayscale option, not dependent on CSS.
* Eliminates in-page anchor focusing, due to conflicts with plug-ins that attach scripts to links with hashes.
* Remove row action visibility from default admin stylesheet, due to 3.7 changes making those links keyboard accessible.
* Added row actions always visible option.

= 1.2.5 =

* Added Spanish translation.
* Added incomplete Finnish translation.
* Updated French translation.
* Bug fix: WP Accessibility disabled ability to use theme styles in TinyMCE editor.

= 1.2.4.1 =

* Minor settings bug.

= 1.2.4 =

* Added diagnostic.css (beta) for admin users on front end and in post editor.
* Bug fix in a11y.js; incorrect function call in scrollTo.
* Bug fix in a11y.js; removed hook to # urls
* Added visible logout link to admin to support users of voice activated controls.
* Bug fix to built-in support request form.
* Added filter wpa_move_toolbar to make it possible to attach a11y toolbar to something other than the body element.
* Added French translation.


= 1.2.3 =

* Updated jQuery ScrollTo to version 1.4.5 to resolve JS conflict.
* Updated method of accessing $ in jQuery.
* Added CDATA blocks so WP Accessibility doesn't prevent validation as XML.
* Added Italian translation, courtesy of Roberto Scano.

= 1.2.2 =

* Bug fix: compatibility issue with PageLines framework.

= 1.2.1 =

* Disabled grayscale toggle in Accessibility toolbar by default due to poor browser support and low functional value. (Can still be enabled by user.)
* Removed php notice in title- recent posts widget
* Updated German and added Polish translations

= 1.2.0 =

* Added space between content output and continue reading text in excerpt context.
* Added German translation
* Added Accessibility Toolbar (<a href="http://www.usableinteractions.com/2012/11/accessibility-toolbar/">Source</a>)
* Added WP admin stylesheet:
* Some contrast improvements.
* Placed post row action links (Edit, Quick Edit, Trash, View) into screen reader visible and keyboard usable position.
* Added underlines to links on hover
* Supports your own custom wp-admin stylesheet via your Theme directory. 

= 1.1.2 =

* Update support statement to WP 3.5.0
* Add role='navigation' to skiplinks container.

= 1.1.1 =

* Bug fix: extra template loaded when search template is inserted.
* Bug fix: jQuery not always loaded when required.

= 1.1.0 =

* Added ability to add focus outline in :focus pseudo class.
* Added color contrast tool.
* Added settings link to plugins listing.
* Added link to translations site for this plug-in. 
* Improved response for forcing search error on empty search submission.
* Bug fix for adding custom skip link.

= 1.0.0 =

* Initial release!

== Frequently Asked Questions ==

= WP Accessibility is inserting some information via javascript. Is this really accessible? =

Yes. It does require that the user is operating a device that has javascript support, but that encompasses the vast majority of devices and browsers today, including screen readers.

= I installed WP Accessibility and ran some tests, but I'm still getting errors WP Accessibility is supposed to correct. =

Even if WP Accessibility is running correctly, not all accessibility testing tools will be aware of the fixes. Here's a resource for more information: [Mother Effing Tool Confuser](http://mothereffingtoolconfuser.com/).

== Screenshots ==

1. Settings Page

== Upgrade Notice ==

* 2.1.6 Security Fix. Please update promptly.