<?php
/**
 * Plugin Name: Customizer Reset
 * Plugin URI: https://customizerreset.io/
 * Description: The best WordPress customizer reset.
 * Version: 1.0
 * Author: MapSteps
 * Author URI: https://github.com/MapSteps/
 * Text Domain: customizer-reset
 *
 * @package Customizer_Reset
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Helper constants.
define( 'CUSTOMIZER_RESET_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOMIZER_RESET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CUSTOMIZER_RESET_PLUGIN_VERSION', '1.0' );

require __DIR__ . '/autoload.php';
