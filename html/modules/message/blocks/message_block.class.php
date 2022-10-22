<?php
/**
 * @license https://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 3
 * @author Marijuana
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
        if (null !== $service) {
            $client = $root->mServiceManager->createClient($service);
            $render->setAttribute('block', $client->call('getCountUnreadPM', ['uid' => $uid]));
        }

        if (null !== $root->mServiceManager->getService('UserSearch')) {
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
