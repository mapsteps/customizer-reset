<?php
/**
 * Customizer export helper.
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset\Helpers;

/**
 * A class that handle customizer export.
 */
class Export extends Base {

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
	 * Export the customizer.
	 */
	public function export() {

		$theme    = get_stylesheet();
		$template = get_template();
		$charset  = get_option( 'blog_charset' );
		$mods     = get_theme_mods();
		$data     = array(
			'template' => $template,
			'mods'     => $mods ? $mods : array(),
			'options'  => array(),
		);

		// Get options from the Customizer API.
		$settings = $this->wp_customize->settings();

		foreach ( $settings as $key => $setting ) {

			if ( 'option' === $setting->type ) {

				// Don't save widget data.
				if ( 'widget_' === substr( strtolower( $key ), 0, 7 ) ) {
					continue;
				}

				// Don't save sidebar data.
				if ( 'sidebars_' === substr( strtolower( $key ), 0, 9 ) ) {
					continue;
				}

				// Don't save core options.
				if ( in_array( $key, $this->core_options, true ) ) {
					continue;
				}

				$data['options'][ $key ] = $setting->value();
			}
		}

		// Plugin developers can specify additional option keys to export.
		$option_keys = apply_filters( 'customizer_export_option_keys', array() );

		foreach ( $option_keys as $option_key ) {
			$data['options'][ $option_key ] = get_option( $option_key );
		}

		if ( function_exists( 'wp_get_custom_css_post' ) ) {
			$data['wp_css'] = wp_get_custom_css();
		}

		// Set the download headers.
		header( 'Content-disposition: attachment; filename=Customizer-Export-of-' . $theme . '.json' );
		header( 'Content-Type: application/octet-stream; charset=' . $charset );

		// Output the export data.
		echo wp_json_encode( $data );

		// Start the download.
		exit;

	}
}
