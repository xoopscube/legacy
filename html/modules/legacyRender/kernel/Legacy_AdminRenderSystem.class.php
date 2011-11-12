<?php
/**
 * @package Legacy
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

//
// TODO
//
require_once XOOPS_ROOT_PATH . "/modules/legacyRender/kernel/Legacy_RenderSystem.class.php";

define('LEGACY_ADMIN_RENDER_TEMPLATE_DIRNAME', "templates");

define('LEGACY_ADMIN_RENDER_FALLBACK_PATH', XOOPS_MODULE_PATH . "/legacy/admin/theme");
define('LEGACY_ADMIN_RENDER_FALLBACK_URL', XOOPS_MODULE_URL . "/legacy/admin/theme");

require_once SMARTY_DIR."/Smarty.class.php";

/**
 * @internal
 * @public
 * @brief The special extended smarty class for Legacy_AdminRenderSystem.
 * This class extends Smarty to mediate the collision compiled file name.
 */
class Legacy_AdminSmarty extends Smarty
{
	var $mModulePrefix = null;

	//
	// If you don't hope to override for theme, set false.
	//
	var $overrideMode = true;
	
	function Legacy_AdminSmarty()
	{
		parent::Smarty();

		$this->compile_id = XOOPS_URL;
		$this->_canUpdateFromFile = true;
		$this->compile_check = true;
		$this->compile_dir = XOOPS_COMPILE_PATH;
		$this->left_delimiter = "<{";
		$this->right_delimiter = "}>";
		$this->plugins_dir = array(SMARTY_DIR.'plugins', XOOPS_ROOT_PATH.'/class/smarty/plugins');

		//
		// [TODO]
		//	If we don't set true to the following flag, a user can not recover
		// with deleting additional theme. But, a user should to select true or
		// false by site_custom.ini.php.
		//
		$this->force_compile = false;
	}
	
	function setModulePrefix($prefix)
	{
		$this->mModulePrefix = $prefix;
	}
	
	function _get_auto_filename($autoBase, $autoSource = null, $auotId = null)
	{
		$autoSource = $this->mModulePrefix . "_admin_" . $autoSource;
		return parent::_get_auto_filename($autoBase, $autoSource, $auotId);
	}

	function _fetch_resource_info(&$params)
	{
		$_return = false;

		$root =& XCube_Root::getSingleton();
		$theme = $root->mSiteConfig['Legacy']['Theme'];
		$dirname = $this->mModulePrefix;
		
		if ($dirname != null) {
			$params['resource_base_path'] = XOOPS_THEME_PATH . "/" . $theme . "/modules/" . $dirname;
			$params['quiet'] = true;
			
			$_return = parent::_fetch_resource_info($params);
		}

		if (!$_return) {
			unset ($params['resource_base_path']);
			$params['quiet'] = false;
			
			$_return = parent::_fetch_resource_info($params);
		}
		
		return $_return;
	}
	
}

/**
 * @brief The specific FILE-TYPE render-system.
 * @todo We depends on Legacy_RenderSystem that a add-in module defines. We must stop this situation.
 */
class Legacy_AdminRenderSystem extends Legacy_RenderSystem
{
	var $mController;
	var $mSmarty;
	
	/**
	 * This is the buffer which stores standard output when the render-target
	 * in renderMain() doesn't use a template.
	 * 
	 * @access private
	 * @var string
	 */
	var $_mStdoutBuffer = null;
	
	function prepare(&$controller)
	{
		$this->mController =& $controller;
		
		$this->mSmarty =new Legacy_AdminSmarty();
		$this->mSmarty->register_modifier("theme", "Legacy_modifier_theme");
		$this->mSmarty->register_function("stylesheet", "Legacy_function_stylesheet");

		$this->mSmarty->assign(array(
			"xoops_url" 	   => XOOPS_URL,
			"xoops_rootpath"   => XOOPS_ROOT_PATH,
			"xoops_langcode"   => _LANGCODE,
			"xoops_charset"    => _CHARSET,
			"xoops_version"    => XOOPS_VERSION,
			"xoops_upload_url" => XOOPS_UPLOAD_URL)
		);

		if ($controller->mRoot->mSiteConfig['Legacy_AdminRenderSystem']['ThemeDevelopmentMode'] == true) {
			$this->mSmarty->force_compile = true;
		}
	}
	
