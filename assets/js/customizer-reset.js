/**
 * Setting up customizer reset.
 *
 * Used global objects:
 * - wp
 * - ajaxurl
 * - jQuery
 * - customizerResetObj
 */
(function ($) {
	/**
	 * Import form popup.
	 * 
	 * @var object importFormPopup The tinglejs object.
	 */
	var importFormPopup;

	/**
	 * Setup the flow.
	 */
	function init() {
		setupOutput();
	}

	/**
	 * Setup output:
	 * - reset button
	 * - export & import buttons
	 * - import form
	 */
	function setupOutput() {
		var $buttonsWrapper = $('<div class="customizer-reset-footer"></div>');

		var $resetButton = $(
			'<button name="customizer-reset" class="button-primary customizer-reset-button"><img src="' + customizerResetObj.pluginUrl + '/assets/images/trash.svg">' + customizerResetObj.buttons.reset.text + '</button>'
		);

		var $exportButton = $(
			'<a href="' + customizerResetObj.customizerUrl + '?action=customizer_export&nonce=' + customizerResetObj.nonces.export + '" class="customizer-export-import customizer-export-link hint--top"><img alt="' + customizerResetObj.buttons.export.text + '" src="' + customizerResetObj.pluginUrl + '/assets/images/export.svg"><span class="customizer-export-hint">' + customizerResetObj.buttons.export.text + '</span></a>'
		);

		var $importButton = $(
			'<a href="" class="customizer-export-import customizer-import-trigger hint--top"><img alt="' + customizerResetObj.buttons.import.text + '" src="' + customizerResetObj.pluginUrl + '/assets/images/import.svg"><span class="customizer-export-hint">' + customizerResetObj.buttons.import.text + '</span></a>'
		);

		$resetButton.on('click', resetCustomizer);
		$importButton.on('click', openImportForm);

		$buttonsWrapper.append($resetButton);
		$buttonsWrapper.append($exportButton);
		$buttonsWrapper.append($importButton);

		$('#customize-footer-actions').prepend($buttonsWrapper);
		$('.customizer-reset-footer').append(customizerResetObj.importForm.templates);
		$('.customizer-import-form .close').on('click', closeImportForm);
		$('.customizer-import-form').on('submit', showImportWarning);
	}

	/**
	 * Reset customizer.
	 * 
	 * @param Event e Event object.
	 */
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

	function openImportForm(e) {
		e.preventDefault();
		$('.customizer-import-form').addClass('is-expanded');
	}

	function closeImportForm(e) {
		e.preventDefault();
		$('.customizer-import-form').removeClass('is-expanded');
	}

	function showImportWarning(e) {
		e.preventDefault();

		if (confirm(customizerResetObj.dialogs.importWarning)) this.submit();
	}

	// Start!
	init();

})(jQuery);
