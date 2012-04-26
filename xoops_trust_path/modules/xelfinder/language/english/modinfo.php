<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'xelfinder' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// a flag for this language file has already been read or not.
define( $constpref.'_LOADED' , 1 ) ;

define( $constpref.'_DESC' , 'The module which uses the file manager elFinder of a Web base as an image manager.');

// admin menu
define( $constpref.'_ADMENU_MYLANGADMIN',  'Languages');
define( $constpref.'_ADMENU_MYTPLSADMIN',  'Templates');
define( $constpref.'_ADMENU_MYBLOCKSADMIN','Blocks/Permissions');
define( $constpref.'_ADMENU_MYPREFERENCES','Preferences');

// configurations
define( $constpref.'_VOLUME_SETTING' ,          'Volume Drivers' );
define( $constpref.'_VOLUME_SETTING_DESC' ,     '[Module directory name]:[Plugin name]:[Saved files dirctory path]:[View name]<br />Written line by line. Will be ignored and put a "#" at the beginning.' );
define( $constpref.'_SHARE_HOLDER' ,            'Share holder' );
define( $constpref.'_FTP_NAME' ,                'Name of FTP net volume' );
define( $constpref.'_FTP_NAME_DESC' ,           'The display name of the FTP connection net volume for administrators.' );
define( $constpref.'_FTP_HOST' ,                'FTP Host name' );
define( $constpref.'_FTP_HOST_DESC' ,           '' );
define( $constpref.'_FTP_PORT' ,                'FTP port' );
define( $constpref.'_FTP_PORT_DESC' ,           'default: 21' );
define( $constpref.'_FTP_PATH' ,                'Directory as root' );
define( $constpref.'_FTP_PATH_DESC' ,           '' );
define( $constpref.'_FTP_USER' ,                'FTP user name' );
define( $constpref.'_FTP_USER_DESC' ,           '' );
define( $constpref.'_FTP_PASS' ,                'FTP password' );
define( $constpref.'_FTP_PASS_DESC' ,           '' );
define( $constpref.'_FTP_SEARCH' ,              'FTP volume include in search results' );
define( $constpref.'_FTP_SEARCH_DESC' ,         'If in search results include FTP net volume, the search may time out.' );
define( $constpref.'_DROPBOX_TOKEN' ,           'Dropbox.com App key' );
define( $constpref.'_DROPBOX_TOKEN_DESC' ,      'Developers - Dropbox [ https://www.dropbox.com/developers ]' );
define( $constpref.'_DROPBOX_SECKEY' ,          'Dropbox.com App secret' );
define( $constpref.'_DROPBOX_SECKEY_DESC' ,     '' );
define( $constpref.'_THUMBNAIL_SIZE' ,          'Thumbnail size of image insertion' );
define( $constpref.'_THUMBNAIL_SIZE_DESC' ,     'The default value (px) of the thumbnail size at picture insertion by BBcode.' );
define( $constpref.'_DEFAULT_ITEM_PERM' ,       'Permission of new items' );
define( $constpref.'_DEFAULT_ITEM_PERM_DESC' ,  'Permission is three-digit hexadecimal.[File owner][group][Guest]<br />4bit binary number each digit is [Hide][Read][Write][Unlock]<br />744 Owner: 7 =-rwu, group 4 =-r--, Guest 4 =-r--' );
define( $constpref.'_USE_USERS_DIR' ,           'Use of the holder for each user' );
define( $constpref.'_USE_USERS_DIR_DESC' ,      '' );
define( $constpref.'_USERS_DIR_PERM' ,          'Permission of "holder for each user"' );
define( $constpref.'_USERS_DIR_PERM_DESC' ,     'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br />ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
define( $constpref.'_USERS_DIR_ITEM_PERM' ,     'Permission of the new items in "holder by user"' );
define( $constpref.'_USERS_DIR_ITEM_PERM_DESC' ,'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br />ex. 7cc: Owner 7 = -rwu, Group c = hr--, Guest c = hr--' );
define( $constpref.'_USE_GUEST_DIR' ,           'Use the holder for guest' );
define( $constpref.'_USE_GUEST_DIR_DESC' ,      '' );
define( $constpref.'_GUEST_DIR_PERM' ,          'Permission of "holder for guest"' );
define( $constpref.'_GUEST_DIR_PERM_DESC' ,     'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br />ex. 766: Owner 7 = -rwu, Group 6 = -rw-, Guest 6 = -rw-' );
define( $constpref.'_GUEST_DIR_ITEM_PERM' ,     'Permission of the new items in "holder for guest"' );
define( $constpref.'_GUEST_DIR_ITEM_PERM_DESC' ,'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br />ex. 744: Owner 7 = -rwu, Group 4 = -r--, Guest 4 = -r--' );
define( $constpref.'_USE_GROUP_DIR' ,           'Use the holder for each group' );
define( $constpref.'_USE_GROUP_DIR_DESC' ,      '' );
define( $constpref.'_GROUP_DIR_PARENT' ,        'Parent holder name for "holder for each group"' );
define( $constpref.'_GROUP_DIR_PARENT_DESC' ,   '' );
define( $constpref.'_GROUP_DIR_PARENT_NAME' ,   'Read by group');
define( $constpref.'_GROUP_DIR_PERM' ,          'Permission of "holder for each group"' );
define( $constpref.'_GROUP_DIR_PERM_DESC' ,     'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br />ex. 768: Owner 7 = -rwu, Group 6 = -rw-, Guest 8 = h---' );
define( $constpref.'_GROUP_DIR_ITEM_PERM' ,     'Permission of the new items in "holder for each group"' );
define( $constpref.'_GROUP_DIR_ITEM_PERM_DESC' ,'The setting here is referred to only when it is created. Please change after it is created directly in the elFinder. <br />ex. 748: Owner 7 = -rwu, Group 4 = -r--, Guest 8 = h---' );

define( $constpref.'_UPLOAD_ALLOW_ADMIN' ,      'Upload allow MIME types for Admin' );
define( $constpref.'_UPLOAD_ALLOW_ADMIN_DESC' , 'Specifies the MIME types, separated by a space.<br />all: Allow all, none: Nothing<br />ex. image text/plain' );
define( $constpref.'_AUTO_RESIZE_ADMIN' ,       'Auto resize for Admin (px)' );
define( $constpref.'_AUTO_RESIZE_ADMIN_DESC' ,  'Value(px) which resizes a picture automatically so that it may fit in the specified rectangle size at the time of upload.<br />An input of nothing will not perform automatic resizing.' );

define( $constpref.'_SPECIAL_GROUPS' ,          'Special groups' );
define( $constpref.'_SPECIAL_GROUPS_DESC' ,     'Select groups you want to special group. (Multiple Select)' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS' ,   'Upload allow MIME types for Special groups' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS_DESC','' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS' ,    'Auto resize for Special groups (px)' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS_DESC','' );

define( $constpref.'_UPLOAD_ALLOW_USER' ,       'Upload allow MIME types for Registed user' );
define( $constpref.'_UPLOAD_ALLOW_USER_DESC' ,  '' );
define( $constpref.'_AUTO_RESIZE_USER' ,        'Auto resize for Registed user (px)' );
define( $constpref.'_AUTO_RESIZE_USER_DESC',    '' );

define( $constpref.'_UPLOAD_ALLOW_GUEST' ,      'Upload allow MIME types for Guest' );
define( $constpref.'_UPLOAD_ALLOW_GUEST_DESC' , '' );
define( $constpref.'_AUTO_RESIZE_GUEST' ,       'Auto resize for Guest (px)' );
define( $constpref.'_AUTO_RESIZE_GUEST_DESC',   '' );

define( $constpref.'_DISABLE_PATHINFO' ,        'Disable PathInfo of file reference URL' );
define( $constpref.'_DISABLE_PATHINFO_DESC' ,   '' );

define( $constpref.'_EDIT_DISABLE_LINKED' ,     'Write-protect of linked file' );
define( $constpref.'_EDIT_DISABLE_LINKED_DESC' ,'Write-protect automatically of referenced linked files  for order to prevent inadvertent overwriting or broken links.' );

define( $constpref.'_SSL_CONNECTOR_URL' ,       'Secure connection URL' );
define( $constpref.'_SSL_CONNECTOR_URL_DESC' ,  'When only communication with a back-end uses secure environment, please specify URL of "connector.php" which begins from "https://".<br />It\'s supports only "Firefox", "Chrome" & "Safari".' );

define( $constpref.'_UNZIP_LANG_VALUE' ,        'Local for unzip' );
define( $constpref.'_UNZIP_LANG_VALUE_DESC' ,   'Local (LANG) for unzip exec' );

define( $constpref.'_DEBUG' ,                   'Enable Debug mode' );
define( $constpref.'_DEBUG_DESC' ,              'If it is set in a debug mode, an individual file will be read instead of "elfinder.min.css" and "elfinder.min.js" by elFinder.<br />Moreover, debugging information is included in the response of JavaScript.<br />We recommend "No debug" for performance improvement.' );

}
