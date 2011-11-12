<?php
/**
 * @package user
 * @version $Id: GroupPermAction.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH."/user/admin/actions/GroupPropertyAction.class.php";
require_once XOOPS_MODULE_PATH."/user/admin/forms/GroupPermEditForm.class.php";

/***
 * @internal
 * This function keeps difficult problems that this depens on the column's
 * block of X2 theme format.
 */
class User_GroupPermAction extends User_GroupPropertyAction
{
	var $mActionForm = null;
	
	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
		$this->mActionForm =new User_GroupPermEditForm();
		$this->mActionForm->prepare();
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		$this->_loadGroup();
		
		if (!is_object($this->mGroup)) {
			return USER_FRAME_VIEW_ERROR;
		}
		
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return $this->getDefaultView($controller, $xoopsUser);
		}
		
		//
		// Reset group permission
		//
		$gpermHandler =& xoops_gethandler('groupperm');
		
		$criteria =new CriteriaCompo();
		$criteria->add(new Criteria('gperm_groupid', $this->mGroup->get('groupid')));
		$criteria->add(new Criteria('gperm_modid', 1));
		$criteria->add(new Criteria('gperm_name', 'system_admin'));
		$gpermHandler->deleteAll($criteria);

		foreach ($this->mActionForm->get('system') as $sid => $value) {
			$item =new User_PermissionSystemAdminItem($sid, null);
			$perm =new User_Permission($this->mGroup->get('groupid'), $item);
			
			$perm->save();

			unset($item);
			unset($perm);
		}
		
		$moduleHandler =& xoops_gethandler('module');
		$modPerms = array();

		//
		// Store module read permission
		//
		$this->_loadActiveModules();
		foreach ($this->_mActiveModules as $module)	{
			$value = $this->mActionForm->get('module', $module->get('mid'));
			if ($value) {
				$gpermHandler->addRight('module_read', $module->get('mid'), $this->mGroup->get('groupid'));
			}
			else {
				$gpermHandler->removeRight('module_read', $module->get('mid'), $this->mGroup->get('groupid'));
			}
		}

		foreach ($this->_mActiveModules as $module)	{
			$value = $this->mActionForm->get('module_admin', $module->get('mid'));
			if ($value) {
				$gpermHandler->addRight('module_admin', $module->get('mid'), $this->mGroup->get('groupid'));
			}
			else {
				$gpermHandler->removeRight('module_admin', $module->get('mid'), $this->mGroup->get('groupid'));
			}
		}

		$blockHandler =& xoops_gethandler('block');

		$this->_loadActiveBlocks();
		foreach ($this->_mActiveBlocks as $side => $blocks) {
			foreach ($blocks as $block) {
				$value = $this->mActionForm->get('block', $block->get('bid'));
				if ($value) {
					$gpermHandler->addRight('block_read', $block->get('bid'), $this->mGroup->get('groupid'));
				}
				elseif (is_object($block) && !$value) {
					$gpermHandler->removeRight('block_read', $block->get('bid'), $this->mGroup->get('groupid'));
				}
			}
		}
		
		return USER_FRAME_VIEW_SUCCESS;
	}
	
	function executeViewIndex(&$controller,&$xoopsUser,&$render)
	{
		$render->setTemplateName("group_perm.html");
		$render->setAttribute("group", $this->mGroup);
		$render->setAttribute("actionForm", $this->mActionForm);
		$render->setAttribute("modulePermissions", $this->mPermissions);
		$render->setAttribute("blockPermissions", $this->mBlockPermissions);
		$render->setAttribute("systemPermissions", $this->mSystemPermissions);
	}

	function executeViewSuccess(&$controller,&$xoopsUser,&$render)
	{
		$controller->executeForward("index.php?action=GroupPerm&groupid=" . $this->mGroup->getVar('groupid'));
	}
}

?>
