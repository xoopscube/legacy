<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Root.class.php,v 1.10 2008/11/20 16:05:57 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */

if (!defined('XCUBE_CORE_PATH')) define('XCUBE_CORE_PATH', dirname(__FILE__));

require_once XCUBE_CORE_PATH . '/XCube_HttpContext.class.php';

if (version_compare(PHP_VERSION, "5.0", ">=")) {
    function XC_CLASS_EXISTS($className)
    {
	return class_exists($className, false);
    }
} else {
    function XC_CLASS_EXISTS($className)
    {
	return class_exists($className);
    }
}

/**
 * @public
 * @brief [FINAL CLASS] The root object which collects exchangable managers. 
 * 
 * This class offers the access course same as global variable for a logic in old mechanism.
 * This class does not let you depend on a main controller class name
 * You must not succeed to this class.
 */
class XCube_Root
{
	/**
	 * @public
	 * @brief [READ ONLY] XCube_Controller
	 */
	var $mController = null;
	
	/**
	 * @public
	 * @brief [READ ONLY] XCube_LanguageManager
	 */
	var $mLanguageManager = null;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_DelegateManager
	 */
	var $mDelegateManager = null;
	
	/**
	 * @public
	 * @brief [READ ONLY] XCube_ServiceManager
	 */
	var $mServiceManager = null;

	/**
	 * @private
	 * @brief Hash-Map Array - std::map<string, XCube_RenderSystem*> - Caches for genereted render-systems.
	 * @attention
	 *      Only the kernel system should access this member property.
	 */
	var $_mRenderSystems = array();
	
	/**
	 * @public
	 * @brief [READ ONLY] Hash-Map Array - std::map<string, string>
	 */
	var $mSiteConfig = array();
	
	/**
	 * @internal
	 * @access public
	 * @var XCube_AbstractPermissionProvider
	 */
	var $mPermissionManager = null;
	
	/**
	 * @public
	 * @brief [READ ONLY] XCube_RoleManager
	 * @todo Let's implements!
	 */
	var $mRoleManager = null;
	
	/**
	 * @internal
	 * @deprecated
	 * @todo Check! This is deprecated member.
	 */
	var $mCacheSystem = null;
	
	/**
	 * @public
	 * @brief [READ ONLY] XCube_TextFilter
	 * @attention
	 *      In some cases, this member is not initialized. Use getTextFilter().
	 * 
	 * @see getTextFilter()
	 */
	var $mTextFilter = null;
	
	/**
	 * @public
	 * @brief [READ ONLY] XCube_HttpContext
	 */
	var $mContext = null;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_Session
	 */
	var $mSession = null;

	/**
	 * @internal
	 */
	function XCube_Root()
	{
	}

	/**
	 * @public
	 * @brief [Static] Gets a object of XCube_Root as singleton.
	 * @return XCube_Root
	 */
	function &getSingleton()
	{
		static $instance;
		
		if (!isset($instance))
			$instance = new XCube_Root();
		
		return $instance;
	}

	/**
	 * @public
	 * @berif [Secret Agreement][Overload] Loads SiteConfig from plural files, and control set and override site config.
	 * @return void
	 * 
	 * \par $root->loadSiteConfig(string $file1);
	 *   Loads the site settings from file1.
	 * 
	 * \par $root->loadSiteConfig(string $file1, string $file2);
	 *   Loads the site setting from file1. After that, override file1's setting with file2's setting.
	 * 
	 * @attention
	 *     Only a base module's boot strap should call this method.
	 */
	function loadSiteConfig()
	{
		$n = func_num_args();
		if ($n == 0) {
			die("FETAL: open error: site setting config.");
		}

		$files = func_get_args();
		$file = array_shift($files);

		if(!file_exists($file)) {
			die("FETAL: open error: site setting config.");
		}
		
		$this->setSiteConfig(parse_ini_file($file, true));

		//
		// Override setting.
		//
		if ($n > 1) {
			foreach ($files as $overrideFile) {
				if (file_exists($overrideFile)) {
					$this->overrideSiteConfig(parse_ini_file($overrideFile, true));
				}
			}
		}
	}
	
	/**
	 * @internal
	 * @public
	 * @brief Sets site configs.
	 * @param $config Array
	 * @return void
	 */
	function setSiteConfig($config)
	{
		$this->mSiteConfig = $config;
	}
	
