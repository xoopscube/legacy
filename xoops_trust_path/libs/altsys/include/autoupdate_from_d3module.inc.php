<?php
/**
 * Altsys library (UI-Components) for D3 modules
 *
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

require_once XOOPS_TRUST_PATH . '/libs/altsys/include/altsys_functions.php';

// Since XCL 2.3.x add altsys preferences bool
// To also check config settings : $xoopsConfig['theme_fromfile'] &&
if ( $altsysModuleConfig['theme_fromfile'] == 0 ) {
	return;
}

// templates/ under modules
// $tplsadmin_autoupdate_path = XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/templates' ;

$tplsadmin_autoupdate_mydirnames =[];
if ( ! is_array( $tplsadmin_autoupdate_mydirnames ) ) {
    $tplsadmin_autoupdate_mydirnames = array( 'd3forum', 'pico' ) ;
}

foreach ( $tplsadmin_autoupdate_mydirnames as $tplsadmin_mydirname ) {
	$tplsadmin_mydirname = preg_replace( '/[^a-zA-Z0-9_-]/', '', $tplsadmin_mydirname );

	require XOOPS_ROOT_PATH . '/modules/' . $tplsadmin_mydirname . '/mytrustdirname.php';
	$altsys_mid_path           = 'altsys' == $mytrustdirname ? '/libs/' : '/modules/';
	$tplsadmin_autoupdate_path = XOOPS_TRUST_PATH . $altsys_mid_path . $mytrustdirname . '/templates';

	// modules
//	if ( $handler = @opendir( $tplsadmin_autoupdate_path . '/' ) ) {
//		while ( ( $file = readdir( $handler ) ) !== false) {
//			$file_path = $tplsadmin_autoupdate_path . '/' . $file;
//			if ( is_file( $file_path ) ) {
//				$mtime    = (int) @filemtime( $file_path );
//				$tpl_file = $tplsadmin_mydirname . '_' . $file;
//				list( $count ) = $xoopsDB->fetchRow( $xoopsDB->query( 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $xoopsConfig['template_set'] ) . "' AND tpl_file='" . addslashes( $tpl_file ) . "' AND tpl_lastmodified >= $mtime" ) );
//				if ( $count >= 0 ) {
//					include_once XOOPS_TRUST_PATH . '/libs/altsys/include/tpls_functions.php';
//					tplsadmin_import_data( $xoopsConfig['template_set'], $tpl_file, implode( '', file( $file_path ) ), $mtime );
//				}
//			}
//		}
//	}
    if ( $handler = @opendir( $tplsadmin_autoupdate_path . '/' ) ) {
        while ( ( $file = readdir( $handler ) ) !== false) {
            $file_path = $tplsadmin_autoupdate_path . '/' . $file;
            /**
             * Import CSS
             * proposed by tohokuaiki
             * ! if( is_file( $file_path ) && (substr( $file , -5 ) == '.html'||substr( $file , -4 ) == '.css') ) {
             */
            if ( is_file( $file_path ) && '.html' == substr( $file, - 5 ) ) {
                $mtime = (int) @filemtime( $file_path );
                $tpl_file = $tplsadmin_mydirname . '_' . $file;
                list( $count ) = $xoopsDB->fetchRow( $xoopsDB->query( 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $xoopsConfig['template_set'] ) . "' AND tpl_file='" . addslashes( $tpl_file ) . "' AND tpl_lastmodified >= $mtime" ) );
                if ( $count <= 0 ) {
                    include_once XOOPS_TRUST_PATH . '/libs/altsys/include/tpls_functions.php';
                    tplsadmin_import_data( $xoopsConfig['template_set'], $file, implode( '', file( $file_path ) ), $mtime );
                }
            }
        }
    }

}
