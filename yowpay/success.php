<?php

require_once __DIR__ . '/../../../init.php';
require_once 'library/yowpayGateway.php';
require_once 'library/yowpayTemplate.php';

YowPayGateway::loadLanguage(YowPayGateway::getLanguage());

$invoiceId = 0;
if (isset($_GET['orderId'])) {
	$invoiceId = (int) $_GET['orderId'];
}

echo YowPayTemplate::getTemplateContents(
	YowPayTemplate::TEMPLATE_ORDER_SUCCESS,
	array(
		'{{ORDER_SUCCESS_TEXT}}'                 => YowPayGateway::$language['orderSuccess'],
		'{{THANK_YOU_TEXT}}'                     => YowPayGateway::$language['thankYou'],
		'{{ORDER_SUCCESS_DETAIL_TEXT}}'          => YowPayGateway::$language['orderSuccessDetail'],
		'{{ORDER_SUCCESS_INSTRUCTION_TEXT}}' => YowPayTemplate::replaceTemplateData(
			YowPayGateway::$language['orderSuccessInstruction'],
			array(
				'{{INVOICE_URL}}' => YowPayGateway::getWhmcsBaseUrl() . '/viewinvoice.php?id=' . $invoiceId,
			)
		),
		'{{BACK_TO_OUR_SITE_TEXT}}'              => YowPayGateway::$language['backToOurSite'],
	)
);

exit;
