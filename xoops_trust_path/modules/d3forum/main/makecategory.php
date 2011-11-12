<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.php' ;

$cat_id = intval( @$_GET['cat_id'] ) ;
if( ! empty( $_POST['cat_id'] ) ) $cat_id = intval( $_POST['cat_id'] ) ;

if( $cat_id ) {
	// get&check this category ($category4assign, $category_row), override options
	if( ! include dirname(dirname(__FILE__)).'/include/process_this_category.inc.php' ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;
}

// special check for makecategory
if( ! $isadmin ) die( _MD_D3FORUM_ERR_CREATECATEGORY ) ;

// TRANSACTION PART
// permissions will be set same as the parent category. (also moderator)
require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
if( isset( $_POST['categoryman_post'] ) ) {
	// create a record for category and category_access
	$new_cat_id = d3forum_makecategory( $mydirname , $cat_id ) ;
	redirect_header( XOOPS_URL."/modules/$mydirname/index.php?cat_id=$cat_id" , 2 , _MD_D3FORUM_MSG_CATEGORYMADE ) ;
	exit ;
}

// FORM PART

include dirname(dirname(__FILE__)).'/include/constant_can_override.inc.php' ;
$options4html = '' ;
foreach( $xoopsModuleConfig as $key => $val ) {
	if( isset( $d3forum_configs_can_be_override[ $key ] ) ) {
		$options4html .= htmlspecialchars( $key , ENT_QUOTES ) . ':' . htmlspecialchars( $val , ENT_QUOTES ) . "\n" ;
	}
}

$category4assign = array(
	'id' => 0 ,
	'title' => '' ,
	'weight' => 0 ,
	'desc' => '' ,
	'options' => '' , //$options4html ,
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
	'page' => 'makecategory' ,
	'formtitle' => _MD_D3FORUM_LINK_MAKECATEGORY ,
	'cat_jumpbox_options' => d3forum_make_cat_jumpbox_options( $mydirname , $whr_read4cat , $cat_id ) ,
	'xoops_module_header' => "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".str_replace('{mod_url}',XOOPS_URL.'/modules/'.$mydirname,$xoopsModuleConfig['css_uri'])."\" />" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	'xoops_pagetitle' => _MD_D3FORUM_LINK_MAKECATEGORY ,
	'xoops_breadcrumbs' => array_merge( $xoops_breadcrumbs , array( array( 'name' => _MD_D3FORUM_LINK_MAKECATEGORY ) ) ) ,
) ) ;

include XOOPS_ROOT_PATH.'/footer.php';

?>