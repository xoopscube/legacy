<?php

// Altsys admin menu and breadcrumbs
define( '_MD_A_MYMENU_MYTPLSADMIN' , 'Шаблоны');
define( '_MD_A_MYMENU_MYBLOCKSADMIN' , 'Блоки');
define( '_MD_A_MYMENU_MYPREFERENCES' , 'Предпочтения');

// contents list admin
define( '_MD_A_PICO_H2_CONTENTS' , 'Список содержимого');
define( '_MD_A_PICO_TH_CONTENTSID' , 'ID');
define( '_MD_A_PICO_TH_CONTENTSSUBJECT' , 'титул');
define( '_MD_A_PICO_TH_CONTENTSWEIGHT' , 'Заказ');
define( '_MD_A_PICO_TH_CONTENTSVISIBLE' , 'VIS');
define( '_MD_A_PICO_TH_CONTENTSSHOWINNAVI' , 'NAVI');
define( '_MD_A_PICO_TH_CONTENTSSHOWINMENU' , 'MENU');
define( '_MD_A_PICO_TH_CONTENTSALLOWCOMMENT' , 'COM');
define( '_MD_A_PICO_TH_CONTENTSFILTERS' , 'Фильтры');
define( '_MD_A_PICO_TH_CONTENTSACTIONS' , 'Действие');
define( '_MD_A_PICO_LEGEND_CONTENTSTHS' , 'VIS: видимый &nbsp; NAVI:показывать в навигации по страницам &nbsp; МЕНЮ:показать в меню &nbsp; COM:комментарии');
define( '_MD_A_PICO_BTN_MOVE' , 'Подвиньте');
define( '_MD_A_PICO_LABEL_CONTENTSRIGHTCHECKED' , 'Пакетное действие для выбранных элементов');
define( '_MD_A_PICO_MSG_CONTENTSMOVED' , 'Содержимое было перемещено');
define( '_MD_A_PICO_LABEL_MAINDISP' , 'вид');
define( '_MD_A_PICO_BTN_DELETE' , 'Удалить');
define( '_MD_A_PICO_CONFIRM_DELETE' , 'Вы уверены, что хотите их удалить?');
define( '_MD_A_PICO_MSG_CONTENTSDELETED' , 'Удалено успешно');
define( '_MD_A_PICO_BTN_EXPORT' , 'Экспорт');
define( '_MD_A_PICO_CONFIRM_EXPORT' , 'Выбранные элементы экспортируются в качестве основного содержимого модуля. Комментарии копироваться не будут. Пожалуйста, подтвердите экспорт данных.');
define( '_MD_A_PICO_MSG_CONTENTSEXPORTED' , 'Успешно экспортирован');
define( '_MD_A_PICO_MSG_FMT_DUPLICATEDVPATH' , 'Некоторое содержимое не было обновлено из-за дублированного vpath (ID: %s)');

// category_access
define( '_MD_A_PICO_LABEL_SELECTCATEGORY' , 'Выберите категорию');
define( '_MD_A_PICO_H2_INDEPENDENTPERMISSION' , 'Создайте уникальный набор разрешений.');
define( '_MD_A_PICO_LABEL_INDEPENDENTPERMISSION' , 'Этот элемент в настоящее время наследует разрешения от родителя. Вы можете установить флажок и отправить, чтобы настроить уникальные разрешения для этой категории.');
define( '_MD_A_PICO_LINK_CATPERMISSIONID' , 'Verify inherited permissions from parent category.');
define( '_MD_A_PICO_H2_GROUPPERMS' , 'Групповые разрешения');
define( '_MD_A_PICO_H2_USERPERMS' , 'Разрешения пользователя');
define( '_MD_A_PICO_TH_UID' , 'uid');
define( '_MD_A_PICO_TH_UNAME' , 'uname');
define( '_MD_A_PICO_TH_GROUPNAME' , 'Название группы');
define( '_MD_A_PICO_NOTICE_ADDUSERS' , 'Вы можете предоставить или отказать в разрешении определенным пользователям.<br>Добавьте <b>uid</b> или <b>uname</b> пользователя, а затем назначьте разрешения.');

