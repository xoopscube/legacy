<?php
/**
 * Bannerstats - LogoutAction: Handles logout
 */
if (!defined('XOOPS_ROOT_PATH')) exit();

// Include the Legacy module's Action class
if (!class_exists('Legacy_Action')) {
    require_once XOOPS_ROOT_PATH . '/modules/legacy/class/Legacy_Action.class.php';
}

class Bannerstats_LogoutAction extends Legacy_Action
{
    function getDefaultView(&$controller, &$xoopsUser)
    {
        return LEGACY_FRAME_VIEW_NONE;
    }
    
    function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        // This won't be called due to our execute() implementation
    }
    
    function execute(&$controller, &$xoopsUser)
    {
        unset($_SESSION['bannerstats_client_id']);
        $controller->executeForward('./index.php?action=Index');
        return LEGACY_FRAME_VIEW_NONE;
    }
}
