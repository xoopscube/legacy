<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class favoritesAction extends AbstractAction
{
  private $mService;
  private $favorites;
  
  public function __construct()
  {
    parent::__construct();
    $this->mService = $this->root->mServiceManager->getService('UserSearch');
    $this->setUrl('index.php?action=favorites');
  }
  
  private function addFavorites()
  {
    $ret = array();
    $adduid = $this->root->mContext->mRequest->getRequest('adduid');
    if ( !is_array($adduid) || count($adduid) == 0 ) {
      $this->setErr(_MD_MESSAGE_FAVORITES0);
      return true;
    }
    $mid = $this->root->mContext->mXoopsModule->get('mid');
    $client = $this->root->mServiceManager->createClient($this->mService);
    foreach ( $adduid as $fuid ) {
      $ret[] = $client->call('addFavorites', array('mid' => $mid, 'fuid' => $fuid, 'weight' => 0));
    }
    if ( in_array(false, $ret) ) {
      $this->setErr(_MD_MESSAGE_FAVORITES1);
    } else {
      $this->setErr(_MD_MESSAGE_FAVORITES2);
    }
  }
  
  private function edtFavorites()
  {
    $weight = $this->root->mContext->mRequest->getRequest('weight');
    if ( !is_array($weight) || count($weight) == 0 ) {
      return true;
    }
    $client = $this->root->mServiceManager->createClient($this->mService);
    foreach ( $weight as $id => $w ) {
      $ret[] = $client->call('edtFavorites', array('id' => $id, 'weight' => $w));
    }
    if ( in_array(false, $ret) ) {
      $this->setErr(_MD_MESSAGE_FAVORITES3);
      return false;
    } else {
      $this->setErr(_MD_MESSAGE_FAVORITES4);
    }
    return true;
  }
  
  private function delFavorites()
  {
    $delid = $this->root->mContext->mRequest->getRequest('delid');
    if ( !is_array($delid) || count($delid) == 0 ) {
      return;
    }
    $client = $this->root->mServiceManager->createClient($this->mService);
    foreach ( $delid as $id ) {
      $ret[] = $client->call('delFavorites', array('id' => $id));
    }
    if ( in_array(false, $ret) ) {
      $this->setErr(_MD_MESSAGE_FAVORITES3);
    } else {
      $this->setErr(_MD_MESSAGE_FAVORITES5);
    }
  }
  
  private function getFavorites()
  {
    $mid = $this->root->mContext->mXoopsModule->get('mid');
    $client = $this->root->mServiceManager->createClient($this->mService);
    $this->favorites = $client->call('getFavoritesUsers', array('mid' => $mid));
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
      $cmd = $this->root->mContext->mRequest->getRequest('cmd');
      if ( $cmd == "" ) {
        $this->getFavorites();
      } else {
        switch ($cmd) {
          case 'add':
            $this->addFavorites();
            break;
          case 'edt':
            if ( $this->edtFavorites() ) {
              $this->delFavorites();
            }
            break;
        }
      }
    }
  }
  
  public function executeView(&$render)
  {
    $render->setTemplateName('message_favorites.html');
    $render->setAttribute('fuser', $this->favorites);
  }
}
?>
