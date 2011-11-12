<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

if( ! preg_match( '/^[0-9a-zA-Z_-]+$/' , $mydirname ) ) exit ;

require_once dirname(__FILE__).'/class/bulletin.php' ;
require_once dirname(__FILE__).'/include/common_functions.php' ;

if( ! class_exists( 'BulletinPreloadBase' ) ) {

class BulletinPreloadBase extends XCube_ActionFilter
{
	function postFilter()
	{
		$this->mController->mRoot->mDelegateManager->add("Legacy_BackendAction.GetRSSItems", array( &$this , "getRSSItems" ) ) ;
	}


	function getRSSItems( &$items )
	{
		// check module_read permission
		$module_handler =& xoops_gethandler( 'module' ) ;
		$module =& $module_handler->getByDirname( $this->mydirname ) ;
		$gperm_handler =& xoops_gethandler( 'groupperm' ) ;
		$can_read = $gperm_handler->checkRight( 'module_read' , $module->getVar('mid') , XOOPS_GROUP_ANONYMOUS ) ;
		if( ! $can_read ) return ;

		// check config (feed_as_backend)
		$config_handler =& xoops_gethandler( 'config' ) ;
		$mod_config =& $config_handler->getConfigsByCat( 0 , $module->getVar( 'mid' ) ) ;
		if( empty( $mod_config['feed_as_backend'] ) ) {
			return ;
		}

		$myts =& MyTextSanitizer::getInstance();
		$articles = Bulletin::getAllPublished( $this->mydirname , 10 , 0 ) ;
		foreach( $articles as $article ) {
			$hometext = $article->getVar('hometext','n') ;
			if( function_exists( 'easiestml' ) ) {
				$hometext = easiestml( $hometext ) ;
			}
			$items[] = array(
				'pubdate' => $article->getVar('published') ,
				'title' => htmlspecialchars(bulletin_utf8_encode($article->getVar('title', 'n')), ENT_QUOTES), 
				'category' => htmlspecialchars(bulletin_utf8_encode($article->newstopic->topic_title), ENT_QUOTES), 
				'link' => XOOPS_URL.'/modules/'.$this->mydirname.'/index.php?page=article&amp;storyid='.$article->getVar('storyid') ,
				'guid' => XOOPS_URL.'/modules/'.$this->mydirname.'/index.php?page=article&amp;storyid='.$article->getVar('storyid') ,
				'description' => bulletin_utf8_encode(htmlspecialchars(strip_tags($myts->xoopsCodeDecode($hometext)), ENT_QUOTES)),
			) ;
		}
	}

}

}

eval( 'class '.ucfirst( $mydirname ).'_BulletinPreload extends BulletinPreloadBase { var $mydirname = "'.$mydirname.'" ; }' ) ;



?>