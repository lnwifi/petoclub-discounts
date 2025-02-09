<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Asegura que no se acceda directamente al archivo

class Cupones_API {

    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_api_routes' ] );
    }

    public function register_api_routes() {
        register_rest_route( 'petoclub/v1', '/cupon/generar/', [
            'methods'  => 'POST',
            'callback' => [ $this, 'generar_cupon' ],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route( 'petoclub/v1', '/cupon/validar/', [
            'methods'  => 'POST',
            'callback' => [ $this, 'validar_cupon' ],
            'permission_callback' => '__return_true',
        ]);
    }

    public function generar_cupon($request) {
        $local_id = $request->get_param('local_id');
        $user_id  = $request->get_param('user_id');

        if (!$local_id || !$user_id) {
            return new WP_Error('missing_params', 'Faltan parámetros: local_id o user_id.', ['status' => 400]);
        }

        // Obtener cupones del local
        $cupones = get_post_meta($local_id, 'cupones', true) ?: [];

        $ahora = time();
        $duracion_cupon = 15 * 60; // 15 minutos en segundos
        $cupon_activo = null;

        // Verificar si ya hay un cupón activo para este usuario
        foreach ($cupones as $cupon) {
            if ($cupon['user_id'] == $user_id && $cupon['estado'] === 'pendiente') {
                $creado = strtotime($cupon['creado']);
                if (($ahora - $creado) < $duracion_cupon) {
                    return rest_ensure_response([
                        'codigo' => $cupon['codigo'],
                        'qr_local' => "https://cupones.petoclub.com.ar/validar-cupon?local_id=" . $local_id,
                        'mensaje' => 'Ya tienes un cupón activo. Escanea el QR del local para validarlo.'
                    ]);
                }
            }
        }

        // Generar código aleatorio único (8 caracteres)
        $codigo = strtoupper(substr(md5(time() . $user_id), 0, 8));
        $nuevo_cupon = [
            'codigo' => $codigo,
            'user_id' => $user_id,
            'local_id' => $local_id,  // Guardamos el ID del local aquí
            'estado' => 'pendiente',
            'creado' => date('Y-m-d H:i:s') // Guardar fecha y hora de creación
        ];

        $cupones[] = $nuevo_cupon;
        update_post_meta($local_id, 'cupones', $cupones);

        // URL del QR del local
        $qr_local = "https://cupones.petoclub.com.ar/validar-cupon?local_id={$local_id}";

        return rest_ensure_response([
            'codigo' => $codigo,
            'qr_local' => $qr_local, // QR que el usuario debe escanear en el local
            'mensaje' => 'Cupón generado con éxito. Escanea el QR del local para validarlo.'
        ]);
    }

    public function validar_cupon($request) {
        $codigo = $request->get_param('codigo');
        $local_id = $request->get_param('local_id');
        $user_id  = $request->get_param('user_id');

        if (!$codigo || !$local_id || !$user_id) {
            return new WP_Error('missing_params', 'Faltan parámetros: local_id, user_id o código.', ['status' => 400]);
        }

        // Buscar el cupón en la base de datos del local
        $cupones = get_post_meta($local_id, 'cupones', true) ?: [];

        foreach ($cupones as &$cupon) {
            if ($cupon['codigo'] === $codigo && $cupon['estado'] === 'pendiente' && $cupon['user_id'] == $user_id) {
                // **🚀 Validación extra: Asegurar que el cupón pertenece a este local**
                if ($cupon['local_id'] != $local_id) {
                    return new WP_Error('invalid_local', '❌ Este cupón no pertenece a este local.', ['status' => 403]);
                }

                // Marcar el cupón como canjeado
                $cupon['estado'] = 'canjeado';
                update_post_meta($local_id, 'cupones', $cupones);

                // Guardar que el usuario ya canjeó un cupón hoy en este local
                $user_cupones = get_user_meta($user_id, 'cupones_canjeados', true) ?: [];
                $hoy = date('Y-m-d');
                $user_cupones[$local_id] = $hoy;
                update_user_meta($user_id, 'cupones_canjeados', $user_cupones);

                return rest_ensure_response(['mensaje' => '✅ Cupón validado correctamente en este local.']);
            }
        }

        return new WP_Error('invalid_code', '❌ Cupón inválido o ya canjeado.', ['status' => 400]);
    }

    public function puede_generar_cupon($user_id, $local_id) {
        $user_cupones = get_user_meta($user_id, 'cupones_canjeados', true) ?: [];
        $hoy = date('Y-m-d');

        // Si el usuario ya canjeó un cupón en este local hoy, no puede generar otro
        return !isset($user_cupones[$local_id]) || $user_cupones[$local_id] !== $hoy;
    }
}