<?php

// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'modinfo.php' , $mydirname , $mytrustdirname , false ) ;

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$modversion['name'] = 'xelFinder' ;
//$modversion['name'] = $constpref.'_NAME') ;
$modversion['description'] = constant($constpref.'_DESC');
$modversion['version'] = 0.15 ;
$modversion['credits'] = "Hypweb.net";
$modversion['author'] = "nao-pon" ;
$modversion['help'] = "" ;
$modversion['license'] = "GPL" ;
$modversion['official'] = 0 ;
$modversion['image'] = is_file( $mydirpath.'/module_icon.png' ) ? 'module_icon.png' : 'module_icon.php' ;
$modversion['dirname'] = $mydirname ;
$modversion['trust_dirname'] = $mytrustdirname ;
$modversion['read_any'] = true ;

// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false ;
$modversion['tables'] = array() ;

// Admin things
$modversion['hasAdmin'] = 1 ;
$modversion['adminindex'] = 'admin/index.php' ;
$modversion['adminmenu'] = 'admin/admin_menu.php' ;

// Search
$modversion['hasSearch'] = 0 ;
//$modversion['search']['file'] = 'search.php' ;
//$modversion['search']['func'] = $mydirname.'_global_search' ;

// Menu
$modversion['hasMain'] = 1 ;

// Submenu (just for mainmenu)
$modversion['sub'] = array() ;

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

// Blocks
$modversion['blocks'] = array() ;

// Comments
$modversion['hasComments'] = 0 ;

