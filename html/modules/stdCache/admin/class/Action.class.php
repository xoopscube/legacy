<?php
/**
 * Standard cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class stdCache_Action
{
    public $mAdminFlag = false;

    public function __construct($adminFlag = false)
    {
        $this->mAdminFlag = $adminFlag;
    }

    public function isSecure()
    {
        return false;
    }

    protected function _getPageAction()
    {
        return null;
    }

    protected function _getPagetitle()
    {
        return null;
    }

    public function getPageTitle()
    {
        return Legacy_Utils::formatPagetitle(XCube_Root::getSingleton()->mContext->mModule->mXoopsModule->get('name'), $this->_getPagetitle(), $this->_getPageAction());
    }

    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        return true;
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        return true;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return STDCACHE_FRAME_VIEW_SUCCESS;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return true;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $render->setAttribute('xoops_pagetitle', $this->getPageTitle());
        return true;
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $render->setAttribute('xoops_pagetitle', $this->getPageTitle());
        return true;
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setAttribute('xoops_pagetitle', $this->getPageTitle());
        return true;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setAttribute('xoops_pagetitle', $this->getPageTitle());
        return true;
    }

    public function executeViewPreview(&$controller, &$xoopsUser, &$render)
    {
        $render->setAttribute('xoops_pagetitle', $this->getPageTitle());
        return true;
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $render->setAttribute('xoops_pagetitle', $this->getPageTitle());
        return true;
    }
}
