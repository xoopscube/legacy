<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/RanksFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/RanksListForm.class.php";

class User_RanksListAction extends User_AbstractListAction
{
    public $mRanksObjects = array();
    public $mActionForm = null;
    public $mpageArr = array(5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0);

    // !Fix compatibility with  User_Action::prepare(&$controller, &$xoopsUser, $moduleConfig)
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    // public function prepare(&$controller, &$xoopsUser)
    {
        $this->mActionForm =new User_RanksListForm();
        $this->mActionForm->prepare();
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('ranks');
        return $handler;
    }

    public function &_getPageNavi()
    {
        $navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);

        $root =& XCube_Root::getSingleton();
        $perpage = $root->mContext->mRequest->getRequest($navi->mPrefix.'perpage');

        if (isset($perpage) && intval($perpage) == 0) {
            $navi->setPerpage(0);
        }
        return $navi;
    }

    public function &_getFilterForm()
    {
        $filter =new User_RanksFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function _getBaseUrl()
    {
        return "./index.php?action=RanksList";
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("ranks_list.html");
        $render->setAttribute("objects", $this->mObjects);
        $render->setAttribute("pageNavi", $this->mFilter->mNavi);
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('pageArr', $this->mpageArr);
        $render->setAttribute('filterForm', $this->mFilter);
        $rank_handler =& $this->_getHandler();
        $rank_total = $rank_handler->getCount();
        $rank_s_total = $rank_handler->getCount(new Criteria('rank_special', 1));
        $render->setAttribute('rankTotal_S', $rank_s_total);
        $render->setAttribute('rankTotal', $rank_total);
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
        if ($form_cancel != null) {
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
        $titleArr = $this->mActionForm->get('title');
        $ranksHandler =& xoops_getmodulehandler('ranks');
        //
        // Do mapping.
        //
        foreach (array_keys($titleArr) as $rid) {
            $ranks =& $ranksHandler->get($rid);
            if (is_object($ranks)) {
                $this->mRanksObjects[$rid] =& $ranks;
            }
            unset($ranks);
        }

        return USER_FRAME_VIEW_INPUT;
    }

    public function _processSave(&$controller, &$xoopsUser)
    {
        $titleArr = $this->mActionForm->get('title');
        $ranksHandler =& xoops_getmodulehandler('ranks');

        foreach (array_keys($titleArr) as $rid) {
            $ranks =& $ranksHandler->get($rid);
            if (is_object($ranks)) {
                $olddata['title'] = $ranks->get('rank_title');
                $olddata['min'] = $ranks->get('rank_min');
                $olddata['max'] = $ranks->get('rank_max');
                $newdata['title'] = $this->mActionForm->get('title', $rid);
                $newdata['min'] = $this->mActionForm->get('min', $rid);
                $newdata['max'] = $this->mActionForm->get('max', $rid);
                if (count(array_diff_assoc($olddata, $newdata)) > 0) {
                    $ranks->set('rank_title', $this->mActionForm->get('title', $rid));
                    $ranks->set('rank_min', $this->mActionForm->get('min', $rid));
                    $ranks->set('rank_max', $this->mActionForm->get('max', $rid));
                    if (!$ranksHandler->insert($ranks)) {
                        return USER_FRAME_VIEW_ERROR;
                    }
                }//count if
            }//object if
        }//foreach

              foreach (array_keys($titleArr) as $rid) {
                  if ($this->mActionForm->get('delete', $rid) == 1) {
                      $ranks =& $ranksHandler->get($rid);
                      if (is_object($ranks)) {
                          if (!$ranksHandler->delete($ranks)) {
                              return USER_FRAME_VIEW_ERROR;
                          }
                      }
                  }
              }
        return USER_FRAME_VIEW_SUCCESS;
    }

    /**
     * To support a template writer, this send the list of mid that actionForm kept.
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("ranks_list_confirm.html");
        $render->setAttribute('ranksObjects', $this->mRanksObjects);
        $render->setAttribute('actionForm', $this->mActionForm);
        
        //
        // To support a template writer, this send the list of mid that
        // actionForm kept.
        //
        $t_arr = $this->mActionForm->get('title');
        $render->setAttribute('rids', array_keys($t_arr));
    }


    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=RanksList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeRedirect('./index.php?action=RanksList', 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=RanksList');
    }
}
