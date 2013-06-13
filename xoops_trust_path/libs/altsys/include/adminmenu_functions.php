<?php

define( 'ALTSYS_ADMINMENU_FILE' , XOOPS_CACHE_PATH.'/adminmenu.php' ) ;
define( 'ALTSYS_ADMINMENU_HACK_NONE' , 0 ) ;
define( 'ALTSYS_ADMINMENU_HACK_2COL' , 1 ) ;
define( 'ALTSYS_ADMINMENU_HACK_NOIMG' , 2 ) ;
define( 'ALTSYS_ADMINMENU_HACK_XCSTY' , 3 ) ;


//
// insert/replace items of mymenu into adminmenu
//
function altsys_adminmenu_insert_mymenu( &$module )
{
	global $altsysModuleConfig ;

	if( empty( $altsysModuleConfig['adminmenu_insert_mymenu'] ) ) return ;

	if( altsys_get_core_type() < ALTSYS_CORE_TYPE_ORE ) {
		altsys_adminmenu_insert_mymenu_x20( $module ) ;
	}
}

function altsys_adminmenu_insert_mymenu_x20( &$module )
{
	// read
	if( ! file_exists( ALTSYS_ADMINMENU_FILE ) ) {
		redirect_header( XOOPS_URL.'/admin.php' , 1 , 'Rebuild adminmenu' ) ;
		exit ;
	}
	$not_inside_cp_functions = true ;
	include ALTSYS_ADMINMENU_FILE ;

	$dirname = $module->getVar( 'dirname' ) ;
	$mid = $module->getVar( 'mid' ) ;
	$anchor = '<!-- ALTSYS ANCHOR '.$dirname.' -->' ;

	// fetch popup_no
	if( empty( $xoops_admin_menu_ft[$mid] ) ) return ;
	if( ! preg_match( '/popUpL(\d+)/' , $xoops_admin_menu_ft[$mid] , $regs ) ) return ;
	$popup_no = intval( $regs[1] ) ;

	// replace
	$search  = '<img src=\''.XOOPS_URL.'/images/pointer.gif\' width=\'8\' height=\'8\' alt=\'\' />&nbsp;<a href=\''.XOOPS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='.$mid.'\'' ;
	$replace = $anchor.'<img src=\''.XOOPS_URL.'/images/pointer.gif\' width=\'8\' height=\'8\' alt=\'\' />&nbsp;<a href=\''.XOOPS_URL.'/modules/'.$dirname.'/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mypreferences\'' ;

	// do replacement
	$new_xoops_admin_menu_dv = preg_replace( '#'.preg_quote($search).'#' , $replace , $xoops_admin_menu_dv ) ;

	if( $xoops_admin_menu_dv == $new_xoops_admin_menu_dv ) return ;
	$xoops_admin_menu_dv = $new_xoops_admin_menu_dv ;

	$insert = '' ;

	// insert blocksadmin
	if( $dirname != 'altsys' ) {
		$blocksadmin_title = defined( '_MD_A_MYMENU_MYBLOCKSADMIN' ) ? _MD_A_MYMENU_MYBLOCKSADMIN : 'blocksadmin' ;
		$insert .= '<img src=\''.XOOPS_URL.'/images/pointer.gif\' width=\'8\' height=\'8\' alt=\'\' />&nbsp;<a href=\''.XOOPS_URL.'/modules/'.$dirname.'/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin\'>'.$blocksadmin_title.'</a><br />'."\n" ;
	}

	// insert tplsadmin
	$db =& Database::getInstance() ;
	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix("tplfile")." WHERE tpl_module='$dirname'" ) ) ;
	if( $count > 0 ) {
		$tplsadmin_title = defined( '_MD_A_MYMENU_MYTPLSADMIN' ) ? _MD_A_MYMENU_MYTPLSADMIN : 'tplsadmin' ;
		$insert = '<img src=\''.XOOPS_URL.'/images/pointer.gif\' width=\'8\' height=\'8\' alt=\'\' />&nbsp;<a href=\''.XOOPS_URL.'/modules/'.$dirname.'/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mytplsadmin\'>'.$tplsadmin_title.'</a><br />'."\n".$insert ;
	}

	// do insertion
	$xoops_admin_menu_dv = preg_replace( '#'.preg_quote($anchor).'#' , $anchor.$insert , $xoops_admin_menu_dv ) ;

	// write back
	altsys_adminmenu_save_x20( array( 'xoops_admin_menu_js' => $xoops_admin_menu_js , 'xoops_admin_menu_ml' => $xoops_admin_menu_ml , 'xoops_admin_menu_sd' => $xoops_admin_menu_sd , 'xoops_admin_menu_ft' => $xoops_admin_menu_ft , 'xoops_admin_menu_dv' => $xoops_admin_menu_dv , 'altsys_adminmenu_ft_hacked' => intval( @$altsys_adminmenu_ft_hacked ) , 'altsys_adminmenu_dv_updated' => true ) ) ;
}

