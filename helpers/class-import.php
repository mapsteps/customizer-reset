<?php
/**
 * Customizer import helper.
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset\Helpers;

/**
 * A class that handle customizer import.
 */
class Import extends Base {
	/**
	 * An instance of WP_Customize_Manager.
	 *
	 * @access private
	 * @var object $wp_customize
	 */
	private $wp_customize;

	/**
	 * Class constructor
	 *
	 * @param object $wp_customize `WP_Customize_Manager` instance.
	 */
	public function __construct( $wp_customize = null ) {
		$this->wp_customize = $wp_customize;
	}

	/**
	 * Import the customizer.
	 */
	public function import() {
		global $wp_customize;

		// Make sure WordPress upload support is loaded.
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Setup internal vars.
		$error     = false;
		$template  = get_template();
		$overrides = array(
			'test_form' => false,
		);
		$file      = wp_handle_upload( $_FILES['customizer-import-file'], $overrides );

		// Make sure we have an uploaded file.
		if ( isset( $file['error'] ) ) {
			$error = $file['error'];
			return;
		}

		if ( ! file_exists( $file['file'] ) ) {
			$error = __( 'Error importing settings! Please try again.', 'customizer-reset' );
			return;
		}

		// Get the upload data.
		$raw  = file_get_contents( $file['file'] );
		$data = json_decode( $raw, true );

		// Remove the uploaded file.
		unlink( $file['file'] );

		// Data checks.
		if ( ! is_array( $data ) ) {
			$error = __( 'Error importing settings! Please check that you uploaded a customizer export file.', 'customizer-reset' );
			return;
		}

		if ( ! isset( $data['template'] ) || ! isset( $data['mods'] ) ) {
			$error = __( 'Error importing settings! Please check that you uploaded a customizer export file.', 'customizer-reset' );
			return;
		}

		if ( $data['template'] !== $template ) {
			$error = __( 'Error importing settings! The settings you uploaded are not for the current theme.', 'customizer-reset' );
			return;
		}

		// Import images.
		if ( isset( $_POST['customizer-import-images'] ) ) {
			$data['mods'] = $this->import_images( $data['mods'] );
		}

		// Import custom options.
		if ( isset( $data['options'] ) ) {

			foreach ( $data['options'] as $option_key => $option_value ) {

				$option = new Customizer_Setting(
					$wp_customize,
					$option_key,
					array(
						'default'    => '',
						'type'       => 'option',
						'capability' => 'edit_theme_options',
					)
				);

				$option->import( $option_value );
			}
		}

		// If wp_css is set then import it.
		if ( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && '' !== $data['wp_css'] ) {
			wp_update_custom_css_post( $data['wp_css'] );
		}

		// Call the customize_save action.
		do_action( 'customize_save', $wp_customize );

		// Loop through the mods.
		foreach ( $data['mods'] as $key => $val ) {

			// Call the customize_save_ dynamic action.
			do_action( 'customize_save_' . $key, $wp_customize );

			// Save the mod.
			set_theme_mod( $key, $val );
		}

		// Call the customize_save_after action.
		do_action( 'customize_save_after', $wp_customize );
	}
}
