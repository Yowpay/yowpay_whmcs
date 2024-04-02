<?php

class YowPayApi {

	const API_URL_BASE = 'https://api.yowpay.com';
	const API_TIMEOUT  = 90;

	private $appToken;
	private $appSecretKey;

	public function __construct($appToken, $appSecretKey) {

		$this->appToken     = $appToken;
		$this->appSecretKey = $appSecretKey;

	}

	public static function createSignature($postData, $appSecretKey) {
		return hash_hmac('sha256', json_encode($postData), $appSecretKey);
	}

	public static function successCallbackResponse() {

		return json_encode(
			array(
				'result' => 'ok'
			)
		);

	}

	public static function succesResponse($responseData = array()) {

		return json_encode(
			array(
				'content' => $responseData
			)
		);

	}

	public static function errorResponse($errorCode, $errorMessage) {

		return json_encode(
			array(
				'error' => array(
					'code' => $errorCode,
					'msg'  => $errorMessage
				)
			)
		);

	}

	private function apiCall($apiRoute, $postData = array()) {

		$currentTimestamp = time();

		$postData['timestamp'] = $currentTimestamp;

		$curlHandler    = curl_init();
		$curlHeaderList = array(
			'Content-type: application/json',
			'Accept: application/json',
			'X-App-Access-Ts: ' . $currentTimestamp,
			'X-App-Token: ' . $this->appToken,
			'X-App-Access-Sig: ' . self::createSignature($postData, $this->appSecretKey),
		);

		curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $curlHeaderList);
		curl_setopt($curlHandler, CURLOPT_URL, self::API_URL_BASE . $apiRoute);
		curl_setopt($curlHandler, CURLOPT_POST, 1);
		curl_setopt($curlHandler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($curlHandler, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandler, CURLOPT_CONNECTTIMEOUT, self::API_TIMEOUT);
		curl_setopt($curlHandler, CURLOPT_TIMEOUT, self::API_TIMEOUT);

		$apiResponse = curl_exec($curlHandler);

		curl_close($curlHandler);

		return $apiResponse;

	}

	public function updateConfig($returnUrl, $cancelUrl, $webhookUrl) {

		return $this->apiCall(
			'/config/update',
			array(
				'returnUrl'  => $returnUrl,
				'cancelUrl'  => $cancelUrl,
				'webhookUrl' => $webhookUrl,
			)
		);

	}

	public function getBankData() {

		return $this->apiCall(
			'/bankData/get'
		);

	}

}
