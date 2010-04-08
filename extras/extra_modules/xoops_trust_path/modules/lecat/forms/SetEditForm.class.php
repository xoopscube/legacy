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
 * Lecat_SetEditForm
**/
class Lecat_SetEditForm extends XCube_ActionForm
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
        return "module.lecat.SetEditForm.TOKEN";
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
        $this->mFormProperties['set_id'] =new XCube_IntProperty('set_id');
        $this->mFormProperties['title'] =new XCube_StringProperty('title');
        $this->mFormProperties['level'] =new XCube_IntProperty('level');
        $this->mFormProperties['actions'] =new XCube_TextProperty('actions');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['set_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['set_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['set_id']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_SET_ID);
    
        $this->mFieldProperties['title'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['title']->setDependsByArray(array('required','maxlength'));
        $this->mFieldProperties['title']->addMessage('required', _MD_LECAT_ERROR_REQUIRED, _MD_LECAT_LANG_TITLE, '255');
        $this->mFieldProperties['title']->addMessage('maxlength', _MD_LECAT_ERROR_MAXLENGTH, _MD_LECAT_LANG_TITLE, '255');
        $this->mFieldProperties['title']->addVar('maxlength', '255');
    
        $this->mFieldProperties['level'] =new XCube_FieldProperty($this);
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
        $this->set('set_id', $obj->get('set_id'));
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
        //$obj->set('set_id', $this->get('set_id'));
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