	/**
	 * @public
	 * @brief [Secret Agreement] Overwrites the current site configs with $config.
	 * 
	 * Override site config. SiteConfig is overridden by $config value. And, if 
	 * $config has new key, that key is set.
	 * 
 	 * @attention
	 *     Only the header of the current base module should call this method.
	 * 
	 * @param array $config
	 */
	function overrideSiteConfig($config)
	{
		foreach ($config as $_overKey=>$_overVal) {
			if (array_key_exists($_overKey, $this->mSiteConfig)) {
				$this->mSiteConfig[$_overKey] = array_merge($this->mSiteConfig[$_overKey], $_overVal);
			}
			else {
				$this->mSiteConfig[$_overKey] = $_overVal;
			}
		}
	}

	/**
	 * @public
	 * @brief [Overload] Gets a value of site config that is defined by .ini files.
	 * @return mixed - If the value specified by parameters is no, return null.
	 * 
	 * \par $root->getSiteConfig();
	 *   Gets array.
	 * 
	 * \par $root->getSiteConfig(string $groupName);
	 *   Gets array of the group specified by $groupName.
	 * 
	 * \par $root->getSiteConfig(string $groupName, string $itemName);
	 *   Gets a config value specified by $groupName & $itemName.
	 * 
	 * \par $root->getSiteConfig(string $groupName, string $itemName, string $default);
	 *   If the config value is NOT defined specified by $groupName & $itemName, gets $default.
	 */
	function getSiteConfig()
	{
		//
		// TODO Check keys with using 'isset'
		//
		$m = &$this->mSiteConfig;
		$n = func_num_args();
		if ($n == 0) return $m;
		elseif ($n == 1) {
			$a = func_get_arg(0);
			if (isset($m[$a])) return $m[$a];
		}
		elseif ($n == 2) {
			list($a, $b) = func_get_args();
			if (isset($m[$a][$b])) return $m[$a][$b];
		}
		elseif ($n == 3) {
			list($a, $b, $c) = func_get_args();
			if (isset($m[$a][$b])) return $m[$a][$b];
			else return $c; //return 3rd param as a default value;
		}

		return null;
	}

	/**
	 * @public
	 * @brief [Secret Agreement] Creates controller with the rule.
	 * 
	 * Creates controller with the rule, and call member function prepare().
	 * The class of creating controller is defined in ini.php files.
	 * 
	 * @attention
	 *     Only the header of the current base module should call this method.
	 * 
	 * @return void
	 */
	function setupController()
	{
		//
		// [NOTICE]
		// We don't decide the style of SiteConfig.
		//
		$controllerName = $this->mSiteConfig['Cube']['Controller'];
        if(isset($this->mSiteConfig[$controllerName]['root'])) {
            $this->mController =& $this->_createInstance($this->mSiteConfig[$controllerName]['class'], $this->mSiteConfig[$controllerName]['path'], $this->mSiteConfig[$controllerName]['root']);
        }
        else {
            $this->mController =& $this->_createInstance($this->mSiteConfig[$controllerName]['class'], $this->mSiteConfig[$controllerName]['path']);
        }
		$this->mController->prepare($this);
	}

	/**
	 * @public
	 * @public Gets a XCube_Controller object.
	 * @return XCube_Controller
	 */
	function &getController()
	{
		return $this->mController;
	}

	/**
	 * @public
	 * @brief Sets the XCube_LanguageManager object.
	 * @param $languageManager XCube_LanguageManager
	 * @return void
	 */
	function setLanguageManager(&$languageManager)
	{
		$this->mLanguageManager =& $languageManager;
	}
	
	/**
	 * @public
	 * @brief Gets a XCube_LanguageManager object.
	 * @return XCube_LanguageManager
	 */
	function &getLanguageManager()
	{
		return $this->mLanguageManager;
	}
	
	/**
	 * @public
	 * @brief Sets the XCube_DelegateManager object.
	 * @param $delegateManager XCube_DelegateManager
	 * @return void
	 */
	function setDelegateManager(&$delegateManager)
	{
		$this->mDelegateManager =& $delegateManager;
	}
	
	/**
	 * @public
	 * @brief Gets a XCube_DelegateManager object.
	 * @return XCube_DelegateManager
	 */
	function &getDelegateManager()
	{
		return $this->mDelegateManager;
	}
	
	/**
	 * @public
	 * @brief Sets the XCube_ServiceManager object.
	 * @param $serviceManager XCube_ServiceManager
	 * @return void
	 */
	function setServiceManager(&$serviceManager)
	{
		$this->mServiceManager =& $serviceManager;
	}
	
	/**
	 * @public
	 * @brief Gets a XCube_ServiceManager object.
	 * @return XCube_ServiceManager
	 */
	function &getServiceManager()
	{
		return $this->mServiceManager;
	}
	
