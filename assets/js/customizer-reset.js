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
	var obj = customizerResetObj;

	/**
	 * Setup the flow.
	 */
	function init() {
		setupOutput();
	}

	/**
	 * Build an icon button (export & import).
	 *
	 * @param {string} tag The tag name.
	 * @param {string} className The class name.
	 * @param {string} icon The icon file name.
	 * @param {string} label The label.
	 *
	 * @return {jQuery} The button.
	 */
	function iconButton(tag, className, icon, label) {
		var $button = $('<' + tag + '>')
			.addClass('customizer-export-import ' + className)
			.attr('aria-label', label);

		$('<img alt="">').attr('src', obj.pluginUrl + 'assets/images/' + icon).appendTo($button);
		$('<span class="customizer-export-import-hint" aria-hidden="true"></span>').text(label).appendTo($button);

		return $button;
	}

	/**
	 * Setup output:
	 * - reset button
	 * - export & import buttons
	 * - import form
	 */
	function setupOutput() {
		var $buttonsWrapper = $('<div class="customizer-reset-area customizer-reset-footer"></div>');

		var $resetButton = $('<button type="button" name="customizer-reset" class="button-primary customizer-reset-button"></button>');
		$('<img alt="">').attr('src', obj.pluginUrl + 'assets/images/trash.svg').appendTo($resetButton);
		$resetButton.append(document.createTextNode(obj.buttons.reset.text));

		var $exportButton = iconButton('a', 'customizer-export-link', 'export.svg', obj.buttons.export.text)
			.attr('href', obj.customizerUrl + '?action=customizer_export&nonce=' + encodeURIComponent(obj.nonces.export));

		var $importButton = iconButton('button', 'customizer-import-trigger', 'import.svg', obj.buttons.import.text)
			.attr({ type: 'button', 'aria-expanded': 'false' });

		$resetButton.on('click', resetCustomizer);
		$importButton.on('click', openImportForm);

		$buttonsWrapper.append($resetButton, $exportButton, $importButton);

		$('#customize-footer-actions').prepend($buttonsWrapper);
		$buttonsWrapper.append(obj.importForm.templates);
		$('.customizer-import-form .close').on('click', closeImportForm);
		$('#customizer_import_file').on('change', showFileName);
		$('.customizer-import-form').on('submit', showImportWarning);
	}

	/**
	 * Reset customizer.
	 *
	 * @param {Event} e Event object.
	 */
	function resetCustomizer(e) {
		var button = this;

		e.preventDefault();

		if (!confirm(obj.dialogs.resetWarning)) return;

		button.disabled = true;

		$.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				wp_customize: 'on',
				action: 'customizer_reset',
				nonce: obj.nonces.reset
			}
		}).done(function (r) {
			if (!r || !r.success) {
				button.disabled = false;
				alert(obj.dialogs.resetError);
				return;
			}

			// Skip the "unsaved changes" prompt, the page reloads with the reset values.
			wp.customize.state('saved').set(true);
			location.reload();
		}).fail(function () {
			button.disabled = false;
			alert(obj.dialogs.resetError);
		});
	}

	/**
	 * Open the import form.
	 *
	 * @param {Event} e Event object.
	 */
	function openImportForm(e) {
		e.preventDefault();
		$('.customizer-import-form').addClass('is-expanded');
		$('.customizer-import-trigger').attr('aria-expanded', 'true');
		// Focus without scrolling, the row is still sliding in.
		document.getElementById('customizer_import_file').focus({ preventScroll: true });
	}

	/**
	 * Close the import form.
	 *
	 * @param {Event} e Event object.
	 */
	function closeImportForm(e) {
		e.preventDefault();
		$('.customizer-import-form').removeClass('is-expanded');
		$('.customizer-import-trigger').attr('aria-expanded', 'false').get(0).focus({ preventScroll: true });
	}

	/**
	 * Show the chosen file name & enable the import button.
	 */
	function showFileName() {
		var file = this.files && this.files[0];
		var $name = $('.customizer-import-filename');

		$name.text(file ? file.name : $name.data('placeholder'));
		$('.customizer-import-picker').toggleClass('has-file', !!file);
		$('.customizer-import-submit').prop('disabled', !file);
	}

	/**
	 * Check the file & warn before importing.
	 *
	 * @param {Event} e Event object.
	 */
	function showImportWarning(e) {
		e.preventDefault();

		if (!$('#customizer_import_file').val()) {
			alert(obj.dialogs.emptyImport);
			return;
		}

		if (confirm(obj.dialogs.importWarning)) this.submit();
	}

	// Start!
	init();

})(jQuery);
