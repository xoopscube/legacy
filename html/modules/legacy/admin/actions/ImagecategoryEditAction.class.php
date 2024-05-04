<?php
/**
 * ImagecategoryEditAction.class.php
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
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImagecategoryAdminEditForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImagecategoryAdminNewForm.class.php';

class Legacy_ImagecategoryEditAction extends Legacy_AbstractEditAction
{
    public function _getId()
    {
        return isset($_REQUEST['imgcat_id']) ? xoops_getrequest('imgcat_id') : 0;
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('imagecategory');
        return $handler;
    }

    public function _setupObject()
    {
        parent::_setupObject();
        $this->mObject->loadReadGroups();
        $this->mObject->loadUploadGroups();
    }

    public function _setupActionForm()
    {
        $this->mActionForm = $this->mObject->isNew() ? new Legacy_ImagecategoryAdminNewForm()
                                                     : new Legacy_ImagecategoryAdminEditForm();

        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('imagecategory_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);

        $handler =& xoops_gethandler('group');
        $groupArr =& $handler->getObjects();
        $render->setAttribute('groupArr', $groupArr);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ImagecategoryList');
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
