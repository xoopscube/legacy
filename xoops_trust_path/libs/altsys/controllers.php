<?php

//require_once dirname(__FILE__).'/class/D3LanguageManager.class.php' ;
//$langman =& D3LanguageManager::getInstance() ;
//$langman->read( 'modinfo.php' , 'altsys' , 'altsys' ) ;

if( file_exists( dirname(__FILE__).'/language/'.$GLOBALS['xoopsConfig']['language'].'/modinfo.php' ) ) {
	include_once dirname(__FILE__).'/language/'.$GLOBALS['xoopsConfig']['language'].'/modinfo.php' ;
} else if ( file_exists( dirname(__FILE__).'/language/english/modinfo.php' ) ) {
	include_once dirname(__FILE__).'/language/english/modinfo.php' ;
}

$controllers = array(
	'myblocksadmin',
	'compilehookadmin',
	'get_templates',
	'get_tplsvarsinfo',
	'mypreferences',
	'mytplsadmin',
	'mytplsform',
	'put_templates',
	'mylangadmin',
) ;

?>