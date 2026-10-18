<?php
/**
 * WP Accessibility Longdesc implementation
 *
 * @category Features
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2
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
 * @return array New attributes array.
 */
function wpa_featured_longdesc( $attr, $attachment, $size ) {
	if ( 'on' === get_option( 'wpa_longdesc_featured' ) ) {
		$permissions = wpa_check_image_permissions( $attachment );
		if ( $permissions && false !== (bool) wp_strip_all_tags( $attachment->post_content ) ) {
			$args = array( 'longdesc' => $attachment->ID );
			// The referrer is the post that the image is inserted into.
			if ( get_the_ID() ) {
				$args['referrer'] = absint( get_the_ID() );
			}

			$target = add_query_arg( $args, home_url() );
			$id     = wpa_longdesc_return_anchor( $attachment->ID );

			$attr['longdesc'] = $target;
			$attr['id']       = $id;
		}
	}

	return $attr;
}

// longdesc support, based on work by Michael Fields (http://wordpress.org/plugins/long-description-for-image-attachments/).
define( 'WPA_TEMPLATES', trailingslashit( __DIR__ ) . 'templates/' );
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
	// Return early if there is no reason to proceed. This is a public page, no nonce.
	if ( ! isset( $_GET['longdesc'] ) ) {
		return;
	}

	global $post;

	// Get the image attachment's data.
	$id   = absint( $_GET['longdesc'] );
	$post = get_post( $id );
	if ( is_object( $post ) ) {
		$post_permissions = wpa_check_image_permissions( $post );
		if ( ! $post_permissions ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit;
		}
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
		require_once $template;
		exit;
	} else {
		// Use plugin's template file.
		require_once WPA_TEMPLATES . 'longdesc-template.php';
		exit;
	}
}

/**
 * Check whether an image can be accessed by the current user.
 *
 * @param WP_Post  $post Image attachment post object.
 * @return bool True if the image can be accessed, false otherwise.
 */
function wpa_check_image_permissions( $post ) {
	$return = true;
	// Private is not a default status for attachments, but if a plugin sets it, we should respect it.
	$post_status = $post->post_status;
	if ( 'private' === $post_status && ! current_user_can( 'read_post', $post->ID ) ) {
		$return = false;
	}
	// By default, attachments inherit the visibility of their parent post.
	$post_parent_status = get_post( $post->post_parent )->post_status;
	if ( 'private' === $post_parent_status && ! current_user_can( 'read_post', $post->post_parent )  ) {
		$return = false;
	}
	// If the post currently requires a password, check if it has been provided.
	// True means the password is required and has not been provided; permit only if false.
	$post_password_needed = post_password_required( $post );
	if ( $post_password_needed ) {
		$return = false;
	}

	return $return;
}
/**
 * Anchor.
 *
 * Create anchor id for linking from a Long Description to referring post.
 * Also creates an anchor to return from Long Description page.
 *
 * @param int $post_id ID of the post which contains an image with a longdesc attribute.
 *
 * @return string
 * @since 2010-09-26
 */
function wpa_longdesc_return_anchor( $post_id ) {
	return 'longdesc-return-' . $post_id;
}

add_filter( 'image_send_to_editor', 'wpa_longdesc_add_attr', 10, 8 );
/**
 * Add Attribute.
 *
 * Add longdesc attribute when WordPress sends image to the editor.
 * Also creates an anchor to return from Long Description page.
 *
 * @param string $html Image HTML.
 * @param int    $attachment_id Post ID.
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
function wpa_longdesc_add_attr( $html, $attachment_id, $caption, $title, $align, $url, $size, $alt ) {
	// Get data for the image attachment.
	$image       = get_post( $attachment_id );
	$permissions = wpa_check_image_permissions( $image );
	if ( isset( $image->ID ) && ! empty( $image->ID ) && $permissions ) {
		$args = array( 'longdesc' => $image->ID );
		// The referrer is the post that the image is inserted into.
		// Post ID is sent via the request on new posts.
		if ( isset( $_REQUEST['post_id'] ) || get_the_ID() ) {
			$post_id          = ( isset( $_REQUEST['post_id'] ) ) ? absint( $_REQUEST['post_id'] ) : get_the_ID();
			$args['referrer'] = $post_id;
		}
		if ( '' !== trim( wp_strip_all_tags( $image->post_content ) ) ) {
			$search  = '<img ';
			$replace = '<img tabindex="-1" id="' . esc_attr( wpa_longdesc_return_anchor( $image->ID ) ) . '" longdesc="' . esc_url( add_query_arg( $args, home_url() ) ) . '" ';
			$html    = str_replace( $search, $replace, $html );
		}
	}

	return $html;
}

/**
 * Register WPA long description block style.
 */
function wpa_register_block_style() {
	if ( function_exists( 'register_block_style' ) ) {
		if ( 'false' !== get_option( 'wpa_longdesc' ) ) {
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
	}
}
add_action( 'init', 'wpa_register_block_style' );
