<?php
/**
 * Portfólio na página inicial, com filtros por segmento.
 */
defined( 'ABSPATH' ) || exit;

// Na página inicial, o portfólio só aparece com pelo menos 3 projetos publicados.
$wg_total = (int) wp_count_posts( 'wg_projeto' )->publish;
if ( $wg_total < 3 ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<section class="wg-section"><div class="wg-container"><div class="wg-admin-hint">' . wp_kses_post( sprintf( __( '<strong>Portfólio:</strong> esta seção aparece na página inicial quando houver pelo menos 3 projetos publicados (hoje: %1$d). <a href="%2$s">Ver projetos</a> — use apenas fotos reais e publique nomes de clientes só com autorização.', 'wg' ), $wg_total, esc_url( admin_url( 'edit.php?post_type=wg_projeto' ) ) ) ) . '</div></div></section>';
	}
	return;
}

$q = new WP_Query(
	array(
		'post_type'      => 'wg_projeto',
		'posts_per_page' => 6,
		'meta_key'       => '_wg_featured',
		'orderby'        => array( 'meta_value' => 'DESC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);
if ( ! $q->have_posts() ) {
	// Sem consulta por meta, para incluir projetos não destacados.
	$q = new WP_Query( array( 'post_type' => 'wg_projeto', 'posts_per_page' => 6, 'no_found_rows' => true ) );
}
if ( ! $q->have_posts() ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<section class="wg-section"><div class="wg-container"><div class="wg-admin-hint">' . wp_kses_post( sprintf( __( '<strong>Portfólio:</strong> esta seção aparece automaticamente quando houver projetos publicados. <a href="%s">Cadastrar projeto</a> (use apenas fotos reais).', 'wg' ), esc_url( admin_url( 'post-new.php?post_type=wg_projeto' ) ) ) ) . '</div></div></section>';
	}
	return;
}
$terms = get_terms( array( 'taxonomy' => 'wg_segmento', 'hide_empty' => true ) );
?>
<section class="wg-section wg-portfolio" id="portfolio">
	<div class="wg-container">
		<div class="wg-split-head">
			<?php wg_section_head( __( 'Portfólio', 'wg' ), __( 'Eventos que ganharam vida com a nossa estrutura', 'wg' ) ); ?>
			<?php echo wg_button( __( 'Ver portfólio completo', 'wg' ), get_post_type_archive_link( 'wg_projeto' ), 'ghost', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
		</div>
		<?php if ( $terms && ! is_wp_error( $terms ) && count( $terms ) > 1 ) : ?>
			<div class="wg-filters" role="group" aria-label="<?php esc_attr_e( 'Filtrar por segmento', 'wg' ); ?>" data-filter-group>
				<button type="button" class="is-active" data-filter="*"><?php esc_html_e( 'Todos', 'wg' ); ?></button>
				<?php foreach ( $terms as $t ) : ?>
					<button type="button" data-filter="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="wg-grid wg-grid--3" data-filter-items>
			<?php
			while ( $q->have_posts() ) {
				$q->the_post();
				get_template_part( 'template-parts/card', 'project' );
			}
			wp_reset_postdata();
			?>
		</div>
		<div class="wg-inline-cta wg-reveal">
			<p><?php esc_html_e( 'Quer uma estrutura assim no seu próximo evento?', 'wg' ); ?></p>
			<div class="wg-actions">
				<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url(), 'primary', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
				<?php echo wg_button( __( 'WhatsApp', 'wg' ), wg_whatsapp_url(), 'ghost', array( 'icon' => 'whatsapp', 'wa' => 'portfolio' ) ); // phpcs:ignore ?>
			</div>
		</div>
	</div>
</section>
