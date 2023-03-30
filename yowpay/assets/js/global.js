class YowPayModule {

	static bankStatusConnected = 1;

	static openBankingConnectionButton          = $('#yowPayOpenBankingConnectionButton');
	static linkButton                           = $('#yowPayLinkButton');
	static openBankingConnectionDetailContainer = $('#yowPayPageOpenBankingConnectionDetail');

	static yowPayGatewayAppTokenInput  = $('input[name="field[yowPayGatewayAppToken]"]');
	static yowPayGatewayAppSecretInput = $('input[name="field[yowPayGatewayAppSecret]"]');

	static init() {

		YowPayModule.initEventListener();

		YowPayModule.setLinkButtonVisibility(false);
		YowPayModule.setOpenBankingConnectionVisibility(false);

		if (YowPayModule.tokenAndSecretKeyExist()) {
			YowPayModule.setLinkButtonVisibility(true);
			YowPayModule.linkButton.trigger('click');

			YowPayModule.setOpenBankingConnectionVisibility(true);
			YowPayModule.openBankingConnectionButton.trigger('click');
		}


	}

	static setOpenBankingConnectionVisibility(isVisible) {

		YowPayModule.openBankingConnectionDetailContainer.addClass('isHidden');
		YowPayModule.openBankingConnectionButton.parent().addClass('isHidden');

		if (isVisible) {
			YowPayModule.openBankingConnectionDetailContainer.removeClass('isHidden');
			YowPayModule.openBankingConnectionButton.parent().removeClass('isHidden');
		}

	}

	static setLinkButtonVisibility(isVisible) {

		YowPayModule.linkButton.addClass('isHidden');

		if (isVisible) {
			YowPayModule.linkButton.removeClass('isHidden');
		}

	}

	static tokenAndSecretKeyExist() {

		return YowPayModule.yowPayGatewayAppTokenInput.val().trim() !== '' &&
			YowPayModule.yowPayGatewayAppSecretInput.val().trim() !== '';

	}

	static setButtonActionResultMessage(button, messageType, message) {

		let loadingMessage = button.parent().find('.loadingMessage');
		let successMessage = button.parent().find('.successMessage');
		let errorMessage   = button.parent().find('.errorMessage');

		loadingMessage.addClass('isHidden');
		successMessage.addClass('isHidden');
		errorMessage.addClass('isHidden');

		switch (messageType) {

			case 'loading':

				loadingMessage.removeClass('isHidden');
				loadingMessage.find('span').text(message);
				break;

			case 'success':

				successMessage.removeClass('isHidden');
				successMessage.find('span').text(message);
				break;

			case 'error':

				errorMessage.removeClass('isHidden');
				errorMessage.find('span').text(message);
				break;

			default:

			//

		}

	}

	static initEventListener() {

		YowPayModule.linkButton.on('click', () => {

			YowPayModule.linkButton.attr('disabled', 'true');
			YowPayModule.setButtonActionResultMessage(YowPayModule.linkButton, 'loading', translation['loading']);

			YowPayModule.yowPayLinkAjax().then(
				(data) => {

					let dataObject = JSON.parse(data);
					if (dataObject.error) {
						YowPayModule.setButtonActionResultMessage(YowPayModule.linkButton, 'error', dataObject.error.code + ': ' + dataObject.error.msg);
						YowPayModule.linkButton.removeAttr('disabled');
						return;
					}

					YowPayModule.linkButton.removeAttr('disabled');
					YowPayModule.setButtonActionResultMessage(YowPayModule.linkButton, 'success', translation['moduleLinkSuccess']);

				},
				() => {

					YowPayModule.setButtonActionResultMessage(YowPayModule.linkButton, 'error', translation['moduleLinkError']);
					YowPayModule.linkButton.removeAttr('disabled');

				}
			);

		});

		YowPayModule.openBankingConnectionButton.on('click', () => {

			YowPayModule.openBankingConnectionButton.attr('disabled', 'true');
			YowPayModule.setButtonActionResultMessage(YowPayModule.openBankingConnectionButton, 'loading', translation['loading']);

			YowPayModule.yowPayOpenBankingConnectionAjax().then(
				(data) => {

					let dataObject = JSON.parse(data);
					if (dataObject.error) {
						YowPayModule.openBankingConnectionButton.removeAttr('disabled');
						YowPayModule.setButtonActionResultMessage(YowPayModule.openBankingConnectionButton, 'error', dataObject.error.code + ': ' + dataObject.error.msg);
						return;
					}

					YowPayModule.openBankingConnectionButton.removeAttr('disabled');
					YowPayModule.setButtonActionResultMessage(YowPayModule.openBankingConnectionButton, 'success', translation['getConnectionSuccess']);

					YowPayModule.openBankingConnectionDetailContainer.find('.accountOwner').text(dataObject.content.accountHolder);
					YowPayModule.openBankingConnectionDetailContainer.find('.iban').text(dataObject.content.iban);
					YowPayModule.openBankingConnectionDetailContainer.find('.swift').text(dataObject.content.swift);
					YowPayModule.openBankingConnectionDetailContainer.find('.consentExpirationDate').text(dataObject.content.consentExpirationTime);
					YowPayModule.openBankingConnectionDetailContainer.find('.openBankingStatus .statusText').text(dataObject.content.statusName);

					YowPayModule.openBankingConnectionDetailContainer.find('.dataExplanation').removeClass('isHidden');
					YowPayModule.openBankingConnectionDetailContainer.find('.openBankingStatus').removeClass('layoutNotConnected');
					YowPayModule.openBankingConnectionDetailContainer.find('.openBankingStatus').removeClass('layoutConnected');

					if (dataObject.content.statusCode === YowPayModule.bankStatusConnected) {
						YowPayModule.openBankingConnectionDetailContainer.find('.openBankingStatus').addClass('layoutConnected');
					}
					else {
						YowPayModule.openBankingConnectionDetailContainer.find('.openBankingStatus').addClass('layoutNotConnected');
					}

				},
				() => {

					YowPayModule.openBankingConnectionButton.removeAttr('disabled');
					YowPayModule.setButtonActionResultMessage(YowPayModule.openBankingConnectionButton, 'error', translation['getConnectionError']);

				}
			);

		});

	}


	static yowPayLinkAjax() {

		return $.post(
			'/modules/gateways/yowpay/ajax/linkModuleWithYowPay.php',
			{
				appToken    : YowPayModule.yowPayGatewayAppTokenInput.val(),
				appSecretKey: YowPayModule.yowPayGatewayAppSecretInput.val(),
			}
		);

	}


	static yowPayOpenBankingConnectionAjax() {

		return $.post(
			'/modules/gateways/yowpay/ajax/openBankingConnectionRefresh.php',
			{
				appToken    : YowPayModule.yowPayGatewayAppTokenInput.val(),
				appSecretKey: YowPayModule.yowPayGatewayAppSecretInput.val()
			}
		);

	}

}

$(document).ready(function () {

	YowPayModule.init();

});
