<?php
/**
 * SmilesAdminEditForm.class.php
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

class Legacy_SmilesAdminEditForm extends XCube_ActionForm
{
    public $mOldFileName = null;
    public $_mIsNew = null;
    public $mFormFile = null;

    public function getTokenName()
    {
        return 'module.legacy.SmilesAdminEditForm.TOKEN' . $this->get('id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['id'] =new XCube_IntProperty('id');
        $this->mFormProperties['code'] =new XCube_StringProperty('code');
        $this->mFormProperties['smile_url'] =new XCube_ImageFileProperty('smile_url');
        $this->mFormProperties['emotion'] =new XCube_StringProperty('emotion');
        $this->mFormProperties['display'] =new XCube_BoolProperty('display');

        //
        // Set field properties
        //
        $this->mFieldProperties['id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['id']->setDependsByArray(['required']);
        $this->mFieldProperties['id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_ID);

        $this->mFieldProperties['code'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['code']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['code']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_CODE, '50');
        $this->mFieldProperties['code']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _MD_LEGACY_LANG_CODE, '50');
        $this->mFieldProperties['code']->addVar('maxlength', '50');

        $this->mFieldProperties['smile_url'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['smile_url']->setDependsByArray(['extension']);
        $this->mFieldProperties['smile_url']->addMessage('extension', _AD_LEGACY_ERROR_EXTENSION);
        $this->mFieldProperties['smile_url']->addVar('extension', 'jpg,gif,png');

        $this->mFieldProperties['emotion'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['emotion']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['emotion']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_EMOTION, '75');
        $this->mFieldProperties['emotion']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _MD_LEGACY_LANG_EMOTION, '75');
        $this->mFieldProperties['emotion']->addVar('maxlength', '75');
    }

    public function validateSmile_url()
    {
        if ($this->_mIsNew && null == $this->get('smile_url')) {
            $this->addErrorMessage(XCube_Utils::formatString(_MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_SMILE_URL));
        }
    }

    public function load(&$obj)
    {
        $this->set('id', $obj->get('id'));
        $this->set('code', $obj->get('code'));
        $this->set('emotion', $obj->get('emotion'));
        $this->set('display', $obj->get('display'));

        $this->_mIsNew = $obj->isNew();
        $this->mOldFileName = $obj->get('smile_url');
    }

    public function update(&$obj)
    {
        $obj->set('id', $this->get('id'));
        $obj->set('code', $this->get('code'));
        $obj->set('emotion', $this->get('emotion'));
        $obj->set('display', $this->get('display'));

        $this->mFormFile = $this->get('smile_url');
        if (null != $this->mFormFile) {
            $this->mFormFile->setRandomToBodyName('smil');    // Fix your prefix
            $obj->set('smile_url', $this->mFormFile->getFileName());
        }
    }
}
