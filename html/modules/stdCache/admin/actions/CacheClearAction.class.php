<?php
/**
 * Standard cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8 
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once __DIR__ . '/../class/Action.class.php';
require_once __DIR__ . '/../class/CacheManager.class.php';
// require_once XOOPS_ROOT_PATH . '/modules/legacy/class/Legacy_Utils.class.php'; // Not directly used
require_once __DIR__ . '/../forms/CacheClearForm.class.php';

class stdCache_CacheClearAction extends stdCache_Action
{
    /**
     * @var XCube_Root
     */
    protected $mRoot = null;
    /**
     * @var XoopsModule
     */
    protected $mModule = null; // Module instance

    /**
     * @var stdCache_CacheClearForm
     */
    protected $mActionForm = null;

    /**
     * @var stdCache_CacheManager
     */
    protected $mCacheManager = null;


    public function __construct()
    {
        parent::__construct(); // Call parent constructor
        $this->mRoot =& XCube_Root::getSingleton();
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
        parent::prepare($controller, $xoopsUser, $moduleConfig); // Call parent

        if (!(is_object($xoopsUser) && $xoopsUser->isAdmin())) {
            $controller->executeForward(XOOPS_URL . '/');
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
            $this->mCacheManager = new stdCache_CacheManager();
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
        
        if (!$this->mActionForm->validate()) {
            return STDCACHE_FRAME_VIEW_INPUT; // Show form with errors
        }
        
        // Token validation should be part of XCube_ActionForm or handled explicitly if needed
        // For POST, XCube_ActionForm usually handles token validation if getTokenName() is defined.
        // Let's assume token validation is implicitly handled
        // if (!$this->mActionForm->getToken()->validate()) { // This might need specific token setup
        //     return STDCACHE_FRAME_VIEW_ERROR;
        // }
        
        $ageToClear = (int)$this->mActionForm->get('clear_age');
        // When constructing options for CacheManager:
        // The CacheManager::clearCache method needs to be adapted to accept age.
        // For example, if clearCache takes $options as an array:

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
            // TODO: Add a Help-Tip about age-clearing for uploads.
            // Users might expect "all" or nothing.
            $optionsToClear['uploads'] = ['age' => $ageToClear];
        }
        

        if (empty($optionsToClear)) {
            // TODO: This case should be caught by form validation, but as a safeguard:
            // Add a message that nothing was selected.
            // For example: $this->mRoot->mContext->mModule->setMsg(_AD_STDCACHE_SELECT_CACHE_TYPE_TO_CLEAR);
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

    protected function _setupViewCommon(&$render)
    {
        $render->setTemplateName('stdcache_admin_cache_clear.html');
        $render->setAttribute('actionForm', $this->mActionForm); // Pass the action form
        
        // Token handling for the form template
        // $render->setAttribute('xoops_token_name', $this->mActionForm->getTokenName());
        // $render->setAttribute('xoops_token', $this->mActionForm->getTokenHTML()); // Use getTokenHTML() for Smarty

        if (is_object($this->mModule)) {
            $render->setAttribute('module', $this->mModule);
        }
        $render->setAttribute('xoops_pagetitle', $this->getPageTitle()); // Already set by parent Action class
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewInput($controller, $xoopsUser, $render); // Call parent
        $this->_setupViewCommon($render);
        // Ensure form has defaults for the first view
        if (xoops_getenv('REQUEST_METHOD') !== 'POST') {
            $this->mActionForm->loadDefaults();
        }
        return true;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewSuccess($controller, $xoopsUser, $render); // Call parent
        // After successful clear, redirect to stats page or show success on the same page.
        // Redirecting is often cleaner.
        $controller->executeForward('./index.php?action=CacheStats'); 
        // If not redirecting, then:
        // $this->_setupViewCommon($render);
        // $render->setTemplateName('stdcache_admin_cache_clear_success.html'); // Or similar
        return true;
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render); // Call parent
        $this->_setupViewCommon($render);
        // The form already contains error messages if validation failed.
        // If error came from CacheManager, messages were added via DelegateUtils.
        return true;
    }

    // executeViewIndex is not typically used for an action like "clear",
    // but if it were, it would be similar to executeViewInput.
    /*
    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewIndex($controller, $xoopsUser, $render); // Call parent
        $this->_setupViewCommon($render);
        return true;
    }
    */
    
}
