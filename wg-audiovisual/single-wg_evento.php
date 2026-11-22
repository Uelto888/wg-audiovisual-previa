<?php
/**
 * Página de um tipo de evento.
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$wg_id    = get_the_ID();
	$wg_title = get_the_title();
	$wg_msg   = sprintf( __( 'Olá! Estou organizando um evento do tipo %s e gostaria de um orçamento de infraestrutura audiovisual.', 'wg' ), mb_strtolower( $wg_title ) );
	$wg_image = has_post_thumbnail() ? get_the_post_thumbnail_url( $wg_id, 'wg-wide' ) : wg_stock( get_post_meta( $wg_id, '_wg_stock', true ) );

	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow' => __( 'Tipos de evento', 'wg' ),
			'title'   => sprintf( __( 'Estrutura audiovisual para %s', 'wg' ), mb_strtolower( $wg_title ) ),
			'text'    => get_the_excerpt(),
			'image'   => $wg_image,
			'wa'      => $wg_msg,
		)
	);
	?>
	<section class="wg-section">
		<div class="wg-container wg-narrow wg-prose wg-reveal">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
	$wg_rel = array_filter( array_map( 'intval', (array) get_post_meta( $wg_id, '_wg_services', true ) ) );
	if ( $wg_rel ) :
		$wg_q = new WP_Query( array( 'post_type' => 'wg_servico', 'post__in' => $wg_rel, 'orderby' => 'post__in', 'posts_per_page' => 8, 'no_found_rows' => true ) );
		?>
		<section class="wg-section wg-section--tight">
			<div class="wg-container">
				<?php wg_section_head( __( 'Soluções recomendadas', 'wg' ), sprintf( __( 'O que costuma fazer parte de %s', 'wg' ), mb_strtolower( $wg_title ) ) ); ?>
				<div class="wg-grid wg-grid--4">
					<?php
					while ( $wg_q->have_posts() ) {
						$wg_q->the_post();
						get_template_part( 'template-parts/card', 'service' );
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	get_template_part( 'template-parts/section', 'process' );
	get_template_part( 'template-parts/section', 'cta', array( 'wa' => $wg_msg ) );
endwhile;
get_footer();
