<?php

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu = array(
	array(
		'title' => constant( $constpref.'_ADMIN_CONF' ),
		'link'  => '?cmd=conf'
	),
	array(
		'title' => constant( $constpref.'_ADMIN_TOOLS' ),
		'link'  => '?:AdminTools'
	),
	array(
		'title' => constant( $constpref.'_PLUGIN_CONVERTER' ),
		'link'  => 'admin/index.php?page=plugin_conv'
	),
	array(
		'title' => constant( $constpref.'_SKIN_CONVERTER' ),
		'link'  => 'admin/index.php?page=skin_conv'
	),
);

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