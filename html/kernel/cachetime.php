<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class XoopsCachetime extends XoopsObject
{
	function XoopsCachetime()
	{
		static $initVars;
		if (isset($initVars)) {
			$this->vars = $initVars;
			return;
		}
		$this->initVar('cachetime', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('label', XOBJ_DTYPE_TXTBOX, null, true, 255);
		$initVars = $this->vars;
	}
}

class XoopsCachetimeHandler extends XoopsObjectHandler
{
	var $_mResult;
	
	function XoopsCachetimeHandler(&$db)
	{
		parent::XoopsObjectHandler($db);

		//
		// This handler not connects to database.
		//
		$this->_mResult = array(
			"0"       => _NOCACHE,
			"30"      => sprintf(_SECONDS, 30),
			"60"      => _MINUTE,
			"300"     => sprintf(_MINUTES, 5),
			"1800"    => sprintf(_MINUTES, 30),
			"3600"    => _HOUR,
			"18000"   => sprintf(_HOURS, 5),
			"86400"   => _DAY,
			"259200"  => sprintf(_DAYS, 3),
			"604800"  => _WEEK,
			"2592000" => _MONTH
		);
	}
	
	function &create()
	{
		$ret =new XoopsCachetime();
		return $ret;
	}
	
	function &get($cachetime)
	{
		if (isset($this->_mResult[$cachetime])) {
			$obj =new XoopsCachetime();
			$obj->setVar('cachetime', $cachetime);
			$obj->setVar('label', $this->_mResult[$cachetime]);

			return $obj;
		}
		
		$ret = null;
		return $ret;
	}

	function &getObjects($criteria = null, $key_as_id = false)
	{
		$ret = array();
		
		foreach ($this->_mResult as $cachetime => $label) {
			$obj =new XoopsCachetime();
			$obj->setVar('cachetime', $cachetime);
			$obj->setVar('label', $label);
			if ($key_as_id) {
				$ret[$cachetime] =& $obj;
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
