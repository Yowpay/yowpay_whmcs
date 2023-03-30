<?php

class YowPayTemplate {

	const TEMPLATE_GATEWAY_REDIRECT_FORM          = 'gatewayRedirectionForm';
	const TEMPLATE_INVOICE_FORM                   = 'invoiceForm';
	const TEMPLATE_WELCOME                        = 'welcome';
	const TEMPLATE_MODULE_SUB_TITLE_WITH_BUTTON   = 'moduleSubtitleWithButton';
	const TEMPLATE_OPEN_BANKING_CONNECTION_DETAIL = 'openBankingConnectionDetail';
	const TEMPLATE_ORDER_SUCCESS                  = 'successPage';
	const TEMPLATE_ORDER_CANCEL                   = 'cancelPage';
	const TEMPLATE_MODULE_JS_VARIABLES            = 'moduleJsVariables';

	static function getTemplateContents($templateFile, $templateData = array()) {
		$templateContents = file_get_contents(__DIR__ . '/../template/' . $templateFile . '.html');
		$templateContents = self::replaceTemplateData($templateContents, $templateData);

		return $templateContents;
	}

	static function replaceTemplateData($templateContents, $templateData) {
		return str_replace(array_keys($templateData), array_values($templateData), $templateContents);
	}

	static function getImageFullPath($imageName) {

		return '/modules/gateways/yowpay/assets/image/' . $imageName;

	}

	static function getStyleSheetTag($stylesheetName) {
		return '<link rel="stylesheet" type="text/css" href="' . self::getStylesheetFullPath('global') . '"/>';
	}

	static function getStylesheetFullPath($stylesheetName) {

		return '/modules/gateways/yowpay/assets/css/' . $stylesheetName . '.css';

	}

	static function getScriptTag($scriptName) {

		return '<script type="application/javascript" src="' . self::getScriptFullPath('global') . '"></script>';

	}

	static function getScriptFullPath($scriptName) {

		return '/modules/gateways/yowpay/assets/js/' . $scriptName . '.js';

	}

}
