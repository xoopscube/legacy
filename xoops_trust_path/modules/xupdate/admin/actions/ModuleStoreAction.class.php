<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH . '/class/AbstractListAction.class.php';

class Xupdate_Admin_ModuleStoreAction extends Xupdate_AbstractListAction
{
	protected $Xupdate  ;	// Xupdate instance
	protected $Ftp  ;	// FTP instance
	protected $Func ;	// Functions instance
	protected $mod_config ;

	protected $items ;
	protected $sid = 1;
	protected $mSiteModuleObjects = array();

	var $mModuleObjects = array();
	var $mFilter = null;

	var $mActionForm = null;

	public function __construct()
	{
		parent::__construct();

		$this->mRoot =& XCube_Root::getSingleton();
		$this->mModule =& $this->mRoot->mContext->mModule;
		$this->mAsset =& $this->mModule->mAssetManager;

// Xupdate_ftp class object
//		require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		$this->Ftp = $this->Xupdate->Ftp ;		// FTP instance
		$this->Func = $this->Xupdate->func ;		// Functions instance
		$this->mod_config = $this->mRoot->mContext->mModuleConfig ;	// mod_config

		$jQuery = $this->mRoot->mContext->getAttribute('headerScript');
		$jQuery->addScript($this->RapidModuleInstall_js(),false);


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
		parent::prepare();
		$this->_setupActionForm();

		$this->sid = $this->_getId();
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
		$this->mActionForm =& $this->mAsset->getObject('form', 'ModuleStore',true);
		$this->mActionForm->prepare();
	}

	function &_getFilterForm()
	{
		$filter =& $this->mAsset->getObject('filter', 'ModuleStore' , true );
		$filter->prepare($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return './?action=ModuleStore';
	}

	function &_getPageNavi()
	{
		$navi =& parent::_getPageNavi();
		$navi->setPerpage(30);//TODO

		return $navi;
	}


	/**
	 * _getId
	 *
	 * @param   void
	 *
	 * @return  int
	**/
	protected function _getId()
	{
		$this->sid = (int)$this->mRoot->mContext->mRequest->getRequest('sid');
		$this->sid = empty($this->sid)? 1 : $this->sid;
		return $this->sid;
	}
	/**
	 * @protected
	 */
	protected function &_getHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'ModuleStore',false);
		return $handler;
	}


	function getDefaultView()
	{

//データの自動作成と削除
		//for test start ---------------------------
		include dirname(__FILE__) .'/modules.ini';
		//このストアーごとのアイテム配列をセットしてください
		//テストなので$sid = 1のみ
		$sid = empty($this->sid)? 1:  (int)$this->sid ;
		if ($sid != 1){
			$this->items = array();
		}else{
			$this->items[1] = $items;
		}
		//for test end ---------------------------
		//上記を使用して
		//登録済のデータをマージします
		$this->_setmSiteModuleObjects($sid);
		//未登録のデータは自動で登録
		foreach($this->items as $sid => $items){
			foreach($items as $key => $item){
				if ($item['type'] == 'TrustModule' ){
					$this->_setDataTrustModule($sid , $item);
				}else{
					$this->_setDataSingleModule($sid , $item);
				}
			}
		}
//-----------------------------------------------

		$modHand = & $this->_getHandler();

		$this->mFilter = $this->_getFilterForm();
		$this->mFilter->fetch();

		$criteria = $this->mFilter->getCriteria();
		if (!empty($this->sid)){
			$criteria->add(new Criteria( 'sid', $this->sid ) );
		}

		$this->mModuleObjects =& $modHand->getObjects($criteria);
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
		$render->setTemplateName('admin_module_store.html');

//		$render->setAttribute('xupdate_items', $this->items);
		$render->setAttribute('mod_config', $this->mod_config);

		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());

		$render->setAttribute('moduleObjects', $this->mModuleObjects);

		$modHand = & $this->_getHandler();

		if (empty( $this->sid)){
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria( 'sid', $this->sid ) );
			$module_total = $modHand->getCount($criteria);
		}else{
			$module_total = $modHand->getCount();
		}

		$render->setAttribute('pageNavi', $this->mFilter->mNavi);

		$render->setAttribute('ModuleTotal', $module_total);

		$render->setAttribute('actionForm', $this->mActionForm);


	}

	function execute()
	{
		$form_cancel = $this->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
		if ($form_cancel != null) {
			return XUPDATE_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if ($this->mActionForm->hasError()) {
			return $this->_processConfirm();
		} else {
			return $this->_processSave();
		}

	}

	function _processConfirm()
	{
		$modHand = & $this->_getHandler();

		if (empty( $this->sid)){
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria( 'sid', $this->sid ) );
			$this->mModuleObjects =& $modHand->getObjects($criteria ,null , null , true);
		}else{
			$this->mModuleObjects =& $modHand->getObjects(null ,null , null , true);
		}

		return XUPDATE_FRAME_VIEW_INPUT;
	}

	/**
	 * To support a template writer, this send the list of mid that actionForm kept.
	 */
	function executeViewInput(&$render)
	{
		$render->setTemplateName("admin_module_store_confirm.html");
		$render->setAttribute('moduleObjects', $this->mModuleObjects);
		$render->setAttribute('actionForm', $this->mActionForm);
		// To support a template writer, this send the list of id that
		// actionForm kept.
		//
		$t_arr = $this->mActionForm->get('dirname');
		$render->setAttribute('ids', array_keys($t_arr));
	}

	function _processSave()
	{
		$modHand = & $this->_getHandler();
		if (empty( $this->sid)){
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria( 'sid', $this->sid ) );
			$t_objectArr =& $modHand->getObjects($criteria);//TODO bug $id_as_key=true
		}else{
			$t_objectArr =& $modHand->getObjects();//TODO bug $id_as_key=true
		}

		$successFlag = true;
		$newdata_dirname_arr = $this->mActionForm->get('dirname');
		foreach($t_objectArr as $obj) {
			$id = $obj->getVar('id');
			$olddata['dirname'] = $obj->getVar('dirname');
			$newdata['dirname'] = $newdata_dirname_arr[$id];
			if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
				$obj->set('dirname', $newdata_dirname_arr[$id]);
				if ($modHand->insert($obj)) {
					$successFlag &= true;
				}else{
					$successFlag = false;
					break;
				}
			}
		}

		return $successFlag ? XUPDATE_FRAME_VIEW_SUCCESS : XUPDATE_FRAME_VIEW_ERROR;
	}

	function executeViewSuccess(&$renderer)
	{
		$this->mRoot->mController->executeForward('./index.php?action=ModuleStore');
	}

	function executeViewError(&$renderer)
	{
		$this->mRoot->mController->executeRedirect('./index.php?action=ModuleStore', 1, _MD_XUPDATE_ERROR_DBUPDATE_FAILED);
	}
	function executeViewCancel(&$renderer)
	{
		$this->mRoot->mController->executeForward('./index.php?action=ModuleStore');
	}

