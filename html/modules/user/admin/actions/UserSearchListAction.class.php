<?php
/**
 * @package user
 * @version $Id: UserSearchListAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/UserSearchFilterForm.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/UserSearchListForm.class.php';

// @todo @gigamaster !Fix WARNING:
// Declaration of User_UserSearchListAction::prepare(&$controller, &$xoopsUser)
// should be compatible with
// User_Action::prepare(&$controller, &$xoopsUser, $moduleConfig)
class User_UserSearchListAction extends User_AbstractListAction
{
    public $mUserObjects = [];
    public $mActionForm = null;
    public $mpageArr = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0];
    public $mExtraURL = '';

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->mActionForm =new User_UserSearchListForm();
        $this->mActionForm->prepare();
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('users_search');
        return $handler;
    }

    public function &_getFilterForm()
    {
        $filter =new User_UserSearchFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function &_getPageNavi()
    {
        $navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);
        if (isset($_REQUEST[$navi->mPrefix.'perpage']) && 0 == (int)$_REQUEST[$navi->mPrefix . 'perpage']) {
            $navi->setPerpage(0);
        }
        return $navi;
    }

    public function _getBaseUrl()
    {
        return './index.php?action=UserSearchList';
    }

    public function execute(&$controller, &$xoopsUser)
    {
        //in case of result of user-search
        if (!isset($_REQUEST['batchjob'])) {
            return $this->getDefaultView($controller, $xoopsUser);
        }

        //To return user to proper-url with search condition
        $this->mFilter =& $this->_getFilterForm();
        $this->mFilter->fetch();
        //
        if (null != xoops_getrequest('_form_control_cancel')) {
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

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $controller->mRoot->mDelegateManager->add('Legacy.Event.Explaceholder.Get.UserPagenaviOtherUrl', 'User_UserSearchListAction::renderOtherUrlControl');
        $controller->mRoot->mDelegateManager->add('Legacy.Event.Explaceholder.Get.UserSearchPagenaviHidden', 'User_UserSearchListAction::renderHiddenControl');

        $render->setTemplateName('user_search_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('filterForm', $this->mFilter);
        $render->setAttribute('pageArr', $this->mpageArr);
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
     * To support a template writer, this send the list of mid that actionForm kept.
     * @param $controller
     * @param $xoopsUser
     * @param $render
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('user_search_list_confirm.html');
        $render->setAttribute('userObjects', $this->mUserObjects);
        $render->setAttribute('actionForm', $this->mActionForm);

        //
        // To support a template writer, this send the list of mid that
        // actionForm kept.
        //
        $t_arr = $this->mActionForm->get('level');
        $render->setAttribute('uids', array_keys($t_arr));
        //To return user to proper-url with search condition
        $controller->mRoot->mDelegateManager->add('Legacy.Event.Explaceholder.Get.UserSearchPagenaviHidden', 'User_UserSearchListAction::renderHiddenControl');
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
    }

// @todo @gigamaster Check change $render to $renderer
    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=UserSearchList'.$this->getExtraURL());
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeRedirect('./index.php?action=UserSearchList'.$this->getExtraURL(), 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=UserSearchList'.$this->getExtraURL());
    }


    public function getExtraURL()
    {
        $extraurl = '';
        if (count($this->mFilter->mNavi->mExtra) > 0) {
            $t_arr = [];
            foreach ($this->mFilter->mNavi->mExtra as $key => $value) {
                $t_arr[] = $key . '=' . urlencode($value);
            }
            $extraurl = '&' . implode('&', $t_arr);
        }
        return $extraurl;
    }

    public static function renderOtherUrlControl(&$buf, $params)
    {
        if (isset($params['pagenavi']) && is_object($params['pagenavi'])) {
            $navi =& $params['pagenavi'];
            $url = $params['url'];
            if (count($navi->mExtra) > 0) {
                $t_arr = [];

                foreach ($navi->mExtra as $key => $value) {
                    $t_arr[] = $key . '=' . urlencode($value);
                }

                if (0 == count($t_arr)) {
                    $buf = $url;
                    return;
                }

                if (false !== strpos($url, '?')) {
                    $buf = $url . '&amp;' . implode('&amp;', $t_arr);
                } else {
                    $buf = $url . '?' . implode('&amp;', $t_arr);
                }
            }
        }
    }

    public static function renderHiddenControl(&$buf, $params)
    {
        if (isset($params['pagenavi']) && is_object($params['pagenavi'])) {
            $navi =& $params['pagenavi'];
            $mask = isset($params['mask']) ? explode('+', $params['mask']) : [];
            foreach ($navi->mExtra as $key => $value) {
                if (!in_array($key, $mask)) {
                    $value = htmlspecialchars($value, ENT_QUOTES);
                    $buf .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\" />";
                }
            }
        }
    }
}
