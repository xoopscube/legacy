<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

if( empty( $mydirname ) ) $mydirname = basename(dirname(dirname(__FILE__))) ;

// Detect Altsys
// Not Active ALTSYS => $active_altsys = false;
// Active ALTSYS =>     $active_altsys = true;
$module_handler =& xoops_gethandler('module');
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('dirname','altsys'));
$criteria->add(new Criteria('isactive', 1));
$altsys_mod = $module_handler->getObjects($criteria);
if(empty($altsys_mod)) $active_altsys = false; else $active_altsys = true;


if( ! defined( 'XOOPS_ORETEKI' ) ) {
	// Skip for ORETEKI XOOPS

	if( ! isset( $module ) || ! is_object( $module ) ) $module = $xoopsModule ;
	else if( ! is_object( $xoopsModule ) ) die( '$xoopsModule is not set' )  ;

	// load modinfo.php if necessary (judged by a specific constant is defined)
	if( ! defined( '_MYMENU_CONSTANT_IN_MODINFO' ) || ! defined( _MYMENU_CONSTANT_IN_MODINFO ) ) {
		if( file_exists("../language/".$xoopsConfig['language']."/modinfo.php") ) {
			include_once("../language/".$xoopsConfig['language']."/modinfo.php");
		} else {
			include_once("../language/english/modinfo.php");
		}
	}

	include( './menu.php' ) ;

	$menuitem_dirname = $module->getvar('dirname') ;

	// mytplsadmin (TODO check if this module has tplfile)
	if( $active_altsys ) {
		$title = defined( '_MD_A_MYMENU_MYTPLSADMIN' ) ? _MD_A_MYMENU_MYTPLSADMIN : 'tplsadmin' ;
		array_push( $adminmenu , array( 'title' => $title , 'link' => 'admin/index.php?mode=admin&lib=altsys&page=mytplsadmin' ) ) ;
	}
	
	// myblocksadmin
	$title = defined( '_MD_A_MYMENU_MYBLOCKSADMIN' ) ? _MD_A_MYMENU_MYBLOCKSADMIN : 'blocksadmin' ;
	if( $active_altsys ){
		// mypreferences
		array_push( $adminmenu , array( 'title' => $title , 'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ) ) ;
	} else if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		// Cube Legacy without altsys
		array_push( $adminmenu , array( 'title' => $title , 'link' => XOOPS_URL.'/modules/legacy/admin/index.php?action=BlockList') ) ;
	} else if ( preg_match ( "/^ImpressCMS/" , XOOPS_VERSION ) ){
		// ImpressCMS
		array_push( $adminmenu , array( 'title' => $title , 'link' => XOOPS_URL.'/modules/system/admin.php?fct=blocksadmin&filtersel=mid&filtersel2='.$module->getvar('mid') ) ) ;
	} else {
		array_push( $adminmenu , array( 'title' => $title , 'link' => XOOPS_URL.'/modules/system/admin.php?fct=blocksadmin&op=list&filter=1&selgen='.$module->getvar('mid').'&selmod=-2&selgrp=-1&selvis=-1' ) ) ;
	}
	
	// mylangadmin
	if( $active_altsys ){
		$title = defined( '_MD_A_MYMENU_MYLANGADMIN' ) ? _MD_A_MYMENU_MYLANGADMIN : 'langadmin' ;
		array_push( $adminmenu , array( 'title' => $title , 'link' => 'admin/index.php?mode=admin&lib=altsys&page=mylangadmin' ) ) ;
	}
	
	// preferences
	if( $module->getvar('hasconfig') ){
		if( $active_altsys ){
			// mypreferences
			$title = defined( '_MD_A_MYMENU_MYPREFERENCES' ) ? _MD_A_MYMENU_MYPREFERENCES : _PREFERENCES ;
			array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences' ) ) ;
		} else if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			// Cube Legacy without altsys
			array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => XOOPS_URL.'/modules/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $module->getvar('mid') ) ) ;
		} else {
			// system->preferences
			array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=showmod&mod='.$module->mid() ) ) ;
		}
	}
	
	// hilight
	$mymenu_uri = empty( $mymenu_fake_uri ) ? $_SERVER['REQUEST_URI'] : $mymenu_fake_uri ;
	$mymenu_link = substr( strstr( $mymenu_uri , '/admin/' ) , 1 ) ;

	foreach( array_keys( $adminmenu ) as $i ) {
		if( $mymenu_link == $adminmenu[$i]['link'] ) {
			$adminmenu[$i]['color'] = '#FFCCCC' ;
			$adminmenu_hilighted = true ;
			$GLOBALS['altsysAdminPageTitle'] = $adminmenu[$i]['title'] ;
		} else {
			$adminmenu[$i]['color'] = '#DDDDDD' ;
		}
	}
	if( empty( $adminmenu_hilighted ) ) {
		foreach( array_keys( $adminmenu ) as $i ) {
			if( stristr( $mymenu_uri , $adminmenu[$i]['link'] ) ) {
				$adminmenu[$i]['color'] = '#FFCCCC' ;
				$GLOBALS['altsysAdminPageTitle'] = $adminmenu[$i]['title'] ;
				break ;
			}
		}
	}

	// link conversion from relative to absolute
	foreach( array_keys( $adminmenu ) as $i ) {
		if( stristr( $adminmenu[$i]['link'] , XOOPS_URL ) === false ) {
			$adminmenu[$i]['link'] = XOOPS_URL."/modules/$mydirname/" . $adminmenu[$i]['link'] ;
		}
	}

	// display
	echo "<div style='text-align:left;width:98%;'>" ;
	foreach( $adminmenu as $menuitem ) {
		echo "<div style='float:left;height:1.5em;'><nobr><a href='".htmlspecialchars($menuitem['link'],ENT_QUOTES)."' style='background-color:{$menuitem['color']};font:normal normal bold 9pt/12pt;'>".htmlspecialchars($menuitem['title'],ENT_QUOTES)."</a> | </nobr></div>\n" ;
	}
	echo "</div>\n<hr style='clear:left;display:block;' />\n" ;

}

?>