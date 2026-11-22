<?php
/**
 * "WG › Conteúdo inicial": cria páginas, soluções, tipos de evento, segmentos e menus.
 * Nada é sobrescrito: itens com o mesmo endereço (slug) são mantidos como estão.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_submenu_page( 'wg-settings', __( 'Conteúdo inicial', 'wg' ), __( 'Conteúdo inicial', 'wg' ), 'manage_options', 'wg-import', 'wg_render_import_page' );
}, 20 );

function wg_render_import_page() {
	echo '<div class="wrap"><h1>' . esc_html__( 'Conteúdo inicial', 'wg' ) . '</h1>';
	if ( isset( $_GET['done'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$log = get_transient( 'wg_import_log' );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Conteúdo criado.', 'wg' ) . '</p>';
		if ( $log ) {
			echo '<ul style="list-style:disc;margin-left:20px">';
			foreach ( (array) $log as $line ) {
				echo '<li>' . esc_html( $line ) . '</li>';
			}
			echo '</ul>';
		}
		echo '</div>';
	}
	echo '<p>' . esc_html__( 'Cria as páginas (Início, Sobre, Orçamento, Contato e Política de privacidade), as 9 soluções, os 12 tipos de evento, os segmentos do portfólio e os menus. Também define a página inicial e os links permanentes.', 'wg' ) . '</p>';
	echo '<p>' . esc_html__( 'Itens que já existem com o mesmo endereço não são alterados. Você pode rodar de novo com segurança.', 'wg' ) . '</p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'wg_import' );
	echo '<input type="hidden" name="action" value="wg_import">';
	echo '<p><label><input type="checkbox" name="front" value="1" checked> ' . esc_html__( 'Definir a nova página inicial do site', 'wg' ) . '</label></p>';
	echo '<p><label><input type="checkbox" name="menus" value="1" checked> ' . esc_html__( 'Criar e ativar os menus', 'wg' ) . '</label></p>';
	submit_button( __( 'Criar conteúdo inicial', 'wg' ) );
	echo '</form></div>';
}

add_action( 'admin_post_wg_import', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'wg_import' ) ) {
		wp_die( esc_html__( 'Sem permissão.', 'wg' ) );
	}
	$log = wg_run_import( ! empty( $_POST['front'] ), ! empty( $_POST['menus'] ) );
	set_transient( 'wg_import_log', $log, 300 );
	wp_safe_redirect( admin_url( 'admin.php?page=wg-import&done=1' ) );
	exit;
} );

/**
 * Pode ser chamado também via WP-CLI: wp eval "wg_run_import();"
 */
