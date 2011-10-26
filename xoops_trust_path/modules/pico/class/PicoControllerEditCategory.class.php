<?php

require_once dirname(__FILE__).'/PicoControllerAbstract.class.php' ;
require_once dirname(__FILE__).'/PicoModelCategory.class.php' ;
require_once dirname(__FILE__).'/gtickets.php' ;

class PicoControllerEditCategory extends PicoControllerAbstract {

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

	// makecategory/categorymanager
	$page = empty( $request['makecategory'] ) ? 'categorymanager' : 'makecategory' ;

	// $categoryObj (not parent)
	$picoPermission =& PicoPermission::getInstance() ;
	$categoryObj = new PicoCategory( $this->mydirname , $request['cat_id'] , $picoPermission->getPermissions( $this->mydirname ) , $page == 'makecategory' , $this->currentCategoryObj ) ;

	// check existence
	if( $categoryObj->isError() ) {
		redirect_header( XOOPS_URL."/modules/$this->mydirname/index.php" , 2 , _MD_PICO_ERR_READCONTENT ) ;
		exit ;
	}

	// fetch data from DB
	$cat_data = $categoryObj->getData() ;
	$this->assign['category_base'] = $categoryObj->getData4html( true ) ;
	$this->categoryObjs['category_base'] =& $categoryObj ;
	$this->assign['category'] = $categoryObj->getData4edit() ;

	// permission check
	if( $page == 'makecategory' ) {
		$pcat_data = $this->currentCategoryObj->getData() ;
		if( empty( $pcat_data['can_makesubcategory'] ) ) {
			redirect_header( XOOPS_URL.'/' , 2 , _MD_PICO_ERR_CREATECATEGORY ) ;
		}
	} else {
		if( empty( $cat_data['isadminormod'] ) ) {
			redirect_header( XOOPS_URL.'/' , 2 , _MD_PICO_ERR_CATEGORYMANAGEMENT ) ;
		}
	}

	// category list can be read for category jumpbox etc.
	$categoryHandler = new PicoCategoryHandler( $this->mydirname , $this->permissions ) ;
	$categories = $categoryHandler->getAllCategories() ;
	$this->assign['categories_can_post'] = array() ;
	foreach( $categories as $tmpObj ) {
		$tmp_data = $tmpObj->getData() ;
		if( empty( $tmp_data['can_makesubcategory'] ) ) continue ;
		$this->assign['categories_can_makesubcategory'][ $tmp_data['id'] ] = str_repeat('--',$tmp_data['cat_depth_in_tree']).$tmp_data['cat_title'] ;
	}

	// breadcrumbs
	$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
	if( $page == 'makecategory' ) {
		$breadcrumbsObj->appendPath( '' , _MD_PICO_LINK_MAKECATEGORY ) ;
		$this->assign['xoops_pagetitle'] = _MD_PICO_LINK_MAKECATEGORY ;
	} else {
//		$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/'.$this->mydirname.'/'.$this->assign['category']['link'] , $this->assign['category']['title'] ) ;
		$breadcrumbsObj->appendPath( '' , _MD_PICO_CATEGORYMANAGER ) ;
		$this->assign['xoops_pagetitle'] = _MD_PICO_CATEGORYMANAGER ;
	}
	$this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs() ;

	// misc assigns
	$this->assign['page'] = $page ;
	$this->assign['formtitle'] = $page == 'makecategory' ? _MD_PICO_LINK_MAKECATEGORY : _MD_PICO_CATEGORYMANAGER ;
	$this->assign['gticket_hidden'] = $GLOBALS['xoopsGTicket']->getTicketHtml( __LINE__ , 1800 , 'pico') ;
	$this->assign['category']['option_desc'] = $this->getCategoryOptions4edit() ;
	$this->assign['category']['wraps_directories'] = array( '' => '---' ) + pico_main_get_wraps_directories_recursively( $this->mydirname , '/' ) ;

	// views
	$this->template_name = $this->mydirname.'_main_category_form.html' ;
	$this->is_need_header_footer = true ;
}


// get samples of category options
function getCategoryOptions4edit()
{
	include dirname(dirname(__FILE__)).'/include/configs_can_override.inc.php' ;

	$lines = array() ;
	foreach( $pico_configs_can_be_override as $key => $type ) {
		if( isset( $this->mod_config[ $key ] ) ) {
			$val = $this->mod_config[ $key ] ;
			if( $type == 'int' || $type == 'bool' ) {
				$val = intval( $val ) ;
			}
			$lines[] = htmlspecialchars( $key . ':' . $val , ENT_QUOTES ) ;
		}
	}
	return implode( '<br />' , $lines ) ;
}


}

?>