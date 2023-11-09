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
			'&nbsp;',
			' ',
			'-',
			'--',
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
					} elseif ( in_array( $alt, $invalid_values, true ) || ctype_punct( $alt ) || ctype_space( $alt ) ) {
						echo '<span class="missing"><span class="dashicons dashicons-no" aria-hidden="true"></span> <a href="' . get_edit_post_link( $id ) . '#attachment_alt">' . __( 'Invalid <code>alt</code>', 'wp-accessibility' ) . '</a></span>';
					} elseif ( wpa_suspicious_alt( $alt ) ) {
						echo '<span class="missing"><span class="dashicons dashicons-no" aria-hidden="true"></span> <a href="' . get_edit_post_link( $id ) . '#attachment_alt">' . __( 'Suspicious <code>alt</code>', 'wp-accessibility' ) . '</a></span>';
					} elseif ( wpa_long_alt( $alt ) ) {
						echo '<span class="long"><span class="dashicons dashicons-warning" aria-hidden="true"></span> <a href="' . get_edit_post_link( $id ) . '#attachment_alt">' . __( 'Long <code>alt</code> text', 'wp-accessibility' ) . '</a></span>';
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

/**
 * Check whether an alt is unusually long.
 *
 * @param string $alt Alt attribute.
 *
 * @return bool
 */
function wpa_long_alt( $alt ) {
	$length = strlen( $alt );
	/**
	 * What length of an alt text is considered long. Default `140`.
	 *
	 * @hook wpa_long_alt
	 *
	 * @param {int} $limit Default length to call alt text long.
	 *
	 * @return int
	 */
	$limit = apply_filters( 'wpa_long_alt', 140 );
	if ( $length > $limit ) {
		return true;
	}

	return false;
}

/**
 * Check whether an alt attribute contains suspect strings.
 *
 * @param string $alt Alt attribute.
 *
 * @return bool
 */
function wpa_suspicious_alt( $alt ) {
	$case_insensitive = array(
		'logo',
		'image',
		'picture',
		'alt text',
		'alternative text',
	);
	/**
	 * Filter array of case insensitive strings that make alt text suspicious.
	 *
	 * @hook wpa_case_insensitive
	 *
	 * @param {array} $case_insensitive Array of strings.
	 *
	 * @return array
	 */
	$case_insensitive = apply_filters( 'wpa_case_insensitive', $case_insensitive );
	$case_sensitive   = array(
		'DSC',
		'IMG',
	);
	/**
	 * Filter array of case sensitive strings that make alt text suspicious.
	 *
	 * @hook wpa_case_sensitive
	 *
	 * @param {array} $case_sensitive Array of strings.
	 *
	 * @return array
	 */
	$case_sensitive = apply_filters( 'wpa_case_sensitive', $case_sensitive );
	foreach ( $case_insensitive as $term ) {
		if ( false !== stripos( $alt, $term ) ) {
			return true;
		}
	}
	foreach ( $case_sensitive as $term ) {
		if ( false !== strpos( $alt, $term ) ) {
			return true;
		}
	}

	return false;
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
	$warning = false;
	if ( true === $noalt ) {
		$html = str_replace( 'alt="' . $alt . '"', 'alt=""', $html );
	}
	$suspicious = wpa_suspicious_alt( $alt );
	$long       = wpa_long_alt( $alt );
	if ( ( '' === $alt || $suspicious ) && true !== $noalt ) {
		if ( $long ) {
			$warning         = 'wpa-warning wpa-long-alt';
			$caption_warning = __( 'Long alt text', 'wp-accessibility' );
		} elseif ( $suspicious ) {
			$warning         = 'wpa-warning wpa-suspicious-alt';
			$caption_warning = __( 'Suspicious alt text', 'wp-accessibility' );
		} elseif ( $alt && $caption === $alt ) {
			$warning         = 'wpa-warning wpa-caption-is-alt';
			$caption_warning = __( 'Caption and alt text are the same', 'wp-accessibility' );
		} else {
			$warning         = 'wpa-warning wpa-image-missing-alt';
			$caption_warning = __( 'Missing alt text', 'wp-accessibility' );
		}
		$html = str_replace( 'class="', 'data-warning="' . $caption_warning . '" class="' . $warning . ' ', $html );
	}
	if ( $warning && ! $caption ) {
		return '<div class="wp-block-image">' . $html . '</div>';
	}

	return $html;
}

add_action( 'init', 'wpa_add_editor_styles' );
/**
 * Enqueue custom editor styles for WP Accessibility. Used in display of img replacements.
 */
function wpa_add_editor_styles() {
	$wpa_version = ( SCRIPT_DEBUG ) ? wp_rand( 10000, 100000 ) : wpa_check_version();
	add_editor_style( plugins_url( 'css/editor-style.css', __FILE__ ), false, $wpa_version );
}

add_action( 'enqueue_block_editor_assets', 'wpa_block_editor_assets' );
/**
 * Enqueue custom block editor styles for WP Accessibility. Used in display of img replacements.
 */
function wpa_block_editor_assets() {
	$wpa_version = ( SCRIPT_DEBUG ) ? wp_rand( 10000, 100000 ) : wpa_check_version();
	wp_enqueue_style( 'wpa-block-styles', plugins_url( 'css/editor-style.css', __FILE__ ), false, $wpa_version );
}
