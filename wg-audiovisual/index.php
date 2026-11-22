<?php
/**
 * Lista padrão (blog, busca e arquivos sem template próprio).
 */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'title'   => is_search() ? sprintf( __( 'Resultados para “%s”', 'wg' ), get_search_query() ) : ( is_home() ? __( 'Conteúdos', 'wg' ) : wp_strip_all_tags( get_the_archive_title() ) ),
		'buttons' => false,
		'size'    => 'sm',
	)
);
?>
<section class="wg-section">
	<div class="wg-container">
		<?php if ( have_posts() ) : ?>
			<div class="wg-grid wg-grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="wg-card wg-reveal">
						<a class="wg-card__link" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="wg-card__media"><?php the_post_thumbnail( 'wg-card', array( 'loading' => 'lazy' ) ); ?></div>
							<?php endif; ?>
							<div class="wg-card__body">
								<p class="wg-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
								<h2 class="wg-card__title"><?php the_title(); ?></h2>
								<p class="wg-card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
							</div>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="wg-pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Nenhum conteúdo encontrado.', 'wg' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
