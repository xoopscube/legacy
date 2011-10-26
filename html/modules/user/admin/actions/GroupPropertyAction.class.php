<?php
/**
 * @package user
 * @version $Id: GroupPropertyAction.class.php,v 1.4 2007/06/29 03:23:14 nobunobu Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH."/core/XCube_PageNavigator.class.php";
require_once XOOPS_MODULE_PATH."/user/admin/class/Permission.class.php";

/***
 * @internal
 * This function keeps difficult problems that this depens on the column's
 * block of X2 theme format.
 */
class User_GroupPropertyAction extends User_Action
{
	var $_mActiveModules = array();
	var $_mActiveModulesLoadedFlag = false;
	var $_mActiveBlocks = array();
	var $_mActiveBlocksLoadedFlag = false;
	
	var $mGroup;
	var $mPermissions;
	var $mSystemPermissions;
	var $mBlockPermissions;
	
	var $mUsers;
	var $mPageNavi;

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->_loadGroup();

		if (!is_object($this->mGroup)) {
			return USER_FRAME_VIEW_ERROR;
		}

		$root =& XCube_Root::getSingleton();
		$root->mLanguageManager->loadModuleAdminMessageCatalog("system");
		$root->mLanguageManager->loadModinfoMessageCatalog("system");

		//
		// Get member list
		//
		$memberHandler =& xoops_gethandler('member');

		$total = $memberHandler->getUserCountByGroup($this->mGroup->getVar('groupid'));
		$this->mPageNavi =new XCube_PageNavigator("./index.php?action=GroupProperty", XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);	// TODO get controller->getUrl() ?
		$this->mPageNavi->setTotalItems($total);
		$this->mPageNavi->addExtra('groupid', $this->mGroup->get('groupid'));

		$this->mPageNavi->fetch();
		
		$this->mUsers =& $memberHandler->getUsersByGroup($this->mGroup->getVar('groupid'), true, $this->mPageNavi->getPerPage(), $this->mPageNavi->getStart());

		$moduleHandler =& xoops_gethandler('module');
		//
		// Get...
		//
		if (file_exists(XOOPS_ROOT_PATH . "/modules/system/constants.php")) {
		    if ($moduleHandler->getByDirname('system')) {
        		require_once XOOPS_ROOT_PATH . "/modules/system/constants.php";
        		$fileHandler = opendir(XOOPS_ROOT_PATH . "/modules/system/admin");
        		while ($file = readdir($fileHandler)) {
        			$infoFile = XOOPS_ROOT_PATH . "/modules/system/admin/" . $file . "/xoops_version.php";
        			if (file_exists($infoFile)) {
        				require_once $infoFile;
        				if (!empty($modversion['category'])) {
        					$item =new User_PermissionSystemAdminItem($modversion['category'], $modversion['name']);
        					$this->mSystemPermissions[] =new User_Permission($this->mGroup->getVar('groupid'), $item);

        					unset($item);
        				}
        				unset($modversion);
        			}
        		}
        	}
        }
		//
		// Get module list
		//
		$this->_loadActiveModules();
	
		$t_activeModuleIDs = array();
	
		foreach ($this->_mActiveModules as $module) {
			$item =new User_PermissionModuleItem($module);
			$this->mPermissions[] =new User_Permission($this->mGroup->getVar('groupid'), $item);
		
			$t_activeModuleIDs[] = $module->get('mid');
		
			unset($module);
			unset($item);
		}

		//
		// Get block list
		//
		$blockHandler = xoops_gethandler('block');
		$this->_loadActiveBlocks();
		$idx = 0;
		foreach (array(0, 1, 3, 4, 5) as $side) {
			$this->mBlockPermissions[$idx] = array();

			foreach ($this->_mActiveBlocks[$side] as $block) {
				$item =new User_PermissionBlockItem($block);
				$this->mBlockPermissions[$idx][] =new User_Permission($this->mGroup->get('groupid'), $item);
				unset ($item);
				unset ($block);
			}
			
			$idx++;
		}

		return USER_FRAME_VIEW_INDEX;
	}
	
	function _loadActiveModules()
	{
		if ($this->_mActiveModulesLoadedFlag) {
			return;
		}
		
		$moduleHandler =& xoops_gethandler('module');
		$this->_mActiveModules =& $moduleHandler->getObjects(new Criteria('isactive', 1));
		
		$this->_mActiveModulesLoadedFlag = true;
	}
	
	function _loadActiveBlocks()
	{
		if ($this->_mActiveBlocksLoadedFlag) {
			return;
		}

		$this->_loadActiveModules();
		
		$t_activeModuleIDs = array();
		foreach ($this->_mActiveModules as $module) {
			$t_activeModuleIDs[] = $module->get('mid');
		}
		$t_activeModuleIDs[] = 0;

		$blockHandler = xoops_gethandler('block');
		foreach (array(0, 1, 3, 4, 5) as $side) {
			$this->_mActiveBlocks[$side] = array();
			$blockArr =& $blockHandler->getAllBlocks("object", $side, null);

			foreach ($blockArr as $block) {
				if ($block->get('visible') && in_array($block->get('mid'), $t_activeModuleIDs)) {
					$this->_mActiveBlocks[$side][] =& $block;
				}
				unset ($block);
			}
			
			unset($blockArr);
		}
		
		$this->_mActiveBlocksLoadedFlag = true;
	}

	function _loadGroup()
	{
		$id = xoops_getrequest('groupid');

		$handler =& xoops_getmodulehandler('groups');
		$this->mGroup =& $handler->get($id);
	}

	function executeViewIndex(&$controller,&$xoopsUser,&$render)
	{
		$render->setTemplateName("group_property.html");
		$render->setAttribute("group", $this->mGroup);
		$render->setAttribute("modulePermissions", $this->mPermissions);
		$render->setAttribute("blockPermissions", $this->mBlockPermissions);
		$render->setAttribute("systemPermissions", $this->mSystemPermissions);
		$render->setAttribute("users", $this->mUsers);
		$render->setAttribute("pageNavi", $this->mPageNavi);
	}
}

?>
