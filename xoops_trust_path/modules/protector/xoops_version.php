<?php

// language file (modinfo.php)
if( file_exists( dirname(__FILE__).'/language/'.@$GLOBALS['xoopsConfig']['language'].'/modinfo.php' ) ) {
	include dirname(__FILE__).'/language/'.@$GLOBALS['xoopsConfig']['language'].'/modinfo.php' ;
} else if( file_exists( dirname(__FILE__).'/language/english/modinfo.php' ) ) {
	include dirname(__FILE__).'/language/english/modinfo.php' ;
}
$constpref = '_MI_' . strtoupper( $mydirname ) ;


$modversion['name'] = constant($constpref.'_NAME') ;
$modversion['description'] = constant($constpref.'_DESC') ;
$modversion['version'] = file_get_contents( dirname(__FILE__).'/include/version.txt' ) ;
$modversion['credits'] = "PEAK Corp." ;
$modversion['author'] = "GIJ=CHECKMATE<br />PEAK Corp.(http://www.peak.ne.jp/)" ;
$modversion['help'] = "" ;
$modversion['license'] = "GPL" ;
$modversion['official'] = 0 ;
$modversion['image'] = file_exists( $mydirpath.'/module_icon.png' ) ? 'module_icon.png' : 'module_icon.php' ;
$modversion['iconbig'] = 'module_icon.php?file=iconbig' ;
$modversion['iconsmall'] = 'module_icon.php?file=iconsmall' ;
$modversion['dirname'] = $mydirname ;

// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false ;
$modversion['tables'] = array() ;

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/admin_menu.php";

// Templates

// Blocks
$modversion['blocks'] = array() ;

// Menu
$modversion['hasMain'] = 0;

