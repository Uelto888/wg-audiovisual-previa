<?php
/**
 * Post individual (blog).
 */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow' => get_the_date(),
			'image'   => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'wg-wide' ) : '',
			'buttons' => false,
			'size'    => 'sm',
		)
	);
	?>
	<section class="wg-section"><div class="wg-container wg-narrow wg-prose"><?php the_content(); ?></div></section>
	<?php
endwhile;
get_template_part( 'template-parts/section', 'cta' );
get_footer();
