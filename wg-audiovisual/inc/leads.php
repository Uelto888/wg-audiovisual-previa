<?php
/**
 * Formulário de orçamento: validação, registro de leads, e-mail, webhook para CRM e painel.
 */

defined( 'ABSPATH' ) || exit;

function wg_lead_statuses() {
	return array(
		'novo'      => __( 'Novo', 'wg' ),
		'contato'   => __( 'Em contato', 'wg' ),
		'proposta'  => __( 'Proposta enviada', 'wg' ),
		'ganho'     => __( 'Fechado', 'wg' ),
		'perdido'   => __( 'Não fechado', 'wg' ),
	);
}

function wg_lead_fields() {
	return array(
		'nome'          => 'Nome',
		'empresa'       => 'Empresa',
		'email'         => 'E-mail',
		'whatsapp'      => 'WhatsApp',
		'cargo'         => 'Cargo',
		'tipo_evento'   => 'Tipo de evento',
		'data_evento'   => 'Data do evento',
		'cidade'        => 'Cidade',
		'local'         => 'Local ou espaço',
		'participantes' => 'Participantes',
		'dias'          => 'Quantidade de dias',
		'hora_inicio'   => 'Início',
		'hora_fim'      => 'Término',
		'servicos'      => 'Serviços desejados',
		'mensagem'      => 'Sobre o projeto',
		'arquivo'       => 'Arquivo anexado',
		'origem'        => 'Página de origem',
		'utm'           => 'Campanha (UTM)',
	);
}

add_action( 'admin_post_nopriv_wg_quote', 'wg_handle_quote' );
add_action( 'admin_post_wg_quote', 'wg_handle_quote' );
add_action( 'wp_ajax_nopriv_wg_quote', 'wg_handle_quote' );
add_action( 'wp_ajax_wg_quote', 'wg_handle_quote' );

