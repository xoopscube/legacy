<?php
/**
 * @package Legacy
 * @version XCL 2.5.0
 * @author Nuno Luciano aka Gigamaster XCL PHP7
 * @author kilica, v 1.3 2008/09/25 15:10:19 Exp $
 * @copyright (c) 2005-2025 The XOOPSCube Project
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
            if (is_dir(XOOPS_ROOT_PATH . '/install/')) {
                $alert[]= '<div class="error">'. XCube_Utils::formatString( _WARNINSTALL2, XOOPS_ROOT_PATH . '/install'). '<button type="button" class="button alert-close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
            }
            if (is_writable(XOOPS_ROOT_PATH . '/mainfile.php')) {
                $alert[]= '<div class="error">'. XCube_Utils::formatString(_WARNINWRITEABLE, XOOPS_ROOT_PATH . '/mainfile.php'). '<button type="button" class="button alert-close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
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
