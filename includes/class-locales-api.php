<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Locales_API {

    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_api_routes' ] );
    }

    public function register_api_routes() {
        register_rest_route('petoclub/v1', '/locales/', [
            'methods'  => 'GET',
            'callback' => [ $this, 'obtener_locales' ],
            'permission_callback' => '__return_true', // Permitir acceso público
        ]);

        register_rest_route('petoclub/v1', '/local/detalles/', [
            'methods'  => 'GET',
            'callback' => [ $this, 'obtener_detalles_local' ],
            'permission_callback' => '__return_true',
        ]);
    }

    public function obtener_locales($request) {
        $args = [
            'post_type'      => 'local',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];

        $query = new WP_Query($args);
        $locales = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $local_id = get_the_ID();

                $locales[] = [
                    'id'    => $local_id,
                    'title' => get_the_title(),
                ];
            }
            wp_reset_postdata();
        }

        return rest_ensure_response($locales);
    }

    public function obtener_detalles_local($request) {
        $local_id = $request->get_param('local_id');

        if (!$local_id) {
            return new WP_Error('missing_params', 'Falta el ID del local.', ['status' => 400]);
        }

        $post = get_post($local_id);
        if (!$post || $post->post_type !== 'local') {
            return new WP_Error('not_found', 'No se encontró el local.', ['status' => 404]);
        }

        // Obtener cupones asociados al local
        $cupones = [];
        $cupon_query = new WP_Query([
            'post_type'  => 'cupon',
            'meta_key'   => 'local_id',
            'meta_value' => $local_id,
            'post_status' => 'publish',
        ]);

        if ($cupon_query->have_posts()) {
            while ($cupon_query->have_posts()) {
                $cupon_query->the_post();
                $cupones[] = [
                    'titulo'          => get_the_title(),
                    'descripcion'     => get_the_content(),
                    'dias_disponibles'=> get_post_meta(get_the_ID(), 'dias_disponibles', true),
                    'formas_pago'     => get_post_meta(get_the_ID(), 'formas_pago', true),
                    'validez_inicio'  => get_post_meta(get_the_ID(), 'validez_inicio', true),
                    'validez_fin'     => get_post_meta(get_the_ID(), 'validez_fin', true),
                ];
            }
            wp_reset_postdata();
        }

        return rest_ensure_response([
            'local_id'       => $local_id,
            'nombre'         => get_the_title($local_id),
            'descripcion'    => get_post_meta($local_id, 'descripcion', true),
            'cupones'        => $cupones,
        ]);
    }
}

new Locales_API();
?>