function wg_handle_quote() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- formulário público protegido por honeypot, tempo mínimo, limite por IP e Turnstile opcional (nonce quebraria com cache de página).
	$is_ajax = wp_doing_ajax();
	$p       = wp_unslash( $_POST );

	$fail = function ( $errors, $code = 400 ) use ( $is_ajax ) {
		if ( $is_ajax ) {
			wp_send_json( array( 'ok' => false, 'errors' => $errors ), $code );
		}
		wp_safe_redirect( add_query_arg( 'erro', '1', wp_get_referer() ? wp_get_referer() : wg_quote_url() ) . '#orcamento' );
		exit;
	};

	// Armadilhas anti-spam.
	if ( ! empty( $p['website'] ) ) {
		$fail( array( '_form' => __( 'Não foi possível enviar.', 'wg' ) ) );
	}
	$t = isset( $p['wg_t'] ) ? (int) $p['wg_t'] : 0;
	if ( $t && ( time() - $t ) < 4 ) {
		$fail( array( '_form' => __( 'Envio muito rápido. Tente novamente.', 'wg' ) ) );
	}
	$ip_key = 'wg_rl_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 6 ) {
		$fail( array( '_form' => __( 'Muitos envios em pouco tempo. Tente novamente mais tarde ou fale pelo WhatsApp.', 'wg' ) ), 429 );
	}

	if ( wg_opt( 'turnstile_secret' ) ) {
		$token = isset( $p['cf-turnstile-response'] ) ? sanitize_text_field( $p['cf-turnstile-response'] ) : '';
		$res   = wp_remote_post(
			'https://challenges.cloudflare.com/turnstile/v0/siteverify',
			array(
				'timeout' => 8,
				'body'    => array(
					'secret'   => wg_opt( 'turnstile_secret' ),
					'response' => $token,
				),
			)
		);
		$body = is_wp_error( $res ) ? array() : json_decode( wp_remote_retrieve_body( $res ), true );
		if ( empty( $body['success'] ) ) {
			$fail( array( '_form' => __( 'Confirme que você não é um robô e envie novamente.', 'wg' ) ) );
		}
	}

	// Coleta e validação.
	$d = array();
	foreach ( array( 'nome', 'empresa', 'whatsapp', 'cargo', 'tipo_evento', 'data_evento', 'cidade', 'local', 'participantes', 'dias', 'hora_inicio', 'hora_fim', 'origem', 'utm' ) as $k ) {
		$d[ $k ] = isset( $p[ $k ] ) ? sanitize_text_field( $p[ $k ] ) : '';
	}
	$d['email']    = isset( $p['email'] ) ? sanitize_email( $p['email'] ) : '';
	$d['mensagem'] = isset( $p['mensagem'] ) ? sanitize_textarea_field( $p['mensagem'] ) : '';
	$allowed_srv   = wg_form_service_options();
	$d['servicos'] = isset( $p['servicos'] ) && is_array( $p['servicos'] ) ? array_values( array_intersect( array_map( 'sanitize_text_field', $p['servicos'] ), $allowed_srv ) ) : array();

	$errors = array();
	$req    = array(
		'nome'          => __( 'Informe seu nome.', 'wg' ),
		'empresa'       => __( 'Informe a empresa.', 'wg' ),
		'whatsapp'      => __( 'Informe um WhatsApp para contato.', 'wg' ),
		'tipo_evento'   => __( 'Selecione o tipo de evento.', 'wg' ),
		'data_evento'   => __( 'Informe a data (mesmo que aproximada).', 'wg' ),
		'cidade'        => __( 'Informe a cidade.', 'wg' ),
		'participantes' => __( 'Informe o número estimado de participantes.', 'wg' ),
	);
	foreach ( $req as $k => $msg ) {
		if ( '' === $d[ $k ] ) {
			$errors[ $k ] = $msg;
		}
	}
	if ( ! is_email( $d['email'] ) ) {
		$errors['email'] = __( 'Informe um e-mail válido.', 'wg' );
	}
	if ( strlen( preg_replace( '/\D+/', '', $d['whatsapp'] ) ) < 10 && empty( $errors['whatsapp'] ) ) {
		$errors['whatsapp'] = __( 'Informe o número com DDD.', 'wg' );
	}
	if ( empty( $d['servicos'] ) ) {
		$errors['servicos'] = __( 'Selecione pelo menos uma opção.', 'wg' );
	}
	if ( empty( $p['lgpd'] ) ) {
		$errors['lgpd'] = __( 'É preciso concordar com o uso dos dados para receber a proposta.', 'wg' );
	}
	if ( $errors ) {
		$fail( $errors, 422 );
	}

	// Anexo opcional.
	$d['arquivo'] = '';
	if ( ! empty( $_FILES['arquivo']['name'] ) ) {
		$file = wg_store_briefing( $_FILES['arquivo'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( is_wp_error( $file ) ) {
			$fail( array( 'arquivo' => $file->get_error_message() ), 422 );
		}
		$d['arquivo'] = $file;
	}

	set_transient( $ip_key, $count + 1, HOUR_IN_SECONDS );

	$title   = trim( $d['nome'] . ' — ' . $d['empresa'] . ' — ' . $d['tipo_evento'] );
	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'wg_lead',
			'post_status' => 'publish',
			'post_title'  => $title,
		),
		true
	);
	if ( is_wp_error( $lead_id ) ) {
		$fail( array( '_form' => __( 'Não foi possível registrar seu pedido. Tente novamente ou fale pelo WhatsApp.', 'wg' ) ), 500 );
	}
	update_post_meta( $lead_id, '_wg_lead', $d );
	update_post_meta( $lead_id, '_wg_lead_status', 'novo' );
	foreach ( array( 'empresa', 'tipo_evento', 'data_evento', 'cidade', 'email' ) as $k ) {
		update_post_meta( $lead_id, '_wg_lead_' . $k, $d[ $k ] );
	}

	wg_notify_lead( $lead_id, $d );
	wg_send_webhook( $lead_id, $d );

	$wa = wg_whatsapp_url( wg_lead_whatsapp_message( $lead_id, $d ) );

	if ( $is_ajax ) {
		wp_send_json(
			array(
				'ok'       => true,
				'id'       => $lead_id,
				'whatsapp' => $wa,
				'has_wa'   => '' !== wg_whatsapp_number(),
			)
		);
	}
	$url = add_query_arg(
		array(
			'enviado' => $lead_id,
			'k'       => wg_lead_key( $lead_id ),
		),
		wg_quote_url()
	);
	wp_safe_redirect( $url . '#orcamento' );
	exit;
	// phpcs:enable
}

function wg_lead_key( $id ) {
	return substr( hash_hmac( 'sha256', 'lead' . $id, wp_salt( 'nonce' ) ), 0, 12 );
}

function wg_lead_whatsapp_message( $id, $d ) {
	$parts = array( sprintf( 'Olá! Acabei de enviar uma solicitação de orçamento pelo site (protocolo #%d).', $id ) );
	$info  = array_filter( array( $d['tipo_evento'], $d['cidade'], $d['data_evento'] ? 'data ' . wg_format_date( $d['data_evento'] ) : '' ) );
	if ( $info ) {
		$parts[] = 'Evento: ' . implode( ', ', $info ) . '.';
	}
	if ( ! empty( $d['servicos'] ) ) {
		$parts[] = 'Interesse: ' . implode( ', ', $d['servicos'] ) . '.';
	}
	$parts[] = 'Gostaria de dar continuidade por aqui.';
	return implode( ' ', $parts );
}

