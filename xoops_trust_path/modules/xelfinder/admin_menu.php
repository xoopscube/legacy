<?php

$constpref = '_MI_' . strtoupper( $mydirname );

$adminmenu = [
    [
        'title' => constant( $constpref . '_ADMENU_INDEX_CHECK' ),
        'link'  => 'admin/index.php',
    ],
	[
		'title' => constant( $constpref . '_ADMENU_GOTO_MODULE' ),
		'link'  => 'index.php',
	],
	[
		'title' => constant( $constpref . '_ADMENU_GOTO_MANAGER' ),
		'link'  => 'manager.php?admin=1',
	],
	[
		'title' => constant( $constpref . '_ADMENU_GOOGLEDRIVE' ),
		'link'  => 'admin/index.php?page=googledrive',
	],
	[
		'title' => constant( $constpref . '_ADMENU_VENDORUPDATE' ),
		'link'  => 'admin/index.php?page=vendorup',
	]
];

$adminmenu4altsys = [
	[
		'title' => constant( $constpref . '_ADMENU_MYLANGADMIN' ),
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=mylangadmin',
	],
	 	[
	 		'title' => constant( $constpref.'_ADMENU_MYTPLSADMIN' ) ,
	 		'link' => 'admin/index.php?mode=admin&lib=altsys&page=mytplsadmin' ,
	 	],
	[
		'title' => constant( $constpref . '_ADMENU_MYBLOCKSADMIN' ),
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin',
	],
    [
        'title' => _HELP,
        'link'  => '../legacy/admin/index.php?action=Help&dirname='.$mydirname,
    ],
	// [
	// 	'title' => constant( $constpref.'_ADMENU_MYPREFERENCES' ) ,
	// 	'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences' ,
	// ],
];