	function renderBlock(&$target)
	{
		$this->mSmarty->template_dir = XOOPS_ROOT_PATH . "/modules/legacy/admin/templates";

		foreach ($target->getAttributes() as $key => $value) {
			$this->mSmarty->assign($key, $value);
		}
		
		$this->mSmarty->setModulePrefix($target->getAttribute('legacy_module'));
		$result = $this->mSmarty->fetch("blocks/" . $target->getTemplateName());
		$target->setResult($result);

		//
		// Reset
		//
		foreach($target->getAttributes() as $key => $value) {
			$this->mSmarty->clear_assign($key);
		}
	}
	
	function renderTheme(&$target)
	{
		//
		// Assign from attributes of the render-target.
		//
		foreach($target->getAttributes() as $key=>$value) {
			$this->mSmarty->assign($key,$value);
		}
		
		$this->mSmarty->assign('stdout_buffer', $this->_mStdoutBuffer);
	
		//jQuery Ready functions
		XCube_DelegateUtils::call("Site.JQuery.AddFunction", new XCube_Ref($this->mController->mRoot->mContext->mAttributes['headerScript']));
		$headerScript = $this->mController->mRoot->mContext->getAttribute('headerScript');
		$moduleHeader =  $headerScript->createLibraryTag() . $headerScript->createOnloadFunctionTag();
		$this->mSmarty->assign('xoops_module_header', $moduleHeader);
	
		//
		// Get a virtual current module object from the controller and assign it.
		//
		$moduleObject =& $this->mController->getVirtualCurrentModule();
		$this->mSmarty->assign("currentModule", $moduleObject);

		//
		// Other attributes
		//
		$this->mSmarty->assign('legacy_sitename', $this->mController->mRoot->mContext->getAttribute('legacy_sitename'));
		$this->mSmarty->assign('legacy_pagetitle', $this->mController->mRoot->mContext->getAttribute('legacy_pagetitle'));
		$this->mSmarty->assign('legacy_slogan', $this->mController->mRoot->mContext->getAttribute('legacy_slogan'));
		
		//
		// Theme rendering
		//
		$blocks = array();
		foreach($this->mController->mRoot->mContext->mAttributes['legacy_BlockContents'][0] as $key => $result) {
			// $this->mSmarty->append('xoops_lblocks', $result);
			$blocks[$result['name']] = $result;
		}
		$this->mSmarty->assign('xoops_lblocks', $blocks);

		//
		// Check Theme or Fallback
		//
		$root =& XCube_Root::getSingleton();
		$theme = $root->mSiteConfig['Legacy']['Theme'];
		
		if (file_exists(XOOPS_ROOT_PATH."/themes/".$theme."/admin_theme.html")) {
			$this->mSmarty->template_dir=XOOPS_THEME_PATH."/".$theme;
		}
		else {
			$this->mSmarty->template_dir=LEGACY_ADMIN_RENDER_FALLBACK_PATH;
		}

		$this->mSmarty->setModulePrefix('');
		$result=$this->mSmarty->fetch("file:admin_theme.html");

		$target->setResult($result);
	}

	function renderMain(&$target)
	{
		//
		// Assign from attributes of the render-target.
		//
		foreach ($target->getAttributes() as $key=>$value) {
			$this->mSmarty->assign($key, $value);
		}
		
		$result = null;
		
		if ($target->getTemplateName()) {
			if ($target->getAttribute('legacy_module') != null) {
				$this->mSmarty->setModulePrefix($target->getAttribute('legacy_module'));
				$this->mSmarty->template_dir = XOOPS_MODULE_PATH . "/" . $target->getAttribute('legacy_module') . "/admin/". LEGACY_ADMIN_RENDER_TEMPLATE_DIRNAME;
			}
			
			$result=$this->mSmarty->fetch("file:".$target->getTemplateName());
			$buffer = $target->getAttribute("stdout_buffer");
			
			$this->_mStdoutBuffer .= $buffer;
		}
		else {
			$result=$target->getAttribute("stdout_buffer");
		}
		
		$target->setResult($result);

		//
		// Clear assign.
		//
		foreach ($target->getAttributes() as $key=>$value) {
			$this->mSmarty->clear_assign($key);
		}
	}
}

