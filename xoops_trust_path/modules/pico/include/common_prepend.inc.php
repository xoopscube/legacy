<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

require_once dirname( __DIR__ ) . '/include/main_functions.php';
require_once dirname( __DIR__ ) . '/include/common_functions.php';
require_once dirname( __DIR__ ) . '/class/PicoTextSanitizer.class.php';
require_once dirname( __DIR__ ) . '/class/PicoUriMapper.class.php';
require_once dirname( __DIR__ ) . '/class/PicoPermission.class.php';
require_once dirname( __DIR__ ) . '/class/PicoModelCategory.class.php';
require_once dirname( __DIR__ ) . '/class/PicoModelContent.class.php';
require_once XOOPS_TRUST_PATH . '/libs/altsys/class/AltsysBreadcrumbs.class.php';

// add XOOPS_TRUST_PATH/PEAR/ into include_path
if ( ! defined( 'PATH_SEPARATOR' ) ) {
	define( 'PATH_SEPARATOR', DIRECTORY_SEPARATOR == '/' ? ':' : ';' );
}
ini_set( 'include_path', ini_get( 'include_path' ) . PATH_SEPARATOR . XOOPS_TRUST_PATH . '/PEAR' );

// breadcrumbs
$breadcrumbsObj = AltsysBreadcrumbs::getInstance();

$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/' . $mydirname . '/index.php', $xoopsModule->getVar( 'name' ) );

// URI Mapper
$mapper_class = empty( $xoopsModuleConfig['uri_mapper_class'] ) ? 'PicoUriMapper' : $xoopsModuleConfig['uri_mapper_class'];

require_once dirname( __DIR__ ) . '/class/' . $mapper_class . '.class.php';

$uriMapper = new $mapper_class( $mydirname, $xoopsModuleConfig );

$uriMapper->initGet();

// get requests
$picoRequest = $uriMapper->parseRequest(); // clean data

// permissions
$picoPermission = &PicoPermission::getInstance();

$permissions = $picoPermission->getPermissions( $mydirname );

// current category object
$currentCategoryObj = new PicoCategory( $mydirname, $picoRequest['cat_id'], $permissions );

if ( $currentCategoryObj->isError() ) {
	redirect_header( XOOPS_URL . "/modules/$mydirname/index.php", 2, _MD_PICO_ERR_READCATEGORY );
	exit;
}

// override $xoopsModuleConfig
$xoopsModuleConfig = $currentCategoryObj->getOverriddenModConfig();

// append paths from each categories into breadcrumbs
$breadcrumbsObj->appendPath( $currentCategoryObj->getBreadcrumbs() );
