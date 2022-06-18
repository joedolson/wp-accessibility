## Custom Fontsize Stylesheet

```php
 * Filter the CSS file for the WP Accessibility font size styles.
 * This example returns the URL for a file named 'fontsize.css' and placed in your theme or child theme directory.
 * 
 * @since WP Accessibility 1.4.3
 * 
 * @param string URL to default stylesheet
 * 
 * @return mixed boolean/string Return false to disable the stylesheet; return a URL resolving to your custom styles to replace it.
 */
function my_fontsize_css( $url ) {
	// return false; // disable CSS.
	return get_stylesheet_directory_uri() . '/fontsize.css';
}
add_filter( 'wpa_fontsize_css', 'my_fontsize_css' );
```