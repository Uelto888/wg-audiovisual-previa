<?php
/**
 * Página "Tipos de evento".
 */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'eyebrow' => __( 'Tipos de evento', 'wg' ),
		'title'   => __( 'Estrutura audiovisual para cada formato de evento', 'wg' ),
		'text'    => __( 'Convenções, congressos, feiras, lançamentos, premiações, treinamentos e eventos híbridos. Cada formato pede uma solução técnica diferente.', 'wg' ),
		'image'   => wg_stock( 'evento-congressos' ),
	)
);
get_template_part( 'template-parts/section', 'events', array( 'head' => false ) );
get_template_part( 'template-parts/section', 'differentials' );
get_template_part( 'template-parts/section', 'cta' );
get_footer();
