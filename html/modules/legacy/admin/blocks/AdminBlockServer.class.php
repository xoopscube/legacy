<?php
/**
 * AdminBlockServer.class.php
 * @package    Legacy
 * @version    XCL 2.3.2
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// TODO cache block ?
//define('LEGACY_ADMINBLOCKSERVER_CACHEPREFIX', XOOPS_CACHE_PATH.'/'.urlencode(XOOPS_URL).'_admin_blockserver_');

class Legacy_AdminBlockServer extends Legacy_AbstractBlockProcedure
{
    public function getName()
    {
        return 'block_server';
    }

    public function getTitle()
    {
        return _AD_BLOCK_SERVER;
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
        $sys_info = [];
        $root =& XCube_Root::getSingleton();

        // load admin message catalog of legacy for _AD_LEGACY_LANG_NO_SETTING, even if the current module is not Legacy.
        $langMgr =& $root->mLanguageManager;
        $langMgr->loadModuleAdminMessageCatalog('legacy');
        // load info 'modinfo' message catalog
        $langMgr->loadModinfoMessageCatalog('legacy');


        /**
         * Assign Server
         * $sys_info @array
         */
        $sys_info['version']    = XOOPS_VERSION;

        if (defined('XOOPSFORM_DEPENDENCE_RENDER_SYSTEM')) {
            $sys_info['render'] = XOOPSFORM_DEPENDENCE_RENDER_SYSTEM;
        }

        $debugmode = (int)($root->mContext->mXoopsConfig['debug_mode']);
        if ($debugmode == 0) {
            $sys_info['debug'] = _MD_AM_DEBUGMODE0;
        } elseif ($debugmode == 1) {
            $sys_info['debug'] = _MD_AM_DEBUGMODE1;
        } elseif ($debugmode == 2) {
            $sys_info['debug'] = _MD_AM_DEBUGMODE2;
        } elseif ($debugmode == 3) {
            $sys_info['debug'] = _MD_AM_DEBUGMODE3;
        }

        $sys_info['theme']    = $root->mContext->mXoopsConfig['theme_set'];
        $sys_info['template'] = $root->mContext->mXoopsConfig['template_set'];
        $sys_info['language'] = $root->mContext->mXoopsConfig['language'];

            $db               = &$root->mController->getDB();
            $result           = $db->query('SELECT VERSION()');
            [$mysqlversion]   = $db->fetchRow($result);

        $sys_info['mysqlversion'] = $mysqlversion;
        $sys_info['phpversion']   = phpversion();
        $sys_info['os']           = substr(php_uname(), 0, 7);
        $sys_info['server']       = xoops_getenv('SERVER_SOFTWARE');

        $render =& $this->getRenderTarget();

        $render->setAttribute('legacy_module', 'legacy');
        $render->setAttribute('sys_info', $sys_info);

        $render->setTemplateName('legacy_admin_block_server.html');

        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());

        $renderSystem->renderBlock($render);

        // file_put_contents($cachePath, $render->mRenderBuffer);
    }


    public function hasResult()
    {
        return true;
    }

    public function &getResult()
    {
        $dmy = 'dummy';
        return $dmy;
    }

    public function getRenderSystemName()
    {
        return 'Legacy_AdminRenderSystem';
    }
}
