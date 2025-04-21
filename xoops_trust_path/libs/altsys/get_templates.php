<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Get templates admin
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

error_reporting( 0 );

include_once __DIR__ . '/include/gtickets.php';
include_once __DIR__ . '/include/altsys_functions.php';


// this page can be called only from altsys
if ( 'altsys' != $xoopsModule->getVar( 'dirname' ) ) {
	die( 'this page can be called only from altsys' );
}


// language file
altsys_include_language_file( 'compilehookadmin' );

if ( ! empty( $_POST['download_zip'] ) ) {
	require_once XOOPS_ROOT_PATH . '/class/zipdownloader.php';
	$downloader  = new XoopsZipDownloader();
	$do_download = true;
} elseif ( ! empty( $_POST['download_tgz'] ) ) {
	require_once XOOPS_ROOT_PATH . '/class/tardownloader.php';
	$downloader  = new XoopsTarDownloader();
	$do_download = true;
}
if ( empty( $do_download ) ) {
	exit;
}

$tplset = @$_POST['tplset'];
if ( ! preg_match( '/^[0-9A-Za-z_-]{1,16}$/', $tplset ) ) {
	die( _TPLSADMIN_ERR_INVALIDTPLSET );
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
$trs = $xoopsDB->query( 'SELECT DISTINCT tpl_file,tpl_source,tpl_lastmodified FROM ' . $xoopsDB->prefix( 'tplfile' ) . ' NATURAL LEFT JOIN ' . $xoopsDB->prefix( 'tplsource' ) . " WHERE tpl_tplset='" . addslashes( $tplset ) . "' ORDER BY tpl_file" );
if ( $xoopsDB->getRowsNum( $trs ) <= 0 ) {
	die( _TPLSADMIN_ERR_INVALIDTPLSET );
}

while ( [$tpl_file, $tpl_source, $tpl_lastmodified] = $xoopsDB->fetchRow( $trs ) ) {
	$downloader->addFileData( $tpl_source, $tplset . '/' . $tpl_file, $tpl_lastmodified );
}
//bugfix by nao-pon ,echo is not necessary for downloader
$downloader->download( 'template_' . $tplset, true );
