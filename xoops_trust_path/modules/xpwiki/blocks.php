<?php

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

// language files
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if(is_file( $langmanpath)) {
	require_once( $langmanpath ) ;
	$langman =& D3LanguageManager::getInstance() ;
	$langman->read( 'blocks_common.php' , $mydirname , $mytrustdirname , false ) ;
} else {
	$language = empty( $GLOBALS['xoopsConfig']['language'] ) ? 'english' : $GLOBALS['xoopsConfig']['language'] ;
	if( is_file( "$mydirpath/language/$language/blocks_common.php" ) ) {
		// user customized language file (already read by class/xoopsblock.php etc)
		include_once "$mydirpath/language/$language/blocks_common.php" ;
	} else if( is_file( "$mytrustdirpath/language/$language/blocks_common.php" ) ) {
		// default language file
		include_once "$mytrustdirpath/language/$language/blocks_common.php" ;
	} else {
		// fallback english
		include_once "$mytrustdirpath/language/english/blocks_common.php" ;
	}
}

require_once "$mytrustdirpath/blocks/block_functions.php" ;