function wg_run_import( $set_front = true, $set_menus = true ) {
	$log = array();

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = 'Links permanentes definidos como /nome-da-pagina/.';
	}

	// Soluções.
	$service_ids = array();
	foreach ( wg_default_services() as $i => $s ) {
		$existing = get_page_by_path( $s['slug'], OBJECT, 'wg_servico' );
		if ( $existing ) {
			$service_ids[ $s['slug'] ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'wg_servico',
				'post_status'  => 'publish',
				'post_title'   => $s['title'],
				'post_name'    => $s['slug'],
				'post_excerpt' => $s['excerpt'],
				'post_content' => wg_to_blocks( $s['content'] ),
				'menu_order'   => $i + 1,
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wg_headline', $s['headline'] );
			update_post_meta( $id, '_wg_icon', $s['icon'] );
			update_post_meta( $id, '_wg_items', $s['items'] );
			update_post_meta( $id, '_wg_benefits', $s['benefits'] );
			update_post_meta( $id, '_wg_stock', $s['stock'] );
			update_post_meta( $id, '_wg_seo_title', $s['seo_title'] );
			update_post_meta( $id, '_wg_seo_desc', $s['seo_desc'] );
			$service_ids[ $s['slug'] ] = $id;
			$log[] = 'Solução criada: ' . $s['title'];
		}
	}

	// Tipos de evento.
	foreach ( wg_default_events() as $i => $e ) {
		if ( get_page_by_path( $e['slug'], OBJECT, 'wg_evento' ) ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'wg_evento',
				'post_status'  => 'publish',
				'post_title'   => $e['title'],
				'post_name'    => $e['slug'],
				'post_excerpt' => $e['excerpt'],
				'post_content' => wg_to_blocks( $e['content'] ),
				'menu_order'   => $i + 1,
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wg_stock', $e['stock'] );
			if ( $e['page'] ) {
				update_post_meta( $id, '_wg_has_page', '1' );
			}
			$rel = array();
			foreach ( $e['services'] as $slug ) {
				if ( isset( $service_ids[ $slug ] ) ) {
					$rel[] = $service_ids[ $slug ];
				}
			}
			if ( $rel ) {
				update_post_meta( $id, '_wg_services', $rel );
			}
			$log[] = 'Tipo de evento criado: ' . $e['title'];
		}
	}

	// Segmentos do portfólio.
	foreach ( wg_default_segments() as $slug => $name ) {
		if ( ! term_exists( $slug, 'wg_segmento' ) ) {
			wp_insert_term( $name, 'wg_segmento', array( 'slug' => $slug ) );
		}
	}

	// Projeto de exemplo (rascunho) para mostrar como preencher.
	if ( ! get_posts( array( 'post_type' => 'wg_projeto', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
		wp_insert_post(
			array(
				'post_type'    => 'wg_projeto',
				'post_status'  => 'draft',
				'post_title'   => 'Exemplo de projeto (edite ou exclua)',
				'post_excerpt' => 'Resumo curto do projeto, que aparece no card do portfólio.',
				'post_content' => wg_to_blocks( '<p>Descreva o desafio do cliente, a solução técnica entregue e o resultado. Use apenas fotos reais do evento e publique o nome do cliente somente com autorização.</p>' ),
			)
		);
		$log[] = 'Projeto de exemplo criado como rascunho em Portfólio.';
	}

	// Cases reais (criados como rascunho até a autorização do cliente).
	$log = array_merge( $log, wg_import_cases( $service_ids ) );

	// Páginas.
	$pages = array(
		'inicio'                  => array( 'Início', '', '' ),
		'sobre'                   => array( 'Sobre a empresa', wg_about_content(), 'templates/page-sobre.php' ),
		'orcamento'               => array( 'Solicitar orçamento', '', 'templates/page-orcamento.php' ),
		'contato'                 => array( 'Contato', '', 'templates/page-contato.php' ),
		'politica-de-privacidade' => array( 'Política de privacidade', wg_privacy_content(), '' ),
	);
	$page_ids = array();
	foreach ( $pages as $slug => $p ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$page_ids[ $slug ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $p[0],
				'post_name'    => $slug,
				'post_content' => wg_to_blocks( $p[1] ),
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			if ( $p[2] ) {
				update_post_meta( $id, '_wp_page_template', $p[2] );
			}
			$page_ids[ $slug ] = $id;
			$log[] = 'Página criada: ' . $p[0];
		}
	}
	if ( isset( $page_ids['politica-de-privacidade'] ) ) {
		update_option( 'wp_page_for_privacy_policy', $page_ids['politica-de-privacidade'] );
	}
	if ( $set_front && isset( $page_ids['inicio'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['inicio'] );
		$log[] = 'Página inicial definida.';
	}

	if ( $set_menus ) {
		$log = array_merge( $log, wg_build_menus( $service_ids, $page_ids ) );
	}

	update_option( 'blogdescription', 'Soluções audiovisuais para eventos corporativos' );
	if ( in_array( get_option( 'blogname' ), array( '', 'My WordPress Website', 'Meu site' ), true ) ) {
		update_option( 'blogname', wg_company_name() );
	}

	flush_rewrite_rules();
	return $log;
}

/**
 * Cases com material real da empresa (quadros extraídos dos vídeos do Instagram).
 * Ficam como rascunho, exceto os já autorizados ('status' => 'publish').
 */
function wg_default_cases() {
	return array(
		array(
			'slug'     => 'proinvest-40-anos',
			'title'    => 'Proinvest — 40 anos',
			'excerpt'  => 'Celebração dos 40 anos da Proinvest com projeção mapeada, palco, pista de LED, sonorização e iluminação cênica e arquitetural.',
			'content'  => '<p>Para comemorar os 40 anos da Proinvest, montamos a infraestrutura audiovisual completa do evento na Vila Pau Brasil.</p>'
				. '<h2>A solução</h2>'
				. '<p>A projeção mapeada levou a identidade dos 40 anos para a parede principal, atrás do palco da banda. A pista Paris, com piso iluminado, ficou integrada à área de palco, e a iluminação arquitetural valorizou o paisagismo e o salão de jantar.</p>'
				. '<p>A estrutura contemplou sonorização, iluminação de pista e arquitetural, pista Paris, palco, projeção mapeada e DJ.</p>',
			'status'   => 'publish',
			'segment'  => 'corporativo',
			'client'   => 'Proinvest',
			'location' => 'Vila Pau Brasil',
			'date'     => 'Novembro de 2026',
			'services' => array( 'sonorizacao-para-eventos', 'iluminacao-para-eventos', 'projecao-e-telas', 'palcos-e-estruturas', 'cenografia-audiovisual' ),
			'cover'    => 'proinvest-palco.jpg',
			'gallery'  => array( 'proinvest-palco.jpg', 'proinvest-projecao.jpg', 'proinvest-salao.jpg', 'proinvest-ambiente.jpg' ),
		),
		array(
			'slug'     => 'mariotta-evento-corporativo-slaviero',
			'title'    => 'Mariotta — evento corporativo no Slaviero',
			'excerpt'  => 'Evento corporativo da Mariotta no Hotel Slaviero, com painel de LED, palco, sonorização e iluminação cênica, em parceria com a Chaves Produções.',
			'content'  => '<p>Para o evento corporativo da Mariotta no Hotel Slaviero, realizado em parceria com a Chaves Produções, entregamos a infraestrutura audiovisual do salão.</p>'
				. '<h2>A solução</h2>'
				. '<p>Um painel de LED de grande formato no palco exibiu a identidade da marca e as apresentações da programação. O palco recebeu acabamento em LED nas bordas, e a iluminação cênica ambientou o salão nas cores do evento.</p>'
				. '<p>A estrutura contemplou painel de LED, palco, sonorização e iluminação cênica.</p>',
			'status'   => 'publish',
			'segment'  => 'corporativo',
			'client'   => 'Mariotta',
			'location' => 'Hotel Slaviero',
			'date'     => 'Novembro de 2026',
			'services' => array( 'painel-de-led-para-eventos', 'palcos-e-estruturas', 'sonorizacao-para-eventos', 'iluminacao-para-eventos' ),
			'cover'    => 'mariotta-apresentacao.jpg',
			'gallery'  => array( 'mariotta-apresentacao.jpg', 'mariotta-sala.jpg', 'mariotta-led-logo.jpg', 'mariotta-passarela.jpg' ),
		),
		array(
			'slug'     => 'e-copa-sao-paulo-2026-final-pacaembu',
			'title'    => 'E-COPA São Paulo 2026 — Grande Final no Pacaembu',
			'excerpt'  => 'Estrutura para a grande final da E-COPA São Paulo 2026, na Mercado Livre Arena Pacaembu, dentro da Virada Esportiva.',
			'content'  => '<p>A grande final da E-COPA São Paulo 2026 aconteceu na Mercado Livre Arena Pacaembu, dentro da programação da Virada Esportiva, com entrada gratuita e transmissão ao vivo no YouTube. O campeonato foi um projeto da Prefeitura de São Paulo, por meio da Secretaria Municipal de Esportes e Lazer, em parceria com a APREL.</p>'
				. '<h2>A solução</h2>'
				. '<p>Contratados pela produtora responsável pelo evento, atuamos como fornecedor técnico na montagem da arena: o palco da grande final com backdrop, os pórticos em box truss sobre as baterias de simuladores e a ilha técnica para a operação do evento.</p>',
			'status'   => 'publish',
			'segment'  => 'Institucional',
			'client'   => '',
			'location' => 'Mercado Livre Arena Pacaembu, São Paulo',
			'date'     => 'Setembro de 2026',
			'services' => array( 'palcos-e-estruturas' ),
			'cover'    => 'arena-visao-geral.jpg',
			'gallery'  => array( 'arena-visao-geral.jpg', 'arena-palco.jpg', 'arena-estruturas.jpg', 'arena-ilha-tecnica.jpg', 'arena-visao-geral-2.jpg' ),
		),
	);
}

function wg_import_cases( $service_ids ) {
	$log = array();
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	foreach ( wg_default_cases() as $c ) {
		if ( get_page_by_path( $c['slug'], OBJECT, 'wg_projeto' ) ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'wg_projeto',
				'post_status'  => isset( $c['status'] ) ? $c['status'] : 'draft',
				'post_title'   => $c['title'],
				'post_name'    => $c['slug'],
				'post_excerpt' => $c['excerpt'],
				'post_content' => wg_to_blocks( $c['content'] ),
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_wg_client', $c['client'] );
		update_post_meta( $id, '_wg_location', $c['location'] );
		update_post_meta( $id, '_wg_date', $c['date'] );
		update_post_meta( $id, '_wg_featured', '1' );
		$rel = array();
		foreach ( $c['services'] as $slug ) {
			if ( isset( $service_ids[ $slug ] ) ) {
				$rel[] = (int) $service_ids[ $slug ];
			}
		}
		if ( $rel ) {
			update_post_meta( $id, '_wg_services', $rel );
		}
		wp_set_object_terms( $id, $c['segment'], 'wg_segmento' );

		$ids = array();
		foreach ( $c['gallery'] as $file ) {
			$att = wg_sideload_theme_image( 'assets/img/cases/' . $file, $id, $c['title'] );
			if ( $att ) {
				$ids[ $file ] = $att;
			}
		}
		if ( isset( $ids[ $c['cover'] ] ) ) {
			set_post_thumbnail( $id, $ids[ $c['cover'] ] );
		}
		if ( $ids ) {
			update_post_meta( $id, '_wg_gallery', implode( ',', $ids ) );
		}
		$log[] = ( isset( $c['status'] ) && 'publish' === $c['status'] ? 'Case publicado: ' : 'Case criado como rascunho (publique após a autorização do cliente): ' ) . $c['title'];
	}
	return $log;
}

/**
 * Copia uma imagem do tema para a Biblioteca de mídia.
 */
function wg_sideload_theme_image( $rel_path, $post_id, $alt = '' ) {
	$src = WG_DIR . '/' . $rel_path;
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	$tmp = wp_tempnam( basename( $src ) );
	if ( ! $tmp || ! copy( $src, $tmp ) ) {
		return 0;
	}
	$att = media_handle_sideload(
		array(
			'name'     => basename( $src ),
			'tmp_name' => $tmp,
		),
		$post_id,
		$alt
	);
	if ( is_wp_error( $att ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return 0;
	}
	if ( $alt ) {
		update_post_meta( $att, '_wp_attachment_image_alt', $alt );
	}
	return (int) $att;
}

function wg_build_menus( $service_ids, $page_ids ) {
	$log       = array();
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( empty( $locations['primary'] ) ) {
		$menu_id = wp_create_nav_menu( 'Menu principal' );
		if ( ! is_wp_error( $menu_id ) ) {
			$parent = wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Soluções', 'menu-item-url' => get_post_type_archive_link( 'wg_servico' ), 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ) );
			foreach ( $service_ids as $sid ) {
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-object-id' => $sid, 'menu-item-object' => 'wg_servico', 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $parent, 'menu-item-status' => 'publish' ) );
			}
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Eventos', 'menu-item-url' => get_post_type_archive_link( 'wg_evento' ), 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ) );
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Portfólio', 'menu-item-url' => get_post_type_archive_link( 'wg_projeto' ), 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ) );
			foreach ( array( 'sobre' => 'Sobre', 'contato' => 'Contato' ) as $slug => $title ) {
				if ( isset( $page_ids[ $slug ] ) ) {
					wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $title, 'menu-item-object-id' => $page_ids[ $slug ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
				}
			}
			$locations['primary'] = $menu_id;
			$log[] = 'Menu principal criado.';
		}
	}

	if ( empty( $locations['footer'] ) ) {
		$menu_id = wp_create_nav_menu( 'Menu do rodapé' );
		if ( ! is_wp_error( $menu_id ) ) {
			foreach ( array( 'sobre' => 'Sobre a empresa', 'orcamento' => 'Solicitar orçamento', 'contato' => 'Contato', 'politica-de-privacidade' => 'Política de privacidade' ) as $slug => $title ) {
				if ( isset( $page_ids[ $slug ] ) ) {
					wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $title, 'menu-item-object-id' => $page_ids[ $slug ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
				}
			}
			$locations['footer'] = $menu_id;
			$log[] = 'Menu do rodapé criado.';
		}
	}

	set_theme_mod( 'nav_menu_locations', $locations );
	return $log;
}

/**
 * Converte HTML simples em blocos do editor (parágrafos e títulos).
 */
function wg_to_blocks( $html ) {
	if ( '' === trim( $html ) ) {
		return '';
	}
	$out = '';
	if ( preg_match_all( '#<(h2|h3|p|ul)>(.*?)</\1>#s', $html, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $b ) {
			if ( 'p' === $b[1] ) {
				$out .= "<!-- wp:paragraph -->\n<p>{$b[2]}</p>\n<!-- /wp:paragraph -->\n\n";
			} elseif ( 'ul' === $b[1] ) {
				$out .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$b[2]}</ul>\n<!-- /wp:list -->\n\n";
			} else {
				$lvl  = 'h3' === $b[1] ? ' {"level":3}' : '';
				$out .= "<!-- wp:heading{$lvl} -->\n<{$b[1]} class=\"wp-block-heading\">{$b[2]}</{$b[1]}>\n<!-- /wp:heading -->\n\n";
			}
		}
	}
	return $out;
}

function wg_about_content() {
	return '<h2>Infraestrutura audiovisual com responsabilidade técnica</h2>'
		. '<p>Somos uma empresa de soluções audiovisuais para eventos corporativos. Participamos de todas as etapas da infraestrutura técnica: planejamento, consultoria, dimensionamento dos equipamentos, projeto audiovisual, logística, montagem, configuração, testes, operação e desmontagem.</p>'
		. '<p>Nosso trabalho é fazer com que a tecnologia funcione a favor da mensagem do evento, para que organizadores, agências e departamentos de marketing e RH possam se concentrar no que importa: o público.</p>'
		. '<h2>Como trabalhamos</h2>'
		. '<p>Cada projeto começa com um briefing detalhado. Entendemos o objetivo, o espaço, o público e a programação, e só então indicamos a estrutura necessária. Durante o evento, nossa equipe técnica acompanha a operação do início ao fim.</p>'
		. '<h2>Nossa história</h2>'
		. '<p>[DEFINIR: conte aqui a trajetória da empresa, o ano de fundação e os principais marcos. Use apenas informações reais.]</p>';
}

function wg_privacy_content() {
	return '<p>Esta política explica como coletamos, usamos e protegemos os dados pessoais enviados por meio deste site, em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018).</p>'
		. '<h2>Quem é o responsável pelos dados</h2>'
		. '<p>[DEFINIR: razão social, CNPJ, endereço e e-mail de contato do responsável pelo tratamento de dados.]</p>'
		. '<h2>Quais dados coletamos</h2>'
		. '<p>Ao solicitar um orçamento ou entrar em contato, coletamos nome, empresa, cargo, e-mail, WhatsApp, informações sobre o evento e arquivos que você decidir anexar. Também podemos coletar dados de navegação por meio de cookies de medição, apenas se você autorizar.</p>'
		. '<h2>Para que usamos os dados</h2>'
		. '<p>Usamos os dados para responder à sua solicitação, elaborar propostas comerciais, manter contato sobre o seu evento e melhorar o site. Não vendemos dados pessoais.</p>'
		. '<h2>Base legal</h2>'
		. '<p>O tratamento é feito com base no seu consentimento e na execução de procedimentos preliminares relacionados a um contrato solicitado por você.</p>'
		. '<h2>Compartilhamento</h2>'
		. '<p>Os dados podem ser armazenados em serviços de hospedagem, e-mail e gestão de relacionamento com clientes contratados por nós, sempre com medidas de segurança adequadas.</p>'
		. '<h2>Cookies</h2>'
		. '<p>Cookies de medição, como os do Google Analytics, só são ativados se você clicar em “Aceitar” no aviso de cookies. Você pode recusar sem prejuízo ao uso do site.</p>'
		. '<h2>Por quanto tempo guardamos os dados</h2>'
		. '<p>Mantemos os dados pelo tempo necessário para atender à sua solicitação e cumprir obrigações legais. Depois disso, eles são excluídos ou anonimizados.</p>'
		. '<h2>Seus direitos</h2>'
		. '<p>Você pode solicitar a confirmação, o acesso, a correção, a portabilidade ou a exclusão dos seus dados, e revogar o consentimento a qualquer momento, pelo e-mail [DEFINIR].</p>'
		. '<h2>Atualizações</h2>'
		. '<p>Esta política pode ser atualizada. A versão vigente estará sempre disponível nesta página.</p>';
}

/**
 * Lembrete para rodar o importador após ativar o tema.
 */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) || get_posts( array( 'post_type' => 'wg_servico', 'posts_per_page' => 1, 'fields' => 'ids', 'post_status' => 'any' ) ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p><strong>WG Audiovisual:</strong> ' . esc_html__( 'Crie as páginas, soluções e menus do site com um clique.', 'wg' ) . ' <a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=wg-import' ) ) . '">' . esc_html__( 'Criar conteúdo inicial', 'wg' ) . '</a></p></div>';
} );
