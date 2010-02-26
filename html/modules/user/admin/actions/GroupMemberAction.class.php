<?php
/**
 * @package user
 * @version $Id: GroupMemberAction.class.php,v 1.3 2007/12/15 12:25:29 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH."/core/XCube_PageNavigator.class.php";
require_once XOOPS_MODULE_PATH."/user/admin/forms/GroupMemberEditForm.class.php";

if (!defined('USER_GROUPMEMBER_DEFAULT_PERPAGE')) {
	define ('USER_GROUPMEMBER_DEFAULT_PERPAGE', 10);
}

/***
 * @internal
 * This function keeps difficult problems that this depens on the column's
 * block of X2 theme format.
 */
class User_GroupMemberAction extends User_Action
{
	/**
	 * Target group object
	 * 
	 * @var UserGroupsObject
	 */
	var $mGroup = null;
	
	/**
	 * group member list.
	 */
	var $mUsers = array();
	var $mPageNavi = null;

	/**
	 * No group member list.
	 */
	var $mNoUsers = array();
	var $mNoPageNavi = null;
	
	var $mActionForm = null;

	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		//
		// TODO Because this action has two form, we should prepare two action forms.
		//
		$this->mActionForm =new User_GroupMemberEditForm();
		$this->mActionForm->prepare();
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->_loadGroup();
		if (!is_object($this->mGroup)) {
			return USER_FRAME_VIEW_ERROR;
		}

		$memberHandler =& xoops_gethandler('member');
		$groupid = $this->mGroup->getVar('groupid');

		//
		// Get member list
		//
		$total = $memberHandler->getUserCountByGroup($groupid);

		$this->mPageNavi =new XCube_PageNavigator("./index.php?action=GroupMember", XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);	// TODO get controller->getUrl() ?
		$this->mPageNavi->setTotalItems($total);
		$this->mPageNavi->addExtra('groupid', $groupid);
		$this->mPageNavi->setPerpage(USER_GROUPMEMBER_DEFAULT_PERPAGE);

		$this->mPageNavi->fetch();
		
		$this->mUsers =& $memberHandler->getUsersByGroup($groupid, true, $this->mPageNavi->getPerpage(), $this->mPageNavi->getStart());

		//
		// Get no member list
		//
		$total = $memberHandler->getUserCountByNoGroup($groupid);

		$this->mNoPageNavi=new XCube_PageNavigator("./index.php?action=GroupMember", XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);	// TODO get controller->getUrl() ?
		$this->mNoPageNavi->setTotalItems($total);
		$this->mNoPageNavi->addExtra('groupid', $groupid);
		$this->mNoPageNavi->setPrefix("no");
		$this->mNoPageNavi->setPerpage(USER_GROUPMEMBER_DEFAULT_PERPAGE);

		$this->mNoPageNavi->fetch();
		
		$this->mNoUsers =& $memberHandler->getUsersByNoGroup($groupid, true, $this->mNoPageNavi->getPerpage(), $this->mNoPageNavi->getStart());

		return USER_FRAME_VIEW_INDEX;
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

		$memberHandler =& xoops_gethandler('member');
		$userHandler =& xoops_getmodulehandler('users');
		
		foreach($this->mActionForm->get('uid') as $uid => $value) {
			$user =& $userHandler->get($uid);
			
			if (is_object($user)) {
				if ($value == 1) {
					$memberHandler->addUserToGroup($this->mGroup->get('groupid'), $uid);
				}
				elseif ($value == 2) {
					$memberHandler->removeUserFromGroup($this->mGroup->get('groupid'), $uid);
				}
			}
		}
		
		return $this->getDefaultView($controller, $xoopsUser);
	}

	function _loadGroup()
	{
		if (!is_object($this->mGroup)) {
			$id = xoops_getrequest('groupid');

			$handler =& xoops_getmodulehandler('groups');
			$this->mGroup =& $handler->get($id);
		}
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("group_member.html");
		$render->setAttribute("group", $this->mGroup);

		$render->setAttribute("users", $this->mUsers);
		$render->setAttribute("pageNavi", $this->mPageNavi);

		$render->setAttribute("noUsers", $this->mNoUsers);
		$render->setAttribute("noPageNavi", $this->mNoPageNavi);
		
		$render->setAttribute("actionForm", $this->mActionForm);
	}
}

?>
