<?php
/**
 * Direct EPay payment methods.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EPAY_Gateway_Wechat extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'wxpay';
	protected $epay_title = '微信支付';
	protected $epay_icon  = 'wx-logo.jpg';
}

class EPAY_Gateway_Alipay extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'alipay';
	protected $epay_title = '支付宝';
	protected $epay_icon  = 'zfb-logo.jpg';
}

class EPAY_Gateway_Bank extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'bank';
	protected $epay_title = '银联支付';
	protected $epay_icon  = 'bank.svg';
}

class EPAY_Gateway_Revolut extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'revolut';
	protected $epay_title = 'Revolut';
	protected $epay_icon  = 'revolut.svg';
}

class EPAY_Gateway_Paypal extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'paypal';
	protected $epay_title = 'PayPal';
	protected $epay_icon  = 'paypal.svg';
}

class EPAY_Gateway_Alipayhk extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'alipayhk';
	protected $epay_title = 'AlipayHK';
	protected $epay_icon  = 'alipayhk.svg';
}

class EPAY_Gateway_Usdt extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'usdt';
	protected $epay_title = 'USDT';
	protected $epay_icon  = 'usdt.svg';
}

class EPAY_Gateway_Linepay extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'linepay';
	protected $epay_title = 'LINE Pay';
	protected $epay_icon  = 'linepay.svg';
}

class EPAY_Gateway_Paynow extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'paynow';
	protected $epay_title = 'PayNow';
	protected $epay_icon  = 'paynow.svg';
}

class EPAY_Gateway_Card extends EPAY_Abstract_Gateway {
	protected $epay_type  = 'card';
	protected $epay_title = '信用卡 / 借记卡';
	protected $epay_icon  = 'card.svg';
}
