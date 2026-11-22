<?php
/**
 * WG Audiovisual — bootstrap do tema.
 */

defined( 'ABSPATH' ) || exit;

define( 'WG_VERSION', '1.0.0' );
define( 'WG_DIR', get_template_directory() );
define( 'WG_URI', get_template_directory_uri() );

$wg_includes = array(
	'helpers',
	'content-data',
	'setup',
	'post-types',
	'meta-boxes',
	'options',
	'leads',
	'seo',
	'consent',
	'importer',
);

foreach ( $wg_includes as $wg_file ) {
	require_once WG_DIR . '/inc/' . $wg_file . '.php';
}
