<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class UserMailjobObject extends XoopsSimpleObject
{
    public $mUsers = array();
    public $_mUsersLoadedFlag = false;
    public $mUserCount = 0;
    public $_mUserCountLoadedFlag = false;
    
    /**
     * @var XCube_Delegate
     */
    public $mGetReplaceTitle = null;

    /**
     * @var XCube_Delegate
     */
    public $mGetReplaceBody = null;

    /**
     * @var XCube_Delegate
     */
    public $mSend = null;
        
    // !Fix deprecated constructor for PHP 7.x
    public function __construct()
    // public function UserMailjobObject()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
        } else {
            $this->initVar('mailjob_id', XOBJ_DTYPE_INT, '', false);
            $this->initVar('title', XOBJ_DTYPE_STRING, '', true, 255);
            $this->initVar('body', XOBJ_DTYPE_TEXT, '', true);
            $this->initVar('from_name', XOBJ_DTYPE_STRING, '', false, 255);
            $this->initVar('from_email', XOBJ_DTYPE_STRING, '', false, 255);
            $this->initVar('is_pm', XOBJ_DTYPE_BOOL, '0', true);
            $this->initVar('is_mail', XOBJ_DTYPE_BOOL, '0', true);
            $this->initVar('create_unixtime', XOBJ_DTYPE_INT, time(), true);
            $initVars=$this->mVars;
        }
        
        $this->mGetReplaceTitle =new XCube_Delegate();
        $this->mGetReplaceTitle->register('UserMailjobObject.GetReplaceTitle');
        
        $this->mGetReplaceBody =new XCube_Delegate();
        $this->mGetReplaceBody->register('UserMailjobObject.GetReplaceBody');
        
        $this->mSend =new XCube_Delegate();
        $this->mSend->register('UserMailjobObject.Send');
    }

    /**
     * Count the number of target users, and set it to mUserCount. 
     */
    public function loadUserCount()
    {
        if (!$this->_mUserCountLoadedFlag) {
            $handler =& xoops_getmodulehandler('mailjob_link', 'user');
            $this->mUserCount = $handler->getCount(new Criteria('mailjob_id', $this->get('mailjob_id')));
            $this->_mUserCountLoadedFlag = true;
        }
    }
    
    /**
     * Load the uid list of target users.
     */
    public function loadUser()
    {
        if (!$this->_mUsersLoadedFlag) {
            $handler =& xoops_getmodulehandler('mailjob_link', 'user');
            $this->mUsers =& $handler->getObjects(new Criteria('mailjob_id', $this->get('mailjob_id')));
            $this->_mUsersLoadedFlag = true;
        }
    }
    
    /**
     * Gets users who this mailjob will send mail to, with $retry number.
     * @param int $retry
     */
    public function &getUsers($retry)
    {
        $handler =& xoops_getmodulehandler('mailjob_link', 'user');
        
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('mailjob_id', $this->get('mailjob_id')));
        $criteria->add(new Criteria('retry', $retry));
        
        $arr =& $handler->getObjects($criteria);
        
        return $arr;
    }
    
    /**
     * Gets count of users who this mailjob will send mail to.
     * @return int count of users 
     */
    public function getUserCount()
    {
        $this->loadUserCount();
        return $this->mUserCount;
    }
    
    public function send($from_user)
    {
        $root =& XCube_Root::getSingleton();
        
        $userArr =& $this->getUsers($this->getCurrentRetry());
        $handler =& xoops_getmodulehandler('mailjob_link', 'user');

        $userHandler =& xoops_gethandler('user');
        
        foreach (array_keys($userArr) as $key) {
            $to_user =& $userHandler->get($userArr[$key]->get('uid'));
            
            $userArr[$key]->set('message', '');
            
            if (is_object($to_user)) {
                $this->mSend->call(new XCube_Ref($userArr[$key]), new XCube_Ref($this), $to_user, $from_user);
            } else {
                $userArr[$key]->set('message', 'This user does not exist.');
            }
            
            if ($userArr[$key]->get('message') == '') {
                $handler->delete($userArr[$key]);
            } else {
                $userArr[$key]->set('retry', $userArr[$key]->get('retry') + 1);
                $handler->insert($userArr[$key]);
            }
        }
    }
    
    public function getReplaceTitle(&$to_user, &$from_user)
    {
        return $this->get('title');
    }
    
    public function getReplaceBody(&$to_user, &$from_user)
    {
        $t_body = $this->get('body');
        
        //
        // TODO {X_UACTLINK}
        //
        $t_body = str_replace('{X_UID}', $to_user->get('uid'), $t_body);
        $t_body = str_replace('{X_UNAME}', $to_user->get('uname'), $t_body);
        $t_body = str_replace('{X_UEMAIL}', $to_user->get('email'), $t_body);

        $this->mGetReplaceBody->call(new XCube_Ref($t_body), $to_user, $from_user);
        
        return $t_body;
    }
    
    public function getCurrentRetry()
    {
        $handler =& xoops_getmodulehandler('mailjob_link', 'user');
        return $handler->getCurrentRetry($this->get('mailjob_id'));
    }
}

class UserMailjobHandler extends XoopsObjectGenericHandler
{
    public $mTable = "user_mailjob";
    public $mPrimary = "mailjob_id";
    public $mClass = "UserMailjobObject";
}
