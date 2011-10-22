<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class XoopsSubjecticon extends XoopsObject
{
	function XoopsSubjecticon()
	{
        $this->initVar('filename', XOBJ_DTYPE_TXTBOX, null, true, 255);
	}
}

class XoopsSubjecticonHandler extends XoopsObjectHandler
{
	var $_mResult;
	
	function XoopsSubjecticonHandler(&$db)
	{
		require_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
		$this->_mResult =& XoopsLists::getSubjectsList();
	}
	
	function &create()
	{
		$ret =new XoopsSubjecticon();
		return $ret;
	}
	
	function &get($filename)
	{
		if (isset($this->_mResult[$filename])) {
			$obj =new XoopsSubjecticon();
			$obj->setVar('filename', $this->_mResult[$filename]);

			return $obj;
		}
		
		$ret = null;
		return $ret;
	}

	function &getObjects($criteria = null, $key_as_id = false)
	{
		$ret = array();
		
		foreach ($this->_mResult as $filename => $value) {
			$obj =new XoopsSubjecticon();
			$obj->setVar('filename', $filename);
			if ($key_as_id) {
				$ret[$filename] =& $obj;
			}
			else {
				$ret[] =& $obj;
			}
			unset($obj);
		}
		
		return $ret;
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
