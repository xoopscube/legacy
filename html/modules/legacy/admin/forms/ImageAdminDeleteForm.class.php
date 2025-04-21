<?php
/**
 * ImageAdminDeleteForm.class.php
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

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Legacy_ImageAdminDeleteForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacy.ImageAdminDeleteForm.TOKEN' . $this->get('image_id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['image_id'] =new XCube_IntProperty('image_id');

        //
        // Set field properties
        //
        $this->mFieldProperties['image_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['image_id']->setDependsByArray(['required']);
        $this->mFieldProperties['image_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMAGE_ID);
    }

    public function load(&$obj)
    {
        $this->set('image_id', $obj->get('image_id'));
    }

    public function update(&$obj)
    {
        $obj->set('image_id', $this->get('image_id'));
    }
}
