<?php
/**
 * ImageDeleteAction.class.php
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

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractDeleteAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImageAdminDeleteForm.class.php';

class Legacy_ImageDeleteAction extends Legacy_AbstractDeleteAction
{
    public function _getId()
    {
        return isset($_REQUEST['image_id']) ? xoops_getrequest('image_id') : 0;
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('image');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_ImageAdminDeleteForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $this->mObject->loadImagecategory();

        $render->setTemplateName('image_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ImageList&imgcat_id=' . $this->mObject->get('imgcat_id'));
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=ImageList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ImageList&imgcat_id=' . $this->mObject->get('imgcat_id'));
    }
}
