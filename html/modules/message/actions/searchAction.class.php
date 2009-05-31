<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class searchAction extends AbstractAction
{
  private $mActionform;
  private $listdata;
  private $mPagenavi = null;
  private $mService;
  
  public function __construct()
  {
    parent::__construct();
    $this->mService = $this->root->mServiceManager->getService('UserSearch');
    
  }
  
  private function getData($request)
  {
    $client = $this->root->mServiceManager->createClient($this->mService);
    $this->listdata = $client->call('getUserList', $request);
  }
  
  public function execute()
  {
    if ( !$this->chk_use() ) {
      $this->setUrl('index.php?action=settings');
      $this->setErr(_MD_MESSAGE_SETTINGS_MSG5);
    } else {
      if ( $this->mService == null ) {
        $this->setErr('Service Not loaded.');
        return;
      }
      $this->root->mLanguageManager->loadModuleMessageCatalog('usersearch');
      require_once XOOPS_MODULE_PATH.'/usersearch/forms/UsersearchForm.class.php';
      $this->mActionform = new UsersearchForm();
      $this->mActionform->prepare();
      
      $this->mActionform->fetch();
      if ( $this->mActionform->get('dosearch') == 1 ) {
        $this->mActionform->validate();
        if ($this->mActionform->hasError()) {
          $this->setErr($this->mActionform->getErrorMessages());
          return;
        }
        $request = array(
          'uname' => $this->mActionform->get('uname'),
          'stype' => $this->mActionform->get('searchtype'),
          'page'  => 10,
          'url'   => 'index.php?action=search'
        );
        $this->getData($request);
      } else {
        $this->mActionform->set('searchtype', 0);
      }
    }
  }
  
  public function executeView(&$render)
  {
    $render->setTemplateName('message_usersearch.html');
    $render->setAttribute('mActionform', $this->mActionform);
    $render->setAttribute('listdata', $this->listdata);
  }
}
?>
