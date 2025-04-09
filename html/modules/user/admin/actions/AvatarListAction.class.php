<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: AvatarListAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/AvatarFilterForm.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/AvatarListForm.class.php';

class User_AvatarListAction extends User_AbstractListAction
{

    public $mAvatarObjects = [];
    public $mActionForm = null;
    public $mpageArr = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 0];

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->mActionForm =new User_AvatarListForm();
        $this->mActionForm->prepare();

        // Since XCL 2.3.x @gigamaster added avatar info
        $this->mAvatarWidth = $moduleConfig['avatar_width'];
        $this->mAvatarHeight = $moduleConfig['avatar_height'];
        $this->mAvatarMaxfilesize = $moduleConfig['avatar_maxsize'];
    }


    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('avatar');
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
        $filter =new User_AvatarFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function _getBaseUrl()
    {
        return './index.php?action=AvatarList';
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('avatar_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('pageArr', $this->mpageArr);
        $render->setAttribute('filterForm', $this->mFilter);

        $avt_handler =& $this->_getHandler();
        $savatar_total = $avt_handler->getCount(new Criteria('avatar_type', 'S'));
        $cavatar_total = $avt_handler->getCount(new Criteria('avatar_type', 'C'));
        $render->setAttribute('savatarTotal', $savatar_total);
        $render->setAttribute('cavatarTotal', $cavatar_total);
        $render->setAttribute('avatarTotal', $savatar_total+$cavatar_total);

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
        $nameArr = $this->mActionForm->get('name');
        $avatarHandler =& xoops_getmodulehandler('avatar');
        //
        // Do mapping.
        //
        foreach (array_keys($nameArr) as $aid) {
            $avatar =& $avatarHandler->get($aid);
            if (is_object($avatar)) {
                $this->mAvatarObjects[$aid] =& $avatar;
            }
            unset($avatar);
        }

        return USER_FRAME_VIEW_INPUT;
    }

    public function _processSave(&$controller, &$xoopsUser)
    {
        $nameArr = $this->mActionForm->get('name');
        $avatarHandler =& xoops_getmodulehandler('avatar');

        foreach (array_keys($nameArr) as $aid) {
            $avatar =& $avatarHandler->get($aid);
            if (is_object($avatar)) {
                $olddata['name'] = $avatar->get('avatar_name');
                $olddata['display'] = $avatar->get('avatar_display');
                $olddata['weight'] = $avatar->get('avatar_weight');
                $newdata['name'] = $this->mActionForm->get('name', $aid);
                $newdata['display'] = $this->mActionForm->get('display', $aid);
                $newdata['weight'] = $this->mActionForm->get('weight', $aid);
                if (count(array_diff_assoc($olddata, $newdata)) > 0) {
                    $avatar->set('avatar_name', $this->mActionForm->get('name', $aid));
                    $avatar->set('avatar_display', $this->mActionForm->get('display', $aid));
                    $avatar->set('avatar_weight', $this->mActionForm->get('weight', $aid));
                    if (!$avatarHandler->insert($avatar)) {
                        return USER_FRAME_VIEW_ERROR;
                    }
                }//count if
            }//object if
        }//foreach

        $linkHandler =& xoops_getmodulehandler('avatar_user_link');

        foreach (array_keys($nameArr) as $aid) {
            if (1 == $this->mActionForm->get('delete', $aid)) {
                $avatar =& $avatarHandler->get($aid);
                if (is_object($avatar)) {
                    $criteria =new Criteria('avatar_id', $aid);
                    $linkArr =& $linkHandler->getObjects($criteria);

                    if ($avatarHandler->delete($avatar)) {
                        if ((is_countable($linkArr) ? count($linkArr) : 0) > 0) {
                            $userHandler =& xoops_gethandler('user');
                            foreach ($linkArr as $link) {
                                $user =& $userHandler->get($link->get('user_id'));
                                if (is_object($user)) {
                                    $user->set('user_avatar', 'blank.gif');
                                    $userHandler->insert($user);
                                }
                                unset($user);
                            }
                        }
                    } else {
                        return USER_FRAME_VIEW_ERROR;
                    }
                }
            }
        }
        return USER_FRAME_VIEW_SUCCESS;
    }

    /**
     * To support a template designer, this send the list of mid that actionForm kept.
     * @param $controller
     * @param $xoopsUser
     * @param $render
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('avatar_list_confirm.html');
        $render->setAttribute('avatarObjects', $this->mAvatarObjects);
        $render->setAttribute('actionForm', $this->mActionForm);

        //
        // To support a template designer, this send the list of mid that
        // actionForm kept.
        //
        $t_arr = $this->mActionForm->get('name');
        $render->setAttribute('aids', array_keys($t_arr));
    }


    // @todo @gigamaster Check change $render to $renderer
    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=AvatarList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeRedirect('./index.php?action=AvatarList', 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=AvatarList');
    }
}
