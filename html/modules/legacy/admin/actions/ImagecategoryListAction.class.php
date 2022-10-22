<?php
/**
 * ImagecategoryListAction.class.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImagecategoryFilterForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImagecategoryListForm.class.php';

class Legacy_ImagecategoryListAction extends Legacy_AbstractListAction
{
    public $mImagecategoryObjects = [];
    public $mActionForm = null;
    public $mpageArr = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0];

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->mActionForm =new Legacy_ImagecategoryListForm();
        $this->mActionForm->prepare();
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('imagecategory');
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
        $filter =new Legacy_ImagecategoryFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function _getBaseUrl()
    {
        return './index.php?action=ImagecategoryList';
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('imagecategory_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('pageArr', $this->mpageArr);
        $render->setAttribute('filterForm', $this->mFilter);

        $imgcat_handler =& $this->_getHandler();
        $imgcat_total = $imgcat_handler->getCount();
        $file_imgcat_total = $imgcat_handler->getCount(new Criteria('imgcat_storetype', 'file'));
        $render->setAttribute('ImgcatTotal', $imgcat_total);
        $render->setAttribute('fileImgcatTotal', $file_imgcat_total);
        $render->setAttribute('dbImgcatTotal', $imgcat_total - $file_imgcat_total);
        //total of image(s)
        $image_handler =& xoops_getmodulehandler('image');
        $image_total = $image_handler->getCount();
        $display_image_total = $image_handler->getCount(new Criteria('image_display', 1));
        $render->setAttribute('ImageTotal', $image_total);
        $render->setAttribute('displayImageTotal', $display_image_total);
        $render->setAttribute('notdisplayImageTotal', $image_total - $display_image_total);
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
        if (null != $form_cancel) {
            return LEGACY_FRAME_VIEW_CANCEL;
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
        $imagecategoryHandler =& xoops_getmodulehandler('imagecategory');
        //
        // Do mapping.
        //
        foreach (array_keys($nameArr) as $icid) {
            $imagecategory =& $imagecategoryHandler->get($icid);
            if (is_object($imagecategory)) {
                $this->mImagecategoryObjects[$icid] =& $imagecategory;
            }
            unset($imagecategory);
        }


        return LEGACY_FRAME_VIEW_INPUT;
    }

    public function _processSave(&$controller, &$xoopsUser)
    {
        $nameArr = $this->mActionForm->get('name');
        $imagecategoryHandler =& xoops_getmodulehandler('imagecategory');

        foreach (array_keys($nameArr) as $icid) {
            $imagecategory =& $imagecategoryHandler->get($icid);
            if (is_object($imagecategory)) {
                $olddata['name'] = $imagecategory->get('imgcat_name');
                $olddata['weight'] = $imagecategory->get('imgcat_weight');
                $olddata['maxsize'] = $imagecategory->get('imgcat_maxsize');
                $olddata['maxwidth'] = $imagecategory->get('imgcat_maxwidth');
                $olddata['maxheight'] = $imagecategory->get('imgcat_maxheight');
                $olddata['display'] = $imagecategory->get('imgcat_display');
                $newdata['name'] = $this->mActionForm->get('name', $icid);
                $newdata['weight'] = $this->mActionForm->get('weight', $icid);
                $newdata['maxsize'] = $this->mActionForm->get('maxsize', $icid);
                $newdata['maxwidth'] = $this->mActionForm->get('maxwidth', $icid);
                $newdata['maxheight'] = $this->mActionForm->get('maxheight', $icid);
                $newdata['display'] = $this->mActionForm->get('display', $icid);
                if (count(array_diff_assoc($olddata, $newdata)) > 0) {
                    $imagecategory->set('imgcat_name', $this->mActionForm->get('name', $icid));
                    $imagecategory->set('imgcat_weight', $this->mActionForm->get('weight', $icid));
                    $imagecategory->set('imgcat_maxsize', $this->mActionForm->get('maxsize', $icid));
                    $imagecategory->set('imgcat_maxwidth', $this->mActionForm->get('maxwidth', $icid));
                    $imagecategory->set('imgcat_maxheight', $this->mActionForm->get('maxheight', $icid));
                    $imagecategory->set('imgcat_display', $this->mActionForm->get('display', $icid));
                    if (!$imagecategoryHandler->insert($imagecategory)) {
                        return LEGACY_FRAME_VIEW_ERROR;
                    }
                }//count if
            }//object if
        }//foreach

                foreach (array_keys($nameArr) as $icid) {
                    if (1 == $this->mActionForm->get('delete', $icid)) {
                        $imagecategory =& $imagecategoryHandler->get($icid);
                        if (is_object($imagecategory)) {
                            if (!$imagecategoryHandler->delete($imagecategory)) {
                                return LEGACY_FRAME_VIEW_ERROR;
                            }
                        }
                    }
                }
        return LEGACY_FRAME_VIEW_SUCCESS;
    }

    /**
     * To support a template writer, this send the list of mid that actionForm kept.
     * @param $controller
     * @param $xoopsUser
     * @param $render
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('imagecategory_list_confirm.html');
        $render->setAttribute('imagecategoryObjects', $this->mImagecategoryObjects);
        $render->setAttribute('actionForm', $this->mActionForm);
        //
        // To support a template writer, this send the list of mid that
        // actionForm kept.
        //
        $t_arr = $this->mActionForm->get('name');
        $render->setAttribute('icids', array_keys($t_arr));
    }


    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=ImagecategoryList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeRedirect('./index.php?action=ImagecategoryList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=ImagecategoryList');
    }
}
