<?php
/**
 * Cabeçalho do site.
 */
defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>document.documentElement.className=document.documentElement.className.replace('no-js','js');</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="wg-skip" href="#conteudo"><?php esc_html_e( 'Pular para o conteúdo', 'wg' ); ?></a>

<header class="wg-header" data-header>
	<div class="wg-container wg-header__inner">
		<a class="wg-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( wg_company_name() . ' — ' . __( 'página inicial', 'wg' ) ); ?>">
			<?php echo wg_logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>

		<nav class="wg-nav" id="wg-nav" aria-label="<?php esc_attr_e( 'Menu principal', 'wg' ); ?>" data-nav>
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'wg-menu',
						'depth'          => 2,
					)
				);
			} else {
				wg_fallback_menu();
			}
			?>
			<div class="wg-nav__mobile-cta">
				<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url(), 'primary', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
				<?php echo wg_button( __( 'Falar no WhatsApp', 'wg' ), wg_whatsapp_url(), 'ghost', array( 'icon' => 'whatsapp', 'wa' => 'menu' ) ); // phpcs:ignore ?>
			</div>
		</nav>

		<div class="wg-header__actions">
			<?php if ( wg_whatsapp_number() ) : ?>
				<a class="wg-header__wa" href="<?php echo esc_url( wg_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-wa-click="header" aria-label="<?php esc_attr_e( 'Falar no WhatsApp', 'wg' ); ?>"><?php echo wg_icon( 'whatsapp' ); // phpcs:ignore ?></a>
			<?php endif; ?>
			<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url(), 'primary', array( 'class' => 'wg-header__cta' ) ); // phpcs:ignore ?>
			<button class="wg-burger" type="button" aria-controls="wg-nav" aria-expanded="false" data-burger>
				<span class="screen-reader-text"><?php esc_html_e( 'Abrir menu', 'wg' ); ?></span>
				<span class="wg-burger__lines" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</header>

<main id="conteudo" class="wg-main" tabindex="-1">
