<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.3.3
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2023 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';
$root = XCube_Root::getSingleton();
//$root =& XCube_Root::getSingleton();
$root->mController->executeForward($root->mContext->mModule->getAdminIndex());
