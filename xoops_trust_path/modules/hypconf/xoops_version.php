<?php

// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'modinfo.php' , $mydirname , $mytrustdirname , false ) ;

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$modversion['name'] = constant($constpref.'_NAME') ;
$modversion['description'] = constant($constpref.'_DESC') ;
$modversion['version'] = 1.01 ;
$modversion['credits'] = 'hypweb.net';
$modversion['author'] = 'nao-pon<br />hypweb.net(http://xoops.hypweb.net/)' ;
$modversion['help'] = '' ;
$modversion['license'] = 'GPL' ;
$modversion['official'] = 0 ;
$modversion['image'] = file_exists( $mydirpath.'/module_icon.png' ) ? 'module_icon.png' : 'module_icon.php' ;
$modversion['dirname'] = $mydirname ;
$modversion['read_any'] = true ;
$modversion['trust_dirname'] = $mytrustdirname ;

// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false ;
$modversion['tables'] = array() ;

// Admin things
$modversion['hasAdmin'] = 1 ;
$modversion['adminindex'] = 'admin/index.php' ;
$modversion['adminmenu'] = 'admin/admin_menu.php' ;

// Search
$modversion['hasSearch'] = 0 ;

// Menu
$modversion['hasMain'] = 0 ;

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

// Comments
$modversion['hasComments'] = 0 ;

// Notification
$modversion['hasNotification'] = 0 ;

// onInstall, onUpdate, onUninstall
$modversion['onInstall'] = 'oninstall.php' ;
$modversion['onUpdate'] = 'onupdate.php' ;
$modversion['onUninstall'] = 'onuninstall.php' ;

// keep block's options
if( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION , 6 , 3 ) < 2.1 && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname(__FILE__).'/include/x20_keepblockoptions.inc.php' ;
}

