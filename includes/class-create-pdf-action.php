<?php
namespace SR_PDF;

use ElementorPro\Modules\Forms\Classes\Action_Base;
use Dompdf\Dompdf;
use Dompdf\Options;

if ( ! defined( 'ABSPATH' ) ) exit;

class Create_PDF_Action extends Action_Base {

	public function get_name() {
		return 'create_pdf';
	}

	public function get_label() {
		return __( 'Create PDF', 'smart-referrals-pdf-addon' );
	}

	public function register_settings_section( $widget ) {
		// No se necesitan opciones en el editor
	}

	public function run( $record, $ajax_handler ) {
		$fields = $record->get( 'fields' );

		// 🎯 Generar código de presupuesto secuencial por mes
		$year = date('Y');
		$letras_mes = [ 'A','B','C','D','E','F','G','H','I','J','K','L' ];
		$letra = $letras_mes[ (int)date('n') - 1 ];
		$counter_key = 'smart_referrals_pdf_counter_' . $year . '_' . $letra;
		$numero = (int) get_option( $counter_key, 0 ) + 1;
		update_option( $counter_key, $numero );
		$presupuesto = sprintf('%s-%s%03d', $year, $letra, $numero);

		// 🧾 Datos del formulario
		$cliente  = $fields['contract_customer']['value'] ?? 'Cliente desconocido';
		$servicio = $fields['servicio']['value'] ?? 'Servicio no definido';
		$total    = $fields['total']['value'] ?? 'USD 0.00';

		// 📸 Logo
		$logo_url = SR_PDF_ADDON_URL . 'assets/img/logo.svg';

		// 📄 Cargar plantilla
		$template_path = SR_PDF_ADDON_PATH . 'templates/contrato.html';
		$data = [
			'presupuesto'   => $presupuesto,
			'cliente'       => $cliente,
			'servicio'      => $servicio,
			'fecha'         => date('d/m/Y'),
			'clausula1'     => 'La duración del contrato será hasta la completa ejecución del servicio acordado entre ambas partes.',
			'clausula2'     => 'El cliente deberá realizar un pago inicial del 50% del costo total para comenzar el desarrollo.',
			'clausula3'     => 'El cliente retiene los derechos de propiedad intelectual del diseño final aprobado.',
			'total'         => $total,
			'tiempo'        => '3 días laborales',
			'formas_pago'   => 'Efectivo, PayPal, Transferencia Bancaria, USDT',
			'logo_url'      => $logo_url,
		];
		$html = sr_pdf_render_template( $template_path, $data );

		// 🖨️ Crear PDF
		$options = new Options();
		$options->set('defaultFont', 'DejaVu Sans');
		$options->set('isRemoteEnabled', true);
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isPhpEnabled', false);

		$dompdf = new Dompdf($options);

		// ✅ Usamos tamaño estándar A4
		$dompdf->setPaper('A4', 'portrait');

		$dompdf->loadHtml($html);
		$dompdf->render();

		// 📄 Numeración de páginas
		$canvas = $dompdf->get_canvas();
		$canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
			$text = sprintf("Página %02d de %02d", $pageNumber, $pageCount);
			$font = $fontMetrics->getFont('DejaVu Sans', 'normal');
			$size = 10;
			$width = $fontMetrics->getTextWidth($text, $font, $size);
			$canvas->text($canvas->get_width() - $width - 20, 30, $text, $font, $size);
		});

		// 💾 Guardar archivo
		$upload_dir = wp_upload_dir();
		$filename = 'presupuesto_' . time() . '.pdf';
		$upload_path = trailingslashit($upload_dir['basedir']) . $filename;
		$pdf_url = trailingslashit($upload_dir['baseurl']) . $filename;
		file_put_contents($upload_path, $dompdf->output());

		// 📤 Respuesta AJAX
		$ajax_handler->add_response_data('meta', [
			'generated_pdf_url' => $pdf_url
		]);
	}

	public function on_export( $element ) {
		return $element;
	}
}
