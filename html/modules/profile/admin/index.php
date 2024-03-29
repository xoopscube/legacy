<?php
/**
 * @file
 * @brief The page controller in the directory
 * @package profile
 * @version 2.3
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

$root =& XCube_Root::getSingleton();

$root->mContext->mModule->setAdminMode(true);
$root->mController->execute();

require_once XOOPS_ROOT_PATH . '/footer.php';
