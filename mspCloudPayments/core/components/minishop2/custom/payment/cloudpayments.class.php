<?php
if (!class_exists('msPaymentInterface')) {
	if (file_exists(dirname(__FILE__, 3) . '/model/minishop2/mspaymenthandler.class.php')) {
		require_once dirname(__FILE__, 3) . '/model/minishop2/mspaymenthandler.class.php';
	} else {
		require_once dirname(__FILE__, 3) . '/handlers/mspaymenthandler.class.php';
	}
}

class CloudPayments extends msPaymentHandler implements msPaymentInterface
{
    const PAYMENT_RESULT_SUCCESS = 0;
    const PAYMENT_RESULT_ERROR_INVALID_ORDER = 10;
    const PAYMENT_RESULT_ERROR_INVALID_COST = 11;
    const PAYMENT_RESULT_ERROR_NOT_ACCEPTED = 13;
    const PAYMENT_RESULT_ERROR_EXPIRED = 20;

    /** @var  resource */
    private $curl;

    /**
     * CloudPayments constructor.
     * @param xPDOObject $object
     * @param array      $config
     */
    function __construct(xPDOObject $object, $config = array())
    {
        parent::__construct($object, $config);
        $configPrefix = 'ms2_payment_cloudpayments_';
	      $this->modx->switchContext($object->get('context'));

        $this->config = array_merge(array(
            'public_id'             => $this->modx->getOption($configPrefix . 'public_id'),
            'secret_key'            => $this->modx->getOption($configPrefix . 'secret_key'),
    	    'inn'                   => $this->modx->getOption($configPrefix . 'inn'),
    	    'calculation_method'    => $this->modx->getOption($configPrefix . 'calculation_method'),
    	    'calculation_object'    => $this->modx->getOption($configPrefix . 'calculation_object'),
    	    'status_delivered'      => $this->modx->getOption($configPrefix . 'order_status_delivered', null, 2),
            'skin'                  => $this->modx->getOption($configPrefix . 'skin', null, 'classic'),
            'currency'              => $this->modx->getOption($configPrefix . 'currency', null, 'RUR'),
            'status_auth_id'        => $this->modx->getOption($configPrefix . 'order_status_auth_id', null, 2),
            'status_pay_id'         => $this->modx->getOption($configPrefix . 'order_status_pay_id', null, 2),
            'status_fail_id'        => $this->modx->getOption($configPrefix . 'order_status_refund_id', null, 4),
            'status_refund_id'      => $this->modx->getOption($configPrefix . 'order_status_refund_id', null, 4),
            'status_for_confirm_id' => $this->modx->getOption($configPrefix . 'status_for_confirm_id', null, 3),
            'status_for_cancel_id'  => $this->modx->getOption($configPrefix . 'status_for_cancel_id', null, 4),
            'checkout_id'           => $this->modx->getOption($configPrefix . 'checkout_id', null, 1),
            'two_steps'             => $this->modx->getOption($configPrefix . 'two_steps', null, false)
        ), $config);

        $this->config['public_id']  = trim($this->config['public_id']);
        $this->config['secret_key'] = trim($this->config['secret_key']);
        if (!is_array($this->config['status_for_confirm_id'])) {
            $this->config['status_for_confirm_id'] = explode(',', $this->config['status_for_confirm_id']);
            $this->config['status_for_confirm_id'] = array_map('trim', $this->config['status_for_confirm_id']);
        }
        if (!is_array($this->config['status_for_cancel_id'])) {
            $this->config['status_for_cancel_id'] = explode(',', $this->config['status_for_cancel_id']);
            $this->config['status_for_cancel_id'] = array_map('trim', $this->config['status_for_cancel_id']);
        }

        $this->modx->lexicon->load('minishop2:cloudpayments');
    }

    /**
     *
     */
    function __destruct()
    {
        if ($this->curl) {
            curl_close($this->curl);
        }
    }

    /* @inheritdoc} */
    public function send(msOrder $order)
    {
        $link = $this->getPaymentLink($order);
        $data = array(
            'msorder' => $order->get('id')
        );
        if (!empty($link)) {
            $data['redirect'] = $link;
        }

        return $this->success('', $data);
    }

