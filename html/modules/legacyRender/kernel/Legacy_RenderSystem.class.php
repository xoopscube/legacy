<?php
/**
 * @version $Id: Legacy_RenderSystem.class.php,v 1.4 2008/08/26 15:58:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH."/modules/legacyRender/kernel/Legacy_RenderTarget.class.php";
require_once XOOPS_ROOT_PATH . "/class/template.php";

/**
 * If a module handling banners can not work perfectly in your site, change the following
 * "false" to "true". (For Bug#1786123)
 */
define("LEGACY_RENDERSYSTEM_BANNERSETUP_BEFORE", false);

/**
 * @brief The sub-class for Legacy_RenderSystem. 
 * 
 * Because XoopsTpl class may be used without Cube's boot, this is declared.
 */
class Legacy_XoopsTpl extends XoopsTpl
{
	/**
	 * @private
	 * If variables having the following key are assigned, converts value with
	 * htmlspecialchars_decode, and set it to the context for compatibility.
	 */
	var $_mContextReserve = array();
	
	function Legacy_XoopsTpl()
	{
		$this->_mContextReserve = array ('xoops_pagetitle' => 'legacy_pagetitle');
		parent::XoopsTpl();
	}
	
	function assign($tpl_var, $value = null)
	{
		if (is_array($tpl_var)){
			foreach ($tpl_var as $key => $val) {
				if ($key != '') {
					$this->assign($key, $val);
				}
			}
		}
		else {
			if ($tpl_var != '') {
				if (isset($this->_mContextReserve[$tpl_var])) {
					$root =& XCube_Root::getSingleton();
					$root->mContext->setAttribute($this->_mContextReserve[$tpl_var], htmlspecialchars_decode($value));
				}
				$this->_tpl_vars[$tpl_var] = $value;
			}
		}
	}
	
	function assign_by_ref($tpl_var, &$value)
	{
		if ($tpl_var != '') {
			if (isset($this->_mContextReserve[$tpl_var])) {
				$root =& XCube_Root::getSingleton();
				$root->mContext->setAttribute($this->_mContextReserve[$tpl_var], htmlspecialchars_decode($value));
			}
			$this->_tpl_vars[$tpl_var] =& $value;
		}
	}
	
	function &get_template_vars($name = null)
	{
		$root =& XCube_Root::getSingleton();
		if (!isset($name)) {
			foreach ($this->_mContextReserve as $t_key => $t_value) {
				if (isset($this->_mContextReserve[$t_value])) {
					$this->_tpl_vars[$t_key] = htmlspecialchars($root->mContext->getAttribute($this->_mContextReserve[$t_value]), ENT_QUOTES);
				}
			}
			$value =& parent::get_template_vars($name);
		}
		elseif (isset($this->_mContextReserve[$name])) {
			$value = htmlspecialchars($root->mContext->getAttribute($this->_mContextReserve[$name]), ENT_QUOTES);
		}
		else {
			$value =& parent::get_template_vars($name);
		}
		return $value;
	}
}

/**
 * Compatible render system with XOOPS 2 Themes & Templates.
 *
 * This manages theme and main render-target directly. And, this realizes
 * variable-sharing-mechanism with using smarty.
 */
class Legacy_RenderSystem extends XCube_RenderSystem
{
	var $mXoopsTpl;

	/**
	 * Temporary
	 */
	var $mThemeRenderTarget;
	
	/**
	 * Temporary
	 */
	var $mMainRenderTarget;
	
	var $_mContentsData = null;

	/**
	 * @type XCube_Delegate
	 */
	var $mSetupXoopsTpl = null;
	
	/**
	 * @private
	 */
	var $_mIsActiveBanner = false;

	var $mBeginRender = null;
	
	function Legacy_RenderSystem()
	{
		parent::XCube_RenderSystem();
		$this->mSetupXoopsTpl =new XCube_Delegate();
		$this->mSetupXoopsTpl->register('Legacy_RenderSystem.SetupXoopsTpl');

		$this->mBeginRender =new XCube_Delegate();
		$this->mBeginRender->register("Legacy_RenderSystem.BeginRender");
	}
	
