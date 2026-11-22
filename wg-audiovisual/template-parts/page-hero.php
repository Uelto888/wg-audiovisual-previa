<?php
/**
 * Topo das páginas internas.
 *
 * @var array $args eyebrow, title, text, image (URL), buttons (bool), service (string), size (sm|lg)
 */
defined( 'ABSPATH' ) || exit;

$a = wp_parse_args(
	$args,
	array(
		'eyebrow' => '',
		'title'   => get_the_title(),
		'text'    => '',
		'image'   => '',
		'buttons' => true,
		'service' => '',
		'wa'      => '',
		'size'    => 'lg',
	)
);
?>
<section class="wg-page-hero wg-page-hero--<?php echo esc_attr( $a['size'] ); ?><?php echo $a['image'] ? ' has-image' : ''; ?>">
	<?php if ( $a['image'] ) : ?>
		<div class="wg-page-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( $a['image'] ); ?>" alt="" fetchpriority="high" decoding="async">
		</div>
	<?php endif; ?>
	<div class="wg-container wg-page-hero__inner">
		<?php wg_breadcrumbs(); ?>
		<?php if ( $a['eyebrow'] ) : ?>
			<p class="wg-eyebrow"><?php echo esc_html( $a['eyebrow'] ); ?></p>
		<?php endif; ?>
		<h1 class="wg-page-hero__title"><?php echo wp_kses_post( $a['title'] ); ?></h1>
		<?php if ( $a['text'] ) : ?>
			<p class="wg-page-hero__text"><?php echo wp_kses_post( $a['text'] ); ?></p>
		<?php endif; ?>
		<?php if ( $a['buttons'] ) : ?>
			<div class="wg-actions">
				<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url( $a['service'] ), 'primary', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
				<?php echo wg_button( __( 'Falar no WhatsApp', 'wg' ), wg_whatsapp_url( $a['wa'] ), 'ghost', array( 'icon' => 'whatsapp', 'wa' => 'hero' ) ); // phpcs:ignore ?>
			</div>
		<?php endif; ?>
	</div>
</section>
