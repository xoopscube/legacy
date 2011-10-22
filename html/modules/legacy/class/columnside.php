<?php
/**
 *
 * @package Legacy
 * @version $Id: columnside.php,v 1.3 2008/09/25 15:11:23 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';

class LegacyColumnsideObject extends XoopsSimpleObject
{
	function LegacyColumnsideObject()
	{
		$this->initVar('id', XOBJ_DTYPE_INT, '', true);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', true, 255);
	}
}

class LegacyColumnsideHandler extends XoopsObjectHandler
{
	var $_mResults = array();
	
	function LegacyColumnsideHandler(&$db)
	{
		$t_arr = array (
				0 => _AD_LEGACY_LANG_SIDE_BLOCK_LEFT,
				1 => _AD_LEGACY_LANG_SIDE_BLOCK_RIGHT,
				3 => _AD_LEGACY_LANG_CENTER_BLOCK_LEFT,
				4 => _AD_LEGACY_LANG_CENTER_BLOCK_RIGHT,
				5 => _AD_LEGACY_LANG_CENTER_BLOCK_CENTER
			);
			
		foreach ($t_arr as $id => $name) {
			$this->_mResults[$id] =& $this->create();
			$this->_mResults[$id]->setVar('id', $id);
			$this->_mResults[$id]->setVar('name', $name);
		}
	}
	
	function &create()
	{
		$ret =new LegacyColumnsideObject();
		return $ret;
	}
	
	function &get($id)
	{
		if (isset($this->_mResults[$id])) {
			return $this->_mResults[$id];
		}
		
		$ret = null;
		return $ret;
	}
	
	function &getObjects($criteria = null, $id_as_key = false)
	{
		if ($id_as_key) {
			return $this->_mResults;
		}
		else {
			$ret = array();
		
			foreach (array_keys($this->_mResults) as $key) {
				$ret[] =& $this->_mResults[$key];
			}
			
			return $ret;
		}
	}
	
	function insert(&$obj)
	{
		return false;
	}

	function delete(&$obj)
	{
		return false;
	}
}

?>
