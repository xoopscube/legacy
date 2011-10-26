<?php

require_once dirname(dirname(__FILE__)).'/include/main_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/pico.textsanitizer.php' ;
require_once dirname(dirname(__FILE__)).'/class/PicoUriMapper.class.php' ;
require_once dirname(dirname(__FILE__)).'/class/PicoPermission.class.php' ;
require_once dirname(dirname(__FILE__)).'/class/PicoModelCategory.class.php' ;
require_once dirname(dirname(__FILE__)).'/class/PicoModelContent.class.php' ;
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/AltsysBreadcrumbs.class.php' ;

// breadcrumbs
$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/'.$mydirname.'/index.php' , $xoopsModule->getVar( 'name' ) ) ;

// permissions
$picoPermission =& PicoPermission::getInstance() ;
$permissions = $picoPermission->getPermissions( $mydirname ) ;

// current category object (this "current" means "targeted")
$currentCategoryObj = new PicoCategory( $mydirname , intval( @$_REQUEST['cat_id'] ) , $permissions ) ;
if( $currentCategoryObj->isError() ) {
	redirect_header( XOOPS_URL."/modules/$mydirname/index.php" , 2 , _MD_PICO_ERR_READCATEGORY ) ;
	exit ;
}

// override $xoopsModuleConfig
$xoopsModuleConfig = $currentCategoryObj->getOverriddenModConfig() ;

// append paths from each categories into breadcrumbs
$breadcrumbsObj->appendPath( $currentCategoryObj->getBreadcrumbs() ) ;

// request
$picoRequest = array() ;
$picoRequest['cat_id'] = intval( @$_REQUEST['cat_id'] ) ;

if( ! empty( $_POST['categoryman_post'] ) ) {
	$controller_class = 'PicoControllerUpdateCategory' ;
} else if( ! empty( $_POST['categoryman_delete'] ) ) {
	$controller_class = 'PicoControllerDeleteCategory' ;
} else {
	$controller_class = 'PicoControllerEditCategory' ;
}

require_once dirname(dirname(__FILE__)).'/class/'.$controller_class.'.class.php' ;
$controller = new $controller_class( $currentCategoryObj ) ;
$controller->execute( $picoRequest ) ;

// render
if( $controller->isNeedHeaderFooter() ) {
	$xoopsOption['template_main'] = $controller->getTemplateName() ;
	include XOOPS_ROOT_PATH.'/header.php';
	$xoopsTpl->assign( $controller->getAssign() ) ;
	$xoopsTpl->assign( 'xoops_module_header' , pico_main_render_moduleheader( $mydirname , $xoopsModuleConfig , $controller->getHtmlHeader() ) . $xoopsTpl->get_template_vars( 'xoops_module_header' ) ) ;
	include XOOPS_ROOT_PATH.'/footer.php';
} else {
	$controller->render() ;
}
exit ;

?>