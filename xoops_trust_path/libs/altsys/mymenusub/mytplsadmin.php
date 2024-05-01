<?php
/**
 * Altsys library (UI-Components) for D3 modules
 *
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

$current_dirname = preg_replace( '/[^0-9a-zA-Z_-]/', '', @$_GET['dirname'] );

$db =& XoopsDatabaseFactory::getDatabaseConnection();

// get custom templates
[$count] = $db->fetchRow( $db->query( 'SELECT COUNT(t.tpl_module) AS tpl_count FROM ' . $db->prefix( 'tplfile' ) . " t WHERE t.tpl_type='custom'" ) );
if ( '_custom' == $current_dirname ) {
	$custom_selected = true;
} else {
	$custom_selected = false;
}

$adminmenu = [
	[
		'selected' => $custom_selected,
		'title'    => _MYTPLSADMIN_CUSTOMTEMPLATE . " ($count)",
		'link'     => '?mode=admin&lib=altsys&page=mytplsadmin&dirname=_custom',
	],
];

// get modules/templates
$mrs = $db->query( 'SELECT m.name,m.dirname,COUNT(t.tpl_module) AS tpl_count FROM ' . $db->prefix( 'modules' ) . ' m LEFT JOIN ' . $db->prefix( 'tplfile' ) . ' t ON m.dirname=t.tpl_module WHERE m.isactive GROUP BY m.mid HAVING tpl_count>0 ORDER BY m.weight,m.mid' );

// module loop
while ( [$name, $dirname, $count] = $db->fetchRow( $mrs ) ) {

    $moduleIcon = '<img class="svg" src="'.XOOPS_URL.'/modules/'.$dirname.'/images/module_icon.svg" width="1em" height="1em" alt="module-icon">';

	if ( $dirname == $current_dirname ) {
		$adminmenu[] = [
			'selected' => true,
			'title'    => "<span class='badge-count fingerprint6'>$count</span>" . $name,
			'link'     => '?mode=admin&lib=altsys&page=mytplsadmin&dirname=' . $dirname,
            'icon'     => $moduleIcon,
		];
		//$GLOBALS['altsysXoopsBreadcrumbs'][] = array( 'name' => htmlspecialchars( $name , ENT_QUOTES ) ) ;
        // @todo @gigamaster Badge count
	} else {
		$adminmenu[] = [
			'selected' => false,
			'title'    => "<span class='badge-count'>$count</span>". $name,
			'link'     => '?mode=admin&lib=altsys&page=mytplsadmin&dirname=' . $dirname,
            'icon'     => $moduleIcon,
		];
	}
}

// display
require_once XOOPS_TRUST_PATH . '/libs/altsys/class/D3Tpl.class.php';
$tpl = new D3Tpl();
$tpl->assign(
	[
		'adminmenu' => $adminmenu,
		'mypage'    => 'mytplsadmin',
	]
);
$tpl->display( 'db:altsys_inc_menu_sub.html' );
