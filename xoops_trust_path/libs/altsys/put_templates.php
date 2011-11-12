<?php
// ------------------------------------------------------------------------- //
//                        put_tplsvarsinfo.php (altsys)                      //
//                      - XOOPS templates admin module -                     //
//                        GIJOE <http://www.peak.ne.jp/>                     //
// ------------------------------------------------------------------------- //

include_once dirname(__FILE__).'/include/gtickets.php' ;
include_once dirname(__FILE__).'/include/altsys_functions.php' ;
include_once dirname(__FILE__).'/include/tpls_functions.php' ;


// this page can be called only from altsys
if( $xoopsModule->getVar('dirname') != 'altsys' ) die( 'this page can be called only from altsys' ) ;


// language file
altsys_include_language_file( 'compilehookadmin' ) ;


$db =& Database::getInstance() ;

if( empty( $_FILES['tplset_archive']['tmp_name'] ) || ! is_uploaded_file( $_FILES['tplset_archive']['tmp_name'] ) ) die( _TPLSADMIN_ERR_NOTUPLOADED ) ;

//
// EXTRACT STAGE
//

$orig_filename4check = strtolower( $_FILES['tplset_archive']['name'] ) ;
if( strtolower( substr( $orig_filename4check , -4 ) ) == '.zip' ) {

	// zip
	require_once dirname(__FILE__).'/include/Archive_Zip.php' ;
	$reader = new Archive_Zip( $_FILES['tplset_archive']['tmp_name'] ) ;
	$files = $reader->extract( array( 'extract_as_string' => true ) ) ;
	if( ! is_array( @$files ) ) die( $reader->errorName() ) ;
	$do_upload = true ;

} else if( substr( $orig_filename4check , -4 ) == '.tgz' || substr( $orig_filename4check , -7 ) == '.tar.gz' ) {

	// tar.gz
	require_once XOOPS_ROOT_PATH.'/class/class.tar.php' ;
	$tar = new tar() ;
	$tar->openTar( $_FILES['tplset_archive']['tmp_name'] ) ;
	$files = array() ;
	foreach( $tar->files as $id => $info ) {
		$files[] = array(
			'filename' => $info['name'] ,
			'mtime' => $info['time'] ,
			'content' => $info['file'] ,
		) ;
	}
	if( empty( $files ) ) die( _TPLSADMIN_ERR_INVALIDARCHIVE ) ;
	$do_upload = true ;
}

if( empty( $do_upload ) ) die( _TPLSADMIN_ERR_EXTENSION ) ;

//
// IMPORT STAGE
//

$tplset = @$_POST['tplset'] ;
if( ! preg_match( '/^[0-9A-Za-z_-]{1,16}$/' , $tplset ) ) {
	die( _TPLSADMIN_ERR_INVALIDTPLSET ) ;
}

$imported = 0 ;
foreach( $files as $file ) {

	if( ! empty( $file['folder'] ) ) continue ;
	$pos = strrpos( $file['filename'] , '/' ) ;
	$tpl_file = $pos === false ? $file['filename'] : substr( $file['filename'] , $pos + 1 ) ;

	if( tplsadmin_import_data( $tplset , $tpl_file , rtrim( $file['content'] ) , $file['mtime'] ) ) $imported ++ ;

}

redirect_header( '?mode=admin&lib=altsys&page=compilehookadmin' , 3 , sprintf( _TPLSADMIN_FMT_MSG_PUTTEMPLATES , $imported )  ) ;
exit ;

?>
