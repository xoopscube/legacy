<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

if( empty( $mydirname ) ) $mydirname = basename(dirname(dirname(__FILE__))) ;

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

//	array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => '../system/admin.php?fct=preferences&op=showmod&mod=' . $module->getvar('mid') ) ) ;
	$menuitem_dirname = $module->getvar('dirname') ;

	if( defined( 'XOOPS_TRUST_PATH' ) ) {
		array_pop( $adminmenu );
		array_pop( $adminmenu );
		// with XOOPS_TRUST_PATH and altsys

		if( file_exists( XOOPS_TRUST_PATH.'/libs/altsys/mytplsadmin.php' ) ) {
			// mytplsadmin (TODO check if this module has tplfile)
			$title = defined( '_MD_A_MYMENU_MYTPLSADMIN' ) ? _MD_A_MYMENU_MYTPLSADMIN : 'tplsadmin' ;
			array_push( $adminmenu , array( 'title' => $title , 'link' => 'admin/index.php?mode=admin&lib=altsys&page=mytplsadmin' ) ) ;
		}

		if( file_exists( XOOPS_TRUST_PATH.'/libs/altsys/myblocksadmin.php' ) ) {
			// myblocksadmin
			$title = defined( '_MD_A_MYMENU_MYBLOCKSADMIN' ) ? _MD_A_MYMENU_MYBLOCKSADMIN : 'blocksadmin' ;
			array_push( $adminmenu , array( 'title' => $title , 'link' => 'admin/index.php?mode=admin&lib=altsys&page=myblocksadmin' ) ) ;
		}

		if( file_exists( XOOPS_TRUST_PATH.'/libs/altsys/mylangadmin.php' ) ) {
			// mylangadmin
			$title = defined( '_MD_A_MYMENU_MYLANGADMIN' ) ? _MD_A_MYMENU_MYLANGADMIN : 'langadmin' ;
			array_push( $adminmenu , array( 'title' => $title , 'link' => 'admin/index.php?mode=admin&lib=altsys&page=mylangadmin' ) ) ;
		}

		// preferences
		$config_handler =& xoops_gethandler('config');
		if( count( $config_handler->getConfigs( new Criteria( 'conf_modid' , $module->mid() ) ) ) > 0 ) {
			if( file_exists( XOOPS_TRUST_PATH.'/libs/altsys/mypreferences.php' ) ) {
				// mypreferences
				$title = defined( '_MD_A_MYMENU_MYPREFERENCES' ) ? _MD_A_MYMENU_MYPREFERENCES : _PREFERENCES ;
				array_push( $adminmenu , array( 'title' => $title , 'link' => 'admin/index.php?mode=admin&lib=altsys&page=mypreferences' ) ) ;
			} else if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
				// Cube Legacy without altsys
				array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => XOOPS_URL.'/modules/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $module->getvar('mid') ) ) ;
			} else {
				// system->preferences
				array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=showmod&mod='.$module->mid() ) ) ;
			}
		}

	} else if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		// Cube Legacy without altsys
		if( $module->getvar('hasconfig') ) array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => XOOPS_URL.'/modules/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $module->getvar('mid') ) ) ;
	} else {
		// conventinal X2
		if( $module->getvar('hasconfig') ) array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=showmod&mod=' . $module->getvar('mid') ) ) ;
	}

	$mymenu_uri = empty( $mymenu_fake_uri ) ? $_SERVER['REQUEST_URI'] : $mymenu_fake_uri ;
	$mymenu_link = substr( strstr( $mymenu_uri , '/admin/' ) , 1 ) ;

	// hilight
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
		echo "<div style='float:left;height:1.5em; white-space:nowrap;'><a href='".htmlspecialchars($menuitem['link'],ENT_QUOTES)."' style='background-color:{$menuitem['color']};font:normal normal bold 9pt/12pt;'>".htmlspecialchars($menuitem['title'],ENT_QUOTES)."</a> | </div>\n" ;
	}
	echo "</div>\n<hr style='clear:left;display:block;' />\n" ;

}

?>