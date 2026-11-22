<?php
/**
 * Páginas comuns (ex.: política de privacidade).
 */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'title'   => get_the_title(),
			'text'    => has_excerpt() ? get_the_excerpt() : '',
			'image'   => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'wg-wide' ) : '',
			'buttons' => false,
			'size'    => 'sm',
		)
	);
	?>
	<section class="wg-section">
		<div class="wg-container wg-narrow wg-prose"><?php the_content(); ?></div>
	</section>
	<?php
endwhile;
get_footer();
