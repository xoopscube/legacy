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

class stdCache_CacheClearAction extends stdCache_Action
{
    /**
     * @var XCube_Root
     */
    protected $mRoot = null;
    /**
     * @var XoopsModule
     */
    protected $mModule = null;

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
    public function __construct($adminFlag = false)
    {
        parent::__construct($adminFlag);
        $this->mRoot = XCube_Root::getSingleton();
        if (is_object($this->mRoot->mContext->mModule)) {
            $this->mModule = $this->mRoot->mContext->mModule->getXoopsModule();
        }
    }

    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        return is_object($xoopsUser) && $xoopsUser->isAdmin();
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        parent::prepare($controller, $xoopsUser, $moduleConfig);

        if (!(is_object($this->mRoot->mContext->mXoopsUser) && $this->mRoot->mContext->mXoopsUser->isAdmin())) {
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
        // Load default values
        $this->mActionForm->loadDefaults();
        return STDCACHE_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $this->mActionForm->fetch();

        // validate() handles token validation if getTokenName() is defined for POST
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
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', _AD_STDCACHE_CONFIRM_CLEAR);
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
            return STDCACHE_FRAME_VIEW_ERROR;
        }

        if ($results['success']) {
            // TODO: Optionally add a general success message
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
        return _MI_STDCACHE_ADMENU_CLEAR;
    }

    /**
     * Helper method to set common view attributes for the clear cache page
     * @param XCube_RenderTarget $render
     */
    protected function _setupViewCommon(&$render)
    {
        $render->setTemplateName('stdcache_admin_cache_clear.html');
        $render->setAttribute('actionForm', $this->mActionForm);

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
        // form defaults for the first view (GET request)
        // If it's a POST request that failed validation, fetch() already loaded the submitted values.
        if (xoops_getenv('REQUEST_METHOD') !== 'POST') {
            $this->mActionForm->loadDefaults();
        }
        return true;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewSuccess($controller, $xoopsUser, $render);
        // clear and redirect
        $this->mRoot->mController->executeForward('./index.php?action=CacheStats');
        // or customize if not redirecting:
        // $this->_setupViewCommon($render);
        // $render->setTemplateName('stdcache_admin_cache_clear_success.html');
        return true;
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render);
        $this->_setupViewCommon($render);
        // error messages added via DelegateUtils
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