function wg_format_date( $ymd ) {
	$ts = strtotime( $ymd );
	return $ts ? date_i18n( 'd/m/Y', $ts ) : $ymd;
}

/**
 * Salva o arquivo de briefing em uma pasta própria com nome aleatório.
 */
function wg_store_briefing( $file ) {
	if ( ! empty( $file['error'] ) ) {
		return new WP_Error( 'upload', __( 'Não foi possível receber o arquivo.', 'wg' ) );
	}
	if ( $file['size'] > 10 * MB_IN_BYTES ) {
		return new WP_Error( 'upload', __( 'O arquivo deve ter até 10 MB.', 'wg' ) );
	}
	$mimes = array(
		'pdf'  => 'application/pdf',
		'doc'  => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'ppt'  => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'xls'  => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'jpg|jpeg' => 'image/jpeg',
		'png'  => 'image/png',
		'zip'  => 'application/zip',
	);
	$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );
	if ( empty( $check['ext'] ) ) {
		return new WP_Error( 'upload', __( 'Formato não aceito. Envie PDF, Word, PowerPoint, Excel, imagem ou ZIP.', 'wg' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	$dir_filter = function ( $dirs ) {
		$dirs['subdir'] = '/wg-briefings';
		$dirs['path']   = $dirs['basedir'] . '/wg-briefings';
		$dirs['url']    = $dirs['baseurl'] . '/wg-briefings';
		return $dirs;
	};
	$name_filter = function ( $f ) use ( $check ) {
		$f['name'] = wp_generate_password( 20, false ) . '.' . $check['ext'];
		return $f;
	};
	add_filter( 'upload_dir', $dir_filter );
	add_filter( 'wp_handle_upload_prefilter', $name_filter );
	$moved = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => $mimes,
		)
	);
	remove_filter( 'upload_dir', $dir_filter );
	remove_filter( 'wp_handle_upload_prefilter', $name_filter );

	if ( isset( $moved['error'] ) ) {
		return new WP_Error( 'upload', $moved['error'] );
	}
	$index = dirname( $moved['file'] ) . '/index.php';
	if ( ! file_exists( $index ) ) {
		file_put_contents( $index, "<?php // Silence.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	return $moved['url'];
}

function wg_lead_rows( $d ) {
	$rows = array();
	foreach ( wg_lead_fields() as $k => $label ) {
		$v = isset( $d[ $k ] ) ? $d[ $k ] : '';
		if ( is_array( $v ) ) {
			$v = implode( ', ', $v );
		}
		if ( 'data_evento' === $k && $v ) {
			$v = wg_format_date( $v );
		}
		if ( '' !== $v ) {
			$rows[ $label ] = $v;
		}
	}
	return $rows;
}

function wg_notify_lead( $id, $d ) {
	$to = wg_opt( 'lead_email' ) ? array_map( 'trim', explode( ',', wg_opt( 'lead_email' ) ) ) : array( get_option( 'admin_email' ) );
	$subject = sprintf( '[Orçamento #%d] %s — %s', $id, $d['empresa'], $d['tipo_evento'] );
	$html    = '<div style="font-family:Arial,sans-serif;max-width:640px"><h2 style="color:#E6197A;margin:0 0 12px">Novo pedido de orçamento #' . (int) $id . '</h2><table cellpadding="8" style="border-collapse:collapse;width:100%">';
	foreach ( wg_lead_rows( $d ) as $label => $v ) {
		$val   = ( 'Arquivo anexado' === $label ) ? '<a href="' . esc_url( $v ) . '">Baixar arquivo</a>' : nl2br( esc_html( $v ) );
		$html .= '<tr><td style="border-bottom:1px solid #eee;color:#666;width:180px;vertical-align:top">' . esc_html( $label ) . '</td><td style="border-bottom:1px solid #eee">' . $val . '</td></tr>';
	}
	$wa     = preg_replace( '/\D+/', '', $d['whatsapp'] );
	$html  .= '</table><p style="margin-top:20px"><a href="' . esc_url( admin_url( 'post.php?action=edit&post=' . $id ) ) . '">Abrir no painel</a> · <a href="https://wa.me/' . ( strlen( $wa ) <= 11 ? '55' : '' ) . esc_attr( $wa ) . '">Responder pelo WhatsApp</a></p></div>';
	$headers = array( 'Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . $d['nome'] . ' <' . $d['email'] . '>' );
	wp_mail( $to, $subject, $html, $headers );
}

function wg_send_webhook( $id, $d ) {
	$url = wg_opt( 'webhook_url' );
	if ( ! $url ) {
		return;
	}
	$payload = array_merge(
		array(
			'id'         => $id,
			'criado_em'  => current_time( 'c' ),
			'site'       => home_url(),
			'painel_url' => admin_url( 'post.php?action=edit&post=' . $id ),
		),
		$d
	);
	wp_remote_post(
		$url,
		array(
			'timeout'  => 5,
			'blocking' => false,
			'headers'  => array( 'Content-Type' => 'application/json' ),
			'body'     => wp_json_encode( $payload ),
		)
	);
}

/* -------------------------------------------------------------------------
 * Painel: lista, detalhes, status e exportação
 * ---------------------------------------------------------------------- */

add_filter( 'manage_wg_lead_posts_columns', function () {
	return array(
		'cb'        => '<input type="checkbox">',
		'title'     => __( 'Contato', 'wg' ),
		'wg_event'  => __( 'Evento', 'wg' ),
		'wg_date'   => __( 'Data do evento', 'wg' ),
		'wg_city'   => __( 'Cidade', 'wg' ),
		'wg_status' => __( 'Status', 'wg' ),
		'date'      => __( 'Recebido em', 'wg' ),
	);
} );

add_action( 'manage_wg_lead_posts_custom_column', function ( $col, $id ) {
	switch ( $col ) {
		case 'wg_event':
			echo esc_html( get_post_meta( $id, '_wg_lead_tipo_evento', true ) );
			break;
		case 'wg_date':
			echo esc_html( wg_format_date( get_post_meta( $id, '_wg_lead_data_evento', true ) ) );
			break;
		case 'wg_city':
			echo esc_html( get_post_meta( $id, '_wg_lead_cidade', true ) );
			break;
		case 'wg_status':
			$s   = get_post_meta( $id, '_wg_lead_status', true );
			$all = wg_lead_statuses();
			echo '<span class="wg-status wg-status--' . esc_attr( $s ) . '">' . esc_html( isset( $all[ $s ] ) ? $all[ $s ] : $s ) . '</span>';
			break;
	}
}, 10, 2 );

add_action( 'restrict_manage_posts', function ( $type ) {
	if ( 'wg_lead' !== $type ) {
		return;
	}
	$cur = isset( $_GET['wg_status'] ) ? sanitize_key( $_GET['wg_status'] ) : ''; // phpcs:ignore
	echo '<select name="wg_status"><option value="">' . esc_html__( 'Todos os status', 'wg' ) . '</option>';
	foreach ( wg_lead_statuses() as $k => $l ) {
		echo '<option value="' . esc_attr( $k ) . '" ' . selected( $cur, $k, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select>';
	echo ' <a class="button" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=wg_export_leads' ), 'wg_export' ) ) . '">' . esc_html__( 'Exportar planilha (CSV)', 'wg' ) . '</a>';
} );

add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() && $q->is_main_query() && 'wg_lead' === $q->get( 'post_type' ) && ! empty( $_GET['wg_status'] ) ) { // phpcs:ignore
		$q->set( 'meta_key', '_wg_lead_status' );
		$q->set( 'meta_value', sanitize_key( $_GET['wg_status'] ) ); // phpcs:ignore
	}
} );

