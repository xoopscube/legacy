<?php

$icon_cache_limit = 3600 ; // default 3600sec == 1hour

session_cache_limiter('public');
header("Expires: ".date('r',intval(time()/$icon_cache_limit)*$icon_cache_limit+$icon_cache_limit));
header("Cache-Control: public, max-age=$icon_cache_limit");
header("Last-Modified: ".date('r',intval(time()/$icon_cache_limit)*$icon_cache_limit));
header("Content-type: image/png");

// file name
if( ! empty( $_GET['file'] ) ) {
	$file_base = preg_replace( '/[^0-9a-z_]/' , '' , $_GET['file'] ) ;
} else {
	$file_base = 'module_icon' ;
}

// branches by cores
if( defined( 'ICMS_TRUST_PATH' ) ) {
	$draw_dirname = false ;
	$file_base .= '_icms' ;
} else if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
	$draw_dirname = false ;
	$file_base .= '_xcl' ;
} else {
	$draw_dirname = true ;
}

// icon files must be PNG
$file = $file_base . '.png' ;

// custom icon
if( file_exists( $mydirpath.'/'.$file ) ) {
	$draw_dirname = false ;
	$icon_fullpath = $mydirpath.'/module_icon.png' ;
} else {
	$icon_fullpath = dirname(__FILE__).'/images/'.$file ;
}

if( $draw_dirname && function_exists( 'imagecreatefrompng' ) && function_exists( 'imagecolorallocate' ) && function_exists( 'imagestring' ) && function_exists( 'imagepng' ) ) {

	$im = imagecreatefrompng( $icon_fullpath ) ;

	$color = imagecolorallocate( $im , 0 , 0 , 0 ) ; // black
	$px = ( 92 - 6 * strlen( $mydirname ) ) / 2 ;
	imagestring( $im , 3 , $px , 34 , $mydirname , $color ) ;
	imagepng( $im ) ;
	imagedestroy( $im ) ;

} else {

	readfile( $icon_fullpath ) ;

}

?>