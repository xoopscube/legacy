<?php
/**
 * stdCache Module onInstall Script
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

/**
 * Performs tasks upon module installation
 *
 * @param XoopsModule $module Reference to the module object
 * @return bool True on success, false on failure
 */
function xoops_module_install_stdCache(XoopsModule $module) {
    // If the module needs custom tables to create via PHP (not SQL file),
    // that logic would go here.
    // For stdCache, preferences are defined in xoops_version.php

    // Any specific permissions setup could also go here

    return true; // successful install
}