//------------------prepare
	private function _setmSiteModuleObjects($sid = null)
	{
		$modHand = & $this->_getHandler();
		//この該当サイト登録済みデータを全部確認する
		if ( empty($sid)){
			$siteModuleStoreObjects =& $modHand->getObjects();
		}else{
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria( 'sid', $sid ) );
			$siteModuleStoreObjects =& $modHand->getObjects($criteria);
		}
		if (empty($siteModuleStoreObjects)){
			return;
		}
		foreach($siteModuleStoreObjects as $id => $mobj){

			if (isset($this->items[$sid])){
				$is_sitedata = false;
				foreach($this->items[$sid] as $key => $item){
					if ($item['dirname'] == $mobj->getVar('target_key') ){
						$is_sitedata = true;
						break;
					}
				}
				//このサイトデータに無い
				if ($is_sitedata == false){
					$modHand->delete($mobj,true);
				}
			}

			if (isset($this->mSiteModuleObjects[$mobj->getVar('sid')][$mobj->getVar('target_key')][$mobj->getVar('dirname')])){
				//データ重複
				$modHand->delete($mobj,true);
			}else{
				$this->mSiteModuleObjects[$mobj->getVar('sid')][$mobj->getVar('target_key')][$mobj->getVar('dirname')]=$mobj;
			}
		}

	}

	private function _setDataSingleModule($sid , $item)
	{
		$modHand = & $this->_getHandler();
		//trustモジュールでない(複製可能なものはどうしよう)
		$mModuleStore = new $modHand->mClass();
		$mModuleStore->assignVars($item);
		$mModuleStore->assignVar('sid', $sid);
		$mModuleStore->assignVar('target_key',$item['dirname']);

		$mModuleStore->setmModule();

		if (isset($this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']])){
			$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']]->getVar('id') );
			$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']]);
		}else{
			$mModuleStore->setNew();
			$modHand->insert($mModuleStore ,true);
		}
		unset($mModuleStore);

	}
	private function _setDataTrustModule($sid ,$item)
	{
		$modHand = & $this->_getHandler();
		//インストール済みの同じtrustモージュールのリストを取得
		$list = Legacy_Utils::getDirnameListByTrustDirname($item['dirname']);

		if (empty($list)){
			//インストール済みの同じtrustモージュール無し、注意 is_active
			$mModuleStore = new $modHand->mClass();
			$mModuleStore->assignVars($item);
			$mModuleStore->set('sid',$sid);
			$mModuleStore->assignVar('trust_dirname',$item['dirname']);
			$mModuleStore->assignVar('target_key',$item['dirname']);

			$mModuleStore->setmModule();

			if (isset($this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']])){
				$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']]->getVar('id') );
				$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']]);
			}else{
				$mModuleStore->setNew();
				$modHand->insert($mModuleStore ,true);
			}
			unset($mModuleStore);

		}else{

			$_isrootdirmodule = false;
			foreach($list as $dirname){
				$mModuleStore = new $modHand->mClass();
				$mModuleStore->assignVars($item);
				$mModuleStore->assignVar('sid',$sid);

				$mModuleStore->assignVar('dirname',$dirname);
				$mModuleStore->assignVar('trust_dirname',$item['dirname']);
				$mModuleStore->assignVar('target_key',$item['dirname']);
				$mModuleStore->setmModule();

				if ( $dirname == $item['dirname'] ){
					$_isrootdirmodule = true;
				}
				if (isset($this->mSiteModuleObjects[$sid][$dirname])){
					$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$sid][$item['dirname']][$dirname]->getVar('id') );
					$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']]);
				}else{
					$mModuleStore->setNew();
					$modHand->insert($mModuleStore ,true);
				}
				unset($mModuleStore);
			}
			//そのままインストールしていない場合、そのまま追加可能なので
			if ( $_isrootdirmodule == false ){
				$mModuleStore = new $modHand->mClass();
				$mModuleStore->assignVars($item);
				$mModuleStore->assignVar('sid',$sid);

				$mModuleStore->assignVar('trust_dirname',$item['dirname']);
				$mModuleStore->assignVar('target_key',$item['dirname']);

				$mModuleStore->setmModule();

				if (isset($this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']])){
					$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']]->getVar('id') );
					$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$sid][$item['dirname']][$item['dirname']]);
				}else{
					$mModuleStore->setNew();
					$modHand->insert($mModuleStore ,true);
				}
				unset($mModuleStore);
			}
		}

	}

