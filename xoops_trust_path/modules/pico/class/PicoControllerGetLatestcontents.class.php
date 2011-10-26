<?php

require_once dirname(__FILE__).'/PicoControllerAbstract.class.php' ;
require_once dirname(__FILE__).'/PicoModelCategory.class.php' ;

class PicoControllerGetLatestcontents extends PicoControllerAbstract {

//var $mydirname = '' ;
//var $mytrustdirname = '' ;
//var $assign = array() ;
//var $mod_config = array() ;
//var $uid = 0 ;
//var $currentCategoryObj = null ;
//var $permissions = array() ;
//var $is_need_header_footer = true ;
//var $template_name = '' ;
//var $html_header = '' ;
//var $contentObjs = array() ;

function execute( $request )
{
	parent::execute( $request ) ;

	// check existence
	if( $this->currentCategoryObj->isError() ) {
		redirect_header( XOOPS_URL."/modules/$this->mydirname/index.php" , 2 , _MD_PICO_ERR_READCATEGORY ) ;
		exit ;
	}

	$cat_data = $this->currentCategoryObj->getData() ;
	$this->assign['category'] = $this->currentCategoryObj->getData4html() ;

	// permission check
	if( ! $cat_data['can_read'] ) {
		redirect_header( XOOPS_URL."/modules/$this->mydirname/index.php" , 2 , _MD_PICO_ERR_READCATEGORY ) ;
		exit ;
	}

	// contents (order by modified_time DESC)
	$this->assign['contents'] = array() ;
	$contentObjs = $this->currentCategoryObj->getLatestContents( 10 , true ) ;
	foreach( $contentObjs as $contentObj ) {
		$content_data = $contentObj->getData() ;
		if( $content_data['can_read'] ) {
			$this->assign['contents'][] = array(
				'body4rss' => htmlspecialchars( xoops_substr( strip_tags( $content_data['body_cached'] ) , 0 , 255 ) , ENT_QUOTES ) ,
				'created_time4rss' => date( 'r' , $content_data['created_time'] ) ,
				'modified_time4rss' => date( 'r' , $content_data['modified_time'] ) ,
			) + $contentObj->getData4html() ;
		}
	}

	// views
	if( $request['view'] == 'rss' ) {
		$this->template_name = 'db:'.$this->mydirname.'_independent_rss20.html' ;
		$this->is_need_header_footer = false ;
		if( function_exists( 'mb_http_output' ) ) mb_http_output( 'pass' ) ;
		pico_common_utf8_encode_recursive( $this->assign ) ;
		header( 'Content-Type:text/xml; charset=utf-8' ) ;
	} else {
		$this->template_name = $this->mydirname.'_main_latestcontents.html' ;
		$this->is_need_header_footer = true ;
	}
}


}

?>