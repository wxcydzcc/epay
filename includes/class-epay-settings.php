<?php
/**
 * EPay shared settings page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EPAY_Settings {

	const OPTION_NAME = 'epay_settings';

	/** @var EPAY_Settings|null */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	/** @return array<string, string> */
	public static function get_options() {
		$options = get_option( self::OPTION_NAME, array() );
		return is_array( $options ) ? $options : array();
	}

	public function admin_menu() {
		add_submenu_page(
			'woocommerce',
			__( '易支付设置', 'epay' ),
			__( '易支付设置', 'epay' ),
			'manage_woocommerce',
			'epay-settings',
			array( $this, 'settings_page' )
		);
	}

	public function settings_init() {
		register_setting(
			'epay_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array(),
			)
		);

		add_settings_section(
			'epay_api_section',
			__( '易支付 API 设置', 'epay' ),
			array( $this, 'section_description' ),
			'epay-settings'
		);

		$this->add_field( 'api_url', __( '支付网关地址', 'epay' ), 'url', 'https://pay.example.com', __( '填写易支付站点根地址，插件会自动请求 submit.php。', 'epay' ) );
		$this->add_field( 'merchant_id', __( '商户 ID', 'epay' ), 'text', '', '' );
		$this->add_field( 'merchant_key', __( '商户密钥', 'epay' ), 'password', '', __( '留空保持已保存的密钥不变。', 'epay' ) );
		$this->add_field( 'return_url', __( '自定义支付后返回地址', 'epay' ), 'url', '', __( '可选；留空时返回 WooCommerce 订单完成页。', 'epay' ) );
	}

	private function add_field( $id, $title, $type, $placeholder, $description ) {
		add_settings_field(
			$id,
			$title,
			array( $this, 'render_field' ),
			'epay-settings',
			'epay_api_section',
			array(
				'id'          => $id,
				'type'        => $type,
				'placeholder' => $placeholder,
				'description' => $description,
			)
		);
	}

	public function render_field( $args ) {
		$options = self::get_options();
		$value   = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
		if ( 'password' === $args['type'] ) {
			$value = '';
		}

		printf(
			'<input class="regular-text" type="%1$s" id="%2$s" name="%3$s[%2$s]" value="%4$s" placeholder="%5$s" autocomplete="off">',
			esc_attr( $args['type'] ),
			esc_attr( $args['id'] ),
			esc_attr( self::OPTION_NAME ),
			esc_attr( $value ),
			esc_attr( $args['placeholder'] )
		);

		if ( ! empty( $args['description'] ) ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	public function section_description() {
		echo '<p>' . esc_html__( '这些凭据供所有支付方式共用。请在 WooCommerce → 设置 → 付款中分别启用需要的通道。', 'epay' ) . '</p>';
		echo '<p><strong>' . esc_html__( '异步通知地址：', 'epay' ) . '</strong> <code>' . esc_html( WC()->api_request_url( 'epay_notify' ) ) . '</code></p>';
	}

	/**
	 * @param mixed $input Submitted settings.
	 * @return array<string, string>
	 */
	public function sanitize( $input ) {
		$current = self::get_options();
		$input   = is_array( $input ) ? $input : array();
		$output  = array();

		$output['api_url'] = isset( $input['api_url'] ) ? untrailingslashit( esc_url_raw( trim( $input['api_url'] ), array( 'http', 'https' ) ) ) : '';
		$output['merchant_id'] = isset( $input['merchant_id'] ) ? sanitize_text_field( $input['merchant_id'] ) : '';
		$output['return_url'] = isset( $input['return_url'] ) ? esc_url_raw( trim( $input['return_url'] ), array( 'http', 'https' ) ) : '';

		if ( ! empty( $input['merchant_key'] ) ) {
			$output['merchant_key'] = sanitize_text_field( $input['merchant_key'] );
		} elseif ( ! empty( $current['merchant_key'] ) ) {
			$output['merchant_key'] = $current['merchant_key'];
		}

		return $output;
	}

	public function settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( '易支付设置', 'epay' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'epay_settings_group' );
				do_settings_sections( 'epay-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
