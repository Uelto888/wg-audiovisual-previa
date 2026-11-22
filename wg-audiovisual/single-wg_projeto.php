<?php
/**
 * Página de um projeto (case).
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$wg_id      = get_the_ID();
	$wg_terms   = get_the_terms( $wg_id, 'wg_segmento' );
	$wg_seg     = $wg_terms && ! is_wp_error( $wg_terms ) ? implode( ', ', wp_list_pluck( $wg_terms, 'name' ) ) : '';
	$wg_srv     = array_filter( array_map( 'intval', (array) get_post_meta( $wg_id, '_wg_services', true ) ) );
	$wg_gallery = array_filter( array_map( 'intval', explode( ',', (string) get_post_meta( $wg_id, '_wg_gallery', true ) ) ) );
	$wg_facts   = array_filter(
		array(
			__( 'Cliente', 'wg' )  => get_post_meta( $wg_id, '_wg_client', true ),
			__( 'Segmento', 'wg' ) => $wg_seg,
			__( 'Local', 'wg' )    => get_post_meta( $wg_id, '_wg_location', true ),
			__( 'Data', 'wg' )     => get_post_meta( $wg_id, '_wg_date', true ),
			__( 'Público', 'wg' )  => get_post_meta( $wg_id, '_wg_audience', true ),
		)
	);

	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow' => __( 'Portfólio', 'wg' ),
			'text'    => has_excerpt() ? get_the_excerpt() : '',
			'image'   => has_post_thumbnail() ? get_the_post_thumbnail_url( $wg_id, 'wg-wide' ) : '',
			'buttons' => false,
		)
	);
	?>
	<section class="wg-section">
		<div class="wg-container wg-article-layout">
			<div class="wg-prose wg-reveal"><?php the_content(); ?></div>
			<aside class="wg-aside">
				<div class="wg-aside__card">
					<h2 class="wg-aside__title"><?php esc_html_e( 'Ficha do projeto', 'wg' ); ?></h2>
					<?php if ( $wg_facts ) : ?>
						<dl class="wg-facts">
							<?php foreach ( $wg_facts as $wg_k => $wg_v ) : ?>
								<dt><?php echo esc_html( $wg_k ); ?></dt><dd><?php echo esc_html( $wg_v ); ?></dd>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
					<?php if ( $wg_srv ) : ?>
						<h3 class="wg-aside__subtitle"><?php esc_html_e( 'Serviços executados', 'wg' ); ?></h3>
						<ul class="wg-checklist">
							<?php foreach ( $wg_srv as $wg_s ) : ?>
								<li><?php echo wg_icon( 'check' ); // phpcs:ignore ?><a href="<?php echo esc_url( get_permalink( $wg_s ) ); ?>"><?php echo esc_html( get_the_title( $wg_s ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</aside>
		</div>
	</section>

	<?php if ( $wg_gallery ) : ?>
		<section class="wg-section wg-section--tight">
			<div class="wg-container">
				<div class="wg-gallery" data-gallery>
					<?php foreach ( $wg_gallery as $wg_img ) : ?>
						<a class="wg-gallery__item wg-reveal" href="<?php echo esc_url( wp_get_attachment_image_url( $wg_img, 'full' ) ); ?>">
							<?php echo wp_get_attachment_image( $wg_img, 'wg-card', false, array( 'loading' => 'lazy' ) ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	get_template_part(
		'template-parts/section',
		'cta',
		array(
			'title' => __( 'Quer um resultado assim no seu evento?', 'wg' ),
			'text'  => __( 'Conte o que você está planejando e receba uma proposta sob medida.', 'wg' ),
			'wa'    => sprintf( __( 'Olá! Vi o projeto "%s" no site e gostaria de um orçamento para um evento parecido.', 'wg' ), get_the_title() ),
		)
	);
endwhile;
get_footer();