// Configs
$modversion['config'][] = array(
	'name'			=> 'volume_setting' ,
	'title'			=> $constpref.'_VOLUME_SETTING' ,
	'description'	=> $constpref.'_VOLUME_SETTING_DESC' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'xelfinder:xelfinder_db:uploads/xelfinder:'.constant($constpref.'_SHARE_HOLDER' ).'
xelfinder:xelfinder:uploads/elfinder:elFinder
myalbum:myalbum:uploads/photos:MyAlbum
gnavi:gnavi:uploads/gnavi:GNAVI
mailbbs:mailbbs:modules/mailbbs/imgs:MailBBS'
) ;
$modversion['config'][] = array(
		'name'			=> 'ftp_name' ,
		'title'			=> $constpref.'_FTP_NAME' ,
		'description'	=> $constpref.'_FTP_NAME_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> 'Local-FTP'
) ;
$modversion['config'][] = array(
		'name'			=> 'ftp_host' ,
		'title'			=> $constpref.'_FTP_HOST' ,
		'description'	=> $constpref.'_FTP_HOST_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> 'localhost'
) ;
$modversion['config'][] = array(
		'name'			=> 'ftp_port' ,
		'title'			=> $constpref.'_FTP_PORT' ,
		'description'	=> $constpref.'_FTP_PORT_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> '21'
) ;
$modversion['config'][] = array(
		'name'			=> 'ftp_path' ,
		'title'			=> $constpref.'_FTP_PATH' ,
		'description'	=> $constpref.'_FTP_PATH_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> XOOPS_ROOT_PATH
) ;
$modversion['config'][] = array(
		'name'			=> 'ftp_user' ,
		'title'			=> $constpref.'_FTP_USER' ,
		'description'	=> $constpref.'_FTP_USER_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> ''
) ;
$modversion['config'][] = array(
		'name'			=> 'ftp_pass' ,
		'title'			=> $constpref.'_FTP_PASS' ,
		'description'	=> $constpref.'_FTP_PASS_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> ''
) ;
$modversion['config'][] = array(
		'name'			=> 'ftp_search' ,
		'title'			=> $constpref.'_FTP_SEARCH' ,
		'description'	=> $constpref.'_FTP_SEARCH_DESC' ,
		'formtype'		=> 'yesno' ,
		'valuetype'		=> 'int' ,
		'default'		=> '0'
) ;
$modversion['config'][] = array(
		'name'			=> 'dropbox_token' ,
		'title'			=> $constpref.'_DROPBOX_TOKEN' ,
		'description'	=> $constpref.'_DROPBOX_TOKEN_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> ''
) ;
$modversion['config'][] = array(
		'name'			=> 'dropbox_seckey' ,
		'title'			=> $constpref.'_DROPBOX_SECKEY' ,
		'description'	=> $constpref.'_DROPBOX_SECKEY_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> ''
) ;
$modversion['config'][] = array(
		'name'			=> 'thumbnail_size' ,
		'title'			=> $constpref.'_THUMBNAIL_SIZE' ,
		'description'	=> $constpref.'_THUMBNAIL_SIZE_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> '160'
) ;
$modversion['config'][] = array(
	'name'			=> 'default_item_perm' ,
	'title'			=> $constpref.'_DEFAULT_ITEM_PERM' ,
	'description'	=> $constpref.'_DEFAULT_ITEM_PERM_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '744'
) ;
$modversion['config'][] = array(
	'name'			=> 'use_users_dir' ,
	'title'			=> $constpref.'_USE_USERS_DIR',
	'description'	=> $constpref.'_USE_USERS_DIR_DESC',
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1
) ;
$modversion['config'][] = array(
	'name'			=> 'users_dir_perm' ,
	'title'			=> $constpref.'_USERS_DIR_PERM',
	'description'	=> $constpref.'_USERS_DIR_PERM_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '7cc'
) ;
$modversion['config'][] = array(
	'name'			=> 'users_dir_item_perm' ,
	'title'			=> $constpref.'_USERS_DIR_ITEM_PERM',
	'description'	=> $constpref.'_USERS_DIR_ITEM_PERM_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '7cc'
) ;
$modversion['config'][] = array(
	'name'			=> 'use_guest_dir' ,
	'title'			=> $constpref.'_USE_GUEST_DIR',
	'description'	=> $constpref.'_USE_GUEST_DIR_DESC',
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1
) ;
$modversion['config'][] = array(
	'name'			=> 'guest_dir_perm' ,
	'title'			=> $constpref.'_GUEST_DIR_PERM',
	'description'	=> $constpref.'_GUEST_DIR_PERM_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '766'
) ;
$modversion['config'][] = array(
	'name'			=> 'guest_dir_item_perm' ,
	'title'			=> $constpref.'_GUEST_DIR_ITEM_PERM',
	'description'	=> $constpref.'_GUEST_DIR_ITEM_PERM_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '777'
) ;
$modversion['config'][] = array(
	'name'			=> 'use_group_dir' ,
	'title'			=> $constpref.'_USE_GROUP_DIR',
	'description'	=> $constpref.'_USE_GROUP_DIR_DESC',
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1
) ;
$modversion['config'][] = array(
	'name'			=> 'group_dir_parent' ,
	'title'			=> $constpref.'_GROUP_DIR_PARENT',
	'description'	=> $constpref.'_GROUP_DIR_PARENT_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> constant($constpref.'_GROUP_DIR_PARENT_NAME')
) ;
$modversion['config'][] = array(
	'name'			=> 'group_dir_perm' ,
	'title'			=> $constpref.'_GROUP_DIR_PERM',
	'description'	=> $constpref.'_GROUP_DIR_PERM_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '768'
) ;
$modversion['config'][] = array(
	'name'			=> 'group_dir_item_perm' ,
	'title'			=> $constpref.'_GROUP_DIR_ITEM_PERM',
	'description'	=> $constpref.'_GROUP_DIR_ITEM_PERM_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '748'
) ;
$modversion['config'][] = array(
	'name'			=> 'upload_allow_admin' ,
	'title'			=> $constpref.'_UPLOAD_ALLOW_ADMIN',
	'description'	=> $constpref.'_UPLOAD_ALLOW_ADMIN_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'all'
) ;
$modversion['config'][] = array(
		'name'			=> 'auto_resize_admin' ,
		'title'			=> $constpref.'_AUTO_RESIZE_ADMIN' ,
		'description'	=> $constpref.'_AUTO_RESIZE_ADMIN_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> ''
) ;
$modversion['config'][] = array(
	'name'			=> 'special_groups' ,
	'title'			=> $constpref.'_SPECIAL_GROUPS',
	'description'	=> $constpref.'_SPECIAL_GROUPS_DESC',
	'formtype'		=> 'group_multi' ,
	'valuetype'		=> 'array' ,
	'default'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'upload_allow_spgroups' ,
	'title'			=> $constpref.'_UPLOAD_ALLOW_SPGROUPS',
	'description'	=> $constpref.'_UPLOAD_ALLOW_SPGROUPS_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'all'
) ;
$modversion['config'][] = array(
		'name'			=> 'auto_resize_spgroups' ,
		'title'			=> $constpref.'_AUTO_RESIZE_SPGROUPS' ,
		'description'	=> $constpref.'_AUTO_RESIZE_SPGROUPS_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> ''
) ;
$modversion['config'][] = array(
	'name'			=> 'upload_allow_user' ,
	'title'			=> $constpref.'_UPLOAD_ALLOW_USER',
	'description'	=> $constpref.'_UPLOAD_ALLOW_USER_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'image text/plain'
) ;
$modversion['config'][] = array(
		'name'			=> 'auto_resize_user' ,
		'title'			=> $constpref.'_AUTO_RESIZE_USER' ,
		'description'	=> $constpref.'_AUTO_RESIZE_USER_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> '1024'
) ;
$modversion['config'][] = array(
	'name'			=> 'upload_allow_guest' ,
	'title'			=> $constpref.'_UPLOAD_ALLOW_GUEST',
	'description'	=> $constpref.'_UPLOAD_ALLOW_GUEST_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'image'
) ;
$modversion['config'][] = array(
		'name'			=> 'auto_resize_guest' ,
		'title'			=> $constpref.'_AUTO_RESIZE_GUEST' ,
		'description'	=> $constpref.'_AUTO_RESIZE_GUEST_DESC' ,
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'text' ,
		'default'		=> '1024'
) ;
$modversion['config'][] = array(
	'name'			=> 'disable_pathinfo' ,
	'title'			=> $constpref.'_DISABLE_PATHINFO',
	'description'	=> $constpref.'_DISABLE_PATHINFO_DESC',
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0
) ;
$modversion['config'][] = array(
	'name'			=> 'edit_disable_linked' ,
	'title'			=> $constpref.'_EDIT_DISABLE_LINKED',
	'description'	=> $constpref.'_EDIT_DISABLE_LINKED_DESC',
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1
) ;
$modversion['config'][] = array(
	'name'			=> 'ssl_connector_url' ,
	'title'			=> $constpref.'_SSL_CONNECTOR_URL',
	'description'	=> $constpref.'_SSL_CONNECTOR_URL_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'string' ,
	'default'		=> ''
) ;
$modversion['config'][] = array(
	'name'			=> 'unzip_lang_value' ,
	'title'			=> $constpref.'_UNZIP_LANG_VALUE',
	'description'	=> $constpref.'_UNZIP_LANG_VALUE_DESC',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'string' ,
	'default'		=> ''
) ;
$modversion['config'][] = array(
	'name'			=> 'debug' ,
	'title'			=> $constpref.'_DEBUG',
	'description'	=> $constpref.'_DEBUG_DESC',
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0
) ;

// Notification
$modversion['hasNotification'] = 0;
$modversion['notification'] = array();

$modversion['onInstall'] = 'oninstall.php' ;
$modversion['onUpdate'] = 'onupdate.php' ;
$modversion['onUninstall'] = 'onuninstall.php' ;

// keep block's options
if( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION , 6 , 3 ) < 2.1 && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname(__FILE__).'/include/x20_keepblockoptions.inc.php' ;
}

