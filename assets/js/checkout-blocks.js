( function () {
	'use strict';

	if (
		! window.wc ||
		! window.wc.wcBlocksRegistry ||
		! window.wc.wcSettings ||
		! window.wp ||
		! window.wp.element
	) {
		return;
	}

	var registerPaymentMethod = window.wc.wcBlocksRegistry.registerPaymentMethod;
	var getSetting = window.wc.wcSettings.getSetting;
	var createElement = window.wp.element.createElement;
	var decodeEntities = window.wp.htmlEntities && window.wp.htmlEntities.decodeEntities
		? window.wp.htmlEntities.decodeEntities
		: function ( value ) {
			return value;
		};

	var gatewayIds = [
		'epay_wxpay',
		'epay_alipay',
		'epay_bank',
		'epay_revolut',
		'epay_paypal',
		'epay_alipayhk',
		'epay_usdt',
		'epay_linepay',
		'epay_paynow',
		'epay_card'
	];

	gatewayIds.forEach( function ( gatewayId ) {
		var settings = getSetting( gatewayId + '_data', {} );
		if ( ! settings.title ) {
			return;
		}

		var title = decodeEntities( settings.title );
		var description = decodeEntities( settings.description || '' );
		var labelChildren = [];

		if ( settings.icon ) {
			labelChildren.push(
				createElement( 'img', {
					key: 'icon',
					src: settings.icon,
					alt: '',
					style: {
						maxHeight: '32px',
						maxWidth: '96px',
						marginRight: '10px',
						verticalAlign: 'middle'
					}
				} )
			);
		}

		labelChildren.push(
			createElement( 'span', { key: 'title' }, title )
		);

		var label = createElement(
			'span',
			{
				style: {
					display: 'inline-flex',
					alignItems: 'center'
				}
			},
			labelChildren
		);
		var content = createElement( 'div', null, description );

		registerPaymentMethod( {
			name: gatewayId,
			paymentMethodId: gatewayId,
			label: label,
			content: content,
			edit: content,
			canMakePayment: function () {
				return true;
			},
			ariaLabel: title,
			supports: {
				features: settings.supports || [ 'products' ]
			}
		} );
	} );
}() );
