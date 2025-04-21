<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

$adminmenu = [
//    [
//        'title' => _MI_ALTSYS_MENU_NEWCUSTOMBLOCK,
//        'link'  => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin&dirname=__CustomBlocks__&op=edit',
//        'show'  => false,
//    ],
    [
		'title' => _MI_ALTSYS_MENU_CUSTOMBLOCKS,
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin&dirname=__CustomBlocks__',
	],
	[
		'title' => _MI_ALTSYS_MENU_MYBLOCKSADMIN,
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin',
	],
	[
		'title' => _MI_ALTSYS_MENU_MYTPLSADMIN,
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=mytplsadmin',
	],
	[
		'title' => _MI_ALTSYS_MENU_COMPILEHOOKADMIN,
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=compilehookadmin',
	],
	[
		'title' => _MI_ALTSYS_MENU_MYLANGADMIN,
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=mylangadmin',
	],
	/*	array(
		'title' => _MI_ALTSYS_MENU_MYAVATAR ,
		'link' => 'index.php?mode=admin&lib=altsys&page=myavatar' ,
	) ,*/
	/*	array(
		'title' => _MI_ALTSYS_MENU_MYSMILEY ,

		'link' => 'index.php?mode=admin&lib=altsys&page=mysmiley' ,
	) ,*/
];

$adminmenu4altsys = [
	[
		'title' => _PREFERENCES,
		'link'  => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences',
	],
    [
        'title' => _HELP,
        'link'  => '../legacy/admin/index.php?action=Help&dirname=altsys',
    ],
];
