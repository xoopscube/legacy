<?php
	$mydirname = basename(dirname( dirname( __FILE__ ) )) ;
	$mydirpath = dirname( dirname( __FILE__ ) ) ;
	require_once '../../../include/cp_header.php' ;
	require_once $mydirpath . '/wp-config.php' ;
	
	$url = XOOPS_URL . '/modules/' . $mydirname . '/wp-admin/';	
	header('Location: ' . $url);
?>