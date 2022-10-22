<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.3.1
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}

/**
 * themes/[theme]/ckeditor4/preload.class.php Preload to load if present
 *
 * preload.class.php Class name is "ckeditor4_PreloadForTheme" Fixed
 * setParams, preSetConfig, postSetConfig only these three method are required.
 *
 * @ param array $params Smarty Parameters given by the plug-in
 * @ param array $config CKEditor Config array to give
 *                       key(string): config Name
 *                       val(mixed) : value  [] Character strings enclosed in brackets are handled as JavaScript objects
 *                                      Other values are processed by json_encode and passed to CKEditor
 *
 * class ckeditor4_PreloadForTheme
 * {
 *     var $themeName; // The current theme name is set
 *
 *     // Smarty For custom parameters passed from plug-ins
 *     function setParams(& $params) {}
 *
 *     // ckeditor.config customized
 *     // (Before Params interpretation: Toolbar specified in Smarty plug-in and management screen: General config config cannot be overwritten)
 *     function preSetConfig(& $config, $params) {}
 *
 *     // ckeditor.config customized
 *     // (Final stage: all config can be overwritten)
 *     function postSetConfig(& $config, $params) {}
 * }
 *
 * @author nao-pon
 */

class ckeditor4_ThemePreload extends XCube_ActionFilter
{
	var $preloadTheme = null;

	function postFilter()
	{
		// Smarty for custom parameters passed from plug-ins
		$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PreBuild_ckconfig', array(&$this, 'setParams'));

		// ckeditor.config for custom parameters' interpretation
		// Toolbar specified in Smarty plug-in and management screen: General config cannot be overwritten
		$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PreParseBuild_ckconfig', array(&$this, 'preSetConfig'));

		// ckeditor.config for custom parameters (final stage: all configs can be overwritten)
		$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PostBuild_ckconfig', array(&$this, 'postSetConfig'));
	}

	/**
	 * setParams
	 * Smarty for custom parameters passed from plug-ins
	 *
	 * @param array $params
	 */
	function setParams(&$params)
	{

		$themeName = $this->mRoot->mContext->mXoopsConfig['theme_set'];
		$_preload = XOOPS_THEME_PATH . '/' . $themeName . '/ckeditor4/preload.class.php';
		@include_once $_preload;

		if (!class_exists('ckeditor4_PreloadForTheme')) {
            return;
        }

		$this->preloadTheme = new ckeditor4_PreloadForTheme();
		$this->preloadTheme->themeName = $themeName;

		// call setParams()
		if (method_exists($this->preloadTheme, 'setParams')) {
			$this->preloadTheme->setParams($params);
		}
	}

	/**
	 * preSetConfig
	 * For custom ckeditor.config
	 * (Before interpretation: toolbar specified by Smarty plugin and administration screen: config of general settings can not be overwritten)
	 *
	 * @param array $config
	 * @param array $params
	 */
	function preSetConfig(&$config, $params)
	{
		if (!$this->preloadTheme) {
            return;
        }

		// call preSetConfig()
		if (method_exists($this->preloadTheme, 'preSetConfig')) {
			$this->preloadTheme->preSetConfig($config, $params);
		}
	}

	/**
	 * postSetConfig
	 * For custom ckeditor.config
	 * (Final stage: can overwrite all configs)
	 *
	 * @param array $config
	 * @param array $params
	 */
	function postSetConfig(&$config, $params)
	{
		if (!$this->preloadTheme) {
            return;
        }

		// call postSetConfig()
		if (method_exists($this->preloadTheme, 'postSetConfig')) {
			$this->preloadTheme->postSetConfig($config, $params);
		}
	}
}
