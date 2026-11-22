<?php
/**
 * Portfólio (lista de projetos e filtro por segmento).
 */
defined( 'ABSPATH' ) || exit;
get_header();

$wg_term = is_tax( 'wg_segmento' ) ? get_queried_object() : null;
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'eyebrow' => __( 'Portfólio', 'wg' ),
		'title'   => $wg_term ? sprintf( __( 'Projetos: %s', 'wg' ), $wg_term->name ) : __( 'Eventos que ganharam vida com a nossa estrutura', 'wg' ),
		'text'    => __( 'Conheça projetos audiovisuais realizados para eventos corporativos.', 'wg' ),
		'image'   => wg_stock( 'publico' ),
		'buttons' => false,
		'size'    => 'sm',
	)
);
$wg_terms = get_terms( array( 'taxonomy' => 'wg_segmento', 'hide_empty' => true ) );
?>
<section class="wg-section">
	<div class="wg-container">
		<?php if ( have_posts() ) : ?>
			<?php if ( $wg_terms && ! is_wp_error( $wg_terms ) ) : ?>
				<nav class="wg-filters" aria-label="<?php esc_attr_e( 'Filtrar por segmento', 'wg' ); ?>">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'wg_projeto' ) ); ?>" class="<?php echo $wg_term ? '' : 'is-active'; ?>"><?php esc_html_e( 'Todos', 'wg' ); ?></a>
					<?php foreach ( $wg_terms as $wg_t ) : ?>
						<a href="<?php echo esc_url( get_term_link( $wg_t ) ); ?>" class="<?php echo $wg_term && $wg_term->term_id === $wg_t->term_id ? 'is-active' : ''; ?>"><?php echo esc_html( $wg_t->name ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
			<div class="wg-grid wg-grid--3">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/card', 'project' );
				}
				?>
			</div>
			<div class="wg-pagination"><?php the_posts_pagination( array( 'prev_text' => __( 'Anteriores', 'wg' ), 'next_text' => __( 'Próximos', 'wg' ) ) ); ?></div>
		<?php else : ?>
			<div class="wg-empty wg-reveal">
				<span class="wg-empty__icon"><?php echo wg_icon( 'camera' ); // phpcs:ignore ?></span>
				<h2><?php esc_html_e( 'Portfólio em atualização', 'wg' ); ?></h2>
				<p><?php esc_html_e( 'Estamos organizando os registros dos nossos projetos mais recentes. Peça cases e referências diretamente à nossa equipe.', 'wg' ); ?></p>
				<div class="wg-actions wg-actions--center">
					<?php echo wg_button( __( 'Pedir referências no WhatsApp', 'wg' ), wg_whatsapp_url( __( 'Olá! Gostaria de conhecer cases e referências de eventos realizados por vocês.', 'wg' ) ), 'primary', array( 'icon' => 'whatsapp', 'wa' => 'portfolio-empty' ) ); // phpcs:ignore ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/section', 'cta' );
get_footer();
