<?php
/**
 * ImagecategoryAdminNewForm.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ImagecategoryAdminEditForm.class.php';

class Legacy_ImagecategoryAdminNewForm extends Legacy_ImagecategoryAdminEditForm
{
    public function getTokenName()
    {
        return 'module.legacy.ImagecategoryAdminNewForm.TOKEN';
    }

    public function prepare()
    {
        parent::prepare();

        //
        // Set form properties
        //
        $this->mFormProperties['imgcat_storetype'] =new XCube_StringProperty('imgcat_storetype');

        //
        // Set field properties
        //
        $this->mFieldProperties['imgcat_storetype'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['imgcat_storetype']->setDependsByArray(['required', 'mask']);
        $this->mFieldProperties['imgcat_storetype']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMGCAT_STORETYPE);
        $this->mFieldProperties['imgcat_storetype']->addMessage('mask', _MD_LEGACY_ERROR_MASK, _AD_LEGACY_LANG_IMGCAT_STORETYPE);
        $this->mFieldProperties['imgcat_storetype']->addVar('mask', '/^(file|db)$/');
    }

    public function load(&$obj)
    {
        parent::load($obj);
        $this->set('imgcat_storetype', $obj->get('imgcat_storetype'));
    }

    public function update(&$obj)
    {
        parent::update($obj);
        $obj->set('imgcat_storetype', $this->get('imgcat_storetype'));
    }
}
