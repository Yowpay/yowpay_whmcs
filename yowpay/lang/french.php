<?php

$yowPayLanguage                             = array();
$yowPayLanguage['productionSettings']       = 'Paramètres de fabrication';
$yowPayLanguage['linkModuleWithYowPay']     = 'Module de liaison avec YowPay';
$yowPayLanguage['appToken']                 = 'Jeton d\'application';
$yowPayLanguage['appTokenDetail']           = 'Entrez le jeton d\'application créé dans votre compte YowPay et lié à ce site de commerce électronique';
$yowPayLanguage['appSecretKey']             = 'Clé secrète de l\'application';
$yowPayLanguage['appSecretKeyDetail']       = 'Entrez la clé secrète de l\'application créée dans votre compte YowPay et liée à ce site de commerce électronique';
$yowPayLanguage['returnUrl']                = 'URL de retour';
$yowPayLanguage['cancelUrl']                = 'URL d\'annulation';
$yowPayLanguage['webhookUrl']               = 'URL du webhook';
$yowPayLanguage['openBankingConnection']    = 'Open Banking Connexion';
$yowPayLanguage['refresh']                  = 'Rafraîchir';
$yowPayLanguage['notProvided']              = 'Non fourni';
$yowPayLanguage['active']                   = 'Actif';
$yowPayLanguage['expired']                  = 'Expiré';
$yowPayLanguage['lost']                     = 'Perdu';
$yowPayLanguage['unknown']                  = 'Inconnue';
$yowPayLanguage['pluginWelcomeText']        = '
		<p>Bienvenue sur le plug-in YowPay ! En quelques minutes, vos clients pourront payer avec les Virements Instantanés SEPA !</p>
		<p>Pour commencer à accepter les paiements avec SEPA Instant Transfer de vos clients, vous devrez suivre trois étapes simples:</p>
		<ul>
			<li>
				<a href="{{SIGNUP_LINK}}" target="_blank">Inscrivez-vous à YowPay</a> if you don\'t have an account already
			</li>
			<li>
				<a href="{{ECOMMERCE_ENTRY_LINK}}" target="_blank">Créez votre entrée de site Web de commerce électronique</a> dans l\'administrateur YowPay
			</li>
			<li>
				<a href="{{CREDENTIALS_LINK}}" target="_blank">Obtenez votre jeton d\'application et votre clé secrète</a> et collez-les dans les champs correspondants ci-dessous
			</li>
		</ul>
		<p>Si vous souhaitez savoir comment configurer ce plugin pour vos besoins, <a href="{{DOCUMENTATION_LINK}}" target="_blank">consultez notre documentation</a>.</p>';
$yowPayLanguage['accountOwner']             = 'Propriétaire du compte';
$yowPayLanguage['iban']                     = 'IBAN';
$yowPayLanguage['bicSwift']                 = 'BIC / SWIFT';
$yowPayLanguage['bankingStatus']            = 'Open Banking Statut';
$yowPayLanguage['bankingConsentStatus']     = '
		<p>
			Le consentement à l\'Open Banking expire à <span class="consentExpirationDate">-</span><br/>
			Prenez soin de renouveler avant la date d\'expiration. YowPay utilise l\'accès bancaire ouvert pour valider les paiements
		</p>';
$yowPayLanguage['manageBankConnection']     = 'Cliquez ici pour gérer votre connexion bancaire';
$yowPayLanguage['loading']                  = 'Chargement ...';
$yowPayLanguage['moduleLinkSuccess']        = 'Liaison de module réussie';
$yowPayLanguage['moduleLinkError']          = 'Erreur de liaison du module avec YowPay';
$yowPayLanguage['getConnectionSuccess']     = 'Obtenir les données de connexion avec succès';
$yowPayLanguage['getConnectionError']       = 'Erreur lors de l\'obtention des données de connexion';
$yowPayLanguage['sepaTransferYowPay']       = 'Payez par virement instantané SEPA - par YowPay';
$yowPayLanguage['orderCanceled']            = 'Commande annulée';
$yowPayLanguage['thankYou']                 = 'Merci!';
$yowPayLanguage['orderCanceledDetail']      = 'Votre commande a été annulée';
$yowPayLanguage['orderCanceledInstruction'] = 'Si vous souhaitez réessayer votre commande, nous avons créé une facture pour vous <a target="_blank" href="{{INVOICE_URL}}">ici</a>, ou vous pouvez y accéder ultérieurement depuis votre compte utilisateur.';
$yowPayLanguage['backToOurSite']            = 'Cliquez ici pour revenir sur notre site';
$yowPayLanguage['orderSuccess']             = 'Succès de la commande';
$yowPayLanguage['orderSuccessDetail']       = 'Votre commande a été traitée avec succès';
$yowPayLanguage['orderSuccessInstruction']  = 'Vos services seront activés dès réception de votre confirmation de paiement. Vous pouvez vérifier l\'état de votre facture <a target="_blank" href="{{INVOICE_URL}}">ici</a>, ou vous pouvez y accéder ultérieurement depuis votre compte utilisateur.';
$yowPayLanguage['yowpayTagline1']           = 'Faciliter les virements instantanés SEPA pour les entreprises';
$yowPayLanguage['yowpayTagline2']           = 'Pour les E-commerces & Point de vente';
$yowPayLanguage['payHere']                  = 'Payez ici';
