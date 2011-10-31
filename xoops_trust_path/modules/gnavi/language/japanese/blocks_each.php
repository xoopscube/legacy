<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'pico' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

define( $constpref.'_ARCH_POSTMONTH' , "%1\$d╟п%2\$d╖юд╬┼ъ╣╞" ) ;

// definitions for displaying blocks 
//define($constpref."","");


}

?>