//
// modify the first layer of adminmenu (just for X20)
//
function altsys_adminmenu_hack_ft()
{
	global $altsysModuleConfig ;

	if( altsys_get_core_type() >= ALTSYS_CORE_TYPE_ORE ) return ;
	if( empty( $altsysModuleConfig['adminmenu_hack_ft'] ) ) return ;

	if( $altsysModuleConfig['adminmenu_hack_ft'] == ALTSYS_ADMINMENU_HACK_2COL ) {
		altsys_adminmenu_hack_ft_2col_x20() ;
	} else if( $altsysModuleConfig['adminmenu_hack_ft'] == ALTSYS_ADMINMENU_HACK_NOIMG ) {
		altsys_adminmenu_hack_ft_noimg_x20() ;
	} else if( $altsysModuleConfig['adminmenu_hack_ft'] == ALTSYS_ADMINMENU_HACK_XCSTY ) {
		altsys_adminmenu_hack_ft_xcsty_x20() ;
	}
}

function altsys_adminmenu_hack_ft_2col_x20()
{
	// read
	if( ! file_exists( ALTSYS_ADMINMENU_FILE ) ) {
		redirect_header( XOOPS_URL.'/admin.php' , 1 , 'Rebuild adminmenu' ) ;
		exit ;
	}
	$not_inside_cp_functions = true ;
	include ALTSYS_ADMINMENU_FILE ;

	// check previous hack
	if( ! empty( $altsys_adminmenu_ft_hacked ) ) {
		if( $altsys_adminmenu_ft_hacked == ALTSYS_ADMINMENU_HACK_2COL ) {
			// skip
			return ;
		} else {
			// rebuild adminmenu
			require_once XOOPS_ROOT_PATH.'/include/cp_functions.php' ;
			xoops_module_write_admin_menu( xoops_module_get_admin_menu() ) ;
			// backup $xoops_admin_menu_dv
			$backup_admin_menu_dv = $xoops_admin_menu_dv ;
			// include new adminmenu
			include ALTSYS_ADMINMENU_FILE ;
			// restore $xoops_admin_menu_dv
			$xoops_admin_menu_dv = $backup_admin_menu_dv ;
		}
	}

	$search  = ' alt=\'\' /></a><br />' ;
	$replace_fmt = ' alt="%s" /></a>' ;

	$is_left = true ;
	$module_handler =& xoops_gethandler( 'module' ) ;
	$mids = array_keys( $xoops_admin_menu_ft ) ;
	$last_mid = $mids[ sizeof( $mids ) - 1 ] ;
	foreach( $mids as $mid ) {
		$module =& $module_handler->get( $mid ) ;
		$new_menu_ft = preg_replace( '#'.preg_quote($search).'#' , sprintf( $replace_fmt , $module->getVar('name') ) , $xoops_admin_menu_ft[$mid] ) ;
		if( $is_left ) {
			if( $mid == $last_mid ) {
				$xoops_admin_menu_ft[$mid] = $new_menu_ft . '</td><td>' ;
			} else {
				$left_body = $new_menu_ft ;
				$left_key = $mid ;
				$xoops_admin_menu_ml[$mid] = str_replace( ',105);' , ',85);' , $xoops_admin_menu_ml[$mid] ) ;
			}
		} else {
			$xoops_admin_menu_ft[$mid] = $left_body . '</td><td>' . $new_menu_ft ;
			unset( $xoops_admin_menu_ft[$left_key] ) ;
			$xoops_admin_menu_ml[$mid] = str_replace( ',105);' , ',185);' , $xoops_admin_menu_ml[$mid] ) ;
		}
		$is_left = ! $is_left ;
	}

	// write back
	altsys_adminmenu_save_x20( array( 'xoops_admin_menu_js' => $xoops_admin_menu_js , 'xoops_admin_menu_ml' => $xoops_admin_menu_ml , 'xoops_admin_menu_sd' => $xoops_admin_menu_sd , 'xoops_admin_menu_ft' => $xoops_admin_menu_ft , 'xoops_admin_menu_dv' => $xoops_admin_menu_dv , 'altsys_adminmenu_ft_hacked' => ALTSYS_ADMINMENU_HACK_2COL ) ) ;
}


