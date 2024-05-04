<?php
/**
 * CommentAdminEditForm.class.php
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

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class Legacy_AbstractCommentAdminEditForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacy.CommentAdminEditForm.TOKEN' . $this->get('com_id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['com_id'] =new XCube_IntProperty('com_id');
        $this->mFormProperties['com_icon'] =new XCube_StringProperty('com_icon');
        $this->mFormProperties['com_title'] =new XCube_StringProperty('com_title');
        $this->mFormProperties['com_text'] =new XCube_TextProperty('com_text');
        $this->mFormProperties['com_sig'] =new XCube_BoolProperty('com_sig');
        $this->mFormProperties['com_status'] =new XCube_IntProperty('com_status');
        $this->mFormProperties['dohtml'] =new XCube_BoolProperty('dohtml');
        $this->mFormProperties['dosmiley'] =new XCube_BoolProperty('dosmiley');
        $this->mFormProperties['doxcode'] =new XCube_BoolProperty('doxcode');
        $this->mFormProperties['doimage'] =new XCube_BoolProperty('doimage');
        $this->mFormProperties['dobr'] =new XCube_BoolProperty('dobr');

        //
        // Set field properties
        //

        $this->mFieldProperties['com_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['com_id']->setDependsByArray(['required']);
        $this->mFieldProperties['com_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_COM_ID);

        $this->mFieldProperties['com_icon'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['com_icon']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['com_icon']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _MD_LEGACY_LANG_COM_ICON, '25');
        $this->mFieldProperties['com_icon']->addVar('maxlength', '25');

        $this->mFieldProperties['com_title'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['com_title']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['com_title']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_COM_TITLE, '191');
        $this->mFieldProperties['com_title']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _MD_LEGACY_LANG_COM_TITLE, '191');
        $this->mFieldProperties['com_title']->addVar('maxlength', '191');

        $this->mFieldProperties['com_text'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['com_text']->setDependsByArray(['required']);
        $this->mFieldProperties['com_text']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_COM_TEXT);
    }

    public function load(&$obj)
    {
        $this->set('com_id', $obj->get('com_id'));
        $this->set('com_icon', $obj->get('com_icon'));
        $this->set('com_title', $obj->get('com_title'));
        $this->set('com_text', $obj->get('com_text'));
        $this->set('com_sig', $obj->get('com_sig'));
        $this->set('com_status', $obj->get('com_status'));
        $this->set('dohtml', $obj->get('dohtml'));
        $this->set('dosmiley', $obj->get('dosmiley'));
        $this->set('doxcode', $obj->get('doxcode'));
        $this->set('doimage', $obj->get('doimage'));
        $this->set('dobr', $obj->get('dobr'));
    }

    public function update(&$obj)
    {
        $obj->set('com_id', $this->get('com_id'));
        $obj->set('com_icon', $this->get('com_icon'));
        $obj->set('com_title', $this->get('com_title'));
        $obj->set('com_text', $this->get('com_text'));
        $obj->set('com_sig', $this->get('com_sig'));
        $obj->set('com_status', $this->get('com_status'));
        $obj->set('dohtml', $this->get('dohtml'));
        $obj->set('dosmiley', $this->get('dosmiley'));
        $obj->set('doxcode', $this->get('doxcode'));
        $obj->set('doimage', $this->get('doimage'));
        $obj->set('dobr', $this->get('dobr'));
    }
}

class Legacy_PendingCommentAdminEditForm extends Legacy_AbstractCommentAdminEditForm
{
    public function prepare()
    {
        parent::prepare();

        $this->mFieldProperties['com_status'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['com_status']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['com_status']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_COM_STATUS);
        $this->mFieldProperties['com_status']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_LEGACY_LANG_COM_STATUS);
        $this->mFieldProperties['com_status']->addVar('min', '1');
        $this->mFieldProperties['com_status']->addVar('max', '3');
    }
}

class Legacy_ApprovalCommentAdminEditForm extends Legacy_AbstractCommentAdminEditForm
{
    public function prepare()
    {
        parent::prepare();

        $this->mFieldProperties['com_status'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['com_status']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['com_status']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_COM_STATUS);
        $this->mFieldProperties['com_status']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_LEGACY_LANG_COM_STATUS);
        $this->mFieldProperties['com_status']->addVar('min', '2');
        $this->mFieldProperties['com_status']->addVar('max', '3');
    }
}
