<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractAction.class.php';

require_once XUPDATE_TRUST_PATH . '/include/ModulesIniDadaSet.class.php';

/**
 * Xupdate_Admin_StoreAction
**/
class Xupdate_Admin_ModuleViewAction extends Xupdate_AbstractAction
{

//	protected $stores ;
//	protected $items ;

	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * prepare
	 *
	 * @param   void
	 *
	 * @return  bool
	**/
	function prepare()
	{
		//データの自動作成と削除
		$inidataset = new Xupdate_ModulesIniDadaSet;
		$inidataset->storeHand =  & $this->_getStoreHandler();
		$inidataset->modHand = & $this->_getModStoreHandler();
		$inidataset->execute('module');
		//-----------------------------------------------
		return true;

	}
	/**
	 * @protected
	 */
	protected function &_getStoreHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'Store',false);
		return $handler;
	}
	/**
	 * @protected
	 */
	protected function &_getModStoreHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'ModuleStore',false);
		return $handler;
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
		return XUPDATE_FRAME_VIEW_SUCCESS;
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
		$render->setTemplateName('admin_module_view.html');

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);

		$render->setAttribute('xupdate_items', $this->get_storeItems());

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());

	}


	private function get_storeItems()
	{
		$store_mod_arr=array();
		$storeHand =  & $this->_getStoreHandler();
		$modHand = & $this->_getModStoreHandler();

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'contents', 'module' ) );

		$storeObjects =& $storeHand->getObjects($criteria,null,null,true);

		foreach($storeObjects as $sid => $store){
			$store_mod_arr[$sid]['storeobj']=$store;

			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria( 'sid', $sid ) );
			$siteModuleStoreObjects =& $modHand->getObjects($criteria);

			$itemsobj = array();
			foreach($siteModuleStoreObjects as $key => $mobj){
				$itemsobj[]=$mobj;
			}
			$store_mod_arr[$sid]['itemsobj']=$itemsobj;
			$store_mod_arr[$sid]['items_count']=count($itemsobj);
		}
		return $store_mod_arr;
	}

}

?>