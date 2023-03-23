<?php
/**
 * This class is generated by makeActionForm tool.
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     code generator makeActionForm
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

/**
 *
 * @auchor
 */
class Legacy_SmilesListForm extends XCube_ActionForm
{
    /**
     * If the request is GET, never return token name.
     * By this logic, a action can have three page in one action.
     */
    public function getTokenName()
    {
        //
        //
        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            return 'module.legacy.SmilesSettingsForm.TOKEN';
        } else {
            return null;
        }
    }

    /**
     * For displaying the confirm-page, don't show CSRF error.
     * Always return null.
     */
    public function getTokenErrorMessage()
    {
        return null;
    }

    public function prepare()
    {
        // set properties
        $this->mFormProperties['code']=new XCube_StringArrayProperty('code');
        $this->mFormProperties['emotion']=new XCube_StringArrayProperty('emotion');
        $this->mFormProperties['display']=new XCube_BoolArrayProperty('display');
        $this->mFormProperties['delete']=new XCube_BoolArrayProperty('delete');
        //to display error-msg at confirm-page
        $this->mFormProperties['confirm'] =new XCube_BoolProperty('confirm');
        // set fields
        $this->mFieldProperties['code']=new XCube_FieldProperty($this);
        $this->mFieldProperties['code']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['code']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_CODE, '50');
        $this->mFieldProperties['code']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _MD_LEGACY_LANG_CODE, '50');
        $this->mFieldProperties['code']->addVar('maxlength', 50);

        $this->mFieldProperties['emotion']=new XCube_FieldProperty($this);
        $this->mFieldProperties['emotion']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['emotion']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_EMOTION, '75');
        $this->mFieldProperties['emotion']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _MD_LEGACY_LANG_EMOTION, '75');
        $this->mFieldProperties['emotion']->addVar('maxlength', 75);
    }
}
