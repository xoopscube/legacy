<?php
// $Id$
define("_INSTALL_L0", "Добро пожаловать в программу установки XOOPS Cube 2.1");
define("_INSTALL_L70", "Пожалуйста, измените права файла mainfile.php так, чтобы он был доступен для записи сервером (например, chmod 777 mainfile.php на UNIX/LINUX сервере, или проверьте, что атрибут read-only не установлен на Windows сервере). Перезагрузите эту страницу после того как внесете изменения.");
//define("_INSTALL_L71","Click on the button below to begin the installation.");
define("_INSTALL_L1", "Откройте файл mainfile.php в текстовом редакторе и найдите следующий код в строке 31:");
define("_INSTALL_L2", "Изменить его на:");
define("_INSTALL_L3", "Теперь в строке 35 замените %s на %s");
define("_INSTALL_L4", "Да, я внес предложеные изменения, попробуем еще раз!");
define("_INSTALL_L5", "ВНИМАНИЕ!");
define("_INSTALL_L6", "Найдено несоответствие между значением переменной XOOPS_ROOT_PATH в строке 31 файла mainfile.php и текущим значением пути к корневой папке, которое было определено автоматически.");
define("_INSTALL_L7", "Ваши настройки: ");
define("_INSTALL_L8", "Автоматически определено: ");
define("_INSTALL_L9", "(На платформе MS вы можете получить данное сообщение даже если Ваша конфигурация корректна. Если это так - нажмите кнопку ниже, чтобы продолжить.)");
define("_INSTALL_L10", "Нажмите кнопку ниже если Вы считаете, что все в порядке.");
define("_INSTALL_L11", "Путь на сервере к корневому каталогу XOOPS Cube: ");
define("_INSTALL_L12", "URL к корневому каталогу XOOPS Cube: ");
define("_INSTALL_L13", "Если сведения выше корректны - нажмите кнопку ниже, чтобы продолжить.");
define("_INSTALL_L14", "Далее");
define("_INSTALL_L15", "Пожалуйста откройте файл mainfile.php и внесите в него информацию о подключении к БД.");
define("_INSTALL_L16", "%s hostname сервера баз данныхof your database server.");
define("_INSTALL_L17", "%s имя пользователя в базе.");
define("_INSTALL_L18", "%s пароль, необходимый для доступа к базе.");
define("_INSTALL_L19", "%s название базы, в которой будут созданы таблицы данных XOOPS Cube.");
define("_INSTALL_L20", "%s префикс таблиц, которые будут созданы в процессе установки.");
define("_INSTALL_L21", "Указанная база не найдена на сервере:");
define("_INSTALL_L22", "Попытаться создать ее?");
define("_INSTALL_L23", "Да");
define("_INSTALL_L24", "Нет");
define("_INSTALL_L25", "Следующая информация получена из Вашего конфигкурационного файла mainfile.php. Пожалуйста, внесите в эту информацию исправления, если они необходимы.");
define("_INSTALL_L26", "Конфигурация базы данных");
define("_INSTALL_L51", "Драйвер базы данных");
define("_INSTALL_L66", "Выберите тип базы данных который Вы предполагаете использовать");
define("_INSTALL_L27", "Адрес сервера БД");
define("_INSTALL_L67", "Адерс сервера баз данных. Если вы не уверены используйте значение 'localhost' чаще всего это срабатывает.");
define("_INSTALL_L28", "Имя пользователя БД");
define("_INSTALL_L65", "Имя аккаунта пользователя с правами доступа к серверу БД");
define("_INSTALL_L29", "Имя базы данных");
define("_INSTALL_L64", "Имя базы данных на сервере. Программа установки попыается создать базу для Вас если она не существует");
define("_INSTALL_L52", "Пароль пользователя БД");
define("_INSTALL_L68", "Пароль Вашего аккаунта на сервере БД");
define("_INSTALL_L30", "Префикс таблиц");
define("_INSTALL_L63", "Этот префикс будет добавлен ко всем новым таблицам в базе данных, для избежания конфликта имен. Если вы не уверены - не меняйте префикс предложеный по умолчанию.");
define("_INSTALL_L54", "Использовать постоянное (persistent) подключение к базе?");
define("_INSTALL_L69", "По умолчанию 'Нет'. Используйте 'Нет' если вы не уверены.");
define("_INSTALL_L55", "Физический путь к XOOPS Cube");
define("_INSTALL_L59", "Физический путь к корневой папке XOOPS Cube БЕЗ завершающего слеша.");
const _INSTALL_L75 = 'Физический путь TRUST_PATH' ;
const _INSTALL_L76 = 'Физический путь к вашему основному каталогу TRUST_PATH БЕЗ косой черты в конце<br>Рекомендуется размещать TRUST_PATH вне общедоступного DocumentRoot.' ;

define("_INSTALL_L56", "Виртуальный путь к XOOPS Cube (URL)");
define("_INSTALL_L58", "Виртуальный путь к XOOPS Cube БЕЗ завершающего слеша");

