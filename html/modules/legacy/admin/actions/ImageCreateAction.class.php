<?php
/**
 * ImageCreateAction.class.php
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

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImageAdminEditForm.class.php';

class Legacy_ImageCreateAction extends Legacy_AbstractEditAction
{
    public function _getId()
    {
        return 0;
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('image', 'legacy');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_ImageAdminCreateForm();
        $this->mActionForm->prepare();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $flag = parent::getDefaultView($controller, $xoopsUser);

        if (LEGACY_FRAME_VIEW_INPUT === $flag && $this->_enableCatchImgcat()) {
            $this->mActionForm->set('imgcat_id', xoops_getrequest('imgcat_id'));
        }

        return $flag;
    }

    public function _enableCatchImgcat()
    {
        return true;
    }

    public function _doExecute()
    {
        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $category =& $handler->get($this->mActionForm->get('imgcat_id'));

        //
        // [TODO]
        // Should the following procedure be after parent::_doExecute()?
        //
        if ('file' === $category->get('imgcat_storetype')) {
            $this->_storeFile();
        } else {
            $this->_storeDB();
        }

        return parent::_doExecute();
    }

    public function _storeFile()
    {
        if (null === $this->mActionForm->mFormFile) {
            return null;
        }

        //
        // If there is an old file, delete it
        //
        if (null !== $this->mActionForm->mOldFileName) {
            @unlink(XOOPS_UPLOAD_PATH . '/' . $this->mActionForm->mOldFileName);

            // Get a body name of the old file.
            $match = [];
            if (preg_match("/(.+)\.\w+$/", $this->mActionForm->mOldFileName, $match)) {
                $this->mActionForm->mFormFile->setBodyName($match[1]);
            }
        }

        $this->mObject->set('image_name', $this->mActionForm->mFormFile->getFileName());

        return $this->mActionForm->mFormFile->saveAs(XOOPS_UPLOAD_PATH);
    }

    public function _storeDB()
    {
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $this->mObject->loadImagecategory();

        $render->setTemplateName('image_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);

        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $categoryArr =& $handler->getObjects();
        $render->setAttribute('categoryArr', $categoryArr);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ImageList&imgcat_id=' . $this->mActionForm->get('imgcat_id'));
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=ImagecategoryList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ImagecategoryList');
    }
}
