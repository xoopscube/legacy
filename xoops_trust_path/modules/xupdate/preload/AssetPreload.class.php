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
	    	if ($count = $handler->getCountHasUpdate()) {
	    		$this->mRoot->mLanguageManager->loadBlockMessageCatalog('xupdate');
	    		$checkimg = '<img src="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleView&amp;checkonly=1" width="1" height="1" alt="" />';
	    		$blockVal = array();
	    		$blockVal['adminlink'] = XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleStore&amp;filter=updated';
	    		$blockVal['pendingnum'] = $count;
	    		$blockVal['lang_linkname'] = _MB_XUPDATE_MODULEUPDATE . $checkimg;
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
		
		$no_notify_reg = '/action=(?:ModuleInstall|ModuleUpdate|ModuleStore&filter=updated)/';
		if (!preg_match($no_notify_reg, $_SERVER['QUERY_STRING'])) {
			$handler = Legacy_Utils::getModuleHandler('ModuleStore', 'xupdate');
			if ($count = $handler->getCountHasUpdate()) {
				$root =& XCube_Root::getSingleton();
				$root->mLanguageManager->loadBlockMessageCatalog('xupdate');
				$type = (! empty($_COOKIE['xupdate_ondemand']))? 'ondemand' : 'sticky';
				$arg = parse_url(XOOPS_URL);
				$cookie_path = $arg['path'] . '/';
				$notifyJS = <<<EOD
$('.notification.{$type}').notify({ type: '{$type}' });
$('.close').click(function(){
	$.cookie('xupdate_ondemand', '1', { path: '{$cookie_path}' });
});
$('.ondemand-button').click(function(){
	$.removeCookie('xupdate_ondemand', { path: '{$cookie_path}' });
});
EOD;
				$ondemandBtn = '';
				if ($type === 'ondemand') {
					$notifyJS .= "\n".'$(\'.ondemand-button\').show();';
					$ondemandBtn = '<div class="hide ondemand-button">
        			<a href="javascript:"><img src="'.XOOPS_URL.'/common/js/notify/images/icon-arrowdown.png" /></a>
					</div>';
				}
				$headerScript= $root->mContext->getAttribute('headerScript');
				$headerScript->addStylesheet('/common/js/notify/style/default.css');
				$headerScript->addStylesheet('/modules/xupdate/admin/templates/stylesheets/module.css');
				$headerScript->addLibrary('/common/js/notify/notification.js');
				$headerScript->addLibrary('/common/js/jquery.cookie.js');
				$headerScript->addScript($notifyJS);
				$result = '<div class="notification '.$type.' hide">
				<a class="close" href="javascript:"><img src="'.XOOPS_URL.'/common/js/notify/images/icon-close.png" /></a>
				<div>
				<a href="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleStore&amp;filter=updated">'.sprintf(_MB_XUPDATE_HAVE_UPDATEMODULE, $count).'</a>
				</div>
				</div>' . $ondemandBtn;
			}
		}

		$result .= '<img src="'.XOOPS_MODULE_URL.'/xupdate/admin/index.php?action=ModuleView&amp;checkonly=1" width="1" height="1" alt="" />';
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
