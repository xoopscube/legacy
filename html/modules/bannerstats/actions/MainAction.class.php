<?php
/**
 * Bannerstats - MainAction: Shows login or redirects to stats if authenticated
 */

// Include mainfile.php to access system bootstrap and constants
require_once '../../../../mainfile.php';

if (!defined('XOOPS_ROOT_PATH')) exit();

// Include the Legacy module's Action class
if (!class_exists('Legacy_Action')) {
    require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Action.class.php';
}

class Bannerstats_MainAction extends Legacy_Action
{
    function getDefaultView(&$controller, &$xoopsUser)
    {
        if (isset($_SESSION['bannerstats_client_id'])) {
            $controller->executeForward('./index.php?action=Stats');
            return LEGACY_FRAME_VIEW_NONE;
        } else {
            $controller->executeForward('./index.php?action=Login');
            return LEGACY_FRAME_VIEW_NONE;
        }
    }
    
    function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        // This won't be called due to our getDefaultView() implementation
    }
}
