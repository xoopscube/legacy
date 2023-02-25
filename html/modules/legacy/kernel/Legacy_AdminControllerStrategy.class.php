<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_AdminControllerStrategy.class.php,v 1.5 2008/09/25 15:11:56 kilica Exp $
 * @copyright (c) 2005-2023 The XOOPSCube Project
 * @license   GPL 2.0
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
    public $_mSpecialActions = ['Help', 'CommentList'];

    public function __construct(&$controller)
    {
        global $xoopsOption;

        parent::__construct($controller);

        //
        // TODO We have to develop completed-switching-controller-mechanism.
        //
        if (!defined('LEGACY_DEPENDENCE_RENDERER')) {
            define('LEGACY_DEPENDENCE_RENDERER', 'Legacy_AdminRenderSystem');
        }

        $controller->mRoot->mContext->mBaseRenderSystemName = 'Legacy_AdminRenderSystem';

        //
        // Cover the spec of admin.php of the system module, for the compatibility.
        //
        if (isset($_REQUEST['fct']) && 'users' == $_REQUEST['fct']) {
            $GLOBALS['xoopsOption']['pagetype'] = 'user';
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
        if (1 == $this->mController->mRoot->getSiteConfig('Legacy', 'AutoPreload')) {
            $this->mController->_processPreload(XOOPS_ROOT_PATH . '/preload/admin');
        }
    }

    public function setupModuleContext(&$context, $dirname)
    {
        if (null == $dirname) {
            $dirname = 'legacy';
        }

        parent::setupModuleContext($context, $dirname);
    }

	/**
	 * Render Admin blocks directly without AdminDashboard settings 
     * ActionSearch and Admin SideMenu
	*/
    public function setupBlock()
    {
        require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminActionSearch.class.php';
        require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminSideMenu.class.php';
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

        if (null != $this->mController->mRoot->mContext->mModule) {
            $module =& $this->mController->mRoot->mContext->mXoopsModule;

            if ('legacy' == $module->get('dirname') && isset($_REQUEST['dirname'])) {
                if (in_array(xoops_getrequest('action'), $this->_mSpecialActions)) {
                    $handler =& xoops_gethandler('module');
                    $t_xoopsModule =& $handler->getByDirname(xoops_getrequest('dirname'));
                    $ret_module =& Legacy_Utils::createModule($t_xoopsModule);
                }
            } elseif ('legacy' == $module->get('dirname') && 'PreferenceEdit' == xoops_getrequest('action') && isset($_REQUEST['confmod_id'])) {
                $handler =& xoops_gethandler('module');
                $t_xoopsModule =& $handler->get((int)xoops_getrequest('confmod_id'));
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

        if (null != $this->mController->mRoot->mContext->mModule) {
            $dirname = $this->mController->mRoot->mContext->mXoopsModule->get('dirname');

            if ('legacy' == $dirname) {
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

    // TODO @gigamaster TRUST PATH lang catalog
    public function setupModuleLanguage()
    {
        $root =& XCube_Root::getSingleton();

        $root->mContext->mXoopsModule->loadInfo($root->mContext->mXoopsModule->get('dirname'));

        if (isset($root->mContext->mXoopsModule->modinfo['cube_style']) && false != $root->mContext->mXoopsModule->modinfo['cube_style']) {
            $root->mLanguageManager->loadModuleMessageCatalog($root->mContext->mXoopsModule->get('dirname'));
        }
        $root->mLanguageManager->loadModuleAdminMessageCatalog($root->mContext->mXoopsModule->get('dirname'));
        $root->mLanguageManager->loadModinfoMessageCatalog($root->mContext->mXoopsModule->get('dirname'));
    }
}
