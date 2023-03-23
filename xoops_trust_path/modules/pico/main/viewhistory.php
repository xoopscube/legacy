<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

require_once dirname( __DIR__ ) . '/include/main_functions.php';
require_once dirname( __DIR__ ) . '/include/history_functions.php';

// set $cat_id,$content_id from $content_history_id
$content_history_id = (int) @$_GET['content_history_id'];

[ $_REQUEST['cat_id'], $_REQUEST['content_id'], ] = pico_get_content_history_profile( $mydirname, $content_history_id );

// common prepend
require dirname( __DIR__ ) . '/include/common_prepend.inc.php';
// global $breadcrumbsObj, $picoRequest, $permissions, $currenCategoryObj
// global $xoopsModuleConfig(overridden)

// add request
$picoRequest['content_history_id'] = $content_history_id;

$picoRequest['view'] = 'viewhistory';

// controller
require_once dirname( __DIR__ ) . '/class/PicoControllerGetHistory.class.php';

$controller = new PicoControllerGetHistory( $currentCategoryObj );

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
