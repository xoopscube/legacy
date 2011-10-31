<?php
	global $xoopsConfig , $xoopsDB , $xoopsUser;

	//get trust dir name from blocks
	$mytrustdirname = isset($mytrustdirname) ?  $mytrustdirname : basename( dirname( dirname( __FILE__ ) ) ) ;

	// module information
	$mod_url = XOOPS_URL . "/modules/$mydirname" ;
	$mod_path = XOOPS_ROOT_PATH . "/modules/$mydirname" ;
	$mod_trust_path = XOOPS_TRUST_PATH . "/modules/$mytrustdirname" ;
	$mod_copyright = "<a href='http://xoops.iko-ze.net'><strong>GNavi</strong></a> &nbsp; <span style='font-size:0.8em;'>(based on <a href='http://www.peak.ne.jp/'>MyAlbum-P</a>)</span>" ;

	// global langauge file
	$language = $xoopsConfig['language'] ;
	if ( file_exists( "$mod_trust_path/language/$language/gnavi_constants.php" ) ) {
		include_once "$mod_trust_path/language/$language/gnavi_constants.php" ;
	} else {
		include_once "$mod_trust_path/language/english/gnavi_constants.php" ;
		$language = "english" ;
	}

	// read from xoops_config
	// get my mid
	$rs = $xoopsDB->query( "SELECT mid FROM ".$xoopsDB->prefix('modules')." WHERE dirname='$mydirname'" ) ;
	list( $gnavi_mid ) = $xoopsDB->fetchRow( $rs ) ;

	// read configs from xoops_config directly
	$rs = $xoopsDB->query( "SELECT conf_name,conf_value FROM ".$xoopsDB->prefix('config')." WHERE conf_modid=$gnavi_mid" ) ;
	while( list( $key , $val ) = $xoopsDB->fetchRow( $rs ) ) {
		$gnavi_configs[ $key ] = $val ;
	}

	foreach( $gnavi_configs as $key => $val ) {
		if( strncmp( $key , "gnavi_" , 6 ) == 0 ) $$key = $val ;
	}

	// User Informations
	if( empty( $xoopsUser ) ) {
		$my_uid = 0 ;
		$isadmin = false ;
	} else {
		$my_uid = $xoopsUser->uid() ;
		$isadmin = $xoopsUser->isAdmin( $gnavi_mid ) ;
	}

	// Value Check
	$gnavi_addposts = intval( $gnavi_addposts ) ;
	if( $gnavi_addposts < 0 ) $gnavi_addposts = 0 ;

	// Path to Main Photo & Thumbnail ;
	if( ord( $gnavi_photospath ) != 0x2f ) $gnavi_photospath = "/$gnavi_photospath" ;
	if( ord( $gnavi_thumbspath ) != 0x2f ) $gnavi_thumbspath = "/$gnavi_thumbspath" ;
	$photos_dir = XOOPS_ROOT_PATH . $gnavi_photospath ;
	$photos_url = XOOPS_URL . $gnavi_photospath ;
	$icon_dir = $photos_dir.'/icon' ;
	$icon_url = $photos_url.'/icon' ;
	$qrimg_dir = $photos_dir.'/qr' ;
	$qrimg_url = $photos_url.'/qr' ;
;

	if( $gnavi_makethumb ) {
		$thumbs_dir = XOOPS_ROOT_PATH . $gnavi_thumbspath ;
		$thumbs_url = XOOPS_URL . $gnavi_thumbspath ;
	} else {
		$thumbs_dir = $photos_dir ;
		$thumbs_url = $photos_url ;
	}



	// DB table name
	$table_photos = $xoopsDB->prefix( "{$mydirname}_photos" ) ;
	$table_cat = $xoopsDB->prefix( "{$mydirname}_cat" ) ;
	$table_icon = $xoopsDB->prefix( "{$mydirname}_icons" ) ;
	$table_text = $xoopsDB->prefix( "{$mydirname}_text" ) ;
	$table_votedata = $xoopsDB->prefix( "{$mydirname}_votedata" ) ;
	$table_comments = $xoopsDB->prefix( "xoopscomments" ) ;

	// Pipe environment check
	if( $gnavi_imagingpipe || function_exists( 'imagerotate' ) ) $gnavi_canrotate = true ;
	else $gnavi_canrotate = false ;
	if( $gnavi_imagingpipe || $gnavi_forcegd2 ) $gnavi_canresize = true ;
	else $gnavi_canresize = false ;

	// Normal Extensions of Image
	$gnavi_normal_exts = array( 'jpg' , 'jpeg' , 'gif' , 'png' ) ;

	// Allowed extensions & MIME types
	if( empty( $gnavi_allowedexts ) ) {
		$array_allowed_exts = $gnavi_normal_exts ;
	} else {
		$array_allowed_exts = explode( '|' , $gnavi_allowedexts ) ;
	}
	if( empty( $gnavi_allowedmime ) ) {
		$array_allowed_mimetypes = array() ;
	} else {
		$array_allowed_mimetypes = explode( '|' , $gnavi_allowedmime ) ;
	}
?>