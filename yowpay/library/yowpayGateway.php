<?php

class YowPayGateway {

	// https://developers.whmcs.com/themes/variables/
	const WHMCS_PAGE_VIEW_INVOICE = 'VIEWINVOICE';

	const BANK_STATUS_NOT_PROVIDED = 0;
	const BANK_STATUS_ACTIVE       = 1;
	const BANK_STATUS_EXPIRED      = 2;
	const BANK_STATUS_LOST         = 3;

	const WEBHOOK_TRANSACTION_STATUS_PAID_IN_FULL   = 1;
	const WEBHOOK_TRANSACTION_STATUS_PAID_PARTIALLY = 2;

	static $language = array();

	static function getModuleMetaData() {

		return array(
			'DisplayName'                 => 'Yowpay Gateway Module',
			'APIVersion'                  => '2.0',
			'DisableLocalCreditCardInput' => true,
			'TokenisedStorage'            => false,
		);

	}

	static function getModuleConfig() {

		self::loadLanguage(self::getLanguage());

		return array(
			'FriendlyName'                             => array(
				'Type'  => 'System',
				'Value' => 'YowPay',
			),
			'yowPayGatewayWelcome'                     => array(
				'Description' =>
					YowPayTemplate::getTemplateContents(
						YowPayTemplate::TEMPLATE_WELCOME,
						array(
							'{{YOWPAY_LOGO_IMAGE_PATH}}' => YowPayTemplate::getImageFullPath('plugin-logo-txt-only.png'),
							'{{PLUGIN_WELCOME_TEXT}}'    => YowPayTemplate::replaceTemplateData(
								self::$language['pluginWelcomeText'],
								array(
									'{{SIGNUP_LINK}}'          => 'https://yowpay.com/signup',
									'{{ECOMMERCE_ENTRY_LINK}}' => 'https://yowpay.com/account/ecommerce/new',
									'{{CREDENTIALS_LINK}}'     => 'https://yowpay.com/account/ecommerce',
									'{{DOCUMENTATION_LINK}}'   => 'https://yowpay.com/documentation',
								)
							)
						)
					),
			),
			'yowPayGatewayProductionSettings'          => array(
				'Description' =>
					YowPayTemplate::getTemplateContents(
						YowPayTemplate::TEMPLATE_MODULE_SUB_TITLE_WITH_BUTTON,
						array(
							'{{TITLE}}'         => self::$language['productionSettings'],
							'{{BUTTON_ID}}'     => 'yowPayLinkButton',
							'{{BUTTON_TEXT}}'   => self::$language['linkModuleWithYowPay'],
							'{{LOADING_IMAGE}}' => YowPayTemplate::getImageFullPath('loading.svg'),
							'{{ERROR_IMAGE}}'   => YowPayTemplate::getImageFullPath('ko.png'),
							'{{SUCCESS_IMAGE}}' => YowPayTemplate::getImageFullPath('ok.png'),
						)
					),
			),
			'yowPayGatewayAppToken'                    => array(
				'FriendlyName' => self::$language['appToken'],
				'Type'         => 'text',
				'Size'         => '100',
				'Default'      => '',
				'Description'  => self::$language['appTokenDetail'],
			),
			'yowPayGatewayAppSecret'                   => array(
				'FriendlyName' => self::$language['appSecretKey'],
				'Type'         => 'text',
				'Size'         => '100',
				'Default'      => '',
				'Description'  => self::$language['appSecretKeyDetail'],
			),
			'yowPayGatewayReturnUrl'                   => array(
				'FriendlyName' => self::$language['returnUrl'],
				'Description'  => self::getReturnUrl()
			),
			'yowPayGatewayCancelUrl'                   => array(
				'FriendlyName' => self::$language['cancelUrl'],
				'Description'  => self::getCancelUrl()
			),
			'yowPayGatewayWebhookUrl'                  => array(
				'FriendlyName' => self::$language['webhookUrl'],
				'Description'  => self::getWebhookUrl()
			),
			'yowPayGatewayOpenBankingConnection'       => array(
				'Description' =>
					YowPayTemplate::getTemplateContents(
						YowPayTemplate::TEMPLATE_MODULE_SUB_TITLE_WITH_BUTTON,
						array(
							'{{TITLE}}'         => self::$language['openBankingConnection'],
							'{{BUTTON_ID}}'     => 'yowPayOpenBankingConnectionButton',
							'{{BUTTON_TEXT}}'   => self::$language['refresh'],
							'{{LOADING_IMAGE}}' => YowPayTemplate::getImageFullPath('loading.svg'),
							'{{ERROR_IMAGE}}'   => YowPayTemplate::getImageFullPath('ko.png'),
							'{{SUCCESS_IMAGE}}' => YowPayTemplate::getImageFullPath('ok.png'),
						)
					),
			),
			'yowPayGatewayOpenBankingConnectionDetail' => array(
				'Description' =>
					YowPayTemplate::getTemplateContents(
						YowPayTemplate::TEMPLATE_OPEN_BANKING_CONNECTION_DETAIL,
						array(
							'{{SUCCESS_IMAGE}}'                  => YowPayTemplate::getImageFullPath('ok.png'),
							'{{FAIL_IMAGE}}'                     => YowPayTemplate::getImageFullPath('ko.png'),
							'{{ACCOUNT_OWNER_TEXT}}'             => self::$language['accountOwner'],
							'{{IBAN_TEXT}}'                      => self::$language['iban'],
							'{{BIC_SWIFT_TEXT}}'                 => self::$language['bicSwift'],
							'{{BANKING_STATUS_TEXT}}'            => self::$language['bankingStatus'],
							'{{BANKING_CONSENT_STATUS_TEXT}}'    => self::$language['bankingConsentStatus'],
							'{{MANAGE_BANKING_CONNECTION_TEXT}}' => self::$language['manageBankConnection'],
						)
					) .
					YowPayTemplate::getTemplateContents(
						YowPayTemplate::TEMPLATE_MODULE_JS_VARIABLES,
						array(
							'{{LOADING_TEXT}}'                => self::$language['loading'],
							'{{MODULE_LINK_SUCCESS_TEXT}}'    => self::$language['moduleLinkSuccess'],
							'{{MODULE_LINK_ERROR_TEXT}}'      => self::$language['moduleLinkError'],
							'{{GET_CONNECTION_SUCCESS_TEXT}}' => self::$language['getConnectionSuccess'],
							'{{GET_CONNECTION_ERROR_TEXT}}'   => self::$language['getConnectionError'],
						)
					) .
					YowPayTemplate::getStyleSheetTag('global') .
					YowPayTemplate::getScriptTag('global'),
			)
		);

	}

