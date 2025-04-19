<?php
/**
 * Protector module menu for XCL Administration panel.
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
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

/* if ( file_exists( XOOPS_TRUST_PATH . '/libs/altsys/myblocksadmin.php' ) ) {
	// myblocksadmin
	$title       = defined( '_MD_A_MYMENU_MYBLOCKSADMIN' ) ? _MD_A_MYMENU_MYBLOCKSADMIN : 'blocksadmin';
	$adminmenu[] = [ 'title' => $title, 'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ];
} */

// preferences
$config_handler =& xoops_gethandler( 'config' );
if ( (is_countable($config_handler->getConfigs( new Criteria( 'conf_modid', $xoopsModule->mid() ) )) ? count( $config_handler->getConfigs( new Criteria( 'conf_modid', $xoopsModule->mid() ) ) ) : 0) > 0 ) {
	// legacy->preferences
	$adminmenu[] = [
		'title' => _PREFERENCES,
		'link'  => XOOPS_URL . '/modules/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $xoopsModule->mid()
	];
}


$adminmenu = array_merge( $adminmenu, $adminmenu4altsys );

$mymenu_uri  = empty( $mymenu_fake_uri ) ? $_SERVER['REQUEST_URI'] : $mymenu_fake_uri;
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
