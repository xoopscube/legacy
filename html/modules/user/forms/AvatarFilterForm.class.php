<?php
/**
 * @package user
 * @version $Id: AvatarFilterForm.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractFilterForm.class.php";

/***
 * @internal
 *  This filter does not have the sorting feature. To fetch the list of preset
 * system avatars, it generates the criterion object which shows avatar_type
 * is 'S' && the displaying flag is true.
 */
class User_AvatarFilterForm extends User_AbstractFilterForm
{
	var $mSort = 0;

	function fetch()
	{
		parent::fetch();
		
		$this->_mCriteria->add(new Criteria('avatar_display', 1));
		$this->_mCriteria->add(new Criteria('avatar_type', 'S'));
	}
}

?>
