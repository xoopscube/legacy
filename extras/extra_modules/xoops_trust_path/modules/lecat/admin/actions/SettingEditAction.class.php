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

require_once LECAT_TRUST_PATH . '/class/AbstractEditAction.class.php';

/**
 * Lecat_Admin_IndexAction
**/
class Lecat_Admin_SettingEditAction extends Lecat_AbstractEditAction
{
	/**
	 * _setupActionForm
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	protected function _setupActionForm()
	{
		// $this->mActionForm =new Lecat_CatEditForm();
		$this->mActionForm =& $this->mAsset->getObject('form', 'setting', true, 'edit');
		$this->mActionForm->prepare();
	}

	/**
	 * prepare
	 * 
	 * @param	void
	 * 
	 * @return	bool
	**/
	public function prepare()
	{
		$this->_setupActionForm();
	
		return true;
	}

	/**
	 * execute
	 * 
	 * @param	void
	 * 
	 * @return	Enum
	**/
	public function execute()
	{
		$keyArr = $this->mRoot->mContext->mRequest->getRequest('key');
		$titleArr = $this->mRoot->mContext->mRequest->getRequest('title');
		$defaultArr = $this->mRoot->mContext->mRequest->getRequest('default');
	
		if(count($keyArr) == count($titleArr)){
			$valueArr = array();
			foreach(array_keys($keyArr) as $k){
				$valueArr['key'][$k] =$keyArr[$k];
				$valueArr['title'][$k] =$titleArr[$k];
				$valueArr['default'][$k] = isset($defaultArr[$k]) ? $defaultArr[$k] : 0;
			}
			if(! $this->_insertConfig('actions', serialize($valueArr))){
				return LECAT_FRAME_VIEW_ERROR;
			}
		}
		return LECAT_FRAME_VIEW_SUCCESS;
	}

	/**
	 * getActions
	 * 
	 * @param	void
	 * 
	 * @return	string[]
	**/
	protected function _getActions()
	{
		$actions = $this->mModule->getModuleConfig('actions');
		return isset($actions) ? unserialize($actions) : array('key'=>array('viewer','poster','manager'),'title'=>array('Viewer', 'Poster', 'Manager'),'default'=>array(1,1,0));
	}

	protected function _insertConfig($key, $value)
	{
		$handler = xoops_gethandler('config');
		$cri = new CriteriaCompo();
		$cri->add(new Criteria('conf_modid', $this->mRoot->mContext->mModule->mXoopsModule->get('mid')));
		$cri->add(new Criteria('conf_name', $key));
		$objs = $handler->getConfigs($cri);
		if(count($objs)>0){
			$obj =array_shift($objs);
		}
		elseif(count($objs)==0){
			$obj = $handler->createConfig();
			$obj->set('conf_modid', $this->mRoot->mContext->mModule->mXoopsModule->get('mid'));
			$obj->set('conf_name', $key);
		}
	
		$obj->set('conf_value', $value);
		return($handler->insertConfig($obj));
	}

	/**
	 * setHeaderScript
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function setHeaderScript()
	{
		$headerScript = $this->mRoot->mContext->getAttribute('headerScript');
		$headerScript->addStylesheet($this->_getStylesheet());
		$headerScript->addScript('actionsCounter='. count($this->_getActions()). ';',false);
		$headerScript->addScript('function addActionKeyForm() {$("#permitOptions").append("<tr><td><input type=\'text\' id=\'legacy_xoopsform_key["+actionsCounter+"]\' value=\'\' name=\'key["+actionsCounter+"]\'></td><td><input type=\'text\' id=\'legacy_xoopsform_title["+actionsCounter+"]\' value=\'\' name=\'title["+actionsCounter+"]\'></td><td><input type=\'checkbox\' id=\'legacy_xoopsform_default["+actionsCounter+"]\' value=\'1\' name=\'default["+actionsCounter+"]\'></td></tr>");actionsCounter++;}', false);
	}

	/**
	 * getDefaultView
	 * 
	 * @param	void
	 * 
	 * @return	Enum
	**/
	public function getDefaultView()
	{
		return LECAT_FRAME_VIEW_INPUT;
	}

	/**
	 * executeViewInput
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewInput(&$render)
	{
		$render->setTemplateName('setting_edit.html');
		$render->setAttribute('actions', $this->_getActions());
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('dirname', $this->mAsset->mDirname);
	}

	/**
	 * executeViewSuccess
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewSuccess(&$render)
	{
		$this->mRoot->mController->executeForward($this->_getForwardUrl());
	}

	/**
	 * executeViewError
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewError(&$render)
	{
		$this->mRoot->mController->executeRedirect($this->_getForwardUrl(), 1, 'error');
	}

	/**
	 * _getForwardUrl
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	protected function _getForwardUrl()
	{
		return './index.php?action=SettingEdit';
	}
}

?>