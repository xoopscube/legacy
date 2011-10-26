<?php

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

// language file (blocks_common.php&blocks_each.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'blocks_common.php' , $mydirname , $mytrustdirname ) ;
$langman->read( 'blocks_each.php' , $mydirname , $mytrustdirname , false ) ;

require_once "$mytrustdirpath/blocks/block_functions.php" ;

?>