function altsys_adminmenu_hack_ft_noimg_x20()
{
	// read
	if( ! file_exists( ALTSYS_ADMINMENU_FILE ) ) {
		redirect_header( XOOPS_URL.'/admin.php' , 1 , 'Rebuild adminmenu' ) ;
		exit ;
	}
	$not_inside_cp_functions = true ;
	include ALTSYS_ADMINMENU_FILE ;

	// check previous hack
	if( ! empty( $altsys_adminmenu_ft_hacked ) ) {
		if( $altsys_adminmenu_ft_hacked == ALTSYS_ADMINMENU_HACK_NOIMG ) {
			// skip
			return ;
		} else {
			// rebuild adminmenu
			require_once XOOPS_ROOT_PATH.'/include/cp_functions.php' ;
			xoops_module_write_admin_menu( xoops_module_get_admin_menu() ) ;
			// backup $xoops_admin_menu_dv
			$backup_admin_menu_dv = $xoops_admin_menu_dv ;
			// include new adminmenu
			include ALTSYS_ADMINMENU_FILE ;
			// restore $xoops_admin_menu_dv
			$xoops_admin_menu_dv = $backup_admin_menu_dv ;
		}
	}

	$module_handler =& xoops_gethandler( 'module' ) ;
	$mids = array_keys( $xoops_admin_menu_ft ) ;
	foreach( $mids as $mid ) {
		$module =& $module_handler->get( $mid ) ;
		$xoops_admin_menu_ft[$mid] = preg_replace( '/\<img src\=.*$/' , $module->getVar('name').'</a>' , $xoops_admin_menu_ft[$mid] ) ;
		$xoops_admin_menu_ft[$mid] = '<div style="text-align:'._GLOBAL_LEFT.';background-color:#CCC;" title="'.$module->getVar('dirname').'">'.$xoops_admin_menu_ft[$mid].'</div>' ;
		$xoops_admin_menu_ml[$mid] = str_replace( ',105);' , ',45);' , $xoops_admin_menu_ml[$mid] ) ;
	}

	// write back
	altsys_adminmenu_save_x20( array( 'xoops_admin_menu_js' => $xoops_admin_menu_js , 'xoops_admin_menu_ml' => $xoops_admin_menu_ml , 'xoops_admin_menu_sd' => $xoops_admin_menu_sd , 'xoops_admin_menu_ft' => $xoops_admin_menu_ft , 'xoops_admin_menu_dv' => $xoops_admin_menu_dv , 'altsys_adminmenu_ft_hacked' => ALTSYS_ADMINMENU_HACK_NOIMG ) ) ;
}


