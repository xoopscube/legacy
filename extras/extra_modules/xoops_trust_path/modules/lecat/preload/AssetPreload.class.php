<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

if(!defined('LECAT_TRUST_PATH'))
{
	define('LECAT_TRUST_PATH',XOOPS_TRUST_PATH . '/modules/lecat');
}

require_once LECAT_TRUST_PATH . '/class/LecatUtils.class.php';



/**
 * Lecat_AssetPreloadBase
**/
class Lecat_AssetPreloadBase extends XCube_ActionFilter
{
	public $mDirname = null;

    /**
     * prepare
     * 
     * @param   string	$dirname
     * 
     * @return  void
    **/
    public static function prepare(/*** string ***/ $dirname)
    {
        $root =& XCube_Root::getSingleton();
        $instance = new Lecat_AssetPreloadBase($root->mController);
        $instance->mDirname = $dirname;
        $root->mController->addActionFilter($instance);
    }

	/**
	 * preBlockFilter
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function preBlockFilter()
	{
		$file = LECAT_TRUST_PATH . '/class/DelegateFunctions.class.php';
	
		$this->mRoot->mDelegateManager->add('Module.lecat.Global.Event.GetAssetManager','Lecat_AssetPreloadBase::getManager');
		$this->mRoot->mDelegateManager->add('Legacy_Utils.CreateModule','Lecat_AssetPreloadBase::getModule');
		$this->mRoot->mDelegateManager->add('Legacy_Utils.CreateBlockProcedure','Lecat_AssetPreloadBase::getBlock');
		$this->mRoot->mDelegateManager->add('Module.'.$this->mDirname.'.Global.Event.GetNormalUri','Lecat_CoolUriDelegate::getNormalUri', $file);
		$this->mRoot->mDelegateManager->add('Legacy_ImageClient.GetClientList','Lecat_ImageClientDelegate::getClientList', $file);
	
		//Legacy Category Delegate
		$prefix = 'Legacy_Category.' . $this->mDirname;
		$this->mRoot->mDelegateManager->add($prefix .'.GetTitle','Lecat_DelegateFunctions::getTitle', $file);
		$this->mRoot->mDelegateManager->add($prefix .'.GetTree','Lecat_DelegateFunctions::getTree', $file);
		$this->mRoot->mDelegateManager->add($prefix .'.GetTitleList','Lecat_DelegateFunctions::getTitleList', $file);
		$this->mRoot->mDelegateManager->add($prefix .'.HasPermission','Lecat_DelegateFunctions::hasPermission', $file);
		$this->mRoot->mDelegateManager->add($prefix .'.GetParent','Lecat_DelegateFunctions::getParent', $file);
		$this->mRoot->mDelegateManager->add($prefix .'.GetChildren','Lecat_DelegateFunctions::getChildren', $file);
		$this->mRoot->mDelegateManager->add($prefix .'.GetCatPath','Lecat_DelegateFunctions::getCatPath', $file);
		$this->mRoot->mDelegateManager->add($prefix .'.GetPermittedIdList','Lecat_DelegateFunctions::getPermittedIdList', $file);
	}

	/**
	 * getManager
	 * 
	 * @param	Lecat_AssetManager	&$obj
	 * @param	string	$dirname
	 * 
	 * @return	void
	**/
	public static function getManager(/*** Lecat_AssetManager ***/ &$obj,/*** string ***/ $dirname)
	{
		require_once LECAT_TRUST_PATH . '/class/AssetManager.class.php';
		$obj = Lecat_AssetManager::getInstance($dirname);
	}

	/**
	 * getModule
	 * 
	 * @param	Legacy_AbstractModule  &$obj
	 * @param	XoopsModule  $module
	 * 
	 * @return	void
	**/
	public static function getModule(/*** Legacy_AbstractModule ***/ &$obj,/*** XoopsModule ***/ $module)
	{
		if($module->getInfo('trust_dirname') == 'lecat')
		{
			require_once LECAT_TRUST_PATH . '/class/Module.class.php';
			$obj = new Lecat_Module($module);
		}
	}

	/**
	 * getBlock
	 * 
	 * @param	Legacy_AbstractBlockProcedure  &$obj
	 * @param	XoopsBlock	$block
	 * 
	 * @return	void
	**/
	public static function getBlock(/*** Legacy_AbstractBlockProcedure ***/ &$obj,/*** XoopsBlock ***/ $block)
	{
		$moduleHandler =& Lecat_Utils::getXoopsHandler('module');
		$module =& $moduleHandler->get($block->get('mid'));
		if(is_object($module) && $module->getInfo('trust_dirname') == 'lecat')
		{
			require_once LECAT_TRUST_PATH . '/blocks/' . $block->get('func_file');
			$className = 'Lecat_' . substr($block->get('show_func'), 4);
			$obj = new $className($block);
		}
	}
}

?>
