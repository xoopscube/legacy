<?php
/**
 * @package user
 * @version $Id: LegacypageFunctions.class.php,v 1.6 2007/12/15 15:18:11 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
 
/***
 * @internal
 * This is static functions collection class for legacy pages access.
 */
class User_LegacypageFunctions
{
    static $passwordNeedsRehash;
    /***
     * @internal
     * The process for userinfo.php. This process doesn't execute anything
     * directly. Forward to the controller of the user module.
     */
    public static function userinfo()
    {
        //
        // Boot the action frame of the user module directly.
        //
        $root =& XCube_Root::getSingleton();
        $root->mController->executeHeader();
        
        $root->mController->setupModuleContext('user');
        $root->mLanguageManager->loadModuleMessageCatalog('user');
        
        require_once XOOPS_MODULE_PATH . "/user/class/ActionFrame.class.php";

        $moduleRunner = new User_ActionFrame(false);
        $moduleRunner->setActionName("UserInfo");

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();
        
        $root->mController->executeView();
    }
    
    /***
     * @internal
     * The process for edituser.php. This process doesn't execute anything
     * directly. Forward to the controller of the user module.
     */
    public static function edituser()
    {
        $actionName = "EditUser";
        switch (xoops_getrequest('op')) {
            case 'avatarform':
            case 'avatarupload':
                $actionName = "AvatarEdit";
                break;
                
            case 'avatarchoose':
                $actionName = "AvatarSelect";
                break;
        }
        
        //
        // Boot the action frame of the user module directly.
        //
        $root =& XCube_Root::getSingleton();
        $root->mController->executeHeader();
        
        $root->mController->setupModuleContext('user');
        $root->mLanguageManager->loadModuleMessageCatalog('user');
        
        require_once XOOPS_MODULE_PATH . "/user/class/ActionFrame.class.php";

        $moduleRunner = new User_ActionFrame(false);
        $moduleRunner->setActionName($actionName);

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();
        
        $root->mController->executeView();
    }
    
    /***
     * @internal
     * The process for register.php. This process doesn't execute anything
     * directly. Forward to the controller of the user module.
     */
    public static function register()
    {
        $root =& XCube_Root::getSingleton();
        $xoopsUser =& $root->mContext->mXoopsUser;
        
        if (is_object($xoopsUser)) {
            $root->mController->executeForward(XOOPS_URL);
        }
        
        //
        // Boot the action frame of the user module directly.
        //
        $root->mController->executeHeader();

        $root->mController->setupModuleContext('user');
        $root->mLanguageManager->loadModuleMessageCatalog('user');
                
        require_once XOOPS_MODULE_PATH . "/user/class/ActionFrame.class.php";

        $actionName = "";
        $action = $root->mContext->mRequest->getRequest('action');
        if ($action != null && $action =="UserRegister") {
            $actionName = "UserRegister";
        } else {
            $actionName = $action != null ? "UserRegister_confirm" : "UserRegister";
        }

        $moduleRunner = new User_ActionFrame(false);
        $moduleRunner->setActionName($actionName);

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();
        
        $root->mController->executeView();
    }
    
    /***
     * @internal
     * The process for lostpass.php. This process doesn't execute anything
     * directly. If the current user is registered user, kick out to the top
     * page. Else, forward to the lost-pass page.
     */
    public static function lostpass()
    {
        $root =& XCube_Root::getSingleton();
        $xoopsUser =& $root->mContext->mXoopsUser;

        if (is_object($xoopsUser)) {
            $root->mController->executeForward(XOOPS_URL);
        }
        
        //
        // Boot the action frame of the user module directly.
        //
        $root->mController->executeHeader();

        $root->mController->setupModuleContext('user');
        $root->mLanguageManager->loadModuleMessageCatalog('user');
                
        require_once XOOPS_MODULE_PATH . "/user/class/ActionFrame.class.php";

        $root =& XCube_Root::getSingleton();

        $moduleRunner = new User_ActionFrame(false);
        $moduleRunner->setActionName("LostPass");

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();
        
        $root->mController->executeView();
    }

