<?php

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

// language files (blocks)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'blocks.php' , $mydirname , $mytrustdirname ) ;
$langman->read( 'main.php' , $mydirname , $mytrustdirname ) ;

// include all block files
$block_path = $mytrustdirpath.'/blocks' ;

if( $handler = @opendir( $block_path . '/' ) ) {
	while( ( $file = readdir( $handler ) ) !== false ) {
		if( substr( $file , 0 , 1 ) == '.' ) continue ;
		$file_path = $block_path . '/' . $file ;
		if( is_file( $file_path ) && substr( $file , -4 ) == '.php' ) {
			include_once $file_path ;
		}
	}
}
?>