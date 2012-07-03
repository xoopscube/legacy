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

require_once XUPDATE_TRUST_PATH . '/include/FtpModuleInstall.class.php';

/**
 * Xupdate_Admin_StoreAction
 *
 * @property mixed downloadUrlFormat
 */
class Xupdate_Admin_ThemeInstallAction extends Xupdate_AbstractAction
{

//	protected $Xupdate  ;	// Xupdate instance
//	protected $Ftp  ;	// FTP instance
//	protected $Func ;	// Functions instance
//	protected $mod_config ;

	protected $id;//modulestore key
	protected $sid;//store key

	protected $addon_url;

	protected $target_key;
	protected $target_type;
	protected $trust_dirname;
	protected $dirname;

	protected $unzipdirlevel;


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
	public function prepare()
	{
		parent::prepare();
		$this->_setupActionForm();
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
	protected function &_getModuleStoreHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'ModuleStore',false);
		return $handler;
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
		$this->mActionForm =& $this->mAsset->getObject('form', 'ModuleInstall', true);
		$this->mActionForm->prepare();
	}

	public function execute()
	{
		$form_cancel = $this->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
		if ($form_cancel != null) {
			return XUPDATE_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if ($this->mActionForm->hasError()) {
			return XUPDATE_FRAME_VIEW_INDEX;
		} else {
			return XUPDATE_FRAME_VIEW_SUCCESS;
		}
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

		return XUPDATE_FRAME_VIEW_INDEX;
	}
	/**
	 * executeViewIndex
	 *
	 * @param	XCube_RenderTarget	&$render
	 *
	 * @return	void
	**/
	public function executeViewIndex(&$render)
	{
		$this->id = intval($this->Xupdate->get('id'));
		$this->sid = intval($this->Xupdate->get('sid'));

		$modHand =& $this->_getModuleStoreHandler();
		$storeHand =  & $this->_getStoreHandler();

		$mobj =& $modHand->get($this->id);
		if (is_object($mobj)){
			$this->id = $mobj->get('id');
			$this->sid = $mobj->get('sid');

			$this->target_key = $mobj->get('target_key');
			$this->target_type = $mobj->get('target_type');
			$this->trust_dirname = $mobj->get('trust_dirname');

			$this->dirname = $this->Xupdate->get('dirname');
			$this->dirname = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->dirname ) ;
			if (empty($this->dirname)){
				$this->dirname = $mobj->get('dirname');
			}
			$this->unzipdirlevel = $mobj->get('unzipdirlevel');
				//adump($this->unzipdirlevel);
			$this->addon_url = $mobj->get('addon_url');

			$sobj =& $storeHand->get($this->sid);
			if (is_object($sobj)){
				//$this->addon_url = $sobj->get('addon_url');
			}
		}
		//-------------------------------------------

		$render->setTemplateName('admin_theme_install_confirm.html');

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);

		//TODO
		$render->setAttribute('id', $this->id);
		$render->setAttribute('sid', $this->sid);

		$render->setAttribute('addon_url', $this->addon_url);

		$render->setAttribute('target_key', $this->target_key);
		$render->setAttribute('target_type', $this->target_type);
		$render->setAttribute('trust_dirname', $this->trust_dirname);
		$render->setAttribute('dirname', $this->dirname);

		$render->setAttribute('unzipdirlevel', $this->unzipdirlevel);

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
		$render->setAttribute('actionForm', $this->mActionForm);
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

		$xupdateFtpModuleInstall = new Xupdate_FtpModuleInstall ;// Xupdate instance
		//setup
		$xupdateFtpModuleInstall->downloadDirPath = $this->Xupdate->params['temp_path'];
		$xupdateFtpModuleInstall->downloadUrlFormat = $this->mActionForm->get('addon_url');
		$xupdateFtpModuleInstall->target_key =  $this->mActionForm->get('target_key');
		$xupdateFtpModuleInstall->target_type = $this->mActionForm->get('target_type');
		$xupdateFtpModuleInstall->trust_dirname = $this->mActionForm->get('trust_dirname');
		$xupdateFtpModuleInstall->dirname = $this->mActionForm->get('dirname');

		$xupdateFtpModuleInstall->unzipdirlevel = $this->mActionForm->get('unzipdirlevel');

		//execute
		$result = $xupdateFtpModuleInstall->execute('theme');

		//--------------------------------//
		$render->setTemplateName('admin_theme_install.html');

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);
		$render->setAttribute('xupdate_nextlink', $xupdateFtpModuleInstall->nextlink);

		$render->setAttribute('xupdate_content', $xupdateFtpModuleInstall->content);
		$render->setAttribute('xupdate_message', $xupdateFtpModuleInstall->Ftp->getMes());

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
		$render->setAttribute('actionForm', $this->mActionForm);
	}

	/**
	 * executeViewCancel
	 *
	 * @param	XCube_RenderTarget	&$render
	 *
	 * @return	void
	 **/
	public function executeViewCancel(&$render)
	{
		$this->mRoot->mController->executeForward('./index.php?action=ThemeStore');
	}


}

?>