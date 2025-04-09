<?php
/**
 * Common Cubecore.init
 * @package    XCL
 * @version    XCL 2.5.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 * @brief      This file was entirely rewritten by the XOOPSCube Legacy project
 *             for compatibility with XOOPS2
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH . '/include/cubecore_init.php';

$root=&XCube_Root::getSingleton();
$xoopsController=&$root->getController();
$xoopsController->executeCommon();
