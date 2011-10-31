<?php

$icon_cache_limit = 3600 ; // default 3600sec == 1hour

session_cache_limiter('public');
header("Expires: ".date('r',intval(time()/$icon_cache_limit)*$icon_cache_limit+$icon_cache_limit));
header("Cache-Control: public, max-age=$icon_cache_limit");
header("Last-Modified: ".date('r',intval(time()/$icon_cache_limit)*$icon_cache_limit));
header("Content-type: image/png");

if( file_exists( $mydirpath.'/module_icon.png' ) ) {
	$use_custom_icon = true ;
//HACK by domifara
	$icon_fullpath = realpath($mydirpath.'/module_icon.png') ;
	if (empty($icon_fullpath)){
		$use_custom_icon = false ;
		$icon_fullpath = realpath(dirname(__FILE__).'/module_icon.png') ;
	}
} else {
	$use_custom_icon = false ;
//HACK by domifara
	$icon_fullpath = realpath(dirname(__FILE__).'/module_icon.png') ;
}

if( ! $use_custom_icon && function_exists( 'imagecreatefrompng' ) && function_exists( 'imagecolorallocate' ) && function_exists( 'imagestring' ) && function_exists( 'imagepng' ) ) {

	$im = imagecreatefrompng( $icon_fullpath ) ;
//HACK by domifara
	$color = imagecolorallocate( $im , 0 , 0 , 0 ) ; // black
	$px = ( 144 - 6 * strlen( $mydirname ) ) / 2 ;
	imagestring( $im , 3 , $px , 5 , $mydirname , $color ) ;
	imagepng( $im ) ;
	imagedestroy( $im ) ;

} else {

	readfile( $icon_fullpath ) ;

}

?>