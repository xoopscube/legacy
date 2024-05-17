<?php
/**
 * @package    profile
 * @version    2.4.0
 * @author     Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Kilica
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Profile_DataEditForm extends XCube_ActionForm
{
    //table field definitions
    public $mDef = [];

    /**
     * @public
     */
    public function getTokenName()
    {
        return 'module.profile.DataEditForm.TOKEN';
    }

    /**
     * @public
     */
    public function prepare()
    {
        $handler =& xoops_getmodulehandler('definitions');
        $this->mDef = $handler->getFields4DataEdit();
    
        //
        // Set form properties
        //
        $this->mFormProperties['uid'] =new XCube_IntProperty('uid');
        foreach (array_keys($this->mDef) as $key) {
            $className = $this->mDef[$key]->mFieldType->getFormPropertyClass();
            $this->mFormProperties[$this->mDef[$key]->get('field_name')] =new $className($this->mDef[$key]->get('field_name'));
        
            //validation checks
            $validationArr = [];
            $this->mFieldProperties[$this->mDef[$key]->get('field_name')] =new XCube_FieldProperty($this);
            //required check
            if (1 == $this->mDef[$key]->get('required')) {
                $validationArr[] = 'required';
                $this->mFieldProperties[$this->mDef[$key]->get('field_name')]->addMessage('required', _MD_PROFILE_ERROR_REQUIRED, $this->mDef[$key]->get('label'));
            }
            //validation check
            switch ($this->mDef[$key]->get('validation')) {
            case 'email' :
                $validationArr[] = 'email';
                $this->mFieldProperties[$this->mDef[$key]->get('field_name')]->addMessage($this->mDef[$key]->get('field_name'), _MD_PROFILE_ERROR_EMAIL);
            break;
            }
            $this->mFieldProperties[$this->mDef[$key]->get('field_name')]->setDependsByArray($validationArr);
        }
    
        //
        // Set field properties
        //
        $this->mFieldProperties['uid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['uid']->setDependsByArray(['required']);
        $this->mFieldProperties['uid']->addMessage('required', _MD_PROFILE_ERROR_REQUIRED, _MD_PROFILE_LANG_UID);
    }

    /**
     * @public
     * @param $obj
     */
    public function load(&$obj)
    {
        $this->set('uid', $obj->get('uid'));
        foreach (array_keys($this->mDef) as $key) {
            $this->set($this->mDef[$key]->get('field_name'), $obj->showField($this->mDef[$key]->get('field_name'), Profile_ActionType::EDIT));
        }
    }

    /**
     * @public
     * @param $obj
     */
    public function update(&$obj)
    {
        $obj->set('uid', $this->get('uid'));
        foreach (array_keys($this->mDef) as $key) {
            $val = ('date' != $this->mDef[$key]->get('type')) ? $this->get($this->mDef[$key]->get('field_name')) : $this->_makeUnixtime($this->mDef[$key]->get('field_name'));
            $obj->set($this->mDef[$key]->get('field_name'), $val);
        }
    }

    protected function _makeUnixtime($key)
    {
        $timeArray = explode('/', $this->get($key));
        return mktime(0, 0, 0, $timeArray[1], $timeArray[2], $timeArray[0]);
    }
}
