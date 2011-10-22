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
 * Lecat_CatEditForm
**/
class Lecat_CatEditForm extends XCube_ActionForm
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
        return "module.lecat.CatEditForm.TOKEN";
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
        $this->mFormProperties['cat_id'] =new XCube_IntProperty('cat_id');
        $this->mFormProperties['title'] =new XCube_StringProperty('title');
        $this->mFormProperties['p_id'] =new XCube_IntProperty('p_id');
        $this->mFormProperties['modules'] =new XCube_TextProperty('modules');
        $this->mFormProperties['description'] =new XCube_TextProperty('description');
        $this->mFormProperties['weight'] =new XCube_IntProperty('weight');
        $this->mFormProperties['options'] =new XCube_TextProperty('options');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['cat_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['cat_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['cat_id']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_CAT_ID);
    
        $this->mFieldProperties['title'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['title']->setDependsByArray(array('required','maxlength'));
        $this->mFieldProperties['title']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_TITLE, '255');
        $this->mFieldProperties['title']->addMessage('maxlength', _MD_LECAT_ERROR_MAXLENGTH, _MD_LECAT_LANG_TITLE, '255');
        $this->mFieldProperties['title']->addVar('maxlength', '255');
    
        $this->mFieldProperties['p_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['p_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['p_id']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_P_ID);
    
        $this->mFieldProperties['weight'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['weight']->setDependsByArray(array('required'));
        $this->mFieldProperties['weight']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_WEIGHT);
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
        $this->set('cat_id', $obj->get('cat_id'));
        $this->set('title', $obj->get('title'));
        $this->set('p_id', $obj->get('p_id'));
        $this->set('modules', $obj->get('modules'));
        $this->set('description', $obj->get('description'));
        $this->set('weight', $obj->get('weight'));
        $this->set('options', $obj->get('options'));
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
        //$obj->set('cat_id', $this->get('cat_id'));
        $obj->set('title', $this->get('title'));
        $obj->set('p_id', $this->get('p_id'));
        $obj->set('modules', $this->get('modules'));
        $obj->set('description', $this->get('description'));
        $obj->set('weight', $this->get('weight'));
        $obj->set('options', $this->get('options'));
    }
}

?>
