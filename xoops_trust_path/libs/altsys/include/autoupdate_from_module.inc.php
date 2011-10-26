<?php

if( ! $xoopsConfig['theme_fromfile'] ) return ;

// templates/ under modules
// $tplsadmin_autoupdate_path = XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/templates' ;

if( ! is_array( @$tplsadmin_autoupdate_dirnames ) ) return ;

foreach( $tplsadmin_autoupdate_dirnames as $dirname ) {

	$dirname = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $dirname ) ;
	$tplsadmin_autoupdate_path = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates' ;

	// modules
	if( $handler = @opendir( $tplsadmin_autoupdate_path . '/' ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			$file_path = $tplsadmin_autoupdate_path . '/' . $file ;
			if( is_file( $file_path ) && substr( $file , -5 ) == '.html' ) {
				$mtime = intval( @filemtime( $file_path ) ) ;
				list( $count ) = $xoopsDB->fetchRow( $xoopsDB->query( "SELECT COUNT(*) FROM ".$xoopsDB->prefix("tplfile")." WHERE tpl_tplset='".addslashes($xoopsConfig['template_set'])."' AND tpl_file='".addslashes($file)."' AND tpl_lastmodified >= $mtime" ) ) ;
				if( $count <= 0 ) {
					include_once XOOPS_TRUST_PATH.'/libs/altsys/include/tpls_functions.php' ;
					tplsadmin_import_data( $xoopsConfig['template_set'] , $file , implode( '' , file( $file_path ) ) , $mtime ) ;
				}
			}
		}
	}

	// blocks
	if( $handler = @opendir( $tplsadmin_autoupdate_path . '/blocks/' ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			$file_path = $tplsadmin_autoupdate_path . '/blocks/' . $file ;
			if( is_file( $file_path ) && substr( $file , -5 ) == '.html' ) {
				$mtime = intval( @filemtime( $file_path ) ) ;
				list( $count ) = $xoopsDB->fetchRow( $xoopsDB->query( "SELECT COUNT(*) FROM ".$xoopsDB->prefix("tplfile")." WHERE tpl_tplset='".addslashes($xoopsConfig['template_set'])."' AND tpl_file='".addslashes($file)."' AND tpl_lastmodified >= $mtime" ) ) ;
				if( $count <= 0 ) {
					include_once XOOPS_TRUST_PATH.'/libs/altsys/include/tpls_functions.php' ;
					tplsadmin_import_data( $xoopsConfig['template_set'] , $file , implode( '' , file( $file_path ) ) , $mtime ) ;
				}
			}
		}
	}
}

?>