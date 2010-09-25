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
 * Lecat_PermitEditAction
**/
class Lecat_PermitEditAction extends Lecat_AbstractEditAction
{
	var $mCatId = 0;

	/**
	 * &_getHandler
	 * 
	 * @param	void
	 * 
	 * @return	Lecat_PermitHandler
	**/
	protected function &_getHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'permit');
		return $handler;
	}

	function prepare()
	{
		parent::prepare();
		//if no cat_id and no permit_id is requested, it is invalid request.
		$catId = $this->mRoot->mContext->mRequest->getRequest('cat_id');
		if(! isset($catId) && ! $this->_getId()){
			$this->mRoot->mController->executeRedirect("./index.php?action=CatList", 1, _MD_LECAT_ERROR_NO_CATEGORY_REQUESTED);
		}
	
		$this->mCatId = $this->mRoot->mContext->mRequest->getRequest('cat_id');
	}

	/**
	 * _setupActionForm
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	protected function _setupActionForm()
	{
		// $this->mActionForm =new Lecat_PermitEditForm();
		$this->mActionForm =& $this->mAsset->getObject('form', 'permit',false,'edit');
		$this->mActionForm->prepare();
	}

	/**
	 * executeViewInput
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewInput(/*** XCube_RenderTarget ***/ &$render)
	{
		$render->setTemplateName($this->mAsset->mDirname . '_permit_edit.html');
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		$render->setAttribute('dirname', $this->mAsset->mDirname);
	}

	/**
	 * executeViewSuccess
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewSuccess(/*** XCube_RenderTarget ***/ &$render)
	{
		if($this->mCatId==0){
			$this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'Default', 0, 'Set'));
		}
		else{
			$this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'cat', $this->mCatId));
		}
	}

	/**
	 * executeViewError
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewError(/*** XCube_RenderTarget ***/ &$render)
	{
		$this->mRoot->mController->executeRedirect(Legacy_Utils::renderUri($this->mAsset->mDirname, 'cat', $this->mCatId), 1, _MD_LECAT_ERROR_DBUPDATE_FAILED);
	}

	/**
	 * executeViewCancel
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewCancel(/*** XCube_RenderTarget ***/ &$render)
	{
		$this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'cat', $this->mCatId));
	}

	function execute()
	{
		//get the list of user groups
		$groupHandler = xoops_gethandler('member');
		$groups =& $groupHandler->getGroups();
	
		//set group permissions
		foreach(array_keys($groups) as $key){	//$key:groupid
			$groupId = $groups[$key]->get('groupid');
			if(! $this->_getHandler()->updatePermission($this->mCatId, $groupId, $this->mRoot->mContext->mRequest->getRequest('permission'))){
				return LECAT_FRAME_VIEW_ERROR;
			}
		}
	
		return LECAT_FRAME_VIEW_SUCCESS;
	
	}

}

?>
