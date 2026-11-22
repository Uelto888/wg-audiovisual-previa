<?php
/**
 * Template Name: Solicitar orçamento
 */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'eyebrow' => __( 'Orçamento', 'wg' ),
		'title'   => __( 'Conte sobre o seu evento', 'wg' ),
		'text'    => __( 'Quanto mais detalhes, mais precisa será a proposta. Leva cerca de 2 minutos.', 'wg' ),
		'image'   => wg_stock( 'sobre-operacao-audio' ),
		'buttons' => false,
		'size'    => 'sm',
	)
);
?>
<section class="wg-section wg-section--form">
	<div class="wg-container wg-quote-layout">
		<?php get_template_part( 'template-parts/quote-form' ); ?>
		<aside class="wg-quote-aside">
			<div class="wg-aside__card">
				<h2 class="wg-aside__title"><?php esc_html_e( 'O que acontece depois', 'wg' ); ?></h2>
				<ol class="wg-mini-steps">
					<li><strong><?php esc_html_e( 'Análise do pedido', 'wg' ); ?></strong><span><?php esc_html_e( 'Nossa equipe avalia as informações do evento.', 'wg' ); ?></span></li>
					<li><strong><?php esc_html_e( 'Conversa de briefing', 'wg' ); ?></strong><span><?php esc_html_e( 'Entramos em contato para entender os detalhes.', 'wg' ); ?></span></li>
					<li><strong><?php esc_html_e( 'Proposta sob medida', 'wg' ); ?></strong><span><?php esc_html_e( 'Você recebe uma solução técnica personalizada.', 'wg' ); ?></span></li>
				</ol>
			</div>
			<div class="wg-aside__card wg-aside__card--alt">
				<h2 class="wg-aside__title"><?php esc_html_e( 'Prefere conversar agora?', 'wg' ); ?></h2>
				<p><?php esc_html_e( 'Fale direto com a nossa equipe pelo WhatsApp.', 'wg' ); ?></p>
				<?php echo wg_button( __( 'Falar no WhatsApp', 'wg' ), wg_whatsapp_url(), 'ghost', array( 'icon' => 'whatsapp', 'wa' => 'quote-aside' ) ); // phpcs:ignore ?>
			</div>
		</aside>
	</div>
</section>
<?php
get_template_part( 'template-parts/section', 'faq' );
get_footer();
