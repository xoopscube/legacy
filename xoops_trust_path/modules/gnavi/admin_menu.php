<?php

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu = array(
	array(
		'title' => constant( $constpref.'_ADMENU_MYCATEGOLY' ) ,
		'link' => 'admin/index.php?page=category' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYICON' ) ,
		'link' => 'admin/index.php?page=icon' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYPHOTOMANAGER' ) ,
		'link' => 'admin/index.php?page=photomanager' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYLADMISSION' ) ,
		'link' => 'admin/index.php?page=admission' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYGROUPPERM' ) ,
		'link' => 'admin/index.php?page=groupperm' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYCHECKCONFIGS' ) ,
		'link' => 'admin/index.php?page=checkconfigs' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYBATCH' ) ,
		'link' => 'admin/index.php?page=batch' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYREDOTHUMBS' ) ,
		'link' => 'admin/index.php?page=redothumbs' ,
	) ,
) ;

$adminmenu4altsys = array(
	array(
		'title' => constant( $constpref.'_ADMENU_MYLANGADMIN' ) ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mylangadmin' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYTPLSADMIN' ) ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mytplsadmin' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYBLOCKSADMIN' ) ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MYPREFERENCES' ) ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences' ,
	) ,
) ;

?>