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
define( $constpref.'_GOOGLEAPI_SECRET_DESC' ,   'To use Google Drive as a network volume, set redirect_uri in Google Developer Console :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=googledrive&host=1</pre></small>' );
define( $constpref.'_ONEDRIVEAPI_ID' ,          'OneDrive - API Application ID' );
define( $constpref.'_ONEDRIVEAPI_ID_DESC' ,     'Войдите в систему, чтобы <a href="https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps" target="_blank" rel="noopeneer nofollow">Azure Active Directory Registered Apps ↗ 🌐</a>' );
define( $constpref.'_ONEDRIVEAPI_SECRET' ,      'OneDrive - API Password' );
define( $constpref.'_ONEDRIVEAPI_SECRET_DESC' , 'To use OneDrive as a network volume, add this redirect URL into the OneDrive API application settings :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php/netmount/onedrive/1</pre></small>' );
define( $constpref.'_DROPBOX_TOKEN' ,           'Dropbox.com - App key' );
define( $constpref.'_DROPBOX_TOKEN_DESC' ,      'Sign in to <a href="https://www.dropbox.com/developers" target="_blank" rel="noopeneer nofollow">Dropbox Developers ↗ 🌐</a>' );
define( $constpref.'_DROPBOX_SECKEY' ,          'Dropbox.com - App secret' );
define( $constpref.'_DROPBOX_SECKEY_DESC' ,     'The App secret found in the settings page of your Dropbox application. OAuth 2 Redirect URIs :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=dropbox2&host=1</pre></small>' );
define( $constpref.'_DROPBOX_ACC_TOKEN' ,       'Dropbox.com - App secret token' );
define( $constpref.'_DROPBOX_ACC_TOKEN_DESC' ,  'The generated access token for the shared Dropbox volume.<br>Sign in to <a href="https://www.dropbox.com/developers/apps" target="_blank" rel="noopeneer nofollow">Dropbox.com Developers-Apps ↗ 🌐</a>' );
define( $constpref.'_DROPBOX_ACC_SECKEY' ,      'Dropxbox.com - OAuth 1 only [ blank for OAuth2 ]' );
define( $constpref.'_DROPBOX_ACC_SECKEY_DESC' , 'Migrate access tokens or re-authenticate with a new permission API v1 → v2<br>Leave this field empty and use the new API v2 app key.' );
define( $constpref.'_DROPBOX_NAME' ,            'Dropbox.com - Shared volume name' );
define( $constpref.'_DROPBOX_NAME_DESC' ,       'Unlike mount of network volume, shared volume name is available to all users.' );
define( $constpref.'_DROPBOX_PATH' ,            'Dropxbox.com - root path of shared volume' );
define( $constpref.'_DROPBOX_PATH_DESC' ,       'The path of Dropbox shared volume. Example: "/Public"<br>This is also used by the Dropbox plugin volume driver.<br>If you set-up the "dropbox" plug-in, leave this root path blank.' );
define( $constpref.'_DROPBOX_HIDDEN_EXT' ,      'Dropxbox.com - Shared volume hidden files' );
define( $constpref.'_DROPBOX_HIDDEN_EXT_DESC' , 'Hidden files are displayed only to administrators. Specify files name separated by comma ",".<br>This targets a folder that ends with "/"' );
define( $constpref.'_DROPBOX_WRITABLE_GROUPS' , 'Dropxbox.com - Groups with full access' );
define( $constpref.'_DROPBOX_WRITABLE_GROUPS_DESC' , 'Any member of the group can add, edit, delete, share, or download files and folders. Other groups can only read.<br>You can organize members of your team into groups. Share a folder or file with a group to grant access automatically to all group members.' );
define( $constpref.'_DROPBOX_UPLOAD_MIME' ,     'Shared Dropbox MIME type which can be uploaded') ;
define( $constpref.'_DROPBOX_UPLOAD_MIME_DESC' ,'The MIME type which the group which permits writing can upload. It sets up by comma separated values. Administrators do not receive this restriction.') ;
define( $constpref.'_DROPBOX_WRITE_EXT' ,       'Shared Writable files') ;
define( $constpref.'_DROPBOX_WRITE_EXT_DESC' ,  'File permissions are inherited from the group with write permissions. File name separated by comma ",".<br>It targets a folder that ends with "/".<br>Administrators are not restricted.') ;
define( $constpref.'_DROPBOX_UNLOCK_EXT' ,      'Dropxbox.com - Shared unlocked files') ;
define( $constpref.'_DROPBOX_UNLOCK_EXT_DESC' , 'Unlocked file can be deleted, moved and renamed.<br>File name separated by comma ",".<br>It targets a folder that ends with "/".<br>All the files are unlocked for administrators.') ;
define( $constpref.'_JQUERY' ,                  'URL of jQuery JavaScript' );
define( $constpref.'_JQUERY_DESC' ,             'CDN or local URL (recommended self-hosted version)' );
define( $constpref.'_JQUERY_UI' ,               'URL of jQuery UI JavaScript' );
define( $constpref.'_JQUERY_UI_DESC' ,          'CDN or local URL (recommended self-hosted version)' );
define( $constpref.'_JQUERY_UI_CSS' ,           'URL of jQuery UI CSS style sheet' );
define( $constpref.'_JQUERY_UI_CSS_DESC' ,      'CDN or local URL (recommended jQueryUI with CSS custom properties)' );
define( $constpref.'_JQUERY_UI_THEME' ,         'jQuery UI Theme' );
define( $constpref.'_JQUERY_UI_THEME_DESC' ,    'CDN or local URL of jQuery Theme CSS (Default: smoothness)' );
define( $constpref.'_GMAPS_APIKEY' ,            'Google Maps - API Key' );
define( $constpref.'_GMAPS_APIKEY_DESC' ,       'Google Maps - API key used in KML preview' );
define( $constpref.'_ZOHO_APIKEY' ,             'Zoho office editor API Key' );
define( $constpref.'_ZOHO_APIKEY_DESC' ,        'You can to get API key at <a href="https://www.zoho.com/docs/help/office-apis.html#get-started" target="_blank" rel="noopeneer nofollow">Zoho.com office apis ↗ 🌐</a>' );
define( $constpref.'_CREATIVE_CLOUD_APIKEY' ,   'Creative Cloud SDK API Key' );
define( $constpref.'_CREATIVE_CLOUD_APIKEY_DESC','API key can be obtained at <a href="https://console.adobe.io/" target="_blank" rel="noopeneer nofollow">Console Adobe ↗ 🌐</a>' );
define( $constpref.'_ONLINE_CONVERT_APIKEY' ,   'Online-convert.com API Key' );
define( $constpref.'_ONLINE_CONVERT_APIKEY_DESC','You can to get API key at <a href="https://apiv2.online-convert.com/docs/getting_started/api_key.html" target="_blank" rel="noopeneer nofollow">Online-convert API ↗ 🌐</a>' );
define( $constpref.'_EDITORS_JS',               'URL of editors.js' );
define( $constpref.'_EDITORS_JS_DESC',          'Specify the URL of JavaScript to customize editors "common/elfinder/js/extras/editors.default.js"' );
define( $constpref.'_UI_OPTIONS_JS',            'URL of xelfinderUiOptions.js' );
define( $constpref.'_UI_OPTIONS_JS_DESC',       'Specify the URL of JavaScript to customize "modules/'.$mydirname.'/include/js/xelfinderUiOptions.default.js"' );
define( $constpref.'_THUMBNAIL_SIZE' ,          'Thumbnail default insert image size' );
define( $constpref.'_THUMBNAIL_SIZE_DESC' ,     'The default value in pixels of the thumbnail size to insert in BBcode.' );
define( $constpref.'_DEFAULT_ITEM_PERM' ,       'Set permissions for new items' );
define( $constpref.'_DEFAULT_ITEM_PERM_DESC' ,  'Permissions are three-digit hexadecimal [File owner][group][Guest]<br>4bit binary number each digit is for [Hide][Read][Write][Unlock]<br>744 Owner: 7 =-rwu, group 4 =-r--, Guest 4 =-r--' );
define( $constpref.'_USE_USERS_DIR' ,           'Enable account holder for each user' );
define( $constpref.'_USE_USERS_DIR_DESC' ,      '' );
define( $constpref.'_USERS_DIR_PERM' ,          'Set permission of "account holder for each user"' );
define( $constpref.'_USERS_DIR_PERM_DESC' ,     'The parameter here is only a reference when an item is created. Please change permissions directly in elFinder.<br>ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
define( $constpref.'_USERS_DIR_ITEM_PERM' ,     'Set Permission of new items in "account holder for user"' );
define( $constpref.'_USERS_DIR_ITEM_PERM_DESC' ,'The parameter here is only a reference when an item is created. Please change permissions directly in elFinder.<br>ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
define( $constpref.'_USE_GUEST_DIR' ,           'Enable account holder for guest' );
define( $constpref.'_USE_GUEST_DIR_DESC' ,      '' );
define( $constpref.'_GUEST_DIR_PERM' ,          'Set 🛈permissions of "account holder for guest"' );
define( $constpref.'_GUEST_DIR_PERM_DESC' ,     'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder.<br>ex. 766: Owner 7 = -rwu, Group 6 = -rw-, Guest 6 = -rw-' );
define( $constpref.'_GUEST_DIR_ITEM_PERM' ,     'Set permissions of new items in "account holder for guest"' );
define( $constpref.'_GUEST_DIR_ITEM_PERM_DESC' ,'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder.<br>ex. 744: Owner 7 = -rwu, Group 4 = -r--, Guest 4 = -r--' );
define( $constpref.'_USE_GROUP_DIR' ,           'Enable account holder for each group' );
define( $constpref.'_USE_GROUP_DIR_DESC' ,      '' );
define( $constpref.'_GROUP_DIR_PARENT' ,        'Set parent holder name for "account holder for each group"' );
define( $constpref.'_GROUP_DIR_PARENT_DESC' ,   '' );
define( $constpref.'_GROUP_DIR_PARENT_NAME' ,   'Parent For group');
define( $constpref.'_GROUP_DIR_PERM' ,          'Set permissions of "account holder for each group"' );
define( $constpref.'_GROUP_DIR_PERM_DESC' ,     'The parameter here is only a reference when an item is created. Please change permissions directly in elFinder.<br>ex. 768: Owner 7 = -rwu, Group 6 = -rw-, Guest 8 = h---' );
define( $constpref.'_GROUP_DIR_ITEM_PERM' ,     'Set permissions of new items in "account holder for each group"' );
define( $constpref.'_GROUP_DIR_ITEM_PERM_DESC' ,'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder.<br>ex. 748: Owner 7 = -rwu, Group 4 = -r--, Guest 8 = h---' );

define( $constpref.'_UPLOAD_ALLOW_ADMIN' ,      'Allowed MIME Types for Uploads by Admin' );
define( $constpref.'_UPLOAD_ALLOW_ADMIN_DESC' , 'Specify the MIME types, separated by a space.<br>Value to allow all: all. Value to disable all : none<br>Exemple: image text/plain' );
define( $constpref.'_AUTO_RESIZE_ADMIN' ,       'Auto-resize uploads by Admin' );
define( $constpref.'_AUTO_RESIZE_ADMIN_DESC' ,  'Value in pixels to resize an image automatically so that it may fit in the specified rectangle size at the time of upload.<br>Leave empty to disable auto-resize.' );
define( $constpref.'_UPLOAD_MAX_ADMIN' ,        'Allowed maximum file size for Admin' );
define( $constpref.'_UPLOAD_MAX_ADMIN_DESC',    'limit upload with a maximum file size. Leave blank or set "0" for unlimited. Example of maximum value: 10M' );

define( $constpref.'_SPECIAL_GROUPS' ,          'Special groups' );
define( $constpref.'_SPECIAL_GROUPS_DESC' ,     'Select groups you want to set specific permissions. Multiple Select' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS' ,   'Allowed MIME Types for Special groups' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS_DESC','' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS' ,    'Auto-resize uploads by Special groups (px)' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS_DESC','' );
define( $constpref.'_UPLOAD_MAX_SPGROUPS' ,     'Allowed maximum file size for Special groups' );
define( $constpref.'_UPLOAD_MAX_SPGROUPS_DESC', '' );

define( $constpref.'_UPLOAD_ALLOW_USER' ,       'Allowed MIME Types for Registered users' );
define( $constpref.'_UPLOAD_ALLOW_USER_DESC' ,  '' );
define( $constpref.'_AUTO_RESIZE_USER' ,        'Auto-resize uploads by Registered users (px)' );
define( $constpref.'_AUTO_RESIZE_USER_DESC',    '' );
define( $constpref.'_UPLOAD_MAX_USER' ,         'Allowed maximum file size for Registered users' );
define( $constpref.'_UPLOAD_MAX_USER_DESC',     '' );

define( $constpref.'_UPLOAD_ALLOW_GUEST' ,      'Allowed MIME Types for Guests' );
define( $constpref.'_UPLOAD_ALLOW_GUEST_DESC' , '' );
define( $constpref.'_AUTO_RESIZE_GUEST' ,       'Auto-resize uploads by Guests (px)' );
define( $constpref.'_AUTO_RESIZE_GUEST_DESC',   '' );
define( $constpref.'_UPLOAD_MAX_GUEST' ,        'Allowed maximum file size for Guests' );
define( $constpref.'_UPLOAD_MAX_GUEST_DESC',    '' );

define( $constpref.'_DISABLE_PATHINFO' ,        '🚩 Disable "PATH_INFO" in file reference URL' );
define( $constpref.'_DISABLE_PATHINFO_DESC' ,   'Select "Yes" for servers where the environment variable "PATH_INFO" is not available, e.g. NGINX broken image links.' );

define( $constpref.'_EDIT_DISABLE_LINKED' ,     'Write-protected linked files' );
define( $constpref.'_EDIT_DISABLE_LINKED_DESC' ,'Automatically enables "write-protection" of files to prevent broken links and inadvertent overwriting.' );

define( $constpref.'_CHECK_NAME_VIEW' ,         'Matching of file names in file reference URLs' );
define( $constpref.'_CHECK_NAME_VIEW_DESC' ,    'If the file name in the file reference URL does not match the registered file name, return "404 Not Found" error.' );

define( $constpref.'_CONNECTOR_URL' ,           'External or secure connection connector URL (optional)' );
define( $constpref.'_CONNECTOR_URL_DESC' ,      'Specify the URL of connector.php when connecting to an external site or when using a secure environment only for communication with the backend.' );

define( $constpref.'_CONN_URL_IS_EXT',          'External connector URL' );
define( $constpref.'_CONN_URL_IS_EXT_DESC',     'Select "Yes" if the specified connector URL is an external site or<br>select "No" if the connector URL is SSL only for back-end communication.<br>When connecting to an external site, this site must be permitted on the other site.' );

define( $constpref.'_ALLOW_ORIGINS',            'Allow domain origin' );
define( $constpref.'_ALLOW_ORIGINS_DESC',       'Set the external domains sites allowed to connect to this site, separated by newlines<br>Example of website URL without the last slash: "https://example.com"<br>SSL connection to back-end communication requires https : <strong>'.preg_replace('#^(https?://[^/]+).*$#', '$1', XOOPS_URL).'</strong>' );

define( $constpref.'_UNZIP_LANG_VALUE' ,        'Local (Language) for unzip exec' );
define( $constpref.'_UNZIP_LANG_VALUE_DESC' ,   '' );

define( $constpref.'_AUTOSYNC_SEC_ADMIN',       'Auto-sync interval (Admin)' );
define( $constpref.'_AUTOSYNC_SEC_ADMIN_DESC',  'Specify the time interval between synchronization cycles in seconds.' );

define( $constpref.'_AUTOSYNC_SEC_SPGROUPS',    'Auto-sync interval (Special groups)' );
define( $constpref.'_AUTOSYNC_SEC_SPGROUPS_DESC', '' );

define( $constpref.'_AUTOSYNC_SEC_USER',        'Auto-sync interval (Registered user)' );
define( $constpref.'_AUTOSYNC_SEC_USER_DESC',   '' );

define( $constpref.'_AUTOSYNC_SEC_GUEST',       'Auto-sync interval (Guest)' );
define( $constpref.'_AUTOSYNC_SEC_GUEST_DESC',  '' );

define( $constpref.'_AUTOSYNC_START',           'Auto-sync as soon as possible' );
define( $constpref.'_AUTOSYNC_START_DESC',      'Start and stop the auto-sync using "reload" from the context menu.' );

define( $constpref.'_FFMPEG_PATH',              'Path to ffmpeg command' );
define( $constpref.'_FFMPEG_PATH_DESC',         'Specify the path when the path to ffmpeg is required.' );

define( $constpref.'_DEBUG' ,                   'Enable Debug mode' );
define( $constpref.'_DEBUG_DESC' ,              'elFinder reads an individual file instead of "elfinder.min.css" and "elfinder.min.js"<br>Moreover, debugging information is included in the response of JavaScript.<br>Debug is not recommended in production environment.' );

// admin/dropbox.php
define( $constpref.'_DROPBOX_STEP1' ,        'Step 1: Make App');
define( $constpref.'_DROPBOX_GOTO_APP' ,     'Please create App at the following link place (Dropbox.com), acquire App key and App secret, and set to "%s" and "%s" of Preferences.');
define( $constpref.'_DROPBOX_GET_TOKEN' ,    'Get "Dropbox App Token"');
define( $constpref.'_DROPBOX_STEP2' ,        'Step 2: Go to Dropbox and approves');
define( $constpref.'_DROPBOX_GOTO_CONFIRM' , 'Please move on to the following link place (Dropbox.com), and approve an application.');
define( $constpref.'_DROPBOX_CONFIRM_LINK' , 'Go to Dropbox.com and approves an application. ');
define( $constpref.'_DROPBOX_STEP3' ,        'Step 3: Completed. It sets to Preferences.');
define( $constpref.'_DROPBOX_SET_PREF' ,     'Please set the following value as each item of Preferences.');

// admin/googledrive.php
define( $constpref.'_GOOGLEDRIVE_GET_TOKEN', 'Google Drive API');

// admin/composer_update.php
define( $constpref.'_COMPOSER_UPDATE' ,       'Update Vendor - Composer');
define( $constpref.'_COMPOSER_RUN_UPDATE' ,   'Run Composer Update');
define( $constpref.'_COMPOSER_UPDATE_STARTED','Update started. Please wait until the system displays the message "Update was completed" ...');
define( $constpref.'_COMPOSER_DONE_UPDATE' ,  'The Vendor Update was completed.');
define( $constpref.'_COMPOSER_UPDATE_ERROR' , 'The driver might not be installed, or it might not be installed correctly!');
define( $constpref.'_COMPOSER_UPDATE_FAIL',   'The Vendor file does not exist : %s ');
define( $constpref.'_COMPOSER_UPDATE_SUCCESS','The Vendor file exists  %s .');
define( $constpref.'_COMPOSER_UPDATE_TIME' ,  'This can take some time depending on the Internet connection !');
define( $constpref.'_COMPOSER_UPDATE_HELP' ,  'Run composer to update the required packages and re-generate a composer lock file.');
}
