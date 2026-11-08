<?php
/**
 * Plugin Name: Customizer Reset - Export & Import
 * Plugin URI: https://mapsteps.com
 * Description: Reset, export and import your WordPress Customizer settings with one click.
 * Version: 1.5
 * Requires at least: 4.7
 * Requires PHP: 5.6
 * Author: David Vongries
 * Author URI: https://mapsteps.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: customizer-reset
 *
 * @package Customizer_Reset
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Plugin constants.
define( 'CUSTOMIZER_RESET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOMIZER_RESET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CUSTOMIZER_RESET_PLUGIN_VERSION', '1.5' );

require __DIR__ . '/autoload.php';
