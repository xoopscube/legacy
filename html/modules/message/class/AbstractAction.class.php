<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
define('_USE_XOOPSMAILER', false);

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
  
  protected function getMailer()
  {
    $classname = 'XoopsMailer';
    if ( _USE_XOOPSMAILER == true ) {
      require_once XOOPS_ROOT_PATH.'/class/xoopsmailer.php';
      if ( is_file(XOOPS_ROOT_PATH.'/language/'.$this->root->mLanguageManager->mLanguageName.'/xoopsmailerlocal.php') ) {
        require_once XOOPS_ROOT_PATH.'/language/'.$this->root->mLanguageManager->mLanguageName.'/xoopsmailerlocal.php';
        if ( XC_CLASS_EXISTS('XoopsMailerLocal') ) {
          $classname = 'XoopsMailerLocal';
        }
      }
    } else {
      require_once XOOPS_ROOT_PATH.'/class/mail/phpmailer/class.phpmailer.php';
      require_once _MY_MODULE_PATH.'class/MyMailer.class.php';
      $classname = 'My_Mailer';
    }
    return new $classname();
  }
  
  abstract public function execute();
  abstract public function executeView(&$render);
}
?>
