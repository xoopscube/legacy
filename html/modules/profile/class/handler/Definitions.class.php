<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_DefinitionsObject extends XoopsSimpleObject
{
	/**
	 * @public
	 */
	function Profile_DefinitionsObject()
	{
		$this->initVar('field_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('field_name', XOBJ_DTYPE_STRING, '', false, 32);
		$this->initVar('label', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('type', XOBJ_DTYPE_STRING, '', false, 32);
		$this->initVar('validation', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('required', XOBJ_DTYPE_BOOL, 0, false);
		$this->initVar('show_form', XOBJ_DTYPE_BOOL, 0, false);
		$this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('description', XOBJ_DTYPE_TEXT, '', false);
		$this->initVar('access', XOBJ_DTYPE_TEXT, '', false);
		$this->initVar('options', XOBJ_DTYPE_TEXT, '', false);
	}

	/**
	 * @public
	 */
	function getQuery4AlterTable()
	{
		switch($this->get('type')){
			case 'string':
				return 'VARCHAR(255) NOT NULL';
				break;
			case 'text':
				return 'TEXT NOT NULL';
				break;
			case 'int':
				return 'INT(11) UNSIGNED NOT NULL';
				break;
			case 'date':
				return 'INT(11) UNSIGNED NOT NULL';
				break;
			case 'checkbox':
				return 'TINYINT(1) UNSIGNED NOT NULL';
				break;
			case 'selectbox':
				return 'VARCHAR(64) NOT NULL';
				break;
		}
	}

	/**
	 * @public
	 */
	function getXObjType()
	{
		switch($this->get('type')){
			case 'string':
				return XOBJ_DTYPE_STRING;
				break;
			case 'text':
				return XOBJ_DTYPE_TEXT;
				break;
			case 'int':
				return XOBJ_DTYPE_INT;
				break;
			case 'date':
				return XOBJ_DTYPE_INT;
				break;
			case 'checkbox':
				return XOBJ_DTYPE_BOOL;
				break;
			case 'selectbox':
				return XOBJ_DTYPE_STRING;
				break;
		}
	}

	/**
	 * @public
	 */
	function getTypeList()
	{
		return array("string", "text", "int", "date", "checkbox", "selectbox");
	}

	/**
	 * @public
	 */
	function getValidationList()
	{
		return array("email", "url");
	}

	/**
	 * @public
	 */
	function getServiceType()
	{
		switch($this->get('type')){
			case 'string':
				return 'string';
				break;
			case 'text':
				return 'text';
				break;
			case 'int':
				return 'int';
				break;
			case 'date':
				return 'int';
				break;
			case 'checkbox':
				return 'bool';
				break;
			case 'selectbox':
				return 'string';
				break;
		}
	}

	/**
	 * @public
	 */
	function getOptions()
	{
		if($this->get('options')){
			return explode('|', $this->get('options'));
		}
		else{
			return array();
		}
	}

	/**
	 * @public
	 */
	function getServiceField()
	{
		return $this->getServiceType() .' '. $this->get('field_name');
	}
}

class Profile_DefinitionsHandler extends XoopsObjectGenericHandler
{
	var $mTable = 'profile_definitions';
	var $mPrimary = 'field_id';
	var $mClass = 'Profile_DefinitionsObject';

	/**
	 * @public
	 */
	function getFields4DataEdit()
	{
		$criteria = new CriteriaCompo('1', '1');
		$criteria->add(new Criteria('show_form', '1'));
		$criteria->setSort('weight');
	
		return $this->getObjects($criteria);
	}

	/**
	 * @public
	 */
	function getFields4DataShow($uid)
	{
		$lHandler =& xoops_getmodulehandler('groups_users_link', 'user');
	
		$criteria = new CriteriaCompo('1', '1');
		$criteria->setSort('weight');
		$fieldArr = $this->getObjects($criteria);
		foreach(array_keys($fieldArr) as $keyF){
			$flag = false;
			$accessArr = explode(',', $fieldArr[$keyF]->get('access'));
			foreach(array_keys($accessArr) as $keyA){
				if($lHandler->isUserOfGroup($uid, $accessArr[$keyA])){
					$flag = true;
				}
			}
			if(! $flag){
				unset($fieldArr[$keyF]);
			}
		}
	
		return $fieldArr;
	}

	/**
	 * @public
	 */
	function insert(&$obj)
	{
		global $xoopsDB;
		if ($obj->isNew()) {
			$sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' ADD `'. $obj->get('field_name') .'` '. $obj->getQUery4AlterTable();
			$xoopsDB->query($sql);
		}
		else {
			$oldObj =& $this->get($obj->get('field_id'));
			if($oldObj->get('field_name')!=$obj->get('field_name')){
				$sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' CHANGE `'. $oldObj->get('field_name') .'` `'. $obj->get('field_name') .'` '. $oldObj->getQuery4AlterTable();
				$xoopsDB->query($sql);
			}
		}
	
		return parent::insert($obj);
	}

	/**
	 * @public
	 */
	function delete(&$obj)
	{
		global $xoopsDB;
		$sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' DROP `'. $obj->get('field_name') .'`';
		$xoopsDB->query($sql);
		
		return parent::delete($obj);
	}

}

?>
