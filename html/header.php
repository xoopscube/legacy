<?php
/**
 * @package Legacy
 * @version $Id: header.php,v 1.3 2008/09/25 15:10:26 kilica Exp $
 * @copyright (c) 2005-2024 The XOOPSCube Project
 * @license GPL 2.0
 */
/*------------------------------------------------------------------------*
 |  This file was entirely rewritten by the XOOPS Cube Legacy project for |
 |   keeping compatibility with XOOPS 2.0.x                               |
 *------------------------------------------------------------------------*/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

$root=&XCube_Root::getSingleton();
if (!is_object($root->mController)) {
    exit();
}

$root->mController->executeHeader();
