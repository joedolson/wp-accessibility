<?php
/**
 * Longdesc display template.
 *
 * The goal for this template is to provide the simplest possible interface to view the long description.
 * If you replace the template, I recommend against including your peripheral design, such as navigation.
 * The purpose of the long description is to get the description and return to your previous context.
 *
 * @category Templates
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2
 * @link     https://www.joedolson.com/wp-accessibility/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<title>
	<?php
	// translators: %s is the post ID of the image.
	printf( esc_html__( 'Image Long Description %s', 'wp-accessibility' ), '#' . esc_html( get_the_ID() ) );
	?>
	</title>
	<?php wp_head(); ?>
</head>
<body>
<main id="longdesc" class="template-longdesc">
	<div id="desc_<?php the_ID(); ?>">
		<div id="desc_wp-image-<?php the_ID(); ?>">
			<?php the_content(); ?>
		</div>
		<?php
		// This is a public page. There is no nonce.
		if ( isset( $_GET['referrer'] ) ) {
			$referer = (int) $_GET['referrer'];
			// Don't disclose link if user doesn't have permission to view the post.
			if ( is_post_publicly_viewable( $referer ) || current_user_can( 'read_post', $referer ) ) {
				$uri = get_permalink( $referer );
				if ( ! empty( $uri ) ) {
					$uri .= '#' . wpa_longdesc_return_anchor( get_the_ID() );
					print '<p><a href="' . esc_url( $uri ) . '">' . esc_html__( 'Return to article.', 'wp-accessibility' ) . '</a></p>';
				}
			}
		}
		?>
	</div>
</main>
</body>
</html>
