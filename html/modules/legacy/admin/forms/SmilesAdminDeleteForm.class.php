<?php
/**
 * SmilesAdminDeleteForm.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class Legacy_SmilesAdminDeleteForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacy.SmilesAdminDeleteForm.TOKEN' . $this->get('id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['id'] =new XCube_IntProperty('id');

        //
        // Set field properties
        //
        $this->mFieldProperties['id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['id']->setDependsByArray(['required']);
        $this->mFieldProperties['id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_ID);
    }

    public function load(&$obj)
    {
        $this->set('id', $obj->get('id'));
    }

    public function update(&$obj)
    {
        $obj->set('id', $this->get('id'));
    }
}
