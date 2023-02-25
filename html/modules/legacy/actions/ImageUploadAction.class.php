<?php
/**
 * ImageUploadAction.class.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/admin/actions/ImageEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/forms/ImageUploadForm.class.php';

/***
 * @internal
 */
class Legacy_ImageUploadAction extends Legacy_ImageEditAction
{
    public $mCategory = null;

    public function prepare(&$controller, &$xoopsUser)
    {
        parent::prepare($controller, $xoopsUser);
        $controller->setDialogMode(true);

        $root =& $controller->mRoot;
        $root->mLanguageManager->loadModuleMessageCatalog('legacy');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('image', 'legacy');
        return $handler;
    }

    public function _setupObject()
    {
        $this->mObjectHandler =& $this->_getHandler();
        $this->mObject =& $this->mObjectHandler->create();
        $this->mObject->set('imgcat_id', xoops_getrequest('imgcat_id'));
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_ImageUploadForm();
        $this->mActionForm->prepare();
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        $groups = [];
        if (is_object($xoopsUser)) {
            $groups = $xoopsUser->getGroups();
        } else {
            $groups = [XOOPS_GROUP_ANONYMOUS];
        }

        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $this->mCategory =& $handler->get(xoops_getrequest('imgcat_id'));
        if (!is_object($this->mCategory) || (is_object($this->mCategory) && !$this->mCategory->hasUploadPerm($groups))) {
            return false;
        }

        return true;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_image_upload.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);

        $render->setAttribute('category', $this->mCategory);
        $render->setAttribute('target', xoops_getrequest('target'));
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward(XOOPS_URL . '/imagemanager.php?imgcat_id=' . $this->mActionForm->get('imgcat_id') . '&target=' . xoops_getrequest('target'));
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        redirect_header(XOOPS_URL . '/imagemanager.php', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }
}