    /**
     * @param msOrder $order
     * @return string
     */
    public function getPaymentLink(msOrder $order)
    {
        $params = array(
            'msorder' => $order->get('id')
        );

        $checkoutId = $this->config['checkout_id'];
        if (empty($checkoutId)) {
            return '';
        }
        $context     = $order->get('context');
        $checkoutUrl = $this->modx->makeUrl($checkoutId, $context, $params, 'full');

        return $checkoutUrl;
    }

    /* @inheritdoc} */
    public function receive(msOrder $order, $params = array())
    {
        //Validate hash
        if (!$this->validateRequest()) {
            return $this->paymentError(self::PAYMENT_RESULT_ERROR_NOT_ACCEPTED, 'Wrong hash', $params);
        }

        //Validate fields
        $requiredFields = array(
            'ms2_action',
            'InvoiceId',
            'Amount',
            //'Currency',
        );

        foreach ($requiredFields as $field) {
            if (!isset($params[$field])) {
                return $this->paymentError(self::PAYMENT_RESULT_ERROR_NOT_ACCEPTED, 'Wrong payment request', $params);
            }
        }

        if (in_array($params['ms2_action'], array('pay', 'confirm', 'check', 'receipt'))) {
            if (!isset($params['Status'])) {
                return $this->paymentError(self::PAYMENT_RESULT_ERROR_NOT_ACCEPTED, 'Wrong payment request', $params);
            }

            if (!in_array($params['Status'], array('Completed', 'Authorized'))) {
                return $this->paymentError(self::PAYMENT_RESULT_ERROR_NOT_ACCEPTED, 'Wrong status', $params);
            }

            if ($params['ms2_action'] != 'receipt' && $params['Currency'] != $this->config['currency']) {
                return $this->paymentError(self::PAYMENT_RESULT_ERROR_INVALID_COST, 'Wrong currency', $params);
            }
            if ($params['Amount'] != number_format($order->get('cost'), 2, '.', '')) {
                return $this->paymentError(self::PAYMENT_RESULT_ERROR_INVALID_COST, 'Wrong order cost', $params);
            }
        }

        //Save transaction id
        if (!empty($params['TransactionId'])) {
            $transactionId = $this->getOrderProperty($order, 'transaction_id');
            if (empty($transactionId)) {
                $this->setOrderProperty($order, 'transaction_id', $params['TransactionId']);
            }
        }

        if ($params['ms2_action'] == 'check') {
            //For check action only validate request
            return self::PAYMENT_RESULT_SUCCESS;
        }

        //Change status
        $newStatus = false;
        if (in_array($params['ms2_action'], array('pay', 'confirm'))) {
            if ($params['Status'] == 'Authorized') {
                $newStatus = $this->config['status_auth_id'];
            } elseif ($params['Status'] == 'Completed') {
                $newStatus = $this->config['status_pay_id'];
            }
        } elseif ($params['ms2_action'] == 'fail') {
            $newStatus = $this->config['status_fail_id'];
        } elseif ($params['ms2_action'] == 'refund') {
            $newStatus = $this->config['status_refund_id'];
        } elseif ($params['ms2_action'] == 'cancel') {
                      $newStatus = $this->config['status_fail_id'];
                  }

        if ($newStatus && $order->get('status') != $newStatus) {
            if (isset($this->modx->context->key)) {
                $this->modx->context->key = 'mgr';
            }
            $this->ms2->changeOrderStatus($order->get('id'), $newStatus);
        }

        return self::PAYMENT_RESULT_SUCCESS;
    }

    /**
     * @return bool
     */
    private function validateRequest()
    {
        $postData    = file_get_contents('php://input');
        $checkSign   = base64_encode(hash_hmac('SHA256', $postData, $this->config['secret_key'], true));
        $requestSign = isset($_SERVER['HTTP_CONTENT_HMAC']) ? $_SERVER['HTTP_CONTENT_HMAC'] : '';

        return $checkSign === $requestSign;
    }

    /**
     * @param msOrder    $order
     * @param string     $name
     * @param mixed|null $default
     * @return mixed|null
     */
    private function getOrderProperty(msOrder $order, $name, $default = null)
    {
        $props = $order->get('properties');

        return isset($props['payments']['cloudpayments'][$name]) ? $props['payments']['cloudpayments'][$name] : $default;
    }

