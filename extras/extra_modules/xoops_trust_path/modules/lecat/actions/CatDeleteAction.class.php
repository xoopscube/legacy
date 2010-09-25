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
