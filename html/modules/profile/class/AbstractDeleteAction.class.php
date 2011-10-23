<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once dirname(__FILE__) . "/AbstractEditAction.class.php";

class Profile_AbstractDeleteAction extends Profile_AbstractEditAction
{
	/**
	 * @protected
	 */
	function _isEnableCreate()
	{
		return false;
	}

	/**
	 * @public
	 */
	function prepare()
	{
		parent::prepare();
		return is_object($this->mObject);
	}

	/**
	 * @protected
	 */
	function _doExecute()
	{
		if ($this->mObjectHandler->delete($this->mObject)) {
			return PROFILE_FRAME_VIEW_SUCCESS;
		}
	
		return PROFILE_FRAME_VIEW_ERROR;
	}
}

?>
