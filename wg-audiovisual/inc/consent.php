<?php
/**
 * Consentimento de cookies (LGPD) e carregamento do Google Analytics / Google Ads.
 * As ferramentas de medição só são carregadas depois que o visitante aceita.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', function () {
	$ga  = wg_opt( 'ga4_id' );
	$ads = wg_opt( 'gads_id' );
	if ( ! $ga && ! $ads ) {
		return;
	}
	$ids = array_values( array_filter( array( $ga, $ads ) ) );
	?>
<script>
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{ad_storage:'denied',analytics_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',wait_for_update:500});
window.WG_TAGS=<?php echo wp_json_encode( array( 'ids' => $ids, 'ads' => $ads, 'adsLabel' => wg_opt( 'gads_label' ) ) ); ?>;
</script>
	<?php
}, 3 );

add_action( 'wp_footer', function () {
	if ( is_admin() ) {
		return;
	}
	$policy = get_privacy_policy_url();
	?>
<div class="wg-consent" id="wg-consent" role="dialog" aria-live="polite" aria-label="<?php esc_attr_e( 'Aviso de cookies', 'wg' ); ?>" hidden>
	<p><?php esc_html_e( 'Usamos cookies para medir o acesso e melhorar sua experiência. Você pode aceitar ou recusar os cookies de medição.', 'wg' ); ?>
	<?php if ( $policy ) : ?>
		<a href="<?php echo esc_url( $policy ); ?>"><?php esc_html_e( 'Política de privacidade', 'wg' ); ?></a>
	<?php endif; ?></p>
	<div class="wg-consent__actions">
		<button type="button" class="wg-btn wg-btn--ghost wg-btn--sm" data-consent="deny"><?php esc_html_e( 'Recusar', 'wg' ); ?></button>
		<button type="button" class="wg-btn wg-btn--primary wg-btn--sm" data-consent="accept"><?php esc_html_e( 'Aceitar', 'wg' ); ?></button>
	</div>
</div>
	<?php
}, 30 );
