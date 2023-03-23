<?php
/**
 * /core/XCube_Root.class.php
 * @package    XCube
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/11/20
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      [FINAL CLASS] The root object which collects exchangeable managers.
 * This class offers the access course same as global variable for a logic in old mechanism.
 * This class does not let you depend on a main controller class name
 * You must not succeed to this class.
 */

if ( ! defined( 'XCUBE_CORE_PATH' ) ) {
	define( 'XCUBE_CORE_PATH', __DIR__ );
}

require_once XCUBE_CORE_PATH . '/XCube_HttpContext.class.php';

if ( PHP_VERSION_ID >= 50000 ) {
	function XC_CLASS_EXISTS( $className ) {
		return $className && class_exists( $className, false );
	}
} else {
	function XC_CLASS_EXISTS( $className ) {
		return class_exists( $className );
	}
}

class XCube_Root {
	/**
	 * @public
	 * @brief [READ ONLY] XCube_Controller
	 */
	public $mController;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_LanguageManager
	 */
	public $mLanguageManager;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_DelegateManager
	 */
	public $mDelegateManager;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_ServiceManager
	 */
	public $mServiceManager;

	/**
	 * @private
	 * @brief Hash-Map Array - std::map<string, XCube_RenderSystem*> - Caches for generated render-systems.
	 * @attention
	 *      Only the kernel system should access this member property.
	 */
	public $_mRenderSystems = [];

	/**
	 * @public
	 * @brief [READ ONLY] Hash-Map Array - std::map<string, string>
	 */
	public $mSiteConfig = [];

	/**
	 * @internal
	 * @access public
	 * @var XCube_AbstractPermissionProvider
	 */
	public $mPermissionManager;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_RoleManager
	 * @todo Let's implements!
	 */
	public $mRoleManager;

	/**
	 * @internal
	 * @deprecated
	 * @todo Check! This is deprecated member.
	 */
	public $mCacheSystem;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_TextFilter
	 * @attention
	 *      In some cases, this member is not initialized. Use getTextFilter().
	 *
	 * @see getTextFilter()
	 */
	public $mTextFilter;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_HttpContext
	 */
	public $mContext;

	/**
	 * @public
	 * @brief [READ ONLY] XCube_Session
	 */
	public $mSession;

	/**
	 * @internal
	 */
	public function __construct() {
	}

	/**
	 * @public
	 * @brief [Static] Gets an object of XCube_Root as singleton.
	 * @return XCube_Root
	 */
	public static function &getSingleton() {
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new XCube_Root();
		}

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
	public function loadSiteConfig() {
		$n = func_num_args();
		if ( 0 == $n ) {
			die( 'FETAL: open error: site setting config.' );
		}

		$files = func_get_args();
		$file  = array_shift( $files );

		if ( ! file_exists( $file ) ) {
			die( 'FETAL: open error: site setting config.' );
		}

		$this->setSiteConfig( parse_ini_file( $file, true ) );

		//
		// Override setting.
		//
		if ( $n > 1 ) {
			foreach ( $files as $overrideFile ) {
				if ( file_exists( $overrideFile ) ) {
					$this->overrideSiteConfig( parse_ini_file( $overrideFile, true ) );
				}
			}
		}
	}

