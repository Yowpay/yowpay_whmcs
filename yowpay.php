<?php

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}

require_once 'yowpay/library/yowpayGateway.php';
require_once 'yowpay/library/yowpayTemplate.php';
require_once 'yowpay/library/yowpayApi.php';

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function yowpay_MetaData() {

	return YowPayGateway::getModuleMetaData();

}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function yowpay_config() {

	return YowPayGateway::getModuleConfig();

}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @return string
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 */
function yowpay_link($params) {

	YowPayGateway::loadLanguage(YowPayGateway::getLanguage());

	$postData = array(
		'amount'    => $params['amount'],
		'currency'  => $params['currency'],
		'orderId'   => (string) $params['invoiceid'],
		'language'  => YowPayGateway::getLanguage(true),
		'token'     => $params['yowPayGatewayAppToken'],
		'timestamp' => time(),
	);

	$invoiceData = array(
		'{{AMOUNT}}'           => $postData['amount'],
		'{{CURRENCY}}'         => $postData['currency'],
		'{{ORDER_ID}}'         => $postData['orderId'],
		'{{LANGUAGE}}'         => $postData['language'],
		'{{TOKEN}}'            => $postData['token'],
		'{{TIMESTAMP}}'        => $postData['timestamp'],
		'{{HASH}}'             => YowPayApi::createSignature($postData, $params['yowPayGatewayAppSecret']),
		'{{YOWPAY_TAGLINE_1}}' => YowPayGateway::$language['yowpayTagline1'],
		'{{YOWPAY_TAGLINE_2}}' => YowPayGateway::$language['yowpayTagline2'],
		'{{PAY_HERE}}'         => YowPayGateway::$language['payHere'],
	);

	if (YowPayGateway::getWhmcsCurrentPage() == YowPayGateway::WHMCS_PAGE_VIEW_INVOICE) {
		return YowPayTemplate::getTemplateContents(YowPayTemplate::TEMPLATE_INVOICE_FORM, $invoiceData);
	}

	return YowPayTemplate::getTemplateContents(YowPayTemplate::TEMPLATE_GATEWAY_REDIRECT_FORM, $invoiceData);

}
