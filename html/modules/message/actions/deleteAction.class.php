<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.3.3
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2023 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class deleteAction extends AbstractAction
{
    private $mActionForm;
    private string $inout = 'inbox';
  
    public function __construct()
    {
        parent::__construct();
    }
  
    public function execute()
    {
        if ('in' == $this->root->mContext->mRequest->getRequest('inout')) {
            $this->inout = 'inbox';
        } else {
            $this->inout = 'outbox';
        }
    
        $boxid = (int)$this->root->mContext->mRequest->getRequest($this->inout);
        $modHand = xoops_getmodulehandler($this->inout, _MY_DIRNAME);
        $modObj = $modHand->get($boxid);
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
        }
    }
  
    public function executeView(&$render)
    {
    }
}
