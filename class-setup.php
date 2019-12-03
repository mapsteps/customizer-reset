<?php
/**
 * Setup customizer reset.
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset;

/**
 * Setup customizer reset.
 */
class Setup {
	/**
	 * Setup action & filter hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_text_domain' ) );
		add_action( 'acf/init', array( $this, 'setup_options_page' ) );
	}

	/**
	 * Setup textdomain.
	 */
	public function setup_text_domain() {
		load_plugin_textdomain( 'customizer-reset', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Setup options page.
	 */
	public function setup_options_page() {
		//
	}
}
