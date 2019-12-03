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
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_customizer_reset', array( $this, 'handle_ajax' ) );
	}

	/**
	 * Setup textdomain.
	 */
	public function setup_text_domain() {
		load_plugin_textdomain( 'customizer-reset', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Add submenu under "Appearance" menu item.
	 */
	public function add_submenu() {
		global $submenu;

		$submenu['themes.php'][] = array( 'Reset Customizer', 'manage_options', admin_url( 'customize.php' ) );
	}

	/**
	 * Store a reference to `WP_Customize_Manager` instance
	 *
	 * @param object $wp_customize `WP_Customize_Manager` instance.
	 */
	public function customize_register( $wp_customize ) {
		$this->wp_customize = $wp_customize;
	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue_scripts() {
		// CSS.
		wp_enqueue_style( 'customizer-reset', CUSTOMIZER_RESET_PLUGIN_URL . '/assets/css/customizer-reset.css', array(), CUSTOMIZER_RESET_PLUGIN_VERSION );

		// JS.
		wp_enqueue_script( 'customizer-reset', CUSTOMIZER_RESET_PLUGIN_URL . '/assets/js/customizer-reset.js', array(), CUSTOMIZER_RESET_PLUGIN_VERSION, true );

		wp_localize_script(
			'customizer-reset',
			'customizerResetObj',
			array(
				'headerButtonText' => __( 'Reset', 'customizer-reset' ),
				'footerButtonText' => __( 'Reset Customizer', 'customizer-reset' ),
				'confirmationText' => __( "Warning! This will remove all customizations have been made via customizer to this theme!\n\nThis action is irreversible!", 'customizer-reset' ),
				'nonce'            => wp_create_nonce( 'customizer-reset' ),
			)
		);
	}

	/**
	 * Handle ajax request of customizer reset.
	 */
	public function handle_ajax() {
		if ( ! $this->wp_customize->is_preview() ) {
			wp_send_json_error( 'not_preview' );
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'customizer-reset' ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}

		$this->reset_customizer();
		wp_send_json_success();
	}

	/**
	 * Reset customizer.
	 */
	public function reset_customizer() {
		$settings = $this->wp_customize->settings();

		// remove theme_mod settings registered in customizer.
		foreach ( $settings as $setting ) {
			if ( 'theme_mod' === $setting->type ) {
				remove_theme_mod( $setting->id );
			}
		}
	}
}
