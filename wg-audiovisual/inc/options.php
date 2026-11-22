<?php
/**
 * Painel "WG › Configurações": dados da empresa, página inicial, orçamentos e SEO.
 */

defined( 'ABSPATH' ) || exit;

function wg_option_sections() {
	return array(
		'empresa' => array(
			'title'  => __( 'Empresa e contato', 'wg' ),
			'fields' => array(
				'company_name'     => array( 'label' => 'Nome da empresa', 'type' => 'text' ),
				'whatsapp'         => array( 'label' => 'WhatsApp (com DDD, ex.: 11 99999-9999)', 'type' => 'text' ),
				'whatsapp_message' => array( 'label' => 'Mensagem padrão do WhatsApp', 'type' => 'textarea', 'rows' => 2 ),
				'email'            => array( 'label' => 'E-mail comercial', 'type' => 'text' ),
				'phone'            => array( 'label' => 'Telefone fixo (opcional)', 'type' => 'text' ),
				'address'          => array( 'label' => 'Endereço', 'type' => 'textarea', 'rows' => 2 ),
				'area'             => array( 'label' => 'Área de atendimento (ex.: São Paulo e região, com atendimento em todo o Brasil)', 'type' => 'text' ),
				'hours'            => array( 'label' => 'Horário de atendimento (opcional)', 'type' => 'text' ),
				'cnpj'             => array( 'label' => 'CNPJ (opcional, aparece no rodapé)', 'type' => 'text' ),
				'instagram'        => array( 'label' => 'Instagram (URL completa)', 'type' => 'url' ),
				'linkedin'         => array( 'label' => 'LinkedIn (URL completa)', 'type' => 'url' ),
				'youtube'          => array( 'label' => 'YouTube (URL completa)', 'type' => 'url' ),
			),
		),
		'home'    => array(
			'title'  => __( 'Página inicial', 'wg' ),
			'fields' => array(
				'hero_eyebrow'  => array( 'label' => 'Topo: sobretítulo', 'type' => 'text' ),
				'hero_title'    => array( 'label' => 'Topo: título principal', 'type' => 'textarea', 'rows' => 2 ),
				'hero_text'     => array( 'label' => 'Topo: texto de apoio', 'type' => 'textarea', 'rows' => 2 ),
				'hero_video'    => array( 'label' => 'Exibir vídeo de fundo no topo (em celulares aparece apenas a imagem)', 'type' => 'checkbox' ),
				'hero_video_url'=> array( 'label' => 'Vídeo próprio (URL de um MP4 enviado para a Biblioteca de mídia; deixe em branco para usar o vídeo padrão)', 'type' => 'url' ),
				'hero_image'    => array( 'label' => 'Imagem do topo (substitui a imagem padrão)', 'type' => 'image' ),
				'value_title'   => array( 'label' => 'Proposta de valor: título', 'type' => 'textarea', 'rows' => 2 ),
				'value_text'    => array( 'label' => 'Proposta de valor: texto', 'type' => 'textarea', 'rows' => 2 ),
				'stats'         => array( 'label' => 'Indicadores (um por linha: Rótulo | Número). A seção só aparece quando houver números preenchidos.', 'type' => 'textarea', 'rows' => 5, 'help' => 'Exemplo: Anos de experiência | 12 — use apenas números reais.' ),
				'differentials' => array( 'label' => 'Diferenciais (um por linha: ícone | Título | Descrição)', 'type' => 'textarea', 'rows' => 9, 'help' => 'Ícones disponíveis: ' . implode( ', ', array_keys( wg_icon_choices() ) ) . '. Mantenha apenas diferenciais confirmados pela empresa.' ),
				'faq'           => array( 'label' => 'Perguntas frequentes (uma por linha: Pergunta | Resposta)', 'type' => 'textarea', 'rows' => 8 ),
				'cta_title'     => array( 'label' => 'Chamada final: título', 'type' => 'text' ),
				'cta_text'      => array( 'label' => 'Chamada final: texto', 'type' => 'textarea', 'rows' => 2 ),
			),
		),
		'leads'   => array(
			'title'  => __( 'Orçamentos, CRM e proteção contra spam', 'wg' ),
			'fields' => array(
				'lead_email'       => array( 'label' => 'E-mails que recebem os pedidos (separe por vírgula). Em branco: e-mail do administrador.', 'type' => 'text' ),
				'webhook_url'      => array( 'label' => 'Webhook para CRM (URL que recebe cada pedido em JSON — RD Station, HubSpot, Pipedrive via Zapier/Make/n8n)', 'type' => 'url' ),
				'turnstile_site'   => array( 'label' => 'Cloudflare Turnstile — Site key (opcional, recomendado)', 'type' => 'text' ),
				'turnstile_secret' => array( 'label' => 'Cloudflare Turnstile — Secret key', 'type' => 'text' ),
			),
		),
		'seo'     => array(
			'title'  => __( 'SEO, análise e redirecionamentos', 'wg' ),
			'fields' => array(
				'ga4_id'      => array( 'label' => 'Google Analytics 4 — ID da métrica (ex.: G-XXXXXXX). Só carrega após o consentimento de cookies.', 'type' => 'text' ),
				'gads_id'     => array( 'label' => 'Google Ads — ID de conversão (opcional, ex.: AW-XXXXXXX)', 'type' => 'text' ),
				'gads_label'  => array( 'label' => 'Google Ads — rótulo da conversão de orçamento (opcional)', 'type' => 'text' ),
				'gsc_verify'  => array( 'label' => 'Google Search Console — código de verificação (só o conteúdo da meta tag)', 'type' => 'text' ),
				'og_image'    => array( 'label' => 'Imagem de compartilhamento padrão (redes sociais e WhatsApp)', 'type' => 'image' ),
				'redirects'   => array( 'label' => 'Redirecionamentos 301 (um por linha: /endereco-antigo/ | /endereco-novo/)', 'type' => 'textarea', 'rows' => 5, 'help' => 'Use para levar endereços do site antigo para as novas páginas sem perder posição no Google.' ),
			),
		),
	);
}