define("_INSTALL_L31", "Не могу создать базу данных. Свяжитесь с администратором сервера, для уточнения подробностей.");
define("_INSTALL_L32", "Первый этап установки завершен");
define("_INSTALL_L33", "Щелкните <a href='../index.php'>ЗДЕСЬ</a> чтобы увидеть главную страницу Вашего сайта.");
define("_INSTALL_L35", "Если у Вас возникли какие-либо ошибки - свяжитесь с разработчиками на сайте <a href='http://xoopscube.sourceforge.net/' target='_blank'>XOOPS Cube Project</a> или с командой русской поддержки на сайте <a href='http://xoopscube.ru/' target='_blank'>XOOPS Cube .RU</a>");
define("_INSTALL_L36", "Пожалуйста, выберите имя пользователя и пароль для администратора сайта.");
define("_INSTALL_L37", "Имя администратора");
define("_INSTALL_L38", "Email администратора");
define("_INSTALL_L39", "Пароль администратора");
define("_INSTALL_L74", "Повторите пароль");
const _INSTALL_L77 = 'Set Default Timezone' ;
define("_INSTALL_L40", "Создать таблицы");
define("_INSTALL_L41", "Пожалуйста вернитесь обратно и заполните всю необходимую информацию.");
define("_INSTALL_L42", "Назад");
define("_INSTALL_L57", "Пожалуйста введите %s");

// %s is database name
define("_INSTALL_L43", "База данных %s создана!");

// %s is table name
define("_INSTALL_L44", "Не могу создать %s");
define("_INSTALL_L45", "Таблица %s создана.");

define("_INSTALL_L46", "Для того, чтобы модули включенные в пакет функционировали правильно следующие файлы должны быть доступны для записи сервером. Пожалуйста измените права доступа для этих файлов. (Например, 'chmod 666 file_name' и 'chmod 777 dir_name' для UNIX/LINUX серверов, или убедитесь что атрибут read-only (только чтение) не установлен для Windows сервера)");
define("_INSTALL_L47", "Далее");

define("_INSTALL_L53", "Пожалуйста, проверьте следующие данные:");

define("_INSTALL_L60", "Не могу записать в mainfile.php. Проверьте права записи и попробуйте еще раз.");
define("_INSTALL_L61", "Не могу записать в mainfile.php. Свяжитесь с администратором сервера для уточнения деталей.");
define("_INSTALL_L62", "Конфигурационные данные успешно записаны в файл mainfile.php.");
define("_INSTALL_L72", "Следующие каталоги должны быть созданы и доступны для записи сервером. (Например, 'chmod 777 directory_name' на UNIX/LINUX серверах)");
define("_INSTALL_L73", "Неверный адрес электронной почты (Email)");

// add by haruki
define("_INSTALL_L80", "Добро пожаловать");
define("_INSTALL_L81", "Проверка прав доступа к файлам");
define("_INSTALL_L82", "Проверка прав доступа к файлам и папкам.");
define("_INSTALL_L83", "Файл %s НЕ ДОСТУПЕН для записи.");
define("_INSTALL_L84", "Файл %s доступен для записи.");
define("_INSTALL_L85", "Каталог %s НЕ ДОСТУПЕН для записи.");
define("_INSTALL_L86", "Каталог %s доступен для записи.");
define("_INSTALL_L87", "Ошибок не найдено.");
define("_INSTALL_L89", "Основные установки");
define("_INSTALL_L90", "Конфигурация сайта");
define("_INSTALL_L91", "Подтверждение");
define("_INSTALL_L92", "Сохранение настроек");
define("_INSTALL_L93", "Изменение настроек");
define("_INSTALL_L88", "Сохранение конфигурационных данных");
const _INSTALL_L166 = 'Проверьте права доступа к файлам в TRUST_PATH' ;
const _INSTALL_L167 = 'Проверьте разрешения пути доверия' ;
define("_INSTALL_L94", "Проверка пути и URL");
define("_INSTALL_L127", "Проверка пути к файлам и параметров URL.");
define("_INSTALL_L95", "Не могу определить физический путь к Вашему каталогу XOOPS Cube.");
define("_INSTALL_L96", "Обнаружен конфликт между автоматически определенным физическим путем (%s) и тем, что вы ввели.");
define("_INSTALL_L97", "<b>Физический путь</b> верен.");

define("_INSTALL_L99", "<b>Физический путь</b> должен быть каталогом.");
define("_INSTALL_L100", "<b>Виртуальный путь</b> верен.");
define("_INSTALL_L101", "<b>Виртуальный путь</b> не является верным адресом URL.");
define("_INSTALL_L102", "Проверка настроек базы данных");
define("_INSTALL_L103", "Начать сначала");
define("_INSTALL_L104", "Проверка базы");
define("_INSTALL_L105", "Попытка создания базы данных");
define("_INSTALL_L106", "Не могу подключиться к серверу БД.");
define("_INSTALL_L107", "Пожалуйста, проверьте сервер БД и его конфигурацию.");
define("_INSTALL_L108", "Соединение с сервером БД прошло успешно.");
define("_INSTALL_L109", "База данных %s не существует.");
define("_INSTALL_L110", "База данных %s существует и доступна.");
define("_INSTALL_L111", "Соединение с БД успешно.<br />Нажмите кнопку ниже, чтобы создать таблицы БД.");
define("_INSTALL_L112", "Настройки административного аккаунта");
define("_INSTALL_L113", "Таблица %s удалена.");
define("_INSTALL_L114", "Не удалось создать таблицы в базе данных.");
define("_INSTALL_L115", "Таблицы в базе данных созданы.");
define("_INSTALL_L116", "Вставка данных");
define("_INSTALL_L117", "Завершение установки");

