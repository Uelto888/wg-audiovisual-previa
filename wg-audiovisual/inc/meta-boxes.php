<?php
/**
 * Campos extras de edição no painel (sem depender de plugins).
 */

defined( 'ABSPATH' ) || exit;

function wg_meta_fields() {
	$services = array();
	if ( is_admin() ) {
		foreach ( get_posts( array( 'post_type' => 'wg_servico', 'posts_per_page' => 50, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $s ) {
			$services[ $s->ID ] = $s->post_title;
		}
	}

	return array(
		'wg_servico'    => array(
			'title'  => __( 'Detalhes da solução', 'wg' ),
			'fields' => array(
				'_wg_headline' => array( 'label' => 'Frase de benefício (aparece no topo da página)', 'type' => 'text' ),
				'_wg_icon'     => array( 'label' => 'Ícone', 'type' => 'select', 'options' => wg_icon_choices() ),
				'_wg_items'    => array( 'label' => 'O que pode fazer parte (um item por linha)', 'type' => 'textarea', 'rows' => 8 ),
				'_wg_benefits' => array( 'label' => 'Benefícios (um por linha, no formato: Título | Descrição)', 'type' => 'textarea', 'rows' => 4 ),
				'_wg_stock'    => array( 'label' => 'Imagem padrão do tema (usada se não houver imagem destacada)', 'type' => 'text', 'help' => 'Nome do arquivo em assets/img/stock, sem extensão.' ),
			),
		),
		'wg_evento'     => array(
			'title'  => __( 'Detalhes do tipo de evento', 'wg' ),
			'fields' => array(
				'_wg_has_page' => array( 'label' => 'Ter página própria no site (recomendado só quando houver texto completo)', 'type' => 'checkbox' ),
				'_wg_services' => array( 'label' => 'Soluções recomendadas', 'type' => 'checkboxes', 'options' => $services ),
				'_wg_stock'    => array( 'label' => 'Imagem padrão do tema', 'type' => 'text' ),
			),
		),
		'wg_projeto'    => array(
			'title'  => __( 'Ficha do projeto', 'wg' ),
			'fields' => array(
				'_wg_client'   => array( 'label' => 'Cliente (deixe em branco se não puder ser divulgado)', 'type' => 'text' ),
				'_wg_location' => array( 'label' => 'Local', 'type' => 'text' ),
				'_wg_date'     => array( 'label' => 'Data ou período (ex.: Março de 2026)', 'type' => 'text' ),
				'_wg_audience' => array( 'label' => 'Público (ex.: 800 participantes)', 'type' => 'text' ),
				'_wg_services' => array( 'label' => 'Serviços executados', 'type' => 'checkboxes', 'options' => $services ),
				'_wg_gallery'  => array( 'label' => 'Galeria de fotos', 'type' => 'gallery' ),
				'_wg_featured' => array( 'label' => 'Destacar na página inicial', 'type' => 'checkbox' ),
			),
		),
		'wg_depoimento' => array(
			'title'  => __( 'Autor do depoimento', 'wg' ),
			'fields' => array(
				'_wg_role'    => array( 'label' => 'Cargo', 'type' => 'text' ),
				'_wg_company' => array( 'label' => 'Empresa', 'type' => 'text' ),
			),
		),
		'wg_cliente'    => array(
			'title'  => __( 'Dados', 'wg' ),
			'fields' => array(
				'_wg_kind' => array( 'label' => 'Tipo', 'type' => 'select', 'options' => array( 'cliente' => 'Cliente', 'parceiro' => 'Parceiro', 'fornecedor' => 'Fornecedor autorizado' ) ),
				'_wg_url'  => array( 'label' => 'Site (opcional)', 'type' => 'url' ),
			),
		),
	);
}

add_action( 'add_meta_boxes', function () {
	foreach ( wg_meta_fields() as $type => $box ) {
		add_meta_box( 'wg_box_' . $type, $box['title'], 'wg_render_meta_box', $type, 'normal', 'high', array( 'type' => $type ) );
	}
	if ( ! wg_seo_plugin_active() ) {
		foreach ( array( 'page', 'post', 'wg_servico', 'wg_evento', 'wg_projeto' ) as $type ) {
			add_meta_box( 'wg_seo', __( 'SEO (Google)', 'wg' ), 'wg_render_seo_box', $type, 'normal', 'low' );
		}
	}
	add_meta_box( 'wg_box_help_depo', __( 'Como preencher', 'wg' ), function () {
		echo '<p>' . esc_html__( 'Título: nome da pessoa. Texto principal: o depoimento, exatamente como enviado pelo cliente. Imagem destacada: foto da pessoa (opcional).', 'wg' ) . '</p>';
	}, 'wg_depoimento', 'side' );
	add_meta_box( 'wg_box_help_cli', __( 'Como preencher', 'wg' ), function () {
		echo '<p>' . esc_html__( 'Título: nome da empresa. Imagem destacada: logotipo (PNG ou SVG com fundo transparente). Publique apenas marcas com autorização.', 'wg' ) . '</p>';
	}, 'wg_cliente', 'side' );
} );

function wg_render_meta_box( $post, $box ) {
	$type   = $box['args']['type'];
	$fields = wg_meta_fields()[ $type ]['fields'];
	wp_nonce_field( 'wg_meta', 'wg_meta_nonce' );
	echo '<div class="wg-meta">';
	foreach ( $fields as $key => $f ) {
		wg_render_field( $key, $f, get_post_meta( $post->ID, $key, true ) );
	}
	echo '</div>';
}

function wg_render_field( $key, $f, $value, $name = null ) {
	$name = $name ? $name : $key;
	$id   = 'f' . md5( $name );
	echo '<p class="wg-field wg-field--' . esc_attr( $f['type'] ) . '">';
	switch ( $f['type'] ) {
		case 'checkbox':
			echo '<label><input type="checkbox" name="' . esc_attr( $name ) . '" value="1" ' . checked( '1', $value, false ) . '> ' . esc_html( $f['label'] ) . '</label>';
			break;
		case 'checkboxes':
			$value = is_array( $value ) ? array_map( 'intval', $value ) : array();
			echo '<strong>' . esc_html( $f['label'] ) . '</strong><br>';
			if ( empty( $f['options'] ) ) {
				echo '<em>' . esc_html__( 'Cadastre as soluções primeiro.', 'wg' ) . '</em>';
			}
			foreach ( $f['options'] as $v => $l ) {
				echo '<label style="display:inline-block;min-width:240px;margin:2px 0"><input type="checkbox" name="' . esc_attr( $name ) . '[]" value="' . esc_attr( $v ) . '" ' . checked( in_array( (int) $v, $value, true ), true, false ) . '> ' . esc_html( $l ) . '</label>';
			}
			break;
		case 'select':
			echo '<label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $f['label'] ) . '</strong></label><br><select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
			foreach ( $f['options'] as $v => $l ) {
				echo '<option value="' . esc_attr( $v ) . '" ' . selected( $value, $v, false ) . '>' . esc_html( $l ) . '</option>';
			}
			echo '</select>';
			break;
		case 'textarea':
			echo '<label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $f['label'] ) . '</strong></label><br><textarea class="widefat" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="' . esc_attr( isset( $f['rows'] ) ? $f['rows'] : 4 ) . '">' . esc_textarea( $value ) . '</textarea>';
			break;
		case 'gallery':
			$ids = array_filter( array_map( 'intval', explode( ',', (string) $value ) ) );
			echo '<strong>' . esc_html( $f['label'] ) . '</strong><br><span class="wg-gallery-preview">';
			foreach ( $ids as $img ) {
				echo wp_get_attachment_image( $img, 'thumbnail' );
			}
			echo '</span><input type="hidden" class="wg-gallery-input" name="' . esc_attr( $name ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '"><br><button type="button" class="button wg-gallery-pick">' . esc_html__( 'Selecionar fotos', 'wg' ) . '</button> <button type="button" class="button-link wg-gallery-clear">' . esc_html__( 'Limpar', 'wg' ) . '</button>';
			break;
		case 'image':
			$img = (int) $value;
			echo '<strong>' . esc_html( $f['label'] ) . '</strong><br><span class="wg-gallery-preview">' . ( $img ? wp_get_attachment_image( $img, 'medium' ) : '' ) . '</span><input type="hidden" class="wg-gallery-input" data-single="1" name="' . esc_attr( $name ) . '" value="' . esc_attr( $img ? $img : '' ) . '"><br><button type="button" class="button wg-gallery-pick" data-single="1">' . esc_html__( 'Selecionar imagem', 'wg' ) . '</button> <button type="button" class="button-link wg-gallery-clear">' . esc_html__( 'Remover', 'wg' ) . '</button>';
			break;
		default:
			echo '<label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $f['label'] ) . '</strong></label><br><input class="widefat" type="' . esc_attr( 'url' === $f['type'] ? 'url' : 'text' ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
	}
	if ( ! empty( $f['help'] ) ) {
		echo '<br><span class="description">' . esc_html( $f['help'] ) . '</span>';
	}
	echo '</p>';
}

function wg_render_seo_box( $post ) {
	wp_nonce_field( 'wg_meta', 'wg_meta_nonce' );
	$t = get_post_meta( $post->ID, '_wg_seo_title', true );
	$d = get_post_meta( $post->ID, '_wg_seo_desc', true );
	echo '<p><label><strong>' . esc_html__( 'Título para o Google (até ~60 caracteres)', 'wg' ) . '</strong><br><input class="widefat" name="_wg_seo_title" value="' . esc_attr( $t ) . '" maxlength="90"></label></p>';
	echo '<p><label><strong>' . esc_html__( 'Descrição para o Google (até ~155 caracteres)', 'wg' ) . '</strong><br><textarea class="widefat" name="_wg_seo_desc" rows="3" maxlength="320">' . esc_textarea( $d ) . '</textarea></label></p>';
	echo '<p class="description">' . esc_html__( 'Se você instalar o Rank Math ou o Yoast, esta caixa some e o plugin assume essa função.', 'wg' ) . '</p>';
}

add_action( 'save_post', function ( $post_id, $post ) {
	if ( ! isset( $_POST['wg_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wg_meta_nonce'] ) ), 'wg_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$all    = wg_meta_fields();
	$fields = isset( $all[ $post->post_type ] ) ? $all[ $post->post_type ]['fields'] : array();
	$fields['_wg_seo_title'] = array( 'type' => 'text' );
	$fields['_wg_seo_desc']  = array( 'type' => 'textarea' );

	foreach ( $fields as $key => $f ) {
		if ( in_array( $key, array( '_wg_seo_title', '_wg_seo_desc' ), true ) && ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		switch ( $f['type'] ) {
			case 'checkbox':
				$val = $raw ? '1' : '';
				break;
			case 'checkboxes':
				$val = is_array( $raw ) ? array_map( 'intval', $raw ) : array();
				break;
			case 'textarea':
				$val = sanitize_textarea_field( (string) $raw );
				break;
			case 'url':
				$val = esc_url_raw( (string) $raw );
				break;
			case 'gallery':
				$val = implode( ',', array_filter( array_map( 'intval', explode( ',', (string) $raw ) ) ) );
				break;
			default:
				$val = sanitize_text_field( (string) $raw );
		}
		if ( '' === $val || array() === $val ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $val );
		}
	}
}, 10, 2 );

/**
 * Seletor de imagens no painel.
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	$screen = get_current_screen();
	$types  = array( 'wg_projeto', 'wg_servico', 'wg_evento', 'wg_depoimento', 'wg_cliente', 'wg_lead' );
	$is_opt = false !== strpos( (string) $hook, 'wg-' );
	if ( ( $screen && in_array( $screen->post_type, $types, true ) ) || $is_opt ) {
		wp_enqueue_media();
		wp_enqueue_script( 'wg-admin', WG_URI . '/assets/js/admin.js', array( 'jquery' ), WG_VERSION, true );
		wp_enqueue_style( 'wg-admin', WG_URI . '/assets/css/admin.css', array(), WG_VERSION );
	}
} );
