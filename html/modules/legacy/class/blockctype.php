<?php
/**
 *
 * @package Legacy
 * @version $Id: blockctype.php,v 1.3 2008/09/25 15:11:21 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyBlockctypeObject extends XoopsSimpleObject
{
	function LegacyBlockctypeObject()
	{
		$this->initVar('type', XOBJ_DTYPE_STRING, '', true);
		$this->initVar('label', XOBJ_DTYPE_STRING, '', true, 255);
	}
}

class LegacyBlockctypeHandler extends XoopsObjectHandler
{
	var $_mResults = array();
	
	function LegacyBlockctypeHandler(&$db)
	{
		$t_arr = array (
				'H' => _AD_LEGACY_LANG_CTYPE_HTML,
				'P' => _AD_LEGACY_LANG_CTYPE_PHP,
				'S' => _AD_LEGACY_LANG_CTYPE_WITH_SMILIES,
				'T' => _AD_LEGACY_LANG_CTYPE_WITHOUT_SMILIES
			);
			
		foreach ($t_arr as $id => $name) {
			$this->_mResults[$id] =& $this->create();
			$this->_mResults[$id]->setVar('type', $id);
			$this->_mResults[$id]->setVar('label', $name);
		}
	}
	
	function &create()
	{
		$ret =new LegacyBlockctypeObject();
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
