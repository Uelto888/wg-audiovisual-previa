<?php
/**
 * Rodapé do site.
 */
defined( 'ABSPATH' ) || exit;

$wg_services = get_posts(
	array(
		'post_type'      => 'wg_servico',
		'posts_per_page' => 12,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);
?>
</main>

<footer class="wg-footer">
	<div class="wg-container">
		<div class="wg-footer__grid">
			<div class="wg-footer__brand">
				<a class="wg-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo wg_logo(); // phpcs:ignore ?></a>
				<p><?php echo esc_html( wg_opt( 'value_text' ) ); ?></p>
				<div class="wg-social">
					<?php
					$wg_social = array(
						'instagram' => 'Instagram',
						'linkedin'  => 'LinkedIn',
						'youtube'   => 'YouTube',
					);
					$wg_any = false;
					foreach ( $wg_social as $wg_key => $wg_label ) {
						if ( wg_opt( $wg_key ) ) {
							$wg_any = true;
							echo '<a href="' . esc_url( wg_opt( $wg_key ) ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $wg_label ) . '">' . wg_icon( $wg_key ) . '</a>'; // phpcs:ignore
						}
					}
					if ( ! $wg_any ) {
						echo wg_pending( 'Instagram: DEFINIR' ); // phpcs:ignore
					}
					?>
				</div>
			</div>

			<div class="wg-footer__col">
				<h2 class="wg-footer__title"><?php esc_html_e( 'Soluções', 'wg' ); ?></h2>
				<ul>
					<?php foreach ( $wg_services as $wg_s ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $wg_s ) ); ?>"><?php echo esc_html( get_the_title( $wg_s ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="wg-footer__col">
				<h2 class="wg-footer__title"><?php esc_html_e( 'Empresa', 'wg' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
						)
					);
				} else {
					echo '<ul>';
					echo '<li><a href="' . esc_url( get_post_type_archive_link( 'wg_evento' ) ) . '">' . esc_html__( 'Tipos de evento', 'wg' ) . '</a></li>';
					echo '<li><a href="' . esc_url( get_post_type_archive_link( 'wg_projeto' ) ) . '">' . esc_html__( 'Portfólio', 'wg' ) . '</a></li>';
					echo '<li><a href="' . esc_url( wg_page_url( 'sobre' ) ) . '">' . esc_html__( 'Sobre a empresa', 'wg' ) . '</a></li>';
					echo '<li><a href="' . esc_url( wg_page_url( 'contato' ) ) . '">' . esc_html__( 'Contato', 'wg' ) . '</a></li>';
					echo '</ul>';
				}
				?>
			</div>

			<div class="wg-footer__col wg-footer__contact">
				<h2 class="wg-footer__title"><?php esc_html_e( 'Contato', 'wg' ); ?></h2>
				<ul>
					<?php
					$wg_admin    = current_user_can( 'edit_theme_options' );
					$wg_contacts = array(
						array( 'whatsapp', wg_whatsapp_number() ? '<a href="' . esc_url( wg_whatsapp_url() ) . '" target="_blank" rel="noopener" data-wa-click="footer">' . esc_html( wg_opt( 'whatsapp' ) ) . '</a>' : '', 'WhatsApp: DEFINIR' ),
						array( 'mail', wg_opt( 'email' ) ? '<a href="mailto:' . esc_attr( antispambot( wg_opt( 'email' ) ) ) . '">' . esc_html( antispambot( wg_opt( 'email' ) ) ) . '</a>' : '', 'E-mail: DEFINIR' ),
						array( 'phone', wg_opt( 'phone' ) ? '<a href="tel:' . esc_attr( preg_replace( '/[^\d+]/', '', wg_opt( 'phone' ) ) ) . '">' . esc_html( wg_opt( 'phone' ) ) . '</a>' : '', '' ),
						array( 'pin', wg_opt( 'address' ) ? '<span>' . nl2br( esc_html( wg_opt( 'address' ) ) ) . '</span>' : '', 'Endereço: DEFINIR' ),
						array( 'target', wg_opt( 'area' ) ? '<span>' . esc_html( wg_opt( 'area' ) ) . '</span>' : '', 'Área de atendimento: DEFINIR' ),
					);
					foreach ( $wg_contacts as $wg_c ) {
						if ( $wg_c[1] ) {
							echo '<li>' . wg_icon( $wg_c[0] ) . $wg_c[1] . '</li>'; // phpcs:ignore
						} elseif ( $wg_admin && $wg_c[2] ) {
							echo '<li>' . wg_icon( $wg_c[0] ) . wg_pending( $wg_c[2] ) . '</li>'; // phpcs:ignore
						}
					}
					?>
				</ul>
				<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url(), 'primary', array( 'icon' => 'arrow', 'class' => 'wg-btn--sm' ) ); // phpcs:ignore ?>
			</div>
		</div>

		<div class="wg-footer__bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . wg_company_name() ); ?><?php echo wg_opt( 'cnpj' ) ? ' · CNPJ ' . esc_html( wg_opt( 'cnpj' ) ) : ''; ?></p>
			<?php if ( get_privacy_policy_url() ) : ?>
				<p><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Política de privacidade', 'wg' ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>
</footer>

<a class="wg-float-wa" href="<?php echo esc_url( wg_whatsapp_url( wg_float_message() ) ); ?>" <?php echo wg_whatsapp_number() ? 'target="_blank" rel="noopener"' : ''; ?> data-wa-click="float" aria-label="<?php esc_attr_e( 'Falar no WhatsApp', 'wg' ); ?>">
	<?php echo wg_icon( 'whatsapp' ); // phpcs:ignore ?>
	<span class="wg-float-wa__label"><?php esc_html_e( 'Fale com a equipe', 'wg' ); ?></span>
</a>

<?php wp_footer(); ?>
</body>
</html>
