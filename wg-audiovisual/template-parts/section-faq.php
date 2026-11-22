<?php
/**
 * Perguntas frequentes.
 */
defined( 'ABSPATH' ) || exit;
$faq = wg_faq();
if ( ! $faq ) {
	return;
}
?>
<section class="wg-section wg-faq" id="perguntas">
	<div class="wg-container wg-faq__layout">
		<?php wg_section_head( __( 'Perguntas frequentes', 'wg' ), __( 'Tire suas dúvidas antes de pedir o orçamento', 'wg' ), __( 'Não encontrou o que procurava? Fale com a nossa equipe.', 'wg' ) ); ?>
		<div class="wg-accordion">
			<?php foreach ( $faq as $item ) : ?>
				<details class="wg-accordion__item wg-reveal">
					<summary><span><?php echo esc_html( $item[0] ); ?></span><?php echo wg_icon( 'plus' ); // phpcs:ignore ?></summary>
					<div class="wg-accordion__body"><p><?php echo esc_html( $item[1] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
