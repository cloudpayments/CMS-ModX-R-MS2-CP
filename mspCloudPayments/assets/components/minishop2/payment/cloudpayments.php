<?php
define('MODX_API_MODE', true);
require dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->getService('error', 'error.modError');

//Logging request if in debug mode
if ($modx->getDebug()) {
    $modx->log(xPDO::LOG_LEVEL_DEBUG,
        '[miniShop2:CloudPayments] Payment notification request: ' . print_r($_REQUEST, true));
}

$allowIps = array(
    '130.193.70.192',
    '185.98.85.109'
);

if (!in_array($_SERVER['REMOTE_ADDR'], $allowIps)) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,
        '[miniShop2:CloudPayments] Request from prohibited IP ' . $_SERVER['REMOTE_ADDR'] .
        ' Request: ' . print_r($_REQUEST, true)
    );
    echo json_encode(array('code' => 13));
    die();
}

/* @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('minishop2', 'miniShop2', $modx->getOption('minishop2.core_path', null,
        $modx->getOption('core_path') . 'components/minishop2/') . 'model/minishop2/', array());
$miniShop2->loadCustomClasses('payment');

$responseCode = null;

if (class_exists('CloudPayments')) {
    //Find order order id
    $orderId = isset($_POST['InvoiceId']) ? intval($_POST['InvoiceId']) : null;
    if (isset($_POST['Data'])) {
        $data = json_decode($_POST['Data'], true);
        if (!empty($data['order_id'])) {
            $orderId = intval($data['order_id']);
        }
    }
    if (!empty($orderId)) {
        /** @var msOrder $order */
        $order = $modx->newObject('msOrder');
        /** @var msPaymentInterface|CloudPayments $handler */
        $handler = new CloudPayments($order);

        $params = $_REQUEST;

        /** @var msOrder $order */
        $order = $modx->getObject('msOrder', $orderId);
        if (isset($order)) {
            $responseCode = $handler->receive($order, $params);
        } else {
            $responseCode = $handler->paymentError(CloudPayments::PAYMENT_RESULT_ERROR_INVALID_ORDER, 'Order not found',
                $params);
        }
    } else {
        $responseCode = CloudPayments::PAYMENT_RESULT_ERROR_INVALID_ORDER;
        $modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2:CloudPayments] Wrong InvoiceId.');
    }
} else {
    $responseCode = 13;
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[miniShop2:CloudPayments] could not load payment class "CloudPayments".');
}

//Always return OK to prevent repeat payment notification
echo json_encode(array('code' => $responseCode));