<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/pico.textsanitizer.php' ;
require_once dirname(dirname(__FILE__)).'/class/PicoUriMapper.class.php' ;
require_once dirname(dirname(__FILE__)).'/class/PicoPermission.class.php' ;
require_once dirname(dirname(__FILE__)).'/class/PicoModelCategory.class.php' ;
require_once dirname(dirname(__FILE__)).'/class/PicoModelContent.class.php' ;


// common prepend
require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;
// global $breadcrumbsObj, $picoRequest, $permissions, $currenCategoryObj
// global $xoopsModuleConfig(overridden)

// modifying controller/view of $picoRequest
$picoRequest = $uriMapper->modifyRequest( $picoRequest , $currentCategoryObj ) ;

if( $picoRequest['controller'] == 'content' ) {
	// content (viewcontent)
	require_once dirname(dirname(__FILE__)).'/class/PicoControllerGetContent.class.php' ;
	$controller = new PicoControllerGetContent( $currentCategoryObj ) ;
} else if( $picoRequest['controller'] == 'htmlwrapped' ) {
	// just html wrapping (viewcontent)
	require_once dirname(dirname(__FILE__)).'/class/PicoControllerGetHtmlwrapped.class.php' ;
	$controller = new PicoControllerGetHtmlwrapped( $currentCategoryObj ) ;
} else if( $picoRequest['controller'] == 'category' ) {
	// category (subcategories and contents)
	require_once dirname(dirname(__FILE__)).'/class/PicoControllerGetCategory.class.php' ;
	$controller = new PicoControllerGetCategory( $currentCategoryObj ) ;
} else if( $picoRequest['controller'] == 'latestcontents' ) {
	// latestcontents under the category (mainly for rss)
	require_once dirname(dirname(__FILE__)).'/class/PicoControllerGetLatestcontents.class.php' ;
	$controller = new PicoControllerGetLatestcontents( $currentCategoryObj ) ;
} else if( $picoRequest['controller'] == 'querycontents' ) {
	// querycontents (tag etc)
	require_once dirname(dirname(__FILE__)).'/class/PicoControllerQueryContents.class.php' ;
	$controller = new PicoControllerQueryContents( $currentCategoryObj ) ;
} else {
	// menu
	require_once dirname(dirname(__FILE__)).'/class/PicoControllerGetMenu.class.php' ;
	$controller = new PicoControllerGetMenu( $currentCategoryObj ) ;
}

// execute
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