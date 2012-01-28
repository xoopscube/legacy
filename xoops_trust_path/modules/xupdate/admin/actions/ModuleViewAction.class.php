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

/**
 * Xupdate_Admin_StoreAction
**/
class Xupdate_Admin_ModuleViewAction extends Xupdate_AbstractAction
{

	protected $Xupdate  ;	// Xupdate instance
	protected $Ftp  ;	// FTP instance
	protected $Func ;	// Functions instance
	protected $mod_config ;
	protected $content ;
	protected $items ;

	/**
	 * getDefaultView
	 * 
	 * @param	void
	 * 
	 * @return	Enum
	**/


	public function __construct()
	{
		parent::__construct();

		$this->mRoot =& XCube_Root::getSingleton();
        	$this->mModule =& $this->mRoot->mContext->mModule;
        	$this->mAsset =& $this->mModule->mAssetManager;
 
		// Xupdate_ftp class object
		require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		$this->Ftp = $this->Xupdate->Ftp ;		// FTP instance
		$this->Func = $this->Xupdate->func ;		// Functions instance
		$this->mod_config = $this->mRoot->mContext->mModuleConfig ;	// mod_config
		//	adump($this->mod_config);
		//	adump($this->Ftp);
	}

	public function execute(&$controller, &$xoopsUser)
	{
	}

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
		$this->_storelist();
		$render->setAttribute('xupdate_items', $this->items);

		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);
		$render->setAttribute('xupdate_content', $this->content);

		$render->setTemplateName('admin_module_view.html');
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
	}

	/**
	 * @public
	 */
	protected function &_getHandler()
	{
	//	$handler =& $this->mAsset->load('handler', "Module");
	//	return $handler;
	}

	private function _storelist ()
	{
		include dirname(__FILE__) .'/modules.ini';

       		$this->items = $this->get_storeItems($items);
	}

	private function get_storeItems($items)
	{
		foreach ($items as $item ){
		        // for test
       	 	$item['url'] = XOOPS_URL .'/modules/'.$this->mAsset->mDirname.'/admin/index.php?action=ModuleInstall&target_key='
					.$item['dirname'] .'&target_type='.$item['type'];
			$rtn_items[] = $item;
		}
		return $rtn_items;
	}

}

?>