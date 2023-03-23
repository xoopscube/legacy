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
require_once dirname( __DIR__ ) . '/include/common_functions.php';
require_once dirname( __DIR__ ) . '/class/PicoTextSanitizer.class.php';
require_once dirname( __DIR__ ) . '/class/PicoUriMapper.class.php';
require_once dirname( __DIR__ ) . '/class/PicoPermission.class.php';
require_once dirname( __DIR__ ) . '/class/PicoModelCategory.class.php';
require_once dirname( __DIR__ ) . '/class/PicoModelContent.class.php';
require_once XOOPS_TRUST_PATH . '/libs/altsys/class/AltsysBreadcrumbs.class.php';

// breadcrumbs
$breadcrumbsObj = AltsysBreadcrumbs::getInstance();

$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/' . $mydirname . '/index.php', $xoopsModule->getVar( 'name' ) );

// permissions
$picoPermission = &PicoPermission::getInstance();

$permissions = $picoPermission->getPermissions( $mydirname );

// current category object (this "current" means "parent"
$currentCategoryObj = new PicoCategory( $mydirname, (int) @$_REQUEST['pid'], $permissions );

if ( $currentCategoryObj->isError() ) {
	redirect_header( XOOPS_URL . "/modules/$mydirname/index.php", 2, _MD_PICO_ERR_READCATEGORY );
	exit;
}

// override $xoopsModuleConfig
$xoopsModuleConfig = $currentCategoryObj->getOverriddenModConfig();

// append paths from each categories into breadcrumbs
$breadcrumbsObj->appendPath( $currentCategoryObj->getBreadcrumbs() );

// request
$picoRequest = [];

$picoRequest['makecategory'] = true;

$picoRequest['cat_id'] = - 1;

if ( ! empty( $_POST['categoryman_post'] ) ) {
	$controller_class = 'PicoControllerInsertCategory';
} else {
	$controller_class = 'PicoControllerEditCategory';
}

require_once dirname( __DIR__ ) . '/class/' . $controller_class . '.class.php';

$controller = new $controller_class( $currentCategoryObj );

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
