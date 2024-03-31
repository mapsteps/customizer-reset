<?php
/**
 * Customizer import helper.
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset\Helpers;

use stdClass;
use WP_Customize_Manager;

/**
 * A class that handle customizer import.
 */
class Import extends Base {

	/**
	 * Instance of `WP_Customize_Manager` object.
	 *
	 * @var WP_Customize_Manager|null
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
		global $customizer_reset_error;

		// Make sure WordPress upload support is loaded.
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$customizer_reset_error = false;

		// Setup internal vars.
		$template  = get_template();
		$overrides = array(
			'test_form' => false,
			'test_type' => false,
		);

		$upload_data = ! empty( $_FILES['customizer_import_file'] ) && is_array( $_FILES['customizer_import_file'] ) ? $_FILES['customizer_import_file'] : [];

		$file = wp_handle_upload( $upload_data, $overrides );

		// Make sure we have an uploaded file.
		if ( isset( $file['error'] ) ) {
			$customizer_reset_error = $file['error'];
			return;
		}

		if ( ! file_exists( $file['file'] ) ) {
			$customizer_reset_error = __( 'Import error! Please try again.', 'customizer-reset' );
			return;
		}

		// Get the upload data.
		$raw  = file_get_contents( $file['file'] );
		$data = json_decode( $raw, true );

		// Remove the uploaded file.
		wp_delete_file( $file['file'] );

		// Data checks.
		if ( ! is_array( $data ) ) {
			$customizer_reset_error = __( 'Import error! Please ensure that the file you are uploading is a valid Customizer export file.', 'customizer-reset' );
			return;
		}

		if ( ! isset( $data['template'] ) || ! isset( $data['mods'] ) ) {
			$customizer_reset_error = __( 'Import error! Please ensure that the file you are uploading is a valid Customizer export file.', 'customizer-reset' );
			return;
		}

		if ( $data['template'] !== $template ) {
			$customizer_reset_error = __( 'Import error! The customizer settings provided are not compatible with the current theme.', 'customizer-reset' );
			return;
		}

		// Import images.
		$data['mods'] = $this->import_images( $data['mods'] );

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

	/**
	 * Imports images for settings saved as mods.
	 *
	 * @param array $mods An array of customizer mods.
	 * @return array The mods array with any new import data.
	 */
	private function import_images( $mods ) {

		foreach ( $mods as $key => $val ) {

			if ( $this->is_image_url( $val ) ) {

				$data = $this->sideload_image( $val );

				if ( ! is_wp_error( $data ) ) {

					$mods[ $key ] = $data->url;

					// Handle header image controls.
					if ( isset( $mods[ $key . '_data' ] ) ) {
						$mods[ $key . '_data' ] = $data;
						update_post_meta( $data->attachment_id, '_wp_attachment_is_custom_header', get_stylesheet() );
					}
				}
			}
		}

		return $mods;

	}

	/**
	 * Checks to see whether a string is an image url or not.
	 *
	 * @param string $str The string to check.
	 * @return bool Whether the string is an image url or not.
	 */
	private function is_image_url( $str = '' ) {

		if ( is_string( $str ) ) {

			if ( preg_match( '/\.(jpg|jpeg|png|gif)/i', $str ) ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Taken from the core media_sideload_image function and
	 * modified to return an array of data instead of html.
	 *
	 * @param string $file The image file path.
	 * @return stdClass Object containing image data.
	 */
	private function sideload_image( $file ) {

		$data = new stdClass();

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		if ( ! empty( $file ) ) {

			// Set variables for storage, fix file filename for query strings.
			preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
			$file_array         = array();
			$file_array['name'] = basename( $matches[0] );

			// Download file to temp location.
			$file_array['tmp_name'] = download_url( $file );

			// If error storing temporarily, return the error.
			if ( is_wp_error( $file_array['tmp_name'] ) ) {
				return $file_array['tmp_name'];
			}

			// Do the validation and storage stuff.
			$id = media_handle_sideload( $file_array, 0 );

			// If error storing permanently, unlink.
			if ( is_wp_error( $id ) ) {
				wp_delete_file( $file_array['tmp_name'] );
				return $id;
			}

			$meta = wp_get_attachment_metadata( $id );

			// Build the object to return.
			$data->attachment_id = $id;
			$data->url           = wp_get_attachment_url( $id );
			$data->thumbnail_url = wp_get_attachment_thumb_url( $id );
			$data->height        = $meta['height'];
			$data->width         = $meta['width'];
		}

		return $data;

	}

}
