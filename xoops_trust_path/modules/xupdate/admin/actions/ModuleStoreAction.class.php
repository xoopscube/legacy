<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH . '/class/AbstractListAction.class.php';
require_once XUPDATE_TRUST_PATH . "/admin/forms/Admin_ModuleStoreFilterForm.class.php";
require_once XUPDATE_TRUST_PATH . "/admin/forms/Admin_ModuleStoreForm.class.php";

class Xupdate_Admin_ModuleStoreAction extends Xupdate_AbstractListAction
{
	protected $Xupdate  ;	// Xupdate instance
	protected $Ftp  ;	// FTP instance
	protected $Func ;	// Functions instance
	protected $mod_config ;
	protected $content ;
	protected $items ;

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
		//	adump($this->mod_config);
		//	adump($this->Ftp);

		$jQuery = $this->mRoot->mContext->getAttribute('headerScript');
		$jQuery->addScript($this->RapidModuleInstall_js(),false);


	}
	function prepare()
	{

		$this->mActionForm =new Xupdate_Admin_ModuleStoreForm();
		$this->mActionForm->prepare();
	}

	function &_getFilterForm()
	{
		$filter = new Xupdate_Admin_ModuleStoreFilterForm();
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
		$navi->setPerpage(10);

		return $navi;
	}


	/**
	 * @protected
	 */
	protected function &_getHandler()
	{
		$handler =& $this->mAsset->load('handler', "ModuleStore");
		return $handler;
	}


	function getDefaultView()
	{
		$this->mFilter = $this->_getFilterForm();
		$this->mFilter->fetch();

		$modHand = & $this->_getHandler();
		$this->mModuleObjects =& $modHand->getObjects($this->mFilter->getCriteria());
		return XUPDATE_FRAME_VIEW_SUCCESS;
	}

	function execute(&$controller, &$xoopsUser)
	{
/*
		$form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
		if ($form_cancel != null) {
			return XUPDATE_FRAME_VIEW_CANCEL;
		}
*/
		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if ($this->mActionForm->hasError()) {
			return XUPDATE_FRAME_VIEW_ERROR;
		}
		else {
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
		$render->setTemplateName('admin_module_store.html');

		$render->setAttribute('xupdate_items', $this->items);

		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);
		$render->setAttribute('xupdate_content', $this->content);

		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());

		$render->setAttribute('moduleObjects', $this->mModuleObjects);

		$modHand = & $this->_getHandler();
		$module_total = $modHand->getCount();
//		$active_module_total = $modHand->getCount(new Criteria('isactive', 1));
		$render->setAttribute('ModuleTotal', $module_total);
//		$render->setAttribute('activeModuleTotal', $active_module_total );
//		$render->setAttribute('inactiveModuleTotal', $module_total - $active_module_total);


		$render->setAttribute('actionForm', $this->mActionForm);


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
			installationModules.push({'storehref':storehref,'installhref':installhref, 'td':td, 'status':0});
		});

		$(installationModules).each(function()
		{
			this.td.html("待機中");
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

				installationModule.td.html("展開中");
				try
				{
					$.ajax({
						type: 'GET',
						async:false,
						url: installationModule.storehref,
						success: getStoreSuccess,
						error: ajaxFailed
					});
				}
				catch ( e )
				{
					installationModule.td.html('<span style="color:red;">エラー</span>');
					installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
					updateModuleStatus();
				}
			}


			if (typeof installationModule.installhref != 'undefined'){

				installationModule.td.html("インストール中");
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
					installationModule.td.html('<span style="color:red;">エラー</span>');
					installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
					updateModuleStatus();
				}
			}else{
					installationModule.td.html('<span style="color:red;">スキップ</span>');
					installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
					updateModuleStatus();
			}

		});
	}
	var getStoreSuccess = function(html)
	{
		installationModule.td.hide().html('<span style="color:green;">展開完了</span>').fadeIn();
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
		installationModule.td.hide().html('<span style="color:green;">完了</span>').fadeIn();
		installedModuleTotal = installInstallStatus(installedModuleTotal, installationModuleTotal);
		updateModuleStatus();
	}

	var ajaxFailed = function(XMLHttpRequest, textStatus, errorThrown)
	{
		throw "Ajaxエラー";
	}

	var installInstallStatus = function(installedModuleTotal, installationModuleTotal)
	{
		installedModuleTotal += 1;

		if ( installedModuleTotal == installationModuleTotal )
		{
			$('#rapidInstallStatus').text("完了")
			return installedModuleTotal;
		}

		$('#rapidInstallStatus .total').text(installedModuleTotal);
		return installedModuleTotal;
	}

	var updateModuleStatus = function()
	{
		installationModule.status = 1;
		isInstallation = false;
		doInstall(); // 次のモジュールへ
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