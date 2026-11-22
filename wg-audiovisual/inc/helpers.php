<?php
/**
 * Funções utilitárias usadas em todo o tema.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lê uma opção do painel "WG › Configurações".
 */
function wg_opt( $key, $default = '' ) {
	static $opts = null;
	if ( null === $opts ) {
		$opts = get_option( 'wg_options', array() );
		if ( ! is_array( $opts ) ) {
			$opts = array();
		}
	}
	if ( isset( $opts[ $key ] ) && '' !== $opts[ $key ] ) {
		return $opts[ $key ];
	}
	$defaults = wg_option_defaults();
	return isset( $defaults[ $key ] ) && '' === $default ? $defaults[ $key ] : $default;
}

/**
 * Mostra "[DEFINIR]" apenas para administradores; visitantes não veem nada.
 */
function wg_pending( $label = 'DEFINIR' ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		return '<span class="wg-pending" title="' . esc_attr__( 'Preencha em WG › Configurações', 'wg' ) . '">[' . esc_html( $label ) . ']</span>';
	}
	return '';
}

function wg_company_name() {
	return wg_opt( 'company_name', 'WG Soluções Audiovisuais' );
}

/**
 * Número de WhatsApp só com dígitos (com DDI 55).
 */
function wg_whatsapp_number() {
	$n = preg_replace( '/\D+/', '', (string) wg_opt( 'whatsapp' ) );
	if ( '' === $n ) {
		return '';
	}
	if ( strlen( $n ) <= 11 ) {
		$n = '55' . $n;
	}
	return $n;
}

/**
 * Link do WhatsApp com mensagem contextualizada.
 * Sem número configurado, leva para a página de orçamento.
 */
function wg_whatsapp_url( $message = '' ) {
	if ( '' === $message ) {
		$message = wg_opt( 'whatsapp_message' );
	}
	$number = wg_whatsapp_number();
	if ( '' === $number ) {
		return wg_quote_url();
	}
	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
}

/**
 * Mensagem de WhatsApp para um serviço ou tipo de evento específico.
 */
function wg_whatsapp_message_for( $subject ) {
	return sprintf(
		/* translators: %s: serviço ou tipo de evento */
		__( 'Olá! Gostaria de solicitar um orçamento de %s para um evento corporativo.', 'wg' ),
		wp_strip_all_tags( $subject )
	);
}

/**
 * Mensagem do botão flutuante conforme a página visitada.
 */
function wg_float_message() {
	if ( is_singular( 'wg_servico' ) ) {
		return wg_whatsapp_message_for( mb_strtolower( get_the_title() ) );
	}
	if ( is_singular( 'wg_evento' ) ) {
		return sprintf( __( 'Olá! Estou organizando um evento do tipo %s e gostaria de um orçamento de infraestrutura audiovisual.', 'wg' ), mb_strtolower( get_the_title() ) );
	}
	if ( is_singular( 'wg_projeto' ) ) {
		return sprintf( __( 'Olá! Vi o projeto "%s" no site e gostaria de um orçamento para um evento parecido.', 'wg' ), get_the_title() );
	}
	return wg_opt( 'whatsapp_message' );
}

function wg_quote_url( $service = '' ) {
	$page = get_page_by_path( 'orcamento' );
	$url  = $page ? get_permalink( $page ) : home_url( '/orcamento/' );
	if ( $service ) {
		$url = add_query_arg( 'servico', rawurlencode( $service ), $url );
	}
	return $url;
}

/**
 * URL de uma imagem de banco incluída no tema.
 */
function wg_stock( $slug ) {
	return WG_URI . '/assets/img/stock/' . $slug . '.webp';
}

/**
 * Imagem destacada do post ou, na falta dela, a imagem padrão do tema.
 */
