<?php

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu = array(
	array(
		'title' => constant( $constpref.'_ADMENU_CONTENTSADMIN' ) ,
		'link' => 'admin/index.php' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_MAIN_SWITCH' ) ,
		'link' => 'admin/index.php?mode=admin&page=main_switch' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_K_TAI_CONF' ) ,
		'link' => 'admin/index.php?mode=admin&page=k_tai_conf' ,
	)
) ;


$adminmenu4altsys = array(
	array(
		'title' => constant( $constpref.'_ADMENU_MYBLOCKSADMIN' ) ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ,
	) ,
) ;

