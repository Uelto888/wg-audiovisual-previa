<?php
/**
 * Template Name: Contato
 */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'eyebrow' => __( 'Contato', 'wg' ),
		'title'   => __( 'Vamos conversar sobre o seu evento', 'wg' ),
		'text'    => __( 'Escolha o canal de sua preferência. Para orçamentos, o formulário ajuda a nossa equipe a responder com mais precisão.', 'wg' ),
		'image'   => wg_stock( 'equipe-tecnica' ),
		'buttons' => false,
		'size'    => 'sm',
	)
);

$wg_channels = array(
	array( 'whatsapp', __( 'WhatsApp', 'wg' ), wg_opt( 'whatsapp' ), wg_whatsapp_number() ? wg_whatsapp_url() : '', 'WhatsApp: DEFINIR' ),
	array( 'mail', __( 'E-mail', 'wg' ), wg_opt( 'email' ) ? antispambot( wg_opt( 'email' ) ) : '', wg_opt( 'email' ) ? 'mailto:' . antispambot( wg_opt( 'email' ) ) : '', 'E-mail: DEFINIR' ),
	array( 'phone', __( 'Telefone', 'wg' ), wg_opt( 'phone' ), wg_opt( 'phone' ) ? 'tel:' . preg_replace( '/[^\d+]/', '', wg_opt( 'phone' ) ) : '', '' ),
	array( 'pin', __( 'Endereço', 'wg' ), wg_opt( 'address' ), '', 'Endereço: DEFINIR' ),
	array( 'instagram', __( 'Instagram', 'wg' ), wg_opt( 'instagram' ) ? '@' . trim( wp_parse_url( wg_opt( 'instagram' ), PHP_URL_PATH ), '/' ) : '', wg_opt( 'instagram' ), 'Instagram: DEFINIR' ),
);
?>
<section class="wg-section">
	<div class="wg-container">
		<div class="wg-grid wg-grid--3 wg-contact-cards">
			<?php
			foreach ( $wg_channels as $wg_c ) :
				if ( ! $wg_c[2] && ( ! $wg_c[4] || ! current_user_can( 'edit_theme_options' ) ) ) {
					continue;
				}
				?>
				<div class="wg-contact-card wg-reveal">
					<span class="wg-contact-card__icon"><?php echo wg_icon( $wg_c[0] ); // phpcs:ignore ?></span>
					<h2 class="wg-contact-card__title"><?php echo esc_html( $wg_c[1] ); ?></h2>
					<?php if ( $wg_c[2] ) : ?>
						<?php if ( $wg_c[3] ) : ?>
							<a href="<?php echo esc_url( $wg_c[3] ); ?>" <?php echo 'whatsapp' === $wg_c[0] ? 'target="_blank" rel="noopener" data-wa-click="contact"' : ''; ?>><?php echo esc_html( $wg_c[2] ); ?></a>
						<?php else : ?>
							<p><?php echo nl2br( esc_html( $wg_c[2] ) ); ?></p>
						<?php endif; ?>
					<?php else : ?>
						<p><?php echo wg_pending( $wg_c[4] ); // phpcs:ignore ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
			<div class="wg-contact-card wg-contact-card--accent wg-reveal">
				<span class="wg-contact-card__icon"><?php echo wg_icon( 'clipboard' ); // phpcs:ignore ?></span>
				<h2 class="wg-contact-card__title"><?php esc_html_e( 'Orçamento', 'wg' ); ?></h2>
				<p><?php esc_html_e( 'Envie as informações do evento em 3 etapas rápidas.', 'wg' ); ?></p>
				<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url(), 'primary', array( 'icon' => 'arrow', 'class' => 'wg-btn--sm' ) ); // phpcs:ignore ?>
			</div>
		</div>
		<?php if ( wg_opt( 'area' ) || wg_opt( 'hours' ) ) : ?>
			<p class="wg-contact-note wg-reveal">
				<?php echo esc_html( trim( wg_opt( 'area' ) . ( wg_opt( 'area' ) && wg_opt( 'hours' ) ? ' · ' : '' ) . wg_opt( 'hours' ) ) ); ?>
			</p>
		<?php endif; ?>
		<?php
		while ( have_posts() ) {
			the_post();
			if ( get_the_content() ) {
				echo '<div class="wg-narrow wg-prose">';
				the_content();
				echo '</div>';
			}
		}
		?>
	</div>
</section>
<?php
get_template_part( 'template-parts/section', 'faq' );
get_footer();
