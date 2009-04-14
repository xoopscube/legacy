<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

abstract class AbstractAction
{
  protected $isError = false;
  protected $errMsg = "";
  protected $root;
  protected $url = 'index.php';
  protected $unamelink = array();
  
  public function __construct()
  {
    $this->root = XCube_Root::getSingleton();
    //FRONT
    if (defined('_FRONTCONTROLLER')) {
      $this->url = XOOPS_URL.'/index.php?moddir='._MY_DIRNAME;
    }
  }
  
  protected function setUrl($url)
  {
    $this->url = $url;
  }
  
  public function getUrl()
  {
    return $this->url;
  }
  
  protected function setErr($msg)
  {
    $this->isError = true;
    $this->errMsg = $msg;
  }
  
  public function getisError()
  {
    return $this->isError;
  }
  
  public function geterrMsg()
  {
    return $this->errMsg;
  }
  
  public function chk_use($uid = 0)
  {
    $modObj = $this->getSettings($uid);
    if ( $modObj->get('usepm') == 1 ) {
      return true;
    } else {
      return false;
    }
  }
  
  public function getSettings($uid = 0)
  {
    if ( $uid == 0 ) {
      $uid = $this->root->mContext->mXoopsUser->get('uid');
    }
    
    $modHand = xoops_getmodulehandler('settings', _MY_DIRNAME);
    $modObj = $modHand->get($uid);
    if ( !is_object($modObj) ) {
      $modObj = $modHand->create();
      $modObj->set('uid', $uid);
    }
    return $modObj;
  }
  
  public function getLinkUnameFromId($uid, $uname = "")
  {
    $uid = intval($uid);
    
    if ($uid > 0) {
      if ( isset($this->unamelink[$uid]) ) {
        return $this->unamelink[$uid];
      }
      $mhandler = xoops_gethandler('member');
      $user = $mhandler->getUser($uid);
      if (is_object($user)) {
        $this->unamelink[$uid] = '<a href="'.XOOPS_URL.'/userinfo.php?uid='.$uid.'">'. $user->getVar('uname').'</a>';
        return $this->unamelink[$uid];
      }
      return $this->root->mContext->mXoopsConfig['anonymous'];
    } else {
      return $uname;
    }
  }
  
  abstract public function execute();
  abstract public function executeView(&$render);
}
?>