function wg_post_image( $post = null, $size = 'wg-card', $fallback = '', $attrs = array() ) {
	$post  = get_post( $post );
	$attrs = wp_parse_args(
		$attrs,
		array(
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => $post ? get_the_title( $post ) : '',
		)
	);
	if ( $post && has_post_thumbnail( $post ) ) {
		return get_the_post_thumbnail( $post, $size, $attrs );
	}
	if ( ! $fallback && $post ) {
		$fallback = get_post_meta( $post->ID, '_wg_stock', true );
	}
	if ( $fallback ) {
		$html = '<img src="' . esc_url( wg_stock( $fallback ) ) . '"';
		foreach ( $attrs as $k => $v ) {
			$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
		return $html . ' width="1600" height="1066">';
	}
	return '';
}

/**
 * Divide um texto em linhas não vazias.
 */
function wg_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Linhas no formato "Título | Descrição" viram pares.
 */
function wg_pairs( $text ) {
	$out = array();
	foreach ( wg_lines( $text ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$out[] = array(
			'title' => $parts[0],
			'text'  => isset( $parts[1] ) ? $parts[1] : '',
		);
	}
	return $out;
}

/**
 * Perguntas frequentes configuradas no painel.
 */
function wg_faq() {
	$out = array();
	foreach ( wg_pairs( wg_opt( 'faq' ) ) as $p ) {
		if ( $p['text'] ) {
			$out[] = array( $p['title'], $p['text'] );
		}
	}
	return $out;
}

/**
 * Diferenciais: "ícone | Título | Descrição".
 */
function wg_differentials() {
	$out = array();
	foreach ( wg_lines( wg_opt( 'differentials' ) ) as $line ) {
		$p = array_map( 'trim', explode( '|', $line ) );
		if ( count( $p ) >= 3 ) {
			$out[] = array( 'icon' => $p[0], 'title' => $p[1], 'text' => $p[2] );
		} elseif ( count( $p ) === 2 ) {
			$out[] = array( 'icon' => 'check', 'title' => $p[0], 'text' => $p[1] );
		}
	}
	return $out;
}

/**
 * Indicadores com número preenchido.
 */
function wg_stats() {
	$out = array();
	foreach ( wg_pairs( wg_opt( 'stats' ) ) as $p ) {
		if ( '' !== $p['text'] ) {
			$out[] = array( 'label' => $p['title'], 'value' => $p['text'] );
		}
	}
	return $out;
}

/**
 * Ícones SVG minimalistas (traço).
 */
function wg_icon( $name, $class = '' ) {
	$icons = array(
		'som'        => '<rect x="5" y="2" width="14" height="20" rx="2"/><circle cx="12" cy="14" r="4"/><circle cx="12" cy="6.5" r="1.5"/>',
		'luz'        => '<path d="M7 3h10l-2 7H9z"/><path d="M9 10 5 21M15 10l4 11M12 10v11"/>',
		'led'        => '<rect x="2" y="3" width="20" height="14" rx="1"/><path d="M2 7.7h20M2 12.3h20M7 3v14M12 3v14M17 3v14M8 21h8M12 17v4"/>',
		'projecao'   => '<rect x="2" y="7" width="20" height="11" rx="2"/><circle cx="8" cy="12.5" r="3"/><path d="M14 11h5M14 14h5M6 18v2M18 18v2"/>',
		'palco'      => '<path d="M3 21V4M21 21V4M3 4h18M3 8h18M3 4l4 4 4-4 4 4 4-4"/><path d="M6 21v-5h12v5"/>',
		'streaming'  => '<circle cx="12" cy="12" r="2"/><path d="M16.2 7.8a6 6 0 0 1 0 8.4M7.8 16.2a6 6 0 0 1 0-8.4M19 5a10 10 0 0 1 0 14M5 19A10 10 0 0 1 5 5"/>',
		'cenografia' => '<path d="M12 2 2 7l10 5 10-5z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
		'camera'     => '<rect x="2" y="6" width="14" height="12" rx="2"/><path d="m16 10 6-3v10l-6-3"/>',
		'energia'    => '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
		'check'      => '<path d="M20 6 9 17l-5-5"/>',
		'arrow'      => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'chevron'    => '<path d="m6 9 6 6 6-6"/>',
		'plus'       => '<path d="M12 5v14M5 12h14"/>',
		'menu'       => '<path d="M3 7h18M3 12h18M3 17h18"/>',
		'close'      => '<path d="M6 6l12 12M18 6 6 18"/>',
		'mail'       => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/>',
		'phone'      => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
		'pin'        => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
		'instagram'  => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6"/>',
		'linkedin'   => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>',
		'youtube'    => '<path d="M2.5 17a24 24 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49 49 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24 24 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49 49 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/>',
		'users'      => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
		'clipboard'  => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2M9 12h6M9 16h6"/>',
		'clock'      => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'wrench'     => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9z"/>',
		'headset'    => '<path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/>',
		'target'     => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
		'shield'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
		'calendar'   => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'chat'       => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
		'upload'     => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>',
		'play'       => '<path d="m7 4 13 8-13 8z"/>',
		'truck'      => '<path d="M1 4h14v12H1zM15 8h4l3 3v5h-7"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
		'sliders'    => '<path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/>',
		'box'        => '<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
		'search'     => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
		'file'       => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
		'quote'      => '<path d="M3 21c3 0 7-1 7-8V5H3v7h4c0 4-2 5-4 5zM14 21c3 0 7-1 7-8V5h-7v7h4c0 4-2 5-4 5z"/>',
	);

	$cls = trim( 'wg-icon ' . $class );

	if ( 'whatsapp' === $name ) {
		return '<svg class="' . esc_attr( $cls ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.26-.47-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.63.71.22 1.36.19 1.87.12.57-.09 1.76-.72 2-1.41.25-.7.25-1.29.18-1.41-.08-.13-.28-.2-.57-.35m-5.42 7.4h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.89 6.99c0 5.45-4.44 9.88-9.88 9.88m8.41-18.3A11.82 11.82 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 0 0 5.68 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.16-3.48-8.41z"/></svg>';
	}

	if ( ! isset( $icons[ $name ] ) ) {
		$name = 'check';
	}

	return '<svg class="' . esc_attr( $cls ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * Lista de ícones disponíveis para os selects do painel.
 */
function wg_icon_choices() {
	return array(
		'som'        => 'Som',
		'luz'        => 'Iluminação',
		'led'        => 'Painel de LED',
		'projecao'   => 'Projeção',
		'palco'      => 'Palco / estrutura',
		'streaming'  => 'Transmissão',
		'cenografia' => 'Cenografia',
		'camera'     => 'Câmera',
		'energia'    => 'Energia',
		'users'      => 'Pessoas',
		'clipboard'  => 'Planejamento',
		'clock'      => 'Relógio',
		'wrench'     => 'Ferramenta',
		'headset'    => 'Atendimento',
		'target'     => 'Alvo',
		'shield'     => 'Segurança',
		'sliders'    => 'Ajustes',
		'truck'      => 'Logística',
		'box'        => 'Caixa',
	);
}

/**
 * Botão padrão.
 */
function wg_button( $label, $url, $variant = 'primary', $extra = array() ) {
	$classes = 'wg-btn wg-btn--' . $variant;
	if ( ! empty( $extra['class'] ) ) {
		$classes .= ' ' . $extra['class'];
	}
	$icon  = isset( $extra['icon'] ) ? $extra['icon'] : '';
	$attrs = '';
	if ( ! empty( $extra['wa'] ) ) {
		$attrs .= ' data-wa-click="' . esc_attr( $extra['wa'] ) . '"';
		if ( 0 === strpos( $url, 'https://wa.me/' ) ) {
			$attrs .= ' target="_blank" rel="noopener"';
		}
	}
	$html = '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( $url ) . '"' . $attrs . '>';
	if ( 'whatsapp' === $icon ) {
		$html .= wg_icon( 'whatsapp' );
	}
	$html .= '<span>' . esc_html( $label ) . '</span>';
	if ( 'arrow' === $icon ) {
		$html .= wg_icon( 'arrow' );
	}
	return $html . '</a>';
}

/**
 * Cabeçalho de seção: sobretítulo, título e texto.
 */
function wg_section_head( $eyebrow, $title, $text = '', $align = 'left', $tag = 'h2' ) {
	echo '<header class="wg-section-head wg-section-head--' . esc_attr( $align ) . ' wg-reveal">';
	if ( $eyebrow ) {
		echo '<p class="wg-eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	echo '<' . esc_attr( $tag ) . ' class="wg-title">' . wp_kses_post( $title ) . '</' . esc_attr( $tag ) . '>';
	if ( $text ) {
		echo '<p class="wg-lead">' . wp_kses_post( $text ) . '</p>';
	}
	echo '</header>';
}
