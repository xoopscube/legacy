<?php

$constpref = '_MI_' . strtoupper( $mydirname );

$adminmenu = [
	[
		'title' => constant( $constpref . '_DASHBOARD' ),
		'link'  => 'admin/index.php?page=dashboard',
	],
	[
		'title' => constant( $constpref . '_LOGLIST' ),
		'link'  => 'admin/index.php?page=log',
	],
	[
		'title' => constant( $constpref . '_IPBAN' ),
		'link'  => 'admin/index.php?page=ban',
	],
	[
		'title' => constant( $constpref . '_SAFELIST' ),
		'link'  => 'admin/index.php?page=safe_list',
	],
	[
		'title' => constant( $constpref . '_PREFIXMANAGER' ),
		'link'  => 'admin/index.php?page=prefix_manager',
	],
	[
		'title' => constant( $constpref . '_ADVISORY' ),
		'link'  => 'admin/index.php?page=advisory',
	],
];

$adminmenu4altsys = [
    [
        'title' => _HELP,
        'link'  => '../legacy/admin/index.php?action=Help&dirname='.$mydirname,
    ],
];
