<?php 

require dirname(__FILE__).'/mimes.php' ;

// for XOOPS
require '../../../../../../mainfile.php' ;

require_once dirname(__FILE__).'/functions.php' ;
if( file_exists( dirname(__FILE__).'/config_and_auth.inc.php' ) ) {
	include dirname(__FILE__).'/config_and_auth.inc.php' ;
} else {
	include dirname(__FILE__).'/config_and_auth.inc.dist.php' ;
}

// Get the main request informaiton.
$file = preg_replace( '?[^0-9a-zA-Z_/-]?' , '' , @$_GET['file'] ) ;

$full_path = FCK_TRUSTUPLOAD_PATH_BASE.$file ;
$original_file_name = DecodeFileName( substr( strrchr( basename( $file ) , '_' ) , 1 ) ) ;
$ext = strtolower( substr( strrchr( $original_file_name , '.' ) , 1 ) ) ;

// language problem ... IE should be exterminated...
$original_file_name4header = $original_file_name ;
$ua = @$_SERVER['HTTP_USER_AGENT'] ;
if( substr( $GLOBALS['xoopsConfig']['language'] , 0 , 2 ) == 'ja' && strstr( $ua , 'MSIE' ) && ! strstr( $ua , 'Opera' ) ) {
	$original_file_name4header = mb_convert_encoding( $original_file_name , 'SJIS' , 'UTF-8' ) ;
}

// remove output bufferings
while( ob_get_level() ) {
	ob_end_clean() ;
}

// can headers be sent?
if( headers_sent() ) {
	restore_error_handler() ;
	die( "Can't send headers. check language files etc." ) ;
}

// check file existance
if( ! file_exists( $full_path ) ) {
	die( 'Invalid file: '.htmlspecialchars($full_path,ENT_QUOTES) ) ;
}

// headers for browser cache
$cache_limit = 600 ;
if( $cache_limit > 0 ) {
	session_cache_limiter('public');
	header("Expires: ".date('r',intval(time()/$cache_limit)*$cache_limit+$cache_limit));
	header("Cache-Control: public, max-age=$cache_limit");
	header("Last-Modified: ".date('r',intval(time()/$cache_limit)*$cache_limit));
	header('Pragma: public'); // for IE with SSL
}

// Content-Type header
if( ! empty( $mimes[ $ext ] ) ) {
	header( 'Content-Type: '.$mimes[ $ext ] ) ;
} else {
	header( 'Content-Type: application/octet-stream' ) ;
}
header( 'Content-Disposition: attachment; filename="'. $original_file_name4header . '"' ) ;


// Transfer
set_time_limit( 0 ) ;
$fp = fopen( $full_path , "rb" ) ;
while( ! feof( $fp ) ) {
	echo fread( $fp , 65536 ) ;
}
exit ;



?>