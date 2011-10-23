<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class deleteAction extends AbstractAction
{
  private $mActionForm;
  private $inout = 'inbox';
  
  public function __construct()
  {
    parent::__construct();
  }
  
  public function execute()
  {
    if ( $this->root->mContext->mRequest->getRequest('inout') == 'in' ) {
      $this->inout = 'inbox';
    } else {
      $this->inout = 'outbox';
    }
    
    $boxid = intval($this->root->mContext->mRequest->getRequest($this->inout));
    $modHand = xoops_getmodulehandler($this->inout, _MY_DIRNAME);
    $modObj = $modHand->get($boxid);
    if ( !is_object($modObj) ) {
      $this->setErr(_MD_MESSAGE_ACTIONMSG1);
      return;
    }
    
    if ( $modObj->get('uid') != $this->root->mContext->mXoopsUser->get('uid') ) {
      $this->setErr(_MD_MESSAGE_ACTIONMSG2);
      return;
    }
    if ( $modHand->delete($modObj) ) {
      $this->setErr(_MD_MESSAGE_ACTIONMSG3);
    } else {
      $this->setErr(_MD_MESSAGE_ACTIONMSG4);
    }
  }
  
  public function executeView(&$render)
  {
  }
}
?>