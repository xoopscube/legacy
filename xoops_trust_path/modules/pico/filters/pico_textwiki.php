<?php

define('_MD_PICO_FILTERS_TEXTWIKIINITWEIGHT',15);

function pico_textwiki( $mydirname , $text , $content4assign )
{
	// add XOOPS_TRUST_PATH/PEAR/ into include_path
	if( ! defined( 'PATH_SEPARATOR' ) ) define( 'PATH_SEPARATOR' , DIRECTORY_SEPARATOR == '/' ? ':' : ';' ) ;
	if( ! strstr( ini_get('include_path') , XOOPS_TRUST_PATH . '/PEAR' ) ) {
		ini_set( 'include_path' , ini_get('include_path') . PATH_SEPARATOR . XOOPS_TRUST_PATH . '/PEAR' ) ;
	}

	include_once "Text/Wiki.php";
	// include_once "Text/sunday_Wiki.php";

	if( ! class_exists( 'Text_Wiki' ) ) die( 'PEAR/Text/Wiki is not installed correctly' ) ;

	$wiki = new Text_Wiki(); // create instance

	// Configuration
	$wiki->deleteRule( 'Wikilink' ); // remove a rule for auto-linking
	$wiki->setFormatConf( 'Xhtml' , 'translate' , false ) ; // HTML_ENTITIES -> HTML_SPECIALCHARS -> false

	// $wiki = new sunday_Text_Wiki(); // create instance
	//$text = str_replace ( "\r\n", "\n", $text );
	//$text = str_replace ( "~\n", "[br]", $text );
	//$text = $wiki->transform($text);
	//$content = str_replace ( "[br]", "<br/>", $text );
	// special thx to minahito! you are great!!
	return $wiki->transform($text);
}

?>