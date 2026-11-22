<?php
/**
 * Template Name: Sobre a empresa
 */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'eyebrow' => __( 'Sobre a empresa', 'wg' ),
		'title'   => wg_opt( 'value_title' ),
		'text'    => wg_opt( 'value_text' ),
		'image'   => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'wg-wide' ) : wg_stock( 'bastidores-montagem' ),
	)
);
?>
<section class="wg-section">
	<div class="wg-container wg-about-layout">
		<div class="wg-prose wg-reveal">
			<?php
			while ( have_posts() ) {
				the_post();
				the_content();
			}
			?>
		</div>
		<figure class="wg-about-media wg-reveal">
			<img src="<?php echo esc_url( wg_stock( 'sobre-operacao-audio' ) ); ?>" alt="<?php esc_attr_e( 'Técnico ajustando mesa de som digital', 'wg' ); ?>" loading="lazy" decoding="async" width="1600" height="1067">
		</figure>
	</div>
</section>
<?php
if ( wg_stats() ) {
	echo '<section class="wg-stats wg-stats--inline"><div class="wg-container"><ul class="wg-stats__list">';
	foreach ( wg_stats() as $wg_s ) {
		echo '<li class="wg-stat wg-reveal"><strong class="wg-stat__value" data-count="' . esc_attr( $wg_s['value'] ) . '">' . esc_html( $wg_s['value'] ) . '</strong><span class="wg-stat__label">' . esc_html( $wg_s['label'] ) . '</span></li>';
	}
	echo '</ul></div></section>';
}
get_template_part( 'template-parts/section', 'differentials' );
get_template_part( 'template-parts/section', 'process' );
get_template_part( 'template-parts/section', 'clients' );
get_template_part( 'template-parts/section', 'cta' );
get_footer();
