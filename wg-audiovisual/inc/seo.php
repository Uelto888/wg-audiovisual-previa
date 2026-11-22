<?php
/**
 * SEO básico do tema: títulos, descrições, Open Graph, dados estruturados e trilha de navegação.
 * Com Rank Math ou Yoast ativos, o tema deixa títulos, descrições e Open Graph por conta do plugin.
 */

defined( 'ABSPATH' ) || exit;

function wg_seo_plugin_active() {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Título e descrição da página atual.
 */
function wg_seo_data() {
	$title = '';
	$desc  = '';
	$image = '';

	if ( is_front_page() ) {
		$title = wg_company_name() . ' | ' . __( 'Audiovisual para eventos corporativos', 'wg' );
		$desc  = wg_opt( 'hero_text' );
	} elseif ( is_singular() ) {
		$id    = get_queried_object_id();
		$title = get_post_meta( $id, '_wg_seo_title', true );
		$desc  = get_post_meta( $id, '_wg_seo_desc', true );
		if ( ! $desc ) {
			$desc = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $id ) ), 28, '…' );
		}
		if ( has_post_thumbnail( $id ) ) {
			$image = get_the_post_thumbnail_url( $id, 'wg-wide' );
		} elseif ( get_post_meta( $id, '_wg_stock', true ) ) {
			$image = wg_stock( get_post_meta( $id, '_wg_stock', true ) );
		}
	} elseif ( is_post_type_archive( 'wg_servico' ) ) {
		$title = __( 'Soluções audiovisuais para eventos corporativos', 'wg' );
		$desc  = __( 'Sonorização, iluminação, painéis de LED, projeção, palcos, streaming, cenografia e produção audiovisual para eventos corporativos.', 'wg' );
	} elseif ( is_post_type_archive( 'wg_evento' ) ) {
		$title = __( 'Estrutura audiovisual por tipo de evento', 'wg' );
		$desc  = __( 'Soluções audiovisuais para convenções, congressos, feiras, lançamentos, premiações, treinamentos e eventos híbridos.', 'wg' );
	} elseif ( is_post_type_archive( 'wg_projeto' ) || is_tax( 'wg_segmento' ) ) {
		$title = is_tax() ? single_term_title( __( 'Portfólio: ', 'wg' ), false ) : __( 'Portfólio de eventos', 'wg' );
		$desc  = __( 'Conheça projetos audiovisuais realizados em eventos corporativos, congressos, feiras e lançamentos.', 'wg' );
	}

	if ( ! $image ) {
		$og    = (int) wg_opt( 'og_image' );
		$image = $og ? wp_get_attachment_image_url( $og, 'full' ) : wg_stock( 'hero-plateia' );
	}

	return array(
		'title' => $title,
		'desc'  => wp_strip_all_tags( (string) $desc ),
		'image' => $image,
	);
}

add_filter( 'pre_get_document_title', function ( $title ) {
	if ( wg_seo_plugin_active() ) {
		return $title;
	}
	$data = wg_seo_data();
	if ( ! $data['title'] ) {
		return $title;
	}
	if ( is_front_page() ) {
		return $data['title'];
	}
	return $data['title'] . ' | ' . wg_company_name();
} );

