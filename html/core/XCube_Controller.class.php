<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Controller.class.php,v 1.9 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

if (!defined('XCUBE_CORE_PATH')) {
    define('XCUBE_CORE_PATH', dirname(__FILE__));
}

require_once XCUBE_CORE_PATH . '/XCube_Root.class.php';

require_once XCUBE_CORE_PATH . '/XCube_ActionFilter.class.php';
require_once XCUBE_CORE_PATH . '/XCube_RenderSystem.class.php';
require_once XCUBE_CORE_PATH . '/XCube_Delegate.class.php';

require_once XCUBE_CORE_PATH . '/XCube_Object.class.php';
require_once XCUBE_CORE_PATH . '/XCube_Service.class.php';

require_once XCUBE_CORE_PATH . '/XCube_Identity.class.php';
require_once XCUBE_CORE_PATH . '/XCube_RoleManager.class.php';
require_once XCUBE_CORE_PATH . '/XCube_Permission.class.php';

require_once XCUBE_CORE_PATH . '/XCube_LanguageManager.class.php';

require_once XCUBE_CORE_PATH . '/XCube_ActionForm.class.php';
require_once XCUBE_CORE_PATH . '/XCube_TextFilter.class.php';
require_once XCUBE_CORE_PATH . '/XCube_Session.class.php';

/**
 * Virtual or Actual front controller class.
 *
 * This is an abstract class. And, a sub-class of this class has many
 * impositions which sets up root object finally and implements many actual
 * logic.
 *
 * executeXXXXX() functions are a public member function called by an accessed
 * file. These member functions call other protected member functions.
 *
 * _setupXXXXX() functions are a protected member function overridden by a
 * sub-class controller. Most of these functions are empty. A sub-class
 * controller overrides them to set up a controller object and others.
 *
 * _createXXXXX() functions are a protected member function overridden by a
 * sub-class controller. These member functions are called in prepare() to set
 * up the root object. And, they have been exported from prepare() for a
 * sub-class controller to override easily. Most of sub-class controllers
 * doesn't need to override them, because typical code is there.
 */
class XCube_Controller
{
    /**
     * The reference for the root object.
     *
     * @var XCube_Root
     */
    public $mRoot;

    /**
     * Array of a procedure class object.
     *
     * @var Array
     */
    public $_mBlockChain = array();
    
    
    /**
     * Vector Array of XCube_ActionFilter class object.
     * @protected
     * @var Array
     * @remarks
     *	   typedef std:vector<XCube_ActionFilter*> FilterList; \n
     *	   FilterList _mFilterChain; \n
     */
    public $_mFilterChain = array();
    
    /**
     * This is Map-Array to keep names of action filter classes which are
     * applied as filters.
     * 
     * @protected
     */
    public $_mLoadedFilterNames = array();
    
    /**
     * The database object which is abstract layer for the database.
     *
     * @var object
     */
    public $mDB;
    
    /**
     * A name of the current local.
     *
     * @access public
     * @var string
     */
    public $mLocale = null;

    /**
     * A name of the current language.
     *
     * @access public
     * @var string
     */
    public $mLanguage = null;

    /**
     * Rebuilds the principal object for the current HTTP-request.
     * void setupUser(XCube_AbstractPrincipal &, XCube_Controller &, XCube_HttpContext &);
     *
     * @var XCube_Delegate
     */
    public $mSetupUser = null;
    
    /**
     * Executes the main logic of the controller.
     * void execute(XCube_Controller &);
     * 
     * @var XCube_Delegate
     */
    public $mExecute = null;
    
    /**
     * Make a instance of TextFilter.
     * void senupTextFilter(XCube_TextFilter &);
     * 
     * @var XCube_Delegate
     */
    public $mSetupTextFilter = null;
    // !Fix PHP7
    public function __construct()
    //public function XCube_Controller()
    {
        $this->_mBlockChain = array();
        $this->_mFilterChain = array();
        $this->_mLoadedFilterNames = array();
        
        $this->mSetupUser = new XCube_Delegate();
        $this->mExecute = new XCube_Delegate();
        $this->mSetupTextFilter = new XCube_Delegate();
        $this->mSetupTextFilter->add('XCube_TextFilter::getInstance', XCUBE_DELEGATE_PRIORITY_FINAL);
    }
    
