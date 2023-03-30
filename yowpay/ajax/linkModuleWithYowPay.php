<?php

require_once __DIR__ . '/../../../../init.php';
require_once '../library/yowpayApi.php';
require_once '../library/yowpayGateway.php';

if (empty($_POST['appToken']) || empty($_POST['appSecretKey'])) {
	echo YowPayApi::errorResponse(100, 'Invalid data');
	exit;
}

$appToken     = trim($_POST['appToken']);
$appSecretKey = trim($_POST['appSecretKey']);

$apiInstance         = new YowPayApi($appToken, $appSecretKey);
$linkWithYowPayResponse = $apiInstance->updateConfig(
	YowPayGateway::getReturnUrl(),
	YowPayGateway::getCancelUrl(),
	YowPayGateway::getWebhookUrl()
);

try {
	$linkWithYowPayResponseParsed = json_decode($linkWithYowPayResponse, true);
}
catch (Exception $e) {
	echo YowPayApi::errorResponse(103, 'Invalid YowPay response');
	exit;
}

echo $linkWithYowPayResponse;
exit;
