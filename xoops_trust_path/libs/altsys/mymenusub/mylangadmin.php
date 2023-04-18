<?php
/**
 * Altsys library (UI-Components) Admin menu Language
 *
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

$current_dirname = preg_replace( '/[^0-9a-zA-Z_-]/', '', @$_GET['dirname'] );

$db  = XoopsDatabaseFactory::getDatabaseConnection();
$mrs = $db->query( 'SELECT m.name,m.dirname,COUNT(l.mid) FROM ' . $db->prefix( 'modules' ) . ' m LEFT JOIN ' . $db->prefix( 'altsys_language_constants' ) . ' l ON m.mid=l.mid WHERE m.isactive GROUP BY m.mid ORDER BY m.weight,m.mid' );

$adminmenu = [];


while ( [$name, $dirname, $count] = $db->fetchRow( $mrs ) ) {

    $moduleIcon = '<img class="svg" src="'.XOOPS_URL.'/modules/'.$dirname.'/images/module_icon.svg" width="1em" height="1em" alt="module-icon">';

	if ( $dirname == $current_dirname ) {

		$adminmenu[] = [
			'selected' => true,
			'title'    => "<span class='badge-count'>$count</span>" .$name,
            'icon'     => $moduleIcon,
			'link'     => '?mode=admin&lib=altsys&page=mylangadmin&dirname=' . $dirname,
		];
		//$GLOBALS['altsysXoopsBreadcrumbs'][] = array( 'name' => htmlspecialchars( $name , ENT_QUOTES ) ) ;
	} else {
		$adminmenu[] = [
			'selected' => false,
			'title'    => "<span class='badge-count'>$count</span>" .$name,
            'icon'     => $moduleIcon,
			'link'     => '?mode=admin&lib=altsys&page=mylangadmin&dirname=' . $dirname,
		];
	}
}

// display
require_once XOOPS_TRUST_PATH . '/libs/altsys/class/D3Tpl.class.php';
$tpl = new D3Tpl();
$tpl->assign(
	[
		'adminmenu' => $adminmenu,
		'mypage'    => 'mylangadmin',
	]
);
$tpl->display( 'db:altsys_inc_menu_sub.html' );