/*
 * このサイトのデータをデータベースに再セットする
 */
	private function _storeupdate ($obj , $oldobj)
	{
		$modHand = & $this->_getHandler();
		$newdata['type'] = $obj->getVar('type');
		$newdata['last_update'] = $obj->getVar('last_update');
		$newdata['version'] = $obj->getVar('version');
		$olddata['type'] = $oldobj->getVar('type');
		$olddata['last_update'] = $oldobj->getVar('last_update');
		$olddata['version'] = $oldobj->getVar('version');
		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->unsetNew();
			$modHand->insert($obj ,true);
		}

	}
	/**
	 * RapidModuleStore_js
	 *
	 * @param
	 *
	 * @return	void
	**/
	public function RapidModuleInstall_js()
	{

		$message_Install = _MI_XUPDATE_LANG_UPDATE;
		$message_Error = _ERRORS;
		$message_Waiting = _AD_XUPDATE_LANG_MESSAGE_WAITING;
		$message_Success = _AD_XUPDATE_LANG_MESSAGE_SUCCESS;
		$message_Getting_files = _AD_XUPDATE_LANG_MESSAGE_GETTING_FILES;
		$message_Processing = _AD_XUPDATE_LANG_MESSAGE_PROCESSING;

		$ret =<<< HTML
jQuery(function($){

	var rapidModuleInstallButton = '#rapidModuleInstallButton';

	var installationModuleTotal = 0;
	var installedModuleTotal  = 0;
	var installationModules = [];
	var installationModule  = null;
	var isInstallation = false;

	var main = function()
	{
		if ( location.href.search('ModuleStore') == -1 && location.href.search(/legacy\/admin\/index.php$/) == -1 )
		{
			return;
		}

		if ( $('#legacy_xoopsform_confirm').length > 0 )
		{
			return;
		}

		addDelegates();
	}

	var addDelegates = function()
	{
		$('body').delegate('#rapidModuleInstallButton', 'click', clickRapidModuleInstallButton)
						.delegate('#rapidInstallCheckboxAll', 'click', checkAll);
	}

	var clickRapidModuleInstallButton = function()
	{
		installationModuleTotal = $('.rapidInstallCheckbox:checked').length;
		installedModuleTotal  = 0;

		if ( installationModuleTotal < 1 )
		{
			return;
		}

		$(this).replaceWith('<span id="rapidInstallStatus">(<span class="total">'+installedModuleTotal+'</span>/'+installationModuleTotal+')インストール中</span>');

		$('.rapidInstallCheckbox:checked').each(function()
		{
			var storehref = $(this).parent('td').parent('tr').find('a[href*="xupdate/admin/index.php?action=ModuleInstall"]').attr('href');
			var installhref = $(this).parent('td').parent('tr').find('a[href*="legacy/admin/index.php?action=Module"]').attr('href');
			var td = $(this).parent('td');
			installationModules.push({'storehref':storehref , 'installhref':installhref , 'td':td , 'status':0});
		});

		$(installationModules).each(function()
		{
			this.td.html("{$message_Waiting}");
		});

		doInstall();
	}

	var doInstall = function()
	{
		$(installationModules).each(function()
		{
			if ( this.status == 1 || isInstallation == true )
			{
				return;
			}

			installationModule = this;
			isInstallation     = true;

			if (typeof installationModule.storehref != 'undefined'){

				installationModule.td.html("{$message_Getting_files}{$message_Processing}");
				try
				{
					$.ajax({
						type: 'GET',
						async:false,
						url: installationModule.storehref,
						success: getStoreConfirmFormSuccess,
						error: ajaxFailed
					});
				}
				catch ( e )
				{
					installationModule.td.html('<span style="color:red;">{$message_Error}</span>');
					installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
					updateModuleStatus();
				}
			}
			var result =installationModule.td.text();
			if (result != '{$message_Getting_files}{$message_Success}'){
				installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
				updateModuleStatus();
			}else{
				if (typeof installationModule.installhref != 'undefined'){

					installationModule.td.html("{$message_Install}{$message_Processing}");
					try
					{
						$.ajax({
							type: 'GET',
							url: installationModule.installhref,
							success: getConfirmFormSuccess,
							error: ajaxFailed
						});
					}
					catch ( e )
					{
						installationModule.td.html('<span style="color:red;">{$message_Error}</span>');
						installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
						updateModuleStatus();
					}
				}else{
						installationModule.td.html('<span style="color:red;">{$message_Error}</span>');
						installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
						updateModuleStatus();
				}
			}

		});
	}
	var getStoreConfirmFormSuccess = function(html)
	{
		var form = $(html).find('#contentBody form');
		var formdata = form.serialize();

		if (typeof installationModule.installhref != 'undefined'){
			$.ajax({
				type: 'POST',
				async:false,
				url: installationModule.storehref,
				data: formdata,
				success: getStoreSuccess,
				error: ajaxFailed
			});
		}
	}


	var getStoreSuccess = function(html)
	{
		var result = $(html).find('#contentBody a').text();
		if (result == '{$message_Install}'){
			installationModule.td.html('<span style="color:green;">{$message_Getting_files}{$message_Success}</span>');
		}else{
			installationModule.td.html('<span style="color:red;">{$message_Getting_files}{$message_Error}</span>');
		}
	}

	var getConfirmFormSuccess = function(html)
	{
		var form = $(html).find('#contentBody form');
		var formdata = form.serialize();

		if (typeof installationModule.installhref != 'undefined'){
			$.ajax({
				type: 'POST',
				url: installationModule.installhref,
				data: formdata,
				success: postFormSuccess,
				error: ajaxFailed
			});
		}
	}

	var postFormSuccess = function(html)
	{
		var result = $(html).find('li.legacy_module_message:last').text();
		installationModule.td.hide().html('<span style="color:green;">{$message_Success}</span>').fadeIn();
		installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
		updateModuleStatus();
	}

	var ajaxFailed = function(XMLHttpRequest, textStatus, errorThrown)
	{
		throw "Ajax{$message_Error}";
	}

	var installInstallStatus = function(installedModuleTotal, installationModuleTotal)
	{
		installedModuleTotal += 1;

		if ( installedModuleTotal == installationModuleTotal )
		{
			$('#rapidInstallStatus').text("{$message_Success}")
			return installedModuleTotal;
		}

		$('#rapidInstallStatus .total').text(installedModuleTotal);
		return installedModuleTotal;
	}

	var updateModuleStatus = function()
	{
		installationModule.status = 1;
		isInstallation = false;
		doInstall(); // Next module
	}

	var checkAll = function()
	{
		var isChecked = $(this).attr('checked');
		if ( isChecked == 'checked' )
		{
			$('.rapidInstallCheckbox').attr('checked', 'checked');
		}else{
			$('.rapidInstallCheckbox').attr('checked', false);
		}
	}


	main();
});
HTML;

		return $ret;
	}


} // end class

?>