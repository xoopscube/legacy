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
            $setupCompleted = self::_setup();
        }
    }

    /**
     * _setup
     *
     * @param   void
     *
     * @return  bool
    **/
    public static function _setup()
    {
        $root =& XCube_Root::getSingleton();
        $instance = new self($root->mController);
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
//
        $this->mRoot->mDelegateManager->add('Legacy.Admin.Event.ModuleUpdate.Xupdate.Success', array(&$this, 'tableupdateXupdate'));
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

  /**
   *  @public
  */
  public function tableupdateXupdate(&$module, &$log)
  {
	if($module->getInfo('trust_dirname') == 'xupdate'){
		$db = $this->mRoot->mController->mDB;
		$sql = sprintf("SHOW TABLES LIKE '%s'", $db->prefix("xupdate_modulestore") );
		list($result) = $db->fetchRow($db->query($sql));
		if( empty($result) ){
			$sql ="CREATE TABLE ".$db->prefix("xupdate_modulestore")." (
					`id` int(11) unsigned NOT NULL  auto_increment,
					`sid` int(11) unsigned NOT NULL default 0,
					`dirname` varchar(25) NOT NULL default '',
					`version` smallint(5) unsigned default '100',
					`last_update` int(10) unsigned default '0',
					`type` varchar(255) NOT NULL default '',
					`trust_dirname` varchar(25) default '',
					`rootdirname` varchar(25) NOT NULL default '',
				PRIMARY KEY  (`id`),
				KEY sid (sid),
				KEY dirname (dirname)
				 ) ENGINE=MyISAM;
			";
			if( $db->query($sql) ){
				$log->add('Table '.htmlspecialchars($db->prefix("xupdate_modulestore")).' created.');
			}else{
				$log->add('Invalid SQL '.htmlspecialchars($sql));
			}
		}

	}

  }

}//END CLASS

?>