<?php
/**
 * Altsys library (UI-Components) for D3 modules
 *
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

altsys_set_module_config();

function altsys_set_module_config() {
	global $altsysModuleConfig, $altsysModuleId;

	$module_handler =& xoops_gethandler( 'module' );
	$module =& $module_handler->getByDirname( 'altsys' );

	if ( is_object( $module ) ) {
		$config_handler     =& xoops_gethandler( 'config' );
		$altsysModuleConfig = $config_handler->getConfigList( $module->getVar( 'mid' ) );
		$altsysModuleId     = $module->getVar( 'mid' );
	} else {
		$altsysModuleConfig = [];
		$altsysModuleId     = 0;
	}

	// for RTL users
	if (!defined("_ADM_USE_RTL")) define("_ADM_USE_RTL", 0);
	@define( '_GLOBAL_LEFT', 1 == @_ADM_USE_RTL ? 'right' : 'left' );
	@define( '_GLOBAL_RIGHT', 1 == @_ADM_USE_RTL ? 'left' : 'right' );
}


function altsys_include_mymenu() {
	global $xoopsModule, $xoopsConfig, $mydirname, $mydirpath, $mytrustdirname, $mytrustdirpath, $mymenu_fake_uri;

	$mymenu_find_paths = [
		$mydirpath . '/admin/mymenu.php',
		$mydirpath . '/mymenu.php',
		$mytrustdirpath . '/admin/mymenu.php',
		$mytrustdirpath . '/mymenu.php',
	];

	foreach ( $mymenu_find_paths as $mymenu_find_path ) {
		if ( is_file( $mymenu_find_path ) ) {
			include $mymenu_find_path;
			break;
		}
	}
}


/**
 * @param $type
 */
function altsys_include_language_file( $type ) {
	$mylang = $GLOBALS['xoopsConfig']['language'];

	if ( is_file( XOOPS_ROOT_PATH . '/modules/altsys/language/' . $mylang . '/' . $type . '.php' ) ) {
		include_once XOOPS_ROOT_PATH . '/modules/altsys/language/' . $mylang . '/' . $type . '.php';
	} elseif ( is_file( XOOPS_TRUST_PATH . '/libs/altsys/language/' . $mylang . '/' . $type . '.php' ) ) {
		include_once XOOPS_TRUST_PATH . '/libs/altsys/language/' . $mylang . '/' . $type . '.php';
	} elseif ( is_file( XOOPS_ROOT_PATH . '/modules/altsys/language/english/' . $type . '.php' ) ) {
		include_once XOOPS_ROOT_PATH . '/modules/altsys/language/english/' . $type . '.php';
	} elseif ( is_file( XOOPS_TRUST_PATH . '/libs/altsys/language/english/' . $type . '.php' ) ) {
		include_once XOOPS_TRUST_PATH . '/libs/altsys/language/english/' . $type . '.php';
	}
}

const ALTSYS_CORE_TYPE_XCL21 = 16; // XOOPSCube 2.1 Legacy

/**
 * @return int|null
 */
function altsys_get_core_type() {

	static $result = null;

	if ( empty( $result ) && defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$result = ALTSYS_CORE_TYPE_XCL21;
	}

	return $result;
}

/**
 * Use system legacy for preferences
 * @param $mid
 *
 * @return string
 */

function altsys_get_link2modpreferences( $mid ) {
	return XOOPS_URL . '/modules/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $mid;
}

/**
 * @param $tpl_id
 */

function altsys_template_touch( $tpl_id ) {
	// just touch the template
	xoops_template_touch( $tpl_id );
}


function altsys_clear_templates_c() {
	$dh = opendir( XOOPS_COMPILE_PATH );
	while ( $file = readdir( $dh ) ) {

		if ( '.' == substr( $file, 0, 1 ) ) {
			continue;
		}

		if ( '.php' != substr( $file, - 4 ) ) {
			continue;
		}
		@unlink( XOOPS_COMPILE_PATH . '/' . $file );
	}
	closedir( $dh );
}
