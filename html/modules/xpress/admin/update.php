<?php
	$mydirname = basename(dirname( dirname( __FILE__ ) )) ;
	$mydirpath = dirname( dirname( __FILE__ ) ) ;
	require_once '../../../include/cp_header.php' ;

	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$url = XOOPS_URL.'/modules/legacy/admin/index.php?action=ModuleUpdate&dirname=' . $mydirname;
	} else {
		$url = XOOPS_URL.'/modules/system/admin.php?fct=modulesadmin&op=update&module=' . $mydirname ;
	}
	header('Location: ' . $url);
?>