	function prepare(&$controller)
	{
		parent::prepare($controller);
		
		$root =& $this->mController->mRoot;
		$context =& $root->getContext();
		$textFilter =& $root->getTextFilter();
		
		// XoopsTpl default setup
		if ( isset($GLOBALS['xoopsTpl']) ) {
			$this->mXoopsTpl =& $GLOBALS['xoopsTpl'];
		} else {
			$this->mXoopsTpl =new Legacy_XoopsTpl();
		}
		$mTpl = $this->mXoopsTpl;
		$mTpl->register_function("legacy_notifications_select", "LegacyRender_smartyfunction_notifications_select");
		$this->mSetupXoopsTpl->call(new XCube_Ref($mTpl));

		// compatible
		$GLOBALS['xoopsTpl'] =& $mTpl;
		
		$mTpl->xoops_setCaching(0);

		// If debugger request debugging to me, send debug mode signal by any methods.
		if ($controller->mDebugger->isDebugRenderSystem()) {
			$mTpl->xoops_setDebugging(true);
		}
		
   		$mTpl->assign(array('xoops_requesturi' => htmlspecialchars($GLOBALS['xoopsRequestUri'], ENT_QUOTES),	//@todo ?????????????
							// set JavaScript/Weird, but need extra <script> tags for 2.0.x themes
							'xoops_js' => '//--></script><script type="text/javascript" src="'.XOOPS_URL.'/include/xoops.js"></script><script type="text/javascript"><!--'
						));
	
		$mTpl->assign('xoops_sitename', $textFilter->toShow($context->getAttribute('legacy_sitename')));
		$mTpl->assign('xoops_pagetitle', $textFilter->toShow($context->getAttribute('legacy_pagetitle')));
		$mTpl->assign('xoops_slogan', $textFilter->toShow($context->getAttribute('legacy_slogan')));

		// --------------------------------------
		// Meta tags
		// --------------------------------------
		$moduleHandler =& xoops_gethandler('module');
		$legacyRender =& $moduleHandler->getByDirname('legacyRender');
		
		if (is_object($legacyRender)) {
			$configHandler =& xoops_gethandler('config');
			$configs =& $configHandler->getConfigsByCat(0, $legacyRender->get('mid'));
			
			//
			// If this site has the setting of banner.
			// TODO this process depends on XOOPS 2.0.x.
			//
			$this->_mIsActiveBanner = $configs['banners'];
			if (LEGACY_RENDERSYSTEM_BANNERSETUP_BEFORE == true) {
				if ($configs['banners'] == 1) {
					$mTpl->assign('xoops_banner',xoops_getbanner());
				}
				else {
					$mTpl->assign('xoops_banner','&nbsp;');
				}
			}
		}
		else {
			$mTpl->assign('xoops_banner','&nbsp;');
		}
		
		// --------------------------------------
		// Add User
		// --------------------------------------
		$arr = null;
		if (is_object($context->mXoopsUser)) {
			$arr = array(
				'xoops_isuser' => true,
				'xoops_userid' => $context->mXoopsUser->getShow('uid'),
				'xoops_uname' => $context->mXoopsUser->getShow('uname')
			);
		}
		else {
			$arr = array(
				'xoops_isuser' => false
			);
		}
		
		$mTpl->assign($arr);
	}

	function setAttribute($key,$value)
	{
		$this->mRenderTarget->setAttribute($key,$value);
	}
	
	function getAttribute($key)
	{
		$this->mRenderTarget->getAttribute($key);
	}

	/**
	 * @protected
	 * Assign common variables for the compatibility with X2.
	 */
	function _commonPrepareRender()
	{
		$root =& $this->mController->mRoot;
		$context =& $root->getContext();
		$textFilter =& $root->getTextFilter();

		$themeName = $context->getThemeName();
		$mTpl = $this->mXoopsTpl;
   		$mTpl->assign('xoops_theme', $themeName);
   		$mTpl->assign('xoops_imageurl', XOOPS_THEME_URL . "/${themeName}/");
   		$mTpl->assign('xoops_themecss', xoops_getcss($themeName));

		$mTpl->assign('xoops_sitename', $textFilter->toShow($context->getAttribute('legacy_sitename')));
		$mTpl->assign('xoops_pagetitle', $textFilter->toShow($context->getAttribute('legacy_pagetitle')));
		$mTpl->assign('xoops_slogan', $textFilter->toShow($context->getAttribute('legacy_slogan')));

		//
		// Assign module informations.
		//
		if($context->mModule != null) {	// The process of module
			$xoopsModule =& $context->mXoopsModule;
			$mTpl->assign(array('xoops_modulename' => $xoopsModule->getShow('name'),
										   'xoops_dirname' => $xoopsModule->getShow('dirname')));
		}
		
		if (isset($GLOBALS['xoopsUserIsAdmin'])) {
			$mTpl->assign('xoops_isadmin', $GLOBALS['xoopsUserIsAdmin']);
		}
	}
	
