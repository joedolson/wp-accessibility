=== WP Accessibility ===
Contributors: joedolson
Donate link: http://www.joedolson.com/donate.php
Tags: title, accessibility, accessible, navigation, wcag, a11y, section508, focus
Requires at least: 3.4.2
Tested up to: 3.9.1
Stable tag: 1.3.3
License: GPLv2 or later

WP Accessibility provides fixes for common accessibility issues in your WordPress site.

== Description ==

This plug-in helps with a variety of common accessibility problems in WordPress themes. While most accessibility issues can't be addressed without directly changing your theme, WP Accessibility adds a number of helpful accessibility features with a minimum amount of setup or expert knowledge.

All features can be disabled according to your theme's needs. For advanced users, all of the functions based on modifying stylesheets can be customized using your own custom styles by placing the appropriate stylesheet in your theme directory.

= Accessibility Features added by WP Accessibility: =

* Enable skip links with WebKit support by adding JavaScript support to move keyboard focus.
* Add skip links with user-defined targets. (Customizable targets and appearance.)
* Add language and text direction attributes to your HTML attribute
* Add an outline to the keyboard focus state for focusable elements. 
* Add a toolbar toggling between high contrast, large print, and desaturated (grayscale) views of your theme.
* Add a long description to images. Use the image's "Description" field to add long descriptions.

= Accessibility Issues fixed by WP Accessibility: =

* Remove the target attribute from links.
* Force a search page error when a search is made with an empty text string. (If your theme has a search.php template.)
* Remove tabindex from elements that are focusable.
* Strip title attributes from images inserted into content. 
* Remove redundant title attributes from page lists, category lists, and archive menus. 
* Add post titles to standard "read more" links.
* Address some accessibility issues in the WordPress admin styles

= Accessibility Tools built into WP Accessibility: =

* Show the color contrast between two provided hexadecimal color values.
* Enable diagnostic CSS to show CSS-detectable problems in visual editor or on front-end of site. 

Learn more! <a href="http://make.wordpress.org/accessibility/wp-accessibility-plugin/">Read about the accessibility issues corrected</a> by WP Accessibility!

The plug-in is intended to help with deficiencies commonly found in themes and to solve some issues in WordPress core. It can't correct every problem (by a long shot), but provides tools to fix some issues, supplement the accessibility of your site, or identify problems.

Translating my plug-ins is always appreciated. Visit <a href="http://translate.joedolson.com">my translations site</a> to start getting your language into shape!

Available languages (in order of completeness):
Portuguese (Portugal), Italian, Romanian, Dutch, French, Spanish, German, Polish, Finnish

Visit the [WP Accessibility translations site](http://translate.joedolson.com/projects/wp-accessibility/) to check the progress of a translation.

== Installation ==

1. Download the plugin's zip file, extract the contents, and upload them to your wp-content/plugins folder.
2. Login to your WordPress dashboard, click "Plugins", and activate WP Accessibility.
2. Customise your settings on the Settings > WP Accessibility page.

== Changelog ==

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
* Removed php notice in title-free recent posts widget
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

* 1.3.2 Couple new minor features; new Portuguese translation