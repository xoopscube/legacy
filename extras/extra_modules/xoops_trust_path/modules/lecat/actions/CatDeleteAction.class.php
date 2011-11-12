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

require_once LECAT_TRUST_PATH . '/class/AbstractDeleteAction.class.php';

/**
 * Lecat_CatDeleteAction
**/
class Lecat_CatDeleteAction extends Lecat_AbstractDeleteAction
{
	protected $_mDataname = 'cat';

	/**
	 * delete category data ?
	 * 
	 * @param	void
	 * 
	 * @return	bool
	**/
	protected function _isForce()
	{
		$force = $this->mRoot->mContext->mRequest->getRequest('force');
		return isset($force) ? true : false;
	}

	/**
	 * &_getHandler
	 * 
	 * @param	void
	 * 
	 * @return	Lecat_CatHandler
	**/
	protected function &_getHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'cat');
		return $handler;
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
		// $this->mActionForm =new Lecat_CatDeleteForm();
		$this->mActionForm =& $this->mAsset->getObject('form', 'cat',false,'delete');
		$this->mActionForm->prepare();
	}

    /**
     * prepare
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function prepare()
    {
        if(! parent::prepare() && is_object($this->mObject)) return false;
    
    	//check child categories
    	$this->mObject->loadChildren();
    	if(count($this->mObject->mChildren)>0 && $this->_isForce()===false){
			$this->mRoot->mController->executeRedirect($this->_getNextUri($this->_mDataname, 'list'), 1, _MD_LECAT_ERROR_HAS_CHILDREN);
    	}
    
    	//check client data
        if($this->mObject->hasClientData()){
			$this->mRoot->mController->executeRedirect($this->_getNextUri($this->_mDataname, 'list'), 1, _MD_LECAT_ERROR_HAS_CLIENT_DATA);
        }
        return true;
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
		$render->setTemplateName($this->mAsset->mDirname . '_cat_delete.html');
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		$render->setAttribute('dirname', $this->mAsset->mDirname);
	}
}

?>
