<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Smarty plugin Purpose:  Fetches templates from a database
 * @package    Altsys
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

/**
 * @param $tpl_name
 * @param mixed $tpl_source
 * @param mixed $smarty
 *
 * @return bool
 */
function smarty_resource_db_source( $tpl_name, &$tpl_source, &$smarty ) {
	if ( ! $tpl = smarty_resource_db_tplinfo( $tpl_name ) ) {
		return false;
	}
	if ( is_object( $tpl ) ) {
		$tpl_source = $tpl->getVar( 'tpl_source', 'n' );
	} else {
		$fp         = fopen( $tpl, 'r' );
		$tpl_source = fread( $fp, filesize( $tpl ) );
		fclose( $fp );
	}

	return true;
}

/**
 * @param $tpl_name
 * @param mixed $tpl_timestamp
 * @param mixed $smarty
 *
 * @return bool
 */
function smarty_resource_db_timestamp( $tpl_name, &$tpl_timestamp, &$smarty ) {
	if ( ! $tpl = smarty_resource_db_tplinfo( $tpl_name ) ) {
		return false;
	}
	if ( is_object( $tpl ) ) {
		$tpl_timestamp = $tpl->getVar( 'tpl_lastmodified', 'n' );
	} else {
		$tpl_timestamp = filemtime( $tpl );
	}

	return true;
}

/**
 * @param $tpl_name
 * @param mixed $smarty
 *
 * @return bool
 */
function smarty_resource_db_secure( $tpl_name, &$smarty ) {
	// assume all templates are secure
	return true;
}

/**
 * @param $tpl_name
 * @param mixed $smarty
 */
function smarty_resource_db_trusted( $tpl_name, &$smarty ) {
	// not used for templates
}

/**
 * @param $tpl_name
 *
 * @return bool|mixed|string
 */
function smarty_resource_db_tplinfo( $tpl_name ) {
	static $cache = [];
	global $xoopsConfig;

	if ( isset( $cache[ $tpl_name ] ) ) {
		return $cache[ $tpl_name ];
	}
	$tplset = $xoopsConfig['template_set'];
	//$theme = isset($xoopsConfig['theme_set']) ? $xoopsConfig['theme_set'] : 'default';
	$theme = $xoopsConfig['theme_set'] ?? 'default';

	$tplfile_handler =& xoops_gethandler( 'tplfile' );
	// If we're not using the "default" template set, then get the templates from the DB
	if ( 'default' != $tplset ) {
		$tplobj = $tplfile_handler->find( $tplset, null, null, null, $tpl_name, true );
		if ( is_countable($tplobj) ? count( $tplobj ) : 0 ) {
			return $cache[ $tpl_name ] = $tplobj[0];
		}
	}
	// If we are using the default tplset, get the template from the filesystem
	$tplobj = $tplfile_handler->find( 'default', null, null, null, $tpl_name, true );

	if ( ! (is_countable($tplobj) ? count( $tplobj ) : 0) ) {
		return $cache[ $tpl_name ] = false;
	}
	$tplobj    = $tplobj[0];
	$module    = $tplobj->getVar( 'tpl_module', 'n' );
	$type      = $tplobj->getVar( 'tpl_type', 'n' );
	$blockpath = ( 'block' == $type ) ? 'blocks/' : '';
	// First, check for an overloaded version within the theme folder @gigamaster modified theme folder structure
	$filepath = XOOPS_THEME_PATH . "/$theme/templates/$module/$blockpath$tpl_name";
	//$filepath = XOOPS_THEME_PATH . "/$theme/modules/$module/$blockpath$tpl_name";
	if ( ! file_exists( $filepath ) ) {
		// If no custom version exists, get the tpl from its default location
		$filepath = XOOPS_ROOT_PATH . "/modules/$module/templates/$blockpath$tpl_name";
		if ( ! file_exists( $filepath ) ) {
			return $cache[ $tpl_name ] = $tplobj;
		}
	}

	return $cache[ $tpl_name ] = $filepath;
}
