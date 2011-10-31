<?php

header("Content-type: image/png");

if( file_exists( $mydirpath.'/module_icon.png' ) ) {
	$use_custom_icon = true ;
	$icon_fullpath = $mydirpath.'/module_icon.png' ;
} else {
	$use_custom_icon = false ;
	$icon_fullpath = dirname(__FILE__).'/module_icon.png' ;
}

if( ! $use_custom_icon && function_exists( 'imagecreatefrompng' ) && function_exists( 'imagecolorallocate' ) && function_exists( 'imagestring' ) && function_exists( 'imagepng' ) ) {

	$im = imagecreatefrompng( $icon_fullpath ) ;

	$color = imagecolorallocate( $im , 0 , 0 , 0 ) ; // black
	$px = ( 92 - 6 * strlen( $mydirname ) ) / 2 ;
	imagestring( $im , 3 , $px , 34 , $mydirname , $color ) ;
	imagepng( $im ) ;
	imagedestroy( $im ) ;

} else {

	@readfile( $icon_fullpath ) ;

}

?>