add_action( 'add_meta_boxes_wg_lead', function () {
	remove_meta_box( 'submitdiv', 'wg_lead', 'side' );
	add_meta_box( 'wg_lead_data', __( 'Dados do pedido', 'wg' ), 'wg_render_lead_box', 'wg_lead', 'normal', 'high' );
	add_meta_box( 'wg_lead_status', __( 'Acompanhamento', 'wg' ), 'wg_render_lead_status_box', 'wg_lead', 'side', 'high' );
} );

function wg_render_lead_box( $post ) {
	$d = (array) get_post_meta( $post->ID, '_wg_lead', true );
	echo '<table class="widefat striped wg-lead-table"><tbody>';
	foreach ( wg_lead_rows( $d ) as $label => $v ) {
		if ( 'Arquivo anexado' === $label ) {
			$v = '<a href="' . esc_url( $v ) . '" target="_blank" rel="noopener">' . esc_html__( 'Baixar arquivo', 'wg' ) . '</a>';
		} elseif ( 'E-mail' === $label ) {
			$v = '<a href="mailto:' . esc_attr( $v ) . '">' . esc_html( $v ) . '</a>';
		} elseif ( 'WhatsApp' === $label ) {
			$n = preg_replace( '/\D+/', '', $v );
			$v = '<a href="https://wa.me/' . ( strlen( $n ) <= 11 ? '55' : '' ) . esc_attr( $n ) . '" target="_blank" rel="noopener">' . esc_html( $v ) . '</a>';
		} else {
			$v = nl2br( esc_html( $v ) );
		}
		echo '<tr><th style="width:200px">' . esc_html( $label ) . '</th><td>' . $v . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</tbody></table>';
}

function wg_render_lead_status_box( $post ) {
	wp_nonce_field( 'wg_lead_status', 'wg_lead_nonce' );
	$s = get_post_meta( $post->ID, '_wg_lead_status', true );
	echo '<p><label><strong>' . esc_html__( 'Status', 'wg' ) . '</strong><br><select name="wg_lead_status" class="widefat">';
	foreach ( wg_lead_statuses() as $k => $l ) {
		echo '<option value="' . esc_attr( $k ) . '" ' . selected( $s, $k, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select></label></p><p><label><strong>' . esc_html__( 'Anotações internas', 'wg' ) . '</strong><br><textarea name="wg_lead_notes" class="widefat" rows="6">' . esc_textarea( get_post_meta( $post->ID, '_wg_lead_notes', true ) ) . '</textarea></label></p>';
	submit_button( __( 'Salvar', 'wg' ), 'primary', 'save', false );
	echo ' <a class="submitdelete" style="margin-left:8px;color:#b32d2e" href="' . esc_url( get_delete_post_link( $post->ID ) ) . '">' . esc_html__( 'Mover para a lixeira', 'wg' ) . '</a>';
	echo '<input type="hidden" name="post_status" value="publish">';
}

add_action( 'save_post_wg_lead', function ( $id ) {
	if ( ! isset( $_POST['wg_lead_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wg_lead_nonce'] ) ), 'wg_lead_status' ) || ! current_user_can( 'edit_post', $id ) ) {
		return;
	}
	$s = isset( $_POST['wg_lead_status'] ) ? sanitize_key( $_POST['wg_lead_status'] ) : 'novo';
	if ( isset( wg_lead_statuses()[ $s ] ) ) {
		update_post_meta( $id, '_wg_lead_status', $s );
	}
	update_post_meta( $id, '_wg_lead_notes', isset( $_POST['wg_lead_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wg_lead_notes'] ) ) : '' );
} );

