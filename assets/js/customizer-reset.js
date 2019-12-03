/**
 * Used global objects:
 * - wp
 * - ajaxurl
 * - jQuery
 * - customizerResetObj
 */
(function ($) {

	var $footerButton = $(
		'<div class="customizer-reset-footer"><button name="customizer-reset" class="button-primary customizer-reset-button">' + customizerResetObj.footerButtonText + '</button></div>'
	);

	$footerButton.on('click', resetCustomizer);

	function resetCustomizer(e) {
		e.preventDefault();

		if (!confirm(customizerResetObj.confirmationText)) return;

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
			$footerButton.attr('disabled', false);
		});
	}

	$('#customize-footer-actions').prepend($footerButton);
})(jQuery);
