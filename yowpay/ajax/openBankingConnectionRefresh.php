<?php

require_once __DIR__ . '/../../../../init.php';
require_once '../library/yowpayApi.php';
require_once '../library/yowpayGateway.php';

if (empty($_POST['appToken']) || empty($_POST['appSecretKey'])) {
	echo YowPayApi::errorResponse(101, 'Invalid data');
	exit;
}

$appToken     = trim($_POST['appToken']);
$appSecretKey = trim($_POST['appSecretKey']);

$apiInstance               = new YowPayApi($appToken, $appSecretKey);
$getBankDataResponse       = $apiInstance->getBankData();

try {
	$getBankDataResponseParsed = json_decode($getBankDataResponse, true);
}
catch (Exception $e) {
	echo YowPayApi::errorResponse(102, 'Invalid YowPay response');
	exit;
}

if (empty($getBankDataResponseParsed['content'])) {
	echo $getBankDataResponse;
	exit;
}

$getBankDataResponseParsed['content']['statusName'] = YowPayGateway::getBankDataStatusNameFromCode($getBankDataResponseParsed['content']['statusCode']);

echo json_encode($getBankDataResponseParsed, true);
exit;