/**
 * Contador de pedidos novos no menu.
 */
add_action( 'admin_menu', function () {
	global $menu;
	$count = count(
		get_posts(
			array(
				'post_type'      => 'wg_lead',
				'posts_per_page' => 99,
				'fields'         => 'ids',
				'meta_key'       => '_wg_lead_status',
				'meta_value'     => 'novo',
			)
		)
	);
	if ( ! $count ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=wg_lead' === $item[2] ) {
			$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . (int) $count . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}
}, 99 );

add_action( 'admin_post_wg_export_leads', function () {
	if ( ! current_user_can( 'edit_posts' ) || ! check_admin_referer( 'wg_export' ) ) {
		wp_die( esc_html__( 'Sem permissão.', 'wg' ) );
	}
	$leads = get_posts( array( 'post_type' => 'wg_lead', 'posts_per_page' => -1, 'post_status' => 'publish' ) );
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=pedidos-de-orcamento-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$fields = wg_lead_fields();
	fputcsv( $out, array_merge( array( 'Protocolo', 'Recebido em', 'Status' ), array_values( $fields ), array( 'Anotações' ) ), ';' );
	$statuses = wg_lead_statuses();
	foreach ( $leads as $l ) {
		$d   = (array) get_post_meta( $l->ID, '_wg_lead', true );
		$s   = get_post_meta( $l->ID, '_wg_lead_status', true );
		$row = array( $l->ID, get_the_date( 'd/m/Y H:i', $l ), isset( $statuses[ $s ] ) ? $statuses[ $s ] : $s );
		foreach ( array_keys( $fields ) as $k ) {
			$v     = isset( $d[ $k ] ) ? $d[ $k ] : '';
			$row[] = is_array( $v ) ? implode( ', ', $v ) : $v;
		}
		$row[] = get_post_meta( $l->ID, '_wg_lead_notes', true );
		fputcsv( $out, $row, ';' );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
} );

/**
 * Remove a edição de título dos leads (somente leitura).
 */
add_action( 'admin_head', function () {
	$screen = get_current_screen();
	if ( $screen && 'wg_lead' === $screen->post_type ) {
		echo '<style>#titlediv #title{pointer-events:none;background:#f6f7f7}.page-title-action{display:none}</style>';
	}
} );
