<?php
/**
 * CommentAdminDeleteForm.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

/***
 * @internal
 */
class Legacy_CommentAdminDeleteForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacy.XoopscommentsAdminDeleteForm.TOKEN' . $this->get('com_id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['com_id'] =new XCube_IntProperty('com_id');
        $this->mFormProperties['delete_mode'] =new XCube_StringProperty('delete_mode');

        //
        // Set field properties
        //
        $this->mFieldProperties['com_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['com_id']->setDependsByArray(['required']);
        $this->mFieldProperties['com_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_COM_ID);
    }

    public function load(&$obj)
    {
        $this->setVar('com_id', $obj->get('com_id'));
    }

    public function update(&$obj)
    {
        $obj->setVar('com_id', $this->get('com_id'));
    }
}
