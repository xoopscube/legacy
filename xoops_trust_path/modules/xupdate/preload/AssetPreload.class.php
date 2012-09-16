<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

if(!defined('XUPDATE_TRUST_PATH'))
{
    define('XUPDATE_TRUST_PATH',XOOPS_TRUST_PATH . '/modules/xupdate');
}

require_once XUPDATE_TRUST_PATH . '/class/XupdateUtils.class.php';

/**
 * Xupdate_AssetPreloadBase
**/
class Xupdate_AssetPreloadBase extends XCube_ActionFilter
{
	public $mDirname = null;
	protected $blockInstance = null;
    
    /**
     * prepare
     *
     * @param   string  $dirname
     *
     * @return  void
    **/
    public static function prepare(/*** string ***/ $dirname)
    {
        static $setupCompleted = false;
        if(!$setupCompleted)
        {
            $setupCompleted = self::_setup($dirname);
        }
    }

    /**
     * _setup
     *
     * @param   void
     *
     * @return  bool
    **/
    public static function _setup($dirname)
    {
        $root =& XCube_Root::getSingleton();
        $instance = new self($root->mController);
        $instance->mDirname = $dirname;
        $root->mController->addActionFilter($instance);
        return true;
    }

    /**
     * preBlockFilter
     *
     * @param   void
     *
     * @return  void
    **/
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Module.xupdate.Global.Event.GetAssetManager','Xupdate_AssetPreloadBase::getManager');
        $this->mRoot->mDelegateManager->add('Legacy_Utils.CreateModule','Xupdate_AssetPreloadBase::getModule');
        $this->mRoot->mDelegateManager->add('Legacy_Utils.CreateBlockProcedure','Xupdate_AssetPreloadBase::getBlock');
        
        $this->mRoot->mDelegateManager->add('Legacy.Admin.Event.ModuleListSave.Success', array(&$this, '_setNeedCacheRemake'));
        $this->mRoot->mDelegateManager->add('Legacy.Admin.Event.ModuleInstall.Success', array(&$this, '_setNeedCacheRemake'));
        $this->mRoot->mDelegateManager->add('Legacy.Admin.Event.ModuleUpdate.Success', array(&$this, '_setNeedCacheRemake'));
        $this->mRoot->mDelegateManager->add('Legacy.Admin.Event.ModuleUninstall.Success', array(&$this, '_setNeedCacheRemake'));

        $this->mRoot->mDelegateManager->add('Legacy_TagClient.GetClientList','Xupdate_TagClientDelegate::getClientList', XUPDATE_TRUST_PATH.'/class/callback/TagClient.class.php');
        $this->mRoot->mDelegateManager->add('Legacy_TagClient.'.$this->mDirname.'.GetClientData','Xupdate_TagClientDelegate::getClientData', XUPDATE_TRUST_PATH.'/class/callback/TagClient.class.php');

        $this->mRoot->mDelegateManager->add('Legacyblock.Waiting.Show',array(&$this, 'callbackWaitingShow'));

