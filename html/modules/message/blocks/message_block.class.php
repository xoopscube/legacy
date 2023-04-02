<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.3.3
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2023 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
class Message_Block extends Legacy_BlockProcedure
{
//    public function __construct(&$block)
//    {
//        parent::__construct($block);
//    }

    public function prepare()
    {
    }

    public function getTitle()
    {
        return _MI_MESSAGE_NAME;
    }

    public function isDisplay()
    {
        $root = XCube_Root::getSingleton();
        return $root->mContext->mUser->isInRole('Site.RegisteredUser');
    }

    public function execute()
    {
        if (!$this->isDisplay()) {
            return;
        }
        $root = XCube_Root::getSingleton();
        $root->mLanguageManager->loadModinfoMessageCatalog(basename(dirname(__DIR__)));
        $root->mLanguageManager->loadModuleMessageCatalog(basename(dirname(__DIR__)));

        $render = $this->getRenderTarget();
        $render->setTemplateName($this->_mBlock->get('template'));
        $render->setAttribute('mid', $this->_mBlock->get('mid'));
        $render->setAttribute('bid', $this->_mBlock->get('bid'));

        $service = $root->mServiceManager->getService('privateMessage');
        $uid = $root->mContext->mXoopsUser->get('uid');
        if ($service !== null) {
            $client = $root->mServiceManager->createClient($service);
            $render->setAttribute('block', $client->call('getCountUnreadPM', ['uid' => $uid]));
        }

        if ($root->mServiceManager->getService('UserSearch') !== null) {
            $render->setAttribute('UserSearch', true);
        }

        $modHand = xoops_getmodulehandler('inbox', 'message');
        $render->setAttribute('incount', $modHand->getInboxCount($uid));

        $modHand = xoops_getmodulehandler('outbox', 'message');
        $render->setAttribute('outcount', $modHand->getOutboxCount($uid));
        $render->setAttribute('message_url', XOOPS_URL.'/modules/message/index.php');
        $renderSystem = $root->getRenderSystem($this->getRenderSystemName());
        $renderSystem->renderBlock($render);
    }
}
