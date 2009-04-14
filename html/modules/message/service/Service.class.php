<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class Message_Service extends XCube_Service
{
  public $mServiceName = 'Message_Service';
  public $mNameSpace = 'Message';
  public $mClassName = 'Message_Service';
  
  public function prepare()
  {
    $this->addFunction(S_PUBLIC_FUNC('string getPmInboxUrl(int uid)'));
    $this->addFunction(S_PUBLIC_FUNC('string getPmliteUrl(int fromUid, int toUid)'));
    $this->addFunction(S_PUBLIC_FUNC('int getCountUnreadPM(int uid)'));
  }
  
  public function getPmInboxUrl()
  {
    $root = XCube_Root::getSingleton();
    $uid = $root->mContext->mRequest->getRequest('uid');
    
    if ($uid > 0) {
      return XOOPS_URL.'/modules/message/index.php';
    }
    
    return "";
  }
  
  public function getPmliteUrl()
  {
    $root = XCube_Root::getSingleton();
    
    $fromUid = $root->mContext->mRequest->getRequest('fromUid');
    $toUid = $root->mContext->mRequest->getRequest('toUid');

    if ($fromUid > 0 && $toUid > 0) {
      return XOOPS_URL.'/modules/message/index.php?action=new&to_userid='.$toUid;
    }
    
    return "";
  }
  
  public function getCountUnreadPM()
  {
    $root = XCube_Root::getSingleton();
    $uid = $root->mContext->mRequest->getRequest('uid');
    
    if ($uid > 0) {
      $modHand = xoops_getmodulehandler('inbox', 'message');
      return $modHand->getCountUnreadByFromUid($uid);
    }
    
    return 0;
  }
}
?>