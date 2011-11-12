<?php

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu = array(
	array(
		'title' => constant( $constpref.'_ADMENU_CONTENTSADMIN' ) ,
		'link' => 'admin/index.php?page=contents' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_CATEGORYACCESS' ) ,
		'link' => 'admin/index.php?page=category_access' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_IMPORT' ) ,
		'link' => 'admin/index.php?page=import' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_TAGS' ) ,
		'link' => 'admin/index.php?page=tags' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_EXTRAS' ) ,
		'link' => 'admin/index.php?page=extras' ,
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