<?php
/**
 * Como funciona: etapas da contratação.
 */
defined( 'ABSPATH' ) || exit;
?>
<section class="wg-section wg-section--light wg-process" id="como-funciona">
	<div class="wg-container">
		<?php wg_section_head( __( 'Como funciona', 'wg' ), __( 'Do briefing à desmontagem, um processo claro', 'wg' ), __( 'Você sabe exatamente o que acontece em cada etapa e quem é o responsável por ela.', 'wg' ), 'center' ); ?>
		<ol class="wg-steps">
			<?php foreach ( wg_default_process() as $i => $step ) : ?>
				<li class="wg-step wg-reveal">
					<span class="wg-step__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="wg-step__icon"><?php echo wg_icon( $step[0] ); // phpcs:ignore ?></span>
					<h3 class="wg-step__title"><?php echo esc_html( $step[1] ); ?></h3>
					<p class="wg-step__text"><?php echo esc_html( $step[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
		<div class="wg-actions wg-actions--center wg-reveal">
			<?php echo wg_button( __( 'Começar pelo briefing', 'wg' ), wg_quote_url(), 'dark', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
		</div>
	</div>
</section>
