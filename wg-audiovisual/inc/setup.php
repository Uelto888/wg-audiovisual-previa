<?php
/**
 * Configuração do tema, menus, scripts e estilos.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'wg_setup' );
function wg_setup() {
	load_theme_textdomain( 'wg', WG_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 360,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'wg-card', 800, 560, true );
	add_image_size( 'wg-wide', 1600, 900, true );
	add_image_size( 'wg-logo', 320, 160, false );

	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'wg' ),
			'footer'  => __( 'Menu do rodapé', 'wg' ),
		)
	);
}

/**
 * Paleta no editor de blocos.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support(
		'editor-color-palette',
		array(
			array( 'name' => 'Magenta', 'slug' => 'magenta', 'color' => '#E6197A' ),
			array( 'name' => 'Preto palco', 'slug' => 'preto', 'color' => '#0B0C0F' ),
			array( 'name' => 'Grafite', 'slug' => 'grafite', 'color' => '#16181D' ),
			array( 'name' => 'Cinza', 'slug' => 'cinza', 'color' => '#A3A9B4' ),
			array( 'name' => 'Branco gelo', 'slug' => 'branco', 'color' => '#F3F4F6' ),
		)
	);
}, 11 );

add_action( 'wp_enqueue_scripts', 'wg_assets' );
function wg_assets() {
	$css = WG_DIR . '/assets/css/main.css';
	$js  = WG_DIR . '/assets/js/main.js';
	wp_enqueue_style( 'wg-main', WG_URI . '/assets/css/main.css', array(), WG_VERSION . '.' . ( file_exists( $css ) ? filemtime( $css ) : '' ) );
	wp_enqueue_script( 'wg-main', WG_URI . '/assets/js/main.js', array(), WG_VERSION . '.' . ( file_exists( $js ) ? filemtime( $js ) : '' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_localize_script(
		'wg-main',
		'WG',
		array(
			'ajax'      => admin_url( 'admin-ajax.php' ),
			'turnstile' => wg_opt( 'turnstile_site' ) ? 1 : 0,
		)
	);

	if ( is_page_template( 'templates/page-orcamento.php' ) && wg_opt( 'turnstile_site' ) ) {
		wp_enqueue_script( 'cf-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array( 'strategy' => 'defer' ) );
	}

	// O tema não usa os estilos globais de blocos fora do conteúdo.
	wp_dequeue_style( 'classic-theme-styles' );
}

/**
 * Pré-carrega as fontes principais.
 */
add_action( 'wp_head', function () {
	foreach ( array( 'archivo-800-sx', 'inter-400' ) as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( WG_URI . '/assets/fonts/' . $font . '.woff2' ) );
	}
	if ( is_front_page() && wg_opt( 'hero_video' ) ) {
		printf( '<link rel="preload" href="%s" as="image" fetchpriority="high">' . "\n", esc_url( wg_hero_poster_url() ) );
	}
	echo '<meta name="theme-color" content="#0B0C0F">' . "\n";
}, 1 );

/**
 * Favicon padrão do tema quando o ícone do site não foi definido.
 */
add_action( 'wp_head', function () {
	if ( has_site_icon() ) {
		return;
	}
	echo '<link rel="icon" href="' . esc_url( WG_URI . '/assets/img/wg-favicon.svg' ) . '" type="image/svg+xml">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( WG_URI . '/assets/img/wg-avatar-magenta.png' ) . '">' . "\n";
}, 2 );

function wg_hero_poster_url() {
	$id = (int) wg_opt( 'hero_image' );
	if ( $id ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	return WG_URI . '/assets/video/hero-poster.webp';
}

/**
 * Remove scripts desnecessários para ganhar velocidade.
 */
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
} );

/**
 * Logo do cabeçalho: logo personalizado (se enviado) ou o SVG do tema.
 */
function wg_logo( $variant = 'branco' ) {
	if ( 'branco' === $variant && has_custom_logo() ) {
		$id = get_theme_mod( 'custom_logo' );
		return wp_get_attachment_image( $id, 'full', false, array( 'class' => 'wg-logo__img', 'alt' => wg_company_name() ) );
	}
	$file = 'escuro' === $variant ? 'wg-logo-horizontal-escuro.svg' : 'wg-logo-horizontal-branco.svg';
	return '<img class="wg-logo__img" src="' . esc_url( WG_URI . '/assets/img/' . $file ) . '" alt="' . esc_attr( wg_company_name() ) . '" width="300" height="67">';
}

/**
 * Menu padrão quando nenhum menu foi configurado no painel.
 */
function wg_fallback_menu() {
	$services = get_posts(
		array(
			'post_type'      => 'wg_servico',
			'posts_per_page' => 12,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	$items = array(
		array( __( 'Soluções', 'wg' ), get_post_type_archive_link( 'wg_servico' ), $services ),
		array( __( 'Eventos', 'wg' ), get_post_type_archive_link( 'wg_evento' ), array() ),
		array( __( 'Portfólio', 'wg' ), get_post_type_archive_link( 'wg_projeto' ), array() ),
		array( __( 'Sobre', 'wg' ), wg_page_url( 'sobre' ), array() ),
		array( __( 'Contato', 'wg' ), wg_page_url( 'contato' ), array() ),
	);
	echo '<ul class="wg-menu">';
	foreach ( $items as $item ) {
		$has = ! empty( $item[2] );
		echo '<li class="menu-item' . ( $has ? ' menu-item-has-children' : '' ) . '"><a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a>';
		if ( $has ) {
			echo '<ul class="sub-menu">';
			foreach ( $item[2] as $s ) {
				echo '<li class="menu-item"><a href="' . esc_url( get_permalink( $s ) ) . '">' . esc_html( get_the_title( $s ) ) . '</a></li>';
			}
			echo '</ul>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

function wg_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Adiciona botão de submenu acessível aos itens com filhos.
 */
add_filter( 'walker_nav_menu_start_el', function ( $output, $item, $depth, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		$output .= '<button class="wg-submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Abrir submenu', 'wg' ) . '">' . wg_icon( 'chevron' ) . '</button>';
	}
	return $output;
}, 10, 4 );

/**
 * Classe no body para páginas com cabeçalho sobre imagem.
 */
add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'wg';
	if ( is_front_page() || is_singular( array( 'wg_servico', 'wg_evento', 'wg_projeto' ) ) || is_post_type_archive() || is_tax() || is_page() || is_home() ) {
		$classes[] = 'wg-has-hero';
	}
	return $classes;
} );

/**
 * Resumo mais curto nos cards.
 */
add_filter( 'excerpt_length', function () {
	return 26;
} );
add_filter( 'excerpt_more', function () {
	return '…';
} );
