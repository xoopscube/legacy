<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
require _MY_MODULE_PATH.'forms/MessageSettingsForm.class.php';

class settingsAction extends AbstractAction
{
  private $mActionForm;
  
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
    if ( !is_object($modObj) ) {
      $modObj = $modHand->create();
    }
    $this->mActionForm->load($modObj);
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
      $this->mActionForm->fetch();
      $this->mActionForm->validate();
      if ($this->mActionForm->hasError()) {
        $this->setErr($this->mActionForm->getErrorMessages());
      } else {
        $this->setUrl('index.php?action=settings');
        $this->mActionForm->update($modObj);
        if ( !$modHand->insert($modObj) ) {
          $this->setErr(_MD_MESSAGE_SETTINGS_MSG4);
        } else {
          $this->setErr(_MD_MESSAGE_SETTINGS_MSG3);
        }
      }
    }
  }
  
  public function executeView(&$render)
  {
    $render->setTemplateName('message_settings.html');
    $render->setAttribute('mActionForm', $this->mActionForm);
  }
}
?>
