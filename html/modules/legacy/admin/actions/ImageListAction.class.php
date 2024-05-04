<?php
/**
 * ImageListAction.class.php
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImageFilterForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImageListForm.class.php';

class Legacy_ImageListAction extends Legacy_AbstractListAction
{
    public $mImageObjects = [];
    public $mCategory = null;
    public $mActionForm = null;
    public $mpageArr = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0];

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->mActionForm =new Legacy_ImageListForm();
        $this->mActionForm->prepare();
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('image');
        return $handler;
    }

    public function &_getPageNavi()
    {
        $navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);

        $root =& XCube_Root::getSingleton();
        $perpage = $root->mContext->mRequest->getRequest($navi->mPrefix.'perpage');
        if (isset($perpage) && 0 === (int)$perpage) {
            $navi->setPerpage(0);
        }
        return $navi;
    }

    public function &_getFilterForm()
    {
        $filter =new Legacy_ImageFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function _getBaseUrl()
    {
        return './index.php?action=ImageList';
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $result = parent::getDefaultView($controller, $xoopsUser);
        if (LEGACY_FRAME_VIEW_INDEX === $result) {
            $cat_id = xoops_getrequest('imgcat_id');
            $handler =& xoops_getmodulehandler('imagecategory');
            $this->mCategory =& $handler->get($cat_id);

            if (null === $this->mCategory) {
                $result = LEGACY_FRAME_VIEW_ERROR;
            }
        }

        return $result;
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('image_list.html');

        foreach (array_keys($this->mObjects) as $key) {
            $this->mObjects[$key]->loadImagecategory();
        }

        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);

        $render->setAttribute('category', $this->mCategory);
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('pageArr', $this->mpageArr);
        $render->setAttribute('filterForm', $this->mFilter);

        $image_handler =& $this->_getHandler();
        $imgcat_id = $controller->mRoot->mContext->mRequest->getRequest('imgcat_id');
        $cat_id = isset($imgcat_id) ? (int)$imgcat_id : 0;
        $total_criteria =new CriteriaCompo(new Criteria('imgcat_id', $cat_id));
        $image_total = $image_handler->getCount($total_criteria);
        $total_criteria->add(new Criteria('image_display', 1));
        $display_image_total = $image_handler->getCount($total_criteria);
        $render->setAttribute('ImageTotal', $image_total);
        $render->setAttribute('displayImageTotal', $display_image_total);
        $render->setAttribute('notdisplayImageTotal', $image_total - $display_image_total);
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ImagecategoryList');
    }
}
