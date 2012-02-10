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
// Xupdate_ftp class object
require_once XUPDATE_TRUST_PATH . '/class/Root.class.php';

require_once XUPDATE_TRUST_PATH . '/class/FtpModuleInstall.class.php';

/**
 * Xupdate_Admin_StoreAction
*
 * @property mixed downloadUrlFormat
 */
class Xupdate_Admin_ModuleInstallAction extends Xupdate_AbstractAction
{

//	protected $Xupdate  ;	// Xupdate instance
//	protected $Ftp  ;	// FTP instance
//	protected $Func ;	// Functions instance
//	protected $mod_config ;

	protected $target_key;
	protected $target_type;
	protected $trust_dirname;
	protected $dirname;

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
	 * @public
	 */
	protected function &_getHandler()
	{
//		$handler =& $this->mAsset->getObject('handler', 'ModuleStore',false);
//		return $handler;
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

	public function getDefaultView()
	{

		$this->target_key = $this->Xupdate->get('target_key');
		$this->target_key = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->target_key ) ;
		$this->target_type = $this->Xupdate->get('target_type');
		$this->target_type = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->target_type ) ;

		$this->trust_dirname = $this->Xupdate->get('trust_dirname');
		$this->trust_dirname = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->trust_dirname ) ;
		$this->dirname = $this->Xupdate->get('dirname');
		$this->dirname = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $this->dirname ) ;

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
		$xupdateRoot = new Xupdate_Root ;// Xupdate instance

		$render->setTemplateName('admin_module_install_confirm.html');

		$render->setAttribute('mod_config', $this->mod_config);

		$render->setAttribute('xupdate_writable', $xupdateRoot->params['is_writable']);

		//TODO
		$render->setAttribute('id', 1);//TEST dummy
		$render->setAttribute('sid', 1);//TEST dummy

		$render->setAttribute('target_key', $this->target_key);
		$render->setAttribute('target_type', $this->target_type);
		$render->setAttribute('trust_dirname', $this->trust_dirname);
		$render->setAttribute('dirname', $this->dirname);

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
		$this->target_key = $this->mActionForm->get('target_key');
		$this->target_type = $this->mActionForm->get('target_type');
		$this->trust_dirname = $this->mActionForm->get('trust_dirname');
		$this->dirname = $this->mActionForm->get('dirname');

		$xupdateFtpModuleInstall = new Xupdate_FtpModuleInstall ;// Xupdate instance
		//setup
		$xupdateFtpModuleInstall->downloadDirPath = $this->Xupdate->params['temp_path'];
		$xupdateFtpModuleInstall->downloadUrlFormat = $this->mod_config['Mod_download_Url_format'];

		$xupdateFtpModuleInstall->target_key = $this->target_key;
		$xupdateFtpModuleInstall->target_type = $this->target_type;
		$xupdateFtpModuleInstall->trust_dirname = $this->trust_dirname;
		$xupdateFtpModuleInstall->dirname = $this->dirname;
		//execute
		$result = $xupdateFtpModuleInstall->execute();

		$render->setAttribute('xupdate_writable', $xupdateFtpModuleInstall->Xupdate->params['is_writable']);
		$render->setAttribute('xupdate_content', $xupdateFtpModuleInstall->content);
		$render->setAttribute('xupdate_message', $xupdateFtpModuleInstall->Ftp->getMes());

		$render->setTemplateName('admin_module_install.html');
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
	}

	/**
	 * executeViewCancel
	 *
	 * @param	XCube_RenderTarget	&$render
	 *
	 * @return	void
	 **/
	public function executeViewCancel(&$renderer)
	{
		$this->mRoot->mController->executeForward('./index.php?action=ModuleView');
	}


}

?>