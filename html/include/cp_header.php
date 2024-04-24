<?php
/**
 * Xoops Control panel header
 * @package    XCL
 * @subpackage core
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 * @brief      This file was entirely rewritten by the XOOPSCube Legacy project
 *             for compatibility with XOOPS2
 */

if (!defined('XOOPS_ROOT_PATH')) {
    //
    // Strange code? This file is used from files in admin directories having no include "mainfile.php".
    // This is deprecated  since XOOPSCube Legacy.
    //

    /*
     * If you use open_basedir in php.ini and use file_exists for file outside open_basedir path,
     * you will not be warned at log and file_exists returns false even if file really exists.
     */
    if (!file_exists('../../../mainfile.php')) {
        if (!file_exists('../../mainfile.php')) {
            exit();
        }

        require_once '../../mainfile.php';
    } else {
        require_once '../../../mainfile.php';
    }
}

if (!defined('XOOPS_CPFUNC_LOADED')) {
    require_once XOOPS_ROOT_PATH . '/include/cp_functions.php';
}

//
// [Special Task] Additional CHECK!!
// Old modules may call this file from other admin directory.
// In this case, the controller does not have Admin Module Object.
//
$root =& XCube_Root::getSingleton();

require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_AdminControllerStrategy.class.php';
$strategy =new Legacy_AdminControllerStrategy($root->mController);

$root->mController->setStrategy($strategy);
$root->mController->setupModuleContext();
$root->mController->_mStrategy->setupModuleLanguage();    //< Umm...
