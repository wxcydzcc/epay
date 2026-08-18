<?php
/**
 * Signed EPay callback handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EPAY_Notify_Handler {

	/** @var EPAY_Notify_Handler|null */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'woocommerce_api_epay_notify', array( $this, 'handle' ) );
	}

	/**
	 * Generate the EPay MD5 signature.
	 *
	 * @param array<string, mixed> $params Parameters to sign.
	 * @param string               $key Merchant key.
	 * @return string
	 */
	public static function sign( $params, $key ) {
		$filtered = array();
		foreach ( $params as $name => $value ) {
			if ( 'sign' === $name || 'sign_type' === $name || '' === (string) $value ) {
				continue;
			}
			$filtered[ $name ] = (string) $value;
		}

		ksort( $filtered );
		$pairs = array();
		foreach ( $filtered as $name => $value ) {
			$pairs[] = $name . '=' . $value;
		}

		return md5( implode( '&', $pairs ) . $key );
	}

	public function handle() {
		$params  = $this->request_params();
		$options = EPAY_Settings::get_options();
		$key     = isset( $options['merchant_key'] ) ? (string) $options['merchant_key'] : '';
		$sign    = isset( $params['sign'] ) ? strtolower( sanitize_text_field( $params['sign'] ) ) : '';

		if ( '' === $key || '' === $sign || ! hash_equals( self::sign( $params, $key ), $sign ) ) {
			$this->fail( 'Invalid signature.' );
		}

		if ( ! empty( $options['merchant_id'] ) && (string) $options['merchant_id'] !== (string) ( $params['pid'] ?? '' ) ) {
			$this->fail( 'Merchant ID mismatch.' );
		}

		if ( ! isset( $params['trade_status'] ) || 'TRADE_SUCCESS' !== strtoupper( (string) $params['trade_status'] ) ) {
			$this->fail( 'Payment is not successful.' );
		}

		$order_id = isset( $params['out_trade_no'] ) ? absint( $params['out_trade_no'] ) : 0;
		$order    = wc_get_order( $order_id );
		if ( ! $order ) {
			$this->fail( 'Order not found.' );
		}

		$payment_method = (string) $order->get_payment_method();
		$notified_type  = isset( $params['type'] ) ? sanitize_key( $params['type'] ) : '';
		$expected_type  = 0 === strpos( $payment_method, 'epay_' ) ? substr( $payment_method, 5 ) : '';
		if ( '' === $expected_type || ! hash_equals( $expected_type, $notified_type ) ) {
			$this->fail( 'Order payment method mismatch.' );
		}

		$received = isset( $params['money'] ) ? wc_format_decimal( $params['money'], wc_get_price_decimals() ) : '';
		$expected = wc_format_decimal( $order->get_total(), wc_get_price_decimals() );
		if ( '' === $received || ! hash_equals( $expected, $received ) ) {
			$order->add_order_note( sprintf( __( '易支付回调金额不匹配；订单金额 %1$s，回调金额 %2$s。', 'epay' ), $expected, $received ) );
			$this->fail( 'Payment amount mismatch.' );
		}

		if ( $order->is_paid() ) {
			$this->success();
		}

		$transaction_id = isset( $params['trade_no'] ) ? sanitize_text_field( $params['trade_no'] ) : '';
		$order->payment_complete( $transaction_id );
		$order->add_order_note( sprintf( __( '易支付付款成功。交易号：%s', 'epay' ), $transaction_id ?: __( '未提供', 'epay' ) ) );

		$this->success();
	}

	/** @return array<string, mixed> */
	private function request_params() {
		// EPay implementations may notify with either query parameters or form data.
		$params = array_merge( wp_unslash( $_GET ), wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$output = array();
		foreach ( $params as $name => $value ) {
			if ( is_scalar( $value ) ) {
				// Preserve the exact signed value. Individual fields are sanitized before use.
				$output[ sanitize_key( $name ) ] = (string) $value;
			}
		}
		return $output;
	}

	private function success() {
		status_header( 200 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo 'success';
		exit;
	}

	private function fail( $message ) {
		if ( function_exists( 'wc_get_logger' ) ) {
			wc_get_logger()->warning( $message, array( 'source' => 'epay-callback' ) );
		}
		status_header( 400 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo 'fail';
		exit;
	}
}
