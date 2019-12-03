<?php
/**
 * Autoloading
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset;

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Require classes.
require __DIR__ . '/class-setup.php';

// Init classes.
new Setup();
