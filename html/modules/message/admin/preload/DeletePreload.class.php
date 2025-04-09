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
    die();
}

class message_DeletePreload extends XCube_ActionFilter
{
    public function postFilter()
    {
        $this->mRoot->mDelegateManager->add('Legacypage.Admin.SystemCheck', 'message_DeletePreload::deleteMessage');
    }
  
    public static function deleteMessage()
    {
        $confHand = xoops_gethandler('config');
        $modconf = $confHand->getConfigsByDirname('message');

        $inHand = xoops_getmodulehandler('inbox', 'message');
        $inHand->deleteDays($modconf['savedays'], $modconf['dletype']);
    
        $outHand = xoops_getmodulehandler('outbox', 'message');
        $outHand->deleteDays($modconf['savedays']);
    }
}
