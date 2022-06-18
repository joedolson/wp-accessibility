## Change Accessibility Toolbar Attachment Element

```php
/**
 * By default, the A11y toolbar is attached to the body element. Use this filter to attach to some other element.
 * 
 * @param string default attachment element: body
 * 
 * @return string HTML selector string valid for use in jQuery
 */
function my_toolbar_location( $selector ) {
	return '#content'; // attach to your theme's #content div.
}
add_filter( 'wpa_move_toolbar', 'my_toolbar_location' );
```