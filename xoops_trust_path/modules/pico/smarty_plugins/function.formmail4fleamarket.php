<?php

require_once XOOPS_TRUST_PATH.'/modules/pico/class/FormProcessByHtml.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoFormProcessBySmartyBase.class.php' ;


function smarty_function_formmail4fleamarket( $params , &$smarty )
{
	$controller = new PicoFormProcessBySmartyFormmail4fleamarket() ;
	$controller->parseParameters( $params ) ;

	// add a toEmail from xoopsUser
	$content = $smarty->get_template_vars( 'content' ) ;
	$poster_uid = intval( $content['poster_uid'] ) ;
	$user_handler =& xoops_gethandler( 'user' ) ;
	$poster = $user_handler->get( $poster_uid ) ;
	if( is_object( $poster ) ) {
		$controller->toEmails[] = $poster->getVar( 'email' , 'n' ) ;
	}

	if( $controller->countValidToEmails() <= 0 ) die( 'Set a valid email address by adding to="(email)" inside &lt;{'.$controller->mypluginname.'}&gt;' ) ;
	$controller->execute( $params , $smarty ) ;
}


class PicoFormProcessBySmartyFormmail4fleamarket extends PicoFormProcessBySmartyBase
{
	function __construct()
	{
		$this->mypluginname = 'formmail4fleamarket' ;
	}

	function executeLast()
	{
		$this->sendMail() ;
		$this->storeDB() ;
	}
}

?>