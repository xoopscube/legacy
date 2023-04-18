<?php
/**
 * Admin Users Online
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_AdminBlockOnline extends Legacy_AbstractBlockProcedure
{
    public function getName()
    {
        return 'block_online';
    }

    public function getTitle()
    {
        return _AD_BLOCK_ONLINE;
    }

    public function getEntryIndex()
    {
        return 0;
    }

    public function isEnableCache()
    {
        return false;
    }

    public function execute()
    {
        $root =& XCube_Root::getSingleton();
        $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;

        // Language catalog
        $root->mLanguageManager->loadBlockMessageCatalog('user');

        require_once XOOPS_ROOT_PATH . '/modules/user/blocks/user_online.php';
        // vars
        $contents = b_user_online_show();
        $uid = $xoopsUser->get('uid');
        $uname = $xoopsUser->get('uname');

        // XCube RenderTarget
        $render =& $this->getRenderTarget();

        // Load theme template i.e. fallback
        $render->setAttribute('legacy_module', 'legacy');
        $render->setAttribute('uid', $uid);
        $render->setAttribute('uname', $uname);
        // Attributes Smarty vars
        $render->setAttribute('contents', $contents);
        $render->setAttribute('blockid', $this->getName());
        // Render Template
        $render->setTemplateName('legacy_admin_block_onlineinfo.html');
        // Render System
        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());
        // Render Block
        $renderSystem->renderBlock($render);
    }

    public function hasResult()
    {
        return true;
    }

    public function &getResult()
    {
        $dmy = 'dummy';
        return $dmy;
    }

    public function getRenderSystemName()
    {
        return 'Legacy_AdminRenderSystem';
    }
}


