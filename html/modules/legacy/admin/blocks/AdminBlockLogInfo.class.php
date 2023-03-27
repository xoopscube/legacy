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
        $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;

        // Language catalog
        $root->mLanguageManager->loadBlockMessageCatalog('legacy');

        if (is_object($xoopsUser)) {

            $uid = $xoopsUser->get('uid');
            $uname =$xoopsUser->get('uname');
            $flagShowInbox = false;

            //
            // Check does this system have PrivateMessage feature.
            //
            $url = null;
            $service =& $root->mServiceManager->getService('privateMessage');
            if (null != $service) {
                $client =& $root->mServiceManager->createClient($service);
                $url = $client->call('getPmInboxUrl', ['uid' => $xoopsUser->get('uid')]);

                if (null != $url) {
                    $inbox_url = $url;
                    $new_messages = $client->call('getCountUnreadPM', ['uid' => $xoopsUser->get('uid')]);
                    $flagShowInbox = true;
                }
            }

        //    $show_adminlink = $root->mContext->mUser->isInRole('Site.Administrator');


        $useragent  = xoops_getenv('HTTP_USER_AGENT');

        // XCube RenderTarget
        $render = $this->getRenderTarget();

        // Load theme template i.e. fallback
        $render->setAttribute('legacy_module', 'legacy');
        // Attributes Smarty vars
        $render->setAttribute('uid', $uid);
        $render->setAttribute('uname', $uname);
        $render->setAttribute('inbox_url', $inbox_url);
        $render->setAttribute('new_messages', $new_messages);
        $render->setAttribute('flagShowInbox', $flagShowInbox);
        $render->setAttribute('useragent', $useragent);
        $render->setAttribute('blockid', $this->getName());
        // Render Template
        $render->setTemplateName('legacy_admin_block_loginfo.html');
        // Render Template
        $renderSystem = $root->getRenderSystem($this->getRenderSystemName());
        // Render Template
        $renderSystem->renderBlock($render);
        }
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


