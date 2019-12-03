/**
 * Used global objects:
 * - wp
 * - ajaxurl
 * - jQuery
 * - customizerResetObj
 */
(function ($) {
	var $headerButton = $(
		'<button name="customizer-reset" class="button customizer-reset-button">' + customizerResetObj.headerButtonText + '</button>'
	);

	var $footerButton = $(
		'<div class="customizer-reset-footer"><button name="customizer-reset" class="button customizer-reset-button">' + customizerResetObj.footerButtonText + '</button></div>'
	);

	$headerButton.on('click', resetCustomizer);
	$footerButton.on('click', resetCustomizer);

	function resetCustomizer(e) {
		e.preventDefault();

		if (!confirm(customizerResetObj.confirmationText)) return;

		$headerButton.attr('disabled', true);
		$footerButton.attr('disabled', true);

		$.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				wp_customize: 'on',
				action: 'customizer_reset',
				nonce: customizerResetObj.nonce
			}
		}).done(function (r) {
			if (!r || !r.success) return;

			wp.customize.state('saved').set(true);
			location.reload();
		}).always(function () {
			$headerButton.attr('disabled', false);
			$footerButton.attr('disabled', false);
		});
	}

	$('#customize-header-actions').append($headerButton);
	$('#customize-footer-actions').prepend($footerButton);
})(jQuery);
