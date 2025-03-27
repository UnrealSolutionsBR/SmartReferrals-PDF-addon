document.addEventListener('DOMContentLoaded', function () {
	jQuery(window).on('elementor-pro/forms/new', function (event, form) {
		form.$el.on('submit_success', function (e, response) {
			if (
				response.data &&
				response.data.meta &&
				response.data.meta.generated_pdf_url
			) {
				const pdfUrl = response.data.meta.generated_pdf_url;
				console.log("PDF URL recibida:", pdfUrl);

				// Mostrar mensaje visual
				const notice = document.createElement('div');
				notice.className = 'sr-pdf-notice';
				notice.innerText = '✅ Tu PDF está listo, la descarga ha comenzado.';
				Object.assign(notice.style, {
					background: '#DFF0D8',
					color: '#3C763D',
					padding: '12px',
					marginTop: '15px',
					border: '1px solid #D6E9C6',
					borderRadius: '5px',
					fontWeight: 'bold'
				});
				form.$el[0].appendChild(notice);

				// Forzar descarga
				const link = document.createElement('a');
				link.href = pdfUrl;
				link.download = 'formulario.pdf';

				// 👇 Necesario para permitir descarga directa en algunos navegadores
				link.target = '_blank';
				link.rel = 'noopener noreferrer';

				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			}
		});
	});
});