    /**
     * This member function is overridden. The sub-class implements the
     * initialization process which sets up the root object finally.
     *
     * @param XCube_Root $root
     */
    public function prepare(&$root)
    {
        $this->mRoot =& $root;
        
        $this->mRoot->setDelegateManager($this->_createDelegateManager());
        $this->mRoot->setServiceManager($this->_createServiceManager());
        $this->mRoot->setPermissionManager($this->_createPermissionManager());
        $this->mRoot->setRoleManager($this->_createRoleManager());
        $this->mRoot->setContext($this->_createContext());
    }

    /**
     * This member function is actual initialize process of web application.
     * Some Nuke-like bases call this function at any timing.
     *
     * @access public
     */
    public function executeCommon()
    {
        //
        // Setup Filter chain and execute the process of these filters.
        //
        $this->_setupFilterChain();
        $this->_processFilter();

        $this->_setupEnvironment();
        
        $this->_setupDB();

        $this->_setupLanguage();

        $this->_setupTextFilter();

        $this->_setupConfig();
        
        //
        // Block section
        //
        $this->_processPreBlockFilter();    // What's !?

        $this->_setupSession();

        $this->_setupUser();
    }
    
    /**
     * Usually this member function is called after executeCommon(). But, some
     * cases don't call this. Therefore, the page controller type base should
     * not write the indispensable code here. For example, this is good to call
     * blocks.
     */
    public function executeHeader()
    {
        $this->_setupBlock();
        $this->_processBlock();
    }

    /**
     * Executes the main logic.
     *
     * @access public
     */
    public function execute()
    {
        $this->mExecute->call(new XCube_Ref($this));
    }
    
    /**
     * Executes the view logic. This member function is overridden.
     *
     * @access public
     */
    public function executeView()
    {
    }
    
    /**
     * TODO We may change this name to forward()
     * 
     * @param string  $url		Can't use html tags.
     * @param int	  $time
     * @param string  $message
     */
    public function executeForward($url, $time = 0, $message = null)
    {
        // check header output
        header("location: " . $url);
        exit();
    }
    
    /**
     * Redirect to the specified URL with displaying message.
     * 
     * @param string  $url		Can't use html tags.
     * @param int	  $time
     * @param string  $message
     */
    public function executeRedirect($url, $time = 1, $message = null)
    {
        $this->executeForward($url, $time, $message);
    }
    
    /**
     * Adds the ActionFilter instance.
     * @param $filter XCube_ActionFilter
     */
    public function addActionFilter(&$filter)
    {
        $this->_mFilterChain[] =& $filter;
    }

    /**
     * Create filter chain.
     * @access protected
     */
    public function _setupFilterChain()
    {
    }

    /**
     * This member function is overridden. Sets up the controller and the
     * environment.
     */
    public function _setupEnvironment()
    {
    }
    
    /**
     * Creates the instance of DataBase class, and sets it to member property.
     *
     * @access protected
     */
    public function _setupDB()
    {
    }
    
    /**
     * Gets the DB instance.
     *
     * @access public
     */
    public function &getDB()
    {
        return $this->mDB;
    }

    /**
     * Creates the instance of Language Manager class, and sets it to member
     * property.
     *
     * @access protected
     */
    public function _setupLanguage()
    {
        $this->mRoot->mLanguageManager = new XCube_LanguageManager();
    }
    
    
    /**
     * Creates the instance of Text Filter class, and sets it to member
     * property.
     *
     * @access protected
     */
    public function _setupTextFilter()
    {
        $textFilter = null;
        $this->mSetupTextFilter->call(new XCube_Ref($textFilter));
        $this->mRoot->setTextFilter($textFilter);
    }
    
    
    /**
     * This member function is overridden. Loads site configuration informations,
     * and sets them to the member property.
     */
    public function _setupConfig()
    {
    }

    /**
     * This member function is overridden. Sets up handler for session, then
     * starts session.
     * 
     * @access protected
     * @return void
     */
    public function _setupSession()
    {
        $this->mRoot->setSession(new XCube_Session());
    }
    
    /**
     * Sets up a principal object to the root object. In other words, restores
     * the principal object from session or other.
     */
    public function _setupUser()
    {
        $this->mSetupUser->call(new XCube_Ref($this->mRoot->mContext->mUser), new XCube_Ref($this), new XCube_Ref($this->mRoot->mContext));
    }
    
