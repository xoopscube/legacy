<?php

require_once XOOPS_TRUST_PATH.'/modules/pico/class/FormProcessByHtml.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoFormProcessBySmartyBase.class.php' ;


function smarty_function_formmail( $params , &$smarty )
{
	$controller = new PicoFormProcessBySmartyFormmail() ;
	$controller->parseParameters( $params ) ;

	// toEmails from 'adminmail'
	if( empty( $controller->toEmails ) ) {
		if( trim( $GLOBALS['xoopsConfig']['adminmail'] ) != '' ) {
			$controller->toEmails[] = $GLOBALS['xoopsConfig']['adminmail'] ;
		}
	}

	if( $controller->countValidToEmails() <= 0 ) die( 'Set a valid email address by adding to="(email)" inside &lt;{'.$controller->mypluginname.'}&gt;' ) ;
	$controller->execute( $params , $smarty ) ;
}


class PicoFormProcessBySmartyFormmail extends PicoFormProcessBySmartyBase
{
	function __construct()
	{
		$this->mypluginname = 'formmail' ;
	}

	function executeLast()
	{
		$this->sendMail() ;
		//$this->storeDB() ;
	}

}


?>