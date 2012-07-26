<?php
/**
 *
 * @package Legacy
 * @version $Id: imagebody.php,v 1.3 2008/09/25 15:11:24 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyImagebodyObject extends XoopsSimpleObject
{
	function LegacyImagebodyObject()
	{
		static $initVars;
		if (isset($initVars)) {
			$this->mVars = $initVars;
			return;
		}
		$this->initVar('image_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('image_body', XOBJ_DTYPE_TEXT, '', true);
		$initVars=$this->mVars;
	}
}

class LegacyImagebodyHandler extends XoopsObjectGenericHandler
{
	var $mTable = "imagebody";
	var $mPrimary = "image_id";
	var $mClass = "LegacyImagebodyObject";
}

?>
