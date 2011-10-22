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
class Lecat_ActorEditAction extends Lecat_AbstractEditAction
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
		$this->mActionForm =& $this->mAsset->getObject('form', 'actor', false, 'edit');
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
				if($keyArr[$k] && $titleArr[$k]){
					$valueArr['key'][$k] =$keyArr[$k];
					$valueArr['title'][$k] =$titleArr[$k];
					$valueArr['default'][$k] = isset($defaultArr[$k]) ? $defaultArr[$k] : 0;
				}
			}
			if(! $this->_insertConfig('actors', serialize($valueArr))){
				return LECAT_FRAME_VIEW_ERROR;
			}
		}
		return LECAT_FRAME_VIEW_SUCCESS;
	}

	/**
	 * getActors
	 * 
	 * @param	void
	 * 
	 * @return	string[]
	**/
	protected function _getActors()
	{
		$actors = $this->mModule->getModuleConfig('actors');
		return isset($actors) ? unserialize($actors) : array('key'=>array('viewer','poster','manager'),'title'=>array('Viewer', 'Poster', 'Manager'),'default'=>array(1,1,0));
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
		$actors = $this->_getActors();
		$headerScript->addScript('actorsCounter='. count($actors['key']). ';',false);
		$headerScript->addScript('function addActorKeyForm() {$("#permitOptions").append("<tr><td><input type=\'text\' id=\'legacy_xoopsform_key["+actorsCounter+"]\' value=\'\' name=\'key["+actorsCounter+"]\'></td><td><input type=\'text\' id=\'legacy_xoopsform_title["+actorsCounter+"]\' value=\'\' name=\'title["+actorsCounter+"]\'></td><td><input type=\'checkbox\' id=\'legacy_xoopsform_default["+actorsCounter+"]\' value=\'1\' name=\'default["+actorsCounter+"]\'></td></tr>");actorsCounter++;}', false);
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
		$render->setTemplateName($this->mAsset->mDirname . '_actor_edit.html');
		$render->setAttribute('actors', $this->_getActors());
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
		return Legacy_Utils::renderUri($this->mAsset->mDirname);
	}
}

?>