	function renderBlock(&$target)
	{
		$this->_commonPrepareRender();
		
		//
		// Temporary
		//
		$mTpl = $this->mXoopsTpl;
		$mTpl->xoops_setCaching(0);

		foreach($target->getAttributes() as $key=>$value) {
			$mTpl->assign($key,$value);
		}

		$this->mBeginRender->call(new XCube_Ref($mTpl));
		$result=&$mTpl->fetchBlock($target->getTemplateName(),$target->getAttribute("bid"));
		$target->setResult($result);
		
		//
		// Reset
		//
		foreach($target->getAttributes() as $key=>$value) {
			$mTpl->clear_assign($key);
		}
	}
	
	function _render(&$target)
	{
		foreach($target->getAttributes() as $key=>$value) {
			$this->mXoopsTpl->assign($key,$value);
		}

		$this->mBeginRender->call(new XCube_Ref($this->mXoopsTpl), $target->getAttribute('legacy_buffertype'));
		$result=$this->mXoopsTpl->fetch("db:".$target->getTemplateName());
		$target->setResult($result);

		foreach ($target->getAttributes() as $key => $value) {
			$this->mXoopsTpl->clear_assign($key);
		}
	}
	
	function render(&$target)
	{
		//
		// The following lines are temporary until we will finish changing the style!
		//
		switch ($target->getAttribute('legacy_buffertype')) {
			case XCUBE_RENDER_TARGET_TYPE_BLOCK:
				$this->renderBlock($target);
				break;

			case XCUBE_RENDER_TARGET_TYPE_MAIN:
				$this->renderMain($target);
				break;

			case XCUBE_RENDER_TARGET_TYPE_THEME:
				$this->renderTheme($target);
				break;

			case XCUBE_RENDER_TARGET_TYPE_BUFFER:
			default:
				break;
		}
	}

	function renderMain(&$target)
	{
		$this->_commonPrepareRender();
		
		$cachedTemplateId = isset($GLOBLAS['xoopsCachedTemplateId']) ? $GLOBLAS['xoopsCachedTemplateId'] : null;

		foreach($target->getAttributes() as $key=>$value) {
			$this->mXoopsTpl->assign($key,$value);
		}

		if ($target->getTemplateName()) {
			if ($cachedTemplateId!==null) {
				$contents=$this->mXoopsTpl->fetch('db:'.$target->getTemplateName(), $xoopsCachedTemplateId);
			} else {
				$contents=$this->mXoopsTpl->fetch('db:'.$target->getTemplateName());
			}
		} else {
			if ($cachedTemplateId!==null) {
				$this->mXoopsTpl->assign('dummy_content', $target->getAttribute("stdout_buffer"));
				$contents=$this->mXoopsTpl->fetch($GLOBALS['xoopsCachedTemplate'], $xoopsCachedTemplateId);
			} else {
				$contents=$target->getAttribute("stdout_buffer");
			}
		}
		
		$target->setResult($contents);
	}

