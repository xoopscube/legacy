<?php

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu = array(
	array(
		'title' => constant( $constpref.'_ADMENU_PIPE' ) ,
		'link' => 'admin/index.php?page=pipe' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_CACHE' ) ,
		'link' => 'admin/index.php?page=cache' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_CLIPPING' ) ,
		'link' => 'admin/index.php?page=clipping' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_JOINT' ) ,
		'link' => 'admin/index.php?page=joint' ,
	) ,
/*	array(
		'title' => constant( $constpref.'_ADMENU_JOINTCLASS' ) ,
		'link' => 'admin/index.php?page=jointclass' ,
	) ,*/
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