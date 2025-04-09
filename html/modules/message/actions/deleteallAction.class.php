<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.5.0
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2024 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class deleteallAction extends AbstractAction
{
    private string $inout = 'inbox';
  
    public function __construct()
    {
        parent::__construct();
    }
  
    public function execute()
    {
        if ('in' == $this->root->mContext->mRequest->getRequest('inout')) {
            $this->inout = 'inbox';
            $this->setUrl('index.php?action=index');
        } else {
            $this->inout = 'outbox';
            $this->setUrl('index.php?action=send');
        }
    
        $delid = $this->root->mContext->mRequest->getRequest('delmsg');
        if (!is_array($delid) || 0 == count($delid)) {
            $this->setErr(_MD_MESSAGE_DELETEMSG2);
            return;
        }
    
        $modHand = xoops_getmodulehandler($this->inout);
    
        foreach ($delid as $boxid) {
            $modObj = $modHand->get((int)$boxid);
            if (!is_object($modObj)) {
                $this->setErr(_MD_MESSAGE_ACTIONMSG1);
                return;
            }
      
            if ($modObj->get('uid') != $this->root->mContext->mXoopsUser->get('uid')) {
                $this->setErr(_MD_MESSAGE_ACTIONMSG2);
                return;
            }
            if ($modHand->delete($modObj)) {
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
