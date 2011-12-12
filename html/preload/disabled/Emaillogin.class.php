<?php
if (!defined('XOOPS_ROOT_PATH')) exit;

class Emaillogin extends XCube_ActionFilter
{
  function preFilter()
  {
    $this->mRoot->mDelegateManager->add('Site.CheckLogin', 'Emaillogin::checkLogin', XCUBE_DELEGATE_PRIORITY_FIRST);
  }
  
  function preBlockFilter()
  {
    $this->mRoot->mDelegateManager->delete('Site.CheckLogin', 'User_LegacypageFunctions::checkLogin');
  }
  
  function checkLogin(&$xoopsUser)
  {
    $root =& XCube_Root::getSingleton();
    if ($root->mContext->mUser->isInRole('Site.RegisteredUser')) {
      return;
    }
    
    $root->mLanguageManager->loadModuleMessageCatalog('user');

    $userHandler =& xoops_getmodulehandler('users', 'user');
    
    $criteria = new CriteriaCompo();
    if ( xoops_getrequest('uname') != "" && strpos(xoops_getrequest('uname'), '@') !== false ) {
      $criteria->add(new Criteria('email', xoops_getrequest('uname')));
    } else {
	$criteria->add(new Criteria('uname', xoops_getrequest('uname')));	// use for both e-mail or uname logiin
//	$criteria->add(new Criteria('uname',''));				// use for only e-mail logiin
    }
    $criteria->add(new Criteria('pass', md5(xoops_getrequest('pass'))));
    
    $userArr =& $userHandler->getObjects($criteria);
    if (count($userArr) != 1) {
      return;
    }
    if ($userArr[0]->get('level') == 0) {
      return;
    }
    
    $handler =& xoops_gethandler('user');
    $user =& $handler->get($userArr[0]->get('uid'));
    $xoopsUser = $user;
  
    require_once XOOPS_ROOT_PATH . '/include/session.php';
    xoops_session_regenerate();
    $_SESSION = array();
    $_SESSION['xoopsUserId'] = $xoopsUser->get('uid');
    $_SESSION['xoopsUserGroups'] = $xoopsUser->getGroups();
  }
}
?>
