<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

if( ! preg_match( '/^[0-9a-zA-Z_-]+$/' , $mydirname ) ) exit ;

if( ! class_exists( 'PicoPreloadBase' ) ) {

class PicoPreloadBase extends XCube_ActionFilter
{
	var $mydirname = 'pico' ;

	function postFilter()
	{
		$this->mController->mRoot->mDelegateManager->add("Legacy_BackendAction.GetRSSItems", array( &$this , "getRSSItems" ) ) ;
	}

	function getRSSItems( &$items )
	{
/*		$mydirname = $this->mydirname ;
		$module_handler =& xoops_gethandler( 'module' ) ;
		$xoopsModule =& $module_handler->getByDirname( $this->mydirname ) ;
		$xoopsDB =& Database::getInstance() ;
		$_GET['page'] = 'rss' ;
		include dirname(__FILE__).'/main/index.php' ;

		$items[] = array(
			'pubdate' => time() ,
			'title' => $this->mydirname ,
			'link' => 'link' ,
			'description' => 'desc' ,
			'guid' => 'guid' ,
		) ;*/
	}

}

}

if( ! is_numeric( $mydirname{0} ) ) {
	// If you want to name the directory from 0-9, make a site preload.
	eval( 'class '.ucfirst( $mydirname ).'_PicoPreload extends PicoPreloadBase { var $mydirname = "'.$mydirname.'" ; }' ) ;
}


?>