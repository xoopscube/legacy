<?php
//traduzione italiana di evoc cadelsanto@gmail.com www.cadelsanto.org
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'pico' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_CATEGORY","Categoria");
define($constpref."_TOPCATEGORY","Categoria Principale");

}

?>