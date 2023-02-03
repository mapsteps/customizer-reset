<?php
/**
 * Plugin Name: Customizer Reset - Export & Import
 * Plugin URI: https://wp-pagebuilderframework.com/
 * Description: Reset, export, and import your WordPress Customizer settings with just one click of a button.
 * Version: 1.2
 * Author: David Vongries
 * Author URI: https://davidvongries.com
 * Text Domain: customizer-reset
 *
 * @package Customizer_Reset
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Helper constants.
define( 'CUSTOMIZER_RESET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOMIZER_RESET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CUSTOMIZER_RESET_PLUGIN_VERSION', '1.2' );

require __DIR__ . '/autoload.php';
