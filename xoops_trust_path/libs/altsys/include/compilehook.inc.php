<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * tplsadmin compiled cache hook
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

/**
 * Compile Hook to save assigned variables for the template
 * @param $file
 * @param $smarty
 *
 * @return bool
 */
function tplsadmin_save_tplsvars( $file, $smarty ) {
	$tplsvars_file = 'tplsvars_';

	$tplsvars_file .= substr( md5( substr( XOOPS_DB_PASS, 0, 4 ) ), 0, 4 ) . '_';

	if ( 0 === strncmp( $file, 'db:', 3 ) ) {
		$tplsvars_file .= substr( $file, 3 );
	} elseif ( 0 === strncmp( $file, 'file:', 5 ) ) {
		$tplsvars_file .= strtr( substr( $file, 5 ), '/', '%' );
	} else {
		$tplsvars_file .= strtr( $file, '/', '%' );
	}

	if ( $fw = @fopen( XOOPS_COMPILE_PATH . '/' . $tplsvars_file, 'xb' ) ) {
		fwrite( $fw, serialize( $smarty->_tpl_vars ) );

		fclose( $fw );

		return true;
	}

	return false;
}
