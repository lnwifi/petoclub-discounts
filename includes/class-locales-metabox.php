<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Evita acceso directo

class Locales_Metabox {

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'agregar_metaboxes' ] );
        add_action( 'save_post', [ $this, 'guardar_datos_metaboxes' ] );
    }

    public function agregar_metaboxes() {
        add_meta_box(
            'local_detalles',
            'Detalles del Local',
            [ $this, 'mostrar_metabox' ],
            'local',
            'normal',
            'high'
        );
    }

    public function mostrar_metabox($post) {
        // Recuperar valores guardados
        $descripcion = get_post_meta($post->ID, 'descripcion', true);
        $dias_disponibles = get_post_meta($post->ID, 'dias_disponibles', true);
        $formas_pago = get_post_meta($post->ID, 'formas_pago', true);
        $ubicacion = get_post_meta($post->ID, 'ubicacion', true);
        $redes_sociales = get_post_meta($post->ID, 'redes_sociales', true);

        // Campos del formulario
        ?>
        <p>
            <label for="descripcion">Descripci¨®n:</label>
            <textarea id="descripcion" name="descripcion" rows="4" class="widefat"><?php echo esc_textarea($descripcion); ?></textarea>
        </p>
        <p>
            <label for="dias_disponibles">D¨ªas Disponibles:</label>
            <input type="text" id="dias_disponibles" name="dias_disponibles" value="<?php echo esc_attr($dias_disponibles); ?>" class="widefat">
        </p>
        <p>
            <label for="formas_pago">Formas de Pago:</label>
            <input type="text" id="formas_pago" name="formas_pago" value="<?php echo esc_attr($formas_pago); ?>" class="widefat">
        </p>
        <p>
            <label for="ubicacion">Ubicaci¨®n:</label>
            <input type="text" id="ubicacion" name="ubicacion" value="<?php echo esc_attr($ubicacion); ?>" class="widefat">
        </p>
        <p>
            <label for="redes_sociales">Redes Sociales (JSON):</label>
            <textarea id="redes_sociales" name="redes_sociales" rows="4" class="widefat"><?php echo esc_textarea($redes_sociales); ?></textarea>
        </p>
        <?php
    }

    public function guardar_datos_metaboxes($post_id) {
        if (!isset($_POST['descripcion'])) return;
        update_post_meta($post_id, 'descripcion', sanitize_textarea_field($_POST['descripcion']));
        update_post_meta($post_id, 'dias_disponibles', sanitize_text_field($_POST['dias_disponibles']));
        update_post_meta($post_id, 'formas_pago', sanitize_text_field($_POST['formas_pago']));
        update_post_meta($post_id, 'ubicacion', sanitize_text_field($_POST['ubicacion']));
        update_post_meta($post_id, 'redes_sociales', sanitize_textarea_field($_POST['redes_sociales']));
    }
}

new Locales_Metabox();

