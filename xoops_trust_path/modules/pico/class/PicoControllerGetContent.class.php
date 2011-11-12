<?php

require_once dirname(__FILE__).'/PicoControllerAbstract.class.php' ;
require_once dirname(__FILE__).'/PicoModelCategory.class.php' ;
require_once dirname(__FILE__).'/PicoModelContent.class.php' ;

class PicoControllerGetContent extends PicoControllerAbstract {

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

	// $contentObj
	$contentObj = new PicoContent( $this->mydirname , $request['content_id'] , $this->currentCategoryObj ) ;

	// check existence
	if( $contentObj->isError() ) {
		redirect_header( XOOPS_URL."/modules/$this->mydirname/index.php" , 2 , _MD_PICO_ERR_READCONTENT ) ;
		exit ;
	}

	$cat_data = $this->currentCategoryObj->getData() ;
	$this->assign['category'] = $this->currentCategoryObj->getData4html() ;
	$content_data = $contentObj->getData() ;
	$this->assign['content'] = $contentObj->getData4html( true ) ;
	$this->contentObjs['content'] =& $contentObj ;

	// permission check
	if( empty( $content_data['can_read'] ) || empty( $content_data['can_readfull'] ) ) {
		if( $this->uid > 0 ) {
			redirect_header( XOOPS_URL.'/' , 2 , _MD_PICO_ERR_PERMREADFULL ) ;
		} else {
			redirect_header( XOOPS_URL.'/user.php' , 2 , _MD_PICO_ERR_LOGINTOREADFULL ) ;
		}
		exit ;
	}

	// auto-register
	if( ! empty( $this->mod_config['wraps_auto_register'] ) && @$cat_data['cat_vpath']{0} == '/' && $content_data['poster_uid'] == 0 && $content_data['vpath'] != '' ) {
		$register_class = empty( $this->mod_config['auto_register_class'] ) ? 'PicoAutoRegisterWraps' : $this->mod_config['auto_register_class'] ;
		require_once dirname(__FILE__).'/'.$register_class.'.class.php' ;
		$register_obj = new $register_class( $this->mydirname , $this->mod_config ) ;
		$is_updated = $register_obj->updateContent( $content_data['content_id'] , $content_data['vpath'] ) ;
		if( $is_updated > 0 ) {
			// reload if the content is updated
			header( 'Location: '.pico_common_unhtmlspecialchars($this->assign['mod_url']).'/'.pico_common_unhtmlspecialchars($this->assign['content']['link']) ) ;
			exit ;
		}
	}

	// prev/next content
	$prevContentObj =& $contentObj->getPrevContent() ;
	$this->assign['prev_content'] = is_object( $prevContentObj ) ? $prevContentObj->getData4html() : array() ;
	$this->contentObjs['prev_content'] =& $prevContentObj ;
	$nextContentObj =& $contentObj->getNextContent() ;
	$this->assign['next_content'] = is_object( $nextContentObj ) ? $nextContentObj->getData4html() : array() ;
	$this->contentObjs['next_content'] =& $nextContentObj ;

	// link for "tell to friends"
	if( $this->mod_config['use_taf_module'] ) {
		$this->assign['content']['tellafriend_uri'] = XOOPS_URL.'/modules/tellafriend/index.php?target_uri='.rawurlencode( XOOPS_URL."/modules/$this->mydirname/".pico_common_make_content_link4html( $this->mod_config , $content_data ) ).'&amp;subject='.rawurlencode(sprintf(_MD_PICO_FMT_TELLAFRIENDSUBJECT,@$GLOBALS['xoopsConfig']['sitename'])) ;
	} else {
		$this->assign['content']['tellafriend_uri'] = 'mailto:?subject='.pico_main_escape4mailto(sprintf(_MD_PICO_FMT_TELLAFRIENDSUBJECT,@$GLOBALS['xoopsConfig']['sitename'])).'&amp;body='.pico_main_escape4mailto(sprintf(_MD_PICO_FMT_TELLAFRIENDBODY, $content_data['subject'])).'%0A'.XOOPS_URL."/modules/$this->mydirname/".rawurlencode(pico_common_make_content_link4html( $this->mod_config , $content_data )) ;
	}

	// category list can be read for category jumpbox etc.
	$categoryHandler = new PicoCategoryHandler( $this->mydirname , $this->permissions ) ;
	$categories = $categoryHandler->getAllCategories() ;
	$this->assign['categories_can_read'] = array() ;
	foreach( $categories as $tmpObj ) {
		$tmp_data = $tmpObj->getData() ;
		$this->assign['categories_can_read'][ $tmp_data['id'] ] = str_repeat('--',$tmp_data['cat_depth_in_tree']).$tmp_data['cat_title'] ;
	}

	// count up 'viewed'
	if( $content_data['modifier_ip'] != @$_SERVER['REMOTE_ADDR'] ) {
		$contentObj->incrementViewed() ;
	}

	// breadcrumbs
	$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
	$breadcrumbsObj->appendPath( '' , $this->assign['content']['subject'] ) ;
	$this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs() ;
	$this->assign['xoops_pagetitle'] = $this->assign['content']['subject'] ;

	// views
	switch( $request['view'] ) {
		case 'singlecontent' :
			$this->template_name = 'db:'.$this->mydirname.'_independent_singlecontent.html' ;
			$this->is_need_header_footer = false ;
			break ;
		case 'print' :
			$this->template_name = 'db:'.$this->mydirname.'_independent_print.html' ;
			$this->is_need_header_footer = false ;
			break ;
		default :
			$this->template_name = $this->mydirname.'_main_viewcontent.html' ;
			$this->is_need_header_footer = true ;
			break ;
	}

	// htmlheader
	if( ! empty( $this->mod_config['allow_each_htmlheader'] ) ) {
		$this->html_header .= $content_data['htmlheader'] ;
	}
}


}

?>