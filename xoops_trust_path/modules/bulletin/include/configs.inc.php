<?php

	global $xoopsConfig , $xoopsDB , $xoopsUser ;

	// read from xoops_config
	// get my mid
	$rs = $xoopsDB->query( "SELECT mid FROM ".$xoopsDB->prefix('modules')." WHERE dirname='$mydirname'" ) ;
	list( $bulletin_mid ) = $xoopsDB->fetchRow( $rs ) ;

	// read configs from xoops_config directly
	$rs = $xoopsDB->query( "SELECT conf_name,conf_value FROM ".$xoopsDB->prefix('config')." WHERE conf_modid=$bulletin_mid" ) ;
	while( list( $key , $val ) = $xoopsDB->fetchRow( $rs ) ) {
		$bulletin_configs[ $key ] = $val ;
	}

	foreach( $bulletin_configs as $key => $val ) {
		${'bulletin_'.$key} = $val ;
	}

	// User Informations
	if( empty( $xoopsUser ) ) {
		$my_uid  = 0 ;
		$isadmin = false ;
	} else {
		$my_uid  = $xoopsUser->uid() ;
		$isadmin = $xoopsUser->isAdmin( $bulletin_mid ) ;
	}

	// DB table name
	$table_stories  = $xoopsDB->prefix( "{$mydirname}_stories" ) ;
	$table_topics   = $xoopsDB->prefix( "{$mydirname}_topics" ) ;
	$table_comments = $xoopsDB->prefix( "xoopscomments" ) ;

	// sanitizer
	$myts =& MyTextSanitizer::getInstance();

	require_once dirname(__FILE__).'/function.php' ;

?>