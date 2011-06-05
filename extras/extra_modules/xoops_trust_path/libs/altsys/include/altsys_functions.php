<?php

altsys_set_module_config() ;

function altsys_set_module_config()
{
	global $altsysModuleConfig , $altsysModuleId ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( 'altsys' ) ;
	if( is_object( $module ) ) {
		$config_handler =& xoops_gethandler( 'config' ) ;
		$altsysModuleConfig = $config_handler->getConfigList( $module->getVar('mid') ) ;
		$altsysModuleId = $module->getVar('mid') ;
	} else {
		$altsysModuleConfig = array() ;
		$altsysModuleId = 0 ;
	}

	// for RTL users
	@define( '_GLOBAL_LEFT' , @_ADM_USE_RTL == 1 ? 'right' : 'left' ) ;
	@define( '_GLOBAL_RIGHT' , @_ADM_USE_RTL == 1 ? 'left' : 'right' ) ;
}


function altsys_include_mymenu()
{
	global $xoopsModule , $xoopsConfig , $mydirname , $mydirpath , $mytrustdirname , $mytrustdirpath , $mymenu_fake_uri ;

	$mymenu_find_paths = array(
		$mydirpath.'/admin/mymenu.php' ,
		$mydirpath.'/mymenu.php' ,
		$mytrustdirpath.'/admin/mymenu.php' ,
		$mytrustdirpath.'/mymenu.php' ,
	) ;

	foreach( $mymenu_find_paths as $mymenu_find_path ) {
		if( file_exists( $mymenu_find_path ) ) {
			include $mymenu_find_path ;
			include_once dirname(__FILE__).'/adminmenu_functions.php' ;
			altsys_adminmenu_insert_mymenu( $xoopsModule ) ;
			altsys_adminmenu_hack_ft() ;
			break ;
		}
	}
}


function altsys_include_language_file( $type )
{
	$mylang = $GLOBALS['xoopsConfig']['language'] ;

	if( file_exists( XOOPS_ROOT_PATH.'/modules/altsys/language/'.$mylang.'/'.$type.'.php' ) ) {
		include_once XOOPS_ROOT_PATH.'/modules/altsys/language/'.$mylang.'/'.$type.'.php' ;
	} else if( file_exists( XOOPS_TRUST_PATH.'/libs/altsys/language/'.$mylang.'/'.$type.'.php' ) ) {
		include_once XOOPS_TRUST_PATH.'/libs/altsys/language/'.$mylang.'/'.$type.'.php' ;
	} else if( file_exists( XOOPS_ROOT_PATH.'/modules/altsys/language/english/'.$type.'.php' ) ) {
		include_once XOOPS_ROOT_PATH.'/modules/altsys/language/english/'.$type.'.php' ;
	} else if( file_exists( XOOPS_TRUST_PATH.'/libs/altsys/language/english/'.$type.'.php' ) ) {
		include_once XOOPS_TRUST_PATH.'/libs/altsys/language/english/'.$type.'.php' ;
	}
}


define( 'ALTSYS_CORE_TYPE_X20' , 1 ) ; // 2.0.0-2.0.13 and 2.0.x-JP
define( 'ALTSYS_CORE_TYPE_X20S' , 2 ) ; // 2.0.14- from xoops.org (Skalpa's S)
define( 'ALTSYS_CORE_TYPE_ORE' , 4 ) ; // ORETEKI by marijuana
define( 'ALTSYS_CORE_TYPE_X22' , 8 ) ; // 2.2 from xoops.org
define( 'ALTSYS_CORE_TYPE_X23P' , 10 ) ; // 2.3 from xoops.org (phppp's P)
define( 'ALTSYS_CORE_TYPE_ICMS' , 12 ) ; // ImpressCMS
define( 'ALTSYS_CORE_TYPE_XCL21' , 16 ) ; // XOOPS Cube 2.1 Legacy

function altsys_get_core_type()
{
	static $result = null ;

	if( empty( $result ) ) {
		if( defined( 'XOOPS_ORETEKI' ) ) $result = ALTSYS_CORE_TYPE_ORE ;
		else if( defined( 'XOOPS_CUBE_LEGACY' ) ) $result = ALTSYS_CORE_TYPE_XCL21 ;
		else if( defined( 'ICMS_VERSION_NAME' ) ) $result = ALTSYS_CORE_TYPE_ICMS ;
		else if( strstr( XOOPS_VERSION , 'JP' ) ) $result = ALTSYS_CORE_TYPE_X20 ;
		else {
			$versions = array_map( 'intval' , explode( '.' , preg_replace( '/[^0-9.]/' , '' , XOOPS_VERSION ) ) ) ;
			if( $versions[0] == 2 && $versions[1] == 2 ) {
				$result = ALTSYS_CORE_TYPE_X22 ;
			} else if( $versions[0] == 2 && $versions[1] == 0 && $versions[2] > 13 ) {
				$result = ALTSYS_CORE_TYPE_X20S ;
			} else if( $versions[0] == 2 && $versions[1] > 2 ) {
				$result = ALTSYS_CORE_TYPE_X23P ;
			} else {
				$result = ALTSYS_CORE_TYPE_X20 ;
			}
		}
	}
	return $result ;
}


function altsys_get_link2modpreferences( $mid , $coretype )
{
	switch( $coretype ) {
		case ALTSYS_CORE_TYPE_X20 :
		case ALTSYS_CORE_TYPE_X20S :
		case ALTSYS_CORE_TYPE_ORE :
		case ALTSYS_CORE_TYPE_X22 :
		case ALTSYS_CORE_TYPE_X23P :
		case ALTSYS_CORE_TYPE_ICMS :
		default :
			return XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=showmod&mod='.$mid ;
		case ALTSYS_CORE_TYPE_XCL21 :
			return XOOPS_URL.'/modules/legacy/admin/index.php?action=PreferenceEdit&confmod_id='.$mid ;
	}
}


function altsys_template_touch( $tpl_id )
{
	if( in_array( altsys_get_core_type() , array( ALTSYS_CORE_TYPE_X20S , ALTSYS_CORE_TYPE_X23P ) ) ) {
		// need to delete all files under templates_c/
		altsys_clear_templates_c() ;
	} else {
		// just touch the template
		xoops_template_touch( $tpl_id ) ;
	}
}


function altsys_clear_templates_c()
{
	$dh = opendir( XOOPS_COMPILE_PATH ) ;
	while( $file = readdir( $dh ) ) {
		if( substr( $file , 0 , 1 ) == '.' ) continue ;
		if( substr( $file , -4 ) != '.php' ) continue ;
		@unlink( XOOPS_COMPILE_PATH.'/'.$file ) ;
	}
	closedir( $dh ) ;
}


?>