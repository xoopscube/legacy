<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once XOOPS_TRUST_PATH . '/libs/altsys/include/altsys_functions.php';
// TODO altsys preferences bool
//var_dump($altsysModuleConfig);
//array(4) { ["admin_in_theme"]=> string(11) "xcl_default" ["theme_fromfile"]=> int(1) ["enable_force_clone"]=> int(1) ["images_dir"]=> string(6) "images" }
if ( /*$xoopsConfig['theme_fromfile'] &&*/ $altsysModuleConfig['theme_fromfile'] == 0) {
    $altsysModuleConfig = var_export($altsysModuleConfig, true);
    var_dump($altsysModuleConfig);
    return;
}
//if ( ! $xoopsConfig['theme_fromfile'] ) {
//	return;
//}

// templates/ under modules
// $tplsadmin_autoupdate_path = XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/templates' ;

if ( ! is_array( @$tplsadmin_autoupdate_dirnames ) ) {
    return;
}

foreach ( $tplsadmin_autoupdate_dirnames as $dirname ) {
    $dirname = preg_replace( '/[^a-zA-Z0-9_-]/', '', $dirname );

//    $tplsadmin_autoupdate_path = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates';
    $tplsadmin_autoupdate_path = XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/templates/' . $dirname ;

    // modules
    if ( $handler = @opendir( $tplsadmin_autoupdate_path . '/' ) ) {
        while ( false !== ( $file = readdir( $handler ) ) ) {
            $file_path = $tplsadmin_autoupdate_path . '/' . $file;
            /**
             * Import CSS
             * proposed by tohokuaiki
             * ! if( is_file( $file_path ) && (substr( $file , -5 ) == '.html'||substr( $file , -4 ) == '.css') ) {
             */
            if ( is_file( $file_path ) && '.html' == substr( $file, - 5 ) ) {
                $mtime = (int) @filemtime( $file_path );
                [$count] = $xoopsDB->fetchRow( $xoopsDB->query( 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $xoopsConfig['template_set'] ) . "' AND tpl_file='" . addslashes( $file ) . "' AND tpl_lastmodified >= $mtime" ) );
                if ( $count <= 0 ) {
                    include_once XOOPS_TRUST_PATH . '/libs/altsys/include/tpls_functions.php';
                    tplsadmin_import_data( $xoopsConfig['template_set'], $file, implode( '', file( $file_path ) ), $mtime );
                }
            }
        }
    }

    // blocks
    if ( $handler = @opendir( $tplsadmin_autoupdate_path . '/blocks/' ) ) {
        while ( false !== ( $file = readdir( $handler ) ) ) {
            //$file_path = $tplsadmin_autoupdate_path . '/blocks/' . $file;
            $file_path = $tplsadmin_autoupdate_path . '/' . $file;
            if ( is_file( $file_path ) && '.html' == substr( $file, - 5 ) ) {
                $mtime = (int) @filemtime( $file_path );
                [$count] = $xoopsDB->fetchRow( $xoopsDB->query( 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $xoopsConfig['template_set'] ) . "' AND tpl_file='" . addslashes( $file ) . "' AND tpl_lastmodified >= $mtime" ) );
                if ( $count <= 0 ) {
                    include_once XOOPS_TRUST_PATH . '/libs/altsys/include/tpls_functions.php';
                    tplsadmin_import_data( $xoopsConfig['template_set'], $file, implode( '', file( $file_path ) ), $mtime );
                }
            }
        }
    }
}

// templates/ under the theme
//$tplsadmin_autoupdate_path = XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/templates/'. $dirname . ;
//
//if ( $handler = @opendir( $tplsadmin_autoupdate_path . '/' ) ) {
//	while ( false !== ( $file = readdir( $handler ) ) ) {
//		$file_path = $tplsadmin_autoupdate_path . '/' . $file;
//
//		if ( is_file( $file_path ) && '.tpl' == substr( $file, - 5 ) ) {
//			$mtime = (int) ( @filemtime( $file_path ) );
//
//			list( $count ) = $xoopsDB->fetchRow( $xoopsDB->query( 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $xoopsConfig['template_set'] ) . "' AND tpl_file='" . addslashes( $file ) . "' AND tpl_lastmodified >= $mtime" ) );
//
//			if ( $count <= 0 ) {
//				include_once XOOPS_TRUST_PATH . '/libs/altsys/include/tpls_functions.php';
//
//				tplsadmin_import_data( $xoopsConfig['template_set'], $file, implode( '', file( $file_path ) ), $mtime );
//			}
//		}
//	}
//}
