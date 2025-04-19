<?php

$constpref = '_MI_' . strtoupper( $mydirname );
// Add this to the existing menu array
$adminmenu = [
	[
		'title' => constant( $constpref . '_DASHBOARD' ),
		'link'  => 'admin/index.php?page=dashboard',
	],
	[
		'title' => constant( $constpref . '_ADVISORY' ),
		'link'  => 'admin/index.php?page=advisory',
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
    	'title' => constant( $constpref . '_CSP_REPORTS' ),
    	'link' => 'admin/index.php?page=csp_violations'
	],
];
// Add this to your admin menu items
$adminmenu[] = [
    'title' => 'Permissions',
    'link' => 'admin/index.php?page=permissions',
    'icon' => 'images/permissions.png'
];

// TODO: Remove myblocksadmin from here 
// xoops_trust_path\modules\protector\admin\mymenu.php
$adminmenu4altsys = [
/* [
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
   [
		'title' => constant( $constpref.'_ADMENU_MYBLOCKSADMIN' ) ,
		'link' => 'admin/index.php?page=permissions' ,
   ], */
];

// Add menu items for proxy functionality
$module_handler = xoops_getHandler('module');
$config_handler = xoops_getHandler('config');
$module = $module_handler->getByDirname('protector');
$configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

// Check if proxy is enabled
$proxy_enabled = $configs['proxy_enabled'] ?? 0;

// Add proxy menu items
if ($proxy_enabled) {
    $adminmenu[] = [
        'title' => 'Proxy Settings',
        'link' => 'admin/index.php?page=proxy_settings'
    ];
    $adminmenu[] = [
        'title' => 'Proxy Logs',
        'link' => 'admin/index.php?page=proxy_logs'
    ];
    $adminmenu[] = [
        'title' => 'Proxy Plugins',
        'link' => 'admin/index.php?page=proxy_plugins'
    ];
}