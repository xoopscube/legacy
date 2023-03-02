<?php
/**
 * @package Legacy
 * @version $Id: admin.php,v 1.3 2008/09/25 15:10:19 kilica Exp $
 * @copyright (c) 2005-2023 The XOOPSCube Project
 * @license GPL 2.0
 */
/*------------------------------------------------------------------------*
 |  This file was entirely rewritten by the XOOPS Cube Legacy project for |
 |   keeping compatibility with XOOPS 2.0.x                               |
 *------------------------------------------------------------------------*/

include 'mainfile.php';

class DefaultSystemCheckFunction
{
    public static function DefaultCheck()
    {
        if (!defined('LEGACY_INSTALLERCHECKER_ACTIVE')) {
            define('LEGACY_INSTALLERCHECKER_ACTIVE', true); $var = [];
        }
        $alert = [];
        $alert[]= '<div class="alert-notify">';
        // if ( LEGACY_INSTALLERCHECKER_ACTIVE == true || XUPDATE_INSTALLERCHECKER_ACTIVE == true && is_dir(XOOPS_ROOT_PATH . '/install' ) ) {
        //if (LEGACY_INSTALLERCHECKER_ACTIVE == true && XUPDATE_INSTALLERCHECKER_ACTIVE == true) {
            //if ( LEGACY_INSTALLERCHECKER_ACTIVE == true && is_dir(XOOPS_ROOT_PATH . '/install' ) ) {
            if (is_dir(XOOPS_ROOT_PATH . '/install/')) {
                //xoops_error(XCube_Utils::formatString( _WARNINSTALL2, XOOPS_ROOT_PATH . '/install'),'install', 'warning'); version XCL 2.3
                $alert[]= '<div class="error"><button type="button" class="button alert-close" data-dismiss="alert" aria-hidden="true">&times;</button>'. XCube_Utils::formatString( _WARNINSTALL2, XOOPS_ROOT_PATH . '/install'). '</div>';
            }
            if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
                //xoops_error(sprintf(_WARNINWRITEABLE, XOOPS_ROOT_PATH.'/mainfile.php'), '', 'warning'); version Legacy 2.2
                //xoops_error(XCube_Utils::formatString(_WARNINWRITEABLE, XOOPS_ROOT_PATH . '/mainfile.php')); version XCL 2.3
                $alert[]= '<div class="error"><button type="button" class="button alert-close" data-dismiss="alert" aria-hidden="true">&times;</button>'. XCube_Utils::formatString(_WARNINWRITEABLE, XOOPS_ROOT_PATH . '/mainfile.php'). '</div>';
            }
        $alert[]= '</div>';
        foreach ($alert as $notify) {
            echo "$notify ";
        }

    }
}

// RENDER
require_once XOOPS_ROOT_PATH . '/header.php';
$root =& XCube_Root::getSingleton();
$root->mDelegateManager->add('Legacypage.Admin.SystemCheck', 'DefaultSystemCheckFunction::DefaultCheck');
XCube_DelegateUtils::call('Legacypage.Admin.SystemCheck');
require_once XOOPS_ROOT_PATH . '/footer.php';
