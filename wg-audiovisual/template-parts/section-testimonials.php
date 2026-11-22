<?php
/**
 * Depoimentos reais de clientes.
 */
defined( 'ABSPATH' ) || exit;
$q = new WP_Query(
	array(
		'post_type'      => 'wg_depoimento',
		'posts_per_page' => 6,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);
if ( ! $q->have_posts() ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<section class="wg-section wg-section--tight"><div class="wg-container"><div class="wg-admin-hint">' . wp_kses_post( sprintf( __( '<strong>Depoimentos:</strong> esta seção aparece quando houver depoimentos reais cadastrados. <a href="%s">Cadastrar depoimento</a>', 'wg' ), esc_url( admin_url( 'post-new.php?post_type=wg_depoimento' ) ) ) ) . '</div></div></section>';
	}
	return;
}
?>
<section class="wg-section wg-testimonials">
	<div class="wg-container">
		<?php wg_section_head( __( 'Depoimentos', 'wg' ), __( 'O que dizem os nossos clientes', 'wg' ) ); ?>
		<div class="wg-grid wg-grid--3">
			<?php
			while ( $q->have_posts() ) :
				$q->the_post();
				$role    = get_post_meta( get_the_ID(), '_wg_role', true );
				$company = get_post_meta( get_the_ID(), '_wg_company', true );
				?>
				<figure class="wg-quote wg-reveal">
					<?php echo wg_icon( 'quote', 'wg-quote__mark' ); // phpcs:ignore ?>
					<blockquote><?php echo wp_kses_post( wpautop( get_the_content() ) ); ?></blockquote>
					<figcaption>
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'wg-quote__avatar', 'loading' => 'lazy' ) ); ?>
						<?php endif; ?>
						<span><strong><?php the_title(); ?></strong><?php echo esc_html( trim( ( $role ? ', ' . $role : '' ) . ( $company ? ' — ' . $company : '' ) ) ); ?></span>
					</figcaption>
				</figure>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
