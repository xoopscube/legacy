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
class Xupdate_AbstractInstallAction extends Xupdate_AbstractAction
{
	protected $id;//modulestore key
	protected $sid;//store key

	protected $addon_url;
	protected $detail_url;

	protected $target_key;
	protected $target_type;
	protected $trust_dirname;
	protected $dirname;

	protected $unzipdirlevel;

	// for permission control
	protected $options = array();
	protected $writable_dir = array();
	protected $writable_file = array();
	protected $install_only = array();

	protected $contents;
	protected $action;
	protected $currentMenu;
	
	public function __construct()
	{
		parent::__construct();
		$this->contents = '';
		$this->currentMenu = '';
		$this->action = '';
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
		$handler =& $this->mAsset->getObject('handler', 'Store', false);
		return $handler;
	}
	/**
	 * @protected
	 */
	protected function &_getModuleStoreHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'ModuleStore', false);
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
		$jQuery = $this->mRoot->mContext->getAttribute('headerScript');
		$jQuery->addScript($this->modalBoxJs(), false);
		
		$this->id = intval($this->Xupdate->get('id'));
		$this->sid = intval($this->Xupdate->get('sid'));

		$modHand =& $this->_getModuleStoreHandler();
		$storeHand =  & $this->_getStoreHandler();
		
		$action = $this->action;
		
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
			$this->addon_url = $this->Func->_getDownloadUrl( $this->target_key, $mobj->get('addon_url') );
			$this->detail_url = $mobj->get('detail_url');
			if ($mobj->hasNeedUpdate()) {
				$this->version = $mobj->mModule->getRenderedVersion()  . ' &rarr; <strong class="legacy_module_versionMsg">' . $mobj->getRenderedVersion() . '</strong>';
			} else {
				$this->version = $mobj->getRenderedVersion();
			}
			if ($mobj->hasNeedUpdateDetail()) {
				$this->detailed_version = $mobj->modinfo['detailed_version']  . ' &rarr; <strong class="legacy_module_versionMsg">' . $mobj->options['detailed_version'] . '</strong>';
			} else {
				$this->detailed_version = $mobj->options['detailed_version'];
			}
			$this->description = $mobj->get('description');
			$this->screen_shot = $mobj->options['screen_shot'];
			
			$this->options = $mobj->options;
			
			$action = ucfirst($mobj->get('contents')).'Store';
			
			foreach($this->options['writable_dir'] as $_key => $_chk) {
				if (Xupdate_Utils::checkDirWritable($_chk)) {
					unset($this->options['writable_dir'][$_key]);
				}
			}
			foreach($this->options['delete_dir'] as $_key => $_chk) {
				if (! is_dir($_chk)) {
					unset($this->options['delete_dir'][$_key]);
				}
			}
			foreach($this->options['delete_file'] as $_key => $_chk) {
				if (! is_file($_chk)) {
					unset($this->options['delete_file'][$_key]);
				}
			}

			$sobj =& $storeHand->get($this->sid);
			if (is_object($sobj)){
				//$this->addon_url = $sobj->get('addon_url');
				$this->store_name = $sobj->get('name');
			}
		}
		//-------------------------------------------

		$render->setTemplateName('admin_'.$this->contents.'_install_confirm.html');

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);

		//TODO
		$render->setAttribute('id', $this->id);
		$render->setAttribute('sid', $this->sid);
		$render->setAttribute('store_name', $this->store_name);

		$render->setAttribute('addon_url', $this->addon_url);
		$render->setAttribute('detail_url', $this->detail_url);

		$render->setAttribute('target_key', $this->target_key);
		$render->setAttribute('target_type', $this->target_type);
		$render->setAttribute('trust_dirname', $this->trust_dirname);
		$render->setAttribute('dirname', $this->dirname);
		$render->setAttribute('version', $this->version);
		$render->setAttribute('detailed_version', $this->detailed_version);
		$render->setAttribute('description', $this->description);
		$render->setAttribute('screen_shot', $this->screen_shot);
		$render->setAttribute('action', $action);

		$render->setAttribute('options', $this->options );

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
		$render->setAttribute('actionForm', $this->mActionForm);

		$render->setAttribute('currentMenu', $this->currentMenu);
		$render->setAttribute('currentItem', $this->target_key);
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
		$xupdateFtpModuleInstall->html_only = $this->mActionForm->get('html_only');

		$this->id = intval($this->Xupdate->get('id'));
		$modHand =& $this->_getModuleStoreHandler();
		$mobj =& $modHand->get($this->id);
		if (is_object($mobj)){
			$this->dirname = $mobj->get('dirname');
			$this->options = $mobj->options;
			//adump($this->options);
			$_arr = $this->Xupdate->get('writable_dir');
			if(!empty($_arr) && count($_arr)>0){
				foreach ($_arr as $item){
					if (in_array( $item,$this->options['writable_dir'] ))	{
						$xupdateFtpModuleInstall->options['writable_dir'][] = $item;
					}
				}
			}
			$_arr = $this->Xupdate->get('writable_file');
			if(!empty($_arr) && count($_arr)>0){
				foreach ($_arr as $item){
					if (in_array( $item,$this->options['writable_file'] )){
						$xupdateFtpModuleInstall->options['writable_file'][] = $item;
					}
				}
			}
			$_arr = $this->Xupdate->get('install_only');
			if(!empty($this->options['install_only']) && count($this->options['install_only'])>0){
				// checked means allow overwrite
				$xupdateFtpModuleInstall->options['no_overwrite'] = array();
				$xupdateFtpModuleInstall->options['install_only'] = array();
				//if ( isset($mobj->mModule) && $mobj->mModule->get('isactive')==true ){
				if ( isset($mobj->mModule) ){
					foreach ($this->options['install_only'] as $item){
						$_key = 'no_overwrite';
						if (substr($item, -1) === '*') {
							$item = rtrim($item, '*');
							$_key = 'install_only';
						}
						if ( !is_array($_arr) || (is_array($_arr) && !in_array( $item, $_arr ))){
							$xupdateFtpModuleInstall->options[$_key][] = $item;
						}
					}
				}
			}
			$_arr = $this->Xupdate->get('delete_dir');
			if(!empty($_arr) && count($_arr)>0){
				foreach ($_arr as $item){
					if (in_array( $item,$this->options['delete_dir'] ))	{
						$xupdateFtpModuleInstall->options['delete_dir'][] = $item;
					}
				}
			}
			$_arr = $this->Xupdate->get('delete_file');
			if(!empty($_arr) && count($_arr)>0){
				foreach ($_arr as $item){
					if (in_array( $item,$this->options['delete_file'] ))	{
						$xupdateFtpModuleInstall->options['delete_file'][] = $item;
					}
				}
			}
			//adump($_arr, $this->options['install_only'], $xupdateFtpModuleInstall->options);
		}
		
		// for re-post on time out error
		$this->mActionForm->getToken();
		
		//execute
		if ($result = $xupdateFtpModuleInstall->execute($this->contents)) {
			$store_handler =& $this->_getStoreHandler();
			$store_handler->setNeedCacheRemake(true);
		}
		
		//--------------------------------//
		$render->setTemplateName('admin_' . $this->contents . '_install.html');

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);
		$render->setAttribute('xupdate_nextlink', $xupdateFtpModuleInstall->nextlink);

		$render->setAttribute('xupdate_content', $xupdateFtpModuleInstall->content);
		$render->setAttribute('xupdate_message', $xupdateFtpModuleInstall->Ftp->getMes());

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
		$render->setAttribute('actionForm', $this->mActionForm);

		$render->setAttribute('currentMenu', _MI_XUPDATE_ADMENU_MODULE);
		$render->setAttribute('currentItem', $mobj->get('target_key'));
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
		$this->mRoot->mController->executeForward('./index.php?action='.$this->action);
	}

}

?>
