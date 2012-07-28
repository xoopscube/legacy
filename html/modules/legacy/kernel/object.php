<?php
/**
 *
 * @package Legacy
 * @version $Id: object.php,v 1.3 2008/09/25 15:12:02 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 *	This class implements the interface of XoopsObjectInterface. It gives a developer
 * 'TYPE SAFE' with the limit. The instance can have only five data type that are
 * BOOL, INT, FLOAT, STRING and TEXT.
 *	You can not get the sanitizing values by cleanVars() that is the function of
 * XoopsObject. But, all set functions give you 'TYPE SAFE'. You should use this 
 * class with using your favorite ActionForm.
 *
 * "Check values by actionform, set values to XoopsSimpleObject"
 *
 *	This class was defined for "The prolongation of human life plan". This is not 
 * the rule that you are forced.
 * 
 */
class XoopsSimpleObject extends AbstractXoopsObject
{
	public $mVars = array();
	public $mIsNew = true;
	public $mDirname = null;
	
	function XoopsSimpleObject()
	{
	}
	
	function setNew()
	{
		$this->mIsNew = true;
	}
	
	function unsetNew()
	{
		$this->mIsNew = false;
	}

	function isNew()
	{
		return $this->mIsNew;
	}
	
	function initVar($key, $dataType, $value = null, $required = false, $size = null)
	{
		static $_mAllowType = array(XOBJ_DTYPE_BOOL=>XOBJ_DTYPE_BOOL, XOBJ_DTYPE_INT=>XOBJ_DTYPE_INT, XOBJ_DTYPE_FLOAT=>XOBJ_DTYPE_FLOAT, XOBJ_DTYPE_STRING=>XOBJ_DTYPE_STRING, XOBJ_DTYPE_TEXT=>XOBJ_DTYPE_TEXT);
	
		if (!$_mAllowType[$dataType]) {
			die();	// TODO
		}
		
		$this->mVars[$key] = array(
			'data_type' => $dataType,
			'value' => null,
			'required' => $required ? true : false,
			'maxlength' => $size ? (int)$size : null
		);
		
		$this->assignVar($key, $value);
	}
	
	function assignVar($key, $value)
	{
		$vars = &$this->mVars[$key];
		if (!isset($vars)) return;
		
		switch ($vars['data_type']) {
			case XOBJ_DTYPE_BOOL:
				$vars['value'] = $value ? 1 : 0;
				return;

			case XOBJ_DTYPE_INT:
				$vars['value'] = $value !== null ? (int)$value : null;
				return;

			case XOBJ_DTYPE_FLOAT:
				$vars['value'] = $value !== null ? (float)$value : null;
				return;

			case XOBJ_DTYPE_STRING:
				$len = $vars['maxlength'];
				$vars['value'] = ($len !== null && strlen($value) > $len) ? xoops_substr($value, 0, $len, null) : $value;
				return;

			case XOBJ_DTYPE_TEXT:
				$vars['value'] = $value;
				return;
		}
	}
	
	function assignVars($values)
	{
		foreach($values as $key => $value) $this->assignVar($key, $value);
	}
	
	function set($key, $value)
	{
		$this->assignVar($key, $value);
	}
	
	function get($key)
	{
		return $this->mVars[$key]['value'];
	}
	
	function gets()
	{
		$ret = array();
		
		foreach ($this->mVars as $key => $value) {
			$ret[$key] = $value['value'];
		}
		
		return $ret;
	}
	
	function setVar($key, $value)
	{
		$this->assignVar($key, $value);
	}

	function setVars($values)
	{
		$this->assignVars($values);
	}

	/**
	 * @deprecated
	 */	
	function getVar($key)
	{
		return $this->getShow($key);
	}
	
	/**
	 * Return HTML string for displaying only by HTML.
	 * The second parametor doesn't exist.
	 */
	function getShow($key)
	{
		$value = null;
		$vars = $this->mVars[$key];
		
		switch ($vars['data_type']) {
			case XOBJ_DTYPE_BOOL:
			case XOBJ_DTYPE_INT:
			case XOBJ_DTYPE_FLOAT:
				return $vars['value'];

			case XOBJ_DTYPE_STRING:
				$root =& XCube_Root::getSingleton();
				$textFilter =& $root->getTextFilter();
				return $textFilter->toShow($vars['value']);

			case XOBJ_DTYPE_TEXT:
				$root =& XCube_Root::getSingleton();
				$textFilter =& $root->getTextFilter();
				return $textFilter->toShowTarea($vars['value'], 0, 1, 1, 1, 1);
		}
		
		return $value;
	}

	function getTypeInformations()
	{
		$ret = array();
		foreach (array_keys($this->mVars) as $key) {
			$ret[$key] = $this->mVars[$key]['data_type'];
		}
		
		return $ret;
	}

	/**
	 * getPurifiedHtml
	 * 
	 * @param	string	$key
	 * @param	string	$encoding
	 * @param	string	$doctype
	 * 
	 * @return	string
	**/
	public function getPurifiedHtml(/*** string ***/ $key, /*** string ***/ $encoding=null, /*** string ***/ $doctype=null)
	{
		$root = XCube_Root::getSingleton();
		$textFilter = $root->getTextFilter();
		return $textFilter->purifyHtml($this->get($key), $encoding, $doctype);
	}

	/**
	 * getDirname
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	public function getDirname()
	{
		return $this->mDirname;
	}
}

?>
