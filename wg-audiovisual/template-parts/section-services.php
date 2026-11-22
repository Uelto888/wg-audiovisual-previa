<?php
/**
 * Grade de soluções.
 */
defined( 'ABSPATH' ) || exit;
$a = wp_parse_args(
	$args,
	array(
		'exclude' => 0,
		'limit'   => -1,
		'head'    => true,
		'eyebrow' => __( 'Nossas soluções', 'wg' ),
		'title'   => __( 'Tecnologia, estrutura e equipe técnica em um único projeto', 'wg' ),
		'text'    => __( 'Combine as soluções de que o seu evento precisa. Nós dimensionamos, montamos e operamos tudo de forma integrada.', 'wg' ),
		'id'      => 'solucoes',
	)
);
$q = new WP_Query(
	array(
		'post_type'      => 'wg_servico',
		'posts_per_page' => $a['limit'],
		'post__not_in'   => $a['exclude'] ? array( $a['exclude'] ) : array(),
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);
if ( ! $q->have_posts() ) {
	return;
}
?>
<section class="wg-section wg-services" id="<?php echo esc_attr( $a['id'] ); ?>">
	<div class="wg-container">
		<?php
		if ( $a['head'] ) {
			wg_section_head( $a['eyebrow'], $a['title'], $a['text'] );
		}
		?>
		<div class="wg-grid wg-grid--3">
			<?php
			while ( $q->have_posts() ) {
				$q->the_post();
				get_template_part( 'template-parts/card', 'service' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
