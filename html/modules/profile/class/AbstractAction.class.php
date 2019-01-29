<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_AbstractAction
{
    public $mRoot = null;
    public $mModule = null;
    public $mAsset = null;

    /**
     * @public
     */
    public function &_getHandler()
    {
    }
    public function Profile_AbstractAction()
    {
        self::__construct();
    }
    public function __construct()
    {
        $this->mRoot =& XCube_Root::getSingleton();
        $this->mModule =& $this->mRoot->mContext->mModule;
        $this->mAsset =& $this->mModule->mAssetManager;
    }
    public function isMemberOnly()
    {
        return false;
    }
    public function isAdminOnly()
    {
        return false;
    }
    public function prepare()
    {
        return true;
    }

    public function hasPermission()
    {
        return true;
    }

    public function getDefaultView()
    {
        return Profile_FRAME_VIEW_NONE;
    }

    public function execute()
    {
        return Profile_FRAME_VIEW_NONE;
    }

    public function executeViewSuccess(&$controller, &$render)
    {
    }

    public function executeViewError(&$render)
    {
    }

    public function executeViewIndex(&$render)
    {
    }

    public function executeViewInput(&$render)
    {
    }

    public function executeViewPreview(&$render)
    {
    }

    public function executeViewCancel(&$render)
    {
    }
}