	/**
	 * Array
	 *
	 * @param $config
	 *
	 * @return void
	 * @internal
	 * @public
	 * @brief Sets site configs.
	 */
	public function setSiteConfig( $config ) {
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
	public function overrideSiteConfig( $config ) {
		foreach ( $config as $_overKey => $_overVal ) {
			if ( array_key_exists( $_overKey, $this->mSiteConfig ) ) {
				$this->mSiteConfig[ $_overKey ] = array_merge( $this->mSiteConfig[ $_overKey ], $_overVal );
			} else {
				$this->mSiteConfig[ $_overKey ] = $_overVal;
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
	public function getSiteConfig() {
		//
		// ! TODO Check keys with using 'isset'
		//
		$m = &$this->mSiteConfig;
		$n = func_num_args();
		if ( 0 === $n ) {
			return $m;
		}
		if ( 1 === $n ) {
			$a = func_get_arg( 0 );
			if ( isset( $m[ $a ] ) ) {
				return $m[ $a ];
			}
		} elseif ( 2 === $n ) {
			list( $a, $b ) = func_get_args();
			if ( isset( $m[ $a ][ $b ] ) ) {
				return $m[ $a ][ $b ];
			}
		} elseif ( 3 === $n ) {
			list( $a, $b, $c ) = func_get_args();
			if ( isset( $m[ $a ][ $b ] ) ) {
				return $m[ $a ][ $b ];
			}

			return $c; //return 3rd param as a default value;
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
	public function setupController() {
		//
		// [NOTICE]
		// We don't decide the style of SiteConfig.
		//
		$controllerName = $this->mSiteConfig['Cube']['Controller'];
		$controller     =& $this->mSiteConfig[ $controllerName ];
		if ( isset( $controller['root'] ) ) {
			$this->mController =& $this->_createInstance( $controller['class'], $controller['path'], $controller['root'] );
		} else {
			$this->mController =& $this->_createInstance( $controller['class'], $controller['path'] );
		}
		$this->mController->prepare( $this );
	}

	/**
	 * @public
	 * @public Gets a XCube_Controller object.
	 * @return XCube_Controller
	 */
	public function &getController() {
		return $this->mController;
	}

	/**
	 * @public
	 * @brief Sets the XCube_LanguageManager object.
	 *
	 * @param XCube_LanguageManager $languageManager
	 *
	 * @return void
	 */
	public function setLanguageManager( &$languageManager ) {
		$this->mLanguageManager =& $languageManager;
	}

	/**
	 * @public
	 * @brief Gets a XCube_LanguageManager object.
	 * @return XCube_LanguageManager
	 */
	public function &getLanguageManager() {
		return $this->mLanguageManager;
	}

	/**
	 * @public
	 * @brief Sets the XCube_DelegateManager object.
	 *
	 * @param XCube_DelegateManager $delegateManager
	 *
	 * @return void
	 */
	public function setDelegateManager( &$delegateManager ) {
		$this->mDelegateManager =& $delegateManager;
	}

	/**
	 * @public
	 * @brief Gets a XCube_DelegateManager object.
	 * @return XCube_DelegateManager
	 */
	public function &getDelegateManager() {
		return $this->mDelegateManager;
	}

	/**
	 * @public
	 * @brief Sets the XCube_ServiceManager object.
	 *
	 * @param XCube_ServiceManager $serviceManager
	 *
	 * @return void
	 */
	public function setServiceManager( &$serviceManager ) {
		$this->mServiceManager =& $serviceManager;
	}

	/**
	 * @public
	 * @brief Gets a XCube_ServiceManager object.
	 * @return XCube_ServiceManager
	 */
	public function &getServiceManager() {
		return $this->mServiceManager;
	}

	/**
	 * @public
	 * @brief Gets a RenderSystem object having specified name.
	 *
	 * @param string $name - the registered name of the render system.
	 *
	 * @return XCube_RenderSystem
	 *
	 * Return the instance of the render system by the name.
     * If the render system specified by $name doesn't exist, raise fatal error.
     * This member function creates the instance and calls prepare().
	 *
	 */
	public function &getRenderSystem( $name ) {
		$mRS =& $this->_mRenderSystems;
		if ( isset( $mRS[ $name ] ) ) {
			return $mRS[ $name ];
		}

		//
		// create
		//
		$config    =& $this->mSiteConfig;
		$chunkName = $config['RenderSystems'][ $name ];
		$chunk     =& $config[ $chunkName ];
		if ( isset( $config[ $chunkName ]['root'] ) ) {
			$mRS[ $name ] =& $this->_createInstance( $chunk['class'], $chunk['path'], $chunk['root'] );
		} else {
			$mRS[ $name ] =& $this->_createInstance( $chunk['class'], $chunk['path'] );
		}

		if ( ! is_object( $mRS[ $name ] ) ) {
			die( 'NO' );
		}

		$mRS[ $name ]->prepare( $this->mController );

		return $mRS[ $name ];
	}

	/**
	 * @param $manager
	 *
	 * @internal
	 */
	public function setPermissionManager( &$manager ) {
		$this->mPermissionManager =& $manager;
	}

	/**
	 * @internal
	 */
	public function &getPermissionManager() {
		return $this->mPermissionManager;
	}

	/**
	 * @public
	 * @brief Sets a XCube_TextFilter object.
	 *
	 * @param XCube_TextFilter $textFilter
	 *
	 * @return void
	 */
	public function setTextFilter( &$textFilter ) {
		$this->mTextFilter =& $textFilter;
	}

	/**
	 * @public
	 * @brief Gets a XCube_TextFilter object.
	 * @return XCube_TextFilter
	 * @attention
	 *     If mTextFilter member has been not initialized, the root object tries to
	 *     generate an instance though XCube_Controller's delegate.
     *     This is a special case. In principle, a class never calls directly the delegates of other classes.
	 */
	public function &getTextFilter() {
		if ( ! empty( $this->mTextFilter ) ) {
			return $this->mTextFilter;
		}
		if ( ! empty( $this->mController ) ) { //ToDo: This case is for _LEGACY_PREVENT_EXEC_COMMON_ status;
			$this->mController->mSetupTextFilter->call( new XCube_Ref( $this->mTextFilter ) );

			return $this->mTextFilter;
		}

		// Exception
		$ret = null;

		return $ret;
	}

	/**
	 * @public
	 * @brief Sets the role manager object.
	 *
	 * @param XCube_RoleManager $manager
	 *
	 * @return void
	 */
	public function setRoleManager( &$manager ) {
		$this->mRoleManager =& $manager;
	}

	/**
	 * XCube_Context
	 * @public
	 * @brief Sets the HTTP-context object.
	 *
	 * @param $context
	 *
	 * @return void
	 */
	public function setContext( &$context ) {
		$this->mContext =& $context;
	}

	/**
	 * XCube_Context
	 * @public
	 * @brief Gets a HTTP-context object.
	 * @return
	 */
	public function &getContext() {
		return $this->mContext;
	}

	/**
	 * @public
	 * @brief Sets a Session object.
	 *
	 * @param XCube_Session $session
	 *
	 * @return void
	 */
	public function setSession( &$session ) {
		$this->mSession =& $session;
	}

	/**
	 * @public
	 * @brief Gets a Session object.
	 * @return XCube_Session
	 */
	public function &getSession() {
		return $this->mSession;
	}

	/**
	 * @private
	 * @brief Create an instance.
	 *
	 * Create the instance dynamically with the rule and the parameters of the chain.
	 * First, load the file from $classPath.
     * The rule is XOOPS_ROOT_PATH + $classPath + $className + .class.php
     * Then create the instance of the class if it is rightly defined.
     * This member function is called by other member functions of XCube_Root.
	 *
	 * @param string $className - the name of class.
	 * @param string $classPath - the path that $className is defined in.
	 * @param string $root - the root path instead of Cube.Root.
	 *
	 * @return Object
	 *
	 * @todo If the file doesn't exist, require_once() raises fatal errors.
	 */
	public function &_createInstance( $className, $classPath = null, $root = null ) {
		$ret = null;

		if ( null !== $classPath ) {
			if ( null === $root ) {
				$root = $this->mSiteConfig['Cube']['Root'];
			}

			if ( is_file( $root . $classPath ) ) {
				// [secret trick] ... Normally, $classPath has to point a directory.
				require_once $root . $classPath;
			} else {
				require_once $root . $classPath . '/' . $className . '.class.php';
			}
		}

		if ( XC_CLASS_EXISTS( $className ) ) {
			$ret = new $className();
		}

		return $ret;
	}
}
