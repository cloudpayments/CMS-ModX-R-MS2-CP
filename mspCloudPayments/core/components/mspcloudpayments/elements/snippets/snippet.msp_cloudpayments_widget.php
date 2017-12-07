<?php
/** @var modX $modx */
/** @var array $scriptProperties */

$tpl            = $modx->getOption('tpl', $scriptProperties, 'tpl.mspCloudPaymentsWidget');
$id             = $modx->getOption('id', $scriptProperties, null);
$toPlaceholder  = $modx->getOption('toPlaceholder', $scriptProperties, false);
$allowStatusIds = $modx->getOption('allowStatusIds', $scriptProperties, '1');

/** @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {
    return false;
}
$pdoFetch = new pdoFetch($modx, $scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

if (empty($id) && !empty($_GET['msorder'])) {
    $id = intval($_GET['msorder']);
}
if (empty($id)) {
    return '';
}

$modx->lexicon->load('minishop2:cloudpayments');

/** @var msOrder $order */
if (!$order = $modx->getObject('msOrder', $id)) {
    return $modx->lexicon('ms2_err_order_nf');
}
$canView = (!empty($_SESSION['minishop2']['orders']) && in_array($id, $_SESSION['minishop2']['orders'])) ||
    $order->get('user_id') == $modx->user->id || $modx->user->hasSessionContext('mgr') || !empty($scriptProperties['id']);
if (!$canView) {
    return '';
}

$allowStatusIds = array_map('trim', explode(',', $allowStatusIds));
if (!in_array($order->get('status'), $allowStatusIds)) {
    return '';
}

$payment = $modx->getObject('msPayment', $order->get('payment'));
if ($payment->get('class') != 'CloudPayments') {
    return '';
}

$profile = $order->getOne('UserProfile');
$address = $order->getOne('Address');

$params = array(
    'publicId'        => trim($modx->getOption('ms2_payment_cloudpayments_public_id')),
    'description'     => $modx->lexicon('ms2_payment_cloudpayments_order_description', $order->toArray()),
    'currency'        => $modx->getOption('ms2_payment_cloudpayments_currency', null, 'RUB'),
    'language'        => $modx->getOption('ms2_payment_cloudpayments_language', null, 'ru-RU'),
    'email'           => $profile->get('email'),
    'receiver'        => $address->get('receiver'),
    'phone'           => $address->get('phone'),
    'taxation_system' => $modx->getOption('ms2_payment_cloudpayments_taxation_system', null, 0)
);

$jsonParams = 0;
if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $jsonParams = JSON_UNESCAPED_UNICODE;
}

array_walk($params, function ($item) use ($jsonParams) {
    return json_encode($item, $jsonParams);
});

$params = array_merge($params, array(
    'amount'    => $orderCost = sprintf('%.2f', $order->get('cost')),
    'invoiceId' => $order->get('num'),
    'accountId' => $order->get('user_id'),
));

$data = array(
    'name'     => $params['receiver'],
    'phone'    => $params['phone'],
    'order_id' => $order->get('id')
);

if ($modx->getOption('ms2_payment_cloudpayments_kkt')) {
    $receiptData = array(
        'Items'          => array(),
        'taxationSystem' => $params['taxation_system'],
        'email'          => $params['email'],
        'phone'          => $params['phone']
    );

    $products = $pdoFetch->getCollection('msOrderProduct', json_encode(array('order_id' => $id)), array(
        'leftJoin' => array(
            'Product' => array(
                'class' => 'msProduct',
                'on'    => 'msOrderProduct.product_id = Product.id',
            ),
        ),
        'select'   => array(
            'msOrderProduct' => $modx->getSelectColumns('msOrderProduct', 'msOrderProduct', '', array('id'), true),
            'msProduct'      => $modx->getSelectColumns('msProduct', 'Product', '', array('content'), true),
        )
    ));
    $vat      = $modx->getOption('ms2_payment_cloudpayments_vat');
    foreach ($products as $row) {
        $title = !empty($row['name']) ? $row['name'] : $row['pagetitle'];
        $item  = array(
            'label'    => $title,
            'price'    => $row['price'],
            'quantity' => $row['count'],
            'amount'   => $row['cost'],
        );
        if (!empty($vat)) {
            $item['vat'] = $vat;
        }
        $receiptData['Items'][] = $item;
    }

    if ($order->get('delivery_cost') > 0) {
        $item = array(
            'label'    => $modx->lexicon('ms2_payment_cloudpayments_order_delivery_name'),
            'price'    => $order->get('delivery_cost'),
            'quantity' => 1,
            'amount'   => $order->get('delivery_cost')
        );

        $vat = $modx->getOption('ms2_payment_cloudpayments_vat_delivery');
        if (!empty($vat)) {
            $item['vat'] = $vat;
        }
        $receiptData['Items'][] = $item;
    }

    $data['cloudPayments'] = array(
        'customerReceipt' => $receiptData
    );
}

$data = json_encode($data, $jsonParams);

$widgetMethod = $modx->getOption('ms2_payment_cloudpayments_two_steps', null, false) ? 'auth' : 'charge';

$successId = $modx->getOption('ms2_payment_cloudpayments_success_id', null, 0);
if (!empty($successId)) {
    $url = $modx->makeUrl($successId, $order->get('context'), array(
        'msorder' => $order->get('id')
    ), 'full');

    $successRedirect = "window.location.href = '{$url}';";
} else {
    $successRedirect = 'window.location.reload();';
}

$cancelId = $modx->getOption('ms2_payment_cloudpayments_cancel_id', null, 0);
if (!empty($successId)) {
    $url = $modx->makeUrl($cancelId, $order->get('context'), array(
        'msorder' => $order->get('id')
    ), 'full');

    $cancelRedirect = "window.location.href = '{$url}';";
} else {
    $cancelRedirect = 'window.location.reload();';
}

$scriptChunks   = array();
$scriptChunks[] = '<script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>';
$scriptChunks[] = <<<SCRIPT
<script>
    this.pay = function () {
        var widget = new cp.CloudPayments({language: '{$params['language']}'});
        widget.{$widgetMethod}({
                publicId: '{$params['publicId']}',
                description: '{$params['description']}',
                amount: {$params['amount']},
                currency: '{$params['currency']}',
                invoiceId: '{$params['invoiceId']}',
                accountId: '{$params['accountId']}',
                email: '{$params['email']}',
                data: {$data}
            },
            function (options) { // success
                {$successRedirect}
            },
            function (reason, options) { // fail
                {$cancelRedirect}
            }
        );
    };   
    $(document).on('click', '.msp_cloudpayments_btn', pay);
</script>
SCRIPT;

$output = $pdoFetch->getChunk($tpl, array_merge($scriptProperties, array(
    'script' => implode(PHP_EOL, $scriptChunks),
)));

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="mspCloudPaymentsWidgetLog">' . print_r($pdoFetch->getTime(), true) . '</pre>';
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);

    return '';
}

return $output;