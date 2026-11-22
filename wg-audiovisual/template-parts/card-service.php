<?php
/**
 * Card de solução.
 */
defined( 'ABSPATH' ) || exit;
$wg_icon = get_post_meta( get_the_ID(), '_wg_icon', true );
?>
<article class="wg-card wg-card--service wg-reveal">
	<a class="wg-card__link" href="<?php the_permalink(); ?>">
		<div class="wg-card__media"><?php echo wg_post_image( null, 'wg-card' ); // phpcs:ignore ?></div>
		<div class="wg-card__body">
			<span class="wg-card__icon"><?php echo wg_icon( $wg_icon ? $wg_icon : 'check' ); // phpcs:ignore ?></span>
			<h3 class="wg-card__title"><?php the_title(); ?></h3>
			<p class="wg-card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<span class="wg-card__more"><?php esc_html_e( 'Conhecer a solução', 'wg' ); ?><?php echo wg_icon( 'arrow' ); // phpcs:ignore ?></span>
		</div>
	</a>
</article>
