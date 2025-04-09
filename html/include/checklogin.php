<?php
/**
 * checklogin
 * @package    XCL
 * @version    XCL 2.5.0
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 * @brief      This file was entirely rewritten by the XOOPSCube Legacy project
 *             for compatibility with XOOPS2
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

$root =& XCube_Root::getSingleton();
$root->mController->checkLogin();

// ! Add after core!
