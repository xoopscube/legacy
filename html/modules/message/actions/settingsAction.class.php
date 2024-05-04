<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.4.0
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2024 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require _MY_MODULE_PATH.'forms/MessageSettingsForm.class.php';

class settingsAction extends AbstractAction
{
    private \MessageSettingsForm $mActionForm;
    private $mService;

    public function __construct()
    {
        parent::__construct();
        $this->mActionForm = new MessageSettingsForm();
        $this->mActionForm->prepare();
    }

    public function execute()
    {
        $modHand = xoops_getmodulehandler('settings', _MY_DIRNAME);
        $modObj = $modHand->get($this->root->mContext->mXoopsUser->get('uid'));
        if (!is_object($modObj)) {
            $modObj = $modHand->create();
        }
        $this->mActionForm->load($modObj);
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $this->mActionForm->fetch();
            $this->mActionForm->validate();
            if ($this->mActionForm->hasError()) {
                $this->setErr($this->mActionForm->getErrorMessages());
            } else {
                $this->setUrl('index.php?action=settings');
                $this->mActionForm->update($modObj);
                if (!$modHand->insert($modObj)) {
                    $this->setErr(_MD_MESSAGE_SETTINGS_MSG4);
                } else {
                    $this->setErr(_MD_MESSAGE_SETTINGS_MSG3);
                }
            }
        }
        // service UserSearch
        $this->mService = $this->root->mServiceManager->getService('UserSearch');
    }

    public function executeView(&$render)
    {
        $render->setTemplateName('message_settings.html');
        $render->setAttribute('mActionForm', $this->mActionForm);
        $render->setAttribute('purgedays', $this->root->mContext->mModuleConfig['savedays']);
        $render->setAttribute('purgetype', $this->root->mContext->mModuleConfig['dletype']);
        $render->setAttribute('UserSearch', $this->mService);
        $render->setAttribute('message_url', XOOPS_URL.'/modules/message/index.php');
    }
}