define("_INSTALL_L118", "Не удалось создать таблицу %s.");
define("_INSTALL_L119", "%d записей вставлено в таблицу %s.");
define("_INSTALL_L120", "Не удалось вставить %d записей в таблицу %s.");

define("_INSTALL_L121", "Параметр '%s' установлен в значение '%s'.");
define("_INSTALL_L122", "Ошибка записи параметра %s.");

define("_INSTALL_L123", "Файл '%s' записан в катлог 'cache/'.");
define("_INSTALL_L124", "Ошибка записи файла '%s' в каталог 'cache/'.");

define("_INSTALL_L125", "Файл %s перезаписан файлом %s.");
define("_INSTALL_L126", "Не могу записать в файл '%s'.");

define("_INSTALL_L130", "Программа установки определила наличие таблиц XOOPS 1.3.x в Вашей базе данных.<br />Сейчас будет произведена попытка обновления базы до версии XOOPS 2.");
define("_INSTALL_L131", "Таблицы XOOPS 2 уже существуют в Вашей базе данных.");
define("_INSTALL_L132", "Обновление таблиц");
define("_INSTALL_L133", "Таблица %s обновлена.");
define("_INSTALL_L134", "Ошибка обновления таблицы %s.");
define("_INSTALL_L135", "Ошибка обновления таблиц базы данных.");
define("_INSTALL_L136", "Обновление таблиц базы данных.");
define("_INSTALL_L137", "Обновление модулей");
define("_INSTALL_L138", "Обновление комментариев");
define("_INSTALL_L139", "Обновление аватаров");
define("_INSTALL_L140", "Обновление смайликов");
define("_INSTALL_L141", "Сейчас программа установки подготовит каждый модуль к работе с XOOPS Cube.<br />Убедитесь, что Вы загрузили все файлы из поставки XOOPS Cube на Ваш сервер.<br />Обновление может занять некоторое время.");
define("_INSTALL_L142", "Обновление модулей");
define("_INSTALL_L143", "Программа установки сейчас подготовит конфигурационные данные XOOPS 1.3.x к работе с XOOPS Cube.");
define("_INSTALL_L144", "Обновление конфигурации");
define("_INSTALL_L145", "Комментарий (ID: %s) вставлен в базу данных.");
define("_INSTALL_L146", "Не могу вставить комментарий (ID: %s) в базу данных.");
define("_INSTALL_L147", "Обновление комментариев.");
define("_INSTALL_L148", "Обновление завершено.");
define("_INSTALL_L149", "Программа установки сейчас подготовит комментарии XOOPS 1.3.x к использованию с XOOPS Cube.<br />Это может занять некоторое время.");
define("_INSTALL_L150", "Программа установки сейчас подготовит смайлики и ранги пользователейXOOPS 1.3.x к использованию с XOOPS Cube.<br />Это может занять некоторое время.");
define("_INSTALL_L151", "Программа установки сейчас подготовит аватары пользователей XOOPS 1.3.x к использованию с XOOPS Cube.<br />Это может занять некоторое время.");
define("_INSTALL_L155", "Обновление смайликов/рангов пользователей");
define("_INSTALL_L156", "Обновление аватаров пользователей.");
define("_INSTALL_L157", "Выберите группу пользователей по умолчанию для каждого типа групп.");
define("_INSTALL_L158", "Группы в 1.3.x");
define("_INSTALL_L159", "Webmasters");
define("_INSTALL_L160", "Register Users");
define("_INSTALL_L161", "Anonymous Users");
define("_INSTALL_L162", "Вы должны выбрать группу по умолчанию для каждого типа групп.");
define("_INSTALL_L163", "Таблица %s удалена.");
define("_INSTALL_L164", "Ошибка удаления таблицы %s.");
define("_INSTALL_L165", "Сайт сейчас закрыт на профилактику. Пожалуйста, приходите позже.");

// %s is filename
define("_INSTALL_L152", "Не могу открыть файл %s.");
define("_INSTALL_L153", "Не могу обновить файл %s.");
define("_INSTALL_L154", "Файл %s обновлен.");

define('_INSTALL_L128', 'Выберите язык, который будет использоваться программой установки.');
define('_INSTALL_L200', 'Обновить');
define("_INSTALL_L210", "Второй этап установки.");


define('_INSTALL_CHARSET', 'UTF-8');

define('_INSTALL_LANG_XOOPS_SALT', "SALT");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "Играет дополнительную роль в генерации секретного слова и токенов. Нет необходимости менять значение по умолчанию.");

define('_INSTALL_HEADER_MESSAGE', 'Пожалуйста следуйте инструкциям программы установки.');
