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
 * Lecat_GrEditForm
**/
class Lecat_GrEditForm extends XCube_ActionForm
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
        return "module.lecat.GrEditForm.TOKEN";
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
        $this->mFormProperties['title'] =& new XCube_StringProperty('title');
        $this->mFormProperties['level'] =& new XCube_IntProperty('level');
        $this->mFormProperties['actions'] =& new XCube_TextProperty('actions');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['gr_id'] =& new XCube_FieldProperty($this);
        $this->mFieldProperties['gr_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['gr_id']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_GR_ID);
    
        $this->mFieldProperties['title'] =& new XCube_FieldProperty($this);
        $this->mFieldProperties['title']->setDependsByArray(array('required','maxlength'));
        $this->mFieldProperties['title']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_TITLE, '255');
        $this->mFieldProperties['title']->addMessage('maxlength', _MD_LECAT_ERROR_MAXLENGTH, _MD_LECAT_LANG_TITLE, '255');
        $this->mFieldProperties['title']->addVar('maxlength', '255');
    
        $this->mFieldProperties['level'] =& new XCube_FieldProperty($this);
        $this->mFieldProperties['level']->setDependsByArray(array('required'));
        $this->mFieldProperties['level']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_LEVEL);
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
        $this->set('title', $obj->get('title'));
        $this->set('level', $obj->get('level'));
        $this->set('actions', $obj->get('actions'));
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
    	$request = XCube_Root::getSingleton()->mContext->mRequest;
        //$obj->set('gr_id', $this->get('gr_id'));
        $obj->set('title', $this->get('title'));
        $obj->set('level', $this->get('level'));
		//actions
		$actions_key = $request->getRequest('actions_key');
		$actions_title = $request->getRequest('actions_title');
		$actions_default = $request->getRequest('actions_default');
		$actions = array();
		foreach(array_keys($actions_key) as $key){
			if(! $actions_title[$key] || ! $actions_key[$key]){
				//TODO:error
			}
			else{
				$actions['title'][$actions_key[$key]] = $actions_title[$key];
				$actions['default'][$actions_key[$key]] = $actions_default[$key];
			}
		}
		$obj->setActions($actions);
    }
}

?>
