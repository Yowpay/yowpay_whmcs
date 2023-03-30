<?php

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';
require_once __DIR__ . '/../yowpay/library/yowpayApi.php';
require_once __DIR__ . '/../yowpay/library/yowpayGateway.php';

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

$remoteContentType  = $_SERVER['CONTENT_TYPE'];
$remoteAppAccessTs  = $_SERVER['HTTP_X_APP_ACCESS_TS'];
$remoteAppAccessSig = $_SERVER['HTTP_X_APP_ACCESS_SIG'];
$remoteAppToken     = $_SERVER['HTTP_X_APP_TOKEN'];
$remoteJsonBody     = file_get_contents('php://input');

$postData = array(
	'headers' => array(
		'CONTENT_TYPE'          => $remoteContentType,
		'HTTP_X_APP_ACCESS_TS'  => $remoteAppAccessTs,
		'HTTP_X_APP_ACCESS_SIG' => $remoteAppAccessSig,
		'HTTP_X_APP_TOKEN'      => $remoteAppToken,
	),
	'body'    => $remoteJsonBody
);

if (empty($remoteContentType) || empty($remoteAppAccessTs) || empty($remoteAppAccessSig) || empty($remoteAppToken) || empty($remoteJsonBody)) {
	echo YowPayApi::errorResponse(104, 'Invalid postback data');
	logTransaction($gatewayModuleName, $postData, '104: Invalid postback data');
	exit;
}

$remoteJsonBodyParsed = json_decode($remoteJsonBody, true);
if (
	!isset(
		$remoteJsonBodyParsed['timestamp'],
		$remoteJsonBodyParsed['transactionId'],
		$remoteJsonBodyParsed['amount'],
		$remoteJsonBodyParsed['currency'],
		$remoteJsonBodyParsed['language'],
		$remoteJsonBodyParsed['reference'],
		$remoteJsonBodyParsed['orderId'],
		$remoteJsonBodyParsed['createDate'],
		$remoteJsonBodyParsed['validateDate'],
		$remoteJsonBodyParsed['senderIban'],
		$remoteJsonBodyParsed['senderSwift'],
		$remoteJsonBodyParsed['senderAccountHolder'],
		$remoteJsonBodyParsed['status'],
		$remoteJsonBodyParsed['amountPaid'],
		$remoteJsonBodyParsed['currencyPaid']
	)
) {
	echo YowPayApi::errorResponse(106, 'Invalid postback transaction data');
	logTransaction($gatewayModuleName, $postData, '106: Invalid postback transaction data');
	exit;
}

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);
if (!$gatewayParams['type']) {
	echo YowPayApi::errorResponse(105, 'Module Not Activated');
	logTransaction($gatewayModuleName, $postData, '105: Module Not Activated');
	exit;
}


// Retrieve data returned in payment gateway callback
$invoiceId     = $remoteJsonBodyParsed['orderId'];
$transactionId = $remoteJsonBodyParsed['transactionId'];
$paymentFee    = 0;
$paymentAmount = $remoteJsonBodyParsed['amount'];
if ($remoteJsonBodyParsed['status'] == YowPayGateway::WEBHOOK_TRANSACTION_STATUS_PAID_PARTIALLY) {
	$paymentAmount = $remoteJsonBodyParsed['amountPaid'];
}


/**
 * Validate callback authenticity.
 */
$localAppToken  = $gatewayParams['yowPayGatewayAppToken'];
$localAppSecret = $gatewayParams['yowPayGatewayAppSecret'];
if ($localAppToken != $remoteAppToken) {
	echo YowPayApi::errorResponse(107, 'Invalid app token');
	logTransaction($gatewayModuleName, $postData, '107: Invalid app token');
	exit;
}

if ($remoteAppAccessTs != $remoteJsonBodyParsed['timestamp']) {
	echo YowPayApi::errorResponse(108, 'Invalid app timestamp');
	logTransaction($gatewayModuleName, $postData, '108: Invalid app timestamp');
	exit;
}

$localAppAccessSig = YowPayApi::createSignature($remoteJsonBodyParsed, $localAppSecret);
if ($localAppAccessSig != $remoteAppAccessSig) {
	echo YowPayApi::errorResponse(109, 'Invalid app signature');
	logTransaction($gatewayModuleName, $postData, '109: Invalid app signature');
	exit;
}

$invoice      = WHMCS\Billing\Invoice::find($invoiceId);
$userCurrency = getCurrency($invoice->clientId);
if ($userCurrency['code'] != $remoteJsonBodyParsed['currency']) {
	$paymentCurrencyId = WHMCS\Database\Capsule::table('tblcurrencies')->where('code', $remoteJsonBodyParsed['currency'])->value('id');
	if (empty($paymentCurrencyId)) {
		echo YowPayApi::errorResponse(110, 'Invalid currency');
		logTransaction($gatewayModuleName, $postData, '110: Invalid currency');
		exit;
	}
	$paymentAmount = convertCurrency($paymentAmount, $paymentCurrencyId, $userCurrency['id']);
}

/**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 *
 * @param int    $invoiceId Invoice ID
 * @param string $gatewayName Gateway Name
 */
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayModuleName);

/**
 * Check Callback Transaction ID.
 *
 * Performs a check for any existing transactions with the same given
 * transaction number.
 *
 * Performs a die upon encountering a duplicate.
 *
 * @param string $transactionId Unique Transaction ID
 */
checkCbTransID($transactionId);

/**
 * Log Transaction.
 *
 * Add an entry to the Gateway Log for debugging purposes.
 *
 * The debug data can be a string or an array. In the case of an
 * array it will be
 *
 * @param string       $gatewayName Display label
 * @param string|array $debugData Data to log
 * @param string       $transactionStatus Status
 */
logTransaction($gatewayModuleName, $postData, 'Success');

/**
 * Add Invoice Payment.
 *
 * Applies a payment transaction entry to the given invoice ID.
 *
 * @param int    $invoiceId Invoice ID
 * @param string $transactionId Transaction ID
 * @param float  $paymentAmount Amount paid (defaults to full balance)
 * @param float  $paymentFee Payment fee (optional)
 * @param string $gatewayModule Gateway module name
 */
addInvoicePayment(
	$invoiceId,
	$transactionId,
	$paymentAmount,
	$paymentFee,
	$gatewayModuleName
);

echo YowPayApi::successCallbackResponse();
exit;

