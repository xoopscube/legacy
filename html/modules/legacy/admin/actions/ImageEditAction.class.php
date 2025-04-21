<?php
/**
 * ImageEditAction.class.php
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

require_once XOOPS_MODULE_PATH . '/legacy/admin/actions/ImageCreateAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImageAdminEditForm.class.php';

class Legacy_ImageEditAction extends Legacy_ImageCreateAction
{
    public function _getId()
    {
        return isset($_REQUEST['image_id']) ? xoops_getrequest('image_id') : 0;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_ImageAdminEditForm();
        $this->mActionForm->prepare();
    }

    public function isEnableCreate()
    {
        return false;
    }

    public function _enableCatchImgcat()
    {
        return false;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $this->mObject->loadImagecategory();

        $render->setTemplateName('image_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);

        $handler =& xoops_getmodulehandler('imagecategory', 'legacy');
        $t_category = $handler->get($this->mObject->get('imgcat_id'));

        $categoryArr =& $handler->getObjects(new Criteria('imgcat_storetype', $t_category->get('imgcat_storetype')));
        $render->setAttribute('categoryArr', $categoryArr);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ImageList&imgcat_id=' . $this->mObject->get('imgcat_id'));
    }
}
