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
/***
 * @internal
 * This action shows the list of selectable themes to user.
 *
 * [Notice]
 * In XOOPS Cube Legacy which can have many themes with different render-
 * systems, that one render-system has the control to change themes is wrong,
 * because this action can't list up themes of other render-systems.
 * The action to change themes should be in Legacy. And, each render-systems
 * should send theme informations through delegate-mechanism.
 *
 * Therefore, this class is test for that we may move this action from
 * LegacyRender module. If you want to check the concept of this strategy, see
 * ThemeSelect preload in Legacy module.
 */
class Xupdate_Admin_ThemeFinderAction extends Xupdate_AbstractAction
{
	const THEME_FINDER_API_VERSION = '1';

	protected $themeFinderUrl = "http://cmsthemefinder.com/store/enter_store.php";

	public function __construct()
	{
		parent::__construct();
	}

	function prepare()
	{
		if ( defined('TP_THEME_FINDER_URL') === true ) {
			$this->themeFinderUrl = TP_THEME_FINDER_URL; // デバッグ用
		}
		parent::prepare();
		return true;
	}

	/**
	 * _setupActionForm
	 *
	 * @param   void
	 *
	 * @return  void
	**/
	function _setupActionForm()
	{
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
		$jQuery = $this->mRoot->mContext->getAttribute('headerScript');
		//$jQuery->addLibrary('/modules/'.$this->mAsset->mDirname.'/admin/js/ThemeFinder.js', true);
		$src =<<< HTML
jQuery(function($){
	$('#themeFinderIframe').show();
	$('#themeFinderForm').submit().remove();
});
HTML;
		$jQuery->addScript($src,false);

		return XUPDATE_FRAME_VIEW_INDEX;
	}

	/**
	 * executeViewIndex
	 *
	 * @param	XCube_RenderTarget	&$render
	 *
	 * @return	void
	**/
	function executeViewIndex(&$render)
	{

		$render->setTemplateName("admin_themefinder.html");

		$render->setAttribute('mod_config', $this->mod_config);
		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);

		//$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
		$render->setAttribute('currentMenu', _MI_XUPDATE_ADMENU_THEMEFINDER);

		$render->setAttribute("themeFinderUrl", Xupdate_Utils::toShow($this->themeFinderUrl));
		$render->setAttribute("themeFinderApiVersion", self::THEME_FINDER_API_VERSION);
		$render->setAttribute("addonManagerInstallUrl", Xupdate_Utils::toShow(XOOPS_MODULE_URL.'/'.$this->mAsset->mDirname.'/admin/index.php?action=ThemeFinderInstall&target_type=Theme&target_key='));
	}
}
