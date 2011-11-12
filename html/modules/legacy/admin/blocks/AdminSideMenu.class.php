<?php
/**
 *
 * @package Legacy
 * @version $Id: AdminSideMenu.class.php,v 1.3 2008/09/25 15:12:44 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

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
class Legacy_AdminSideMenu extends Legacy_AbstractBlockProcedure
{
	var $mModules = array();
	
	/**
	 * protected, but read OK.
	 * 
	 * @access protected
	 */
	var $mCurrentModule = null;

	function getName()
	{
		return "sidemenu";
	}

	function getTitle()
	{
		return "TEST: AdminSideMenu";
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
		$root =& XCube_Root::getSingleton();
		
		// load message catalog of legacy for _AD_LEGACY_LANG_NO_SETTING, even if the current module is not Legacy.
		$root->mLanguageManager->loadModuleAdminMessageCatalog('legacy'); 
		//
		$root->mLanguageManager->loadModinfoMessageCatalog('legacy');
		
		$controller =& $root->mController;
		$user =& $root->mController->mRoot->mContext->mXoopsUser;
		$render =& $this->getRenderTarget();
		$render->setAttribute('legacy_module', 'legacy');
		
		$this->mCurrentModule =& $controller->mRoot->mContext->mXoopsModule;
		
		if ($this->mCurrentModule->get('dirname') == 'legacy') {
			if (xoops_getrequest('action') == "help") {
				$moduleHandler =& xoops_gethandler('module');
				$t_module =& $moduleHandler->getByDirname(xoops_gethandler('dirname'));
				if (is_object($t_module)) {
					$this->mCurrentModule =& $t_module;
				}
			}
		}
		
		$db=&$controller->getDB();

		$mod = $db->prefix("modules");
		$perm = $db->prefix("group_permission");
		$groups = implode(",", $user->getGroups());
		
		//
		// Users who are belong to ADMIN GROUP have every permissions, so we have to prepare two kinds of SQL.
		//
		if ($root->mContext->mUser->isInRole('Site.Owner')) {
			$sql = "SELECT DISTINCT mid FROM ${mod} WHERE isactive=1 AND hasadmin=1 ORDER BY weight, mid";
		}
		else {
			$sql = "SELECT DISTINCT ${mod}.mid FROM ${mod},${perm} " .
				   "WHERE ${mod}.isactive=1 AND ${mod}.mid=${perm}.gperm_itemid AND ${perm}.gperm_name='module_admin' AND ${perm}.gperm_groupid IN (${groups}) " .
				   "AND ${mod}.hasadmin=1 " .
				   "ORDER BY ${mod}.weight, ${mod}.mid";
		}


		$result=$db->query($sql);
		
		$handler =& xoops_gethandler('module');
		
		while($row = $db->fetchArray($result)) {
			$xoopsModule =& $handler->get($row['mid']);
			$module =& Legacy_Utils::createModule($xoopsModule);

			$this->mModules[] =& $module;
			unset($module);
		}
		//
		$tpl = $db->prefix("tplfile");
		$tpl_modules = array();
		$sql = "SELECT DISTINCT tpl_module FROM ${tpl}";
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)) {
			$tpl_modules[] = $row['tpl_module'];
		}
		$render->setAttribute('tplmodules', $tpl_modules);
		//

		$render->setTemplateName('legacy_admin_block_sidemenu.html');
		$render->setAttribute('modules', $this->mModules);
		$render->setAttribute('currentModule', $this->mCurrentModule);
		
		$renderSystem =& $root->getRenderSystem($this->getRenderSystemName());
		
		$renderSystem->renderBlock($render);
	}
}

?>