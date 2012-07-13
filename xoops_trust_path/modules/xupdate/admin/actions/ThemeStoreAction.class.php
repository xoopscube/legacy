<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH . '/class/AbstractListAction.class.php';

require_once XUPDATE_TRUST_PATH . '/include/ModulesIniDadaSet.class.php';

class Xupdate_Admin_ThemeStoreAction extends Xupdate_AbstractListAction
{
//ListView data
	var $sid ;
	var $mModuleObjects = array();
	var $mFilter = null;

	var $mActionForm = null;

	public function __construct()
	{
		parent::__construct();

		$this->sid = (int)$this->mRoot->mContext->mRequest->getRequest('sid');
		$this->sid = empty($this->sid)? 0 : intval($this->sid);

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
		$this->mActionForm =& $this->mAsset->getObject('form', 'ThemeStore',true);
		$this->mActionForm->prepare();
	}

	function &_getFilterForm()
	{
		$filter =& $this->mAsset->getObject('filter', 'ThemeStore' , true );
		$filter->prepare($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return './?action=ThemeStore';
	}

	function &_getPageNavi()
	{
		$navi =& parent::_getPageNavi();
		$navi->setPerpage(30);//TODO

		return $navi;
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
	protected function &_getHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'ThemeStore',false);
		return $handler;
	}

	function getDefaultView()
	{

//データの自動作成と削除

		$inidataset = new Xupdate_ModulesIniDadaSet;
		$inidataset->storeHand =  & $this->_getStoreHandler();
		$inidataset->modHand = & $this->_getHandler();
		$inidataset->execute( 'theme' );

//-----------------------------------------------

		$jQuery = $this->mRoot->mContext->getAttribute('headerScript');
		$jQuery->addScript($this->RapidModuleInstall_js(),false);

		$modHand = & $this->_getHandler();

		$this->mFilter = $this->_getFilterForm();
		$this->mFilter->fetch();

		$criteria = $this->mFilter->getCriteria();
		$criteria->add(new Criteria( 'target_type', 'Theme' ) );
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
		$render->setTemplateName('admin_theme_store.html');

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);

		$render->setAttribute('sid', $this->sid);

		$render->setAttribute('moduleObjects', $this->mModuleObjects);

		$modHand = & $this->_getHandler();

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'target_type', 'Theme' ) );
		if (!empty($this->sid)){
			$criteria->add(new Criteria( 'sid', $this->sid ) );
		}
		$module_total = $modHand->getCount($criteria);

		$render->setAttribute('pageNavi', $this->mFilter->mNavi);

		$render->setAttribute('ModuleTotal', $module_total);

		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());

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

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'target_type', 'Theme' ) );
		if (!empty($this->sid)){
			$criteria->add(new Criteria( 'sid', $this->sid ) );
		}
		$this->mModuleObjects =& $modHand->getObjects($criteria ,null , null , true);

		return XUPDATE_FRAME_VIEW_INPUT;
	}

	/**
	 * To support a template writer, this send the list of mid that actionForm kept.
	 */
	function executeViewInput(&$render)
	{

		$render->setTemplateName("admin_theme_store_confirm.html");

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);

		$render->setAttribute('sid', $this->sid);

		$render->setAttribute('moduleObjects', $this->mModuleObjects);
		$render->setAttribute('actionForm', $this->mActionForm);
		// To support a template writer, this send the list of id that
		// actionForm kept.
		$t_arr = $this->mActionForm->get('dirname');
		$render->setAttribute('ids', array_keys($t_arr));

		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());

	}

	function _processSave()
	{
		$modHand = & $this->_getHandler();

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'target_type', 'Theme' ) );
		if (!empty($this->sid)){
			$criteria->add(new Criteria( 'sid', $this->sid ) );
		}
		$t_objectArr =& $modHand->getObjects($criteria ,null , null , true);

		$successFlag = true;
		$newdata_dirname_arr = $this->mActionForm->get('dirname');
		foreach($newdata_dirname_arr  as $id => $new_dirname) {
			if (empty($new_dirname) || empty($id)){
				continue;
			}
			$obj=$t_objectArr[$id];
			$olddata['dirname'] = $obj->getVar('dirname');
			$newdata['dirname'] = $new_dirname;
			if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
				$obj->set('dirname', $new_dirname);
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
		if (empty($this->sid)){
			$this->mRoot->mController->executeForward('./index.php?action=ThemeStore');
		}else{
			$this->mRoot->mController->executeForward('./index.php?action=ThemeStore&sid='.$this->sid);
		}
	}

	function executeViewError(&$renderer)
	{
		if (empty($this->sid)){
			$this->mRoot->mController->executeRedirect('./index.php?action=ThemeStore', 1, _MD_XUPDATE_ERROR_DBUPDATE_FAILED);
		}else{
			$this->mRoot->mController->executeRedirect('./index.php?action=ThemeStore&sid='.$this->sid , 1, _MD_XUPDATE_ERROR_DBUPDATE_FAILED);
		}
	}
	function executeViewCancel(&$renderer)
	{
		if (empty($this->sid)){
			$this->mRoot->mController->executeForward('./index.php?action=ThemeStore');
		}else{
			$this->mRoot->mController->executeForward('./index.php?action=ThemeStore&sid='.$this->sid);
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

		$message_Install = _INSTALL;
		$message_Error = _ERRORS;
		$message_Waiting = _AD_XUPDATE_LANG_MESSAGE_WAITING;
		$message_Success = _AD_XUPDATE_LANG_MESSAGE_SUCCESS;
		$message_Getting_files = _AD_XUPDATE_LANG_MESSAGE_GETTING_FILES;
		$message_Processing = _AD_XUPDATE_LANG_MESSAGE_PROCESSING;
		$message_btn_manage = _MI_XUPDATE_ADMENU_THEME._MI_XUPDATE_MANAGE;

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
		if ( location.href.search('ThemeStore') == -1 && location.href.search(/legacy\/admin\/index.php$/) == -1 )
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
			var storehref = $(this).parent('td').parent('tr').find('a[href*="xupdate/admin/index.php?action=ThemeInstall"]').attr('href');
			var installhref = $(this).parent('td').parent('tr').find('a[href*="legacy/admin/index.php?action=Theme"]').attr('href');
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
		var result = $(html).find('#xupdate_addModule a').text();
		if (result == '{$message_btn_manage}'){
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