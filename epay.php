<?php
/**
 * Plugin Name:       EPay for WooCommerce
 * Description:       WooCommerce 易支付网关，支持多种独立支付方式。
 * Author:            EPay Contributors
 * Version:           2.0.0
 * Text Domain:       epay
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * WC requires at least: 5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EPAY_VERSION', '2.0.0' );
define( 'EPAY_FILE', __FILE__ );
define( 'EPAY_PATH', plugin_dir_path( __FILE__ ) );
define( 'EPAY_URL', plugin_dir_url( __FILE__ ) );

/**
 * Declare compatibility with WooCommerce's HPOS order storage.
 */
function epay_declare_woocommerce_compatibility() {
	if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', EPAY_FILE, true );
	}
}
add_action( 'before_woocommerce_init', 'epay_declare_woocommerce_compatibility' );

/**
 * Load the plugin after WooCommerce has loaded.
 */
function epay_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	require_once EPAY_PATH . 'includes/abstract-epay-gateway.php';
	require_once EPAY_PATH . 'includes/class-epay-settings.php';
	require_once EPAY_PATH . 'includes/class-epay-notify-handler.php';
	require_once EPAY_PATH . 'includes/gateways/class-wc-gateway-epay.php';

	EPAY_Settings::instance();
	EPAY_Notify_Handler::instance();

	add_filter( 'woocommerce_payment_gateways', 'epay_register_gateways' );
}
add_action( 'plugins_loaded', 'epay_init', 20 );

/**
 * Register the direct payment methods. There is intentionally no cashier gateway.
 *
 * @param array<int, string> $gateways Gateway class names.
 * @return array<int, string>
 */
function epay_register_gateways( $gateways ) {
	$classes = array(
		'EPAY_Gateway_Wechat',
		'EPAY_Gateway_Alipay',
		'EPAY_Gateway_Bank',
		'EPAY_Gateway_Revolut',
		'EPAY_Gateway_Paypal',
		'EPAY_Gateway_Alipayhk',
		'EPAY_Gateway_Usdt',
		'EPAY_Gateway_Linepay',
		'EPAY_Gateway_Paynow',
		'EPAY_Gateway_Card',
	);

	return array_merge( $gateways, $classes );
}
