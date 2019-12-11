/**
 * Used global objects:
 * - wp
 * - ajaxurl
 * - jQuery
 * - customizerResetObj
 */
(function ($) {
	var $buttonsWrapper = $('<div class="customizer-reset-footer"></div>');

	var $resetButton = $(
		'<button name="customizer-reset" class="button-primary customizer-reset-button">' + customizerResetObj.buttons.reset.text + '</button>'
	);

	var $exportButton = $(
		'<a href="' + customizerResetObj.customizerUrl + '?action=customizer_export&nonce=' + customizerResetObj.nonces.export + '" class="customizer-export-link hint--top" data-hint="' + customizerResetObj.buttons.export.text + '"><img alt="' + customizerResetObj.buttons.export.text + '" src="' + customizerResetObj.pluginUrl + '/assets/images/export.svg"></a>'
	);

	var $importButton = $(
		'<a href="' + customizerResetObj.customizerUrl + '?action=customizer_import&nonce=' + customizerResetObj.nonces.import + '" class="customizer-export-link hint--top" data-hint="' + customizerResetObj.buttons.import.text + '"><img alt="' + customizerResetObj.buttons.import.text + '" src="' + customizerResetObj.pluginUrl + '/assets/images/import.svg"></a>'
	);

	$resetButton.on('click', resetCustomizer);

	function resetCustomizer(e) {
		e.preventDefault();

		if (!confirm(customizerResetObj.dialogs.resetWarning)) return;

		$resetButton.attr('disabled', true);

		$.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				wp_customize: 'on',
				action: 'customizer_reset',
				nonce: customizerResetObj.nonces.reset
			}
		}).done(function (r) {
			if (!r || !r.success) return;

			wp.customize.state('saved').set(true);
			location.reload();
		}).always(function () {
			$resetButton.attr('disabled', false);
		});
	}

	$buttonsWrapper.append($resetButton);
	$buttonsWrapper.append($exportButton);
	$buttonsWrapper.append($importButton);
	$('#customize-footer-actions').prepend($buttonsWrapper);
})(jQuery);
