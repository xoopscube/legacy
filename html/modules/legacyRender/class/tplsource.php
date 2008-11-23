<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyRenderTplsourceObject extends XoopsSimpleObject
{
	function LegacyRenderTplsourceObject()
	{
		$this->initVar('tpl_id', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('tpl_source', XOBJ_DTYPE_TEXT, '', true);
	}
}

class LegacyRenderTplsourceHandler extends XoopsObjectGenericHandler
{
	var $mTable = "tplsource";
	var $mPrimary = "tpl_id";
	var $mClass = "LegacyRenderTplsourceObject";
}

?>
