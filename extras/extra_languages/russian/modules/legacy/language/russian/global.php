<?php
// $Id$

define('_TOKEN_ERROR', 'Внимание! Это защищает Вас от отправки неверного запроса или поста. Пожалуйста, отправтье еще раз чтобы подтвердить Ваши намерения!');
define('_SYSTEM_MODULE_ERROR', 'Следующие модули не установлены.');
define('_INSTALL','Инсталлировать');
define('_UNINSTALL','Деинсталлировать');
define('_SYS_MODULE_UNINSTALLED','Обязателен (Не инсталлирован)');
define('_SYS_MODULE_DISABLED','Обязателен (Выключен)');
define('_SYS_RECOMMENDED_MODULES','Рекомендуется для установки');
define('_SYS_OPTION_MODULES','Дополнительный модуль');
define('_UNINSTALL_CONFIRM','Вы уверены, что хотите деинсталлировать системный модуль?');

//%%%%%%	File Name mainfile.php 	%%%%%
define("_PLEASEWAIT","Пожалуйста подождите.");
define("_FETCHING","Загрузка...");
define("_TAKINGBACK","Taking you back to where you were....");
define("_LOGOUT","Выйти");
define("_SUBJECT","Тема");
define("_MESSAGEICON","Иконка сообщения");
define("_COMMENTS","Комментарии");
define("_POSTANON","Отправить анонимно");
define("_DISABLESMILEY","Отключить смайлы");
define("_DISABLEHTML","Отключить HTML");
define("_PREVIEW","Предв. просмотр");

define("_GO","Поехали!");
define("_NESTED","Nested");
define("_NOCOMMENTS","Нет комментариев");
define("_FLAT","Плоский");
define("_THREADED","Threaded");
define("_OLDESTFIRST","Старые в начале");
define("_NEWESTFIRST","Новые в начале");
define("_MORE","больше...");
define("_MULTIPAGE","Чтобы Ваша статья занимала несколько страниц вставьте слово <font color=red>[pagebreak]</font> (со скобками) в точке разрыва страницы.");
define("_IFNOTRELOAD","Если страница автоматически не перезагрузится перейдите <a href='%s'>по этой ссылке</a>");
define("_WARNINSTALL2","ВНИМАНИЕ: Каталог %s существует на Вашем сервере. Удалите его, для обеспечения большей безопасности.");
define("_WARNINWRITEABLE","ВНИМАНИЕ: Сервер имеет права записи в файл %s. Измените права для этого файла для обеспечения большей безопасности. для Unix (444), для Win32 (Только-чтение)");
define('_WARNPHPENV','ВНИМАНИЕ: Параметр %s файла php.ini установлен в "%s". %s');
define('_WARNSECURITY','(Это может привести к потенциальной бреши в защите)');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define("_PROFILE","Профиль");
define("_POSTEDBY","Автор");
define("_VISITWEBSITE","Посетить вебсайт");
define("_SENDPMTO","Отправить личное сообщение для %s");
define("_SENDEMAILTO","Отправить Email для %s");
define("_ADD","Добавить");
define("_REPLY","Ответить");
define("_DATE","Дата");   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define("_MAIN","Главная");
define("_MANUAL","Руководство");
define("_INFO","Инфо.");
define("_CPHOME","Панель управления");
define("_YOURHOME","Домашняя страница");

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define("_WHOSONLINE","Сейчас на сайте");
define('_GUESTS', 'Гости');
define('_MEMBERS', 'Пользователи');
define("_ONLINEPHRASE","%s пользователей на сайте");
define("_ONLINEPHRASEX","%s пользователей в разделе %s");
define("_CLOSE","Закрыть");  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define("_QUOTEC","Цитата:");

//%%%%%%	File Name admin.php 	%%%%%
define("_NOPERM","У Вас недостаточно прав для доступа к этому разделу.");

