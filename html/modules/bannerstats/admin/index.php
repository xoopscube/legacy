<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @author     Kazuhisa Minato aka minahito, Core developer
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 * @since      v 1.1 2007/05/15 02:34:17 minahito
 **/

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH. "/header.php";

// CORE CLASS
require_once XOOPS_MODULE_PATH . "/bannerstats/class/Banner.class.php";
require_once XOOPS_MODULE_PATH . "/bannerstats/class/BannerClient.class.php";
require_once XOOPS_MODULE_PATH . "/bannerstats/class/handler/BannerFinish.class.php";
require_once XOOPS_MODULE_PATH . "/bannerstats/class/ActionFrame.class.php";

$root =& XCube_Root::getSingleton();

// Check module admin permission
$moduleHandler =& xoops_gethandler('module');
$module =& $moduleHandler->getByDirname('bannerstats');
$currentUser =& $root->mContext->mXoopsUser;

if (!is_object($currentUser) || !$currentUser->isAdmin($module->getVar('mid'))) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

// Render header
$root->mController->executeHeader();

// Create ActionFrame and execute
$actionFrame = new Bannerstats_ActionFrame(true);
$actionFrame->execute($root->mController);

// Render footer
require_once "../../../footer.php";