add_action( 'wp_head', function () {
	if ( wg_opt( 'gsc_verify' ) ) {
		echo '<meta name="google-site-verification" content="' . esc_attr( wg_opt( 'gsc_verify' ) ) . '">' . "\n";
	}
	if ( wg_seo_plugin_active() ) {
		return;
	}
	$d   = wg_seo_data();
	$url = wg_current_url();
	if ( $d['desc'] ) {
		echo '<meta name="description" content="' . esc_attr( wp_html_excerpt( $d['desc'], 160, '…' ) ) . '">' . "\n";
	}
	echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:locale" content="pt_BR">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( wg_company_name() ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
	if ( $d['desc'] ) {
		echo '<meta property="og:description" content="' . esc_attr( wp_html_excerpt( $d['desc'], 200, '…' ) ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $d['image'] ) {
		echo '<meta property="og:image" content="' . esc_url( $d['image'] ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}, 5 );

/**
 * Remove o canonical padrão do WP para não duplicar.
 */
add_action( 'init', function () {
	if ( ! wg_seo_plugin_active() ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
} );

function wg_current_url() {
	if ( is_singular() ) {
		return get_permalink( get_queried_object_id() );
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_post_type_archive() ) {
		return get_post_type_archive_link( get_query_var( 'post_type' ) );
	}
	if ( is_tax() || is_category() || is_tag() ) {
		return get_term_link( get_queried_object() );
	}
	global $wp;
	return home_url( trailingslashit( $wp->request ) );
}

/**
 * Sem indexação para páginas internas sem valor de busca.
 */
add_filter( 'wp_robots', function ( $robots ) {
	if ( is_search() || is_404() || isset( $_GET['enviado'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$robots['noindex'] = true;
	}
	return $robots;
} );

/* -------------------------------------------------------------------------
 * Dados estruturados (JSON-LD)
 * ---------------------------------------------------------------------- */

function wg_org_schema() {
	$org = array(
		'@type' => 'LocalBusiness',
		'@id'   => home_url( '/#empresa' ),
		'name'  => wg_company_name(),
		'url'   => home_url( '/' ),
		'logo'  => WG_URI . '/assets/img/wg-avatar-magenta.png',
		'image' => wg_stock( 'hero-plateia' ),
		'description' => wg_opt( 'value_text' ),
	);
	if ( wg_whatsapp_number() ) {
		$org['telephone'] = '+' . wg_whatsapp_number();
	}
	if ( wg_opt( 'email' ) ) {
		$org['email'] = wg_opt( 'email' );
	}
	if ( wg_opt( 'address' ) ) {
		$org['address'] = array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => wg_opt( 'address' ),
			'addressCountry' => 'BR',
		);
	}
	if ( wg_opt( 'area' ) ) {
		$org['areaServed'] = wg_opt( 'area' );
	}
	$same = array_values( array_filter( array( wg_opt( 'instagram' ), wg_opt( 'linkedin' ), wg_opt( 'youtube' ) ) ) );
	if ( $same ) {
		$org['sameAs'] = $same;
	}
	return $org;
}

add_action( 'wp_footer', function () {
	$graph = array();

	// Com Yoast/Rank Math, empresa, site e trilha de navegação ficam por conta do plugin.
	$plugin = wg_seo_plugin_active();

	if ( is_front_page() ) {
		if ( ! $plugin ) {
			$graph[] = wg_org_schema();
			$graph[] = array(
			'@type'     => 'WebSite',
			'@id'       => home_url( '/#site' ),
			'url'       => home_url( '/' ),
			'name'      => wg_company_name(),
			'publisher' => array( '@id' => home_url( '/#empresa' ) ),
			'inLanguage'=> 'pt-BR',
			);
		}
		$graph[] = wg_faq_schema( wg_faq() );
	}

	if ( is_singular( 'wg_servico' ) ) {
		$id      = get_queried_object_id();
		$graph[] = array(
			'@type'       => 'Service',
			'name'        => get_the_title( $id ),
			'serviceType' => get_post_meta( $id, '_wg_seo_title', true ) ? get_post_meta( $id, '_wg_seo_title', true ) : get_the_title( $id ),
			'description' => get_the_excerpt( $id ),
			'url'         => get_permalink( $id ),
			'provider'    => wg_org_schema(),
			'areaServed'  => wg_opt( 'area' ) ? wg_opt( 'area' ) : 'BR',
		);
	}

	$crumbs = $plugin ? array() : wg_breadcrumb_items();
	if ( count( $crumbs ) > 1 ) {
		$list = array();
		foreach ( $crumbs as $i => $c ) {
			$list[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $c[0],
				'item'     => $c[1],
			);
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list,
		);
	}

	if ( ! $graph ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 20 );

function wg_faq_schema( $faq ) {
	$items = array();
	foreach ( $faq as $q ) {
		$items[] = array(
			'@type'          => 'Question',
			'name'           => $q[0],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $q[1],
			),
		);
	}
	return array(
		'@type'      => 'FAQPage',
		'mainEntity' => $items,
	);
}

/* -------------------------------------------------------------------------
 * Trilha de navegação
 * ---------------------------------------------------------------------- */

function wg_breadcrumb_items() {
	if ( is_front_page() ) {
		return array();
	}
	$items = array( array( __( 'Início', 'wg' ), home_url( '/' ) ) );
	$map   = array(
		'wg_servico' => __( 'Soluções', 'wg' ),
		'wg_evento'  => __( 'Eventos', 'wg' ),
		'wg_projeto' => __( 'Portfólio', 'wg' ),
	);
	if ( is_singular( array_keys( $map ) ) ) {
		$type    = get_post_type();
		$items[] = array( $map[ $type ], get_post_type_archive_link( $type ) );
		$items[] = array( get_the_title(), get_permalink() );
	} elseif ( is_post_type_archive( array_keys( $map ) ) ) {
		$type    = get_query_var( 'post_type' );
		$type    = is_array( $type ) ? reset( $type ) : $type;
		$items[] = array( $map[ $type ], get_post_type_archive_link( $type ) );
	} elseif ( is_tax( 'wg_segmento' ) ) {
		$items[] = array( $map['wg_projeto'], get_post_type_archive_link( 'wg_projeto' ) );
		$items[] = array( single_term_title( '', false ), get_term_link( get_queried_object() ) );
	} elseif ( is_page() ) {
		$items[] = array( get_the_title(), get_permalink() );
	} elseif ( is_singular() ) {
		$items[] = array( get_the_title(), get_permalink() );
	}
	return $items;
}

function wg_breadcrumbs() {
	$items = wg_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}
	echo '<nav class="wg-breadcrumbs" aria-label="' . esc_attr__( 'Você está em', 'wg' ) . '"><ol>';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $it ) {
		echo '<li>';
		if ( $i === $last ) {
			echo '<span aria-current="page">' . esc_html( $it[0] ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $it[1] ) . '">' . esc_html( $it[0] ) . '</a>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * Yoast SEO: tira do sitemap os tipos de evento sem página própria e os conteúdos internos.
 */
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', function ( $ids ) {
	$hidden = get_posts(
		array(
			'post_type'      => 'wg_evento',
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_wg_has_page',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);
	return array_merge( (array) $ids, $hidden );
} );
add_filter( 'wpseo_sitemap_exclude_post_type', function ( $excluded, $post_type ) {
	return in_array( $post_type, array( 'wg_lead', 'wg_depoimento', 'wg_cliente' ), true ) ? true : $excluded;
}, 10, 2 );
add_filter( 'wpseo_sitemap_exclude_author', '__return_true' );
