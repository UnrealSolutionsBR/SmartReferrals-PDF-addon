(function($) {
	$(window).on('elementor-pro/forms/new', function(event, form) {
		form.$el.on('submit_success', function(e, response) {
			if (response.data && response.data.meta && response.data.meta.generated_pdf_url) {
				let pdfUrl = response.data.meta.generated_pdf_url;

				const link = document.createElement('a');
				link.href = pdfUrl;
				link.download = 'formulario.pdf';
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			}
		});
	});
})(jQuery);
