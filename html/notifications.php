<?php
/**
 * @package Legacy
 * @version $Id: notifications.php,v 1.3 2008/09/25 15:10:29 kilica Exp $
 * @copyright (c) 2005-2025 The XOOPSCube Project
 * @license GPL 2.0
 */
/*------------------------------------------------------------------------*
 |  This file was entirely rewritten by the XOOPS Cube Legacy project for |
 |   keeping compatibility with XOOPS 2.0.x                               |
 *------------------------------------------------------------------------*/

require_once 'mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

XCube_DelegateUtils::call('Legacypage.Notifications.Access');