	function renderTheme(&$target)
	{
		$this->_commonPrepareRender();
	
		//jQuery Ready functions
		$mRoot = $this->mController->mRoot;
		$mContext = $mRoot->mContext;
		XCube_DelegateUtils::call("Site.JQuery.AddFunction", new XCube_Ref($mContext->mAttributes['headerScript']));
		$headerScript = $mContext->getAttribute('headerScript');
		$mTpl = $this->mXoopsTpl;
		$moduleHeader = $mTpl->get_template_vars('xoops_module_header');
		$moduleHeader =  $headerScript->createLibraryTag() . $moduleHeader . $headerScript->createOnloadFunctionTag();
		$mTpl->assign('xoops_module_header', $moduleHeader);
		
		$moduleHandler =& xoops_gethandler('module');
		$legacyRender =& $moduleHandler->getByDirname('legacyRender');
		$configHandler =& xoops_gethandler('config');
		$configs =& $configHandler->getConfigsByCat(0, $legacyRender->get('mid'));
	
		$textFilter =& $this->mController->mRoot->getTextFilter();
		$headerScript = $this->mController->mRoot->mContext->getAttribute('headerScript');//echo $headerScript->getMeta('author');die();
		$headerScript->getMeta('keywords') ? $this->mXoopsTpl->assign('xoops_meta_keywords', $textFilter->toShow($headerScript->getMeta('keywords'))) : $this->mXoopsTpl->assign('xoops_meta_keywords', $textFilter->toShow($configs['meta_keywords']));
		$headerScript->getMeta('description') ? $this->mXoopsTpl->assign('xoops_meta_description', $headerScript->getMeta('description')) : $this->mXoopsTpl->assign('xoops_meta_description', $textFilter->toShow($configs['meta_description']));
		$headerScript->getMeta('robots') ? $this->mXoopsTpl->assign('xoops_meta_robots', $textFilter->toShow($headerScript->getMeta('robots'))) : $this->mXoopsTpl->assign('xoops_meta_robots', $textFilter->toShow($configs['meta_robots']));
		$headerScript->getMeta('rating') ? $this->mXoopsTpl->assign('xoops_meta_rating', $textFilter->toShow($headerScript->getMeta('rating'))) : $this->mXoopsTpl->assign('xoops_meta_rating', $textFilter->toShow($configs['meta_rating']));
		$headerScript->getMeta('author') ? $this->mXoopsTpl->assign('xoops_meta_author', $textFilter->toShow($headerScript->getMeta('author'))) : $this->mXoopsTpl->assign('xoops_meta_author', $textFilter->toShow($configs['meta_author']));
		$headerScript->getMeta('copyright') ? $this->mXoopsTpl->assign('xoops_meta_copyright', $textFilter->toShow($headerScript->getMeta('copyright'))) : $this->mXoopsTpl->assign('xoops_meta_copyright', $textFilter->toShow($configs['meta_copyright']));
		$this->mXoopsTpl->assign('xoops_footer', $configs['footer']); // footer may be raw HTML text.
	
		//
		// If this site has the setting of banner.
		// TODO this process depends on XOOPS 2.0.x.
		//
		if (LEGACY_RENDERSYSTEM_BANNERSETUP_BEFORE == false) {
			if ($this->_mIsActiveBanner == 1) {
				$mTpl->assign('xoops_banner',xoops_getbanner());
			}
			else {
				$mTpl->assign('xoops_banner','&nbsp;');
			}
		}

		//
		// Assign from attributes of the render-target.
		//
		foreach($target->getAttributes() as $key => $value) {
			$mTpl->assign($key, $value);
		}
		
		//
		// [TODO]
		// We must implement with a render-target.
		//
		// $this->_processLegacyTemplate();

		// assing
		/// @todo I must move these to somewhere.
		$assignNameMap = array(
				XOOPS_SIDEBLOCK_LEFT=>array('showflag'=>'xoops_showlblock','block'=>'xoops_lblocks'),
				XOOPS_CENTERBLOCK_LEFT=>array('showflag'=>'xoops_showcblock','block'=>'xoops_clblocks'),
				XOOPS_CENTERBLOCK_RIGHT=>array('showflag'=>'xoops_showcblock','block'=>'xoops_crblocks'),
				XOOPS_CENTERBLOCK_CENTER=>array('showflag'=>'xoops_showcblock','block'=>'xoops_ccblocks'),
				XOOPS_SIDEBLOCK_RIGHT=>array('showflag'=>'xoops_showrblock','block'=>'xoops_rblocks')
			);

		foreach($assignNameMap as $key=>$val) {
			$mTpl->assign($val['showflag'],$this->_getBlockShowFlag($val['showflag']));
			if(isset($mContext->mAttributes['legacy_BlockContents'][$key])) {
				foreach($mContext->mAttributes['legacy_BlockContents'][$key] as $result) {
					$mTpl->append($val['block'], $result);
				}
			}
		}

		$this->mBeginRender->call(new XCube_Ref($mTpl));
		
		//
		// Render result, and set it to the RenderBuffer of the $target.
		//
		$result=null;
		if($target->getAttribute("isFileTheme")) {
			$result=$mTpl->fetch($target->getTemplateName()."/theme.html");
		}
		else {
			$result=$mTpl->fetch("db:".$target->getTemplateName());
		}
		
		$result .= $mTpl->fetchDebugConsole();

		$target->setResult($result);
	}

