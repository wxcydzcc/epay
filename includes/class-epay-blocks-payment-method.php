<?php
/**
 * Cart and Checkout Blocks integration for EPay gateways.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class EPAY_Blocks_Payment_Method extends AbstractPaymentMethodType {

	/**
	 * @param string $gateway_id WooCommerce payment gateway ID.
	 */
	public function __construct( $gateway_id ) {
		$this->name = $gateway_id;
	}

	/**
	 * Load the settings saved by the matching WC_Payment_Gateway instance.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_' . $this->name . '_settings', array() );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return 'yes' === $this->get_setting( 'enabled', 'no' );
	}

	/**
	 * @return array<int, string>
	 */
	public function get_payment_method_script_handles() {
		$handle = 'epay-checkout-blocks';

		wp_register_script(
			$handle,
			EPAY_URL . 'assets/js/checkout-blocks.js',
			array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities' ),
			EPAY_VERSION,
			true
		);

		return array( $handle );
	}

	/**
	 * Load the same registration script in the Checkout block editor.
	 *
	 * @return array<int, string>
	 */
	public function get_payment_method_script_handles_for_admin() {
		return $this->get_payment_method_script_handles();
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_payment_method_data() {
		$gateway = $this->get_gateway();
		$title = $gateway ? $gateway->get_title() : $this->get_setting( 'title', $this->name );
		$description = $gateway ? $gateway->get_description() : $this->get_setting( 'description', '' );

		return array(
			'title'       => $title,
			'description' => $description,
			'icon'        => $gateway ? $gateway->icon : '',
			'supports'    => $gateway ? array_values( $gateway->supports ) : array( 'products' ),
		);
	}

	/**
	 * @return WC_Payment_Gateway|null
	 */
	private function get_gateway() {
		if ( ! function_exists( 'WC' ) || ! WC()->payment_gateways() ) {
			return null;
		}

		$gateways = WC()->payment_gateways()->payment_gateways();
		return isset( $gateways[ $this->name ] ) ? $gateways[ $this->name ] : null;
	}
}
