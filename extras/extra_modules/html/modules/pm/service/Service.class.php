<?php
/**
 * @package Pm
 * @version $Id: Service.class.php,v 1.1 2007/05/15 02:35:35 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Sample class
 */
class Pm_Service extends XCube_Service
{
    public $mServiceName = "Pm_Service";
    public $mNameSpace = "Pm";
    public $mClassName = "Pm_Service";
    
    public function prepare()
    {
        $this->addFunction(S_PUBLIC_FUNC('string getPmInboxUrl(int uid)'));
        $this->addFunction(S_PUBLIC_FUNC('string getPmliteUrl(int fromUid, int toUid)'));
        $this->addFunction(S_PUBLIC_FUNC('int getCountUnreadPM(int uid)'));
    }
    
    public function getPmInboxUrl()
    {
        $root =& XCube_Root::getSingleton();
        $uid = $root->mContext->mRequest->getRequest('uid');
        
        if ($uid > 0) {
            return XOOPS_URL . "/viewpmsg.php";
        }
        
        return "";
    }
    
    public function getPmliteUrl()
    {
        $root =& XCube_Root::getSingleton();
        
        $fromUid = $root->mContext->mRequest->getRequest('fromUid');
        $toUid = $root->mContext->mRequest->getRequest('toUid');

        if ($fromUid > 0 && $toUid > 0) {
            return XOOPS_URL . "/pmlite.php?send2=1&to_userid=${toUid}";
        }
        
        return "";
    }
    
    public function getCountUnreadPM()
    {
        $root =& XCube_Root::getSingleton();
        $uid = $root->mContext->mRequest->getRequest('uid');
        
        if ($uid > 0) {
            $handler =& xoops_gethandler('privmessage');
            return $handler->getCountUnreadByFromUid($uid);
        }
        
        return 0;
    }
}
