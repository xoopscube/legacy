<?php
/**
 * Standard cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once __DIR__ . '/../class/Action.class.php';
require_once __DIR__ . '/../class/CacheManager.class.php';
require_once __DIR__ . '/../forms/CacheClearForm.class.php';

// For XCube_DelegateUtils if not autoloaded or included by the core
if (!class_exists('XCube_DelegateUtils') && file_exists(XOOPS_ROOT_PATH . '/core/XCube_DelegateUtils.class.php')) {
    require_once XOOPS_ROOT_PATH . '/core/XCube_DelegateUtils.class.php';
}


class stdCache_CacheClearAction extends stdCache_Action
{
    /**
     * @var XCube_Root
     */
    protected $mRoot = null; // Declare the property
    /**
     * @var XoopsModule
     */
    protected $mModule = null; // XoopsModule instance

    /**
     * @var stdCache_CacheClearForm
     */
    protected $mActionForm = null;

    /**
     * @var stdCache_CacheManager
     */
    protected $mCacheManager = null;


    /**
     * Constructor to initialize mRoot and call parent constructor
     * @param bool $adminFlag Flag indicating an admin action
     */
    public function __construct($adminFlag = false) // Accept adminFlag
    {
        parent::__construct($adminFlag); // Call parent constructor and pass adminFlag
        $this->mRoot = XCube_Root::getSingleton(); // Initialize the property
        // Access mRoot via $this->mRoot now
        if (is_object($this->mRoot->mContext->mModule)) {
            $this->mModule = $this->mRoot->mContext->mModule->getXoopsModule(); // Get XoopsModule object
        }
    }

    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        return is_object($xoopsUser) && $xoopsUser->isAdmin();
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        parent::prepare($controller, $xoopsUser, $moduleConfig);

        // Access mRoot via $this->mRoot now
        if (!(is_object($this->mRoot->mContext->mXoopsUser) && $this->mRoot->mContext->mXoopsUser->isAdmin())) {
            // Use $this->mRoot->mController->executeForward for consistency
            $this->mRoot->mController->executeForward(XOOPS_URL . '/');
            return false;
        }

        $this->_setupActionForm();
        $this->mCacheManager = $this->_getHandler();

        return true;
    }

    protected function _setupActionForm()
    {
        if ($this->mActionForm === null) {
            $this->mActionForm = new stdCache_CacheClearForm();
            $this->mActionForm->prepare();
        }
    }

    protected function _getHandler()
    {
        if ($this->mCacheManager === null) {
            // Use try-catch during object instantiation
            try {
                $this->mCacheManager = new stdCache_CacheManager();
            } catch (Exception $e) {
                // Log the error and rethrow or handle
                error_log("stdCache_CacheClearAction: Failed to initialize CacheManager - " . $e->getMessage());
                throw new RuntimeException('Failed to initialize CacheManager: ' . $e->getMessage());
            }
        }
        return $this->mCacheManager;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        // Load default values into the form for the initial display
        $this->mActionForm->loadDefaults();
        return STDCACHE_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        // Ensure form is fetched and validated for POST requests
        $this->mActionForm->fetch(); // Fetch data from request

        // validate() implicitly handles token validation if getTokenName() is defined for POST
        if (!$this->mActionForm->validate()) {
            return STDCACHE_FRAME_VIEW_INPUT; // Show form with errors
        }

        $ageToClear = (int)$this->mActionForm->get('clear_age');

        $optionsToClear = [];
        if ($this->mActionForm->get('clear_smarty_cache')) {
            $optionsToClear['smarty_cache'] = ['age' => $ageToClear]; // Pass age per type
        }
        if ($this->mActionForm->get('clear_compiled_templates')) {
            $optionsToClear['compiled_templates'] = ['age' => $ageToClear];
        }
        if ($this->mActionForm->get('clear_logs')) {
            $optionsToClear['logs'] = ['age' => $ageToClear];
        }
        if ($this->mActionForm->get('clear_uploads')) {
            // TODO: Add a Help-Tip about age-clearing for uploads
            // Users might expect "all" or nothing
            $optionsToClear['uploads'] = ['age' => $ageToClear];
        }


        if (empty($optionsToClear)) {
            // This case should be caught by form validation, but as a safeguard:
            // Add a message that nothing was selected.
            // Access mRoot via $this->mRoot now
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', defined('_AD_STDCACHE_SELECT_CACHE_TYPE_TO_CLEAR') ? _AD_STDCACHE_SELECT_CACHE_TYPE_TO_CLEAR : 'Please select at least one cache type to clear.');
            return STDCACHE_FRAME_VIEW_INPUT;
        }

        $results = $this->mCacheManager->clearCache($optionsToClear);

        // Add messages from CacheManager operation
        if (!empty($results['messages'])) {
            foreach ($results['messages'] as $message) {
                XCube_DelegateUtils::call('Legacy.Admin.Event.AddMessage', $message);
            }
        }
        if (!empty($results['errors'])) {
            foreach ($results['errors'] as $error) {
                 XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', $error);
            }
            return STDCACHE_FRAME_VIEW_ERROR; // If there were errors, show error view
        }

        if ($results['success']) {
            // TODO: Optionally add a general success message if not already covered by specific ones
            // XCube_DelegateUtils::call('Legacy.Admin.Event.AddMessage', _AD_STDCACHE_SUCCESS_CLEAR);
            return STDCACHE_FRAME_VIEW_SUCCESS;
        } else {
            // Optionally add a general error message
            // XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', _AD_STDCACHE_ERROR_CLEAR);
            return STDCACHE_FRAME_VIEW_ERROR;
        }
    }

    protected function _getPagetitle()
    {
        return defined('_MI_STDCACHE_ADMENU_CLEAR') ? _MI_STDCACHE_ADMENU_CLEAR : 'Clear Cache';
    }

    /**
     * Helper method to set common view attributes for the clear cache page
     * @param XCube_RenderTarget $render
     */
    protected function _setupViewCommon(&$render)
    {
        $render->setTemplateName('stdcache_admin_cache_clear.html');
        $render->setAttribute('actionForm', $this->mActionForm); // Pass the action form

        // Pass the module object
        if (is_object($this->mModule)) {
            $render->setAttribute('module', $this->mModule);
        }
        // Page title is usually set by the parent Action class automatically
        // $render->setAttribute('xoops_pagetitle', $this->getPageTitle());
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewInput($controller, $xoopsUser, $render);
        $this->_setupViewCommon($render);
        // Ensure form has defaults for the first view (GET request)
        // If it's a POST request that failed validation, fetch() already loaded the submitted values.
        if (xoops_getenv('REQUEST_METHOD') !== 'POST') {
            $this->mActionForm->loadDefaults();
        }
        return true;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewSuccess($controller, $xoopsUser, $render);
        // After successful clear, redirect to stats page or show success on the same page
        // Use $this->mRoot->mController->executeForward for consistency
        $this->mRoot->mController->executeForward('./index.php?action=CacheStats');
        // If not redirecting, then:
        // $this->_setupViewCommon($render);
        // $render->setTemplateName('stdcache_admin_cache_clear_success.html'); // Or similar
        return true;
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render);
        $this->_setupViewCommon($render);
        // The form already contains error messages if validation failed
        // If error came from CacheManager, messages added via DelegateUtils
        return true;
    }

    // executeViewIndex is not usually for an action like "clear",
    // but it would be similar to executeViewInput
    /*
    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewIndex($controller, $xoopsUser, $render); // Call parent
        $this->_setupViewCommon($render);
        return true;
    }
    */
    
}
