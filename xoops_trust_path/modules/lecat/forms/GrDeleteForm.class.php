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
 * Lecat_GrDeleteForm
**/
class Lecat_GrDeleteForm extends XCube_ActionForm
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
        return "module.lecat.GrDeleteForm.TOKEN";
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
        $this->mFormProperties['gr_id'] =& new XCube_IntProperty('gr_id');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['gr_id'] =& new XCube_FieldProperty($this);
        $this->mFieldProperties['gr_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['gr_id']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_GR_ID);
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
        $this->set('gr_id', $obj->get('gr_id'));
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
        $obj->set('gr_id', $this->get('gr_id'));
    }
}

?>
