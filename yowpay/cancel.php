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
	YowPayTemplate::TEMPLATE_ORDER_CANCEL,
	array(
		'{{ORDER_CANCELED_TEXT}}'        => YowPayGateway::$language['orderCanceled'],
		'{{THANK_YOU_TEXT}}'             => YowPayGateway::$language['thankYou'],
		'{{ORDER_CANCELED_DETAIL_TEXT}}' => YowPayGateway::$language['orderCanceledDetail'],
		'{{ORDER_CANCELED_INSTRUCTION_TEXT}}' => YowPayTemplate::replaceTemplateData(
			YowPayGateway::$language['orderCanceledInstruction'],
			array(
				'{{INVOICE_URL}}' => YowPayGateway::getWhmcsBaseUrl() . '/viewinvoice.php?id=' . $invoiceId,
			)
		),
		'{{BACK_TO_OUR_SITE_TEXT}}'      => YowPayGateway::$language['backToOurSite'],
	)
);

exit;


