<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: AvatarSelectAction.class.php,v 1.2 2007/06/07 05:27:01 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/forms/AvatarSelectForm.class.php';

/***
 * @internal
 *  When the request is POST, this class fetches avatar_id and set it to user
 * object. This class always kicks out GET request.
 * 
 * @see User_AvatarSelectForm
 */
class User_AvatarSelectAction extends User_AbstractEditAction
{
    public $mOldAvatar = null;
    
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        parent::prepare($controller, $xoopsUser, $moduleConfig);

        $handler =& xoops_getmodulehandler('avatar', 'user');
        $criteria =new Criteria('avatar_file', $xoopsUser->get('user_avatar'));
        $avatarArr =& $handler->getObjects($criteria);
        if ((is_countable($avatarArr) ? count($avatarArr) : 0) > 0) {
            $this->mOldAvatar =& $avatarArr[0];
        }
    }
    
    public function _getId()
    {
        return isset($_REQUEST['uid']) ? (int)xoops_getrequest('uid') : 0;
    }
    
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('users', 'user');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new User_AvatarSelectForm();
        $this->mActionForm->prepare();
    }
    
    /***
     *  Return false.
     *  If a user requests dummy uid, kick out him!
     */
    public function isEnableCreate()
    {
        return false;
    }

    /***
     *  Return true.
     *  This action should not be used by a guest user.
     */
    public function isSecure()
    {
        return true;
    }

    /***
     *  Check whether a current user can access this action.
     * 1) A specified user has to exist.
     * 2) A current user has to equal the specified user, or a current user has
     *    to be a administrator.
     * @param $controller
     * @param $xoopsUser
     * @param $moduleConfig
     * @return bool
     */
    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        if (!is_object($this->mObject)) {
            return false;
        }

        if ($controller->mRoot->mContext->mUser->isInRole('Module.user.Admin')) {
            return true;
        } elseif ($this->mObject->get('uid') == $xoopsUser->get('uid')) {
            return ($this->mObject->get('posts') >= $this->_mMinPost);
        }
        
        return false;
    }

    public function _doExecute()
    {
        if ($this->mObjectHandler->insert($this->mObject)) {
            $avatarHandler =& xoops_getmodulehandler('avatar', 'user');

            //
            // If old avatar is a cutom avatar, delete it.
            //
            if (null != $this->mOldAvatar && 'C' == $this->mOldAvatar->get('avatar_type')) {
                $avatarHandler->delete($this->mOldAvatar);
            }
            
            //
            // Delete all of links about this user from avatar_user_link.
            //
            $linkHandler =& xoops_getmodulehandler('avatar_user_link', 'user');
            $linkHandler->deleteAllByUser($this->mObject);
            
            //
            // Insert new link.
            //
            $criteria =new Criteria('avatar_file', $this->mObject->get('user_avatar'));
            $avatarArr =& $avatarHandler->getObjects($criteria);
            if (is_array($avatarArr) && is_object($avatarArr[0])) {
                $link =& $linkHandler->create();
                $link->set('avatar_id', $avatarArr[0]->get('avatar_id'));
                $link->set('user_id', $this->mObject->get('uid'));
                $linkHandler->insert($link);
            }
            
            return true;
        }
    }

    /***
     * This action always kicks out GET request.
     * @param $controller
     * @param $xoopsUser
     */
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $controller->executeForward(XOOPS_URL . '/edituser.php?op=avatarform&uid=' . $this->mObject->get('uid'));
    }
    
    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderSystem)
    {
        $controller->executeForward(XOOPS_URL . '/userinfo.php?op=avatarform&uid=' . $this->mActionForm->get('uid'));
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderSystem)
    {
        $controller->executeRedirect(XOOPS_URL . '/userinfo.php?op=avatarform&uid=' . $this->mActionForm->get('uid'), 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }
}
