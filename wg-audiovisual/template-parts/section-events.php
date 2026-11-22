<?php
/**
 * Grade de tipos de evento.
 */
defined( 'ABSPATH' ) || exit;
$a = wp_parse_args( $args, array( 'head' => true ) );
$q = new WP_Query(
	array(
		'post_type'      => 'wg_evento',
		'posts_per_page' => 24,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);
if ( ! $q->have_posts() ) {
	return;
}
?>
<section class="wg-section wg-events" id="eventos">
	<div class="wg-container">
		<?php
		if ( $a['head'] ) {
			wg_section_head( __( 'Soluções para cada tipo de evento', 'wg' ), __( 'Do treinamento para 30 pessoas à convenção para milhares', 'wg' ), __( 'Cada formato tem necessidades técnicas próprias. Conheça como atuamos em cada um deles.', 'wg' ) );
		}
		?>
		<div class="wg-grid wg-grid--4 wg-grid--events">
			<?php
			while ( $q->have_posts() ) {
				$q->the_post();
				get_template_part( 'template-parts/card', 'event' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