// Config
$modversion['config'][1] = array(
	'name'			=> 'global_disabled' ,
	'title'			=> $constpref.'_GLOBAL_DISBL' ,
	'description'	=> $constpref.'_GLOBAL_DISBLDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> "0" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'default_lang' ,
	'title'			=> $constpref.'_DEFAULT_LANG' ,
	'description'	=> $constpref.'_DEFAULT_LANGDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'text' ,
	'default'		=> @$GLOBALS['xoopsConfig']['language'] ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'log_level' ,
	'title'			=> $constpref.'_LOG_LEVEL' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=>  255 ,
	'options'		=> array( $constpref.'_LOGLEVEL0' => 0 , $constpref.'_LOGLEVEL15' => 15 , $constpref.'_LOGLEVEL63' => 63 , $constpref.'_LOGLEVEL255' => 255 )
) ;
$modversion['config'][] = array(
	'name'			=> 'banip_time0' ,
	'title'			=> $constpref.'_BANIP_TIME0' ,
	'description'	=> '' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'int' ,
	'default'		=> 86400 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'reliable_ips' ,
	'title'			=> $constpref.'_RELIABLE_IPS' ,
	'description'	=> $constpref.'_RELIABLE_IPSDSC' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'array' ,
	'default'		=> "^192.168.|127.0.0.1" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'session_fixed_topbit' ,
	'title'			=> $constpref.'_HIJACK_TOPBIT' ,
	'description'	=> $constpref.'_HIJACK_TOPBITDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'int' ,
	'default'		=> 24 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'groups_denyipmove' ,
	'title'			=> $constpref.'_HIJACK_DENYGP' ,
	'description'	=> $constpref.'_HIJACK_DENYGPDSC' ,
	'formtype'		=> 'group_multi' ,
	'valuetype'		=> 'array' ,
	'default'		=> array(1) ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'san_nullbyte' ,
	'title'			=> $constpref.'_SAN_NULLBYTE' ,
	'description'	=> $constpref.'_SAN_NULLBYTEDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> "1" ,
	'options'		=> array()
) ;
/* $modversion['config'][] = array(
	'name'			=> 'die_nullbyte' ,
	'title'			=> $constpref.'_DIE_NULLBYTE' ,
	'description'	=> $constpref.'_DIE_NULLBYTEDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> "1" ,
	'options'		=> array()
) ; */
$modversion['config'][] = array(
	'name'			=> 'die_badext' ,
	'title'			=> $constpref.'_DIE_BADEXT' ,
	'description'	=> $constpref.'_DIE_BADEXTDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> "1" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'contami_action' ,
	'title'			=> $constpref.'_CONTAMI_ACTION' ,
	'description'	=> $constpref.'_CONTAMI_ACTIONDS' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> 3 ,
	'options'		=> array( $constpref.'_OPT_NONE' => 0 , $constpref.'_OPT_EXIT' => 3 , $constpref.'_OPT_BIPTIME0' => 7 , $constpref.'_OPT_BIP' => 15 )
) ;
$modversion['config'][] = array(
	'name'			=> 'isocom_action' ,
	'title'			=> $constpref.'_ISOCOM_ACTION' ,
	'description'	=> $constpref.'_ISOCOM_ACTIONDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array( $constpref.'_OPT_NONE' => 0 , $constpref.'_OPT_SAN' => 1 , $constpref.'_OPT_EXIT' => 3 , $constpref.'_OPT_BIPTIME0' => 7 , $constpref.'_OPT_BIP' => 15 )
) ;
$modversion['config'][] = array(
	'name'			=> 'union_action' ,
	'title'			=> $constpref.'_UNION_ACTION' ,
	'description'	=> $constpref.'_UNION_ACTIONDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array( $constpref.'_OPT_NONE' => 0 , $constpref.'_OPT_SAN' => 1 , $constpref.'_OPT_EXIT' => 3 , $constpref.'_OPT_BIPTIME0' => 7 , $constpref.'_OPT_BIP' => 15 )
) ;
$modversion['config'][] = array(
	'name'			=> 'id_forceintval' ,
	'title'			=> $constpref.'_ID_INTVAL' ,
	'description'	=> $constpref.'_ID_INTVALDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> "0" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'file_dotdot' ,
	'title'			=> $constpref.'_FILE_DOTDOT' ,
	'description'	=> $constpref.'_FILE_DOTDOTDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> "1" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'bf_count' ,
	'title'			=> $constpref.'_BF_COUNT' ,
	'description'	=> $constpref.'_BF_COUNTDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'int' ,
	'default'		=> "10" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'bwlimit_count' ,
	'title'			=> $constpref.'_BWLIMIT_COUNT' ,
	'description'	=> $constpref.'_BWLIMIT_COUNTDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'dos_skipmodules' ,
	'title'			=> $constpref.'_DOS_SKIPMODS' ,
	'description'	=> $constpref.'_DOS_SKIPMODSDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'text' ,
	'default'		=> "" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'dos_expire' ,
	'title'			=> $constpref.'_DOS_EXPIRE' ,
	'description'	=> $constpref.'_DOS_EXPIREDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'int' ,
	'default'		=> "60" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'dos_f5count' ,
	'title'			=> $constpref.'_DOS_F5COUNT' ,
	'description'	=> $constpref.'_DOS_F5COUNTDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'int' ,
	'default'		=> "20" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'dos_f5action' ,
	'title'			=> $constpref.'_DOS_F5ACTION' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> "exit" ,
	'options'		=> array( $constpref.'_DOSOPT_NONE' => 'none' , $constpref.'_DOSOPT_SLEEP' => 'sleep' , $constpref.'_DOSOPT_EXIT' => 'exit' , $constpref.'_DOSOPT_BIPTIME0' => 'biptime0' , $constpref.'_DOSOPT_BIP' => 'bip' , $constpref.'_DOSOPT_HTA' => 'hta' )
) ;
$modversion['config'][] = array(
	'name'			=> 'dos_crcount' ,
	'title'			=> $constpref.'_DOS_CRCOUNT' ,
	'description'	=> $constpref.'_DOS_CRCOUNTDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'int' ,
	'default'		=> "40" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'dos_craction' ,
	'title'			=> $constpref.'_DOS_CRACTION' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> "exit" ,
	'options'		=> array( $constpref.'_DOSOPT_NONE' => 'none' , $constpref.'_DOSOPT_SLEEP' => 'sleep' , $constpref.'_DOSOPT_EXIT' => 'exit' , $constpref.'_DOSOPT_BIPTIME0' => 'biptime0' , $constpref.'_DOSOPT_BIP' => 'bip' , $constpref.'_DOSOPT_HTA' => 'hta' )
) ;
$modversion['config'][] = array(
	'name'			=> 'dos_crsafe' ,
	'title'			=> $constpref.'_DOS_CRSAFE' ,
	'description'	=> $constpref.'_DOS_CRSAFEDSC' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'text' ,
	'default'		=> "/(msnbot|Googlebot|Yahoo! Slurp)/i" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'bip_except' ,
	'title'			=> $constpref.'_BIP_EXCEPT' ,
	'description'	=> $constpref.'_BIP_EXCEPTDSC' ,
	'formtype'		=> 'group_multi' ,
	'valuetype'		=> 'array' ,
	'default'		=> array(1) ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'disable_features' ,
	'title'			=> $constpref.'_DISABLES' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array('xmlrpc'=>1,'xmlrpc + 2.0.9.2 bugs'=>1025,'_NONE'=>0)
) ;
$modversion['config'][] = array(
	'name'			=> 'enable_dblayertrap' ,
	'title'			=> $constpref.'_DBLAYERTRAP' ,
	'description'	=> $constpref.'_DBLAYERTRAPDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'dblayertrap_wo_server' ,
	'title'			=> $constpref.'_DBTRAPWOSRV' ,
	'description'	=> $constpref.'_DBTRAPWOSRVDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'enable_bigumbrella' ,
	'title'			=> $constpref.'_BIGUMBRELLA' ,
	'description'	=> $constpref.'_BIGUMBRELLADSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'spamcount_uri4user' ,
	'title'			=> $constpref.'_SPAMURI4U' ,
	'description'	=> $constpref.'_SPAMURI4UDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'spamcount_uri4guest' ,
	'title'			=> $constpref.'_SPAMURI4G' ,
	'description'	=> $constpref.'_SPAMURI4GDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 5 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'filters' ,
	'title'			=> $constpref.'_FILTERS' ,
	'description'	=> $constpref.'_FILTERSDSC' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> "" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'enable_manip_check' ,
	'title'			=> $constpref.'_MANIPUCHECK' ,
	'description'	=> $constpref.'_MANIPUCHECKDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'manip_value' ,
	'title'			=> $constpref.'_MANIPUVALUE' ,
	'description'	=> $constpref.'_MANIPUVALUEDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;


// Search
$modversion['hasSearch'] = 0;

// Comments
$modversion['hasComments'] = 0;

// Config Settings (only for modules that need config settings generated automatically)

// Notification

$modversion['hasNotification'] = 0;

// onInstall, onUpdate, onUninstall
$modversion['onInstall'] = 'oninstall.php' ;
$modversion['onUpdate'] = 'onupdate.php' ;
$modversion['onUninstall'] = 'onuninstall.php' ;

?>
