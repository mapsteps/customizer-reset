/**
 * Used global objects:
 * - wp
 * - ajaxurl
 * - jQuery
 * - customizerResetObj
 */
(function ($) {
	var $button = $(
		'<button type="submit" name="customizer-reset" id="customizer-reset" class="button customizer-reset-button">' + customizerResetObj.buttonText + '</button>'
	);

	$button.on('click', function (event) {
		event.preventDefault();

		var data = {
			wp_customize: 'on',
			action: 'customizer_reset',
			nonce: customizerResetObj.nonce
		};

		if (!confirm(customizerResetObj.confirmText)) return;

		$button.attr('disabled', 'disabled');

		$.post(ajaxurl, data, function () {
			wp.customize.state('saved').set(true);
			location.reload();
		});
	});

	$('#customize-header-actions').append($button);
})(jQuery);
