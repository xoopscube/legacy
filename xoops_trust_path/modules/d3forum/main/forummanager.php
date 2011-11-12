<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;

$forum_id = intval( @$_GET['forum_id'] ) ;

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_forum.inc.php' ) die( _MD_D3FORUM_ERR_READFORUM ) ;

// get&check this category ($category4assign, $category_row), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_category.inc.php' ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;

// special permission check for forummanager
if( ! $isadminormod ) die( _MD_D3FORUM_ERR_MODERATEFORUM ) ;


// get all of d3forum module instances
$module_handler =& xoops_gethandler( 'module' ) ;
$modules =& $module_handler->getObjects() ;
$exportable_modules = array( 0 => '----' ) ;
foreach( $modules as $module ) {
	$mid = $module->getVar('mid') ;
	$dirname = $module->getVar('dirname') ;
	$dirpath = XOOPS_ROOT_PATH.'/modules/'.$dirname ;
	$mytrustdirname = '' ;
	if( file_exists( $dirpath.'/mytrustdirname.php' ) ) {
		include $dirpath.'/mytrustdirname.php' ;
	}
	if( $mytrustdirname == 'd3forum' && $dirname != $mydirname ) {
		// d3forum
		$exportable_modules[$mid] = 'd3forum:'.$module->getVar('name')."($dirname)" ;
		$dist_category_permissions = d3forum_get_category_permissions_of_current_user( $dirname ) ;
		$exportable_module_categories[$mid] = d3forum_make_cat_jumpbox_options( $dirname , '1' , 'c.`cat_id` IN (' . implode( "," , array_keys( $dist_category_permissions ) ) . ')' , 0 ) ;
	}
}


// TRANSACTION PART
require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
if( isset( $_POST['forumman_post'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'd3forum' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	// options, weight and external_link_format can be modified only by admin
	if( ! $isadmin ) {
		$_POST['options'] = '' ;
		$_POST['weight'] = 0 ;
		$_POST['external_link_format'] = '' ;
	}

	d3forum_updateforum( $mydirname , $forum_id , $isadmin ) ;
	redirect_header( XOOPS_URL."/modules/$mydirname/index.php?forum_id=$forum_id" , 2 , _MD_D3FORUM_MSG_FORUMUPDATED ) ;
	exit ;
}
if( isset( $_POST['forumman_delete'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'd3forum' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	d3forum_delete_forum( $mydirname , $forum_id ) ;
	redirect_header( XOOPS_URL."/modules/$mydirname/index.php?cat_id=$cat_id" , 2 , _MD_D3FORUM_MSG_FORUMDELETED ) ;
	exit ;
}
if( ! empty( $_POST['forumman_export_copy'] ) || ! empty( $_POST['forumman_export_move'] ) ) {
	require_once dirname(dirname(__FILE__)).'/include/import_functions.php' ;
	if( ! $xoopsGTicket->check( true , 'd3forum' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	$export_mid = intval( @$_POST['export_mid'] ) ;
	$export_cat_id = intval( @$_POST['export_cat_id'][$export_mid] ) ;
	if( ! empty( $exportable_modules[ $export_mid ] ) && $export_cat_id > 0 ) {
		d3forum_export_forum_to_d3forum( $mydirname , $export_mid , $export_cat_id , $cat_id , $forum_id , ! empty( $_POST['forumman_export_move'] ) ) ;
		redirect_header( XOOPS_URL."/modules/$mydirname/index.php?cat_id=$cat_id" , 2 , _MD_D3FORUM_MSG_FORUMUPDATED ) ;
		exit ;
	}
}


// FORM PART

include dirname(dirname(__FILE__)).'/include/constant_can_override.inc.php' ;
$options4html = '' ;
$forum_configs = @unserialize( $forum_row['forum_options'] ) ;
if( is_array( $forum_configs ) ) foreach( $forum_configs as $key => $val ) {
	if( isset( $d3forum_configs_can_be_override[ $key ] ) ) {
		$options4html .= htmlspecialchars( $key , ENT_QUOTES ) . ':' . htmlspecialchars( $val , ENT_QUOTES ) . "\n" ;
	}
}

$forum4assign = array(
	'id' => $forum_id ,
	'title' => htmlspecialchars( $forum_row['forum_title'] , ENT_QUOTES ) ,
	'weight' => intval( $forum_row['forum_weight'] ) ,
	'external_link_format' => htmlspecialchars( $forum_row['forum_external_link_format'] , ENT_QUOTES ) ,
	'desc' => htmlspecialchars( $forum_row['forum_desc'] , ENT_QUOTES ) ,
	'options' => $options4html ,
	'option_desc' => d3forum_main_get_categoryoptions4edit( $d3forum_configs_can_be_override ) ,
) ;


// dare to set 'template_main' after header.php (for disabling cache)
include XOOPS_ROOT_PATH."/header.php";
$xoopsOption['template_main'] = $mydirname.'_main_forum_form.html' ;

$xoopsTpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'category' => $category4assign ,
	'forum' => $forum4assign ,
	'page' => 'forummanager' ,
	'formtitle' => _MD_D3FORUM_LINK_FORUMMANAGER ,
	'cat_jumpbox_options' => d3forum_make_cat_jumpbox_options( $mydirname , $whr_read4cat , $cat_id ) ,
	'export_to_module_options' => $exportable_modules ,
	'export_to_cat_options' => $exportable_module_categories ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3forum') ,
	'xoops_module_header' => "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".str_replace('{mod_url}',XOOPS_URL.'/modules/'.$mydirname,$xoopsModuleConfig['css_uri'])."\" />" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	'xoops_pagetitle' => _MD_D3FORUM_FORUMMANAGER ,
	'xoops_breadcrumbs' => array_merge( $xoops_breadcrumbs , array( array( 'name' => _MD_D3FORUM_FORUMMANAGER ) ) ) ,
) ) ;

include XOOPS_ROOT_PATH.'/footer.php';

?>