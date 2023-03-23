<?php
/**
 * BlockEditForm.class.php
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/26
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Legacy_BlockEditForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacy.BlockEditForm.TOKEN' . $this->get('bid');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['bid'] =new XCube_IntProperty('bid');
        $this->mFormProperties['options'] =new XCube_StringArrayProperty('options');
        $this->mFormProperties['title'] =new XCube_StringProperty('title');
        $this->mFormProperties['side'] =new XCube_IntProperty('side');
        $this->mFormProperties['weight'] =new XCube_IntProperty('weight');
        $this->mFormProperties['bcachetime'] =new XCube_IntProperty('bcachetime');
        $this->mFormProperties['bmodule'] =new XCube_IntArrayProperty('bmodule');
        $this->mFormProperties['groupid'] =new XCube_IntArrayProperty('groupid');

        //
        // Set field properties
        //
        $this->mFieldProperties['bid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['bid']->setDependsByArray(['required']);
        $this->mFieldProperties['bid']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_BID);

        $this->mFieldProperties['title'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['title']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['title']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_TITLE, '191');
        $this->mFieldProperties['title']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _AD_LEGACY_LANG_TITLE, '191');
        $this->mFieldProperties['title']->addVar('maxlength', '191');

        $this->mFieldProperties['side'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['side']->setDependsByArray(['required', 'objectExist']);
        $this->mFieldProperties['side']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_SIDE);
        $this->mFieldProperties['side']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_SIDE);
        $this->mFieldProperties['side']->addVar('handler', 'columnside');
        $this->mFieldProperties['side']->addVar('module', 'legacy');

        $this->mFieldProperties['weight'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['weight']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['weight']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_WEIGHT);
        $this->mFieldProperties['weight']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_LEGACY_LANG_WEIGHT);
        $this->mFieldProperties['weight']->addVar('min', '0');
        $this->mFieldProperties['weight']->addVar('max', '65535');

        $this->mFieldProperties['bcachetime'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['bcachetime']->setDependsByArray(['required', 'objectExist']);
        $this->mFieldProperties['bcachetime']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_BCACHETIME);
        $this->mFieldProperties['bcachetime']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_BCACHETIME);
        $this->mFieldProperties['bcachetime']->addVar('handler', 'cachetime');

        $this->mFieldProperties['groupid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['groupid']->setDependsByArray(['objectExist']);
        $this->mFieldProperties['groupid']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_GROUPID);
        $this->mFieldProperties['groupid']->addVar('handler', 'group');

        /* @todo @gigamaster template */
        $this->mFormProperties['template'] = new XCube_StringProperty('template');

        $this->mFieldProperties['template'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['template']->setDependsByArray(array('maxlength'));
        $this->mFieldProperties['template']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _AD_LEGACY_LANG_TEMPLATE, '255');
        $this->mFieldProperties['template']->addVar('maxlength', '255');


    }

    public function validateBmodule()
    {
        $bmodule = $this->get('bmodule');
        if (!(count($bmodule))) {
            $this->addErrorMessage(_AD_LEGACY_ERROR_BMODULE);
        } else {
            $handler =& xoops_gethandler('module');
            foreach ($this->get('bmodule') as $mid) {
                $module =& $handler->get($mid);
                if (-1 !== $mid && 0 !== $mid && !is_object($module)) {
                    $this->addErrorMessage(XCube_Utils::formatString(_AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_BMODULE));
                }
            }
        }
    }

    public function validateGroupid()
    {
        $groupid = $this->get('groupid');
        if (!(count($groupid))) {
            $this->addErrorMessage(_AD_LEGACY_ERROR_GROUPID);
        }
    }

    public function load(&$obj)
    {
        $this->set('bid', $obj->get('bid'));
        $this->set('title', $obj->get('title'));
        $this->set('side', $obj->get('side'));
        $this->set('weight', $obj->get('weight'));
        $this->set('bcachetime', $obj->get('bcachetime'));

/* @todo @gigamaster template */
        $this->set('template', $obj->get('template'));

        $i = 0;
        foreach ($obj->mBmodule as $module) {
            if (is_object($module)) {
                $this->set('bmodule', $i++, $module->get('module_id'));
            }
        }

        $i = 0;
        foreach ($obj->mGroup as $group) {
            if (is_object($group)) {
                $this->set('groupid', $i++, $group->get('groupid'));
            }
        }
    }

    public function update(&$obj)
    {
        $obj->set('bid', $this->get('bid'));
        $obj->set('title', $this->get('title'));
        $obj->set('side', $this->get('side'));
        $obj->set('weight', $this->get('weight'));
        $obj->set('bcachetime', $this->get('bcachetime'));

        $obj->set('last_modified', time());

        /* @todo @gigamaster template */
        $obj->set('template', $this->get('template'));
        //
        // Update options (XOOPS2 compatible)
        //
        $optionArr = $this->get('options');
        for ($i = 0; $i < count($optionArr); $i++) {
            if (is_array($optionArr[$i])) {
                $optionArr[$i] = implode(',', $optionArr[$i]);
            }
        }

        $obj->set('options', implode('|', $optionArr));

        $obj->mBmodule = [];
        $handler =& xoops_getmodulehandler('block_module_link', 'legacy');
        foreach ($this->get('bmodule') as $mid) {
            $t_obj =& $handler->create();
            $t_obj->set('block_id', $this->get('bid'));
            $t_obj->set('module_id', $mid);
            $obj->mBmodule[] =& $t_obj;
            unset($t_obj);
        }

        $obj->mGroup = [];
        $handler =& xoops_gethandler('group');
        foreach ($this->get('groupid') as $groupid) {
            $obj->mGroup[] =& $handler->get($groupid);
        }
    }
}
