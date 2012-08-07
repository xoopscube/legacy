<?php

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu = array(
	array(
		'title' => constant( $constpref.'_ADMININDEX' ) ,
		'link' => 'admin/index.php' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADVISORY' ) ,
		'link' => 'admin/index.php?page=advisory' ,
	) ,
	array(
		'title' => constant( $constpref.'_PREFIXMANAGER' ) ,
		'link' => 'admin/index.php?page=prefix_manager' ,
	) ,
) ;

$adminmenu4altsys = array(
	array(
		'title' => constant( $constpref.'_ADMENU_MYBLOCKSADMIN' ) ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ,
	) ,
	array(
		'title' => _PREFERENCES ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences' ,
	) ,
) ;


?>