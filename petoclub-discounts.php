<?php
/**
 * Plugin Name: PetoClub Discounts
 * Plugin URI:  https://tusitio.com
 * Description: Plugin para gestionar locales y cupones de descuentos en PetoClub.
 * Version:     1.1
 * Author:      Tu Nombre
 * License:     GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Evita acceso directo

// Definir la ruta del plugin
define( 'PETOCLUB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Incluir archivos del plugin
require_once PETOCLUB_PLUGIN_PATH . 'includes/class-locales-cpt.php';
require_once PETOCLUB_PLUGIN_PATH . 'includes/class-locales-metabox.php';
require_once PETOCLUB_PLUGIN_PATH . 'includes/class-locales-api.php';
require_once PETOCLUB_PLUGIN_PATH . 'includes/class-cupones-api.php';
require_once PETOCLUB_PLUGIN_PATH . 'includes/class-cupones-cpt.php';

// Inicializar clases
function petoclub_init() {
    new Locales_CPT();        // Registrar el Custom Post Type "Locales"
    new Locales_Metabox();    // Agregar campos personalizados a locales
    new Locales_API();        // API REST para locales
    new Cupones_API();        // API REST para cupones
}
add_action( 'plugins_loaded', 'petoclub_init' );


function petoclub_activate() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'petoclub_activate' );


function petoclub_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'petoclub_deactivate' );

