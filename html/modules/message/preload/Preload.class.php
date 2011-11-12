<?php
if (!defined('XOOPS_ROOT_PATH')) die();

class Message_Preload extends XCube_ActionFilter
{
  public function postFilter()
  {
    $confhand = xoops_gethandler('config');
    $conf = $confhand->getConfigsByDirname('message');
    if ($this->mRoot->mContext->mUser->isInRole('Site.RegisteredUser')) {
      require_once XOOPS_MODULE_PATH.'/message/service/Service.class.php';
      $service = new Message_Service();
      $service->prepare();
      
      $this->mRoot->mServiceManager->addService('privateMessage', $service);
      if ( $conf['newalert'] == 1 ) {
        $this->mRoot->mDelegateManager->add('Myfriend.NewAlert', 'Message_Preload::getNewMessage');
      }
      $this->mRoot->mDelegateManager->add('Legacypage.Viewpmsg.Access', 'Message_Preload::accessToReadpmsg');
      $this->mRoot->mDelegateManager->add('Legacypage.Readpmsg.Access', 'Message_Preload::accessToReadpmsg');
      $this->mRoot->mDelegateManager->add('Legacypage.Pmlite.Access', 'Message_Preload::accessToReadpmsg');
    }
    /*
    if ( $conf['userinfo'] == 1 ) {
      $this->mRoot->mDelegateManager->add('User_ActionFrame.CreateAction', 'Message_Preload::_createAction', XCUBE_DELEGATE_PRIORITY_FIRST);
    }
    */
    $this->mRoot->mDelegateManager->add('Legacy.Event.GetHandler', 'Message_Preload::makeHandler');
    $this->mRoot->mDelegateManager->add('Legacy_RenderSystem.SetupXoopsTpl', 'Message_Preload::addFilter');
  }
  
  public static function addFilter(&$xoopsTpl)
  {
    $xoopsTpl->plugins_dir[] = XOOPS_MODULE_PATH.'/message/smarty';
  }
  
  public static function getNewMessage(&$arrays)
  {
    $root = XCube_Root::getSingleton();
    if ($root->mContext->mUser->isInRole('Site.RegisteredUser')) {
      $uid = $root->mContext->mXoopsUser->get('uid');
      $modHand = xoops_getmodulehandler('inbox', 'message');
      $num = $modHand->getCountUnreadByFromUid($uid);
      if ( $num > 0 ) {
        $root->mLanguageManager->loadModuleMessageCatalog('message');
        $arrays[] = array(
          'url' => XOOPS_MODULE_URL.'/message/index.php',
          'title' => XCube_Utils::formatString(_MD_MESSAGE_NEWMESSAGE, $num)
        );
      }
    }
  }
  
  public static function _createAction(&$actionFrame)
  {
    if (is_object($actionFrame->mAction)) {
      return;
    }

    switch (ucfirst($actionFrame->mActionName)) {
      case 'UserInfo':
        require XOOPS_MODULE_PATH.'/message/actions/userinfoAction.class.php';
        $actionFrame->mAction = new UserinfoAction();
        break;
    }
  }
  
  public static function accessToReadpmsg()
  {
    $root = XCube_Root::getSingleton();
    $root->mController->executeForward(XOOPS_MODULE_URL.'/message/');
  }
  
  public static function makeHandler(&$handler, $name, $optional)
  {
    if ( $name == 'privmessage' ) {
      $handler = xoops_getmodulehandler('inbox', 'message', $optional);
    }
  }
}
?>
