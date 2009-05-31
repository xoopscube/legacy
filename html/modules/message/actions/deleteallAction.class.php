<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class deleteallAction extends AbstractAction
{
  private $inout = 'inbox';
  
  public function __construct()
  {
    parent::__construct();
  }
  
  public function execute()
  {
    if ( $this->root->mContext->mRequest->getRequest('inout') == 'in' ) {
      $this->inout = 'inbox';
      $this->setUrl('index.php?action=index');
    } else {
      $this->inout = 'outbox';
      $this->setUrl('index.php?action=send');
    }
    
    $delid = $this->root->mContext->mRequest->getRequest('delmsg');
    if ( !is_array($delid) || count($delid) == 0 ) {
      $this->setErr(_MD_MESSAGE_DELETEMSG2);
      return;
    }
    
    $modHand = xoops_getmodulehandler($this->inout);
    
    foreach ( $delid as $boxid ) {
      $modObj = $modHand->get(intval($boxid));
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
        return;
      }
    }
  }
  
  public function executeView(&$render)
  {
  }
}
?>
