<?php
/**
 * Bannerstats - Module for XCL
 * LogoutAction: handles logout and redirect
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_LogoutAction
{
    public function __construct()
    {
    }

    /**
     * Performs the logout and redirects
     * This method is called by index.php
     *
     * @param XCube_Controller
     * @param XoopsTpl
     * @return null Returns null because it handles its own redirection
     */
    public function getDefaultView(XCube_Controller $controller, XoopsTpl $xoopsTpl)
    {
        if (class_exists('BannerClientSession')) {
            BannerClientSession::logout();
        }

        $moduleDirname = 'bannerstats';

        $moduleObject = $controller->mRoot->mContext->mModule;
        if (is_object($moduleObject)) {
            if (method_exists($moduleObject, 'getVar')) {
                $fetchedDirname = $moduleObject->getVar('dirname');
                if (!empty($fetchedDirname)) {
                    $moduleDirname = $fetchedDirname;
                }
            } elseif (method_exists($moduleObject, 'get')) {
                $fetchedDirname = $moduleObject->get('dirname');
                if (!empty($fetchedDirname)) {
                    $moduleDirname = $fetchedDirname;
                }
                error_log("Bannerstats_LogoutAction - Called get('dirname') as getVar() was not found on module object of class: " . get_class($moduleObject));
            } else {
                error_log("Bannerstats_LogoutAction - Neither getVar() nor get() method found on module object of class: " . get_class($moduleObject) . ". Using default dirname '{$moduleDirname}'.");
            }
        } else {
            error_log("Bannerstats_LogoutAction - Module context (mModule) is not an object. Using default dirname '{$moduleDirname}'.");
        }
        
        $redirectUrl = XOOPS_URL . '/modules/' . $moduleDirname . '/index.php?action=Login';
        $controller->executeForward($redirectUrl);
        
        return null;
    }

    public function execute(XCube_Controller $controller, XoopsTpl $xoopsTpl)
    {
        return $this->getDefaultView($controller, $xoopsTpl);
    }

    public function getPageTitle(): string
    {
        return "Client Logout";
    }
}
