<?php
/**
 * MiscFriendAction.class.php
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/forms/MiscFriendForm.class.php';

class Legacy_MiscFriendAction extends Legacy_Action
{
    public $mActionForm = null;
    public $mMailer = null;

    public function hasPermission(&$controller, &$xoopsUser)
    {
        return is_object($xoopsUser);
    }

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->mActionForm =new Legacy_MiscFriendForm();
        $this->mActionForm->prepare();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $this->mActionForm->load($xoopsUser);
        return LEGACY_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return LEGACY_FRAME_VIEW_INPUT;
        }

        $root =& XCube_Root::getSingleton();

        $this->mMailer =& getMailer();
        $this->mMailer->setTemplate('tellfriend.tpl');
        $this->mMailer->assign('SITENAME', $root->mContext->getXoopsConfig('sitename'));
        $this->mMailer->assign('ADMINMAIL', $root->mContext->getXoopsConfig('adminmail'));
        $this->mMailer->assign('SITEURL', XOOPS_URL . '/');

        $this->mActionForm->update($this->mMailer);

        $root->mLanguageManager->loadPageTypeMessageCatalog('misc');

        $this->mMailer->setSubject(sprintf(_MSC_INTSITE, $root->mContext->getXoopsConfig('sitename')));

        return $this->mMailer->send() ? LEGACY_FRAME_VIEW_SUCCESS : LEGACY_FRAME_VIEW_ERROR;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_misc_friend.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_misc_friend_success.html');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_misc_friend_error.html');
        $render->setAttribute('xoopsMailer', $this->mMailer);
    }
}
