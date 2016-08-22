=== WP Accessibility ===
Contributors: joedolson
Donate link: http://www.joedolson.com/donate/
Tags: title, accessibility, accessible, navigation, wcag, a11y, section508, focus, alt text, labels, aria
Requires at least: 3.4.2
Tested up to: 4.6
Stable tag: 1.5.5
Text Domain: wp-accessibility
License: GPLv2 or later

WP Accessibility fixes common accessibility issues in your WordPress site.

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
* Enforcement for alt attributes on images.

= Accessibility Issues fixed by WP Accessibility: =

* Remove the target attribute from links.
* Force a search page error when a search is made with an empty text string. (If your theme has a search.php template.)
* Remove tabindex from elements that are focusable.
* Strip title attributes from images inserted into content. 
* Remove redundant title attributes from page lists, category lists, and archive menus. 
* Add post titles to standard "read more" links.
* Address some accessibility issues in the WordPress admin styles
* Add labels to standard WordPress form fields if missing

= Accessibility Tools built into WP Accessibility: =

* Show the color contrast between two provided hexadecimal color values.
* Enable diagnostic CSS to show CSS-detectable problems in visual editor or on front-end of site. 

Learn more! <a href="http://make.wordpress.org/accessibility/wp-accessibility-plugin/">Read about the accessibility issues corrected</a> by WP Accessibility!

The plug-in is intended to help with deficiencies commonly found in themes and to solve some issues in WordPress core. It can't correct every problem (by a long shot), but provides tools to fix some issues, supplement the accessibility of your site, or identify problems.

Translating my plug-ins is always appreciated. Visit <a href="https://translate.wordpress.org/projects/wp-plugins/wp-accessibility">the WordPress translations site</a> to get your language into shape!

== Installation ==

1. Download the plugin's zip file, extract the contents, and upload them to your wp-content/plugins folder.
2. Login to your WordPress dashboard, click "Plugins", and activate WP Accessibility.
3. Customise your settings on the Settings > WP Accessibility page.

== Changelog ==

= Future =

[Suggest a change!](https://github.com/joedolson/wp-accessibility/)

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

* 1.4.6 - Bug fix for duplicate IDs in longdesc implementation