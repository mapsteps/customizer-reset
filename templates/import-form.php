<?php
/**
 * Template for the customizer import form.
 *
 * @package Customizer_Reset
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );
?>

<form action="" method="post" class="customizer-import-form" enctype="multipart/form-data">
	<input type="hidden" name="action" value="customizer_import">
	<?php wp_nonce_field( 'customizer-import', 'nonce' ); ?>
	<div class="customizer-import-inner">
		<input type="file" id="customizer_import_file" name="customizer_import_file" class="customizer-import-file" accept=".json,application/json">
		<label class="customizer-import-picker" for="customizer_import_file">
			<span class="dashicons dashicons-upload" aria-hidden="true"></span>
			<span class="customizer-import-filename" data-placeholder="<?php esc_attr_e( 'Choose a .json file', 'customizer-reset' ); ?>"><?php esc_html_e( 'Choose a .json file', 'customizer-reset' ); ?></span>
		</label>
		<button type="submit" class="button button-primary customizer-import-submit" disabled><?php esc_html_e( 'Import', 'customizer-reset' ); ?></button>
		<button type="button" class="close customizer-reset-icon-button" aria-label="<?php esc_attr_e( 'Close', 'customizer-reset' ); ?>"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
	</div>
</form>
