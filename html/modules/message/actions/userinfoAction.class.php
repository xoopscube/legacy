<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
require_once XOOPS_MODULE_PATH.'/user/actions/UserInfoAction.class.php';

class UserinfoAction extends User_UserInfoAction
{
  protected $isError = false;
  protected $errMsg = "";
  protected $url = 'index.php';
  protected $mController = null;
  protected $mXoopsUser = null;
  
  public function __construct($controller)
  {
  	$this->mController = $controller;
  	$this->mXoopsUser =  $controller->mRoot->mContext->mXoopsUser;
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
  
  public function geterrMsg()
  {
    return $this->errMsg;
  }
  
  public function getisError()
  {
    return $this->isError;
  }
  
  function execute(&$controller = null, &$xoopsUser = null)
  {
  	if (!is_object($controller)) $controller = $this->mController;
  	if (!is_object($xoopsUser)) $xoopsUser = $this->mXoopsUser;
    $result = $this->getDefaultView($controller, $xoopsUser);
    if ($result == USER_FRAME_VIEW_ERROR) {
      $this->setErr(_MD_MESSAGE_SETTINGS_MSG19);
    }
    $language = $controller->mRoot->mContext->getXoopsConfig('language');
    require_once XOOPS_MODULE_PATH.'/user/language/'.$language . '/main.php';
  }

  public function executeView(&$render)
  {
    $render->setTemplateName('message_userinfo.html');
    $render->setAttribute('thisUser', $this->mObject);
    $render->setAttribute('rank', $this->mRankObject);
    $render->setAttribute('pmliteUrl', $this->mPmliteURL);

    $userSignature = $this->mObject->getShow('user_sig');
    
    $render->setAttribute('user_signature', $userSignature);
    $render->setAttribute('searchResults', $this->mSearchResults);
    
    $user_ownpage = (is_object($this->mXoopsUser) && $this->mXoopsUser->get('uid') == $this->mObject->get('uid'));
    $render->setAttribute('user_ownpage', $user_ownpage);
    
    $render->setAttribute('self_delete', $this->mSelfDelete);
    if ($user_ownpage && $this->mSelfDelete) {
      $render->setAttribute('enableSelfDelete', true);
    } else {
      $render->setAttribute('enableSelfDelete', false);
    }
    
    $definitions = array();
    $profile = null;
    XCube_DelegateUtils::call('Legacy_Profile.GetDefinition', new XCube_Ref($definitions), 'view');
    XCube_DelegateUtils::call('Legacy_Profile.GetProfile', new XCube_Ref($profile), $this->mObject->get('uid'));
    $render->setAttribute('definitions', $definitions);
    $render->setAttribute('data', $profile);
  }
}
?>