add_action( 'admin_menu', function () {
	add_menu_page( __( 'WG', 'wg' ), __( 'WG', 'wg' ), 'edit_theme_options', 'wg-settings', 'wg_render_options_page', 'dashicons-admin-generic', 2 );
	add_submenu_page( 'wg-settings', __( 'Configurações', 'wg' ), __( 'Configurações', 'wg' ), 'edit_theme_options', 'wg-settings', 'wg_render_options_page' );
} );

add_action( 'admin_init', function () {
	register_setting(
		'wg_options_group',
		'wg_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'wg_sanitize_options',
		)
	);
} );

function wg_sanitize_options( $input ) {
	$out = array();
	foreach ( wg_option_sections() as $section ) {
		foreach ( $section['fields'] as $key => $f ) {
			$raw = isset( $input[ $key ] ) ? $input[ $key ] : '';
			switch ( $f['type'] ) {
				case 'checkbox':
					$out[ $key ] = $raw ? '1' : '0';
					break;
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( $raw );
					break;
				case 'url':
					$out[ $key ] = esc_url_raw( $raw );
					break;
				case 'image':
					$out[ $key ] = (string) absint( $raw );
					break;
				default:
					$out[ $key ] = sanitize_text_field( $raw );
			}
		}
	}
	return $out;
}

function wg_render_options_page() {
	$opts     = get_option( 'wg_options', array() );
	$defaults = wg_option_defaults();
	$tab      = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'empresa'; // phpcs:ignore WordPress.Security.NonceVerification
	$sections = wg_option_sections();
	if ( ! isset( $sections[ $tab ] ) ) {
		$tab = 'empresa';
	}
	echo '<div class="wrap wg-options"><h1>' . esc_html__( 'Configurações do site', 'wg' ) . '</h1>';
	echo '<nav class="nav-tab-wrapper">';
	foreach ( $sections as $key => $s ) {
		echo '<a class="nav-tab' . ( $key === $tab ? ' nav-tab-active' : '' ) . '" href="' . esc_url( admin_url( 'admin.php?page=wg-settings&tab=' . $key ) ) . '">' . esc_html( $s['title'] ) . '</a>';
	}
	echo '</nav><form method="post" action="options.php">';
	settings_fields( 'wg_options_group' );

	// Campos das outras abas vão ocultos para não serem apagados ao salvar.
	foreach ( $sections as $key => $s ) {
		foreach ( $s['fields'] as $fk => $f ) {
			$val = isset( $opts[ $fk ] ) ? $opts[ $fk ] : ( isset( $defaults[ $fk ] ) ? $defaults[ $fk ] : '' );
			if ( 'hero_video' === $fk && ! isset( $opts[ $fk ] ) ) {
				$val = '1';
			}
			if ( $key === $tab ) {
				wg_render_field( $fk, $f, $val, 'wg_options[' . $fk . ']' );
			} elseif ( 'checkbox' === $f['type'] ) {
				if ( '1' === $val ) {
					echo '<input type="hidden" name="wg_options[' . esc_attr( $fk ) . ']" value="1">';
				}
			} else {
				echo '<input type="hidden" name="wg_options[' . esc_attr( $fk ) . ']" value="' . esc_attr( $val ) . '">';
			}
		}
	}
	submit_button( __( 'Salvar alterações', 'wg' ) );
	echo '</form></div>';
}

/**
 * Aviso para preencher dados pendentes.
 */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$missing = array();
	foreach ( array( 'whatsapp' => 'WhatsApp', 'email' => 'e-mail', 'address' => 'endereço', 'area' => 'área de atendimento', 'instagram' => 'Instagram' ) as $k => $l ) {
		if ( '' === (string) wg_opt( $k ) ) {
			$missing[] = $l;
		}
	}
	if ( $missing ) {
		echo '<div class="notice notice-warning"><p><strong>WG:</strong> ' . esc_html( sprintf( __( 'Dados ainda não definidos: %s.', 'wg' ), implode( ', ', $missing ) ) ) . ' <a href="' . esc_url( admin_url( 'admin.php?page=wg-settings' ) ) . '">' . esc_html__( 'Preencher agora', 'wg' ) . '</a></p></div>';
	}
} );

/**
 * Redirecionamentos 301 configurados no painel.
 */
add_action( 'template_redirect', function () {
	$rules = wg_opt( 'redirects' );
	if ( ! $rules || ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$path = untrailingslashit( strtok( wp_unslash( $_SERVER['REQUEST_URI'] ), '?' ) ); // phpcs:ignore
	foreach ( wg_pairs( $rules ) as $r ) {
		if ( '' === $r['text'] ) {
			continue;
		}
		$from = untrailingslashit( wp_parse_url( $r['title'], PHP_URL_PATH ) );
		if ( $from && strtolower( $from ) === strtolower( $path ) ) {
			$to = 0 === strpos( $r['text'], 'http' ) ? $r['text'] : home_url( $r['text'] );
			wp_redirect( $to, 301 ); // phpcs:ignore WordPress.Security.SafeRedirect
			exit;
		}
	}
}, 1 );
