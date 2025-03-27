<?php
/**
 * Plugin Name: Smart Referrals - PDF addon
 * Description: Añade una acción personalizada "Create PDF" a los formularios de Elementor que genera y descarga un PDF tras el submit.
 * Plugin URI: https://unrealsolutions.com.br/
 * Author: Unreal Solutions
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SR_PDF_ADDON_PATH', plugin_dir_path( __FILE__ ) );
define( 'SR_PDF_ADDON_URL', plugin_dir_url( __FILE__ ) );

// Cargar autoload de Composer (verifica si existe)
$autoload = SR_PDF_ADDON_PATH . 'vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
} else {
	add_action( 'admin_notices', function () {
		echo '<div class="notice notice-error"><p><strong>Smart Referrals - PDF addon:</strong> No se encontró <code>vendor/autoload.php</code>. Ejecuta <code>composer install</code> o sube la carpeta <code>/vendor</code>.</p></div>';
	} );
	return;
}

// Registrar la acción personalizada para formularios de Elementor
add_action( 'plugins_loaded', function () {
	if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		add_action( 'elementor_pro/forms/actions/register', function( $manager ) {
			require_once SR_PDF_ADDON_PATH . 'includes/class-create-pdf-action.php';
			$manager->register( new \SR_PDF\Create_PDF_Action() );
		}, 10, 1 );
	} else {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-warning"><p><strong>Smart Referrals - PDF addon:</strong> Este plugin requiere Elementor Pro activo.</p></div>';
		} );
	}
}, 20 );

// Cargar el JS para descarga automática del PDF
add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'elementor_pro_load_plugin' ) ) {
		wp_enqueue_script(
			'sr-pdf-download',
			SR_PDF_ADDON_URL . 'assets/js/pdf-download.js',
			[ 'jquery' ],
			'1.0',
			true
		);
	}
});