/***
 * @internal
 * Return URL string by "overriding" rule.
 * (Now, test implement)
 * 1) Search file in specified theme directory.
 * 2) Search file in current module template directory.
 * 3) Search file in fallback theme directory.
 */
function Legacy_modifier_theme($string)
{
	$infoArr = Legacy_get_override_file($string);
	
	if ($infoArr['theme'] != null && $infoArr['dirname'] != null) {
		return XOOPS_THEME_URL . "/" . $infoArr['theme'] . "/modules/" . $infoArr['dirname'] . "/" . $string;
	}
	elseif ($infoArr['theme'] != null) {
		return XOOPS_THEME_URL . "/" . $infoArr['theme'] . "/" . $string;
	}
	elseif ($infoArr['dirname'] != null) {
		return XOOPS_MODULE_URL . "/" . $infoArr['dirname'] . "/admin/templates/" . $string;
	}
	
	return LEGACY_ADMIN_RENDER_FALLBACK_URL . "/" . $string;
}

function Legacy_function_stylesheet($params, &$smarty)
{
	if (!isset($params['file'])) {
		$smarty->trigger_error("stylesheet: missing file parameter.");
		return;
	}
	
	$file = $params['file'];
	
	if (strstr($file, "..") !== false) {
		$smarty->trigger_error("stylesheet: missing file parameter.");
		return;
	}
	
	$media = (isset($params['media'])) ? $params['media'] : "all";

	$infoArr = Legacy_get_override_file($file, "stylesheets/");

	// TEMP
	// TODO We must return FALLBACK_URL here.
	if ($infoArr['file'] != null) {
		$request = array();
		foreach ($infoArr as $key => $value) {
			if ($value != null) {
				$request[] = "${key}=${value}";
			}
		}
		$url = XOOPS_MODULE_URL . "/legacyRender/admin/css.php?" . implode("&amp;", $request);
		print '<link rel="stylesheet" type="text/css" media="'. $media .'" href="' . $url . '" />';
	}
}

function Legacy_get_override_file($file, $prefix = null, $isSpDirname = false)
{
	$root =& XCube_Root::getSingleton();
	$moduleObject =& $root->mContext->mXoopsModule;

	if ($isSpDirname && is_object($moduleObject) && $moduleObject->get('dirname') == 'legacy' && isset($_REQUEST['dirname'])) {
		if (preg_match("/^[a-z0-9_]+$/i", xoops_getrequest('dirname'))) {
			$handler =& xoops_gethandler('module');
			$moduleObject =& $handler->getByDirname(xoops_getrequest('dirname'));
		}
	}

	$theme = $root->mSiteConfig['Legacy']['Theme'];

	$ret = array();
	$ret['theme'] = $theme;
	$ret['file'] = $file;
	
	$file = $prefix . $file;

	$themePath = XOOPS_THEME_PATH . '/' . $theme . '/';
	if (!is_object($moduleObject)) {
		if (file_exists($themePath. $file)) {
			return $ret;
		}
		
		$ret['theme'] = null;
		return $ret;
	}
	else {
		$dirname = $moduleObject->get('dirname');
		
		$ret['dirname'] = $dirname;

		if (file_exists($themePath.'modules/' . $dirname . '/' . $file)) {
			return $ret;
		}
		
		if (file_exists($themePath. $file)) {
			$ret['dirname'] = null;
			return $ret;
		}
		
		$ret['theme'] = null;

		if (file_exists(XOOPS_MODULE_PATH . '/' . $dirname . '/admin/templates/' . $file)) {
			return $ret;
		}
		
		$ret['dirname'] = null;

		if (file_exists(LEGACY_ADMIN_RENDER_FALLBACK_PATH . "/" . $file)) {
			return $ret;
		}
		
		$ret['file'] =null;
		return $ret;
	}
}

?>