    /**
     * Calls the preFilter() member function of action filters which have been
     * loaded to the list of the controller.
     *
     * @access protected
     */
    public function _processFilter()
    {
        foreach (array_keys($this->_mFilterChain) as $key) {
            $this->_mFilterChain[$key]->preFilter();
        }
    }
    
    /**
     * FIXME.
     */
    public function _setupBlock()
    {
    }

    /**
     * FIXME.
     */
    public function _processBlock()
    {
        /*		foreach(array_keys($this->mBlockChain) as $key) {
            if ($this->mBlockChain[$key]->hasPermission($this, $this->getUser())) {
                $renderTarget =new XCube_RenderTarget();
                $renderTarget->setType(XCUBE_RENDER_TARGET_TYPE_MAIN);
                
                $this->mBlockChain[$key]->execute($this, $this->getUser(), $renderTarget);
                
                $this->mBlockChain[$key]->mRenderTarget =& $renderTarget;

                unset($renderTarget);
            }
        }*/
    }

    /**
     * Calls the preBlockFilter() member function of action filters which have been
     * loaded to the list of the controller.
     *
     * @access protected
     */
    public function _processPreBlockFilter()
    {
        foreach (array_keys($this->_mFilterChain) as $key) {
            $this->_mFilterChain[$key]->preBlockFilter();
        }
    }
    
    /**
     * Calls the postFilter() member function of action filters which have been
     * loaded to the list of the controller.
     *
     * @access protected
     */
    public function _processPostFilter()
    {
        foreach (array_reverse(array_keys($this->_mFilterChain)) as $key) {
            $this->_mFilterChain[$key]->postFilter();
        }
    }

    /**
     * This is utility member function for the sub-class controller. Load files
     * with the rule from $path, and add the instance of the sub-class to the
     * chain.
     *
     * @access protected
     * @param $path string Absolute path.
     */
    public function _processPreload($path)
    {
        $path = $path . "/";
        
        if (is_dir($path) && ($files = glob($path.'/*.class.php'))) {
            foreach ($files as $file) {
                require_once $file;
                $className = basename($file, '.class.php');
                if (XC_CLASS_EXISTS($className) && !isset($this->_mLoadedFilterNames[$className])) {
                    $this->_mLoadedFilterNames[$className] = true;
                    $instance = new $className($this);
                    $this->addActionFilter($instance);
                    unset($instance);
                }
            }
        }
    }
    
    /**
     * Creates an instance of the delegate manager and returns it.
     * 
     * @return XCube_DelegateManager
     */
    public function &_createDelegateManager()
    {
        $delegateManager = new XCube_DelegateManager();
        return $delegateManager;
    }

    /**
     * Creates an instance of the service manager and returns it.
     * 
     * @return XCube_ServiceManager
     */
    public function &_createServiceManager()
    {
        require_once XCUBE_CORE_PATH . '/XCube_ServiceManager.class.php';
        $serviceManager = new XCube_ServiceManager();
        return $serviceManager;
    }

    /**
     * Creates an instance of the permission manager and returns it.
     * 
     * @return XCube_PermissionManager
     */
    public function &_createPermissionManager()
    {
        $chunkName = $this->mRoot->getSiteConfig('Cube', 'PermissionManager');
        
        //
        // FIXME: Access private method.
        //
        $manager =& $this->mRoot->_createInstance($this->mRoot->getSiteConfig($chunkName, 'class'), $this->mRoot->getSiteConfig($chunkName, 'path'));
        
        return $manager;
    }
    
    /**
     * Creates an instance of the role manager and returns it.
     * 
     * @return XCube_RoleManager
     */
    public function &_createRoleManager()
    {
        $chunkName = $this->mRoot->getSiteConfig('Cube', 'RoleManager');
        
        //
        // FIXME: Access private method.
        //
        $manager =& $this->mRoot->_createInstance($this->mRoot->getSiteConfig($chunkName, 'class'), $this->mRoot->getSiteConfig($chunkName, 'path'));
        
        return $manager;
    }
    
    /**
     * Creates the context object to initial the root object, and returns it.
     *
     * @return XCube_HttpContext
     */
    public function &_createContext()
    {
        $context = new XCube_HttpContext();
        $request = new XCube_HttpRequest();
        $context->setRequest($request);
        
        return $context;
    }
}
