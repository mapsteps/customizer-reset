<?php
/**
 * Template for displaying customizer import form.
 *
 * @package Customizer_Reset
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

ob_start();
?>

<h2 class="customizer-import-header customizer-import-title">
	<?php _e( 'Import Customizer Settings For This Theme', 'wp-quickmenu' ); ?>
</h2>

<div class="customizer-import-content">
	<form action="" method="post" class="customizer-import-form" enctype="multipart/form-data">
		<input type="hidden" name="action" value="customizer_import">
		<?php wp_nonce_field( 'customizer-import', 'nonce' ); ?>
		<div class="field">
			<label class="label" for="customizer_import_file">
				<?php _e( 'Choose exported json file.', 'customizer-reset' ); ?>
			</label>
			<div class="control">
				<input type="file" id="customizer_import_file" name="customizer_import_file" class="customizer-import-file" accept="application/json">
			</div>
		</div>
		<div class="field">
			<div class="control">
				<label class="label" for="customizer_import_images">
					<input type="checkbox" id="customizer_import_images" name="customizer_import_images" value="1">
					<?php _e( 'Download and import image files?', 'customizer-reset' ); ?>
				</label>
			</div>
		</div>
	</form>
</div>

<?php
$customizer_import_form = ob_get_clean();