    /**
     * @param msOrder      $order
     * @param string|array $name
     * @param mixed|null   $value
     */
    private function setOrderProperty(msOrder $order, $name, $value = null)
    {
        $newProperties = array();
        if (is_array($name)) {
            $newProperties = $name;
        } else {
            $newProperties[$name] = $value;
        }

        $orderProperties = $order->get('properties');
        if (!is_array($orderProperties)) {
            $orderProperties = array();
        }
        if (!isset($orderProperties['payments']['cloudpayments'])) {
            $orderProperties['payments']['cloudpayments'] = array();
        }
        $orderProperties['payments']['cloudpayments'] = array_merge(
            $orderProperties['payments']['cloudpayments'],
            $newProperties
        );
        $order->set('properties', $orderProperties);
        $order->save();
    }

    /**
     * @param int    $code
     * @param string $text   Text to log
     * @param array  $params Request parameters
     * @return bool
     */
    public function paymentError($code, $text, $params = array())
    {
        $this->modx->log(xPDO::LOG_LEVEL_ERROR,
            '[miniShop2:CloudPayments] ' . $text . ' Request: ' . print_r($params, true));

        return $code;
    }

    /**
     * @param msOrder $order
     * @return string|bool
     */
    private function getOrderTransaction(msOrder $order)
    {
        return $this->getOrderProperty($order, 'transaction_id');
    }

    /**
     * @param msOrder $order
     * @return bool|mixed
     */
    public function getPaymentStatus(msOrder $order)
    {
        $transactionId = $this->getOrderTransaction($order);
        if (empty($transactionId)) {
            return false;
        }
        $transactionInfo = $this->makeRequest('payments/get', array(
            'TransactionId' => $transactionId
        ));

        return isset($transactionInfo['Model']['Status']) ? $transactionInfo['Model']['Status'] : false;
    }

    /**
     * @param msOrder $order
     * @return bool
     */
    public function cancelPayment(msOrder $order)
    {
        $status = $this->getPaymentStatus($order);
        switch ($status) {
            case 'Authorized':
                return $this->voidPayment($order);
                break;
            case 'Completed':
                return $this->refundPayment($order);
                break;
        }

        return false;
    }

    /**
     * @param msOrder $order
     * @return bool
     */
    public function refundPayment(msOrder $order)
    {
        if ($this->getOrderProperty($order, 'refunded', false)) {
            return true;
        }
        $transactionId = $this->getOrderTransaction($order);
        if (empty($transactionId)) {
            return false;
        }

        $response = $this->makeRequest('payments/refund', array(
            'TransactionId' => $transactionId,
            'Amount'        => $order->get('cost')
        ));
        if ($response !== false) {
            $this->setOrderProperty($order, 'refunded', true);
        }

        return $response !== false;
    }

    /**
     * @param msOrder $order
     * @return bool
     */
    public function voidPayment(msOrder $order)
    {
        if ($this->getOrderProperty($order, 'voided', false)) {
            return true;
        }
        $transactionId = $this->getOrderTransaction($order);
        if (empty($transactionId)) {
            return false;
        }

        $response = $this->makeRequest('payments/void', array(
            'TransactionId' => $transactionId
        ));
        if ($response !== false) {
            $this->setOrderProperty($order, 'voided', true);
        }

        return $response !== false;
    }

