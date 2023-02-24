<?php
/**
 * Cube core init
 * @package    XCL
 * @subpackage core
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_MAINFILE_INCLUDED')) {
    exit();
}
if (!defined('XOOPS_TRUST_PATH')) {
    echo '_TRUST_PATH is required in mainfile.php';
    exit();
}

/**
 * This constant validates that this system is XOOPSCube, for module developers.
 */
define('XOOPS_CUBE_LEGACY', true);

/**
 * This constant validates the system BASE version, for module developers.
 * ex) if(defined('LEGACY_BASE_VERSION') && version_compare(LEGACY_BASE_VERSION, '2.2.0.0', '>='))
 */
define('LEGACY_BASE_VERSION', '2.3.1.1'); // ! XOOPSCube version

require_once XOOPS_ROOT_PATH . '/core/XCube_Root.class.php';
require_once XOOPS_ROOT_PATH . '/core/XCube_Controller.class.php';
require_once XOOPS_ROOT_PATH . '/core/libs/IniHandler.class.php';

//
// TODO We have to move the following lines to an appropriate place.
//		(We may not need the following constants here)
//

define('XCUBE_SITE_SETTING_FILE', XOOPS_TRUST_PATH . '/settings/site_default.ini');
define('XCUBE_SITE_CUSTOM_FILE', XOOPS_TRUST_PATH . '/settings/site_custom.ini');
define('XCUBE_SITE_CUSTOM_FILE_SALT', XOOPS_TRUST_PATH . '/settings/site_custom_' . XOOPS_SALT . '.ini');
define('XCUBE_SITE_DIST_FILE', XOOPS_TRUST_PATH . '/settings/site_default.dist.ini'); // for CorePack

//
//@todo Documentation How does the system decide on the main controller?
//
$root=&XCube_Root::getSingleton();
//$root->loadSiteConfig(XCUBE_SITE_SETTING_FILE, XCUBE_SITE_CUSTOM_FILE, XCUBE_SITE_CUSTOM_FILE_SALT);
$root->loadSiteConfig(XCUBE_SITE_SETTING_FILE, XCUBE_SITE_DIST_FILE, XCUBE_SITE_CUSTOM_FILE, XCUBE_SITE_CUSTOM_FILE_SALT); // edit by CorePack
$root->setupController();
