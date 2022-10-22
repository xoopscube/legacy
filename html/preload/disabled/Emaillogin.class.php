<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit;
}

class Emaillogin extends XCube_ActionFilter
{
    public function preFilter()
    {
        $this->mRoot->mDelegateManager->add('Site.CheckLogin', 'Emaillogin::checkLogin', XCUBE_DELEGATE_PRIORITY_FIRST);
    }
  
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->delete('Site.CheckLogin', 'User_LegacypageFunctions::checkLogin');
    }
  
    public function checkLogin(&$xoopsUser)
    {
        $root =& XCube_Root::getSingleton();
        if ($root->mContext->mUser->isInRole('Site.RegisteredUser')) {
            return;
        }
    
        $root->mLanguageManager->loadModuleMessageCatalog('user');

        $userHandler =& xoops_getmodulehandler('users', 'user');
    
        $criteria = new CriteriaCompo();
        if ('' != xoops_getrequest('uname') && false !== strpos(xoops_getrequest('uname'), '@')) {
            $criteria->add(new Criteria('email', xoops_getrequest('uname')));
        } else {
            $criteria->add(new Criteria('uname', xoops_getrequest('uname')));    // use for both e-mail or uname logiin
//	$criteria->add(new Criteria('uname',''));				// use for only e-mail logiin
        }
        $criteria->add(new Criteria('pass', md5(xoops_getrequest('pass'))));
    
        $userArr =& $userHandler->getObjects($criteria);
        if (1 != count($userArr)) {
            return;
        }
        if (0 == $userArr[0]->get('level')) {
            return;
        }
    
        $handler =& xoops_gethandler('user');
        $user =& $handler->get($userArr[0]->get('uid'));
        $xoopsUser = $user;
  
        require_once XOOPS_ROOT_PATH . '/include/session.php';
        xoops_session_regenerate();
        $_SESSION = [];
        $_SESSION['xoopsUserId'] = $xoopsUser->get('uid');
        $_SESSION['xoopsUserGroups'] = $xoopsUser->getGroups();
    }
}
