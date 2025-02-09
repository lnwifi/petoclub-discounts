<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Evita acceso directo

class Locales_CPT {

    public function __construct() {
        add_action( 'init', [ $this, 'registrar_locales_cpt' ] );
    }

    public function registrar_locales_cpt() {
        $labels = [
            'name'               => 'Locales',
            'singular_name'      => 'Local',
            'menu_name'          => 'Locales',
            'name_admin_bar'     => 'Local',
            'add_new'            => 'Agregar Nuevo',
            'add_new_item'       => 'Agregar Nuevo Local',
            'new_item'           => 'Nuevo Local',
            'edit_item'          => 'Editar Local',
            'view_item'          => 'Ver Local',
            'all_items'          => 'Todos los Locales',
            'search_items'       => 'Buscar Locales',
            'parent_item_colon'  => 'Local Padre:',
            'not_found'          => 'No se encontraron locales.',
            'not_found_in_trash' => 'No se encontraron locales en la papelera.',
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'local' ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => [ 'title', 'editor', 'thumbnail' ],
        ];

        register_post_type( 'local', $args );
    }
}

new Locales_CPT();
