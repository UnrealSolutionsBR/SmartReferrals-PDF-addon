<?php
function sr_pdf_render_template( $template_path, $data = [] ) {
	if ( ! file_exists( $template_path ) ) return '';
	$html = file_get_contents( $template_path );
	foreach ( $data as $key => $value ) {
		$html = str_replace( '{{' . $key . '}}', $value, $html );
	}
	return $html;
}