function altsys_adminmenu_hack_ft_xcsty_x20()
{
	// read
	if( ! file_exists( ALTSYS_ADMINMENU_FILE ) ) {
		redirect_header( XOOPS_URL.'/admin.php' , 1 , 'Rebuild adminmenu' ) ;
		exit ;
	}
	$not_inside_cp_functions = true ;
	include ALTSYS_ADMINMENU_FILE ;

	// check previous hack
	if( ! empty( $altsys_adminmenu_ft_hacked ) ) {
		if( $altsys_adminmenu_ft_hacked == ALTSYS_ADMINMENU_HACK_XCSTY && empty( $altsys_adminmenu_dv_updated ) ) {
			// skip
			return ;
		} else {
			// rebuild adminmenu
			require_once XOOPS_ROOT_PATH.'/include/cp_functions.php' ;
			$fp = fopen( ALTSYS_ADMINMENU_FILE , 'wb' ) ;
			fwrite( $fp , xoops_module_get_admin_menu() ) ;
			fclose( $fp ) ;
			// backup $xoops_admin_menu_dv
			$backup_admin_menu_dv = $xoops_admin_menu_dv ;
			// include new adminmenu
			include ALTSYS_ADMINMENU_FILE ;
			// restore $xoops_admin_menu_dv
			$xoops_admin_menu_dv = $backup_admin_menu_dv ;
		}
	}

	$module_handler =& xoops_gethandler( 'module' ) ;
	$mids = array_keys( $xoops_admin_menu_ft ) ;
	foreach( $mids as $mid ) {
		$module =& $module_handler->get( $mid ) ;
		$submenuitems = array() ;
		if( preg_match( '/popUpL\d+/' , $xoops_admin_menu_ft[$mid] , $regs ) ) {
			$popup = $regs[0] ;
			preg_match_all( '#\<a href.*'.$popup.'\(\).*\</a>#U' , $xoops_admin_menu_dv , $regs ) ;
			foreach( $regs[0] as $submenuitem ) {
				$submenuitems[] = str_replace( $popup.'();' , '' , $submenuitem ) ;
			}
		} else return ;
		// module icon
		if( preg_match( '#\<img .*/\>#U' , $xoops_admin_menu_ft[$mid] , $regs ) ) {
			$icon_img = str_replace( "alt=''" , 'alt="'.$module->getVar('name').'"' , $regs[0] ) ;
		} else {
			$icon_img = '' ;
		}
		// version number
		$icon_img .= '<span class="version" style="">' . sprintf( '%.2f' , $module->getVar('version') / 100.0 ) . '</span>' ;
		$newline = preg_replace( '/ onmouseover.*$/' , '' , $xoops_admin_menu_ft[$mid] ) ;
		$newline = "\n".'<!-- '.$popup.' --><div id="adminmenu_ft'.$mid.'" style="text-align:'._GLOBAL_LEFT.';background-color:#CCC;" title="'.$module->getVar('dirname').'"><a id="adminmenu_ftpoint'.$mid.'" href="javascript:void(0);" onclick="submenuToggle('.$mid.');">+</a> '.$newline.'>'.$module->getVar('name').'</a></div><div id="adminmenu_ftsub'.$mid.'" style="display:none;"><ul>' ;
		foreach( $submenuitems as $submenuitem ) {
			$newline .= '<li>'.$submenuitem.'</li>' ;
		}
		$newline .= '</ul>'.$icon_img.'</div>' ;
		$xoops_admin_menu_ft[$mid] = $newline ;
	}

	$xoops_admin_menu_js = "
	function submenuToggle(mid) {
		el = xoopsGetElementById('adminmenu_ftsub'+mid).style;
		if (el.display == 'block') {
			el.display = 'none';
			xoopsGetElementById('adminmenu_ftpoint'+mid).innerHTML = '+' ;
		} else {
			el.display = 'block';
			xoopsGetElementById('adminmenu_ftpoint'+mid).innerHTML = '-' ;
		}
	}" ;

	// write back
	altsys_adminmenu_save_x20( array( 'xoops_admin_menu_js' => $xoops_admin_menu_js , 'xoops_admin_menu_ml' => array() , 'xoops_admin_menu_sd' => array() , 'xoops_admin_menu_ft' => $xoops_admin_menu_ft , 'xoops_admin_menu_dv' => $xoops_admin_menu_dv , 'altsys_adminmenu_ft_hacked' => ALTSYS_ADMINMENU_HACK_XCSTY ) ) ;
}


//
// common functions about adminmenu
//

function altsys_adminmenu_save_x20( $xoops_admin_vars )
{
	// variable definitions
	ob_start() ;
	echo "<?php\n// modified by altsys\nif( ! defined('XOOPS_ROOT_PATH') ) exit ;\n" ;
	foreach( $xoops_admin_vars as $key => $val ) {
		echo '$' . $key . " = \n" ;
		@var_export( $val ) ;
		echo " ;\n" ;
	}
	$output = ob_get_contents() ;
	ob_end_clean() ;

	// embedding logics
	if( in_array( @$xoops_admin_vars['altsys_adminmenu_ft_hacked'] , array( ALTSYS_ADMINMENU_HACK_NOIMG , ALTSYS_ADMINMENU_HACK_XCSTY ) ) ) {
		$output .= '

if( is_object( @$GLOBALS["xoopsModule"] ) && empty( $not_inside_cp_functions ) ) {
	$mid_tmp = $GLOBALS["xoopsModule"]->getVar("mid") ;
	if( $mid_tmp == 1 && @$_GET["fct"] == "preferences" && @$_GET["op"] == "showmod" && ! empty( $_GET["mod"] ) ) $mid_tmp = intval( $_GET["mod"] ) ;
	$xoops_admin_menu_ft[ $mid_tmp ] = str_replace( array( "background-color:#CCC;" , "display:none;" ) , array( "background-color:#AAA;" , "display:block;" ) , $xoops_admin_menu_ft[ $mid_tmp ] ) ;
	if( $GLOBALS["xoopsModule"]->getInfo("version") > $GLOBALS["xoopsModule"]->getVar("version") / 100.0 + 0.0001 ) {
		$xoops_admin_menu_ft[ $mid_tmp ] = str_replace( "class=\"version\" style=\"\"" , "class=\"version\" style=\"color:red;\"" , $xoops_admin_menu_ft[ $mid_tmp ] ) ;
	} ;
}
		' ;
	}

	// termination
	$output .= "\n\n?>" ;

	// replace into XOOPS_URL
	$output = str_replace( XOOPS_URL , "'.XOOPS_URL.'" , $output ) ;

	// output
	$fp = fopen( ALTSYS_ADMINMENU_FILE , 'wb' ) ;
	fwrite( $fp , $output ) ;
	fclose( $fp ) ;
}


?>