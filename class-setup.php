<?php
/**
 * Setup customizer reset.
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use CustomizerReset\Helpers\Export;
use CustomizerReset\Helpers\Import;
use WP_Customize_Manager;

/**
 * Setup customizer reset.
 */
class Setup {

	/**
	 * Instance of `WP_Customize_Manager` object.
	 *
	 * @var WP_Customize_Manager|null
	 */
	public $wp_customize = null;

	/**
	 * Setup action & filter hooks.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_register', array( $this, 'export' ) );
		add_action( 'customize_register', array( $this, 'import' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_controls_print_scripts', array( $this, 'controls_print_scripts' ) );
		add_action( 'wp_ajax_customizer_reset', array( $this, 'handle_ajax' ) );

	}

	/**
	 * Add a "Reset Customizer" link to the "Appearance" menu.
	 */
	public function add_submenu() {

		// Only display the link if the current user can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $submenu;

		// A plain link to the Customizer, add_theme_page() would register an admin page.
		$submenu['themes.php'][] = array( __( 'Reset Customizer', 'customizer-reset' ), 'manage_options', admin_url( 'customize.php' ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	}

	/**
	 * Store a reference to the `WP_Customize_Manager` instance.
	 *
	 * @param WP_Customize_Manager $wp_customize `WP_Customize_Manager` instance.
	 */
	public function customize_register( $wp_customize ) {

		$this->wp_customize = $wp_customize;

	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue_scripts() {

		wp_enqueue_style( 'customizer-reset', CUSTOMIZER_RESET_PLUGIN_URL . 'assets/css/customizer-reset.css', array(), CUSTOMIZER_RESET_PLUGIN_VERSION );
		wp_enqueue_script( 'customizer-reset', CUSTOMIZER_RESET_PLUGIN_URL . 'assets/js/customizer-reset.js', array( 'jquery', 'customize-controls' ), CUSTOMIZER_RESET_PLUGIN_VERSION, true );

		wp_localize_script(
			'customizer-reset',
			'customizerResetObj',
			array(
				'buttons'       => array(
					'reset'  => array(
						'text' => __( 'Reset Customizer', 'customizer-reset' ),
					),
					'export' => array(
						'text' => __( 'Export', 'customizer-reset' ),
					),
					'import' => array(
						'text' => __( 'Import', 'customizer-reset' ),
					),
				),
				'dialogs'       => array(
					'resetWarning'  => __( 'Caution! Proceeding will erase all customizations made for this theme through the WordPress Customizer.', 'customizer-reset' ),
					'importWarning' => __( 'Caution! Using the import tool will overwrite your current Customizer data. To save your current customizations, export them prior to importing new data.', 'customizer-reset' ),
					'emptyImport'   => __( 'Please select a file to import.', 'customizer-reset' ),
					'resetError'    => __( 'The Customizer could not be reset. Please reload the page and try again.', 'customizer-reset' ),
				),
				'importForm'    => array(
					'templates' => $this->import_form(),
				),
				'customizerUrl' => admin_url( 'customize.php' ),
				'pluginUrl'     => CUSTOMIZER_RESET_PLUGIN_URL,
				'nonces'        => array(
					'reset'  => wp_create_nonce( 'customizer-reset' ),
					'export' => wp_create_nonce( 'customizer-export' ),
				),
			)
		);

	}

	/**
	 * The import form markup.
	 *
	 * @return string The import form.
	 */
	private function import_form() {

		ob_start();
		require __DIR__ . '/templates/import-form.php';

		return ob_get_clean();

	}

	/**
	 * Handle the ajax request of the customizer reset.
	 */
	public function handle_ajax() {

		if ( ! check_ajax_referer( 'customizer-reset', 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( 'insufficient_permissions' );
		}

		if ( is_null( $this->wp_customize ) || ! $this->wp_customize->is_preview() ) {
			wp_send_json_error( 'not_preview' );
		}

		$this->reset_customizer();
		wp_send_json_success();

	}

	/**
	 * Reset customizer.
	 */
	public function reset_customizer() {

		if ( is_null( $this->wp_customize ) ) {
			return;
		}

		$settings = $this->wp_customize->settings();

		// Remove theme_mod settings registered in customizer.
		foreach ( $settings as $setting ) {
			if ( 'theme_mod' === $setting->type ) {
				remove_theme_mod( $setting->id );
			}
		}

	}

	/**
	 * Setup customizer export.
	 */
	public function export() {

		if ( is_null( $this->wp_customize ) || ! is_customize_preview() ) {
			return;
		}

		if ( ! isset( $_GET['action'] ) || 'customizer_export' !== $_GET['action'] ) {
			return;
		}

		$nonce = ! empty( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'customizer-export' ) || ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$exporter = new Export( $this->wp_customize );

		$exporter->export();

	}

	/**
	 * Setup customizer import.
	 */
	public function import() {

		if ( is_null( $this->wp_customize ) || ! is_customize_preview() ) {
			return;
		}

		if ( ! isset( $_POST['action'] ) || 'customizer_import' !== $_POST['action'] ) {
			return;
		}

		$nonce = ! empty( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'customizer-import' ) || ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		require_once __DIR__ . '/helpers/class-customizer-setting.php';

		$importer = new Import( $this->wp_customize );

		$importer->import();

	}

	/**
	 * Show import errors.
	 */
	public function controls_print_scripts() {

		global $customizer_reset_error;

		if ( $customizer_reset_error ) {
			echo '<script>alert(' . wp_json_encode( wp_strip_all_tags( $customizer_reset_error ) ) . ');</script>';
		}

	}

}
