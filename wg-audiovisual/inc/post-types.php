<?php
/**
 * Tipos de conteúdo: serviços, tipos de evento, portfólio, depoimentos, clientes e leads.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'wg_register_post_types' );
function wg_register_post_types() {
	$common = array(
		'public'       => true,
		'show_in_rest' => true,
		'menu_position'=> 21,
		'has_archive'  => true,
	);

	register_post_type(
		'wg_servico',
		array_merge(
			$common,
			array(
				'labels'    => wg_labels( 'Solução', 'Soluções', false ),
				'menu_icon' => 'dashicons-format-audio',
				'rewrite'   => array( 'slug' => 'solucoes', 'with_front' => false ),
				'supports'  => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			)
		)
	);

	register_post_type(
		'wg_evento',
		array_merge(
			$common,
			array(
				'labels'    => wg_labels( 'Tipo de evento', 'Tipos de evento', true ),
				'menu_icon' => 'dashicons-groups',
				'rewrite'   => array( 'slug' => 'eventos', 'with_front' => false ),
				'supports'  => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			)
		)
	);

	register_post_type(
		'wg_projeto',
		array_merge(
			$common,
			array(
				'labels'    => wg_labels( 'Projeto', 'Portfólio', true ),
				'menu_icon' => 'dashicons-format-gallery',
				'rewrite'   => array( 'slug' => 'portfolio', 'with_front' => false ),
				'supports'  => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			)
		)
	);

	register_taxonomy(
		'wg_segmento',
		'wg_projeto',
		array(
			'labels'            => array(
				'name'          => __( 'Segmentos', 'wg' ),
				'singular_name' => __( 'Segmento', 'wg' ),
				'add_new_item'  => __( 'Adicionar segmento', 'wg' ),
			),
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'portfolio/segmento', 'with_front' => false ),
		)
	);

	register_post_type(
		'wg_depoimento',
		array(
			'labels'        => wg_labels( 'Depoimento', 'Depoimentos', true ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'menu_position' => 22,
			'menu_icon'     => 'dashicons-format-quote',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'wg_cliente',
		array(
			'labels'        => wg_labels( 'Cliente ou parceiro', 'Clientes e parceiros', true ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'menu_position' => 22,
			'menu_icon'     => 'dashicons-awards',
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'wg_lead',
		array(
			'labels'              => wg_labels( 'Pedido de orçamento', 'Pedidos de orçamento', true ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'menu_position'       => 3,
			'menu_icon'           => 'dashicons-email-alt',
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}

function wg_labels( $singular, $plural, $male = true ) {
	$new = $male ? 'Novo' : 'Nova';
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'add_new'            => __( 'Adicionar', 'wg' ),
		'add_new_item'       => sprintf( '%s %s', $new, mb_strtolower( $singular ) ),
		'edit_item'          => sprintf( __( 'Editar %s', 'wg' ), mb_strtolower( $singular ) ),
		'new_item'           => sprintf( '%s %s', $new, mb_strtolower( $singular ) ),
		'view_item'          => sprintf( __( 'Ver %s', 'wg' ), mb_strtolower( $singular ) ),
		'search_items'       => sprintf( __( 'Buscar %s', 'wg' ), mb_strtolower( $plural ) ),
		'not_found'          => __( 'Nada encontrado', 'wg' ),
		'not_found_in_trash' => __( 'Nada na lixeira', 'wg' ),
		'all_items'          => $plural,
		'menu_name'          => $plural,
	);
}

/**
 * Ordena serviços e eventos pela ordem definida no painel.
 */
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_post_type_archive( array( 'wg_servico', 'wg_evento' ) ) ) {
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		$q->set( 'posts_per_page', 50 );
	}
	if ( $q->is_post_type_archive( 'wg_projeto' ) || $q->is_tax( 'wg_segmento' ) ) {
		$q->set( 'posts_per_page', 24 );
	}
} );

/**
 * Tipos de evento sem página própria não têm página individual (evita conteúdo fraco no Google).
 */
add_action( 'template_redirect', function () {
	if ( is_singular( 'wg_evento' ) && ! wg_event_has_page( get_queried_object_id() ) ) {
		wp_safe_redirect( get_post_type_archive_link( 'wg_evento' ), 301 );
		exit;
	}
} );

function wg_event_has_page( $id ) {
	return '1' === get_post_meta( $id, '_wg_has_page', true );
}

/**
 * Remove leads e tipos de evento sem página do sitemap do WordPress.
 */
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	unset( $types['wg_lead'], $types['wg_depoimento'], $types['wg_cliente'] );
	return $types;
} );
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $post_type ) {
	if ( 'wg_evento' === $post_type ) {
		$args['meta_query'] = array(
			array(
				'key'   => '_wg_has_page',
				'value' => '1',
			),
		);
	}
	return $args;
}, 10, 2 );
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );
