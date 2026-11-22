<?php
/**
 * Página inicial.
 */
defined( 'ABSPATH' ) || exit;

get_header();

$wg_video     = wg_opt( 'hero_video' ) && '0' !== wg_opt( 'hero_video' );
$wg_video_url = wg_opt( 'hero_video_url' );
$wg_stats     = wg_stats();
$wg_stages    = array( 'Planejamento', 'Consultoria técnica', 'Dimensionamento', 'Projeto audiovisual', 'Logística', 'Montagem', 'Configuração', 'Testes', 'Operação técnica', 'Desmontagem' );
?>

<section class="wg-hero" aria-label="<?php esc_attr_e( 'Apresentação', 'wg' ); ?>">
	<div class="wg-hero__media" aria-hidden="true">
		<img class="wg-hero__poster" src="<?php echo esc_url( wg_hero_poster_url() ); ?>" alt="" fetchpriority="high" decoding="async">
		<?php if ( $wg_video ) : ?>
			<video class="wg-hero__video" muted loop playsinline preload="none" data-hero-video
				data-mp4="<?php echo esc_url( $wg_video_url ? $wg_video_url : WG_URI . '/assets/video/hero.mp4' ); ?>"
				<?php echo $wg_video_url ? '' : 'data-webm="' . esc_url( WG_URI . '/assets/video/hero.webm' ) . '"'; ?>></video>
		<?php endif; ?>
	</div>
	<div class="wg-container wg-hero__inner">
		<p class="wg-eyebrow wg-hero__eyebrow"><?php echo esc_html( wg_opt( 'hero_eyebrow' ) ); ?></p>
		<h1 class="wg-hero__title"><?php echo esc_html( wg_opt( 'hero_title' ) ); ?></h1>
		<p class="wg-hero__text"><?php echo esc_html( wg_opt( 'hero_text' ) ); ?></p>
		<div class="wg-actions">
			<?php echo wg_button( __( 'Solicitar orçamento', 'wg' ), wg_quote_url(), 'primary', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
			<?php echo wg_button( __( 'Conhecer nossas soluções', 'wg' ), '#solucoes', 'ghost' ); // phpcs:ignore ?>
		</div>
	</div>
	<div class="wg-hero__ticker" aria-hidden="true">
		<div class="wg-hero__ticker-track">
			<?php
			$wg_ticker = array( 'Convenções', 'Congressos', 'Feiras', 'Lançamentos', 'Premiações', 'Treinamentos', 'Eventos híbridos', 'Shows corporativos' );
			for ( $wg_i = 0; $wg_i < 2; $wg_i++ ) {
				foreach ( $wg_ticker as $wg_t ) {
					echo '<span>' . esc_html( $wg_t ) . '</span>';
				}
			}
			?>
		</div>
	</div>
</section>

<?php if ( $wg_stats ) : ?>
	<section class="wg-stats" aria-label="<?php esc_attr_e( 'Indicadores', 'wg' ); ?>">
		<div class="wg-container">
			<ul class="wg-stats__list">
				<?php foreach ( $wg_stats as $wg_s ) : ?>
					<li class="wg-stat wg-reveal">
						<strong class="wg-stat__value" data-count="<?php echo esc_attr( $wg_s['value'] ); ?>"><?php echo esc_html( $wg_s['value'] ); ?></strong>
						<span class="wg-stat__label"><?php echo esc_html( $wg_s['label'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
	<section class="wg-section wg-section--tight"><div class="wg-container"><div class="wg-admin-hint">
		<?php echo wp_kses_post( sprintf( __( '<strong>Indicadores:</strong> anos de experiência, eventos realizados, clientes atendidos, projetos executados e regiões atendidas aparecem aqui quando os números reais forem preenchidos em <a href="%s">WG › Configurações › Página inicial</a>.', 'wg' ), esc_url( admin_url( 'admin.php?page=wg-settings&tab=home' ) ) ) ); ?>
	</div></div></section>
<?php endif; ?>

<section class="wg-section wg-value">
	<div class="wg-container wg-value__layout">
		<div class="wg-value__text wg-reveal">
			<p class="wg-eyebrow"><?php esc_html_e( 'Soluções audiovisuais integradas', 'wg' ); ?></p>
			<h2 class="wg-value__title"><?php echo esc_html( wg_opt( 'value_title' ) ); ?></h2>
			<p class="wg-lead"><?php echo esc_html( wg_opt( 'value_text' ) ); ?></p>
			<?php echo wg_button( __( 'Conheça a empresa', 'wg' ), wg_page_url( 'sobre' ), 'ghost', array( 'icon' => 'arrow' ) ); // phpcs:ignore ?>
		</div>
		<div class="wg-value__stages wg-reveal">
			<p class="wg-value__stages-title"><?php esc_html_e( 'Participamos de todas as etapas da infraestrutura técnica', 'wg' ); ?></p>
			<ol class="wg-chips">
				<?php foreach ( $wg_stages as $wg_n => $wg_stage ) : ?>
					<li><span><?php echo esc_html( str_pad( (string) ( $wg_n + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><?php echo esc_html( $wg_stage ); ?></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/section', 'services' ); ?>

<section class="wg-section wg-section--tight wg-audiences">
	<div class="wg-container">
		<div class="wg-audiences__layout">
			<?php wg_section_head( __( 'Quem atendemos', 'wg' ), __( 'Parceiro técnico de quem organiza eventos', 'wg' ) ); ?>
			<ul class="wg-tags">
				<?php foreach ( wg_default_audiences() as $wg_aud ) : ?>
					<li class="wg-reveal"><?php echo wg_icon( 'check' ) . esc_html( $wg_aud ); // phpcs:ignore ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

<?php
get_template_part( 'template-parts/section', 'events' );
get_template_part( 'template-parts/section', 'differentials' );
get_template_part( 'template-parts/section', 'portfolio' );
get_template_part( 'template-parts/section', 'process' );
get_template_part( 'template-parts/section', 'clients' );
get_template_part( 'template-parts/section', 'testimonials' );
get_template_part( 'template-parts/section', 'faq' );
get_template_part( 'template-parts/section', 'cta' );

get_footer();
