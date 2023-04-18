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
define( $constpref.'_ADMENU_INDEX_CHECK' , 'Check Setup' ) ;
define( $constpref.'_ADMENU_GOTO_MODULE' , 'View Module' ) ;
define( $constpref.'_ADMENU_GOTO_MANAGER' ,'File Manager' ) ;
define( $constpref.'_ADMENU_DROPBOX' ,     'Dropbox App Token' ) ;
define( $constpref.'_ADMENU_GOOGLEDRIVE' , 'Google Drive API' ) ;
define( $constpref.'_ADMENU_VENDORUPDATE' ,'Update vendor' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN',  'Languages');
define( $constpref.'_ADMENU_MYTPLSADMIN',  'Templates');
define( $constpref.'_ADMENU_MYBLOCKSADMIN','Blocks/Permissions');
define( $constpref.'_ADMENU_MYPREFERENCES','Preferences');

// configurations
define( $constpref.'_MANAGER_TITLE' ,           'Page title of manager' );
define( $constpref.'_MANAGER_TITLE_DESC' ,      '' );
define( $constpref.'_VOLUME_SETTING' ,          'Volume Drivers' );
define( $constpref.'_VOLUME_SETTING_DESC' ,     '<button class="help-admin button" type="button" data-module="xelfinder" data-help-article="#help-volume" title="Help Volume"><b>?</b></button> Configuration options separated by a new line' );
define( $constpref.'_SHARE_FOLDER' ,            'Shared Folder' );
define( $constpref.'_DISABLED_CMDS_BY_GID' ,    'Group policy - disable cmds' );
define( $constpref.'_DISABLED_CMDS_BY_GID_DESC','[GroupID]= Disabled cmds (comma-separated)].<br>Delimiter ID with ":" colon<br>Command list: archive, chmod, cut, duplicate, edit, empty, extract, mkdir, mkfile, paste, perm, put, rename, resize, rm, upload etc...' );
define( $constpref.'_DISABLE_WRITES_GUEST' ,    'Disable writing cmds to guest' );
define( $constpref.'_DISABLE_WRITES_GUEST_DESC','disable writing and modifying, while allowing readingAll writing commands are added to the disabled commands to guests.' );
define( $constpref.'_DISABLE_WRITES_USER' ,     'Disable writing cmds to user' );
define( $constpref.'_DISABLE_WRITES_USER_DESC', 'All writing commands are added to the disabled commands to registed users.' );
define( $constpref.'_ENABLE_IMAGICK_PS' ,       'Enable PostScript of ImageMagick' );
define( $constpref.'_ENABLE_IMAGICK_PS_DESC',   'If <a href="https://www.kb.cert.org/vuls/id/332928" target="_blank">Ghostscript vulnerabilities</a> has been fixed, you can enable PostScript related processing with ImageMagick by selecting "Yes".' );
define( $constpref.'_USE_SHARECAD_PREVIEW' ,    'Enable ShareCAD preview' );
define( $constpref.'_USE_SHARECAD_PREVIEW_DESC','Use ShareCAD to expand preview file types. When ShareCAD Preview is used, it notifies the content URL to ShareCAD.org.' );
define( $constpref.'_USE_GOOGLE_PREVIEW' ,      'Enable Google Docs preview' );
define( $constpref.'_USE_GOOGLE_PREVIEW_DESC',  'Use Google Docs to expand preview file types. When Google Docs Preview is used, it notifies the content URL to Google Docs.' );
define( $constpref.'_USE_OFFICE_PREVIEW' ,      'Enable Office Online preview' );
define( $constpref.'_USE_OFFICE_PREVIEW_DESC',  'Note: Microsoft not only collects use data via the inbuilt telemetry client, but also records and stores the individual use of Connected Services. The content URL is collected by products.office.com' );
define( $constpref.'_MAIL_NOTIFY_GUEST' ,       'E-Mail Notify (Guest)' );
define( $constpref.'_MAIL_NOTIFY_GUEST_DESC',   'Mailing notifies an administrator of file addition by a guest.' );
define( $constpref.'_MAIL_NOTIFY_GROUP' ,       'E-Mail Notify (Groups)' );
define( $constpref.'_MAIL_NOTIFY_GROUP_DESC',   'Mailing notifies an administrator of file addition by selected groups.' );
define( $constpref.'_FTP_NAME' ,                'Name of FTP net volume' );
define( $constpref.'_FTP_NAME_DESC' ,           'The display name of the FTP connection net volume for administrators.' );
define( $constpref.'_FTP_HOST' ,                'FTP Host name' );
define( $constpref.'_FTP_HOST_DESC' ,           '' );
define( $constpref.'_FTP_PORT' ,                'FTP port' );
define( $constpref.'_FTP_PORT_DESC' ,           'default: 21' );
define( $constpref.'_FTP_PATH' ,                'Directory as root' );
define( $constpref.'_FTP_PATH_DESC' ,           'FTP configuration is also used for "ftp" plug-volume driver. Leave blank only for "ftp" plug-in.' );
define( $constpref.'_FTP_USER' ,                'FTP user name' );
define( $constpref.'_FTP_USER_DESC' ,           '' );
define( $constpref.'_FTP_PASS' ,                'FTP password' );
define( $constpref.'_FTP_PASS_DESC' ,           '' );
define( $constpref.'_FTP_SEARCH' ,              'FTP volume integration in Search Results' );
define( $constpref.'_FTP_SEARCH_DESC' ,         'Some firewalls or network routers can disconnect connections and show the “read timed out” error if the server is taking longer to respond and send information.' );
define( $constpref.'_BOXAPI_ID' ,               'Box API OAuth2 client_id' );
define( $constpref.'_BOXAPI_ID_DESC' ,          'Sign in to <a href="https://app.box.com/developers/services" target="_blank">Box API Console ↗ 🌐</a>' );
define( $constpref.'_BOXAPI_SECRET' ,           'Box API OAuth2 client_secret' );
define( $constpref.'_BOXAPI_SECRET_DESC' ,      'To use Box as a network volume, set the redirect_url in the Box API application configuration section :<br><small><pre>'.str_replace('http://','https://',XOOPS_URL).'/modules/'.$mydirname.'/connector.php</pre></small><br>HTTPS is required. Optional paths after domain can be omitted.' );
define( $constpref.'_GOOGLEAPI_ID' ,            'Google API Client ID' );
define( $constpref.'_GOOGLEAPI_ID_DESC' ,       'Sign in to <a href="https://console.developers.google.com" target="_blank">Google API Console ↗ 🌐</a>' );
define( $constpref.'_GOOGLEAPI_SECRET' ,        'Google API Client Secret' );
define( $constpref.'_GOOGLEAPI_SECRET_DESC' ,   'To use Google Drive as a network volume, set redirect_uri in Google Developer Console :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=googledrive&host=1</pre></small>' );
define( $constpref.'_ONEDRIVEAPI_ID' ,          'OneDrive API Application ID' );
define( $constpref.'_ONEDRIVEAPI_ID_DESC' ,     'Sign in to <a href="https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps" target="_blank">Azure Active Directory Registered Apps ↗ 🌐</a>' );
define( $constpref.'_ONEDRIVEAPI_SECRET' ,      'OneDrive API Password' );
define( $constpref.'_ONEDRIVEAPI_SECRET_DESC' , 'To use OneDrive as a network volume, Use this redirect URL in the OneDrive API application settings :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php/netmount/onedrive/1</pre></small>' );
define( $constpref.'_DROPBOX_TOKEN' ,           'Dropbox.com App key' );
define( $constpref.'_DROPBOX_TOKEN_DESC' ,      'Sign in to <a href="https://www.dropbox.com/developers" target="_blank">Dropbox Developers ↗ 🌐</a>' );
define( $constpref.'_DROPBOX_SECKEY' ,          'Dropbox.com App secret' );
define( $constpref.'_DROPBOX_SECKEY_DESC' ,     'The App secret found in the settings page of your Dropbox application. OAuth 2 Redirect URIs :<br><small><pre>'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=dropbox2&host=1</pre></small>' );
define( $constpref.'_DROPBOX_ACC_TOKEN' ,       'Dropbox.com App secret token' );
define( $constpref.'_DROPBOX_ACC_TOKEN_DESC' ,  'The generated access token for the shared Dropbox volume.<br>Sign in to <a href="https://www.dropbox.com/developers/apps" target="_blank">Dropbox.com Developers-Apps ↗ 🌐</a>' );
define( $constpref.'_DROPBOX_ACC_SECKEY' ,      'Dropxbox.com OAuth 1 only [ blank for OAuth2 ]' );
define( $constpref.'_DROPBOX_ACC_SECKEY_DESC' , 'Migrate access tokens or re-authenticate with a new permission API v1 → v2<br>Leave this field empty and use the new API v2 app key.' );
define( $constpref.'_DROPBOX_NAME' ,            'Dropbox.com Shared volume name' );
define( $constpref.'_DROPBOX_NAME_DESC' ,       'Unlike mount of network volume, shared volume name is available to all users.' );
define( $constpref.'_DROPBOX_PATH' ,            'Root pass of shared Dropbox' );
define( $constpref.'_DROPBOX_PATH_DESC' ,       'The path generally indicated in shared Dropbox volume. (example:  "/Public")<br>A Dropbox setup is "dropbox" of a volume driver. It is used also for plugin.<br>"dropbox" When you set to plug-in, please make a root pass into a blank.' );
define( $constpref.'_DROPBOX_HIDDEN_EXT' ,      'Shared Dropbox hidden files' );
define( $constpref.'_DROPBOX_HIDDEN_EXT_DESC' , 'The file (backward match of a file name) displayed only on administrators is specified by comma separated values.<br>It is aimed at a folder when an end is "/".' );
define( $constpref.'_DROPBOX_WRITABLE_GROUPS' , 'Groups which permits full access to Share Dropbox' );
define( $constpref.'_DROPBOX_WRITABLE_GROUPS_DESC' , 'To the group set up here, all the accesses, such as creation, deletion, movement, etc. of file or directory, are permitted. Other groups can only be read.' );
define( $constpref.'_DROPBOX_UPLOAD_MIME' ,     'Shared Dropbox MIME type which can be uploaded') ;
define( $constpref.'_DROPBOX_UPLOAD_MIME_DESC' ,'The MIME type which the group which permits writing can upload. It sets up by comma separated values. Administrators do not receive this restriction.') ;
define( $constpref.'_DROPBOX_WRITE_EXT' ,       'Shared Dropbox Writable files') ;
define( $constpref.'_DROPBOX_WRITE_EXT_DESC' ,  'The backward match of the file name which permits writing to the group which permits writing. It specifies by comma separated values. <br>It is aimed at a folder when an end is "/".<br>Administrators are not restricted.') ;
define( $constpref.'_DROPBOX_UNLOCK_EXT' ,      'Shared Dropbox unlocked files') ;
define( $constpref.'_DROPBOX_UNLOCK_EXT_DESC' , 'Unlocking file can be deleted, moved and renamed.<br>The file (backward match of a file name) which does not lock is specified by comma separated values.<br>It is aimed at a folder when an end is "/".<br>All the files are unlocking at administrators.') ;
define( $constpref.'_JQUERY' ,                  'URL of jQuery' );
define( $constpref.'_JQUERY_DESC' ,             'When not using CDN of Google, URL of "js" of jQuery is specified.' );
define( $constpref.'_JQUERY_UI' ,               'URL of jQuery UI' );
define( $constpref.'_JQUERY_UI_DESC' ,          'When not using CDN of Google, URL of "js" of jQueryUI is specified.' );
define( $constpref.'_JQUERY_UI_CSS' ,           'URL of jQuery UI CSS' );
define( $constpref.'_JQUERY_UI_CSS_DESC' ,      'When not using CDN of Google, URL of "css" of jQueryUI is specified.' );
define( $constpref.'_JQUERY_UI_THEME' ,         'jQuery UI Theme' );
define( $constpref.'_JQUERY_UI_THEME_DESC' ,    'When CDN of Google is used, Theme name or URL to jQuery Theme CSS (Default: smoothness)' );
define( $constpref.'_GMAPS_APIKEY' ,            'Google Maps API Key' );
define( $constpref.'_GMAPS_APIKEY_DESC' ,       'Google Maps API key used in KML preview' );
define( $constpref.'_ZOHO_APIKEY' ,             'Zoho office editor API Key' );
define( $constpref.'_ZOHO_APIKEY_DESC' ,        'Specify the API key when using Zoho office editor when editing Office items. You can to get API key in <a href=""https://www.zoho.com/docs/help/office-apis.html#get-started" target="_blank">www.zoho.com/docs/help/office-apis.html</a>.' );
define( $constpref.'_CREATIVE_CLOUD_APIKEY' ,   'Creative SDK API Key' );
define( $constpref.'_CREATIVE_CLOUD_APIKEY_DESC','Specify the Creative Cloud API key when using Creative SDK image editor of Creative Cloud. <br> API key can be obtained at https://console.adobe.io/ .' );
define( $constpref.'_ONLINE_CONVERT_APIKEY' ,   'ONLINE-CONVERT.COM API Key' );
define( $constpref.'_ONLINE_CONVERT_APIKEY_DESC','Specify the ONLINE-CONVERT.COM API key when using the content converter API of ONLINE-CONVERT.COM.<br>API key can be obtained at https://apiv2.online-convert.com/docs/getting_started/api_key.html .' );
define( $constpref.'_EDITORS_JS',               'URL of editors.js' );
define( $constpref.'_EDITORS_JS_DESC',          'Specify the URL of JavaScript when customizing "common/elfinder/js/extras/editors.default.js".' );
define( $constpref.'_UI_OPTIONS_JS',            'URL of xelfinderUiOptions.js' );
define( $constpref.'_UI_OPTIONS_JS_DESC',       'Specify the URL of JavaScript when customizing "modules/'.$mydirname.'/include/js/xelfinderUiOptions.default.js".' );
define( $constpref.'_THUMBNAIL_SIZE' ,          '[xelfinder_db] Thumbnail size of image insertion' );
define( $constpref.'_THUMBNAIL_SIZE_DESC' ,     'The default value (px) of the thumbnail size at picture insertion by BBcode.' );
define( $constpref.'_DEFAULT_ITEM_PERM' ,       '[xelfinder_db] Permission of new items' );
define( $constpref.'_DEFAULT_ITEM_PERM_DESC' ,  'Permission is three-digit hexadecimal.[File owner][group][Guest]<br>4bit binary number each digit is [Hide][Read][Write][Unlock]<br>744 Owner: 7 =-rwu, group 4 =-r--, Guest 4 =-r--' );
define( $constpref.'_USE_USERS_DIR' ,           '[xelfinder_db] Use of account holder for each user' );
define( $constpref.'_USE_USERS_DIR_DESC' ,      '' );
define( $constpref.'_USERS_DIR_PERM' ,          '[xelfinder_db] Permission of "account holder for each user"' );
define( $constpref.'_USERS_DIR_PERM_DESC' ,     'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br>ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
define( $constpref.'_USERS_DIR_ITEM_PERM' ,     '[xelfinder_db] Permission of the new items in "account holder by user"' );
define( $constpref.'_USERS_DIR_ITEM_PERM_DESC' ,'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br>ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
define( $constpref.'_USE_GUEST_DIR' ,           '[xelfinder_db] Use the account holder for guest' );
define( $constpref.'_USE_GUEST_DIR_DESC' ,      '' );
define( $constpref.'_GUEST_DIR_PERM' ,          '[xelfinder_db] Permission of "account holder for guest"' );
define( $constpref.'_GUEST_DIR_PERM_DESC' ,     'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br>ex. 766: Owner 7 = -rwu, Group 6 = -rw-, Guest 6 = -rw-' );
define( $constpref.'_GUEST_DIR_ITEM_PERM' ,     '[xelfinder_db] Permission of the new items in "account holder for guest"' );
define( $constpref.'_GUEST_DIR_ITEM_PERM_DESC' ,'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br>ex. 744: Owner 7 = -rwu, Group 4 = -r--, Guest 4 = -r--' );
define( $constpref.'_USE_GROUP_DIR' ,           '[xelfinder_db] Use the account holder for each group' );
define( $constpref.'_USE_GROUP_DIR_DESC' ,      '' );
define( $constpref.'_GROUP_DIR_PARENT' ,        '[xelfinder_db] Parent holder name for "account holder for each group"' );
define( $constpref.'_GROUP_DIR_PARENT_DESC' ,   '' );
define( $constpref.'_GROUP_DIR_PARENT_NAME' ,   'For group');
define( $constpref.'_GROUP_DIR_PERM' ,          '[xelfinder_db] Permission of "account holder for each group"' );
define( $constpref.'_GROUP_DIR_PERM_DESC' ,     'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br>ex. 768: Owner 7 = -rwu, Group 6 = -rw-, Guest 8 = h---' );
define( $constpref.'_GROUP_DIR_ITEM_PERM' ,     '[xelfinder_db] Permission of the new items in "account holder for each group"' );
define( $constpref.'_GROUP_DIR_ITEM_PERM_DESC' ,'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br>ex. 748: Owner 7 = -rwu, Group 4 = -r--, Guest 8 = h---' );

define( $constpref.'_UPLOAD_ALLOW_ADMIN' ,      '[xelfinder_db] Upload allow MIME types for Admin' );
define( $constpref.'_UPLOAD_ALLOW_ADMIN_DESC' , 'Specifies the MIME types, separated by a space.<br>all: Allow all, none: Nothing<br>ex. image text/plain' );
define( $constpref.'_AUTO_RESIZE_ADMIN' ,       '[xelfinder_db] Auto resize for Admin (px)' );
define( $constpref.'_AUTO_RESIZE_ADMIN_DESC' ,  'Value(px) which resizes a picture automatically so that it may fit in the specified rectangle size at the time of upload.<br>An input of nothing will not perform automatic resizing.' );
define( $constpref.'_UPLOAD_MAX_ADMIN' ,        '[xelfinder_db]  Allowed max filesize for Admin' );
define( $constpref.'_UPLOAD_MAX_ADMIN_DESC',    'The maximum file size which an administrator can upload is specified. It becomes unlimited with empty or "0". (ex. 10M)' );

define( $constpref.'_SPECIAL_GROUPS' ,          '[xelfinder_db] Special groups' );
define( $constpref.'_SPECIAL_GROUPS_DESC' ,     'Select groups you want to special group. (Multiple Select)' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS' ,   '[xelfinder_db] Upload allow MIME types for Special groups' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS_DESC','' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS' ,    '[xelfinder_db] Auto resize for Special groups (px)' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS_DESC','' );
define( $constpref.'_UPLOAD_MAX_SPGROUPS' ,     '[xelfinder_db] Allowed max filesize for Special groups' );
define( $constpref.'_UPLOAD_MAX_SPGROUPS_DESC', '' );

define( $constpref.'_UPLOAD_ALLOW_USER' ,       '[xelfinder_db] Upload allow MIME types for Registed user' );
define( $constpref.'_UPLOAD_ALLOW_USER_DESC' ,  '' );
define( $constpref.'_AUTO_RESIZE_USER' ,        '[xelfinder_db] Auto resize for Registed user (px)' );
define( $constpref.'_AUTO_RESIZE_USER_DESC',    '' );
define( $constpref.'_UPLOAD_MAX_USER' ,         '[xelfinder_db]max filesize Allowed  for user' );
define( $constpref.'_UPLOAD_MAX_USER_DESC',     '' );

define( $constpref.'_UPLOAD_ALLOW_GUEST' ,      '[xelfinder_db] Mime types allowed for Guest upload' );
define( $constpref.'_UPLOAD_ALLOW_GUEST_DESC' , '' );
define( $constpref.'_AUTO_RESIZE_GUEST' ,       '[xelfinder_db] Auto resize for Guest (px)' );
define( $constpref.'_AUTO_RESIZE_GUEST_DESC',   '' );
define( $constpref.'_UPLOAD_MAX_GUEST' ,        '[xelfinder_db] max filesize Allowed for Guest' );
define( $constpref.'_UPLOAD_MAX_GUEST_DESC',    '' );

define( $constpref.'_DISABLE_PATHINFO' ,        '[xelfinder_db] Disable "PATH_INFO" in file reference URL' );
define( $constpref.'_DISABLE_PATHINFO_DESC' ,   'Select "Yes" for servers where the environment variable "PATH_INFO" is not available.' );

define( $constpref.'_EDIT_DISABLE_LINKED' ,     '[xelfinder_db] Write-protected linked files' );
define( $constpref.'_EDIT_DISABLE_LINKED_DESC' ,'Automatically enables "write-protection" of files to prevent broken links and inadvertent overwriting.' );

define( $constpref.'_CHECK_NAME_VIEW' ,         '[xelfinder_db] Matching of file names in file reference URLs.' );
define( $constpref.'_CHECK_NAME_VIEW_DESC' ,    'If the file name in the file reference URL does not match the registered file name, a "404 Not Found" error is returned.' );

define( $constpref.'_CONNECTOR_URL' ,           'External or secure connection connector URL (optional)' );
define( $constpref.'_CONNECTOR_URL_DESC' ,      'Specify the URL of connector.php when connecting to an external site or when using a secure environment only for communication with the backend.' );

define( $constpref.'_CONN_URL_IS_EXT',          'External connector URL' );
define( $constpref.'_CONN_URL_IS_EXT_DESC',     'Select "Yes" if the specified connector URL is an external site or<br>select "No" if the connector URL is SSL only for back-end communication.<br>When connecting to an external site, this site must be permitted on the other site.' );

define( $constpref.'_ALLOW_ORIGINS',            'Allow domain origin' );
define( $constpref.'_ALLOW_ORIGINS_DESC',       'Set the domains of external sites allowed to connect to this site, separated by newlines, example : "https://example.com" (without the last slash).<br>If the connector URL is a SSL connection only for back-end communication, it is necessary to specify " <strong>'.preg_replace('#^(https?://[^/]+).*$#', '$1', XOOPS_URL).'</strong> ".' );

define( $constpref.'_UNZIP_LANG_VALUE' ,        'Local for unzip' );
define( $constpref.'_UNZIP_LANG_VALUE_DESC' ,   'Local (LANG) for unzip exec' );

define( $constpref.'_AUTOSYNC_SEC_ADMIN',       'Auto sync interval(Admin) : sec' );
define( $constpref.'_AUTOSYNC_SEC_ADMIN_DESC',  'Specify the interval at which the update automatically check in seconds.' );

define( $constpref.'_AUTOSYNC_SEC_SPGROUPS',    'Auto sync interval(Special groups) : sec' );
define( $constpref.'_AUTOSYNC_SEC_SPGROUPS_DESC', '' );

define( $constpref.'_AUTOSYNC_SEC_USER',        'Auto sync interval(Registed user) : sec' );
define( $constpref.'_AUTOSYNC_SEC_USER_DESC',   '' );

define( $constpref.'_AUTOSYNC_SEC_GUEST',       'Auto sync interval(Guest) : sec' );
define( $constpref.'_AUTOSYNC_SEC_GUEST_DESC',  '' );

define( $constpref.'_AUTOSYNC_START',           'Start auto sync as soon' );
define( $constpref.'_AUTOSYNC_START_DESC',      'Can start-stop of the auto sync by "reload" in the context menu.' );

define( $constpref.'_FFMPEG_PATH',              'Path to ffmpeg command' );
define( $constpref.'_FFMPEG_PATH_DESC',         'Specify the path when the path to ffmpeg is required.' );

define( $constpref.'_DEBUG' ,                   'Enable Debug mode' );
define( $constpref.'_DEBUG_DESC' ,              'If it is set in a debug mode, an individual file will be read instead of "elfinder.min.css" and "elfinder.min.js" by elFinder.<br>Moreover, debugging information is included in the response of JavaScript.<br>We recommend "No debug" for performance improvement.' );

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
