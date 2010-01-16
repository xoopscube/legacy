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

Lecat_AssetPreloadBase::prepare();


/**
 * Lecat_AssetPreloadBase
**/
class Lecat_AssetPreloadBase extends XCube_ActionFilter
{
    /**
     * prepare
     * 
     * @param   void
     * 
     * @return  void
    **/
    public static function prepare()
    {
        $root =& XCube_Root::getSingleton();
        $instance = new Lecat_AssetPreloadBase($root->mController);
        $root->mController->addActionFilter($instance);
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
        $this->mRoot->mDelegateManager->add('Module.lecat.Global.Event.GetAssetManager','Lecat_AssetPreloadBase::getManager');
        $this->mRoot->mDelegateManager->add('Legacy_Utils.CreateModule','Lecat_AssetPreloadBase::getModule');
        $this->mRoot->mDelegateManager->add('Legacy_Utils.CreateBlockProcedure','Lecat_AssetPreloadBase::getBlock');
    }

    /**
     * getManager
     * 
     * @param   Lecat_AssetManager  &$obj
     * @param   string  $dirname
     * 
     * @return  void
    **/
    public static function getManager(/*** Lecat_AssetManager ***/ &$obj,/*** string ***/ $dirname)
    {
        require_once LECAT_TRUST_PATH . '/class/AssetManager.class.php';
        $obj = Lecat_AssetManager::getInstance($dirname);
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
        if($module->getInfo('trust_dirname') == 'lecat')
        {
            require_once LECAT_TRUST_PATH . '/class/Module.class.php';
            $obj = new Lecat_Module($module);
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
