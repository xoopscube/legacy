<?php

require_once dirname(__FILE__).'/PicoControllerAbstract.class.php' ;
require_once dirname(__FILE__).'/PicoModelCategory.class.php' ;
require_once dirname(__FILE__).'/PicoModelContent.class.php' ;

class PicoControllerGetHistory extends PicoControllerAbstract {

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

var $contentObj ;
var $view ;

function execute( $request )
{
	parent::execute( $request ) ;

	$cat_data = $this->currentCategoryObj->getData() ;
	$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;

	$this->contentObj = new PicoContent( $this->mydirname , $request['content_id'] , $this->currentCategoryObj ) ;

	// add breadcrumbs if the content exists
	if( ! $this->contentObj->isError() ) {
		$content_data = $this->contentObj->getData() ;
		$this->assign['content'] = $this->contentObj->getData4html() ;
		$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/'.$this->mydirname.'/'.$this->assign['content']['link'] , $this->assign['content']['subject'] ) ;
		$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/'.$this->mydirname.'/index.php?page=contentmanager&amp;content_id='.$content_data['id'] , _MD_PICO_CONTENTMANAGER ) ;
	}

	// permission check by 'can_edit'
	if( empty( $cat_data['can_edit'] ) ) {
		redirect_header( XOOPS_URL.'/' , 2 , _MD_PICO_ERR_EDITCONTENT ) ;
		exit ;
	}

	// get $history_profile from the id
	$this->assign['content_history_id'] = $request['content_history_id'] ;
	list( , , $history_body ) = pico_get_content_history_profile( $this->mydirname , $request['content_history_id'] ) ;
	$this->assign['history_body_raw'] = $history_body ;

	// breadcrumbs
	$breadcrumbsObj->appendPath( '' , _MD_PICO_HISTORY ) ;
	$this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs() ;
	$this->assign['xoops_pagetitle'] = _MD_PICO_HISTORY ;

	// view
	$this->view = $request['view'] ;
	switch( $this->view ) {
		case 'viewhistory' :
			$this->template_name = $this->mydirname.'_main_viewhistory.html' ;
			$this->is_need_header_footer = true ;
			break ;
		case 'download' :
		default :
			$this->is_need_header_footer = false ;
			break ;
	}
}

function render()
{
	// remove all ob filters
	while( ob_get_level() ) {
		ob_end_clean() ;
	}

	switch( $this->view ) {
		case 'download' :
			header( 'Content-Type: text/plain' ) ;
			header( 'Content-Disposition: attachment; filename="content_history_'.sprintf('%010d',$this->assign['content_history_id']).'.txt"' ) ;
			echo $this->assign['history_body_raw'] ;
			break ;
		default :
			header( 'Content-Type: text/html;' ) ;
			header( 'Content-Disposition: inline; filename="content_history_'.sprintf('%010d',$this->assign['content_history_id']).'.txt"' ) ;
			echo nl2br( htmlspecialchars( $this->assign['history_body_raw'] , ENT_QUOTES ) ) ;
			break ;
	}
}


}

?>