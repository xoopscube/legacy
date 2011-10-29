<?php
$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu = array(
	array(
		'title' => constant( $constpref.'_ADMENU5' ) ,
		'link' => 'admin/index.php?op=list' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU2' ) ,
		'link' => 'admin/index.php?op=topicsmanager' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU_CATEGORYACCESS' ) ,
		'link' => 'admin/index.php?page=category_access' ,
	) ,	
	array(
		'title' => constant( $constpref.'_ADMENU4' ) ,
		'link' => 'admin/index.php?op=permition' ,
	) ,
	array(
		'title' => constant( $constpref.'_ADMENU7' ) ,
		'link' => 'admin/index.php?op=convert' ,
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
		'title' => _PREFERENCES ,
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences' ,
	) ,
) ;

?>