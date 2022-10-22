<?php

$constpref = '_MI_' . strtoupper( $mydirname );

$adminmenu = [
	[
		'title' => constant( $constpref . '_ADMENU_CATEGORYACCESS' ),
		'link'  => 'admin/index.php?page=category_access',
	],
	[
		'title' => constant( $constpref . '_ADMENU_FORUMACCESS' ),
		'link'  => 'admin/index.php?page=forum_access',
	],
	[
		'title' => constant( $constpref . '_ADMENU_ADVANCEDADMIN' ),
		'link'  => 'admin/index.php?page=advanced_admin',
	],
	[
		'title' => constant( $constpref . '_ADMENU_POSTHISTORIES' ),
		'link'  => 'admin/index.php?page=post_histories',
	],
];

$adminmenu4altsys = [
	[
		'title' => constant( $constpref . '_ADMENU_MYLANGADMIN' ),
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=mylangadmin',
	],
	[
		'title' => constant( $constpref . '_ADMENU_MYTPLSADMIN' ),
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=mytplsadmin',
	],
//    [
//		'title' => constant( $constpref.'_ADMENU_MYBLOCKSADMIN' ) ,
//		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ,
//    ],
];
