<?php
/**
 * ImageListAction.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/forms/ImageFilterForm.class.php';

/***
 * @internal
 */
class Legacy_ImageListAction extends Legacy_AbstractListAction
{
    public $mImgcatId = null;

    public function prepare(&$controller, &$xoopsUser)
    {
        $controller->setDialogMode(true);

        $root =& $controller->mRoot;
        $root->mLanguageManager->loadModuleMessageCatalog('legacy');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('image', 'legacy');
        return $handler;
    }

    public function &_getFilterForm()
    {
        $filter =new Legacy_ImageFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function _getBaseUrl()
    {
        return XOOPS_URL . '/imagemanager.php?op=list';
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $result = parent::getDefaultView($controller, $xoopsUser);
        if (LEGACY_FRAME_VIEW_INDEX == $result) {
            $this->mImgcatId = xoops_getrequest('imgcat_id');
            $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
            $this->mCategory =& $handler->get($this->mImgcatId);
        }

        return $result;
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_image_list.html');

        foreach (array_keys($this->mObjects) as $key) {
            $this->mObjects[$key]->loadImagecategory();
        }

        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);

        $render->setAttribute('imgcatId', $this->mImgcatId);

        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');

        if (is_object($xoopsUser)) {
            $groups = $xoopsUser->getGroups();
        } else {
            $groups = [XOOPS_GROUP_ANONYMOUS];
        }
        $categoryArr =& $handler->getObjectsWithReadPerm($groups, 1);

        $render->setAttribute('categoryArr', $categoryArr);

        //
        // If current category object exists, check the permission of uploading.
        //
        $hasUploadPerm = null;
        if (null !== $this->mCategory) {
            $hasUploadPerm = $this->mCategory->hasUploadPerm($groups);
        }
        $render->setAttribute('hasUploadPerm', $hasUploadPerm);
        $render->setAttribute('category', $this->mCategory);
        //echo xoops_getrequest('target');die();
        $render->setAttribute('target', htmlspecialchars(xoops_getrequest('target'), ENT_QUOTES));
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward(XOOPS_URL . '/imagemanager.php?op=list');
    }
}
