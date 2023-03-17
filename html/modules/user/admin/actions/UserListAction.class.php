<?php
/**
 * @package user
 * @version $Id: UserListAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/UserFilterForm.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/UserListForm.class.php';

class User_UserListAction extends User_AbstractListAction
{
    public $mUserObjects = [];
    public $mActionForm = null;
    public $mpageArr = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 0];
    private $mAvatarWidth;
    private $mAvatarHeight;
    private $mAvatarMaxfilesize;

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->mActionForm =new User_UserListForm();
        $this->mActionForm->prepare();
        // Since XCL 2.3.x @gigamaster added avatar info
        $this->mAvatarWidth = $moduleConfig['avatar_width'];
        $this->mAvatarHeight = $moduleConfig['avatar_height'];
        $this->mAvatarMaxfilesize = $moduleConfig['avatar_maxsize'];
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('users');
        return $handler;
    }

    public function &_getPageNavi()
    {
        $navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);

        $root =& XCube_Root::getSingleton();
        $perpage = $root->mContext->mRequest->getRequest($navi->mPrefix.'perpage');

        if (isset($perpage) && 0 == (int)$perpage) {
            $navi->setPerpage(0);
        }
        return $navi;
    }

    public function &_getFilterForm()
    {
        $filter =new User_UserFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function _getBaseUrl()
    {
        return './index.php?action=UserList';
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('user_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('filterForm', $this->mFilter);
        $render->setAttribute('pageArr', $this->mpageArr);

        $member_handler =& $this->_getHandler();
        $active_total = $member_handler->getCount(new Criteria('level', 0, '>'));
        $inactive_total = $member_handler->getCount(new Criteria('level', 0));
        $render->setAttribute('activeUserTotal', $active_total);
        $render->setAttribute('inactiveUserTotal', $inactive_total);
        $render->setAttribute('UserTotal', $active_total+$inactive_total);

        // Since XCL 2.3.x @gigamaster added avatar info
        $render->setAttribute('avatar_width', $this->mAvatarWidth);
        $render->setAttribute('avatar_height', $this->mAvatarHeight);
        $render->setAttribute('avatar_maxsize', $this->mAvatarMaxfilesize);
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
        if (null != $form_cancel) {
            return USER_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return $this->_processConfirm($controller, $xoopsUser);
        } else {
            return $this->_processSave($controller, $xoopsUser);
        }
    }
    
    public function _processConfirm(&$controller, &$xoopsUser)
    {
        $levelArr = $this->mActionForm->get('level');
        $userHandler =& xoops_getmodulehandler('users');
        //
        // Do mapping.
        //
        foreach (array_keys($levelArr) as $uid) {
            $user =& $userHandler->get($uid);
            if (is_object($user)) {
                $this->mUserObjects[$uid] =& $user;
            }
            unset($user);
        }

        return USER_FRAME_VIEW_INPUT;
    }

    public function _processSave(&$controller, &$xoopsUser)
    {
        $levelArr = $this->mActionForm->get('level');
        $userHandler =& xoops_gethandler('user');

        foreach (array_keys($levelArr) as $uid) {
            if (1 != $uid) {
                $user =& $userHandler->get($uid);
                if (is_object($user)) {
                    $olddata['level'] = $user->get('level');
                    $olddata['posts'] = $user->get('posts');
                    $newdata['level'] = $this->mActionForm->get('level', $uid);
                    $newdata['posts'] = $this->mActionForm->get('posts', $uid);
                    if (count(array_diff_assoc($olddata, $newdata)) > 0) {
                        $user->set('level', $this->mActionForm->get('level', $uid));
                        $user->set('posts', $this->mActionForm->get('posts', $uid));
                        if (!$userHandler->insert($user)) {
                            return USER_FRAME_VIEW_ERROR;
                        }
                    }//count if
                }//object if
            }//if
        }//foreach

        foreach (array_keys($levelArr) as $uid) {
            if ((1 == $this->mActionForm->get('delete', $uid)) && (1 != $uid)) {
                $user =& $userHandler->get($uid);
                if (is_object($user)) {
                    XCube_DelegateUtils::call('Legacy.Admin.Event.UserDelete', new XCube_Ref($user));
                    $memberhandler =& xoops_gethandler('member');
                    if ($memberhandler->delete($user)) {
                        XCube_DelegateUtils::call('Legacy.Admin.Event.UserDelete.Success', new XCube_Ref($user));
                    } else {
                        XCube_DelegateUtils::call('Legacy.Admin.Event.UserDelete.Fail', new XCube_Ref($user));
                        return USER_FRAME_VIEW_ERROR;
                    }
                }//object
            }//delete == 1
        }//foreach

        return USER_FRAME_VIEW_SUCCESS;
    }

    /**
     * To support a template writer, this sends the list of mid that actionForm kept.
     * @param $controller
     * @param $xoopsUser
     * @param $render
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('user_list_confirm.html');
        $render->setAttribute('userObjects', $this->mUserObjects);
        $render->setAttribute('actionForm', $this->mActionForm);
        
        //
        // To support a template writer, this sends the list of mid that
        // actionForm kept.
        //
        $t_arr = $this->mActionForm->get('level');
        $render->setAttribute('uids', array_keys($t_arr));
    }

// @todo @gigamaster Check change $render to $renderer
    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=UserList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeRedirect('./index.php?action=UserList', 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=UserList');
    }
}
