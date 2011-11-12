<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

$adminmenu = array(
	array(
		'title' => _MI_ALTSYS_MENU_CUSTOMBLOCKS ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin&dirname=__CustomBlocks__' ,
	) ,
	array(
		'title' => _MI_ALTSYS_MENU_NEWCUSTOMBLOCK ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin&dirname=__CustomBlocks__&op=edit' ,
		'show' => false ,
	) ,
	array(
		'title' => _MI_ALTSYS_MENU_MYBLOCKSADMIN ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ,
	) ,
	array(
		'title' => _MI_ALTSYS_MENU_MYTPLSADMIN ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mytplsadmin' ,
	) ,
	array(
		'title' => _MI_ALTSYS_MENU_COMPILEHOOKADMIN ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=compilehookadmin' ,
	) ,
	array(
		'title' => _MI_ALTSYS_MENU_MYLANGADMIN ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mylangadmin' ,
	) ,
/*	array(
		'title' => _MI_ALTSYS_MENU_MYAVATAR ,
		'link' => 'index.php?mode=admin&lib=altsys&page=myavatar' ,
	) ,*/
/*	array(
		'title' => _MI_ALTSYS_MENU_MYSMILEY ,
		'link' => 'index.php?mode=admin&lib=altsys&page=mysmiley' ,
	) ,*/
) ;

$adminmenu4altsys = array(
	array(
		'title' => 'ALTSYS '._PREFERENCES ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences' ,
	) ,
) ;

?>
