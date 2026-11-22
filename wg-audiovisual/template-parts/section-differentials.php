<?php
/**
 * Por que escolher a empresa.
 */
defined( 'ABSPATH' ) || exit;
$items = wg_differentials();
if ( ! $items ) {
	return;
}
?>
<section class="wg-section wg-diffs">
	<div class="wg-container">
		<div class="wg-diffs__layout">
			<div class="wg-diffs__intro">
				<?php wg_section_head( __( 'Por que escolher a', 'wg' ) . ' ' . wg_company_name(), __( 'Uma equipe preparada para assumir a responsabilidade técnica do seu evento', 'wg' ), __( 'Você conversa com quem planeja, monta e opera. Sem intermediários e sem surpresas no dia.', 'wg' ) ); ?>
				<figure class="wg-diffs__media wg-reveal">
					<img src="<?php echo esc_url( wg_stock( 'operacao-tecnica' ) ); ?>" alt="<?php esc_attr_e( 'Técnico operando mesa de controle durante evento', 'wg' ); ?>" loading="lazy" decoding="async" width="1600" height="1066">
				</figure>
			</div>
			<ul class="wg-diffs__list">
				<?php foreach ( $items as $it ) : ?>
					<li class="wg-diff wg-reveal">
						<span class="wg-diff__icon"><?php echo wg_icon( $it['icon'] ); // phpcs:ignore ?></span>
						<div>
							<h3 class="wg-diff__title"><?php echo esc_html( $it['title'] ); ?></h3>
							<p class="wg-diff__text"><?php echo esc_html( $it['text'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
