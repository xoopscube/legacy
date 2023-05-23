<?php

$constpref = '_MI_' . strtoupper( $mydirname );

$adminmenu = [
	[
		'title' => constant( $constpref . '_ADVISORY' ),
		'link'  => 'admin/index.php?page=advisory',
	],
	[
		'title' => constant( $constpref . '_ADMININDEX' ),
		'link'  => 'admin/index.php',
	],
	[
		'title' => constant( $constpref . '_PREFIXMANAGER' ),
		'link'  => 'admin/index.php?page=prefix_manager',
	],
];

$adminmenu4altsys = [
    [
        'title' => _HELP,
        'link'  => '../legacy/admin/index.php?action=Help&dirname='.$mydirname,
    ],
	/*    [
			'title' => constant($constpref.'_ADMENU_MYBLOCKSADMIN') ,
			'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ,
		],
		 [
			 'title' => _PREFERENCES ,
			 'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences' ,
		 ],*/
];
