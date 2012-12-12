<?php
/**
 * @file
 * @brief The page controller in the directory
 * @package profile
 * @version $Id$
 */

require_once "../../../mainfile.php";
require_once XOOPS_ROOT_PATH . "/header.php";
require_once XOOPS_MODULE_PATH . "/profile/class/ActionFrame.class.php";

$root =& XCube_Root::getSingleton();
$actionName = isset($_GET['action']) ? trim($_GET['action']) : NULL;	//"ProfileList";
$moduleRunner = new Profile_ActionFrame(true);
$moduleRunner->setActionName($actionName);

//$root->mController->mExecute->add(array(&$moduleRunner, 'execute'));
$root->mContext->mModule->setAdminMode(true);
$root->mController->execute();

require_once XOOPS_ROOT_PATH . "/footer.php";