	function _getBlockShowFlag($area) {
		switch($area) {
			case 'xoops_showrblock' :
				if (isset($GLOBALS['show_rblock']) && empty($GLOBALS['show_rblock'])) return 0;
				return (!empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_SIDEBLOCK_RIGHT])) ? 1 : 0;
				break;
			case 'xoops_showlblock' :
				if (isset($GLOBALS['show_lblock']) && empty($GLOBALS['show_lblock'])) return 0;
				return (!empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_SIDEBLOCK_LEFT])) ? 1 : 0;
				break;
			case 'xoops_showcblock' :
				if (isset($GLOBALS['show_cblock']) && empty($GLOBALS['show_cblock'])) return 0;
				return (!empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_CENTERBLOCK_LEFT])||
						!empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_CENTERBLOCK_RIGHT])||
						!empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_CENTERBLOCK_CENTER])) ? 1 : 0;
				break;
			default :
				return 0;
		}
	}
	//
	// There must not be the following functions here!
	//
	//

	/**
	 * @deprecated
	 */
	function sendHeader()
	{
		header('Content-Type:text/html; charset='._CHARSET);
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}

	/**
	 * @deprecated
	 */
	function showXoopsHeader($closeHead=true)
	{
		global $xoopsConfig;
		$myts =& MyTextSanitizer::getInstance();
		if ($xoopsConfig['gzip_compression'] == 1) {
			ob_start("ob_gzhandler");
		} else {
			ob_start();
		}

		$this->sendHeader();
		$this->_renderHeader($closeHead);
	}
	
	// TODO never direct putput
	/**
	 * @deprecated
	 */
	function _renderHeader($closehead=true)
	{
		global $xoopsConfig, $xoopsTheme, $xoopsConfigMetaFooter;

		echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";

		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'._LANGCODE.'" lang="'._LANGCODE.'">
		<head>
		<meta http-equiv="content-type" content="text/html; charset='._CHARSET.'" />
		<meta http-equiv="content-language" content="'._LANGCODE.'" />
		<meta name="robots" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_robots']).'" />
		<meta name="keywords" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_keywords']).'" />
		<meta name="description" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_desc']).'" />
		<meta name="rating" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_rating']).'" />
		<meta name="author" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_author']).'" />
		<meta name="copyright" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_copyright']).'" />
		<meta name="generator" content="XOOPS" />
		<title>'.htmlspecialchars($xoopsConfig['sitename']).'</title>
		<script type="text/javascript" src="'.XOOPS_URL.'/include/xoops.js"></script>
		';
		$themecss = getcss($xoopsConfig['theme_set']);
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/xoops.css" />';
		if ($themecss) {
			echo '<link rel="stylesheet" type="text/css" media="all" href="'.$themecss.'" />';
			//echo '<style type="text/css" media="all"><!-- @import url('.$themecss.'); --></style>';
		}
		if ($closehead) {
			echo '</head><body>';
		}
	}
	
	/**
	 * @deprecated
	 */
	function _renderFooter()
	{
		echo '</body></html>';
		ob_end_flush();
	}
	
	/**
	 * @deprecated
	 */
	function showXoopsFooter()
	{
		$this->_renderFooter();
	}

	function &createRenderTarget($type = LEGACY_RENDER_TARGET_TYPE_MAIN, $option = null)
	{
		$renderTarget = null;
		switch ($type) {
			case XCUBE_RENDER_TARGET_TYPE_MAIN:
				$renderTarget =new Legacy_RenderTargetMain();
				break;
				
			case LEGACY_RENDER_TARGET_TYPE_BLOCK:
				$renderTarget =new XCube_RenderTarget();
				$renderTarget->setAttribute('legacy_buffertype', LEGACY_RENDER_TARGET_TYPE_BLOCK);
				break;
				
			default:
				$renderTarget =new XCube_RenderTarget();
				break;
		}

		return $renderTarget;
	}
	
	/**
	 * @TODO This function is not cool!
	 */
	function &getThemeRenderTarget($isDialog = false)
	{
		$screenTarget = $isDialog ? new Legacy_DialogRenderTarget() : new Legacy_ThemeRenderTarget();
		return $screenTarget;
	}
}

function LegacyRender_smartyfunction_notifications_select($params, &$smarty)
{
	$root =& XCube_Root::getSingleton();
	$renderSystem =& $root->getRenderSystem('Legacy_RenderSystem');
	
	$renderTarget =& $renderSystem->createRenderTarget('main');
	$renderTarget->setTemplateName("legacy_notification_select_form.html");

	XCube_DelegateUtils::call('Legacyfunction.Notifications.Select', new XCube_Ref($renderTarget));

	$renderSystem->render($renderTarget);
	
	return $renderTarget->getResult();
}

?>
