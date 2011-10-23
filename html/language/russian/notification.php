<?php
// $Id$

// RMV-NOTIFY

// Text for various templates...

define ('_NOT_NOTIFICATIONOPTIONS', 'Параметры уведомлений');
define ('_NOT_UPDATENOW', 'Обновить сейчас');
define ('_NOT_UPDATEOPTIONS', 'Обновить параметры уведомлений');

define ('_NOT_CANCEL', 'Отменить');
define ('_NOT_CLEAR', 'Очистить');
define ('_NOT_DELETE', 'Удалить');
define ('_NOT_CHECKALL', 'Отметить все');
define ('_NOT_MODULE', 'Модуль');
define ('_NOT_CATEGORY', 'Категория');
define ('_NOT_ITEMID', 'ID');
define ('_NOT_ITEMNAME', 'Имя');
define ('_NOT_EVENT', 'Событие');
define ('_NOT_EVENTS', 'События');
define ('_NOT_ACTIVENOTIFICATIONS', 'Активные уведомления');
define ('_NOT_NAMENOTAVAILABLE', 'Имя недоступно');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define ('_NOT_ITEMNAMENOTAVAILABLE', 'Элемент Имя недоступен');
define ('_NOT_ITEMTYPENOTAVAILABLE', 'Элемент Тип недоступен');
define ('_NOT_ITEMURLNOTAVAILABLE', 'Элемент URL недоступен');
define ('_NOT_DELETINGNOTIFICATIONS', 'Удаленные уведомления');
define ('_NOT_DELETESUCCESS', 'Уведомления удалены успешно.');
define ('_NOT_UPDATEOK', 'Параметры уведомлений обновлены');
define ('_NOT_NOTIFICATIONMETHODIS', 'Текущий метод уведомления:');
define ('_NOT_EMAIL', 'email');
define ('_NOT_PM', 'личное сообщение');
define ('_NOT_DISABLE', 'отключено');
define ('_NOT_CHANGE', 'Изменить');
define ('_NOT_RUSUREDEL', 'Вы уверены, что хотите удалить эти уведомления');
define ('_NOT_NOACCESS', 'У Вас недостаточно прав для доступа к этой странице.');

// Text for module config options

define ('_NOT_ENABLE', 'Включить');
define ('_NOT_NOTIFICATION', 'Уведомления');

define ('_NOT_CONFIG_ENABLED', 'Включить ведомления');
define ('_NOT_CONFIG_ENABLEDDSC', 'Этот модуль позволяет пользователям выбирать события о которых они хотели бы быть уведомлены. Выберите "Да" чтобы включить данную функцию.');

define ('_NOT_CONFIG_EVENTS', 'Включить сециальные события');
define ('_NOT_CONFIG_EVENTSDSC', 'Выберите на какие типы событий пользователи могут подписаться.');

define ('_NOT_CONFIG_ENABLE', 'Включить уведомления');
define ('_NOT_CONFIG_ENABLEDSC', 'Этот модуль позволяет уведомлять пользователей о различных событиях. Выберите если пользователь должен видеть уведолмение в блоке (Block-style), в модуле (Inline-style), или и там и там. Для block-style уведомлений - блок опции уведомления должен быть включен в модуле.');
define ('_NOT_CONFIG_DISABLE', 'Отключить уведомления');
define ('_NOT_CONFIG_ENABLEBLOCK', 'Включить только Block-style');
define ('_NOT_CONFIG_ENABLEINLINE', 'Включить только Inline-style');
define ('_NOT_CONFIG_ENABLEBOTH', 'Включить уведомления (оба варианта)');

// For notification about comment events

define ('_NOT_COMMENT_NOTIFY', 'Комментарий добавлен');
define ('_NOT_COMMENT_NOTIFYCAP', 'Уведомить меня, когда новый комментарий будет добавлен для этого эелемента.');
define ('_NOT_COMMENT_NOTIFYDSC', 'Получать уведомления когда новый комментарий будет отправлен (или добрен) для этого элемента.');
define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} авто-уведомление: Комментарий добавлен к {X_ITEM_TYPE}');

define ('_NOT_COMMENTSUBMIT_NOTIFY', 'Комментарий отправлен');
define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', 'Уведомлять меня, когда новый комментарий для этого элемента отправлен или ожидает одобрения.');
define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', 'Получать уведомления всякий раз, когда комментарий для этого элемента добавлени или ожидает одобрения.');
define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} авто-уведомление: Комментарий отправлен для {X_ITEM_TYPE}');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define ('_NOT_BOOKMARK_NOTIFY', 'Закладка');
define ('_NOT_BOOKMARK_NOTIFYCAP', 'Пометить этот элемент (без уведомлений).');
define ('_NOT_BOOKMARK_NOTIFYDSC', 'Отслеживать этот элемент без каких либо уведомлений.');

// For user profile
// FIXME: These should be reworded a little...

define ('_NOT_NOTIFYMETHOD', 'Способ уведомления: Если Вы следите, например, за форумом, как Вы хотели бы получать уведомления об имеющихся обновлениях?');
define ('_NOT_METHOD_EMAIL', 'Email (использует адрес в моем профиле)');
define ('_NOT_METHOD_PM', 'Личное сообщение');
define ('_NOT_METHOD_DISABLE', 'Временно выключено');

define ('_NOT_NOTIFYMODE', 'Режим уведомления по умолчанию');
define ('_NOT_MODE_SENDALWAYS', 'Уведомлять меня обо всех выбраных обнвлениях');
define ('_NOT_MODE_SENDONCE', 'Уведомлять однажды');
define ('_NOT_MODE_SENDONCEPERLOGIN', 'Уведомлять однажды, затем выключить до следующего посещения сайта');

?>
