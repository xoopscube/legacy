<?php
/**
 * mymenu for D3 modules always require altsys
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL V2
 */

// Deny direct access
if ( isset($_GET['page']) && 'mymenu' === preg_replace( '/[^a-zA-Z0-9_-]/', '', $_GET['page'] ) ) {
	exit;
}

global $xoopsModule;

if ( ! is_object( $xoopsModule ) ) {
	die( '$xoopsModule is not set' );
}

// language files (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH . '/libs/altsys/class/D3LanguageManager.class.php';

if ( ! file_exists( $langmanpath ) ) {
	die( 'install the latest altsys' );
}

require_once( $langmanpath );

$langman = D3LanguageManager::getInstance();

$langman->read( 'modinfo.php', $mydirname, $mytrustdirname );

include dirname( __DIR__ ) . '/admin_menu.php';

// Block Admin
if ( file_exists( XOOPS_TRUST_PATH . '/libs/altsys/myblocksadmin.php' ) ) {

	$title = defined( '_MD_A_MYMENU_MYBLOCKSADMIN' ) ? _MD_A_MYMENU_MYBLOCKSADMIN : 'blocksadmin';

	$adminmenu[] = [ 
		'title' => $title, 
		'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin',
		'dirname' => $mydirname  // Add the dirname key here
	];
}

// Preferences
$config_handler =& xoops_gethandler( 'config' );
if ( (is_countable($config_handler->getConfigs( new Criteria( 'conf_modid', $xoopsModule->mid() ) )) ? count( $config_handler->getConfigs( new Criteria( 'conf_modid', $xoopsModule->mid() ) ) ) : 0) > 0 ) {
	$adminmenu[] = [
		'title' => _PREFERENCES,
		'link'  => XOOPS_URL . '/modules/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $xoopsModule->mid(),
		'dirname' => $mydirname  // Add the dirname key here
	];
}

$adminmenu = array_merge( $adminmenu, $adminmenu4altsys );

$mymenu_uri = empty( $mymenu_fake_uri ) ? $_SERVER['REQUEST_URI'] : $mymenu_fake_uri;

$mymenu_link = substr( strstr( $mymenu_uri, '/admin/' ), 1 );


// highlight
foreach ( array_keys( $adminmenu ) as $i ) {
	if ( $mymenu_link === $adminmenu[ $i ]['link'] ) {
		$adminmenu[ $i ]['selected']     = true;
		$adminmenu_hilighted             = true;
		$GLOBALS['altsysAdminPageTitle'] = $adminmenu[ $i ]['title'];
	} else {
		$adminmenu[ $i ]['selected'] = false;
	}
}
if ( empty( $adminmenu_hilighted ) ) {
	foreach ( array_keys( $adminmenu ) as $i ) {
		if ( stripos( $mymenu_uri, (string) $adminmenu[ $i ]['link'] ) !== false ) {
			$adminmenu[ $i ]['selected']     = true;
			$GLOBALS['altsysAdminPageTitle'] = $adminmenu[ $i ]['title'];
			break;
		}
	}
}

// link conversion from relative to absolute
foreach ( array_keys( $adminmenu ) as $i ) {
	if ( stripos( $adminmenu[ $i ]['link'], (string) XOOPS_URL ) === false ) {
		$adminmenu[ $i ]['link'] = XOOPS_URL . "/modules/$mydirname/" . $adminmenu[ $i ]['link'];
	}
	// Ensure dirname is set for all menu items
	if (!isset($adminmenu[$i]['dirname'])) {
		$adminmenu[$i]['dirname'] = $mydirname;
	}
}

// Returns module dir name with the first character capitalized
// Assign to template for Admin Breadcrumbs
$dirname = ucfirst( $mydirname );

// display
require_once XOOPS_TRUST_PATH . '/libs/altsys/class/D3Tpl.class.php';

$tpl = new D3Tpl();

$tpl->assign(
	[
		'adminmenu' => $adminmenu,
		'dirname'   => $dirname,
	]
);

$tpl->display( 'db:altsys_inc_menu.html' );
