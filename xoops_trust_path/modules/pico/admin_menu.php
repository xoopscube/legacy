<?php

$constpref = '_MI_' . strtoupper( $mydirname );

$adminmenu = [
	[
		'title' => constant( $constpref . '_ADMENU_ACTIVITY' ),
		'link'  => 'admin/index.php',
	],
	[
		'title' => constant( $constpref . '_ADMENU_CONTENTSADMIN' ),
		'link'  => 'admin/index.php?page=contents',
	],
	[
		'title' => constant( $constpref . '_ADMENU_CATEGORYACCESS' ),
		'link'  => 'admin/index.php?page=category_access',
	],
	[
		'title' => constant( $constpref . '_ADMENU_IMPORT' ),
		'link'  => 'admin/index.php?page=import',
	],
	[
		'title' => constant( $constpref . '_ADMENU_TAGS' ),
		'link'  => 'admin/index.php?page=tags',
	],
	[
		'title' => constant( $constpref . '_ADMENU_EXTRAS' ),
		'link'  => 'admin/index.php?page=extras',
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
    [
        'title' => _HELP,
        'link'  => '../legacy/admin/index.php?action=Help&dirname='.$mydirname,
    ],
];
