<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'xelfinder';
}
$constpref = '_MI_' . strtoupper( $mydirname );

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED' ) ) {

// a flag for this language file has already been read or not.
	define( $constpref . '_LOADED', 1 );

	define( $constpref . '_DESC', 'The module which uses the file manager elFinder of a Web base as an image manager.' );

// admin menu
define( $constpref.'_ADMENU_INDEX_CHECK' , 'Проверьте настройку' ) ;
define( $constpref.'_ADMENU_GOTO_MODULE' , 'Модуль просмотра' ) ;
define( $constpref.'_ADMENU_GOTO_MANAGER' ,'Файловый менеджер' ) ;
define( $constpref.'_ADMENU_DROPBOX' ,     'Токен приложения Dropbox' ) ;
define( $constpref.'_ADMENU_GOOGLEDRIVE' , 'API Google Диска' ) ;
define( $constpref.'_ADMENU_VENDORUPDATE' ,'Поставщик обновлений' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN',  'Язык');
define( $constpref.'_ADMENU_MYTPLSADMIN',  'Шаблоны');
define( $constpref.'_ADMENU_MYBLOCKSADMIN','Разрешения');
define( $constpref.'_ADMENU_MYPREFERENCES','Предпочтения');

// configurations
define( $constpref.'_MANAGER_TITLE' ,           'Название страницы файлового менеджера' );
define( $constpref.'_MANAGER_TITLE_DESC' ,      '' );
define( $constpref.'_VOLUME_SETTING' ,          'Драйверы громкости' );
define( $constpref.'_VOLUME_SETTING_DESC' ,     '<button class="help-admin button" type="button" data-module="xelfinder" data-help-article="#help-volume" title="Справка по драйверу громкости"><b>?</b></button> Параметры конфигурации, разделенные новой строкой' );
define( $constpref.'_SHARE_FOLDER' ,            'Shared Folder' );
define( $constpref.'_DISABLED_CMDS_BY_GID' ,    'Настройки групповой политики - Отключить команды' );
define( $constpref.'_DISABLED_CMDS_BY_GID_DESC','[GroupID]= отключенные команды (разделяются запятой ",")<br>значение по умолчанию: 3=mkdir,paste,archive,extract.<br>Добавьте новый идентификатор группы и отключите команды с разделителем двоеточием ":"<br>Список команд: archive, chmod, cut, duplicate, edit, empty, extract, mkdir, mkfile, paste, perm, put, rename, resize, rm, upload etc...' );
define( $constpref.'_DISABLE_WRITES_GUEST' ,    'Настройки групповой политики - Отключить запись команд для гостей' );
define( $constpref.'_DISABLE_WRITES_GUEST_DESC','Все команды записи недоступны для гостей.<br>Ограничьте запись и изменение, разрешив при этом чтение.' );
define( $constpref.'_DISABLE_WRITES_USER' ,     'Настройки групповой политики - Отключить запись команд пользователям' );
define( $constpref.'_DISABLE_WRITES_USER_DESC', 'Все команды записи отключены для зарегистрированных пользователей.' );
define( $constpref.'_ENABLE_IMAGICK_PS' ,       'Включить PostScript для ImageMagick' );
define( $constpref.'_ENABLE_IMAGICK_PS_DESC',   'Исправлены ли уязвимости в <a href="https://www.kb.cert.org/vuls/id/332928" target="_blank" rel="noopener nofollow">Ghostscript ↗ 🌐</a>, вы можете включить обработку, связанную с PostScript, с помощью ImageMagick, выбрав "Да".' );
define( $constpref.'_USE_SHARECAD_PREVIEW' ,    'Enable ShareCAD preview' );
define( $constpref.'_USE_SHARECAD_PREVIEW_DESC','Используйте Sharecash для расширения типов файлов предварительного просмотра с помощью бесплатного сервиса <a href="https://sharecad.org/de/Home/PrivacyPolicy" target="_blank" rel="noopeneer nofollow">ShareCAD.org [ политика конфиденциальности ] ↗ 🌐</a>' );
define( $constpref.'_USE_GOOGLE_PREVIEW' ,      'Включить предварительный просмотр документов Google' );
define( $constpref.'_USE_GOOGLE_PREVIEW_DESC',  'Используйте Google Docs для расширения списка типов файлов предварительного просмотра. Пожалуйста, ознакомьтесь с Политикой конфиденциальности Google Docs.' );
define( $constpref.'_USE_OFFICE_PREVIEW' ,      'Включить предварительный просмотр Office Online' );
define( $constpref.'_USE_OFFICE_PREVIEW_DESC',  'Примечание: Корпорация Майкрософт не только собирает пользовательские данные с помощью встроенного клиента телеметрии, но также записывает и сохраняет индивидуальное использование подключенных служб. URL-адрес контента собирается с помощью products.office.com' );
define( $constpref.'_MAIL_NOTIFY_GUEST' ,       'Включить уведомление по электронной почте - Загрузка гостем' );
define( $constpref.'_MAIL_NOTIFY_GUEST_DESC',   'Уведомить администратора о файле, загруженном гостем.' );
define( $constpref.'_MAIL_NOTIFY_GROUP' ,       'Включить уведомление по электронной почте - Группы пользователей' );
define( $constpref.'_MAIL_NOTIFY_GROUP_DESC',   'Notify the administrator about files uploaded by selected groups.' );
define( $constpref.'_FTP_NAME' ,                'FTP - сетевой объем' );
define( $constpref.'_FTP_NAME_DESC' ,           'Отобразите имя сетевого тома FTP-соединения для администраторов.' );
define( $constpref.'_FTP_HOST' ,                'FTP - Имя хоста' );
define( $constpref.'_FTP_HOST_DESC' ,           '' );
define( $constpref.'_FTP_PORT' ,                'FTP - порт. Значение по умолчанию: 21' );
define( $constpref.'_FTP_PORT_DESC' ,           '' );
define( $constpref.'_FTP_PATH' ,                'FTP - путь к корневому каталогу' );
define( $constpref.'_FTP_PATH_DESC' ,           'Эта конфигурация также используется для плагина "ftp"-драйвера тома. Оставьте пустым только для подключаемого модуля "ftp".' );
define( $constpref.'_FTP_USER' ,                'FTP - имя пользователя' );
define( $constpref.'_FTP_USER_DESC' ,           '' );
define( $constpref.'_FTP_PASS' ,                'FTP - пароль' );
define( $constpref.'_FTP_PASS_DESC' ,           '' );
define( $constpref.'_FTP_SEARCH' ,              'Интеграция FTP - тома в результатах поиска' );
define( $constpref.'_FTP_SEARCH_DESC' ,         'Некоторые брандмауэры или сетевые маршрутизаторы могут отключать соединения и выдавать ошибку “время ожидания чтения истекло”, если серверу требуется больше времени для ответа и отправки информации.' );
define( $constpref.'_BOXAPI_ID' ,               'Box - API OAuth2 client_id' );
define( $constpref.'_BOXAPI_ID_DESC' ,          'Войдите в систему, чтобы <a href="https://app.box.com/developers/services" target="_blank" rel="noopeneer nofollow">Box API Console ↗ 🌐</a>' );
define( $constpref.'_BOXAPI_SECRET' ,           'Box - API OAuth2 client_secret' );
define( $constpref.'_BOXAPI_SECRET_DESC' ,      'Чтобы использовать Box в качестве сетевого тома, установите redirect_url в разделе конфигурации приложения Box API:<br><small><pre>'.str_replace('http://','https://',XOOPS_URL).'/modules/'.$mydirname.'/connector.php</pre></small><br>Требуется протокол HTTPS. Необязательные пути после домена могут быть опущены.' );
define( $constpref.'_GOOGLEAPI_ID' ,            'Google API - Client ID' );
define( $constpref.'_GOOGLEAPI_ID_DESC' ,       'Войдите в систему, чтобы <a href="https://console.developers.google.com" target="_blank" rel="noopeneer nofollow">Google API Console ↗ 🌐</a>' );
define( $constpref.'_GOOGLEAPI_SECRET' ,        'Google API - Client Secret' );
define( $constpref.'_GOOGLEAPI_SECRET_DESC' ,   'Чтобы использовать Google Диск в качестве сетевого тома, установите параметр redirect_uri в консоли разработчика Google :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=googledrive&host=1</pre></small>' );
define( $constpref.'_ONEDRIVEAPI_ID' ,          'OneDrive - API Application ID' );
define( $constpref.'_ONEDRIVEAPI_ID_DESC' ,     'Войдите в систему, чтобы <a href="https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps" target="_blank" rel="noopeneer nofollow">Azure Active Directory Registered Apps ↗ 🌐</a>' );
define( $constpref.'_ONEDRIVEAPI_SECRET' ,      'OneDrive - API Password' );
define( $constpref.'_ONEDRIVEAPI_SECRET_DESC' , 'Чтобы использовать OneDrive в качестве сетевого тома, добавьте этот URL-адрес перенаправления в настройки приложения OneDrive API:<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php/netmount/onedrive/1</pre></small>' );
define( $constpref.'_DROPBOX_TOKEN' ,           'Dropbox.com - App key' );
define( $constpref.'_DROPBOX_TOKEN_DESC' ,      'Войдите в систему, чтобы <a href="https://www.dropbox.com/developers" target="_blank" rel="noopeneer nofollow">Dropbox Developers ↗ 🌐</a>' );
define( $constpref.'_DROPBOX_SECKEY' ,          'Dropbox.com - App secret' );
define( $constpref.'_DROPBOX_SECKEY_DESC' ,     'Секрет приложения находится на странице настроек вашего приложения Dropbox. URI перенаправления OAuth2 :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=dropbox2&host=1</pre></small>' );
define( $constpref.'_DROPBOX_ACC_TOKEN' ,       'Dropbox.com - Секретный токен приложения' );
define( $constpref.'_DROPBOX_ACC_TOKEN_DESC' ,  'Сгенерированный токен доступа для общего тома Dropbox.<br>Войдите в систему, чтобы <a href="https://www.dropbox.com/developers/apps" target="_blank" rel="noopeneer nofollow">Dropbox.com Developers-Apps ↗ 🌐</a>' );
define( $constpref.'_DROPBOX_ACC_SECKEY' ,      'Dropxbox.com - Только OAuth1 [ пусто для OAuth2 ]' );
define( $constpref.'_DROPBOX_ACC_SECKEY_DESC' , 'Перенесите токены доступа или повторно выполните аутентификацию с помощью нового API разрешений v1 → v2<br>Оставьте это поле пустым и используйте новый ключ приложения API v2.' );
define( $constpref.'_DROPBOX_NAME' ,            'Dropbox.com - Имя общего тома' );
define( $constpref.'_DROPBOX_NAME_DESC' ,       'В отличие от подключения сетевого тома, общий том доступен всем пользователям.' );
define( $constpref.'_DROPBOX_PATH' ,            'Dropxbox.com - корневой путь к общему тому' );
define( $constpref.'_DROPBOX_PATH_DESC' ,       'Путь к общему тому Dropbox. Пример: "/Public"<br>Это также используется драйвером громкости плагина Dropbox.<br>Если вы настраиваете подключаемый модуль "dropbox", оставьте этот корневой путь пустым.' );
define( $constpref.'_DROPBOX_HIDDEN_EXT' ,      'Dropxbox.com - Скрытые файлы в общем томе' );
define( $constpref.'_DROPBOX_HIDDEN_EXT_DESC' , 'Скрытые файлы отображаются только администраторам. Укажите имена файлов, разделенные запятой ",".<br>Это нацелено на папку, которая заканчивается косой чертой "/"' );
define( $constpref.'_DROPBOX_WRITABLE_GROUPS' , 'Dropxbox.com - Группы с полным доступом' );
define( $constpref.'_DROPBOX_WRITABLE_GROUPS_DESC' , 'Любой член группы может добавлять, редактировать, удалять, предоставлять общий доступ или загружать файлы и папки. Другие группы могут только читать.<br>Вы можете организовать членов своей команды в группы. Предоставьте общий доступ к папке или файлу группе, чтобы автоматически предоставить доступ всем членам группы.' );
define( $constpref.'_DROPBOX_UPLOAD_MIME' ,     'Dropbox.com - Тип файла MIME, который можно загрузить в общий том') ;
define( $constpref.'_DROPBOX_UPLOAD_MIME_DESC' ,'Файл MIME-типа, разрешенный к загрузке для группы с правами на запись. Разделяйте значения запятой. Администраторы не ограничены.') ;
define( $constpref.'_DROPBOX_WRITE_EXT' ,       'Shared files available for recording') ;
define( $constpref.'_DROPBOX_WRITE_EXT_DESC' ,  'Права доступа к файлам наследуются от группы с правами на запись. Имя файла, разделенное запятой ",".<br>Он нацелен на папку, которая заканчивается на "/".<br>Администраторы не ограничены.') ;
define( $constpref.'_DROPBOX_UNLOCK_EXT' ,      'Dropxbox.com - Общие разблокированные файлы') ;
define( $constpref.'_DROPBOX_UNLOCK_EXT_DESC' , 'Разблокированный файл может быть удален, перемещен и переименован.<br>Имя файла, разделенное запятой ",".<br>Он нацелен на папку, которая заканчивается на "/".<br>Все файлы разблокированы для администраторов.') ;
define( $constpref.'_JQUERY' ,                  'URL-адрес jQuery JavaScript' );
define( $constpref.'_JQUERY_DESC' ,             'CDN или локальный URL (рекомендуется локальная версия)' );
define( $constpref.'_JQUERY_UI' ,               'URL-адрес jQuery-UI JavaScript' );
define( $constpref.'_JQUERY_UI_DESC' ,          'CDN или локальный URL (рекомендуется локальная версия)' );
define( $constpref.'_JQUERY_UI_CSS' ,           'URL-адрес jQuery-UI CSS style sheet' );
define( $constpref.'_JQUERY_UI_CSS_DESC' ,      'CDN или локальный URL (рекомендуется локальная версия)' );
define( $constpref.'_JQUERY_UI_THEME' ,         'jQuery-UI Theme' );
define( $constpref.'_JQUERY_UI_THEME_DESC' ,    'CDN или локальный URL (рекомендуется локальная версия)' );
define( $constpref.'_GMAPS_APIKEY' ,            'Google Maps - API Key' );
define( $constpref.'_GMAPS_APIKEY_DESC' ,       'Google Maps - Ключ API, используемый в предварительном просмотре KML' );
define( $constpref.'_ZOHO_APIKEY' ,             'Zoho office editor API Key' );
define( $constpref.'_ZOHO_APIKEY_DESC' ,        'Вы можете получить ключ API по адресу <a href="https://www.zoho.com/docs/help/office-apis.html#get-started" target="_blank" rel="noopeneer nofollow">Zoho.com office apis ↗ 🌐</a>' );
define( $constpref.'_CREATIVE_CLOUD_APIKEY' ,   'Creative Cloud SDK API Key' );
define( $constpref.'_CREATIVE_CLOUD_APIKEY_DESC','AВы можете получить ключ API по адресу <a href="https://console.adobe.io/" target="_blank" rel="noopeneer nofollow">Console Adobe ↗ 🌐</a>' );
define( $constpref.'_ONLINE_CONVERT_APIKEY' ,   'Online-convert.com API Key' );
define( $constpref.'_ONLINE_CONVERT_APIKEY_DESC','Вы можете получить ключ API по адресу <a href="https://apiv2.online-convert.com/docs/getting_started/api_key.html" target="_blank" rel="noopeneer nofollow">Online-convert API ↗ 🌐</a>' );
define( $constpref.'_EDITORS_JS',               'URL-адрес editors.js' );
define( $constpref.'_EDITORS_JS_DESC',          'Укажите URL-адрес JavaScript для настройки редакторов "common/elfinder/js/extras/editors.default.js"' );
define( $constpref.'_UI_OPTIONS_JS',            'URL-адрес xelfinderUiOptions.js' );
define( $constpref.'_UI_OPTIONS_JS_DESC',       'Укажите URL-адрес JavaScript для настройки "modules/'.$mydirname.'/include/js/xelfinderUiOptions.default.js"' );
define( $constpref.'_THUMBNAIL_SIZE' ,          'Размер миниатюры изображения для вставки по умолчанию' );
define( $constpref.'_THUMBNAIL_SIZE_DESC' ,     'Значение по умолчанию в пикселях размера миниатюры для вставки в BBCode.' );
define( $constpref.'_DEFAULT_ITEM_PERM' ,       'Установите разрешения для новых элементов' );
define( $constpref.'_DEFAULT_ITEM_PERM_DESC' ,  'Разрешения указаны в трехзначном шестнадцатеричном формате [Владелец файла] [группа][Гость]<br>4-разрядное двоичное число, каждая цифра относится к [скрыть] [показания] [записи] [разблокировки]<br>744 Owner: 7 =-rwu, group 4 =-r--, Guest 4 =-r--' );
define( $constpref.'_USE_USERS_DIR' ,           'Включите учетную запись для каждого пользователя' );
define( $constpref.'_USE_USERS_DIR_DESC' ,      '' );
define( $constpref.'_USERS_DIR_PERM' ,          'Установите разрешения для "учетной записи для каждого пользователя"' );
define( $constpref.'_USERS_DIR_PERM_DESC' ,     'Параметр там является ссылкой только при создании элемента. Пожалуйста, измените разрешения непосредственно в elFinder.<br>ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
define( $constpref.'_USERS_DIR_ITEM_PERM' ,     'Установите разрешение для новых элементов в разделе "учетной записи для каждого пользователя".' );
define( $constpref.'_USERS_DIR_ITEM_PERM_DESC' ,'Параметр там является ссылкой только при создании элемента. Пожалуйста, измените разрешения непосредственно в elFinder.<br>ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
define( $constpref.'_USE_GUEST_DIR' ,           'Включите учетную запись для гостя' );
define( $constpref.'_USE_GUEST_DIR_DESC' ,      '' );
define( $constpref.'_GUEST_DIR_PERM' ,          'Установите разрешения "учетной записи для гостя"' );
define( $constpref.'_GUEST_DIR_PERM_DESC' ,     'Параметр там является ссылкой только при создании элемента. Пожалуйста, измените разрешения непосредственно в elFinder.<br>ex. 766: Owner 7 = -rwu, Group 6 = -rw-, Guest 6 = -rw-' );
define( $constpref.'_GUEST_DIR_ITEM_PERM' ,     'Установите разрешения для новых элементов в "учетной записи для гостя"' );
define( $constpref.'_GUEST_DIR_ITEM_PERM_DESC' ,'Параметр там является ссылкой только при создании элемента. Пожалуйста, измените разрешения непосредственно в elFinder.<br>ex. 744: Owner 7 = -rwu, Group 4 = -r--, Guest 4 = -r--' );
define( $constpref.'_USE_GROUP_DIR' ,           'Включить учетную запись для каждой группы' );
define( $constpref.'_USE_GROUP_DIR_DESC' ,      '' );
define( $constpref.'_GROUP_DIR_PARENT' ,        'Задайте имя родительской папки для "учетной записи для каждой группы".' );
define( $constpref.'_GROUP_DIR_PARENT_DESC' ,   '' );
define( $constpref.'_GROUP_DIR_PARENT_NAME' ,   'Имя родительской папки для группы');
define( $constpref.'_GROUP_DIR_PERM' ,          'Установите разрешения для "учетной записи для каждой группы"' );
define( $constpref.'_GROUP_DIR_PERM_DESC' ,     'Параметр там является ссылкой только при создании элемента. Пожалуйста, измените разрешения непосредственно в elFinder.<br>ex. 768: Owner 7 = -rwu, Group 6 = -rw-, Guest 8 = h---' );
define( $constpref.'_GROUP_DIR_ITEM_PERM' ,     'Установите разрешения для новых элементов для "учетной записи для каждой группы"' );
define( $constpref.'_GROUP_DIR_ITEM_PERM_DESC' ,'Параметр там является ссылкой только при создании элемента. Пожалуйста, измените разрешения непосредственно в elFinder.<br>ex. 748: Owner 7 = -rwu, Group 4 = -r--, Guest 8 = h---' );

define( $constpref.'_UPLOAD_ALLOW_ADMIN' ,      'Разрешенные типы MIME для загрузки администратором' );
define( $constpref.'_UPLOAD_ALLOW_ADMIN_DESC' , 'Укажите типы MIME, разделенные пробелом.<br>Значение для разрешения всего: all. Значение для отключения всех : нет<br>Пример: image text/plain' );
define( $constpref.'_AUTO_RESIZE_ADMIN' ,       'Автоматическое изменение размера загружаемых файлов администратором' );
define( $constpref.'_AUTO_RESIZE_ADMIN_DESC' ,  'Значение в пикселях для автоматического изменения размера изображения таким образом, чтобы оно соответствовало указанному размеру прямоугольника во время загрузки.<br>Оставьте пустым, чтобы отключить автоматическое изменение размера.' );
define( $constpref.'_UPLOAD_MAX_ADMIN' ,        'Разрешенный максимальный размер файла для администратора' );
define( $constpref.'_UPLOAD_MAX_ADMIN_DESC',    'Ограничьте загрузку максимальным размером файла. Оставьте поле пустым или установите "0" для неограниченного количества. Пример максимального значения: 10M' );

define( $constpref.'_SPECIAL_GROUPS' ,          'Специальные группы' );
define( $constpref.'_SPECIAL_GROUPS_DESC' ,     'Выберите группы, которым вы хотите предоставить определенные разрешения. Множественный выбор.' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS' ,   'Разрешенные типы файлов MIME для специальных групп' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS_DESC','' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS' ,    'Автоматическое изменение размера загружаемых файлов по специальным группам (px)' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS_DESC','' );
define( $constpref.'_UPLOAD_MAX_SPGROUPS' ,     'Допустимый максимальный размер файла для специальных групп' );
define( $constpref.'_UPLOAD_MAX_SPGROUPS_DESC', '' );

define( $constpref.'_UPLOAD_ALLOW_USER' ,       'Разрешенные типы MIME для зарегистрированных пользователей' );
define( $constpref.'_UPLOAD_ALLOW_USER_DESC' ,  '' );
define( $constpref.'_AUTO_RESIZE_USER' ,        'Автоматическое изменение размера загрузок зарегистрированными пользователями (в пикселях)' );
define( $constpref.'_AUTO_RESIZE_USER_DESC',    '' );
define( $constpref.'_UPLOAD_MAX_USER' ,         'Разрешенный максимальный размер файла для зарегистрированных пользователей' );
define( $constpref.'_UPLOAD_MAX_USER_DESC',     '' );

define( $constpref.'_UPLOAD_ALLOW_GUEST' ,      'Разрешенные типы MIME для гостей' );
define( $constpref.'_UPLOAD_ALLOW_GUEST_DESC' , '' );
define( $constpref.'_AUTO_RESIZE_GUEST' ,       'Автоматическое изменение размера загружаемых файлов гостями (в пикселях)' );
define( $constpref.'_AUTO_RESIZE_GUEST_DESC',   '' );
define( $constpref.'_UPLOAD_MAX_GUEST' ,        'Разрешенный максимальный размер файла для гостей' );
define( $constpref.'_UPLOAD_MAX_GUEST_DESC',    '' );

define( $constpref.'_DISABLE_PATHINFO' ,        '🚩 Отключите "PATH_INFO" в URL-адресе ссылки на файл' );
define( $constpref.'_DISABLE_PATHINFO_DESC' ,   'Выберите "Да" для серверов, где переменная окружения "PATH_INFO" недоступна, например, ссылки на неработающие изображения NGINX.' );

define( $constpref.'_EDIT_DISABLE_LINKED' ,     'Связанные файлы, защищенные от записи' );
define( $constpref.'_EDIT_DISABLE_LINKED_DESC' ,'Автоматически включает "защиту от записи" файлов для предотвращения неработающих ссылок и любой непреднамеренной перезаписи.' );

define( $constpref.'_CHECK_NAME_VIEW' ,         'Совпадение имен файлов в URL-адресах ссылок на файлы' );
define( $constpref.'_CHECK_NAME_VIEW_DESC' ,    'Если имя файла в URL-адресе ссылки на файл не совпадает с зарегистрированным именем файла, верните ошибку "404 не найдено".' );

define( $constpref.'_CONNECTOR_URL' ,           'Внешнее подключение или URL-адрес защищенного разъема (необязательно)' );
define( $constpref.'_CONNECTOR_URL_DESC' ,      'Укажите URL-адрес connector.php при подключении к внешнему сайту или при использовании защищенной среды только для связи с серверной частью.' );

define( $constpref.'_CONN_URL_IS_EXT',          'URL-адрес внешнего соединителя' );
define( $constpref.'_CONN_URL_IS_EXT_DESC',     'Выберите "Да", если указанный URL-адрес соединителя является внешним сайтом или<br>выберите "Нет", если URL-адрес соединителя является SSL только для внутренней связи.<br>Удаленный веб-сайт должен предоставить вашему сайту разрешения на подключение.' );

define( $constpref.'_ALLOW_ORIGINS',            'Разрешить происхождение домена' );
define( $constpref.'_ALLOW_ORIGINS_DESC',       'Укажите удаленные домены, которым разрешено подключаться к этому сайту, разделенные символами новой строки<br>Пример удаленного домена, URL веб-сайта без последней косой черты: "https://example.com "<br>Для SSL-соединения с серверной частью требуется протокол https : <strong>'.preg_replace('#^(https?://[^/]+).*$#', '$1', XOOPS_URL).'</strong>' );

define( $constpref.'_UNZIP_LANG_VALUE' ,        'Локальный (язык) для unzip exec' );
define( $constpref.'_UNZIP_LANG_VALUE_DESC' ,   '' );

define( $constpref.'_AUTOSYNC_SEC_ADMIN',       'Интервал автоматической синхронизации (администратор)' );
define( $constpref.'_AUTOSYNC_SEC_ADMIN_DESC',  'Укажите интервал между циклами синхронизации - время в секундах.' );

define( $constpref.'_AUTOSYNC_SEC_SPGROUPS',    'Интервал автоматической синхронизации (специальные группы)' );
define( $constpref.'_AUTOSYNC_SEC_SPGROUPS_DESC', '' );

define( $constpref.'_AUTOSYNC_SEC_USER',        'Интервал автоматической синхронизации (зарегистрированный пользователь)' );
define( $constpref.'_AUTOSYNC_SEC_USER_DESC',   '' );

define( $constpref.'_AUTOSYNC_SEC_GUEST',       'Интервал автоматической синхронизации (гостевой)' );
define( $constpref.'_AUTOSYNC_SEC_GUEST_DESC',  '' );

define( $constpref.'_AUTOSYNC_START',           'Автоматическая синхронизация как можно скорее' );
define( $constpref.'_AUTOSYNC_START_DESC',      'Запустите и остановите автосинхронизацию с помощью команды "перезагрузить" из контекстного меню.' );

define( $constpref.'_FFMPEG_PATH',              'Путь к команде ffmpeg' );
define( $constpref.'_FFMPEG_PATH_DESC',         'Укажите путь, если требуется ffmpeg.' );

define( $constpref.'_DEBUG' ,                   'Включить режим отладки' );
define( $constpref.'_DEBUG_DESC' ,              'X-elFinder считывает отдельный файл вместо "elfinder.min.css" и "elfinder.min.js"<br>Более того, отладочная информация включена в ответ JavaScript.<br>Отладка не рекомендуется в производственной среде.' );

// admin/dropbox.php
define( $constpref.'_DROPBOX_STEP1' ,        'Step 1: Создать приложение');
define( $constpref.'_DROPBOX_GOTO_APP' ,     'Пожалуйста, перейдите по ссылке, чтобы создать свое приложение (Dropbox.com), получите ключ приложения и секрет приложения и установите значение "%s" и "%s" из настроек модуля.');
define( $constpref.'_DROPBOX_GET_TOKEN' ,    'Получите "Токен приложения Dropbox"');
define( $constpref.'_DROPBOX_STEP2' ,        'Step 2: Перейдите в Dropbox и подтвердите');
define( $constpref.'_DROPBOX_GOTO_CONFIRM' , 'Пожалуйста, перейдите по ссылке по адресу (Dropbox.com ), и одобрите заявку.');
define( $constpref.'_DROPBOX_CONFIRM_LINK' , 'Перейти к Dropbox.com и одобрите заявку.');
define( $constpref.'_DROPBOX_STEP3' ,        'Step 3: Завершенный. Настройка в настройках модуля.');
define( $constpref.'_DROPBOX_SET_PREF' ,     'Добавьте следующее значение для каждого элемента настроек модуля.');

// admin/googledrive.php
define( $constpref.'_GOOGLEDRIVE_GET_TOKEN', 'Google Drive API');

// admin/composer_update.php
define( $constpref.'_COMPOSER_UPDATE' ,       'Поставщик обновлений - Составитель');
define( $constpref.'_COMPOSER_RUN_UPDATE' ,   'Запустите обновление Composer Update');
define( $constpref.'_COMPOSER_UPDATE_STARTED','Началось обновление. Пожалуйста, подождите, пока система не отобразит сообщение "Обновление завершено". ...');
define( $constpref.'_COMPOSER_DONE_UPDATE' ,  'Обновление поставщика было завершено.');
define( $constpref.'_COMPOSER_UPDATE_ERROR' , 'Возможно, драйвер не установлен или он установлен неправильно!');
define( $constpref.'_COMPOSER_UPDATE_FAIL',   'Файл поставщика не существует : %s ');
define( $constpref.'_COMPOSER_UPDATE_SUCCESS','Файл поставщика существует  %s .');
define( $constpref.'_COMPOSER_UPDATE_TIME' ,  'Это может занять некоторое время в зависимости от подключения к Интернету!');
define( $constpref.'_COMPOSER_UPDATE_HELP' ,  'Запустите composer, чтобы обновить необходимые пакеты и повторно сгенерировать файл блокировки composer.');
}
