<?php
/**
 * Settings English Lexicon Entries for mspCloudPayments
 *
 * @package    mspcloudpayments
 * @subpackage lexicon
 */

$_lang['ms2_payment_cloudpayments_order_description']   = 'Order #[[+num]]';
$_lang['ms2_payment_cloudpayments_order_delivery_name'] = 'Delivery';
$_lang['ms2_payment_cloudpayments_order_pay']           = 'Pay';
$_lang['area_ms2_payment_cloudpayments']                = 'Payment CloudPayments';

$_lang['setting_ms2_payment_cloudpayments_public_id']                   = 'Site ID';
$_lang['setting_ms2_payment_cloudpayments_public_id_desc']              = 'Required site identifier. Located in the CloudPayments.';
$_lang['setting_ms2_payment_cloudpayments_secret_key']                  = 'Secret key';
$_lang['setting_ms2_payment_cloudpayments_secret_key_desc']             = 'Required secret key. Located in the CloudPayments.';
$_lang['setting_ms2_payment_cloudpayments_currency']                    = 'Currency';
$_lang['setting_ms2_payment_cloudpayments_currency_desc']               = 'Payment currency. Available values RUB/USD/EUR/GBP etc. All available values in CloudPayments documentation <a target="_blank" href="https://cloudpayments.ru/Docs/Directory#currencies">https://cloudpayments.ru/Docs/Directory#currencies</a>';
$_lang['setting_ms2_payment_cloudpayments_language']                    = 'Language';
$_lang['setting_ms2_payment_cloudpayments_language_desc']               = 'Example ru-RU, en-US etc. All available values in CloudPayments documentation <a target="_blank" href="https://cloudpayments.ru/Docs/Widget#language">https://cloudpayments.ru/Docs/Widget#language</a>';
$_lang['setting_ms2_payment_cloudpayments_two_steps']                   = 'Two-steps payment';
$_lang['setting_ms2_payment_cloudpayments_two_steps_desc']              = 'Payment will occur in 2 steps: authorization and confirmation.';
$_lang['setting_ms2_payment_cloudpayments_kkt']                         = 'Online check';
$_lang['setting_ms2_payment_cloudpayments_kkt_desc']                    = 'Automatically generate online check when pay.';
$_lang['setting_ms2_payment_cloudpayments_vat']                         = 'Vat';
$_lang['setting_ms2_payment_cloudpayments_vat_desc']                    = 'Available values: 18,10,0,110,118 or empty. Only for generate online check. More details in documentation CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Kassa#data-format">https://cloudpayments.ru/Docs/Kassa#data-format</a>';
$_lang['setting_ms2_payment_cloudpayments_vat_delivery']                = 'Vat for delivery';
$_lang['setting_ms2_payment_cloudpayments_vat_delivery_desc']           = 'Available values: 18,10,0,110,118 or empty. Only for generate online check. More details in documentation CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Kassa#data-format">https://cloudpayments.ru/Docs/Kassa#data-format</a>';
$_lang['setting_ms2_payment_cloudpayments_taxation_system']             = 'Taxation system';
$_lang['setting_ms2_payment_cloudpayments_taxation_system_desc']        = 'Available values: 0-5. More details in documentation CloudPayments <a target="_blank" href="https://cloudpayments.ru/Docs/Directory#taxation-system">https://cloudpayments.ru/Docs/Directory#taxation-system</a>';
$_lang['setting_ms2_payment_cloudpayments_order_status_auth_id']        = 'Order status on authorized payment';
$_lang['setting_ms2_payment_cloudpayments_order_status_auth_id_desc']   = 'When receive auth notify change order status to this. 0 for ignore notify.';
$_lang['setting_ms2_payment_cloudpayments_order_status_pay_id']         = 'Order status on pay';
$_lang['setting_ms2_payment_cloudpayments_order_status_pay_id_desc']    = 'When receive pay or confirm notify change order status to this. 0 for ignore notify.';
$_lang['setting_ms2_payment_cloudpayments_order_status_refund_id']      = 'Order status on refund';
$_lang['setting_ms2_payment_cloudpayments_order_status_refund_id_desc'] = 'When receive refund notify change order status to this. 0 for ignore notify.';
$_lang['setting_ms2_payment_cloudpayments_order_status_fail_id']        = 'Order status on fail';
$_lang['setting_ms2_payment_cloudpayments_order_status_fail_id_desc']   = 'When receive fail notify change order status to this. 0 for ignore notify.';
$_lang['setting_ms2_payment_cloudpayments_status_for_confirm_id']       = 'Order status for confirm payment';
$_lang['setting_ms2_payment_cloudpayments_status_for_confirm_id_desc']  = 'When change order to this status will be request for confirm payment. Only for two-steps payments. You can specify multiple statuses delimited by comma.';
$_lang['setting_ms2_payment_cloudpayments_status_for_cancel_id']        = 'Order status for cancel';
$_lang['setting_ms2_payment_cloudpayments_status_for_cancel_id_desc']   = 'When change order to this status will be request for cancel payment. You can specify multiple statuses delimited by comma.';
$_lang['setting_ms2_payment_cloudpayments_success_id']                  = 'Success page id';
$_lang['setting_ms2_payment_cloudpayments_success_id_desc']             = 'The customer will be sent to this page after complete payment.';
$_lang['setting_ms2_payment_cloudpayments_cancel_id']                   = 'Cancel page id';
$_lang['setting_ms2_payment_cloudpayments_cancel_id_desc']              = 'The customer will be sent to this page after cancel payment.';
$_lang['setting_ms2_payment_cloudpayments_checkout_id']                 = 'Checkout page id';
$_lang['setting_ms2_payment_cloudpayments_checkout_id_desc']            = 'The customer will be sent to this page for payment. On this page you must place widget. 0 for redirect to cart page.';