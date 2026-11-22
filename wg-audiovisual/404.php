<?php
/**
 * Página não encontrada.
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="wg-page-hero wg-page-hero--sm wg-404">
	<div class="wg-container wg-page-hero__inner">
		<p class="wg-eyebrow">404</p>
		<h1 class="wg-page-hero__title"><?php esc_html_e( 'Esta página saiu de cena', 'wg' ); ?></h1>
		<p class="wg-page-hero__text"><?php esc_html_e( 'O endereço pode ter mudado. Veja nossas soluções ou fale com a equipe.', 'wg' ); ?></p>
		<div class="wg-actions">
			<?php echo wg_button( __( 'Ver soluções', 'wg' ), get_post_type_archive_link( 'wg_servico' ), 'primary', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
			<?php echo wg_button( __( 'Página inicial', 'wg' ), home_url( '/' ), 'ghost' ); // phpcs:ignore ?>
		</div>
	</div>
</section>
<?php
get_footer();
