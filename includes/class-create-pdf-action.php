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
		// Sin configuraciones por ahora
	}

	public function run( $record, $ajax_handler ) {
		$fields = $record->get( 'fields' );
		$html = '<h1>Datos del formulario</h1><ul>';

		foreach ( $fields as $id => $field ) {
			$label = $field['title'];
			$value = is_array( $field['value'] ) ? implode(', ', $field['value']) : $field['value'];
			$html .= "<li><strong>$label:</strong> $value</li>";
		}

		$html .= '</ul>';

		$options = new Options();
		$options->set('defaultFont', 'DejaVu Sans');
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();

		// Guardar PDF en /wp-content/uploads/
		$upload_dir = wp_upload_dir();
		$filename = 'formulario_' . time() . '.pdf';
		$upload_path = trailingslashit($upload_dir['basedir']) . $filename;
		$pdf_url = trailingslashit($upload_dir['baseurl']) . $filename;

		file_put_contents($upload_path, $dompdf->output());

		// Guardar la URL del PDF en la metadata
		$record->add_meta( 'generated_pdf_url', $pdf_url );
	}

	public function on_export( $element ) {
		return $element;
	}
}
