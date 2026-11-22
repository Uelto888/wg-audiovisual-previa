<?php
/**
 * Card de projeto do portfólio.
 */
defined( 'ABSPATH' ) || exit;
$wg_terms = get_the_terms( get_the_ID(), 'wg_segmento' );
$wg_slugs = $wg_terms && ! is_wp_error( $wg_terms ) ? wp_list_pluck( $wg_terms, 'slug' ) : array();
$wg_names = $wg_terms && ! is_wp_error( $wg_terms ) ? wp_list_pluck( $wg_terms, 'name' ) : array();
$wg_loc   = get_post_meta( get_the_ID(), '_wg_location', true );
?>
<article class="wg-card wg-card--project wg-reveal" data-seg="<?php echo esc_attr( implode( ' ', $wg_slugs ) ); ?>">
	<a class="wg-card__link" href="<?php the_permalink(); ?>">
		<div class="wg-card__media"><?php echo wg_post_image( null, 'wg-card' ); // phpcs:ignore ?></div>
		<div class="wg-card__body">
			<?php if ( $wg_names ) : ?>
				<p class="wg-card__meta"><?php echo esc_html( implode( ' · ', $wg_names ) ); ?></p>
			<?php endif; ?>
			<h3 class="wg-card__title"><?php the_title(); ?></h3>
			<?php if ( $wg_loc ) : ?>
				<p class="wg-card__text"><?php echo wg_icon( 'pin' ) . esc_html( $wg_loc ); // phpcs:ignore ?></p>
			<?php endif; ?>
		</div>
	</a>
</article>