	static function getWhmcsCurrentPage() {
		$filename = basename($_SERVER['SCRIPT_FILENAME']);

		return str_replace(".PHP", "", strtoupper($filename));
	}

	static function getWhmcsBaseUrl() {
		return str_replace('http://', 'https://', WHMCS\Config\Setting::getValue('SystemURL'));
	}

	static function getWhmcsLanguage() {
		return WHMCS\Config\Setting::getValue('language');
	}

	static function getLanguage($returnLanguageCode = false) {

		$supportedLanguageList = array(
			'english' => 'en',
			'french'  => 'fr',
		);

		$languageName  = 'english';
		$whmcsLanguage = self::getWhmcsLanguage();
		if (in_array($whmcsLanguage, array_keys($supportedLanguageList))) {
			$languageName = $whmcsLanguage;
		}

		return $returnLanguageCode ? $supportedLanguageList[$languageName] : $languageName;

	}

	static function loadLanguage($languageName = 'english') {

		global $yowPayLanguage;

		require_once __DIR__ . '/../lang/' . $languageName . '.php';

		self::$language = $yowPayLanguage;

	}

	static function getReturnUrl() {

		return self::getWhmcsBaseUrl() . '/modules/gateways/yowpay/success.php';

	}

	static function getCancelUrl() {

		return self::getWhmcsBaseUrl() . '/modules/gateways/yowpay/cancel.php';

	}

	static function getWebhookUrl() {

		return self::getWhmcsBaseUrl() . '/modules/gateways/callback/yowpay.php';

	}

	static function getBankDataStatusNameFromCode($statusCode) {

		self::loadLanguage(self::getLanguage());

		switch ($statusCode) {

			case self::BANK_STATUS_NOT_PROVIDED:

				$statusName = self::$language['notProvided'];
				break;

			case self::BANK_STATUS_ACTIVE:

				$statusName = self::$language['active'];
				break;

			case self::BANK_STATUS_EXPIRED:

				$statusName = self::$language['expired'];
				break;

			case self::BANK_STATUS_LOST:

				$statusName = self::$language['lost'];
				break;

			default:

				$statusName = self::$language['unknown'];

		}

		return $statusName;

	}

}
