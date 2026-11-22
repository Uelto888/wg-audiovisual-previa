<?php
/**
 * Card de tipo de evento. Sem página própria, leva ao orçamento com o tipo já selecionado.
 */
defined( 'ABSPATH' ) || exit;
$wg_has = wg_event_has_page( get_the_ID() );
$wg_url = $wg_has ? get_permalink() : add_query_arg( 'evento', rawurlencode( get_the_title() ), wg_quote_url() );
?>
<article class="wg-card wg-card--event wg-reveal">
	<a class="wg-card__link" href="<?php echo esc_url( $wg_url ); ?>">
		<div class="wg-card__media"><?php echo wg_post_image( null, 'wg-card' ); // phpcs:ignore ?></div>
		<div class="wg-card__overlay">
			<h3 class="wg-card__title"><?php the_title(); ?></h3>
			<p class="wg-card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<span class="wg-card__more"><?php echo $wg_has ? esc_html__( 'Ver soluções', 'wg' ) : esc_html__( 'Pedir orçamento', 'wg' ); ?><?php echo wg_icon( 'arrow' ); // phpcs:ignore ?></span>
		</div>
	</a>
</article>
