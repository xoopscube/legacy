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

require_once LECAT_TRUST_PATH . '/actions/CatViewAction.class.php';

/**
 * Lecat_DefaultSetAction
**/
class Lecat_DefaultSetAction extends Lecat_CatViewAction
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
        return 0;
    }

    /**
     * _getPermit
     * 
     * @param   int		$groupId
     * 
     * @return  Lecat_PermitObject[]
    **/
	protected function _getPermit($groupid=0)
	{
		$handler = Legacy_Utils::getModuleHandler('permit', $this->mAsset->mDirname);
		$criteria=new CriteriaCompo();
		$criteria->add(new Criteria('cat_id', 0));
		if(intval($groupid)>0){
			$criteria->add(new Criteria('groupid', $groupid));
		}
		return $handler->getObjects($criteria);
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
		$this->mPermit =Legacy_Utils::getModuleHandler('permit', $this->mAsset->mDirname)->create();
		//for Permissions
		$this->mPermit->set('cat_id', 0);
		$this->_setupActionForm();
	}

    /**
     * getDefaultView
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function getDefaultView()
    {
        return LECAT_FRAME_VIEW_SUCCESS;
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
        $render->setTemplateName($this->mAsset->mDirname . '_default_set.html');
	
		//format Permissions for html form
		$permissions = new Lecat_Permission($this->mAsset->mDirname, $this->_getHandler()->create());
		$permissions->setPermissions($this->_getPermit());
	
		//set renders
		$render->setAttribute('dirname', $this->mAsset->mDirname);
		$render->setAttribute('permitObj', $permissions);
	
		//for permit addition
		$this->mActionForm->load($this->mPermit);
		$render->setAttribute('actionFormPermit', $this->mActionForm);
    }
}

?>
