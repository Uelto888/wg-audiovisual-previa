<?php
/**
 * Logotipos de clientes, parceiros e fornecedores autorizados.
 */
defined( 'ABSPATH' ) || exit;
$q = new WP_Query(
	array(
		'post_type'      => 'wg_cliente',
		'posts_per_page' => 40,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'no_found_rows'  => true,
	)
);
if ( ! $q->have_posts() ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<section class="wg-section wg-section--tight"><div class="wg-container"><div class="wg-admin-hint">' . wp_kses_post( sprintf( __( '<strong>Clientes e parceiros:</strong> esta seção aparece quando houver logotipos cadastrados (somente com autorização). <a href="%s">Cadastrar</a>', 'wg' ), esc_url( admin_url( 'post-new.php?post_type=wg_cliente' ) ) ) ) . '</div></div></section>';
	}
	return;
}
?>
<section class="wg-section wg-section--tight wg-clients">
	<div class="wg-container">
		<?php wg_section_head( __( 'Clientes e parceiros', 'wg' ), __( 'Empresas que confiam na nossa estrutura', 'wg' ), '', 'center', 'h2' ); ?>
		<ul class="wg-logos">
			<?php
			while ( $q->have_posts() ) :
				$q->the_post();
				$url = get_post_meta( get_the_ID(), '_wg_url', true );
				?>
				<li class="wg-reveal">
					<?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener nofollow"><?php endif; ?>
					<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'wg-logo', array( 'alt' => get_the_title(), 'loading' => 'lazy' ) ) : '<span>' . esc_html( get_the_title() ) . '</span>'; // phpcs:ignore ?>
					<?php if ( $url ) : ?></a><?php endif; ?>
				</li>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</ul>
	</div>
</section>
