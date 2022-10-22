<?php
/**
 * @package Legacy
 * @version $Id: footer.php,v 1.3 2008/09/25 15:10:07 kilica Exp $
 * @copyright (c) 2005-2022 The XOOPSCube Project
 * @license GPL 2.0
 */
/*------------------------------------------------------------------------*
 |  This file was entirely rewritten by the XOOPS Cube Legacy project for |
 |   keeping compatibility with XOOPS 2.0.x                               |
 *------------------------------------------------------------------------*/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
if (defined('XOOPS_FOOTER_INCLUDED')) {
    exit();
}

$root=&XCube_Root::getSingleton();
if (!is_object($root->mController)) {
    exit();
}

define('XOOPS_FOOTER_INCLUDED', 1);

$xoopsLogger=&$root->mController->getLogger();
$xoopsLogger->stopTime();

// RMV-NOTIFY
require_once XOOPS_ROOT_PATH.'/include/notification_select.php';

// Display view
$root->mController->executeView();

// Count req
//$filereq = ( count(get_included_files()) );
//echo "<p>Cound included files : $filereq</p>";
