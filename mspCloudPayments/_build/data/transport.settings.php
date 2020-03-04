<?php
/**
 * Loads system settings into build
 *
 * @package mspcloudpayments
 * @subpackage build
 */
$settings = array();

$tmp = array(
    'ms2_payment_cloudpayments_public_id' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments'
    ),
    'ms2_payment_cloudpayments_secret_key' => array(
        'value' => '',
        'xtype' => 'text-password',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_skin' => array(
            'value' => 'classic',
            'xtype' => 'textfield',
            'area' => 'ms2_payment_cloudpayments'
        ),
    'ms2_payment_cloudpayments_currency' => array(
        'value' => 'RUB',
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_language' => array(
        'value' => 'ru-RU',
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_two_steps' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_kkt' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_vat' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_vat_delivery' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_taxation_system' => array(
        'value' => '0',
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_calculation_method' => array(
        'value' => 1,
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_calculation_object' => array(
        'value' => 1,
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_inn' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_order_status_auth_id' => array(
        'value' => 1,
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_order_status_pay_id' => array(
        'value' => 2,
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_order_status_refund_id' => array(
        'value' => 4,
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_order_status_fail_id' => array(
        'value' => 4,
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_order_status_delivered' => array(
        'value' => 3,
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_status_for_confirm_id' => array(
        'value' => 3,
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_status_for_cancel_id' => array(
        'value' => 4,
        'xtype' => 'textfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_success_id' => array(
        'value' => '',
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_cancel_id' => array(
        'value' => '',
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
    'ms2_payment_cloudpayments_checkout_id' => array(
        'value' => '',
        'xtype' => 'numberfield',
        'area' => 'ms2_payment_cloudpayments',
    ),
);


foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => $k
            ,'namespace' => 'minishop2'
        ), $v
    ),'',true,true);

    $settings[] = $setting;
}

return $settings;