	/**
	 * @public
	 * @brief Gets a RenderSystem object having specified name.
	 * @param $name string - the registed name of the render system.
	 * @return XCube_RenderSystem
	 * 
	 * Return the instance of the render system by the name. If the render
	 * system specified by $name doesn't exist, raise fatal error. This member
	 * function does creating the instance and calling prepare().
	 * 
	 */
	function &getRenderSystem($name)
	{
		if (isset($this->_mRenderSystems[$name])) {
			return $this->_mRenderSystems[$name];
		}
		
		//
		// create
		//
		$chunkName = $this->mSiteConfig['RenderSystems'][$name];
		if (isset($this->mSiteConfig[$chunkName]['root'])) {
			$this->_mRenderSystems[$name] =& $this->_createInstance($this->mSiteConfig[$chunkName]['class'], $this->mSiteConfig[$chunkName]['path'], $this->mSiteConfig[$chunkName]['root']);
		}
		else {
			$this->_mRenderSystems[$name] =& $this->_createInstance($this->mSiteConfig[$chunkName]['class'], $this->mSiteConfig[$chunkName]['path']);
		}
		
		if (!is_object($this->_mRenderSystems[$name])) {
			die("NO");
		}
		
		$this->_mRenderSystems[$name]->prepare($this->mController);
		
		return $this->_mRenderSystems[$name];
	}
	
	/**
	 * @internal
	 */
	function setPermissionManager(&$manager)
	{
		$this->mPermissionManager =& $manager;
	}
	
	/**
	 * @internal
	 */
	function &getPermissionManager()
	{
		return $this->mPermissionManager;
	}
	
	/**
	 * @public
	 * @brief Sets a XCube_TextFilter object.
	 * @param $textFilter XCube_TextFilter
	 * @return void
	 */
	function setTextFilter(&$textFilter)
	{
		$this->mTextFilter =& $textFilter;
	}
	
	/**
	 * @public
	 * @brief Gets a XCube_TextFilter object.
	 * @return XCube_TextFilter
	 * @attention
	 *     If mTextFilter member has been not initialized, the root object tries to
	 *     generate an instance though XCube_Controller's delegate. This is a special
	 *     case. Basically, a class never calls degates of other classes directly.
	 */
	function &getTextFilter()
	{
	    if (!empty($this->mTextFilter)) return $this->mTextFilter;
	    if (!empty($this->mController)) { //ToDo: This case is for _LEGACY_PREVENT_EXEC_COMMON_ status;
    	    $this->mController->mSetupTextFilter->call(new XCube_Ref($this->mTextFilter));
    	    return $this->mTextFilter;
	    }
	    
	    // Exception
	    $ret = null;
	    return $ret;
	}
	
	/**
	 * @public
	 * @brief Sets the role manager object.
	 * @param $manager XCube_RoleManager
	 * @return void
	 */
	function setRoleManager(&$manager)
	{
		$this->mRoleManager =& $manager;
	}
	
	/**
	 * @public
	 * @brief Sets the HTTP-context object.
	 * @param $context XCube_Context
	 * @return void
	 */
	function setContext(&$context)
	{
		$this->mContext =& $context;
	}
	
	/**
	 * @public
	 * @brief Gets a HTTP-context object.
	 * @return XCube_Context
	 */
	function &getContext()
	{
		return $this->mContext;
	}

	/**
	 * @public
	 * @brief Sets a Session object.
	 * @param $session XCube_Session
	 * @return void
	 */
	function setSession(&$session)
	{
		$this->mSession =& $session;
	}
	
	/**
	 * @public
	 * @brief Gets a Session object.
	 * @return XCube_Session
	 */
	function &getSession()
	{
		return $this->mSession;
	}

	/**
	 * @private
	 * @brief Create an instance.
	 * 
	 * Create the instance dynamic with the rule and the string parameters.
	 * First, load the file from $classPath. The rule is XOOPS_ROOT_PATH + 
	 * $classPath + $className + .class.php. Next, create the instance of the
	 * class if the class is defined rightly. This member function is called by
	 * other member functions of XCube_Root.
	 * 
	 * @param $className string - the name of class.
	 * @param $classPath string - the path that $className is defined in.
	 * @param $root      string - the root path instead of Cube.Root.
	 * @return Object
	 * 
	 * @todo If the file doesn't exist, require_once() raises fatal errors.
	 */
	function &_createInstance($className, $classPath = null, $root = null)
	{
		$ret = null;
		
		if ($classPath != null) {
			if ($root == null) {
				$root = $this->mSiteConfig['Cube']['Root'];
			}
			
			if (is_file($root . $classPath)) {
				// [secret trick] ... Normally, $classPath has to point a directory.
				require_once $root . $classPath;
			}
			else {
				require_once $root . $classPath . '/' . $className . '.class.php';
			}
		}
		
		if (XC_CLASS_EXISTS($className)) {
			$ret = new $className();
		}

		return $ret;
	}
}

?>
