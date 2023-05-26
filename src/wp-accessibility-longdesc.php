<?php
/**
 * WP Accessibility Longdesc implementation
 *
 * @category Features
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-access/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wp_get_attachment_image_attributes', 'wpa_featured_longdesc', 10, 3 );
/**
 * Get long descriptions for featured images.
 *
 * @param array              $attr Existing image attributes.
 * @param object             $attachment Current image attachment object.
 * @param mixed array/string $size Image size shown.
 *
 * @return New attributes array.
 */
function wpa_featured_longdesc( $attr, $attachment, $size ) {
	if ( 'on' === get_option( 'wpa_longdesc_featured' ) ) {
		$attachment_id = $attachment->ID;
		if ( false !== (bool) strip_tags( $attachment->post_content ) ) {
			$args = array( 'longdesc' => $attachment_id );
			// The referrer is the post that the image is inserted into.
			if ( isset( $_REQUEST['post_id'] ) || get_the_ID() ) {
				$id               = ( isset( $_REQUEST['post_id'] ) ) ? $_REQUEST['post_id'] : get_the_ID();
				$args['referrer'] = intval( $id );
			}

			$target = add_query_arg( $args, home_url() );
			$id     = wpa_longdesc_return_anchor( $attachment_id );

			$attr['longdesc'] = $target;
			$attr['id']       = $id;
		}
	}

	return $attr;
}


// longdesc support, based on work by Michael Fields (http://wordpress.org/plugins/long-description-for-image-attachments/).
define( 'WPA_TEMPLATES', trailingslashit( dirname( __FILE__ ) ) . 'templates/' );
add_action( 'template_redirect', 'wpa_longdesc_template' );
/**
 * Load Template.
 *
 * The ID for an image attachment is expected to be
 * passed via $_GET['longdesc']. If this value exists
 * and a post is successfully queried, postdata will
 * be prepared and a template will be loaded to display
 * the post content.
 *
 * This template must be named "longdesc-template.php".
 *
 * First, this function will look in the child theme
 * then in the parent theme and if no template is found
 * in either theme, the default template will be loaded
 * from the plugin's folder.
 *
 * This function is hooked into the "template_redirect"
 * action and terminates script execution.
 *
 * @return void
 * @link http://wordpress.org/plugins/long-description-for-image-attachments/
 * @since 2010-09-26
 * @alter 2011-03-27
 */
function wpa_longdesc_template() {
	// Return early if there is no reason to proceed.
	if ( ! isset( $_GET['longdesc'] ) ) {
		return;
	}

	global $post;

	// Get the image attachment's data.
	$id   = absint( $_GET['longdesc'] );
	$post = get_post( $id );
	if ( is_object( $post ) ) {
		setup_postdata( $post );
	}

	// Attachment must be an image.
	if ( false === strpos( get_post_mime_type(), 'image' ) ) {
		header( 'HTTP/1.0 404 Not Found' );
		exit;
	}

	// The whole point here is to NOT show an image :).
	remove_filter( 'the_content', 'prepend_attachment' );

	// Check to see if there is a template in the theme.
	$template = locate_template( array( 'longdesc-template.php' ) );
	if ( ! empty( $template ) ) {
		require_once( $template );
		exit;
	} else {
		// Use plugin's template file.
		require_once( WPA_TEMPLATES . 'longdesc-template.php' );
		exit;
	}

	// You've gone too far. Error case.
	header( 'HTTP/1.0 404 Not Found' );
	exit;
}

/**
 * Anchor.
 *
 * Create anchor id for linking from a Long Description to referring post.
 * Also creates an anchor to return from Long Description page.
 *
 * @param int $id ID of the post which contains an image with a longdesc attribute.
 *
 * @return string
 * @since 2010-09-26
 */
function wpa_longdesc_return_anchor( $id ) {
	return 'longdesc-return-' . $id;
}

add_filter( 'image_send_to_editor', 'wpa_longdesc_add_attr', 10, 8 );
/**
 * Add Attribute.
 *
 * Add longdesc attribute when WordPress sends image to the editor.
 * Also creates an anchor to return from Long Description page.
 *
 * @param string $html Image HTML.
 * @param int    $id Post ID.
 * @param string $caption Caption text.
 * @param string $title Image title.
 * @param string $align Image alignment.
 * @param string $url Image URL.
 * @param array  $size Image size.
 * @param string $alt Image alt attribute.
 *
 * @return string
 *
 * @since 2010-09-20
 * @alter 2011-04-06
 */
function wpa_longdesc_add_attr( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	// Get data for the image attachment.
	$image = get_post( $id );
	if ( isset( $image->ID ) && ! empty( $image->ID ) ) {
		$args = array( 'longdesc' => $image->ID );
		// The referrer is the post that the image is inserted into.
		if ( isset( $_REQUEST['post_id'] ) || get_the_ID() ) {
			$id               = ( isset( $_REQUEST['post_id'] ) ) ? $_REQUEST['post_id'] : get_the_ID();
			$args['referrer'] = intval( $id );
		}
		if ( '' !== trim( strip_tags( $image->post_content ) ) ) {
			$search  = '<img ';
			$replace = '<img tabindex="-1" id="' . esc_attr( wpa_longdesc_return_anchor( $image->ID ) ) . '" longdesc="' . esc_url( add_query_arg( $args, home_url() ) ) . '" ';
			$html    = str_replace( $search, $replace, $html );
		}
	}

	return $html;
}

if ( function_exists( 'register_block_style' ) ) {
	/**
	 * Core function. Add reference style for long description.
	 */
	register_block_style(
		'core/image',
		array(
			'name'         => 'longdesc',
			'label'        => __( 'Has Long Description', 'wp-accessibility' ),
			'style_handle' => 'longdesc-style',
		)
	);
}
