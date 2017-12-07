<?php
$output = '';
$lang   = $modx->getOption('manager_language');

$firstLabel = $lang == 'en' ? 'Make this payment method default' : 'Сделать СloudPayments платежной системой по умолчанию';
$firstDesc  = $lang == 'en' ? 'When this option is checked this payment method move to top of payments list and becomes the default payment' : 'При выборе данной опции метод оплаты CloudPayments перемещается в вверх списка оплат и тем самым становится оплатой по умолчанию.';
$output .= '<label><input type="checkbox" name="make_first" value="1" checked>' . $firstLabel . '</label>' .
    $firstDesc . '<br><br>';

$statuses = '';

$statusesDesc = array(
    'Authorized' => array(
        'ru' => 'На данный статус заказ переводится после авторизации платежа (блокирована на счету держателя).',
        'en' => 'Order changes status to this after authorised payment (blocked on the Holder’s account).',
    ),
    'Confirmed'  => array(
        'ru' => 'При переводе заказа в этот статус будет выполнен запрос на подтерждение оплаты. Нужен если требуется подтверждать оплату до отправки заказа. По умолчанию подтверждается при доставке.',
        'en' => 'After change order to this status will be request to confirm payment. Required if you want confirm before delivery. For default payment confirmation on delivery.',
    )
);

/** @var array $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        if (!empty($options['attributes']['statuses'])) {
            $statuses = '<ul id="formCheckboxes" style="height:200px;overflow:auto;">';
            foreach ($options['attributes']['statuses'] as $k => $v) {
                $label = $lang == 'ru' ? $v : $k;
                $desc  = isset($statusesDesc[$k][$lang]) ? '<p>' . $statusesDesc[$k][$lang] . '</p>' : '';
                $statuses .= '
				<li>
					<label>
						<input type="checkbox" name="add_statuses[]" value="' . $k . '"> ' . $label . '
					</label>
					' . $desc . '
				</li>';
            }
            $statuses .= '</ul>';
        }
        break;

    case xPDOTransport::ACTION_UPGRADE:
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

if ($statuses) {

    $twoStepChecked = $modx->getOption('ms2_payment_cloudpayments_two_steps', null, false) ? 'checked' : '';
    switch ($modx->getOption('manager_language')) {
        case 'ru':
            $output .= '<label>
                        <input type="checkbox" name="two_steps" value="1" ' . $twoStepChecked . '>Включить двухстадийную оплату
					    </label><br>';
            $output .= 'Для двухстадийной оплаты требуются дополнительные статусы заказа.<br>';
            $output .= 'Выберите статусы, которые нужно <b>добавить</b>:<br>
				<small>
					<a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = true;});">отметить все</a> |
					<a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = false;});">cнять отметки</a>
				</small>
			';
            break;
        default:
            $output .= '<label>
                        <input type="checkbox" name="two_steps" value="1">Enable two steps payment
					    </label><br>';
            $output .= 'For two steps payment needs additional order statuses.<br>';
            $output .= 'Select statuses, which need to <b>add</b>:<br>
				<small>
					<a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = true;});">select all</a> |
					<a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = false;});">deselect all</a>
				</small>
			';
    }

    $output .= $statuses;
}

return $output;