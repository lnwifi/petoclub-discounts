<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Cupones_CPT {

    public function __construct() {
        add_action( 'init', [ $this, 'registrar_cupones_cpt' ] );
    }

    public function registrar_cupones_cpt() {
        $labels = [
            'name'               => 'Cupones',
            'singular_name'      => 'Cupón',
            'menu_name'          => 'Cupones',
            'name_admin_bar'     => 'Cupón',
            'add_new'            => 'Agregar Nuevo',
            'add_new_item'       => 'Agregar Nuevo Cupón',
            'new_item'           => 'Nuevo Cupón',
            'edit_item'          => 'Editar Cupón',
            'view_item'          => 'Ver Cupón',
            'all_items'          => 'Todos los Cupones',
            'search_items'       => 'Buscar Cupones',
            'parent_item_colon'  => 'Cupón Padre:',
            'not_found'          => 'No se encontraron cupones.',
            'not_found_in_trash' => 'No se encontraron cupones en la papelera.',
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'cupon' ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => [ 'title', 'editor' ],
        ];

        register_post_type( 'cupon', $args );
    }
}

new Cupones_CPT();
?>