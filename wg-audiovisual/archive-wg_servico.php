<?php
/**
 * Página "Soluções".
 */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'eyebrow' => __( 'Soluções', 'wg' ),
		'title'   => __( 'Soluções audiovisuais completas para eventos corporativos', 'wg' ),
		'text'    => __( 'Som, luz, vídeo, estrutura e transmissão planejados em conjunto, com uma única equipe responsável do projeto à desmontagem.', 'wg' ),
		'image'   => wg_stock( 'equipe-tecnica' ),
	)
);
get_template_part( 'template-parts/section', 'services', array( 'head' => false ) );
get_template_part( 'template-parts/section', 'process' );
get_template_part( 'template-parts/section', 'cta' );
get_footer();
