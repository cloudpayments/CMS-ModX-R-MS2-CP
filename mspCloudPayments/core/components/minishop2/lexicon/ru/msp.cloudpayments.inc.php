<?php
/**
 * Settings Russian Lexicon Entries for mspCloudPayments
 *
 * @package    mspcloudpayments
 * @subpackage lexicon
 */

$_lang['ms2_payment_cloudpayments_order_description']   = 'Заказ #[[+num]]';
$_lang['ms2_payment_cloudpayments_order_delivery_name'] = 'Доставка';
$_lang['ms2_payment_cloudpayments_order_pay']           = 'Оплатить';
$_lang['area_ms2_payment_cloudpayments']                = 'Оплата CloudPayments';

$_lang['setting_ms2_payment_cloudpayments_public_id']                   = 'Идентификатор сайта';
$_lang['setting_ms2_payment_cloudpayments_public_id_desc']              = 'Обязательный идентификатор сайта. Находится в ЛК CloudPayments.';
$_lang['setting_ms2_payment_cloudpayments_secret_key']                  = 'Секретный ключ';
$_lang['setting_ms2_payment_cloudpayments_secret_key_desc']             = 'Обязательный секретный ключ. Находится в ЛК CloudPayments (Пароль для API).';
$_lang['setting_ms2_payment_cloudpayments_skin']                        = 'Дизайн виджета';
$_lang['setting_ms2_payment_cloudpayments_skin_desc']                   = 'Дизайн виджета. Дополнительная информация в документации Cloudpayments <a target="_blank" href="https://developers.cloudpayments.ru/#parametry">https://developers.cloudpayments.ru/#parametry</a>';
$_lang['setting_ms2_payment_cloudpayments_currency']                    = 'Валюта';
$_lang['setting_ms2_payment_cloudpayments_currency_desc']               = 'Валюта в которой производится оплата. RUB/USD/EUR/GBP итд. Все возможные значения смотрите в документации <a target="_blank" href="https://developers.cloudpayments.ru/#spisok-valyut">https://developers.cloudpayments.ru/#spisok-valyut</a>';
$_lang['setting_ms2_payment_cloudpayments_language']                    = 'Язык интерфейса виджета';
$_lang['setting_ms2_payment_cloudpayments_language_desc']               = 'Язык интерфейса виджета (ru-RU, en-US итд.) Все возможные значения смотрите в документации <a target="_blank" href="https://developers.cloudpayments.ru/#lokalizatsiya-vidzheta">https://developers.cloudpayments.ru/#lokalizatsiya-vidzheta</a>';
$_lang['setting_ms2_payment_cloudpayments_two_steps']                   = 'Двухстадийная оплата';
$_lang['setting_ms2_payment_cloudpayments_two_steps_desc']              = 'Оплата будет происходит в 2 этапа: авторизация и подтверждение.';
$_lang['setting_ms2_payment_cloudpayments_kkt']                         = 'Формировать онлайн-чек';
$_lang['setting_ms2_payment_cloudpayments_kkt_desc']                    = 'Автоматически формировать онлайн-чек при оплате.';
$_lang['setting_ms2_payment_cloudpayments_vat']                         = 'Ставка НДС';
$_lang['setting_ms2_payment_cloudpayments_vat_desc']                    = 'Возможные значения: 20,10,0,110,120 или пустое значение. Только для формирования онлайн-чека. Более детальная информация в документации CloudPayments <a target="_blank" href="https://developers.cloudpayments.ru/#znacheniya-stavki-nds">https://developers.cloudpayments.ru/#znacheniya-stavki-nds</a>';
$_lang['setting_ms2_payment_cloudpayments_vat_delivery']                = 'Ставка НДС для доставки';
$_lang['setting_ms2_payment_cloudpayments_vat_delivery_desc']           = 'Возможные значения: 20,10,0,110,120 или пустое значение. Только для формирования онлайн-чека. Более детальная информация в документации CloudPayments <a target="_blank" href="https://developers.cloudpayments.ru/#znacheniya-stavki-nds">https://developers.cloudpayments.ru/#znacheniya-stavki-nds</a>';
$_lang['setting_ms2_payment_cloudpayments_taxation_system']             = 'Система налогообложения';
$_lang['setting_ms2_payment_cloudpayments_taxation_system_desc']        = 'Возможные значения: 0-5. Более детальная информация в документации CloudPayments <a target="_blank" href="https://developers.cloudpayments.ru/#sistemy-nalogooblozheniya">https://developers.cloudpayments.ru/#sistemy-nalogooblozheniya</a>';
$_lang['setting_ms2_payment_cloudpayments_order_status_auth_id']        = 'Статус заказа при авторизации оплаты';
$_lang['setting_ms2_payment_cloudpayments_order_status_auth_id_desc']   = 'При получении уведомления об авторизации платежа статус заказа сменится на указанный. 0 для игнорирования уведомления. Только при двухстадийной оплате.';
$_lang['setting_ms2_payment_cloudpayments_order_status_pay_id']         = 'Статус заказа при оплате';
$_lang['setting_ms2_payment_cloudpayments_order_status_pay_id_desc']    = 'При получении уведомления об оплате или подтвеждении заказ сменит на данный статус. 0 для игнорирования уведомления.';
$_lang['setting_ms2_payment_cloudpayments_order_status_refund_id']      = 'Статус заказа при возврате';
$_lang['setting_ms2_payment_cloudpayments_order_status_refund_id_desc'] = 'При получении уведомления о возврате оплаты статус заказа сменится на указанный. 0 для игнорирования уведомления.';
$_lang['setting_ms2_payment_cloudpayments_order_status_fail_id']        = 'Статус заказа при отмене';
$_lang['setting_ms2_payment_cloudpayments_order_status_fail_id_desc']   = 'При получении уведомления об отмене оплаты статус заказа сменится на указанный. 0 для игнорирования уведомления.';
$_lang['setting_ms2_payment_cloudpayments_status_for_confirm_id']       = 'Статус заказа для подтверждения оплаты';
$_lang['setting_ms2_payment_cloudpayments_status_for_confirm_id_desc']  = 'При смене заказа на данный статус будет выполнен запрос подтверждения оплаты. Только при двухстадийной оплате. Вы можете указать несколько статусов через запятую.';
$_lang['setting_ms2_payment_cloudpayments_status_for_cancel_id']        = 'Статус заказа для отмены оплаты';
$_lang['setting_ms2_payment_cloudpayments_status_for_cancel_id_desc']   = 'При смене заказа на данный статус будет выполнен запрос отмену оплаты. Вы можете указать несколько статусов через запятую.';
$_lang['setting_ms2_payment_cloudpayments_success_id']                  = 'Страница успешной оплаты';
$_lang['setting_ms2_payment_cloudpayments_success_id_desc']             = 'Покупатель будет перенаправлен на указанную страницу при успешной оплате.';
$_lang['setting_ms2_payment_cloudpayments_cancel_id']                   = 'Страница при отказе от оплаты';
$_lang['setting_ms2_payment_cloudpayments_cancel_id_desc']              = 'Покупатель будет перенаправлен на указанную страницу при отмена оплаты.';
$_lang['setting_ms2_payment_cloudpayments_checkout_id']                 = 'Страница оплаты';
$_lang['setting_ms2_payment_cloudpayments_checkout_id_desc']            = 'Покупатель будет перенаправлен на указанную страницу для оплаты. На данной странице требуется разместить виджет. 0 для перенаправления на страницу корзины.';