        $this->mRoot->mDelegateManager->add('Legacy_AdminControllerStrategy.SetupBlock', array(&$this, 'onXupdateSetupBlock'));
    }
	
    public function postFilter()
    {
    	if (! defined('LEGACY_INSTALLERCHECKER_ACTIVE')) {
    		define('LEGACY_INSTALLERCHECKER_ACTIVE', false);
    	}
    	if (! defined('XUPDATE_INSTALLERCHECKER_ACTIVE')) {
    		define('XUPDATE_INSTALLERCHECKER_ACTIVE', true);
    	}
    	if (! LEGACY_INSTALLERCHECKER_ACTIVE && XUPDATE_INSTALLERCHECKER_ACTIVE && is_dir(XOOPS_ROOT_PATH . '/install')) {
    		$root =& XCube_Root::getSingleton();
    		if ($root->mContext->mUser->isInRole('Site.Owner')) {
    			if (strpos($_SERVER['REQUEST_URI'], '/xupdate/admin/index.php?action=InstallChecker') === false
    			 && strpos($_SERVER['REQUEST_URI'], '/xupdate/admin/index.php?action=ModuleView') === false
    			 && strpos($_SERVER['REQUEST_URI'], '/legacy/admin/index.php?action=Preference') === false) {
    				while( ob_get_level() && @ ob_end_clean() ){}
    				header('Location:' . XOOPS_MODULE_URL . '/xupdate/admin/index.php?action=InstallChecker');
	    			exit();
    			}
    		} else {
    			$root->mLanguageManager->loadModuleMessageCatalog('legacy');
    			$xoopsConfig = $root->mContext->mXoopsConfig;
    			
    			require_once XOOPS_ROOT_PATH . '/class/template.php';
    			$xoopsTpl =new XoopsTpl();
    			$xoopsTpl->assign(array('xoops_sitename' => htmlspecialchars($xoopsConfig['sitename']),
    					'xoops_themecss' => xoops_getcss(),
    					'xoops_imageurl' => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/',
    					'lang_message_confirm' => XCube_Utils::formatMessage(_MD_LEGACY_MESSAGE_INSTALL_COMPLETE_CONFIRM, XOOPS_ROOT_PATH . "/install"),
    					'lang_message_warning' => XCube_Utils::formatMessage(_MD_LEGACY_MESSAGE_INSTALL_COMPLETE_WARNING, XOOPS_ROOT_PATH . "/install")
    			));
    			
    			$xoopsTpl->compile_check = true;
    				
    			// @todo filebase template with absolute file path
    			$xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/legacy/templates/legacy_install_completed.html');
    			exit();
    		}
    	}
    }
    
	public function _setNeedCacheRemake() {
		$handler = Legacy_Utils::getModuleHandler('store', 'xupdate');
		$handler->setNeedCacheRemake();
	}

    /**
     * getManager
     *
     * @param   Xupdate_AssetManager  &$obj
     * @param   string  $dirname
     *
     * @return  void
    **/
    public static function getManager(/*** Xupdate_AssetManager ***/ &$obj,/*** string ***/ $dirname)
    {
        require_once XUPDATE_TRUST_PATH . '/class/AssetManager.class.php';
        $obj = Xupdate_AssetManager::getInstance($dirname);
    }

    /**
     * getModule
     *
     * @param   Legacy_AbstractModule  &$obj
     * @param   XoopsModule  $module
     *
     * @return  void
    **/
    public static function getModule(/*** Legacy_AbstractModule ***/ &$obj,/*** XoopsModule ***/ $module)
    {
        if($module->getInfo('trust_dirname') == 'xupdate')
        {
            require_once XUPDATE_TRUST_PATH . '/class/Module.class.php';
            $obj = new Xupdate_Module($module);
        }
    }

    /**
     * getBlock
     *
     * @param   Legacy_AbstractBlockProcedure  &$obj
     * @param   XoopsBlock  $block
     *
     * @return  void
    **/
    public static function getBlock(/*** Legacy_AbstractBlockProcedure ***/ &$obj,/*** XoopsBlock ***/ $block)
    {
        $moduleHandler =& Xupdate_Utils::getXoopsHandler('module');
        $module =& $moduleHandler->get($block->get('mid'));
        if(is_object($module) && $module->getInfo('trust_dirname') == 'xupdate')
        {
            require_once XUPDATE_TRUST_PATH . '/blocks/' . $block->get('func_file');
            $className = 'Xupdate_' . substr($block->get('show_func'), 4);
            $obj = new $className($block);
        }
    }

    function callbackWaitingShow(& $modules)
    {
    	if ($this->mRoot->mContext->mUser->isInRole('Site.Administrator')) {
    		$handler = Legacy_Utils::getModuleHandler('ModuleStore', 'xupdate');
	    	if ($count = $handler->getCountHasUpdate('module')) {
	    		$this->mRoot->mLanguageManager->loadBlockMessageCatalog('xupdate');
	    		$checkimg = '<img src="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleView&amp;checkonly=1" width="1" height="1" alt="" />';
	    		$blockVal = array();
	    		$blockVal['adminlink'] = XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleStore&amp;filter=updated';
	    		$blockVal['pendingnum'] = $count;
	    		$blockVal['lang_linkname'] = _MB_XUPDATE_MODULEUPDATE . $checkimg;
	    		$modules[] = $blockVal;
	    	}
	    	if ($count = $handler->getCountHasUpdate('theme')) {
	    		$this->mRoot->mLanguageManager->loadBlockMessageCatalog('xupdate');
	    		$checkimg = '<img src="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleView&amp;checkonly=1" width="1" height="1" alt="" />';
	    		$blockVal = array();
	    		$blockVal['adminlink'] = XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ThemeStore&amp;filter=updated';
	    		$blockVal['pendingnum'] = $count;
	    		$blockVal['lang_linkname'] = _MB_XUPDATE_THEMEUPDATE . $checkimg;
	    		$modules[] = $blockVal;
	    	}
	    	if ($count = $handler->getCountHasUpdate('preload')) {
	    		$this->mRoot->mLanguageManager->loadBlockMessageCatalog('xupdate');
	    		$checkimg = '<img src="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleView&amp;checkonly=1" width="1" height="1" alt="" />';
	    		$blockVal = array();
	    		$blockVal['adminlink'] = XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=PreloadStore&amp;filter=updated';
	    		$blockVal['pendingnum'] = $count;
	    		$blockVal['lang_linkname'] = _MB_XUPDATE_PRELOADUPDATE . $checkimg;
	    		$modules[] = $blockVal;
	    	}
    	}
    }

    public function onXupdateSetupBlock($controller)
    {
    	if ( $this->_isAdminPage() )
    	{
    		$this->blockInstance = new Xupdate_Block();
    		$this->mController->_mBlockChain[] =& $this->blockInstance;
    	}
    }
    
    protected function _isAdminPage()
    {
    	return ( strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false || strpos($_SERVER['SCRIPT_NAME'], '/admin.php') !== false );
    }

}//END CLASS

class Xupdate_Block extends Legacy_AbstractBlockProcedure
{
	function getName()
	{
		return "Xupdate_Block";
	}

	function getTitle()
	{
		return "Xupdate_Block";
	}

	function getEntryIndex()
	{
		return 0;
	}

	function isEnableCache()
	{
		return false;
	}

	function execute()
	{
		$result = '';
		
		// load data refrash image by JS
		$root =& XCube_Root::getSingleton();
		$headerScript= $root->mContext->getAttribute('headerScript');
		$headerScript->addScript('var xupdateCheckImg=new Image();xupdateCheckImg.src="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleView&checkonly=1";');
		
		$no_notify_reg = '/action=(?:(?:Module|Theme|Preload)Install|(?:Module|Theme|Preload)Update|(?:Module|Theme|Preload)Store&filter=updated)/';
		if (!preg_match($no_notify_reg, $_SERVER['QUERY_STRING'])) {
			$handler = Legacy_Utils::getModuleHandler('ModuleStore', 'xupdate');
			$result = $handler->getNotifyHTML();
		}
		
		$render =& $this->getRenderTarget();
		$render->setResult($result);
	}

	function hasResult()
	{
		return true;
	}

	function &getResult()
	{
		$dmy = "dummy";
		return $dmy;
	}

	function getRenderSystemName()
	{
		return 'Legacy_AdminRenderSystem';
	}
}
?>
