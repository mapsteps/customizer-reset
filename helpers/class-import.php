<?php
/**
 * Customizer import helper.
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use stdClass;
use WP_Customize_Manager;

/**
 * A class that handles the customizer import.
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
	 * @param WP_Customize_Manager|null $wp_customize `WP_Customize_Manager` instance.
	 */
	public function __construct( $wp_customize = null ) {

		$this->wp_customize = $wp_customize;

	}

	/**
	 * Import the customizer.
	 *
	 * The nonce is verified in Setup::import() before this runs.
	 */
	public function import() {

		global $customizer_reset_error;

		$customizer_reset_error = false;
		$invalid_file           = __( 'Import error! Please ensure that the file you are uploading is a valid Customizer export file.', 'customizer-reset' );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce checked in Setup::import(), the file is only read & JSON decoded.
		$upload = isset( $_FILES['customizer_import_file'] ) && is_array( $_FILES['customizer_import_file'] ) ? $_FILES['customizer_import_file'] : array();

		$upload_error = isset( $upload['error'] ) ? (int) $upload['error'] : UPLOAD_ERR_NO_FILE;

		if ( UPLOAD_ERR_NO_FILE === $upload_error ) {
			$customizer_reset_error = __( 'Please select a file to import.', 'customizer-reset' );
			return;
		}

		if ( UPLOAD_ERR_OK !== $upload_error || empty( $upload['tmp_name'] ) ) {
			$customizer_reset_error = __( 'Import error! Please try again.', 'customizer-reset' );
			return;
		}

		// Read the uploaded file straight from PHP's temp folder, it never gets moved into the uploads folder.
		if ( ! is_uploaded_file( $upload['tmp_name'] ) ) {
			$customizer_reset_error = __( 'Import error! Please try again.', 'customizer-reset' );
			return;
		}

		$data = json_decode( file_get_contents( $upload['tmp_name'] ), true );

		// Data checks.
		if ( ! is_array( $data ) || ! isset( $data['template'] ) || ! isset( $data['mods'] ) || ! is_array( $data['mods'] ) ) {
			$customizer_reset_error = $invalid_file;
			return;
		}

		if ( get_template() !== $data['template'] ) {
			$customizer_reset_error = __( 'Import error! The customizer settings provided are not compatible with the current theme.', 'customizer-reset' );
			return;
		}

		// Import images.
		$data['mods'] = $this->import_images( $data['mods'] );

		// Import custom options.
		if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {

			foreach ( $data['options'] as $option_key => $option_value ) {

				// Never import core options, even if the file contains them.
				if ( in_array( $option_key, $this->core_options, true ) ) {
					continue;
				}

				$option = new Customizer_Setting(
					$this->wp_customize,
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
		if ( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && is_string( $data['wp_css'] ) && '' !== $data['wp_css'] ) {
			wp_update_custom_css_post( $data['wp_css'] );
		}

		// Call the customize_save action.
		do_action( 'customize_save', $this->wp_customize );

		// Loop through the mods.
		foreach ( $data['mods'] as $key => $val ) {

			// Call the customize_save_ dynamic action.
			do_action( 'customize_save_' . $key, $this->wp_customize );

			// Save the mod.
			set_theme_mod( $key, $val );
		}

		// Call the customize_save_after action.
		do_action( 'customize_save_after', $this->wp_customize );

	}

	/**
	 * Imports images for settings saved as mods.
	 *
	 * @param array $mods An array of customizer mods.
	 * @return array The mods array with any new import data.
	 */
	private function import_images( $mods ) {

		foreach ( $mods as $key => $val ) {

			if ( ! $this->is_image_url( $val ) ) {
				continue;
			}

			// Images that already live on this site don't need to be imported again.
			if ( attachment_url_to_postid( $val ) ) {
				continue;
			}

			$data = $this->sideload_image( $val );

			if ( is_wp_error( $data ) || empty( $data->url ) ) {
				continue;
			}

			$mods[ $key ] = $data->url;

			// Handle header image controls.
			if ( isset( $mods[ $key . '_data' ] ) ) {
				$mods[ $key . '_data' ] = $data;
				update_post_meta( $data->attachment_id, '_wp_attachment_is_custom_header', get_stylesheet() );
			}
		}

		return $mods;

	}

	/**
	 * Checks whether a value is the URL of an image.
	 *
	 * @param mixed $str The value to check.
	 * @return bool Whether the value is an image URL.
	 */
	private function is_image_url( $str = '' ) {

		if ( ! is_string( $str ) || ! preg_match( '#^https?://#i', $str ) ) {
			return false;
		}

		return (bool) preg_match( '/\.(jpe?g|png|gif|webp)\b/i', $str );

	}

	/**
	 * Taken from the core media_sideload_image function and
	 * modified to return an object of data instead of html.
	 *
	 * @param string $file The image URL.
	 * @return stdClass|\WP_Error Object containing image data or an error.
	 */
	private function sideload_image( $file ) {

		$data = new stdClass();

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		// Set variables for storage, fix file filename for query strings.
		if ( ! preg_match( '/[^\?]+\.(jpe?g|png|gif|webp)\b/i', $file, $matches ) ) {
			return $data;
		}

		$file_array         = array();
		$file_array['name'] = wp_basename( $matches[0] );

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
		$data->height        = isset( $meta['height'] ) ? $meta['height'] : 0;
		$data->width         = isset( $meta['width'] ) ? $meta['width'] : 0;

		return $data;

	}

}
