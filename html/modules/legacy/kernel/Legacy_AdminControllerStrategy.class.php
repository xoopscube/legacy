<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_AdminControllerStrategy.class.php,v 1.5 2008/09/25 15:11:56 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * @internal
 */
class Legacy_AdminControllerStrategy extends Legacy_AbstractControllerStrategy
{
    public $mStatusFlag = LEGACY_CONTROLLER_STATE_ADMIN;
    
    /**
     * @var XCube_Delegate
     * @param XCube_Controller &$controller
     */
    public $mSetupBlock = null;
    
    /**
     *  If this array includes current action, getVirtualCurrentModule() returns
     * the module object that specified by dirname.
     * 
     * @access private
     */
    public $_mSpecialActions = array("Help", "CommentList");
    // !Fix PHP7
    public function __construct(&$controller)
    //public function Legacy_AdminControllerStrategy(&$controller)
    {
        global $xoopsOption;
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($controller);
        //parent::Legacy_AbstractControllerStrategy($controller);
        
        //
        // TODO We have to develop complated-switching-controller-mechanizm.
        //
        if (!defined("LEGACY_DEPENDENCE_RENDERER")) {
            define("LEGACY_DEPENDENCE_RENDERER", "Legacy_AdminRenderSystem");
        }
        
        $controller->mRoot->mContext->mBaseRenderSystemName = "Legacy_AdminRenderSystem";

        //
        // Cover the spec of admin.php of the system module, for the compatibility.
        //
        if (isset($_REQUEST['fct']) && $_REQUEST['fct'] == "users") {
            $GLOBALS['xoopsOption']['pagetype'] = "user";
        }
        
        $this->mSetupBlock =new XCube_Delegate();
        $this->mSetupBlock->register('Legacy_AdminControllerStrategy.SetupBlock');
    }

    public function _setupFilterChain()
    {
        parent::_setupFilterChain();

        //
        // Auto pre-loading.
        //
        if ($this->mController->mRoot->getSiteConfig('Legacy', 'AutoPreload') == 1) {
            $this->mController->_processPreload(XOOPS_ROOT_PATH . "/preload/admin");
        }
    }
    
    public function setupModuleContext(&$context, $dirname)
    {
        if ($dirname == null) {
            $dirname = 'legacy';
        }
        
        parent::setupModuleContext($context, $dirname);
    }
    
    public function setupBlock()
    {
        require_once XOOPS_LEGACY_PATH . "/admin/blocks/AdminActionSearch.class.php";
        require_once XOOPS_LEGACY_PATH . "/admin/blocks/AdminSideMenu.class.php";
        $this->mController->_mBlockChain[] =new Legacy_AdminActionSearch();
        $this->mController->_mBlockChain[] =new Legacy_AdminSideMenu();
        
        $this->mSetupBlock->call(new XCube_Ref($this->mController));
    }

    public function _processPreBlockFilter()
    {
        parent::_processPreBlockFilter();
        $this->mController->_processModulePreload('/admin/preload');
    }

    public function &getVirtualCurrentModule()
    {
        static $ret_module;
        if (is_object($ret_module)) {
            return $ret_module;
        }
        
        if ($this->mController->mRoot->mContext->mModule != null) {
            $module =& $this->mController->mRoot->mContext->mXoopsModule;
            
            if ($module->get('dirname') == "legacy" && isset($_REQUEST['dirname'])) {
                if (in_array(xoops_getrequest('action'), $this->_mSpecialActions)) {
                    $handler =& xoops_gethandler('module');
                    $t_xoopsModule =& $handler->getByDirname(xoops_getrequest('dirname'));
                    $ret_module =& Legacy_Utils::createModule($t_xoopsModule);
                }
            } elseif ($module->get('dirname') == "legacy" && xoops_getrequest('action') == 'PreferenceEdit' && isset($_REQUEST['confmod_id'])) {
                $handler =& xoops_gethandler('module');
                $t_xoopsModule =& $handler->get(intval(xoops_getrequest('confmod_id')));
                $ret_module =& Legacy_Utils::createModule($t_xoopsModule);
            }
            
            if (!is_object($ret_module)) {
                $ret_module =& Legacy_Utils::createModule($module);
            }
        }
        
        return $ret_module;
    }

    public function &getMainThemeObject()
    {
        $handler =& xoops_getmodulehandler('theme', 'legacy');
        $theme =& $handler->create();
        
        //
        // TODO Load manifesto here.
        //
        $theme->set('dirname', $this->mController->mRoot->mSiteConfig['Legacy']['Theme']);
        $theme->set('render_system', 'Legacy_AdminRenderSystem');
        
        return $theme;
    }
    
    public function isEnableCacheFeature()
    {
        return false;
    }
    
    public function enableAccess()
    {
        $principal =& $this->mController->mRoot->mContext->mUser;
        
        if (!$principal->mIdentity->isAuthenticated()) {
            return false;
        }
        
        if ($this->mController->mRoot->mContext->mModule != null) {
            $dirname = $this->mController->mRoot->mContext->mXoopsModule->get('dirname');
            
            if ($dirname == 'legacy') {
                return $principal->isInRole('Site.Administrator');
            } elseif (defined('_LEGACY_ALLOW_ACCESS_FROM_ANY_ADMINS_')) {
                return $this->mController->mRoot->mContext->mXoopsUser->isAdmin(0);
            }
            
            return $principal->isInRole("Module.${dirname}.Admin");
        } else {
            return $principal->isInRole('Site.Administrator');
        }
        
        return false;
    }
    
    public function setupModuleLanguage()
    {
        $root =& XCube_Root::getSingleton();
        
        $root->mContext->mXoopsModule->loadInfo($root->mContext->mXoopsModule->get('dirname'));
        
        if (isset($root->mContext->mXoopsModule->modinfo['cube_style']) && $root->mContext->mXoopsModule->modinfo['cube_style'] != false) {
            $root->mLanguageManager->loadModuleMessageCatalog($root->mContext->mXoopsModule->get('dirname'));
        }
        $root->mLanguageManager->loadModuleAdminMessageCatalog($root->mContext->mXoopsModule->get('dirname'));
        $root->mLanguageManager->loadModinfoMessageCatalog($root->mContext->mXoopsModule->get('dirname'));
    }
}
