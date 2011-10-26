<?php

require_once dirname(dirname(__FILE__)).'/include/main_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/history_functions.php' ;

// set $cat_id,$content_id from $older_history_id
list( $_REQUEST['cat_id'] , $_REQUEST['content_id'] , ) = pico_get_content_history_profile( $mydirname , intval( @$_GET['older_history_id'] ) ) ;

// common prepend
require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;
// global $breadcrumbsObj, $picoRequest, $permissions, $currenCategoryObj
// global $xoopsModuleConfig(overridden)

// add request
$picoRequest['older_history_id'] = intval( @$_GET['older_history_id'] ) ;
$picoRequest['newer_history_id'] = intval( @$_GET['newer_history_id'] ) ;
$picoRequest['view'] = @$_GET['view'] == 'single' ? 'single' : 'diffhistories' ;

// controller
require_once dirname(dirname(__FILE__)).'/class/PicoControllerDiffHistories.class.php' ;
$controller = new PicoControllerDiffHistories( $currentCategoryObj ) ;
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