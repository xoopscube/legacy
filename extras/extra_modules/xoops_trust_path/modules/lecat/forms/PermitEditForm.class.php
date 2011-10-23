<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

/**
 * Lecat_PermitEditForm
**/
class Lecat_PermitEditForm extends XCube_ActionForm
{
    /**
     * getTokenName
     * 
     * @param   void
     * 
     * @return  string
    **/
    public function getTokenName()
    {
        return "module.lecat.PermitEditForm.TOKEN";
    }

    /**
     * prepare
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['permit_id'] =new XCube_IntProperty('permit_id');
        $this->mFormProperties['cat_id'] =new XCube_IntProperty('cat_id');
        $this->mFormProperties['groupid'] =new XCube_IntProperty('groupid');
        $this->mFormProperties['permissions'] =new XCube_TextProperty('permissions');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['permit_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['permit_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['permit_id']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_PERMIT_ID);
    
        $this->mFieldProperties['cat_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['cat_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['cat_id']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_CAT_ID);
    
    }

    /**
     * load
     * 
     * @param   XoopsSimpleObject  &$obj
     * 
     * @return  void
    **/
    public function load(/*** XoopsSimpleObject ***/ &$obj)
    {
        $this->set('permit_id', $obj->get('permit_id'));
        $this->set('cat_id', $obj->get('cat_id'));
        $this->set('groupid', $obj->get('groupid'));
        $this->set('permissions', $obj->get('permissions'));
    }

    /**
     * update
     * 
     * @param   XoopsSimpleObject  &$obj
     * 
     * @return  void
    **/
    public function update(/*** XoopsSimpleObject ***/ &$obj)
    {
        $obj->set('permit_id', $this->get('permit_id'));
        $obj->set('cat_id', $this->get('cat_id'));
        $obj->set('groupid', $this->get('groupid'));
        $obj->set('permissions', $this->get('permissions'));
    }
}

?>
