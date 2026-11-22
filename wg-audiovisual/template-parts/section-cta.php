<?php
/**
 * Chamada final para orçamento.
 */
defined( 'ABSPATH' ) || exit;
$a = wp_parse_args(
	$args,
	array(
		'title'   => wg_opt( 'cta_title' ),
		'text'    => wg_opt( 'cta_text' ),
		'service' => '',
		'wa'      => '',
		'image'   => 'cta-palco',
	)
);
?>
<section class="wg-cta">
	<div class="wg-cta__media" aria-hidden="true">
		<img src="<?php echo esc_url( wg_stock( $a['image'] ) ); ?>" alt="" loading="lazy" decoding="async">
	</div>
	<div class="wg-container wg-cta__inner wg-reveal">
		<h2 class="wg-cta__title"><?php echo esc_html( $a['title'] ); ?></h2>
		<p class="wg-cta__text"><?php echo esc_html( $a['text'] ); ?></p>
		<div class="wg-actions wg-actions--center">
			<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url( $a['service'] ), 'primary', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
			<?php echo wg_button( __( 'Falar no WhatsApp', 'wg' ), wg_whatsapp_url( $a['wa'] ), 'ghost', array( 'icon' => 'whatsapp', 'wa' => 'cta' ) ); // phpcs:ignore ?>
		</div>
	</div>
</section>