    /**
     * @param msOrder $order
     * @return bool
     */
    public function confirmPayment(msOrder $order)
    {
        if ($this->getOrderProperty($order, 'confirmed', false)) {
            return true;
        }
        $transactionId = $this->getOrderTransaction($order);
        if (empty($transactionId)) {
            return false;
        }

        $response = $this->makeRequest('payments/confirm', array(
            'TransactionId' => $transactionId,
            'Amount'        => number_format($order->get('cost'), 2, '.', ''),
        ));

        if ($response !== false) {
            $this->setOrderProperty($order, 'confirmed', true);
        }

        return $response !== false;
    }
    public function sendcheckDelivered(msOrder $order)
    {
        $order_id = $order->get('id');
        $vat      = $this->modx->getOption('ms2_payment_cloudpayments_vat', null, 0);
        $vat_d    = $this->modx->getOption('ms2_payment_cloudpayments_vat_delivery', null, 0);
        $profile  = $order->getOne('UserProfile');
        $email    = $profile->get('email');
        $phone    = $profile->get('phone');

        $receiptData = array(
            'Items'           => array(),
            'taxationSystem'  => $this->modx->getOption('ms2_payment_cloudpayments_taxation_system', null, 0),
            'calculationPlace'=>'www.'.$_SERVER['SERVER_NAME'],
            'email'           => $email,
            'phone'           => $phone,
            'amounts'         => array ( 'advancePayment' => sprintf('%.2f', $order->get('cost')),), 
        );
        $pdo = $this->modx->getService('pdoFetch');
        $products = $pdo->getCollection('msOrderProduct', json_encode(array('order_id' => $order_id)), array(
            'leftJoin' => array(
                'Product' => array(
                    'class' => 'msProduct',
                    'on'    => 'msOrderProduct.product_id = Product.id',
                ),
            ),
            'select'   => array(
                'msOrderProduct' => $this->modx->getSelectColumns('msOrderProduct', 'msOrderProduct', '', array('id'), true),
                'msProduct'      => $this->modx->getSelectColumns('msProduct', 'Product', '', array('content'), true),
            )
        ));
   
        foreach ($products as $row) {
            $title = !empty($row['name']) ? $row['name'] : $row['pagetitle'];
            $item  = array(
                'label'    => $title,
                'price'    => $row['price'],
                'quantity' => $row['count'],
                'amount'   => $row['cost'],
    	        'method'   => 4,
                'object'   => $this->modx->getOption('ms2_payment_cloudpayments_calculation_object', null, 1),
            );
            if (!empty($vat)) {
                $item['vat'] = $vat;
            }
            $receiptData['Items'][] = $item;
        }

        if ($order->get('delivery_cost') > 0) {
            $item = array(
                'label'    => $this->modx->lexicon('ms2_payment_cloudpayments_order_delivery_name'),
                'price'    => $order->get('delivery_cost'),
                'quantity' => 1,
                'amount'   => $order->get('delivery_cost'),
    	        'method'   => 4,
                'object'   => 4,
            );
            if (!empty($vat_d)) {
                $item['vat'] = $vat_d;
            }
            $receiptData['Items'][] = $item;
        }
        $data = array(
            "Inn" => $this->modx->getOption('ms2_payment_cloudpayments_inn'),
            "InvoiceId"=> $order->get('num'),
            "Type"=> "Income",
            'customerReceipt' => $receiptData
        );
        
        $response = $this->makeRequest('kkt/receipt', array(
            "Inn" => $this->modx->getOption('ms2_payment_cloudpayments_inn'),
            "InvoiceId"=> $order->get('num'),
            "Type"=> "Income",
            'customerReceipt' => $receiptData
        ));
    }
    /**
     * @param string $location
     * @param array  $request
     * @return bool|array
     */
    private function makeRequest($location, $request = array())
    {
        if (!$this->curl) {
            $this->curl = curl_init();
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($this->curl, CURLOPT_USERPWD, $this->config['public_id'] . ':' . $this->config['secret_key']);
        }

        curl_setopt($this->curl, CURLOPT_URL, 'https://api.cloudpayments.ru/' . $location);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            "content-type: application/json"
        ));
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($request));

        $response = curl_exec($this->curl);
        if ($response === false || curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != 200) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,
                '[miniShop2:CloudPayments] Failed API request.' .
                ' Location: ' . $location .
                ' Request: ' . print_r($request, true) .
                ' HTTP Code: ' . curl_getinfo($this->curl, CURLINFO_HTTP_CODE) .
                ' Error: ' . curl_error($this->curl)
            );

            return false;
        }
        $response = json_decode($response, true);
        if (!isset($response['Success']) || !$response['Success']) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,
                '[miniShop2:CloudPayments] Failed API request.' .
                ' Location: ' . $location .
                ' Request: ' . print_r($request, true) .
                ' Response: ' . print_r($response, true)
            );

            return false;
        }

        return $response;
    }
}