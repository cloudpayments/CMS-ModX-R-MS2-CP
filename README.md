# Данный модуль предоставляет виджет оплаты, который размещается на сайте и оплата производится не покидая сайт на внешние страницы оплаты платежного шлюза.
 Порядок подкоючения к CloudPayments находится в документации  https://cloudpayments.ru/Docs/Connect


### Техническая настройка

В личном кабинете CloudPayments в настройках сайта необходимо включить следующие уведомления:

**Сheck** — http://domain.ru//assets/components/minishop2/payment/cloudpayments.php?ms2_action=check

**Pay** — http://domain.ru//assets/components/minishop2/payment/cloudpayments.php?ms2_action=pay

**Fail** — http://domain.ru//assets/components/minishop2/payment/cloudpayments.php?ms2_action=fail

**Confirm** — http://domain.ru//assets/components/minishop2/payment/cloudpayments.php?ms2_action=confirm

**Refund**(Требуется только при двухстадийной оплате) — http://domain.ru//assets/components/minishop2/payment/cloudpayments.php?ms2_action=refund

Где **domain.ru** доменное имя вашего сайта. Во всех случаях требуется выбирать вариант по умолчанию: кодировка — UTF-8, HTTP-метод — POST, формат — CloudPayments

В настройках MODX (пространство имен miniShop2 раздел "Оплата CloudPayments") необходимо указать следующий настройки:

**Идентификатор сайта** — Public id сайта из личного кабинета CloudPayments

**Секретный ключ** — API Secret из личного кабинета CloudPayments

**Страница успешной оплаты** — id страницы, на которую будет перенаправлен пользователь после оплаты

**Страница при отказе от оплаты** — id страницы, на которую будет перенаправлен пользователь в случае отмены или ошибки при оплате

**Страница оплаты** — id страницы оплаты. Необязательный параметр. 

При указании для оплаты пользователь будет перенаправлен на специальную страницу, на которой нужно вывести состав заказа и виджет оплаты. Также URL данной страницы будет указан в письме пользователю при создании заказа (плейсхолдер [[+payment_link]]). Если параметр не указан, то пользователь перенаправляется на текущую страницу, но в URL добавляется msorder с номером заказа. Поэтому в этом случае виджет оплаты требуется вызывать на странице с корзиной.


Дополнительно также можно указать требуемый язык, валюту, id статусов заказа. Детали смотрите в описании параметров.

Для вывода виджета оплаты требуется на странице с корзиной вызвать сниппет **[[!mspCloudPaymentsWidget]]**.
Сниппет выводит виджет оплаты только если в параметре URL есть msorder с верным id заказа, а также статус заказа "Новый".
Пример минимальной страницы оплаты корзины с параметрами по умолчанию:

**[[!msGetOrder]]**

**[[!mspCloudPaymentsWidget]]**

Сниппет **mspCloudPaymentsWidget** имеет следующие парамтры :

**tpl** — чанк шаблона виджета оплаты. По умолчанию tpl.mspCloudPaymentsWidget;

**id** — id заказа для оплаты. По умолчанию номер заказа берется из GET параметра msorder;

**allowStatusIds** —  id статусов заказа, при которых разрешено оплачивать заказ. По умолчанию 1 ("новый");

**toPlaceholder** — имя плейсходера. При указании вывод будет помещен в указанный плейсхолдер.


После настройки модуля необходимо включить его в настройках оплаты miniShop2 и выбрать доступные методы доставки

# Двухстадийная оплата
В этом режиме оплата происходил в два этапа: авторизация платежа (блокировка суммы на карте покупателя) и подтверждение списания. Для работы модуля в этом режиме могут потребоваться дополнительные статусы заказа:

**Авторизован** — На данный статус заказ переводится при получении уведомления об авторизации оплаты.

**Подтвержден** — На данный статус нужно перевести заказ для отправки запроса на подтверждение оплаты. Необходим если требуется подтверждать заказ до отправки. По умолчанию оплата подтверждается при смене статуса на "Доставлен"

Данные статусы будет предложено создать автоматически при установке модуля. Также данные статусы можно создать вручную в настройках miniShop2 и указать их идентификаторы в настройках модуля.

Для включения двухстадийной оплаты необходимо дополнительно указать следующий настройки в MODX:

**Двухстадийная оплата** — Да

**Статус заказа при авторизации оплаты** — id статуса, в который нужно перевести заказ при получении уведомления об авторизации платежа.

**Статус заказа для подтверждения оплаты** — id статуса при переводе в который отправляется запрос на подтверждение оплаты. Можно указать несколько id через запятую.

# Интеграция с онлайн-кассой
Модуль позволяет интегрировать онлайн-кассу при оплате и отменах платежей. Для этого подключить кассу в личном кабинете CloudPayments https://cloudpayments.ru/Docs/Kassa и указать дополнительные настройки модуля:

**Формировать онлайн-чек** — Да
**Ставка НДС** — Указание ставки НДС или пустое значение, если НДС не облагается. Например 18, для НДС 18%. Все возможные значения указаны в документации https://cloudpayments.ru/Docs/Kassa#data-format

**Ставка НДС для доставки** — Указание отдельной ставки НДС для доставки. Если доставка платная, то она в чеке оформляется отдельной строкой со своей ставкой НДС. Значения аналогично ставке НДС для товаров.

**Система налогообложения** — Тип системы налогообложения. Возможные значения перечислены в документации CloudPayments https://cloudpayments.ru/Docs/Directory#taxation-system

### Ручная Установка Модуля
1. Загрузить в любую подпапку modx (например mspCloudPayments)
2. Скопировать build.config.example.php в build.config.php
3. Поправить путь MODX_BASE_PATH в build.config.php (должен быть на корень установки modx)
4. Запустить http://domain.ru/mspCloudPayments/_build/build.transport.php
5. Пакет должен появится в списке пакетов в разделе "Управление пакетами"
