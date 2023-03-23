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

class Legacy_AdminBlockLogInfo extends Legacy_AbstractBlockProcedure
{
    public function getName()
    {
        return 'block_loginfo';
    }

    public function getTitle()
    {
        return 'Admin Log Info';
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
        $root->mLanguageManager->loadBlockMessageCatalog('legacy');

        $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;
        require_once XOOPS_ROOT_PATH . '/modules/legacy/blocks/legacy_usermenu.php';

        $contents = b_legacy_usermenu_show();

        $uid = $xoopsUser->get('uid');
        $useragent  = xoops_getenv('HTTP_USER_AGENT');

        $render =& $this->getRenderTarget();

        // Load theme template ie fallback
        $render->setAttribute('legacy_module', 'legacy');

        $render->setAttribute('uid', $uid);
        $render->setAttribute('useragent', $useragent);
        $render->setAttribute('contents', $contents);
        $render->setAttribute('blockid', $this->getName());

        $render->setTemplateName('legacy_admin_block_loginfo.html');

        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());

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


