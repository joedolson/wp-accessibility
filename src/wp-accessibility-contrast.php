<?php
/**
 * WP Accessibility Color Contrast testing
 *
 * @category Features
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-accessibility/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Measure the relative luminosity between two RGB values.
 *
 * @param int $r Red value 1.
 * @param int $r2 Red value 2.
 * @param int $g Green value 1.
 * @param int $g2 Green value 2.
 * @param int $b Blue value 1.
 * @param int $b2 Blue value 2.
 *
 * @return luminosity ratio.
 */
function wpa_luminosity( $r, $r2, $g, $g2, $b, $b2 ) {
	$r  = (int) $r;
	$r2 = (int) $r2;
	$g  = (int) $g;
	$g2 = (int) $g2;
	$b  = (int) $b;
	$b2 = (int) $b2;

	$rs_rgb = $r / 255;
	$gs_rgb = $g / 255;
	$bs_rgb = $b / 255;
	$r_new  = ( $rs_rgb <= 0.03928 ) ? $rs_rgb / 12.92 : pow( ( $rs_rgb + 0.055 ) / 1.055, 2.4 );
	$g_new  = ( $gs_rgb <= 0.03928 ) ? $gs_rgb / 12.92 : pow( ( $gs_rgb + 0.055 ) / 1.055, 2.4 );
	$b_new  = ( $bs_rgb <= 0.03928 ) ? $bs_rgb / 12.92 : pow( ( $bs_rgb + 0.055 ) / 1.055, 2.4 );

	$rs_rgb2 = $r2 / 255;
	$gs_rgb2 = $g2 / 255;
	$bs_rgb2 = $b2 / 255;
	$r2_new  = ( $rs_rgb2 <= 0.03928 ) ? $rs_rgb2 / 12.92 : pow( ( $rs_rgb2 + 0.055 ) / 1.055, 2.4 );
	$g2_new  = ( $gs_rgb2 <= 0.03928 ) ? $gs_rgb2 / 12.92 : pow( ( $gs_rgb2 + 0.055 ) / 1.055, 2.4 );
	$b2_new  = ( $bs_rgb2 <= 0.03928 ) ? $bs_rgb2 / 12.92 : pow( ( $bs_rgb2 + 0.055 ) / 1.055, 2.4 );

	if ( $r + $g + $b <= $r2 + $g2 + $b2 ) {
		$l2 = ( .2126 * $r_new + 0.7152 * $g_new + 0.0722 * $b_new );
		$l1 = ( .2126 * $r2_new + 0.7152 * $g2_new + 0.0722 * $b2_new );
	} else {
		$l1 = ( .2126 * $r_new + 0.7152 * $g_new + 0.0722 * $b_new );
		$l2 = ( .2126 * $r2_new + 0.7152 * $g2_new + 0.0722 * $b2_new );
	}
	$luminosity = round( ( $l1 + 0.05 ) / ( $l2 + 0.05 ), 2 );

	return $luminosity;
}

/**
 * Convert an RGB value to a HEX value.
 *
 * @param int $r Red value.
 * @param int $g Green value.
 * @param int $b Blue value.
 *
 * @return Hexadecimal color equivalent.
 */
function wpa_rgb2hex( $r, $g = - 1, $b = - 1 ) {
	if ( is_array( $r ) && sizeof( $r ) === 3 ) {
		list( $r, $g, $b ) = $r;
	}
	$r = intval( $r );
	$g = intval( $g );
	$b = intval( $b );

	$r = dechex( $r < 0 ? 0 : ( $r > 255 ? 255 : $r ) );
	$g = dechex( $g < 0 ? 0 : ( $g > 255 ? 255 : $g ) );
	$b = dechex( $b < 0 ? 0 : ( $b > 255 ? 255 : $b ) );

	$color  = ( strlen( $r ) < 2 ? '0' : '' ) . $r;
	$color .= ( strlen( $g ) < 2 ? '0' : '' ) . $g;
	$color .= ( strlen( $b ) < 2 ? '0' : '' ) . $b;

	return '#' . $color;
}

/**
 * Convert a Hexadecimal color value to RGB.
 *
 * @param string $color Hexadecimal value for a color.
 *
 * @return array of RGB values in R,G,B order.
 */
function wpa_hex2rgb( $color ) {
	$color = str_replace( '#', '', $color );
	if ( strlen( $color ) !== 6 ) {
		return array( 0, 0, 0 );
	}
	$rgb = array();
	for ( $x = 0; $x < 3; $x ++ ) {
		$rgb[ $x ] = hexdec( substr( $color, ( 2 * $x ), 2 ) );
	}

	return $rgb;
}

/**
 * Generate an array of RGB color values from hex codes.
 */
function wpa_contrast() {
	if ( ! empty( $_GET['color'] ) ) {
		if ( isset( $_GET['color'] ) && '' !== $_GET['color'] ) {
			$fore_color = sanitize_text_field( $_GET['color'] );
			if ( '#' === substr( $fore_color, 0, 1 ) ) {
				$fore_color = str_replace( '#', '', $fore_color );
			}
			if ( 3 === strlen( $fore_color ) ) {
				$color6char  = $fore_color[0] . $fore_color[0];
				$color6char .= $fore_color[1] . $fore_color[1];
				$color6char .= $fore_color[2] . $fore_color[2];
				$fore_color  = $color6char;
			}
			if ( preg_match( '/^#?([0-9a-f]{1,2}){3}$/i', $fore_color ) ) {
				$echo_hex_fore = str_replace( '#', '', $fore_color );
			} else {
				$echo_hex_fore = 'FFFFFF';
			}
			if ( isset( $_GET['color2'] ) && '' !== $_GET['color2'] ) {
				$back_color = sanitize_text_field( $_GET['color2'] );
				if ( '#' === substr( $back_color, 0, 1 ) ) {
					$back_color = str_replace( '#', '', $back_color );
				}
				if ( 3 === strlen( $back_color ) ) {
					$color6char  = $back_color[0] . $back_color[0];
					$color6char .= $back_color[1] . $back_color[1];
					$color6char .= $back_color[2] . $back_color[2];
					$back_color  = $color6char;
				}
				if ( preg_match( '/^#?([0-9a-f]{1,2}){3}$/i', $back_color ) ) {
					$echo_hex_back = str_replace( '#', '', $back_color );
				} else {
					$echo_hex_back = 'FFFFFF';
				}
				$color  = wpa_hex2rgb( $echo_hex_fore );
				$color2 = wpa_hex2rgb( $echo_hex_back );
				$rfore  = $color[0];
				$gfore  = $color[1];
				$bfore  = $color[2];
				$rback  = $color2[0];
				$gback  = $color2[1];
				$bback  = $color2[2];
				$colors = array(
					'hex1'   => $echo_hex_fore,
					'hex2'   => $echo_hex_back,
					'red1'   => $rfore,
					'green1' => $gfore,
					'blue1'  => $bfore,
					'red2'   => $rback,
					'green2' => $gback,
					'blue2'  => $bback,
				);

				return $colors;
			} else {
				return false;
			}
		}
	}

	return false;
}
