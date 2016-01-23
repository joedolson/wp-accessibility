<?php
/*
*  The goal for this template is to provide the simplest possible interface to view the long description. 
*  If you replace the template, I recommend against including your peripheral design, such as navigation.
*  The purpose of the long description is to get the description and return to your previous context.
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<title><?php the_title(); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php print get_stylesheet_uri(); ?>">
	<link rel="stylesheet" type="text/css" href="<?php print plugins_url( '/wp-accessibility/wpa-style.css' ); ?>">
</head>
<body>
<div id="longdesc" class="template-longdesc">
	<div id="desc_">
		<div id="desc_wp-image-<?php the_ID(); ?>">
			<?php the_content(); ?>
		</div>
		<?php
		if ( isset( $_GET['referrer'] ) ) {
			$uri = get_permalink( (int) $_GET['referrer'] );
			if ( ! empty( $uri ) ) {
				$uri .= '#' . longdesc_return_anchor( get_the_ID() );
				print '<p><a href="' . esc_url( $uri ) . '">' . esc_html__( 'Return to article.', 'wp-accessibility' ) . '</a></p>';
			}
		}
		?>
	</div>
</div>
</body>
</html>