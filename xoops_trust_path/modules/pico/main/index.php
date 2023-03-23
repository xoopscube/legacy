<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

require_once dirname( __DIR__ ) . '/include/common_functions.php';
require_once dirname( __DIR__ ) . '/class/PicoTextSanitizer.class.php';
require_once dirname( __DIR__ ) . '/class/PicoUriMapper.class.php';
require_once dirname( __DIR__ ) . '/class/PicoPermission.class.php';
require_once dirname( __DIR__ ) . '/class/PicoModelCategory.class.php';
require_once dirname( __DIR__ ) . '/class/PicoModelContent.class.php';


// common prepend
require dirname( __DIR__ ) . '/include/common_prepend.inc.php';
// global $breadcrumbsObj, $picoRequest, $permissions, $currenCategoryObj
// global $xoopsModuleConfig(overridden)

// modifying controller/view of $picoRequest
$picoRequest = $uriMapper->modifyRequest( $picoRequest, $currentCategoryObj );

if ( 'content' == $picoRequest['controller'] ) {
	// content (viewcontent)
	require_once dirname( __DIR__ ) . '/class/PicoControllerGetContent.class.php';

	$controller = new PicoControllerGetContent( $currentCategoryObj );

} else if ( 'htmlwrapped' == $picoRequest['controller'] ) {
	// just html wrapping (viewcontent)
	require_once dirname( __DIR__ ) . '/class/PicoControllerGetHtmlwrapped.class.php';

	$controller = new PicoControllerGetHtmlwrapped( $currentCategoryObj );

} else if ( 'category' == $picoRequest['controller'] ) {
	// category (subcategories and contents)
	require_once dirname( __DIR__ ) . '/class/PicoControllerGetCategory.class.php';

	$controller = new PicoControllerGetCategory( $currentCategoryObj );

} else if ( 'latestcontents' == $picoRequest['controller'] ) {
	// latestcontents under the category (mainly for rss)
	require_once dirname( __DIR__ ) . '/class/PicoControllerGetLatestcontents.class.php';

	$controller = new PicoControllerGetLatestcontents( $currentCategoryObj );

} else if ( 'querycontents' == $picoRequest['controller'] ) {
	// querycontents (tag etc)
	require_once dirname( __DIR__ ) . '/class/PicoControllerQueryContents.class.php';

	$controller = new PicoControllerQueryContents( $currentCategoryObj );

} else {
	// menu
	require_once dirname( __DIR__ ) . '/class/PicoControllerGetMenu.class.php';

	$controller = new PicoControllerGetMenu( $currentCategoryObj );

}

// execute
$controller->execute( $picoRequest );

// render
if ( $controller->isNeedHeaderFooter() ) {

	$xoopsOption['template_main'] = $controller->getTemplateName();

	include XOOPS_ROOT_PATH . '/header.php';

	$xoopsTpl->assign( $controller->getAssign() );

	$xoopsTpl->assign( 'xoops_module_header', pico_main_render_moduleheader( $mydirname, $xoopsModuleConfig, $controller->getHtmlHeader() ) . $xoopsTpl->get_template_vars( 'xoops_module_header' ) );

	include XOOPS_ROOT_PATH . '/footer.php';

} else {
	$controller->render();
}
exit;
