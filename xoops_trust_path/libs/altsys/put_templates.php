<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * @package    Altsys
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

include_once __DIR__ . '/include/gtickets.php';
include_once __DIR__ . '/include/altsys_functions.php';
include_once __DIR__ . '/include/tpls_functions.php';


// this page can be called only from altsys
if ( 'altsys' != $xoopsModule->getVar( 'dirname' ) ) {
	die( 'this page can be called only from altsys' );
}


// language file
altsys_include_language_file( 'compilehookadmin' );


$db = XoopsDatabaseFactory::getDatabaseConnection();

if ( empty( $_FILES['tplset_archive']['tmp_name'] ) || ! is_uploaded_file( $_FILES['tplset_archive']['tmp_name'] ) ) {
	die( _TPLSADMIN_ERR_NOTUPLOADED );
}
//fix for mb_http_output setting and for add any browsers
if ( function_exists( 'mb_http_output' ) ) {
	mb_http_output( 'pass' );
}
//ob_buffer over flow
//HACK by suin & nao-pon 2012/01/06
while ( ob_get_level() > 0 ) {
	if ( ! ob_end_clean() ) {
		break;
	}
}


// EXTRACT VIEW

$orig_filename4check = mb_strtolower( $_FILES['tplset_archive']['name'] );
if ( '.zip' == mb_strtolower( mb_substr( $orig_filename4check, - 4 ) ) ) {
	// zip

    // TODO gigamaster redirect with message
    $msg = "Deprecated zip! "._TPLSADMIN_ERR_NOTUPLOADED;
    redirect_header( '?mode=admin&lib=altsys&page=compilehookadmin', 3, $msg );
    redirect_header( $_SERVER['REQUEST_URI'], 1, $msg );
    exit;

	// zip
	//require_once __DIR__ . '/include/Archive_Zip.php';
	//$reader = new Archive_Zip( $_FILES['tplset_archive']['tmp_name'] );
	//$files  = $reader->extract( [ 'extract_as_string' => true ] );
	//if ( ! is_array( @$files ) ) {
	//	die( $reader->errorName() );
	//}
	//$do_upload = true;
} elseif ( '.tgz' == mb_substr( $orig_filename4check, - 4 ) || '.tar.gz' == mb_substr( $orig_filename4check, - 7 ) ) {
	// tar.gz

    // TODO gigamaster redirect with message
    //$msg = "Deprecated zip"._TPLSADMIN_ERR_NOTUPLOADED;
    $msg = "Deprecated .tgz and .tar.gz ! "._TPLSADMIN_ERR_NOTUPLOADED;
    redirect_header( '?mode=admin&lib=altsys&page=compilehookadmin', 3, $msg );
    exit;
//TODO gigamaster TAR
} elseif ( '.tar' == mb_substr( $orig_filename4check, - 4 ) ) {
	// tar.gz
	require_once XOOPS_ROOT_PATH . '/class/class.tar.php';
	$tar = new tar();

	$tar->openTAR( $_FILES['tplset_archive']['tmp_name'] );

	$files = [];
	foreach ( $tar->files as $id => $info ) {
		$files[] = [
			'filename' => $info['name'],
			'mtime'    => $info['time'],
			'content'  => $info['file'],
		];
	}
	if ( empty( $files ) ) {
		die( _TPLSADMIN_ERR_INVALIDARCHIVE );
	}
	$do_upload = true;
}

if ( empty( $do_upload ) ) {
	die( _TPLSADMIN_ERR_EXTENSION );
}


// IMPORT VIEW

$tplset = @$_POST['tplset'];
if ( ! preg_match( '/^[0-9A-Za-z_-]{1,16}$/', $tplset ) ) {
	die( _TPLSADMIN_ERR_INVALIDTPLSET );
}

$imported = 0;
foreach ( $files as $file ) {
	if ( ! empty( $file['folder'] ) ) {
		continue;
	}

	$pos = mb_strrpos( $file['filename'], '/' );

	$tpl_file = false === $pos ? $file['filename'] : mb_substr( $file['filename'], $pos + 1 );

	if ( tplsadmin_import_data( $tplset, $tpl_file, rtrim( $file['content'] ), $file['mtime'] ) ) {
		$imported ++;
	}
}

redirect_header( '?mode=admin&lib=altsys&page=compilehookadmin', 3, sprintf( _TPLSADMIN_FMT_MSG_PUTTEMPLATES, $imported ) );
exit;
