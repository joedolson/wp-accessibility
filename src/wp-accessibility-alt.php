<?php
/**
 * WP Accessibility Alt control implementation
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

add_filter( 'manage_media_columns', 'wpa_media_columns' );
add_action( 'manage_media_custom_column', 'wpa_media_value', 10, 2 );
/**
 * Add column to media column table view indicating images with no alt attribute not also checked as decorative.
 *
 * @param array $columns Current table view columns.
 *
 * @return columns.
 */
function wpa_media_columns( $columns ) {
	$columns['wpa_data'] = __( 'Accessibility', 'wp-accessibility' );

	return $columns;
}

/**
 * Get media values for current item to indicate alt status.
 *
 * @param array $column Name of column being checked.
 * @param int   $id ID of object thiss row belongs to.
 *
 * @return String alt attribute status for this object.
 */
function wpa_media_value( $column, $id ) {
	if ( 'wpa_data' === $column ) {
		$mime           = get_post_mime_type( $id );
		$invalid_values = array(
			'""',
			"''",
		);
		switch ( $mime ) {
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				$alt    = get_post_meta( $id, '_wp_attachment_image_alt', true );
				$no_alt = (bool) get_post_meta( $id, '_no_alt', true );
				if ( ! $alt && ! $no_alt ) {
					echo '<span class="missing"><span class="dashicons dashicons-no" aria-hidden="true"></span> <a href="' . get_edit_post_link( $id ) . '#attachment_alt">' . __( 'Add <code>alt</code> text', 'wp-accessibility' ) . '</a></span>';
				} else {
					if ( true === $no_alt ) {
						echo '<span class="ok"><span class="dashicons dashicons-yes" aria-hidden="true"></span> ' . __( 'Decorative', 'wp-accessibility' ) . '</span>';
					} elseif ( in_array( $alt, $invalid_values, true ) ) {
						echo '<span class="missing"><span class="dashicons dashicons-no" aria-hidden="true"></span> ' . __( 'Invalid', 'wp-accessibility' ) . '</span>';
					} else {
						echo '<span class="ok"><span class="dashicons dashicons-yes" aria-hidden="true"></span> ' . __( 'Has <code>alt</code>', 'wp-accessibility' ) . '</span>';
					}
				}
				break;
			default:
				echo '<span class="non-image">' . __( 'N/A', 'wp-accessibility' ) . '</span>';
				break;
		}
	}
	return $column;
}

add_filter( 'attachment_fields_to_edit', 'wpa_insert_alt_verification', 10, 2 );
/**
 * Insert custom fields into attachment editor for alt verification.
 *
 * @param array  $form_fields Existing form fields.
 * @param object $post Media attachment object.
 *
 * @return array New form fields.
 */
function wpa_insert_alt_verification( $form_fields, $post ) {
	$mime = get_post_mime_type( $post->ID );
	if ( 'image/jpeg' === $mime || 'image/png' === $mime || 'image/gif' === $mime ) {
		$no_alt                = get_post_meta( $post->ID, '_no_alt', true );
		$alt                   = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
		$checked               = checked( $no_alt, 1, false );
		$form_fields['no_alt'] = array(
			'label' => __( 'Decorative', 'wp-accessibility' ),
			'input' => 'html',
			'value' => 1,
			'html'  => "<input name='attachments[$post->ID][no_alt]' id='attachments-$post->ID-no_alt' value='1' type='checkbox' aria-describedby='wpa_help' $checked /> <em class='help' id='wpa_help'>" . __( 'All images must either have an alt attribute or be declared as decorative.', 'wp-accessibility' ) . '</em>',
		);
	}
	return $form_fields;
}

add_filter( 'attachment_fields_to_save', 'wpa_save_alt_verification', 10, 2 );
/**
 * Save custom alt fields when attachment updated.
 *
 * @param array $post $post data.
 * @param array $attachment Attachment data.
 *
 * @return $post
 */
function wpa_save_alt_verification( $post, $attachment ) {
	if ( isset( $attachment['no_alt'] ) ) {
		update_post_meta( $post['ID'], '_no_alt', 1 );
	} else {
		delete_post_meta( $post['ID'], '_no_alt' );
	}

	return $post;
}

add_filter( 'image_send_to_editor', 'wpa_alt_attribute', 10, 8 );
/**
 * Filter output when image is submitted to the editor. Check for alt attributes, and modify output.
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
 * @return string Image output.
 */
function wpa_alt_attribute( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	// Get data for the image attachment.
	$noalt = (bool) get_post_meta( $id, '_no_alt', true );
	// Get the original title to compare to alt.
	$title   = get_the_title( $id );
	$warning = false;
	if ( true === $noalt ) {
		$html = str_replace( 'alt="' . $alt . '"', 'alt=""', $html );
	}
	if ( ( '' === $alt || $alt === $title ) && true !== $noalt ) {
		if ( $alt === $title ) {
			$warning = __( 'The alt text for this image is the same as the title. In most cases, that means that the alt attribute has been automatically provided from the image file name.', 'wp-accessibility' );
			$image   = 'alt-same.png';
		} else {
			$warning = __( 'This image requires alt text, but the alt text is currently blank. Either add alt text or mark the image as decorative.', 'wp-accessibility' );
			$image   = 'alt-missing.png';
		}
	}
	if ( $warning ) {
		return $html . "<img class='wpa-image-missing-alt size-" . esc_attr( $size ) . ' ' . esc_attr( $align ) . "' src='" . plugins_url( "imgs/$image", __FILE__ ) . "' alt='" . esc_attr( $warning ) . "' />";
	}
	return $html;
}

add_action( 'init', 'wpa_add_editor_styles' );
/**
 * Enqueue custom editor styles for WP Accessibility. Used in display of img replacements.
 */
function wpa_add_editor_styles() {
	add_editor_style( plugins_url( 'css/editor-style.css', __FILE__ ) );
}
