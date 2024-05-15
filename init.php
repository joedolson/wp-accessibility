<?php
/**
 * WP Accessibility
 *
 * @package     WP Accessibility
 * @author      Joe Dolson
 * @copyright   2012-2024 Joe Dolson
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WP Accessibility
 * Plugin URI: http://www.joedolson.com/wp-accessibility/
 * Description: Helps improve accessibility in your WordPress site, like removing title attributes.
 * Author: Joe Dolson
 * Author URI: http://www.joedolson.com/
 * Text Domain: wp-accessibility
 * Domain Path: /lang
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/license/gpl-2.0.txt
 * Version: 2.1.9
 */

/*
	Copyright 2011-2024  Joe Dolson (email : joe@joedolson.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require __DIR__ . '/src/wp-accessibility.php';

register_activation_hook( __FILE__, 'wpa_install' );
