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
		$inidataset->execute('all', ($this->mRoot->mContext->mRequest->getRequest('checkonly')));
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
		if ($this->mRoot->mContext->mRequest->getRequest('checkonly')) {
			while( ob_get_level() && @ ob_end_clean() ){
			}
			header('Content-type: image/gif');
			header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT');
			header('pragma: no-cache');
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			readfile(XOOPS_ROOT_PATH . '/images/blank.gif');
			exit();
		}
		
		$render->setTemplateName('admin_module_view.html');

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);

		$render->setAttribute('module_items', $this->get_storeItems('module'));
		$render->setAttribute('theme_items', $this->get_storeItems('theme'));
		$render->setAttribute('package_items', $this->get_storeItems('package'));

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
		$render->setAttribute('currentMenu', _MI_XUPDATE_ADMENU_STORELIST);

	}


	private function get_storeItems($contents)
	{
		$store_mod_arr=array();
		$storeHand =  & $this->_getStoreHandler();
		$modHand = & $this->_getModStoreHandler();

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'contents', $contents ) );
		$criteria->setSort('sid');
		$criteria->setOrder('ASC');

		$storeObjects =& $storeHand->getObjects($criteria,null,null,true);

		foreach($storeObjects as $sid => $store){
			$store_mod_arr[$sid]['storeobj']=$store;

			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria( 'sid', $sid ) );
			$criteria->setSort('dirname');
			$criteria->setOrder('ASC');
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