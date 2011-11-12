<?php

require_once XOOPS_TRUST_PATH.'/modules/pico/class/FormProcessByHtml.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoFormProcessBySmartyBase.class.php' ;


function smarty_function_survey( $params , &$smarty )
{
	$controller = new PicoFormProcessBySmartySurvey() ;
	$controller->canPostAgain = false ; // default false for survey
	$controller->parseParameters( $params ) ;
	$controller->execute( $params , $smarty ) ;
}


class PicoFormProcessBySmartySurvey extends PicoFormProcessBySmartyBase
{
	function __construct()
	{
		$this->mypluginname = 'survey' ;
	}

	function executeLast()
	{
		$this->sendMail() ;
		$this->storeDB() ;
	}

}


?>