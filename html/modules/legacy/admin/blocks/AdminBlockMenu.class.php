<?php
/**
 * Admin Dashboard Menu
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

define('LEGACY_ADMIN_BLOCK_MENU_CACHEPREFIX', XOOPS_CACHE_PATH.'/'.urlencode(XOOPS_URL).'_admin_block_menu_');

/**
 * This is test menu block for control panel of legacy module.
 * This loads module objects by a permission of the current user.
 * Then this load module's adminmenu and module's information.
 *
 * [ASSIGN]
 *	Array of module objects.
 *
 * @package legacy
 */
class Legacy_AdminBlockMenu extends Legacy_AbstractBlockProcedure
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
        return 'block_menu';
    }

    public function getTitle()
    {
        return 'Dashboard Menu';
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

        $controller =& $root->mController;
        $user =& $root->mContext->mXoopsUser;
        $groups = implode(',', $user->getGroups());
        $cachePath = LEGACY_ADMIN_BLOCK_MENU_CACHEPREFIX . md5(XOOPS_SALT . "($groups)". $langMgr->mLanguageName).'.html';
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

        $db=&$controller->getDB();

        $mod = $db->prefix('modules');
        $perm = $db->prefix('group_permission');

        //
        // Users who belong to the ADMIN group have full permissions, so we need to prepare two types of SQL.
        //
        if ($root->mContext->mUser->isInRole('Site.Owner')) {
            $sql = "SELECT DISTINCT weight, mid FROM ${mod} WHERE isactive=1 AND hasadmin=1 ORDER BY weight, mid";
        } else {
            $sql = "SELECT DISTINCT ${mod}.weight, ${mod}.mid FROM ${mod},${perm} " .
                   "WHERE ${mod}.isactive=1 AND ${mod}.mid=${perm}.gperm_itemid AND ${perm}.gperm_name='module_admin' AND ${perm}.gperm_groupid IN (${groups}) " .
                   "AND ${mod}.hasadmin=1 " .
                   "ORDER BY ${mod}.weight, ${mod}.mid";
        }


        $result=$db->query($sql);

        $handler =& xoops_gethandler('module');

        while (list($weight, $mid) = $db->fetchRow($result)) {
            $xoopsModule = & $handler->get($mid);
            $module =& Legacy_Utils::createModule($xoopsModule, false);

            $this->mModules[] =& $module;
            unset($module);
        }
        //
        $tpl = $db->prefix('tplfile');
        $tpl_modules = [];
        $sql = "SELECT DISTINCT tpl_module FROM ${tpl}";
        $result = $db->query($sql);
        while ($row = $db->fetchArray($result)) {
            $tpl_modules[] = $row['tpl_module'];
        }
        $render->setAttribute('tplmodules', $tpl_modules);
        //

        $render->setTemplateName('legacy_admin_block_menu.html');
        $render->setAttribute('modules', $this->mModules);
        $render->setAttribute('currentModule', $this->mCurrentModule);

        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());

        $renderSystem->renderBlock($render);
        file_put_contents($cachePath, $render->mRenderBuffer);
    }

    public static function clearCache()
    {
        $adminDashboardMenucache = glob(LEGACY_ADMIN_BLOCK_MENU_CACHEPREFIX . '*.html');
        if ($adminDashboardMenucache) {
            foreach ($adminDashboardMenucache as $file) {
                unlink($file);
            }
        }
    }
}
