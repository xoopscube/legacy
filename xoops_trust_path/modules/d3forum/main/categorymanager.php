<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;

$cat_id = intval( @$_GET['cat_id'] ) ;

// get&check this category ($category4assign, $category_row), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_category.inc.php' ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;

// count children
include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;
$mytree = new XoopsTree( $db->prefix($mydirname."_categories") , "cat_id" , "pid" ) ;
$children = $mytree->getAllChildId( $cat_id ) ;

// special check for categorymanager
if( ! $isadmin ) die( _MD_D3FORUM_ERR_CREATECATEGORY ) ;

// TRANSACTION PART
require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
if( isset( $_POST['categoryman_post'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'd3forum' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	d3forum_updatecategory( $mydirname , $cat_id ) ;
	if( ! empty(  $_POST['batch_action_turnsolvedon'] ) ) d3forum_transact_turnsolvedon_in_category( $mydirname , $cat_id ) ;
	redirect_header( XOOPS_URL."/modules/$mydirname/index.php?cat_id=$cat_id" , 2 , _MD_D3FORUM_MSG_CATEGORYUPDATED ) ;
	exit ;
}
if( isset( $_POST['categoryman_delete'] ) && count( $children ) == 0 ) {
	if ( ! $xoopsGTicket->check( true , 'd3forum' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	d3forum_delete_category( $mydirname , $cat_id ) ;
	redirect_header( XOOPS_URL."/modules/$mydirname/index.php" , 2 , _MD_D3FORUM_MSG_CATEGORYDELETED ) ;
	exit ;
}

// FORM PART

include dirname(dirname(__FILE__)).'/include/constant_can_override.inc.php' ;
$options4html = '' ;
$category_configs = @unserialize( $cat_row['cat_options'] ) ;
if( is_array( $category_configs ) ) foreach( $category_configs as $key => $val ) {
	if( isset( $d3forum_configs_can_be_override[ $key ] ) ) {
		$options4html .= htmlspecialchars( $key , ENT_QUOTES ) . ':' . htmlspecialchars( $val , ENT_QUOTES ) . "\n" ;
	}
}

$category4assign = array(
	'id' => $cat_id ,
	'title' => htmlspecialchars( $cat_row['cat_title'] , ENT_QUOTES ) ,
	'weight' => intval( $cat_row['cat_weight'] ) ,
	'desc' => htmlspecialchars( $cat_row['cat_desc'] , ENT_QUOTES ) ,
	'options' => $options4html ,
	'option_desc' => d3forum_main_get_categoryoptions4edit( $d3forum_configs_can_be_override ) ,
) ;


// dare to set 'template_main' after header.php (for disabling cache)
include XOOPS_ROOT_PATH."/header.php";
$xoopsOption['template_main'] = $mydirname.'_main_category_form.html' ;

$xoopsTpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'category' => $category4assign ,
	'page' => 'categorymanager' ,
	'formtitle' => _MD_D3FORUM_LINK_CATEGORYMANAGER ,
	'children_count' => count( $children ) ,
	'cat_jumpbox_options' => d3forum_make_cat_jumpbox_options( $mydirname , $whr_read4cat , $cat_row['pid'] ) ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3forum') ,
	'xoops_module_header' => "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".str_replace('{mod_url}',XOOPS_URL.'/modules/'.$mydirname,$xoopsModuleConfig['css_uri'])."\" />" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	'xoops_pagetitle' => _MD_D3FORUM_CATEGORYMANAGER ,
	'xoops_breadcrumbs' => array_merge( $xoops_breadcrumbs , array( array( 'name' => _MD_D3FORUM_CATEGORYMANAGER ) ) ) ,
) ) ;

include XOOPS_ROOT_PATH.'/footer.php';

?>