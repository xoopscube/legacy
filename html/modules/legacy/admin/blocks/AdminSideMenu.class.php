<?php
/**
 * Admin Side Menu
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

define('LEGACY_ADMINMENU_CACHEPREFIX', XOOPS_CACHE_PATH.'/'.urlencode(XOOPS_URL).'_admin_menu_');

/**
 * This is side menu block for control panel of legacy module.
 * This loads module objects by permissions of the current user.
 * Then this load module's adminmenu and module's information.
 *
 * [ASSIGN]
 *	Array of module objects.
 *
 * @package legacy
 */
class Legacy_AdminSideMenu extends Legacy_AbstractBlockProcedure
{
    public $mModules = [];

    /**
     * protected, but read OK.
     *
     * @access protected
     */
    public $mCurrentModule;

    public function getName()
    {
        return 'sidemenu';
    }

    public function getTitle()
    {
        return 'TEST: AdminSideMenu';
    }

    public function getEntryIndex()
    {
        return 0;
    }

    public function isEnableCache()
    {
        return false;
    }

    public function execute()
    {
        $root =& XCube_Root::getSingleton();

        // load admin message catalog of legacy for _AD_LEGACY_LANG_NO_SETTING, even if the current module is not Legacy.
        $langMgr =& $root->mLanguageManager;
        $langMgr->loadModuleAdminMessageCatalog('legacy');
        // load info 'modinfo' message catalog
        $langMgr->loadModinfoMessageCatalog('legacy');

        // User Group
        $controller =& $root->mController;
        $user =& $root->mContext->mXoopsUser;
        $groups = implode(',', $user->getGroups());
        $cachePath = LEGACY_ADMINMENU_CACHEPREFIX . md5(XOOPS_SALT . "($groups)". $langMgr->mLanguageName).'.html';

        // Render target & cache
        $render =& $this->getRenderTarget();
        if (file_exists($cachePath)) {
            $render->mRenderBuffer = file_get_contents($cachePath);
            return;
        }
        $render->setAttribute('legacy_module', 'legacy');

        $this->mCurrentModule =& $controller->mRoot->mContext->mXoopsModule;

        if (($this->mCurrentModule->get('dirname') === 'legacy') && xoops_getrequest('action') === 'help') {
            $moduleHandler =& xoops_gethandler('module');
            $t_module =& $moduleHandler->getByDirname(xoops_gethandler('legacy'));
            if (is_object($t_module)) {
                $this->mCurrentModule =& $t_module;
            }
        }

        // DB & Permissions
        $db=&$controller->getDB();

        $mod = $db->prefix('modules');
        $perm = $db->prefix('group_permission');

        //
        // Users who belong to the ADMIN group have full permissions, so we need to prepare two types of SQL.
        //
        if ($root->mContext->mUser->isInRole('Site.Owner')) {
            $sql = "SELECT DISTINCT weight, mid FROM {$mod} WHERE isactive=1 AND hasadmin=1 ORDER BY weight, mid";
        } else {
            $sql = "SELECT DISTINCT {$mod}.weight, {$mod}.mid FROM {$mod},{$perm} " .
                   "WHERE {$mod}.isactive=1 AND {$mod}.mid={$perm}.gperm_itemid AND {$perm}.gperm_name='module_admin' AND {$perm}.gperm_groupid IN ({$groups}) " .
                   "AND {$mod}.hasadmin=1 " .
                   "ORDER BY {$mod}.weight, {$mod}.mid";
        }

        $result=$db->query($sql);

        $handler =& xoops_gethandler('module');

        while (list($weight, $mid) = $db->fetchRow($result)) {
            $xoopsModule = & $handler->get($mid);
            $module =& Legacy_Utils::createModule($xoopsModule, false);

            $this->mModules[] =& $module;
            unset($module);
        }
        // Template
        $tpl = $db->prefix('tplfile');
        $tpl_modules = [];
        $sql = "SELECT DISTINCT tpl_module FROM {$tpl}";
        $result = $db->query($sql);
        while ($row = $db->fetchArray($result)) {
            $tpl_modules[] = $row['tpl_module'];
        }
        $render->setAttribute('tplmodules', $tpl_modules);

        // Set Template & attributes
        $render->setTemplateName('legacy_admin_block_sidemenu.html');
        $render->setAttribute('modules', $this->mModules);
        $render->setAttribute('currentModule', $this->mCurrentModule);

        // Render System
        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());
        // Render as Block
        $renderSystem->renderBlock($render);
        file_put_contents($cachePath, $render->mRenderBuffer);
    }

    public static function clearCache()
    {
        $adminMenucache = glob(LEGACY_ADMINMENU_CACHEPREFIX . '*.html');
        if ($adminMenucache) {
            foreach ($adminMenucache as $file) {
                unlink($file);
            }
        }
    }
}
