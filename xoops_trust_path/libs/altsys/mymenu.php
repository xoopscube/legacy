<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Mym enu only for Altsys
 * @package    Altsys
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */


if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

if ( ! isset( $module ) || ! is_object( $module ) ) {
	$module = $xoopsModule;
} elseif ( ! is_object( $xoopsModule ) ) {
	die( '$xoopsModule is not set' );
}

// language files (modinfo.php)
altsys_include_language_file( 'modinfo' );

include __DIR__ . '/admin_menu.php';

$adminmenu = array_merge( $adminmenu, $adminmenu4altsys );

$mymenu_uri  = empty( $mymenu_fake_uri ) ? $_SERVER['REQUEST_URI'] : $mymenu_fake_uri;
$mymenu_link = mb_substr( mb_strstr( $mymenu_uri, '/admin/' ), 1 );

// xoops_breadcrumbs
//$GLOBALS['dirname'] = array( array( 'url' => XOOPS_URL.'/modules/altsys/admin/index.php' , 'name' => $xoopsModule->getVar('name') ) ) ;

// highlight
foreach ( array_keys( $adminmenu ) as $i ) {
	if ( $mymenu_link == $adminmenu[ $i ]['link'] ) {
		$adminmenu[ $i ]['selected'] = true;
		$adminmenu_hilighted         = true;
		// $mydirname['dirname'][] = array( 'url' => XOOPS_URL."/modules/altsys/".htmlspecialchars($adminmenu[$i]['link'],ENT_QUOTES) , 'name' => htmlspecialchars( $adminmenu[$i]['title'] , ENT_QUOTES ) ) ;
	} else {
		$adminmenu[ $i ]['selected'] = false;
	}
}
if ( empty( $adminmenu_hilighted ) ) {
	foreach ( array_keys( $adminmenu ) as $i ) {
		if ( mb_stristr( $mymenu_uri, $adminmenu[ $i ]['link'] ) ) {
			$adminmenu[ $i ]['selected'] = true;
//			$GLOBALS['altsysXoopsBreadcrumbs'][] = array( 'url' => XOOPS_URL."/modules/altsys/".htmlspecialchars($adminmenu[$i]['link'],ENT_QUOTES) , 'name' => htmlspecialchars( $adminmenu[$i]['title'] , ENT_QUOTES ) ) ;
			break;
		}
	}
}

// link conversion from relative to absolute
foreach ( array_keys( $adminmenu ) as $i ) {
	if ( false === mb_stristr( $adminmenu[ $i ]['link'], XOOPS_URL ) ) {
		$adminmenu[ $i ]['link'] = XOOPS_URL . "/modules/$mydirname/" . $adminmenu[ $i ]['link'];
		// $adminmenu[$i]['name'] = XOOPS_URL . "/modules/$mydirname/index.php";
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

// submenu
$page = preg_replace( '/[^0-9a-zA-Z_-]/', '', @$_GET['page'] );
if ( file_exists( __DIR__ . '/mymenusub/' . $page . '.php' ) ) {
	include __DIR__ . '/mymenusub/' . $page . '.php';
}
