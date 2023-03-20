<?php

if ( ! defined( 'XOOPS_MODULE_PATH' ) ) {
	define( 'XOOPS_MODULE_PATH', XOOPS_ROOT_PATH . '/modules' );
}
if ( ! defined( 'XOOPS_MODULE_URL' ) ) {
	define( 'XOOPS_MODULE_URL', XOOPS_URL . '/modules' );
}

$isAdmin = false;
if ( is_object( $xoopsUser ) ) {
	if ( $xoopsUser->isAdmin( $xoopsModule->getVar( 'mid' ) ) ) {
		$isAdmin = true;
	}
}

$xelfinderOpenJs      = XOOPS_MODULE_URL . '/' . $mydirname . '/manager.php';
$xelfinderAdminOpenJs = XOOPS_MODULE_URL . '/' . $mydirname . '/manager.php?admin=1';

// TODO : custom settings for popup/iframe
$popup_width = '800';
$popup_height = '740';

$xelTitle = constant(  '_MD_OPEN_WINDOW' );
$xelAdminTitle = constant( '_MD_OPEN_WINDOW_ADMIN' );

include XOOPS_ROOT_PATH . '/header.php';

$xoopsOption['template_main'] = $mydirname . '_main.html';

$_xoops_header = $xoopsTpl->get_template_vars( 'xoops_module_header' );

$_xoops_header .= '<link rel="stylesheet" href="' . XOOPS_MODULE_URL . '/' . $mydirname . '/include/css/main.css" type="text/css" media="all">';
$_xoops_header .= '<script defer src="' . XOOPS_MODULE_URL . '/' . $mydirname . '/include/js/openWithSelfMain_iframe.js"></script>';

$xoopsTpl->assign( [
		'xoops_module_header' => $_xoops_header,
		'mydirname'           => $mydirname,
		'mod_url'             => XOOPS_MODULE_URL . '/' . $mydirname,
		'mod_name'            => $xoopsModule->getVar( 'name' ),
		'finder_open'         => $xelfinderOpenJs,
		'finder_title'        => $xelTitle,
		'admin_open'          => $xelfinderAdminOpenJs,
		'admin_title'         => $xelAdminTitle,
		'popup_width'         => '800',
		'popup_height'        => '740',
	]
);
$xoopsTpl->display( 'db:' . $mydirname . '_main.html' );

include XOOPS_ROOT_PATH . '/footer.php';