//%%%%%		Common Phrases		%%%%%
define("_NO","Нет");
define("_YES","Да");
define("_EDIT","Редактировать");
define("_DELETE","Удалить");
define("_VIEW","Посмотреть");
define("_SUBMIT","Отправить");
define("_MODULENOEXIST","Выбраный модуль не существует!");
define("_ALIGN","Выключка");
define("_LEFT","Влево");
define("_CENTER","По центру");
define("_RIGHT","Вправо");
define("_FORM_ENTER", "Введите %s");
// %s represents file name
define("_MUSTWABLE","Сервер должен иметь права записи в файл %s!");
// Module info
define('_PREFERENCES', 'Параметры');
define("_VERSION", "Версия");
define("_DESCRIPTION", "Описание");
define("_ERRORS", "Ошибки");
define("_NONE", "None");
define('_ON','on');
define('_READS','просмотров');
define('_WELCOMETO','Добро пожаловать %s');
define('_SEARCH','Поиск');
define('_ALL', 'All');
define('_TITLE', 'Заголовок');
define('_OPTIONS', 'Параметры');
define('_QUOTE', 'Цитата');
define('_LIST', 'Список');
define('_LOGIN','Авторизация');
define('_USERNAME','Имя пользователя:');
define('_PASSWORD','Пароль:');
define("_SELECT","Выбрать");
define("_IMAGE","Изображение");
define("_SEND","Отправить");
define("_CANCEL","Отменить");
define("_ASCENDING","По возрастанию");
define("_DESCENDING","По убыванию");
define('_BACK', 'Назад');
define('_NOTITLE', 'Нет заголовка');
define('_RETURN_TOP', 'вернуться наверх');

/* Image manager */
define('_IMGMANAGER','Управление изображениями');
define('_NUMIMAGES', '%s изображений');
define('_ADDIMAGE','Добавить изображение');
define('_IMAGENAME','Имя:');
define('_IMGMAXSIZE','Максимальный размер файла (в байтах):');
define('_IMGMAXWIDTH','Макс. ширина (pixels):');
define('_IMGMAXHEIGHT','Макс. высота (pixels):');
define('_IMAGECAT','Категория:');
define('_IMAGEFILE','Файл картинки:');
define('_IMGWEIGHT','Порядок отображения в менеджере картинок:');
define('_IMGDISPLAY','Показывать эту картинку?');
define('_IMAGEMIME','Тип MIME:');
define('_FAILFETCHIMG', 'Не могу получить загруженный файл %s');
define('_FAILSAVEIMG', 'Ошибка сохранения файла %s в БД');
define('_NOCACHE', 'No Cache');
define('_CLONE', 'Клонировать');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define("_STARTSWITH", "Начинается с");
define("_ENDSWITH", "Заканчивается на");
define("_MATCHES", "Соответствия");
define("_CONTAINS", "Содержит");

//%%%%%%	File Name commentform.php 	%%%%%
define("_REGISTER","Зарегистрироваться");

//%%%%%%	File Name xoopscodes.php 	%%%%%
define("_SIZE","Размер");  // font size
define("_FONT","Шрифт");  // font family
define("_COLOR","Цвет");  // font color
define("_EXAMPLE","Пример");
define("_ENTERURL","Введите URL ссылки, которую Вы хотите добавить:");
define("_ENTERWEBTITLE","Введите заголовок ссылки:");
define("_ENTERIMGURL","Введите URL картинки, которую Вы хотите добавить:");
define("_ENTERIMGPOS","Теперь введите позицию картинки..");
define("_IMGPOSRORL","'R' или 'r' чтобы расположить ее справа, 'L' или 'l' чтобы расположить слева, или оставьте поле пустым.");
define("_ERRORIMGPOS","Ошибка! Введите расположение картинки.");
define("_ENTEREMAIL","Введите Email который Вы хотите добавить.");
define("_ENTERCODE","Введите коды, которые Вы хотите добавить.");
define("_ENTERQUOTE","Введите текст цитаты.");
define("_ENTERTEXTBOX","Введите текст в поле для ввода.");
define("_ALLOWEDCHAR","Допустимая максимальная длина (симв.): ");
define("_CURRCHAR","Текущая длина (симв.): ");
define("_PLZCOMPLETE","Пожалуйста задайте тему и заполните остальные поля сообщения.");
define("_MESSAGETOOLONG","Ваше сообщение слишком длинное.");

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 секунда');
define('_SECONDS', '%s секунд');
define('_MINUTE', '1 минута');
define('_MINUTES', '%s минут');
define('_HOUR', '1 час');
define('_HOURS', '%s часов');
define('_DAY', '1 день');
define('_DAYS', '%s дней');
define('_WEEK', '1 неделя');
define('_MONTH', '1 месяц');

define('_HELP', "Помощь");

?>