    /***
     * @internal
     * The process for user.php. This process doesn't execute anything directly.
     * Forward to the controller of the user module.
     */
    public static function user()
    {
        $root =& XCube_Root::getSingleton();
        $op = isset($_REQUEST['op']) ? trim(xoops_getrequest('op')) : "main";
        $xoopsUser =& $root->mContext->mXoopsUser;
        
        $actionName = "default";
        
        switch ($op) {
            case "login":
                $root->mController->checkLogin();
                return;
            
            case "logout":
                $root->mController->logout();
                return;
                
            case "main":
                if (is_object($xoopsUser)) {
                    $root->mController->executeForward(XOOPS_URL . "/userinfo.php?uid=" . $xoopsUser->get('uid'));
                }
                break;
                
            case "actv":
                $actionName = "UserActivate";
                break;
                
            case "delete":
                $actionName = "UserDelete";
                break;
        }
        
        //
        // Boot the action frame of the user module directly.
        //
        $root =& XCube_Root::getSingleton();
        $root->mController->executeHeader();
        
        $root->mController->setupModuleContext('user');
        $root->mLanguageManager->loadModuleMessageCatalog('user');
        
        require_once XOOPS_MODULE_PATH . "/user/class/ActionFrame.class.php";

        $moduleRunner = new User_ActionFrame(false);
        $moduleRunner->setActionName($actionName);

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->execute();
        
        $root->mController->executeView();
    }

    public static function checkLogin(&$xoopsUser)
    {
        if (is_object($xoopsUser)) {
            return;
        }

        $root = XCube_Root::getSingleton();
        $root->mLanguageManager->loadModuleMessageCatalog('user');

        $userHandler = xoops_getmodulehandler('users', 'user');
        
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('uname', xoops_getrequest('uname')));
        $userArr = $userHandler->getObjects($criteria);
        if (count($userArr) != 1) {
            return;
        }

        $pass = xoops_getrequest('pass');
        $hash = $userArr[0]->get('pass');
        if (! User_Utils::passwordVerify($pass, $hash)) {
            return;
        }
        
        if ($userArr[0]->get('level') == 0) {
            // TODO We should use message "_MD_USER_LANG_NOACTTPADM"
            return;
        }
        
        $handler =& xoops_gethandler('user');
        $user =& $handler->get($userArr[0]->get('uid'));
        
        if (is_callable(array($user, "getNumGroups"))) { // Compatible for replaced handler.
            if ($user->getNumGroups() == 0) {
                return;
            }
        } else {
            $groups = $user->getGroups();
            if (count($groups) == 0) {
                return;
            }
        }
        
        // auto re-hash
        if (User_Utils::passwordNeedsRehash($hash)) {
            $user->set('pass', User_Utils::encryptPassword($pass), true);
            if (!$handler->insert($user, true)) {
                // set $passwordNeedsRehash
                self::$passwordNeedsRehash = ture;
            }
        }

        $xoopsUser = $user;

        //
        // Regist to session
        //
        $root->mSession->regenerate();
        $_SESSION = array();
        $_SESSION['xoopsUserId'] = $xoopsUser->get('uid');
        $_SESSION['xoopsUserGroups'] = $xoopsUser->getGroups();
    }

    public static function checkLoginSuccess(&$xoopsUser)
    {
        if (is_object($xoopsUser)) {
            $handler = xoops_gethandler('user');
            $xoopsUser->set('last_login', time());
            
            $handler->insert($xoopsUser);

            if (self::$passwordNeedsRehash) {
                $url = XOOPS_URL . '/edituser.php';
                if (($redirect = xoops_getrequest('xoops_redirect')) && $redirect[0] === '/') {
                    $url .= '?xoops_redirect=' . rawurlencode($redirect);
                }
                $root = XCube_Root::getSingleton();
                $root->mController->executeRedirect($url, 5, _MD_USER_MESSAGE_REPASSWORD);
            }
        }
    }

    public static function logout(&$successFlag, $xoopsUser)
    {
        $root =& XCube_Root::getSingleton();
        $xoopsConfig = $root->mContext->mXoopsConfig;
        
        $root->mLanguageManager->loadModuleMessageCatalog('user');

        // Reset session
        $_SESSION = array();
        $root->mSession->destroy(true);

        // clear entry from online users table
        if (is_object($xoopsUser)) {
            $onlineHandler =& xoops_gethandler('online');
            $onlineHandler->destroy($xoopsUser->get('uid'));
        }
        
        $successFlag = true;
    }

    public static function misc()
    {
        if (xoops_getrequest('type') != 'online') {
            return;
        }
        
        require_once XOOPS_MODULE_PATH . "/user/class/ActionFrame.class.php";

        $root =& XCube_Root::getSingleton();
        $root->mController->setupModuleContext('user');
        
        $actionName = "MiscOnline";

        $moduleRunner = new User_ActionFrame(false);
        $moduleRunner->setActionName($actionName);

        $root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

        $root->mController->setDialogMode(true);

        $root->mController->execute();

        $root->mController->executeView();
    }
}
