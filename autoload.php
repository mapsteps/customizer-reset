<?php
/**
 * Autoloading
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset;

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Require helper classes.
require __DIR__ . '/helpers/class-base.php';
require __DIR__ . '/helpers/class-export.php';
require __DIR__ . '/helpers/class-import.php';

// Require setup classes.
require __DIR__ . '/class-setup.php';

// Init classes.
new Setup();
