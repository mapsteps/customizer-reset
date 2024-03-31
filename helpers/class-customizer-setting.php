<?php
/**
 * Customizer Setting.
 *
 * @package Customizer_Reset
 */

namespace CustomizerReset\Helpers;

use WP_Customize_Setting;

/**
 * A class that extends WP_Customize_Setting so we can access
 * the protected updated method when importing options.
 */
final class Customizer_Setting extends WP_Customize_Setting {

	/**
	 * Import an option value for this setting.
	 *
	 * @param mixed $value The option value.
	 * @return void
	 */
	public function import( $value ) {

		$this->update( $value );

	}

}
