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

require_once XUPDATE_TRUST_PATH . '/include/FtpThemeFinderInstall.class.php';

/**
 * Xupdate_Admin_StoreAction
 *
 * @property mixed downloadUrlFormat
 */
class Xupdate_Admin_ThemeFinderInstallAction extends Xupdate_AbstractAction
{

//	protected $Xupdate  ;	// Xupdate instance
//	protected $Ftp  ;	// FTP instance
//	protected $Func ;	// Functions instance
//	protected $mod_config ;


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
	 * _setupActionForm
	 *
	 * @param   void
	 *
	 * @return  void
	**/
	protected function _setupActionForm()
	{
		$this->mActionForm =& $this->mAsset->getObject('form', 'ThemeFinderInstall', true);
		$this->mActionForm->prepare();
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
		$jQuery = $this->mRoot->mContext->getAttribute('headerScript');
		$jQuery->addScript($this->modalBoxJs(), false);

		$render->setTemplateName('admin_themefinder_install_confirm.html');

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);
		
		$target_key = intval($this->Xupdate->get('target_key'));
		$render->setAttribute('addon_url', $this->mod_config['Theme_download_Url_format']);
		$render->setAttribute('addon_url', $this->Func->_getDownloadUrl($target_key, $this->mod_config['Theme_download_Url_format']));
		$render->setAttribute('target_key', $target_key);
		$render->setAttribute('target_type', 'Theme');

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
		$render->setAttribute('actionForm', $this->mActionForm);
	}

	/**
	 * execute
	 *
	 * @param	void
	 *
	 * @return	Enum
	**/
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
	 * executeViewSuccess
	 *
	 * @param	XCube_RenderTarget	&$render
	 *
	 * @return	void
	 **/
	public function executeViewSuccess(&$render)
	{

		$render->setTemplateName('admin_themefinder_install.html');

		$xupdateFtp = new Xupdate_FtpThemeFinderInstall ;// Xupdate instance
		//setup
		$xupdateFtp->downloadDirPath = $this->Xupdate->params['temp_path'];

		$xupdateFtp->downloadUrlFormat = $this->mActionForm->get('addon_url');

		$xupdateFtp->target_key =  $this->mActionForm->get('target_key');
		$xupdateFtp->target_type = $this->mActionForm->get('target_type');
		//execute
		$result = $xupdateFtp->execute();

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);
		$render->setAttribute('xupdate_nextlink', $xupdateFtp->nextlink);

		$render->setAttribute('xupdate_content', $xupdateFtp->content);
		$render->setAttribute('xupdate_message', $xupdateFtp->Ftp->getMes());

		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());

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
		$this->mRoot->mController->executeForward('./index.php?action=ThemeFinder');
	}


}

?>