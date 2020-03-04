<?php
/** @var msOrder $order */
if ($modx->event->name != 'msOnChangeOrderStatus') {
    //Wrong event
    return;
}

$paymentId = $order->get('payment');
$payment   = $modx->getObject('msPayment', $paymentId);
if (!$payment || $payment->get('class') != 'CloudPayments') {
    //Wrong payment
    return;
}

/** @var miniShop2 $miniShop2 */
if ($miniShop2 = $modx->getService('miniShop2')) {
    $miniShop2->loadCustomClasses('payment');
}

if (!class_exists('CloudPayments')) {
    //Failed load class
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2:CloudPayments] could not load payment class "CloudPayments".');

    return;
}

$handler = new CloudPayments($order);

if ($modx->getOption('ms2_payment_cloudpayments_kkt') && $order->get('status') == $handler->config['status_delivered']) {
 
    if ($handler->config['calculation_method'] == 1 || $handler->config['calculation_method'] == 2 || $handler->config['calculation_method'] == 3) {
        
        $handler->sendcheckDelivered($order);
    }
}

if (!$modx->getOption('ms2_payment_cloudpayments_two_steps', null, false)) {
    //Don't use two step payments
    return;
}
/** @var CloudPayments $handler */

if (in_array($order->get('status'), $handler->config['status_for_confirm_id'])) {
    $handler->confirmPayment($order);
} elseif (in_array($order->get('status'), $handler->config['status_for_cancel_id'])) {
    $handler->cancelPayment($order);
}
