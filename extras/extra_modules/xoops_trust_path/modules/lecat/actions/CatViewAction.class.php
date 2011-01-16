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
	protected $_mDataname = 'cat';
	public $mPermit = null;

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
		$this->mPermit =Legacy_Utils::getModuleHandler('permit', $this->mAsset->mDirname)->create();
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
		$render->setAttribute('childrenTree', $this->mObjectHandler->getTree());
		$render->setAttribute('permitObj', $permissions);
		//modules confinement
		$render->setAttribute('modulesArr', $this->mObject->getModuleArr());
	
		//for permit addition
		$this->mActionForm->load($this->mPermit);
		$render->setAttribute('actionFormPermit', $this->mActionForm);
	
		$list = array();
		$clientList = Lecat_Utils::getClientList($this->mAsset->mDirname);
		$render->setAttribute('clientList', $clientList);
		$list = array('title'=>array(), 'template_name'=>array(), 'data'=>array(), 'dirname'=>array(), 'dataname'=>array());
		foreach($clientList as $client){
			$list = $this->mObject->getClientData($client, $list);
		}
		$render->setAttribute('clients', $list);
    }
}

?>
