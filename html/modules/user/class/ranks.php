<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class UserRanksObject extends XoopsSimpleObject
{
	function UserRanksObject()
	{
		$this->initVar('rank_id', XOBJ_DTYPE_INT, 0, true);
		$this->initVar('rank_title', XOBJ_DTYPE_STRING, '', true, 50);
		$this->initVar('rank_min', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('rank_max', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('rank_special', XOBJ_DTYPE_BOOL, '0', true);
		$this->initVar('rank_image', XOBJ_DTYPE_STRING, '', false, 255);
	}
}

class UserRanksHandler extends XoopsObjectGenericHandler
{
	var $mTable = "ranks";
	var $mPrimary = "rank_id";
	var $mClass = "UserRanksObject";

	function delete(&$obj)
	{
		@unlink(XOOPS_UPLOAD_PATH . "/" . $obj->get('rank_image'));
		return parent::delete($obj);
	}
}

?>
