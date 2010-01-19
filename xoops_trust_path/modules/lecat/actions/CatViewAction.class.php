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

require_once LECAT_TRUST_PATH . '/class/AbstractViewAction.class.php';
require_once LECAT_TRUST_PATH . "/class/Permission.class.php";
/**
 * Lecat_CatViewAction
**/
class Lecat_CatViewAction extends Lecat_AbstractViewAction
{
	public $mPermit = null;

    /**
     * _getId
     * 
     * @param   void
     * 
     * @return  int
    **/
    protected function _getId()
    {
        return $this->mRoot->mContext->mRequest->getRequest('cat_id');
    }

    /**
     * &_getHandler
     * 
     * @param   void
     * 
     * @return  Lecat_CatHandler
    **/
    protected function &_getHandler()
    {
        $handler =& $this->mAsset->getObject('handler', 'cat');
        return $handler;
    }

    /**
     * preprare
     * 
     * @param   void
     * 
     * @return  bool
    **/
	public function prepare()
	{
		parent::prepare();
		$this->mObject->loadGr();
		$this->mObject->mGr->loadTree();
		$this->mObject->loadPcat();
	
		//for Permissions
		$this->mPermit->set('cat_id', $this->mObject->get('cat_id'));
		$this->_setupActionForm();
	}

    /**
     * _setupActionForm
     * 
     * @param   void
     * 
     * @return  void
    **/
	protected function _setupActionForm()
	{
		//for Permission
        $this->mActionForm =& $this->mAsset->getObject('form', 'permit',false,'edit');
		$this->mActionForm->prepare();
	}

	function _setupObject()
	{
		parent::_setupObject();
		$this->mPermit =Lecat_Utils::getLecatHandler('permit', $this->mAsset->mDirname)->create();
	}

    /**
     * executeViewSuccess
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewSuccess(/*** XCube_RenderTarget ***/ &$render)
    {
        $render->setTemplateName($this->mAsset->mDirname . '_cat_view.html');
	
		//format Permissions for html form
		$permissions = new Lecat_Permission($this->mAsset->mDirname, $this->mObject);
		$gPermit = ($this->mObject->getThisPermit()) ? $this->mObject->getThisPermit() : array();
		$permissions->setPermissions($gPermit);
	
		//set renders
		$render->setAttribute('dirname', $this->mAsset->mDirname);
		$render->setAttribute('object', $this->mObject);
		$render->setAttribute('childrenTree', $this->mObject->mGr->mTree);
		$render->setAttribute('permitObj', $permissions);
		//modules confinement
		$render->setAttribute('modulesArr', $this->mObject->getModuleArr());
	
		//for permit addition
		$this->mActionForm->load($this->mPermit);
		$render->setAttribute('actionFormPermit', $this->mActionForm);
	
		//set CSS and Javascript
		///TODO XCL2.2
		$jQuery = $this->mRoot->mContext->getAttribute('jQuery');
		//$jQuery->appendCss('/modules/'. $this->mAsset->mDirname .'/lecat.css');
    }

    /**
     * executeViewError
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewError(/*** XCube_RenderTarget ***/ &$render)
    {
        $this->mRoot->mController->executeRedirect('./index.php?action=CatList', 1, _MD_LECAT_ERROR_CONTENT_IS_NOT_FOUND);
    }
}

?>
