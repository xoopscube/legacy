<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

if (!defined('XOOPS_ROOT_PATH')) exit;

class FileManager_AbstractIndexAction extends FileManager_Action
{
	var $mObject = null;
	var $mObjectHandler = null;

	function _getId()
	{
	}

	function &_getHandler()
	{
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return CONTENTS_FRAME_VIEW_ERROR;
		}

		return CONTENTS_VIEW_SUCCESS;
	}

	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView($controller, $xoopsUser);
	}
}

?>