// import
define( '_MD_A_PICO_H2_IMPORTFROM' , 'Импорт');
define( '_MD_A_PICO_LABEL_SELECTMODULE' , 'Выберите модуль');
define( '_MD_A_PICO_BTN_DOIMPORT' , 'Запустите импорт');
define( '_MD_A_PICO_CONFIRM_DOIMPORT' , 'Подтвердите импорт!');
define( '_MD_A_PICO_MSG_IMPORTDONE' , 'Успешно импортирован');
define( '_MD_A_PICO_ERR_INVALIDMID' , 'Указанный модуль неверен и не может быть импортирован.');
define( '_MD_A_PICO_ERR_SQLONIMPORT' , 'Не удалось импортировать. Вы должны проверить версии каждого модуля.');
define( '_MD_A_PICO_HELP_IMPORTFROM' , 'Вы можете импортировать из экземпляров Pico и TinyD. Все данные в этом модуле будут потеряны при выполнении операции импорта.');
define( '_MD_A_PICO_H2_SYNCALL' , 'Синхронизировать избыточные данные');
define( '_MD_A_PICO_BTN_DOSYNCALL' , 'Синхронизировать');
define( '_MD_A_PICO_MSG_SYNCALLDONE' , 'Успешно синхронизирован');
define( '_MD_A_PICO_HELP_SYNCALL' , 'Выполните, если в ваших категориях или содержимом отображаются противоречивые данные.');
define( '_MD_A_PICO_H2_CLEARBODYCACHE' , 'Очистить скомпилированный кэш');
define( '_MD_A_PICO_BTN_DOCLEARBODYCACHE' , 'Очистить кэш');
define( '_MD_A_PICO_MSG_CLEARBODYCACHEDONE' , 'Весь кэш компиляции был очищен.');
define( '_MD_A_PICO_HELP_CLEARBODYCACHE' , 'Выполните для удаления скомпилированных файлов и решения проблем с кэшем, например, после перемещения веб-сайта.');

// extras
define( '_MD_A_PICO_H2_EXTRAS' , 'формы');
define( '_MD_A_PICO_TH_ID' , 'ID');
define( '_MD_A_PICO_TH_TYPE' , 'Тип');
define( '_MD_A_PICO_TH_SUMMARY' , 'Резюме');
define( '_MD_A_PICO_LINK_DETAIL' , 'Деталь');
define( '_MD_A_PICO_LINK_EXTRACT' , 'Удалить');
define( '_MD_A_PICO_LABEL_SEARCHBYPHRASE' , 'Поиск по фразе');
define( '_MD_A_PICO_TH_EXTRASACTIONS' , 'Действие');
define( '_MD_A_PICO_LABEL_EXTRASRIGHTCHECKED' , 'Пакетное действие для выбранных элементов');
define( '_MD_A_PICO_BTN_CSVOUTPUT' , 'Вывод в формате CSV');
define( '_MD_A_PICO_MSG_DELETED' , 'Удалено успешно');

// tags
define( '_MD_A_PICO_H2_TAGS' , 'Управление тегами');
define( '_MD_A_PICO_TH_TAG' , 'тег');
define( '_MD_A_PICO_TH_USED' , 'использовал');
define( '_MD_A_PICO_LABEL_ORDER' , 'Заказ');

// tips
define( '_MD_A_PICO_TIPS_CONTENTS' , 'Советы по содержанию');
define( '_MD_A_PICO_TIPS_TAGS' , 'Теги Советы');
define( '_MD_A_PICO_TIPS_EXTRAS' , 'Дополнительные советы');

// ACTIVITY
define( '_MD_A_PICO_ACTIVITY_OVERVIEW' , 'Обзор деятельности');
define( '_MD_A_PICO_ACTIVITY_SCHEDULE' , 'Просроченный и запланированный контент');
define( '_MD_A_PICO_ACTIVITY_INTERVAL' , 'дни перерыва до и после сегодняшнего дня');
define( '_MD_A_PICO_ACTIVITY_LATEST' , 'последний запланированный контент');