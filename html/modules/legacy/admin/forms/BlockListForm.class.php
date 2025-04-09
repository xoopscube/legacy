<?php
/**
 * BlockListForm.class.php
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
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

/***
 * @internal
 * @public
 * @todo We may rename this class.
 */
class Legacy_BlockListForm extends XCube_ActionForm
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
            return 'module.legacy.BlockListForm.TOKEN';
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
        //
        // Set form properties
        //
        $this->mFormProperties['title'] =new XCube_StringArrayProperty('title');
        $this->mFormProperties['weight'] =new XCube_IntArrayProperty('weight');
        $this->mFormProperties['side'] =new XCube_IntArrayProperty('side');
        $this->mFormProperties['bcachetime'] =new XCube_IntArrayProperty('bcachetime');
        $this->mFormProperties['uninstall']=new XCube_BoolArrayProperty('uninstall');
        //to display error-msg at confirm-page
        $this->mFormProperties['confirm'] =new XCube_BoolProperty('confirm');

        //
        // Set field properties
        //
        $this->mFieldProperties['title'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['title']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['title']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_TITLE, '191');
        $this->mFieldProperties['title']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _AD_LEGACY_LANG_TITLE, '191');
        $this->mFieldProperties['title']->addVar('maxlength', '191');

        $this->mFieldProperties['weight'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['weight']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['weight']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_WEIGHT);
        $this->mFieldProperties['weight']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_LEGACY_LANG_WEIGHT);
        $this->mFieldProperties['weight']->addVar('min', '0');
        $this->mFieldProperties['weight']->addVar('max', '65535');

        $this->mFieldProperties['side'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['side']->setDependsByArray(['required', 'objectExist']);
        $this->mFieldProperties['side']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_SIDE);
        $this->mFieldProperties['side']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_SIDE);
        $this->mFieldProperties['side']->addVar('handler', 'columnside');
        $this->mFieldProperties['side']->addVar('module', 'legacy');

        $this->mFieldProperties['bcachetime'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['bcachetime']->setDependsByArray(['required', 'objectExist']);
        $this->mFieldProperties['bcachetime']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_BCACHETIME);
        $this->mFieldProperties['bcachetime']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_BCACHETIME);
        $this->mFieldProperties['bcachetime']->addVar('handler', 'cachetime');
    }
}
