<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_RoleManager.class.php,v 1.3 2008/09/25 15:11:56 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/**
 * @note draft
 */
class Legacy_RoleManager
{
	/**
	 * Loads roles of the specific module with $module, and set loaded roles to
	 * the current principal.
	 * @static
	 * @param XoopsModule $module
	 */
	function loadRolesByModule(&$module)
	{
		static $cache;
		
		$root =& XCube_Root::getSingleton();
		$context =& $root->mContext;
		
		if ($module == null) {
			return;
		}
		
		if (isset($cache[$module->get('mid')])) {
			return;
		}
		
		$groups = is_object($context->mXoopsUser) ? $context->mXoopsUser->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
		
		$handler =& xoops_gethandler('groupperm');
		if ($handler->checkRight('module_read', $module->get('mid'), $groups)) {
			$context->mUser->addRole('Module.' . $module->get('dirname') . '.Visitor');
		}
		
		if (is_object($context->mXoopsUser) && $handler->checkRight('module_admin', $module->get('mid'), $groups)) {
			$context->mUser->addRole('Module.' . $module->get('dirname') . '.Admin');
		}
		
		$handler =& xoops_getmodulehandler('group_permission', 'legacy');
		$roleArr = $handler->getRolesByModule($module->get('mid'), $groups);
		foreach ($roleArr as $role) {
			$context->mUser->addRole('Module.' . $module->get('dirname') . '.' . $role);
		}
		
		$cache[$module->get('mid')] = true;
	}
	
	/**
	 * Loads roles of the specific module with $mid, and set loaded roles to
	 * the current principal.
	 * @param int $mid
	 */
	function loadRolesByMid($mid)
	{
		$handler =& xoops_gethandler('module');
		$module =& $handler->get($mid);
		
		if (is_object($module)) {
			$this->loadRolesByModule($module);
		}
	}

	/**
	 * Loads roles of the specific module with $dirname, and set loaded roles
	 * to the current principal.
	 * @param string $dirname The dirname of the specific module.
	 * @see loadRolesByMid()
	 */
	function loadRolesByDirname($dirname)
	{
		$handler =& xoops_gethandler('module');
		$module =& $handler->getByDirname($dirname);
		
		if (is_object($module)) {
			$this->loadRolesByModule($module);
		}
	}
}

?>
