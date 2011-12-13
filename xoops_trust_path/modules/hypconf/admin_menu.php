<?php
include_once dirname(__FILE__).'/include/admin_func.php' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu = array(
	array(
		'title' => hypconf_constant( $constpref.'_ADMENU_CONTENTSADMIN' ) ,
		'link' => 'admin/index.php' ,
	) ,
	array(
		'title' => hypconf_constant( $constpref.'_ADMENU_MAIN_SWITCH' ) ,
		'link' => 'admin/index.php?mode=admin&page=main_switch' ,
	) ,
	array(
		'title' => hypconf_constant( $constpref.'_ADMENU_K_TAI_CONF' ) ,
		'link' => 'admin/index.php?mode=admin&page=k_tai_conf' ,
	) ,
	array(
		'title' => hypconf_constant( $constpref.'_ADMENU_XPWIKI_RENDER' ) ,
		'link' => 'admin/index.php?mode=admin&page=xpwiki_render' ,
	),
	array(
		'title' => hypconf_constant( $constpref.'_ADMENU_SPAM_BLOCK' ) ,
		'link' => 'admin/index.php?mode=admin&page=spam_block' ,
	),
	array(
		'title' => 'PHP info' ,
		'link' => 'admin/index.php?mode=admin&page=phpinfo' ,
	)
) ;


$adminmenu4altsys = array(
	array(
		'title' => hypconf_constant( $constpref.'_ADMENU_MYBLOCKSADMIN' ) ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ,
	) ,
) ;

