<?php
/**
 * Formulário inteligente de orçamento (3 etapas).
 * Sem JavaScript, todas as etapas aparecem de uma vez e o envio funciona normalmente.
 */
defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$wg_pre_service = isset( $_GET['servico'] ) ? sanitize_text_field( wp_unslash( $_GET['servico'] ) ) : '';
$wg_pre_event   = isset( $_GET['evento'] ) ? sanitize_text_field( wp_unslash( $_GET['evento'] ) ) : '';
$wg_sent        = isset( $_GET['enviado'], $_GET['k'] ) && hash_equals( wg_lead_key( (int) $_GET['enviado'] ), sanitize_text_field( wp_unslash( $_GET['k'] ) ) ) ? (int) $_GET['enviado'] : 0;
$wg_error       = isset( $_GET['erro'] );
// phpcs:enable

$wg_events = get_posts( array( 'post_type' => 'wg_evento', 'posts_per_page' => 30, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
$wg_types  = wp_list_pluck( $wg_events, 'post_title' );
if ( ! $wg_types ) {
	$wg_types = wp_list_pluck( wg_default_events(), 'title' );
}
$wg_types[] = __( 'Outro', 'wg' );

$wg_people = array( 'Até 50', '51 a 100', '101 a 300', '301 a 500', '501 a 1.000', '1.001 a 3.000', 'Acima de 3.000', 'Ainda não sei' );

$wg_wa_sent = '';
if ( $wg_sent ) {
	$wg_d       = (array) get_post_meta( $wg_sent, '_wg_lead', true );
	$wg_wa_sent = $wg_d ? wg_whatsapp_url( wg_lead_whatsapp_message( $wg_sent, $wg_d + array( 'tipo_evento' => '', 'cidade' => '', 'data_evento' => '', 'servicos' => array() ) ) ) : wg_whatsapp_url();
}
?>
<div class="wg-quote-form" id="orcamento" data-quote>

	<div class="wg-form-success" data-success <?php echo $wg_sent ? '' : 'hidden'; ?> tabindex="-1">
		<span class="wg-form-success__icon"><?php echo wg_icon( 'check' ); // phpcs:ignore ?></span>
		<h2><?php esc_html_e( 'Pedido recebido!', 'wg' ); ?></h2>
		<p><?php esc_html_e( 'Obrigado. Nossa equipe vai analisar as informações do seu evento e retornar com os próximos passos.', 'wg' ); ?></p>
		<p class="wg-form-success__protocol"><?php esc_html_e( 'Protocolo', 'wg' ); ?> <strong data-protocol>#<?php echo (int) $wg_sent; ?></strong></p>
		<p><?php esc_html_e( 'Quer agilizar? Continue a conversa pelo WhatsApp com o resumo do seu pedido.', 'wg' ); ?></p>
		<a class="wg-btn wg-btn--primary" data-success-wa data-wa-click="form-success" href="<?php echo esc_url( $wg_wa_sent ? $wg_wa_sent : wg_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo wg_icon( 'whatsapp' ); // phpcs:ignore ?><span><?php esc_html_e( 'Continuar no WhatsApp', 'wg' ); ?></span></a>
	</div>

	<form class="wg-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" novalidate data-form <?php echo $wg_sent ? 'hidden' : ''; ?>>
		<input type="hidden" name="action" value="wg_quote">
		<input type="hidden" name="wg_t" value="<?php echo esc_attr( time() ); ?>" data-ts>
		<input type="hidden" name="origem" value="" data-origin>
		<input type="hidden" name="utm" value="" data-utm>
		<div class="wg-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

		<?php if ( $wg_error ) : ?>
			<div class="wg-form__alert" role="alert"><?php esc_html_e( 'Alguns campos precisam de atenção. Revise e envie novamente.', 'wg' ); ?></div>
		<?php endif; ?>
		<div class="wg-form__alert" role="alert" data-form-alert hidden></div>

		<ol class="wg-form__progress" aria-hidden="true" data-progress>
			<li class="is-active"><span>1</span><?php esc_html_e( 'Seus dados', 'wg' ); ?></li>
			<li><span>2</span><?php esc_html_e( 'O evento', 'wg' ); ?></li>
			<li><span>3</span><?php esc_html_e( 'Soluções', 'wg' ); ?></li>
		</ol>

		<fieldset class="wg-form__step is-active" data-step="1">
			<legend class="wg-form__legend"><?php esc_html_e( 'Dados do solicitante', 'wg' ); ?></legend>
			<div class="wg-form__grid">
				<div class="wg-field"><label for="q-nome"><?php esc_html_e( 'Nome', 'wg' ); ?> <abbr title="obrigatório">*</abbr></label><input id="q-nome" name="nome" type="text" autocomplete="name" required></div>
				<div class="wg-field"><label for="q-empresa"><?php esc_html_e( 'Empresa', 'wg' ); ?> <abbr title="obrigatório">*</abbr></label><input id="q-empresa" name="empresa" type="text" autocomplete="organization" required></div>
				<div class="wg-field"><label for="q-email"><?php esc_html_e( 'E-mail corporativo', 'wg' ); ?> <abbr title="obrigatório">*</abbr></label><input id="q-email" name="email" type="email" autocomplete="email" inputmode="email" required></div>
				<div class="wg-field"><label for="q-wa"><?php esc_html_e( 'WhatsApp', 'wg' ); ?> <abbr title="obrigatório">*</abbr></label><input id="q-wa" name="whatsapp" type="tel" autocomplete="tel" inputmode="tel" placeholder="(11) 99999-9999" required data-mask="phone"></div>
				<div class="wg-field wg-field--full"><label for="q-cargo"><?php esc_html_e( 'Cargo', 'wg' ); ?> <span class="wg-optional"><?php esc_html_e( '(opcional)', 'wg' ); ?></span></label><input id="q-cargo" name="cargo" type="text" autocomplete="organization-title"></div>
			</div>
			<div class="wg-form__nav"><button type="button" class="wg-btn wg-btn--primary" data-next><span><?php esc_html_e( 'Continuar', 'wg' ); ?></span><?php echo wg_icon( 'arrow' ); // phpcs:ignore ?></button></div>
		</fieldset>

		<fieldset class="wg-form__step" data-step="2">
			<legend class="wg-form__legend"><?php esc_html_e( 'Informações do evento', 'wg' ); ?></legend>
			<div class="wg-form__grid">
				<div class="wg-field"><label for="q-tipo"><?php esc_html_e( 'Tipo de evento', 'wg' ); ?> <abbr title="obrigatório">*</abbr></label>
					<select id="q-tipo" name="tipo_evento" required>
						<option value=""><?php esc_html_e( 'Selecione', 'wg' ); ?></option>
						<?php foreach ( $wg_types as $wg_t ) : ?>
							<option <?php selected( $wg_pre_event, $wg_t ); ?>><?php echo esc_html( $wg_t ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="wg-field"><label for="q-data"><?php esc_html_e( 'Data do evento', 'wg' ); ?> <abbr title="obrigatório">*</abbr></label><input id="q-data" name="data_evento" type="date" required min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>"></div>
				<div class="wg-field"><label for="q-cidade"><?php esc_html_e( 'Cidade', 'wg' ); ?> <abbr title="obrigatório">*</abbr></label><input id="q-cidade" name="cidade" type="text" autocomplete="address-level2" required></div>
				<div class="wg-field"><label for="q-local"><?php esc_html_e( 'Local ou espaço', 'wg' ); ?></label><input id="q-local" name="local" type="text" placeholder="<?php esc_attr_e( 'Hotel, centro de convenções, sede da empresa…', 'wg' ); ?>"></div>
				<div class="wg-field"><label for="q-pessoas"><?php esc_html_e( 'Participantes (estimativa)', 'wg' ); ?> <abbr title="obrigatório">*</abbr></label>
					<select id="q-pessoas" name="participantes" required>
						<option value=""><?php esc_html_e( 'Selecione', 'wg' ); ?></option>
						<?php foreach ( $wg_people as $wg_p ) : ?>
							<option><?php echo esc_html( $wg_p ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="wg-field"><label for="q-dias"><?php esc_html_e( 'Quantidade de dias', 'wg' ); ?></label><input id="q-dias" name="dias" type="number" min="1" max="60" inputmode="numeric" placeholder="1"></div>
				<div class="wg-field"><label for="q-inicio"><?php esc_html_e( 'Horário de início', 'wg' ); ?></label><input id="q-inicio" name="hora_inicio" type="time"></div>
				<div class="wg-field"><label for="q-fim"><?php esc_html_e( 'Horário de término', 'wg' ); ?></label><input id="q-fim" name="hora_fim" type="time"></div>
			</div>
			<div class="wg-form__nav">
				<button type="button" class="wg-btn wg-btn--ghost" data-prev><?php echo wg_icon( 'arrow-left' ); // phpcs:ignore ?><span><?php esc_html_e( 'Voltar', 'wg' ); ?></span></button>
				<button type="button" class="wg-btn wg-btn--primary" data-next><span><?php esc_html_e( 'Continuar', 'wg' ); ?></span><?php echo wg_icon( 'arrow' ); // phpcs:ignore ?></button>
			</div>
		</fieldset>

		<fieldset class="wg-form__step" data-step="3">
			<legend class="wg-form__legend"><?php esc_html_e( 'Serviços desejados', 'wg' ); ?></legend>
			<p class="wg-form__hint" id="q-srv-hint"><?php esc_html_e( 'Selecione uma ou mais opções.', 'wg' ); ?></p>
			<div class="wg-options" role="group" aria-describedby="q-srv-hint" data-group="servicos">
				<?php foreach ( wg_form_service_options() as $wg_o ) : ?>
					<label class="wg-option"><input type="checkbox" name="servicos[]" value="<?php echo esc_attr( $wg_o ); ?>" <?php checked( $wg_pre_service, $wg_o ); ?>><span><?php echo esc_html( $wg_o ); ?></span></label>
				<?php endforeach; ?>
			</div>
			<div class="wg-form__grid">
				<div class="wg-field wg-field--full"><label for="q-msg"><?php esc_html_e( 'Conte um pouco sobre o projeto', 'wg' ); ?></label><textarea id="q-msg" name="mensagem" rows="5" placeholder="<?php esc_attr_e( 'Programação, formato do palco, referências, necessidades específicas…', 'wg' ); ?>"></textarea></div>
				<div class="wg-field wg-field--full">
					<label for="q-arquivo"><?php esc_html_e( 'Anexar briefing ou arquivos', 'wg' ); ?> <span class="wg-optional"><?php esc_html_e( '(opcional — PDF, Word, PowerPoint, Excel, imagem ou ZIP, até 10 MB)', 'wg' ); ?></span></label>
					<label class="wg-file"><input id="q-arquivo" name="arquivo" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"><?php echo wg_icon( 'upload' ); // phpcs:ignore ?><span data-file-label><?php esc_html_e( 'Escolher arquivo', 'wg' ); ?></span></label>
				</div>
			</div>
			<label class="wg-consent-check"><input type="checkbox" name="lgpd" value="1" required>
				<span><?php
				printf(
					/* translators: %s: link da política de privacidade */
					esc_html__( 'Concordo com o uso dos meus dados para receber a proposta e o contato comercial, conforme a %s.', 'wg' ),
					'<a href="' . esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : wg_page_url( 'politica-de-privacidade' ) ) . '" target="_blank">' . esc_html__( 'Política de privacidade', 'wg' ) . '</a>'
				);
				?></span>
			</label>
			<?php if ( wg_opt( 'turnstile_site' ) ) : ?>
				<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( wg_opt( 'turnstile_site' ) ); ?>" data-theme="dark" data-language="pt-br"></div>
			<?php endif; ?>
			<div class="wg-form__nav">
				<button type="button" class="wg-btn wg-btn--ghost" data-prev><?php echo wg_icon( 'arrow-left' ); // phpcs:ignore ?><span><?php esc_html_e( 'Voltar', 'wg' ); ?></span></button>
				<button type="submit" class="wg-btn wg-btn--primary" data-submit><span><?php esc_html_e( 'Enviar pedido de orçamento', 'wg' ); ?></span><?php echo wg_icon( 'arrow' ); // phpcs:ignore ?></button>
			</div>
		</fieldset>
	</form>
</div>
