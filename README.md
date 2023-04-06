# YowPay Third Party Payment Gateway Plugin for WHMCS

Allows you to use YowPay as a payment gateway with your WHMCS installation.

## Installation

1. Make sure you have WHMCS v8.1+ installed
2. Download the latest release of this repo
3. Copy the zip contents into your WHMCS installation as follows:
* copy the `yowpay.php` file to `modules/gateways/yowpay.php`
* copy the `callback/yowpay.php` file to `modules/gateways/callback/yowpay.php`
* copy the `yowpay` folder to `modules/gateways/yowpay` 

## Configuration

1. Login into your WHMCS installation admin section
2. Navigate to `System settings` > `Payment Gateways` and select the tab `All Payment Gateways`
3. Find the `YowPay` payment gateway button and press it
4. Click on the `Manage Existing Gateways` tab and find the `YowPay` payment gateway section
5. Fill in the fields `App Token` and `App Secret Key` with the credentials you created on your YowPay account
6. Select `EUR` as main currency from the select box `Convert To For Processing` (`EUR` should be already added as a optional currency for this step to work, go to `Payments` > `Currencies` to set this up). If your WHMCS default currency is already `EUR` you can skip this step. 
7. Once you `Save changes` the module will attempt to link with YowPay servers
8. If all it's setup correctly you should get a message like `Module linking successful` on your module (use the button `Link module with YowPay` to try again later in case of error)
9. Your current banking connection status should be also visible trough this module, a message like `Get connection data successful` should be shown if it was retrieved successfully (use the button `Refresh` to try again later in case of error)

### Documentation and support

Check our documentation for more information about how YowPay works here: https://yowpay.com/documentation.

Visit https://yowpay.com/contact for any questions or issues you might encounter.

### License

This is licensed under the [MIT License][mit]


[mit]: https://opensource.org/licenses/MIT