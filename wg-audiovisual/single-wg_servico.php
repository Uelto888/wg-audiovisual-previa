<?php
/**
 * Página individual de uma solução.
 */
defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$wg_id       = get_the_ID();
	$wg_title    = get_the_title();
	$wg_map      = wg_service_form_map();
	$wg_slug     = get_post_field( 'post_name', $wg_id );
	$wg_form_opt = isset( $wg_map[ $wg_slug ] ) ? $wg_map[ $wg_slug ] : $wg_title;
	$wg_msg      = wg_whatsapp_message_for( mb_strtolower( $wg_title ) );
	$wg_image    = has_post_thumbnail() ? get_the_post_thumbnail_url( $wg_id, 'wg-wide' ) : wg_stock( get_post_meta( $wg_id, '_wg_stock', true ) );
	$wg_items    = wg_lines( get_post_meta( $wg_id, '_wg_items', true ) );
	$wg_benefits = wg_pairs( get_post_meta( $wg_id, '_wg_benefits', true ) );
	$wg_icon     = get_post_meta( $wg_id, '_wg_icon', true );

	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow' => $wg_title,
			'title'   => get_post_meta( $wg_id, '_wg_headline', true ) ? get_post_meta( $wg_id, '_wg_headline', true ) : $wg_title,
			'text'    => get_the_excerpt(),
			'image'   => $wg_image,
			'service' => $wg_form_opt,
			'wa'      => $wg_msg,
		)
	);
	?>

	<?php if ( $wg_benefits ) : ?>
		<section class="wg-section wg-section--tight wg-benefits">
			<div class="wg-container">
				<ul class="wg-grid wg-grid--3">
					<?php foreach ( $wg_benefits as $wg_b ) : ?>
						<li class="wg-benefit wg-reveal">
							<span class="wg-benefit__icon"><?php echo wg_icon( $wg_icon ? $wg_icon : 'check' ); // phpcs:ignore ?></span>
							<h2 class="wg-benefit__title"><?php echo esc_html( $wg_b['title'] ); ?></h2>
							<p><?php echo esc_html( $wg_b['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

	<section class="wg-section">
		<div class="wg-container wg-article-layout">
			<div class="wg-prose wg-reveal">
				<?php the_content(); ?>
			</div>
			<aside class="wg-aside">
				<div class="wg-aside__card">
					<?php if ( $wg_items ) : ?>
						<h2 class="wg-aside__title"><?php esc_html_e( 'O que pode fazer parte', 'wg' ); ?></h2>
						<ul class="wg-checklist">
							<?php foreach ( $wg_items as $wg_it ) : ?>
								<li><?php echo wg_icon( 'check' ) . esc_html( $wg_it ); // phpcs:ignore ?></li>
							<?php endforeach; ?>
						</ul>
						<p class="wg-aside__note"><?php esc_html_e( 'Os itens são indicados conforme o projeto de cada evento.', 'wg' ); ?></p>
					<?php endif; ?>
					<div class="wg-aside__actions">
						<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url( $wg_form_opt ), 'primary', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
						<?php echo wg_button( __( 'Falar no WhatsApp', 'wg' ), wg_whatsapp_url( $wg_msg ), 'ghost', array( 'icon' => 'whatsapp', 'wa' => 'service' ) ); // phpcs:ignore ?>
					</div>
				</div>
			</aside>
		</div>
	</section>

	<?php
	// Tipos de evento que usam esta solução.
	$wg_events = get_posts(
		array(
			'post_type'      => 'wg_evento',
			'posts_per_page' => 4,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_wg_services',
					'value'   => 'i:' . $wg_id . ';',
					'compare' => 'LIKE',
				),
			),
		)
	);
	if ( $wg_events ) :
		global $post;
		?>
		<section class="wg-section wg-section--tight">
			<div class="wg-container">
				<?php wg_section_head( __( 'Aplicações', 'wg' ), sprintf( __( '%s em diferentes formatos de evento', 'wg' ), $wg_title ) ); ?>
				<div class="wg-grid wg-grid--4">
					<?php
					foreach ( $wg_events as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						setup_postdata( $post );
						get_template_part( 'template-parts/card', 'event' );
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	get_template_part(
		'template-parts/section',
		'services',
		array(
			'exclude' => $wg_id,
			'limit'   => 3,
			'eyebrow' => __( 'Soluções integradas', 'wg' ),
			'title'   => __( 'Combine com outras soluções', 'wg' ),
			'text'    => '',
			'id'      => 'outras-solucoes',
		)
	);
	get_template_part(
		'template-parts/section',
		'cta',
		array(
			'title'   => sprintf( __( 'Vamos planejar %s para o seu evento?', 'wg' ), mb_strtolower( $wg_title ) ),
			'service' => $wg_form_opt,
			'wa'      => $wg_msg,
		)
	);
endwhile;

get_footer();
