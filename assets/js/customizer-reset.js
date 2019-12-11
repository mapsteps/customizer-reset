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
		setupButtons();
		setupPopup();
	}

	/**
	 * Setup reset, export, & import customizer buttons.
	 */
	function setupButtons() {
		var $buttonsWrapper = $('<div class="customizer-reset-footer"></div>');

		var $resetButton = $(
			'<button name="customizer-reset" class="button-primary customizer-reset-button">' + customizerResetObj.buttons.reset.text + '</button>'
		);

		var $exportButton = $(
			'<a href="' + customizerResetObj.customizerUrl + '?action=customizer_export&nonce=' + customizerResetObj.nonces.export + '" class="customizer-export-import customizer-export-link hint--top"><img alt="' + customizerResetObj.buttons.export.text + '" src="' + customizerResetObj.pluginUrl + '/assets/images/export.svg"><span class="customizer-export-hint">' + customizerResetObj.buttons.export.text + '</span></a>'
		);

		var $importButton = $(
			'<a href="" class="customizer-export-import customizer-import-trigger hint--top"><img alt="' + customizerResetObj.buttons.import.text + '" src="' + customizerResetObj.pluginUrl + '/assets/images/import.svg"><span class="customizer-export-hint">' + customizerResetObj.buttons.import.text + '</span></a>'
		);

		$resetButton.on('click', resetCustomizer);

		$buttonsWrapper.append($resetButton);
		$buttonsWrapper.append($exportButton);
		$buttonsWrapper.append($importButton);
		$('#customize-footer-actions').prepend($buttonsWrapper);
	}

	/**
	 * Setup import form popup.
	 */
	function setupPopup() {
		// Instanciate new modal.
		importFormPopup = new tingle.modal({
			footer: true,
			closeMethods: ['escape', 'button']
		});

		// Set content.
		importFormPopup.setContent(customizerResetObj.importForm.templates);

		// Add submit button.
		importFormPopup.addFooterBtn(customizerResetObj.importForm.labels.submit, 'button button-primary button-large tingle-btn--pull-right', function () {
			document.querySelector('.customizer-import-form').submit();
		});

		// Add cancel button.
		importFormPopup.addFooterBtn(customizerResetObj.importForm.labels.cancel, 'button button-large tingle-btn--pull-right cancel-button', function () {
			importFormPopup.close();
		});

		// Set the open trigger.
		document.querySelector('.customizer-import-trigger').addEventListener('click', function (e) {
			e.preventDefault();
			importFormPopup.open();
		})
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

	// Start!
	init();

})(jQuery);
