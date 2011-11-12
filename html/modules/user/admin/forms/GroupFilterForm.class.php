<?php
/**
 * @package user
 * @version $Id: GroupFilterForm.class.php,v 1.1 2007/05/15 02:34:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractFilterForm.class.php";

define('GROUP_SORT_KEY_GROUPID', 1);
define('GROUP_SORT_KEY_NAME', 2);
define('GROUP_SORT_KEY_DESCRIPTION', 3);
define('GROUP_SORT_KEY_GROUP_TYPE', 4);
define('GROUP_SORT_KEY_MAXVALUE', 4);

define('GROUP_SORT_KEY_DEFAULT', GROUP_SORT_KEY_GROUPID);

class User_GroupFilterForm extends User_AbstractFilterForm
{
	var $mSortKeys = array(
		GROUP_SORT_KEY_DEFAULT => 'groupid',
		GROUP_SORT_KEY_GROUPID => 'groupid',
		GROUP_SORT_KEY_NAME => 'name',
		GROUP_SORT_KEY_DESCRIPTION => 'description',
		GROUP_SORT_KEY_GROUP_TYPE => 'group_type'
	);

	function getDefaultSortKey()
	{
		return GROUP_SORT_KEY_DEFAULT;
	}
	
	function fetch()
	{
		parent::fetch();
	
		if (isset($_REQUEST['groupid'])) {
			$this->mNavi->addExtra('groupid', xoops_getrequest('groupid'));
			$this->_mCriteria->add(new Criteria('groupid', xoops_getrequest('groupid')));
		}
	
		if (isset($_REQUEST['name'])) {
			$this->mNavi->addExtra('name', xoops_getrequest('name'));
			$this->_mCriteria->add(new Criteria('name', xoops_getrequest('name')));
		}
	
		if (isset($_REQUEST['group_type'])) {
			$this->mNavi->addExtra('group_type', xoops_getrequest('group_type'));
			$this->_mCriteria->add(new Criteria('group_type', xoops_getrequest('group_type')));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
