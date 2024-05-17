<?php
/**
 * Altsys library (UI-Components) for D3 modules
 *
 * @package    Altsys
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

/**
 * @param $langfile_unique_path
 * @param $mydirname
 *
 * @return array
 */
function altsys_mylangadmin_get_constant_names( $langfile_unique_path, $mydirname ) {

	$constpref = '';

	$langfile_names = [];

	$reqonce_ret = 0;

	$already_read = false;

	$langfile_fingerprint = '_MYLANGADMIN_' . md5( $langfile_unique_path );

	// for debug

	//require_once XOOPS_TRUST_PATH.'/modules/pico/language/japanese/modinfo.php' ;

	// get constant_names by "require"

	if ( ! defined( $langfile_fingerprint ) ) {
		$system_constants = array_keys( get_defined_constants() );

		$reqonce_ret = require_once $langfile_unique_path;

		$langfile_names = array_diff( array_keys( get_defined_constants() ), $system_constants );
	}

	// We have to parse the file if it has been already included ...

	if ( empty( $langfile_names ) && ( true === $reqonce_ret || defined( $langfile_fingerprint ) ) ) {
		$already_read = true;

		$langfile_names = altsys_mylangadmin_get_constant_names_by_pcre( $langfile_unique_path );
	}

	// modinfo.php of D3 module

	if ( empty( $langfile_names ) && file_exists( XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/mytrustdirname.php' ) /* && substr( $langfile_unique_path , -11 ) == 'modinfo.php' */ ) {
		// get $constpref

		$constpref = '';

		require $langfile_unique_path;

		$langfile_names = [];

		if ( $constpref ) {
			foreach ( array_keys( get_defined_constants() ) as $name ) {
				if ( 0 == strncmp( $name, $constpref, mb_strlen( $constpref ) ) ) {
					$langfile_names[] = $name;
				}
			}
		}
	}

	return [ $langfile_names, $constpref, $already_read ];
}

/**
 * @param $langfile_path
 *
 * @return array
 */
function altsys_mylangadmin_get_constant_names_by_pcre( $langfile_path ) {

	if ( ! file_exists( $langfile_path ) ) {
		return [];
	}

	$file_contents = file_get_contents( $langfile_path );

	preg_match_all( '/\n\s*define\(\s*(["\'])([0-9a-zA-Z_]+)\\1/iU', $file_contents, $matches );

	$langfile_names = [];

	foreach ( $matches[2] as $name ) {
		// if( defined( $name ) )

		$langfile_names[] = $name;
	}

	return $langfile_names;
}

/**
 * @param $langfile_path
 *
 * @return array
 */
function altsys_mylangadmin_get_constants_by_pcre( $langfile_path ) {

	if ( ! file_exists( $langfile_path ) ) {
		return [];
	}

	$file_contents = file_get_contents( $langfile_path );

	preg_match_all( '/\n\s*define\(\s*(["\'])([0-9a-zA-Z_]+)\\1\s*\,\s*(["\'])([^\\3]+)\\3/iU', $file_contents, $matches );

	$constants = [];

	foreach ( $matches[2] as $i => $name ) {
		$constants[ $name ] = $matches[4][ $i ];
	}

	return $constants;
}

/**
 * @param $target_mname
 * @param $message4disp
 */
function altsys_mylangadmin_errordie( $target_mname, $message4disp ) {

	xoops_cp_header();

	altsys_include_mymenu();

	$breadcrumbsObj = AltsysBreadcrumbs::getInstance();

	$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mylangadmin', _MI_ALTSYS_MENU_MYLANGADMIN );

	$breadcrumbsObj->appendPath( '', $target_mname );

    echo '<div class="ui-dev-mode">altsys/include/lang_functions.php</div>';
	echo '<h2>' . _MYLANGADMIN_H3_MODULE . ' » ' . $target_mname . '</h2>';
	echo '<p>' . $message4disp . '</p>';

	xoops_cp_footer();
	exit;
}
