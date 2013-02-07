<?php 

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

// configurations
define( 'FCK_UPLOAD_NAME' , 'NewFile' ) ;
define( 'FCK_UPLOAD_PATH_BASE' , XOOPS_UPLOAD_PATH.'/fckeditor' ) ;
define( 'FCK_UPLOAD_URL_BASE' , XOOPS_UPLOAD_URL.'/fckeditor' ) ;
define( 'FCK_TRUSTUPLOAD_PATH_BASE' , XOOPS_TRUST_PATH.'/uploads/fckeditor' ) ;
define( 'FCK_TRUSTUPLOAD_URL_BASE' , XOOPS_URL.'/common/fckeditor/editor/filemanager/connectors/php/transfer.php?file=' ) ;
define( 'FCK_FILE_PREFIX' , '' ) ; // not in use now
define( 'FCK_DIGITS4USERDIR' , 1 ) ; // create folder 0/ 1/ 2/ ... 9/ under uploads/fckeditor/ and chmod 777 them
define( 'FCK_USER_SELFDELETE_LIMIT' , 3600 ) ; // set the time limit by sec. 0 means normal users cannot delete files uploaded by themselves
define( 'FCK_USER_PREFIX' , 'uid%06d_' ) ;
define( 'FCK_CHECK_USER_PREFIX4NORMAL' , true ) ;
define( 'FCK_CHECK_USER_PREFIX4ADMIN' , false ) ;
$fck_uploadable_groups = array( 2 , 4 ) ; // sample
define( 'FCK_FUNCTION_AFTER_IMGUPLOAD' , 'fck_resize_by_imagemagick' ) ;


$fck_resource_type_extensions = array(
	'File' => array() ,
	'Image' => array( 'jpeg' , 'jpg' , 'png' , 'gif' ) ,
	'Flash' => array( 'swf' , 'fla' ) ,
	'Media' => array( 'jpeg' , 'jpg' , 'png' , 'gif' , 'swf' , 'fla' , 'avi' , 'mpg' , 'mpeg' , 'mov' ) ,
) ;
$fck_allowed_extensions = array() ;

// check directory for uploading
if( ! is_dir( FCK_UPLOAD_PATH_BASE ) ) {
	SendError( '1', '', '', 'Create '.htmlspecialchars(FCK_UPLOAD_URL_BASE).' first' ) ;
}


if( ! is_object( $xoopsUser ) ) {
	// guests
	$fck_isadmin = false ;
	$fck_canupload = false ;
	$uid = 0 ;
} else {
	// users
	$uid = $xoopsUser->getVar( 'uid' ) ;
	// check isadmin
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		// for Cube 2.1 (check if legacy module admin)
		$module_handler =& xoops_gethandler( 'module' ) ;
		$module =& $module_handler->getByDirname( 'legacy' ) ;
		$fck_isadmin = $xoopsUser->isAdmin( $module->getVar('mid') ) ;
	} else {
		$fck_isadmin = $xoopsUser->isAdmin(1) ; // system module admin
	}

	// check canupload
	$fck_canupload = $fck_isadmin ;
	if( ! $fck_isadmin ) {
		// users other than admin
		$fck_canupload = count( array_intersect( $xoopsUser->getGroups() , $fck_uploadable_groups ) ) > 0 ? true : false ;
	}
}


if( empty( $fck_isadmin ) ) {
	if( $fck_canupload ) {
		// uploading permissions for normal users
		$fck_allowed_extensions = array(
			'jpg' => 'image/jp' ,
			'jpeg' => 'image/jp' ,
			'png' => 'image/png' , 
			'gif' => 'image/gif' ,
			'pdf' => '' ,
		) ;
	} else {
		// uploading permissions for guests
		$fck_allowed_extensions = array() ;
	}
	$fck_user_prefix = sprintf( FCK_USER_PREFIX , $uid ) ;
	$fck_check_user_prefix = FCK_CHECK_USER_PREFIX4NORMAL ;
	$uid_dir = FCK_DIGITS4USERDIR > 0 ? '/' . sprintf( '%0'.FCK_DIGITS4USERDIR.'d' , $uid % pow( 10 , FCK_DIGITS4USERDIR ) ) : '' ;
	define( 'FCK_UPLOAD_PATH' , FCK_UPLOAD_PATH_BASE.$uid_dir ) ;
	define( 'FCK_UPLOAD_URL' , FCK_UPLOAD_URL_BASE.$uid_dir ) ;
	define( 'FCK_TRUSTUPLOAD_PATH' , FCK_TRUSTUPLOAD_PATH_BASE.$uid_dir ) ;
	define( 'FCK_TRUSTUPLOAD_URL' , FCK_TRUSTUPLOAD_URL_BASE.$uid_dir ) ;
} else {
	// permissions for admin (Only admin of system module can upload)
	$fck_allowed_extensions = array(
		'jpg' => 'image/jp' , // both ok image/jpeg, image/jpg
		'jpeg' => 'image/jp' ,
		'png' => 'image/png' , 
		'gif' => 'image/gif' ,
		'doc' => '' ,
		'xls' => '' ,
		'txt' => '' ,
		'pdf' => '' ,
		'swf' => '' ,
		'fla' => '' ,
		'mpeg' => '' ,
		'mpg' => '' ,
		'avi' => '' ,
		'wmv' => '' ,
		'mov' => '' ,
	) ;
	$fck_user_prefix = sprintf( FCK_USER_PREFIX , $uid ) ;
	$fck_check_user_prefix = FCK_CHECK_USER_PREFIX4ADMIN ;
	define( 'FCK_UPLOAD_PATH' , FCK_UPLOAD_PATH_BASE ) ;
	define( 'FCK_UPLOAD_URL' , FCK_UPLOAD_URL_BASE ) ;
	define( 'FCK_TRUSTUPLOAD_PATH' , FCK_TRUSTUPLOAD_PATH_BASE ) ;
	define( 'FCK_TRUSTUPLOAD_URL' , FCK_TRUSTUPLOAD_URL_BASE ) ;
}


function fck_resize_by_imagemagick( $new_filefullpath )
{
	$max_width = 480 ;
	$max_height = 480 ;

	$image_stats = getimagesize( $new_filefullpath ) ;
	if( $image_stats[0] > $max_width || $image_stats[1] > $max_height ) {
		exec( "mogrify -geometry {$max_width}x{$max_height} $new_filefullpath" ) ;
	}
}

?>