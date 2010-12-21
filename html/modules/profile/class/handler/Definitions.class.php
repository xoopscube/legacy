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
	public function Profile_DefinitionsObject()
	{
		$this->initVar('field_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('field_name', XOBJ_DTYPE_STRING, '', false, 32);
		$this->initVar('label', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('type', XOBJ_DTYPE_STRING, '', false, 32);
		$this->initVar('validation', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('required', XOBJ_DTYPE_BOOL, 0, false);
		$this->initVar('show_form', XOBJ_DTYPE_BOOL, 1, false);
		$this->initVar('weight', XOBJ_DTYPE_INT, 10, false);
		$this->initVar('description', XOBJ_DTYPE_TEXT, '', false);
		$this->initVar('access', XOBJ_DTYPE_TEXT, '', false);
		$this->initVar('options', XOBJ_DTYPE_TEXT, '', false);
	}

	/**
	 * @public
	 */
	public function getFields()
	{
		$cri = new Criteria('1', '1');
		$cri->setSort('weight');
	
		return $this->getObjects($cri);
	}

	/**
	 * @public
	 */
	public function getQuery4AlterTable()
	{
		switch($this->get('type')){
			case Profile_FormType::STRING:
				return 'VARCHAR(255) NOT NULL';
				break;
			case Profile_FormType::TEXT:
				return 'TEXT NOT NULL';
				break;
			case Profile_FormType::INT:
				return 'INT(11) UNSIGNED NOT NULL';
				break;
			case Profile_FormType::DATE:
				return 'INT(11) UNSIGNED NOT NULL';
				break;
			case Profile_FormType::CHECKBOX:
				return 'TINYINT(1) UNSIGNED NOT NULL';
				break;
			case Profile_FormType::SELECTBOX:
				return 'VARCHAR(64) NOT NULL';
				break;
		}
	}

	/**
	 * @public
	 */
	public function getOptions()
	{
		if($this->get('options')){
			return explode('|', $this->get('options'));
		}
		else{
			return array();
		}
	}

	public function getFormPropertyClass()
	{
		$class = null;
	
		switch($this->get('type')){
		case Profile_FormType::STRING:
			$class = 'XCube_StringProperty';
			break;
		case Profile_FormType::TEXT:
			$class = 'XCube_TextProperty';
			break;
		case Profile_FormType::INT:
			$class = 'XCube_IntProperty';
			break;
		case Profile_FormType::DATE:
			$class = 'XCube_StringProperty';
			break;
		case Profile_FormType::CHECKBOX:
			$class = 'XCube_BoolProperty';
			break;
		case Profile_FormType::SELECTBOX:
			$class = 'XCube_StringProperty';
			break;
		}
		return $class;
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
	public function getFields4DataEdit()
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('show_form', '1'));
		$criteria->setSort('weight');
	
		return $this->getObjects($criteria);
	}

	/**
	 * @public
	 */
	public function getFields4DataShow($uid)
	{
		$lHandler = xoops_getmodulehandler('groups_users_link', 'user');
	
		$criteria = new CriteriaCompo();
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
	public function insert(&$obj, $force = false)
	{
		global $xoopsDB;
		if ($obj->isNew()) {
			$sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' ADD `'. $obj->get('field_name') .'` '. $obj->getQuery4AlterTable();
			$xoopsDB->query($sql);
		}
		else {
			$oldObj = $this->get($obj->get('field_id'));
			if($oldObj->get('field_name')!=$obj->get('field_name')){
				$sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' CHANGE `'. $oldObj->get('field_name') .'` `'. $obj->get('field_name') .'` '. $oldObj->getQuery4AlterTable();
				$xoopsDB->query($sql);
			}
		}
	
		return parent::insert($obj, $force);
	}

	/**
	 * @public
	 */
	public function delete(&$obj, $force = false)
	{
		global $xoopsDB;
		$sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' DROP `'. $obj->get('field_name') .'`';
		$xoopsDB->query($sql);
		
		return parent::delete($obj, $force);
	}

	public function getDefinitionsArr($show_form=true)
	{
		$criteria = new CriteriaCompo();
		$criteria->setSort('weight', 'ASC');
		if($show_form==true){
			$criteria->add(new Criteria('show_form', 1));
		}
		$definitions = $this->getObjects($criteria);
		$defArr = array();
		foreach($definitions as $def){
			$defArr[$def->get('field_name')] = $def->gets();
		}
		return $defArr;
	}

	/**
	 * @public
	 */
	public function getTypeList()
	{
		return array(
			Profile_FormType::STRING,
			Profile_FormType::TEXT,
			Profile_FormType::INT,
			Profile_FormType::FLOAT,
			Profile_FormType::DATE,
			Profile_FormType::CHECKBOX,
			Profile_FormType::SELECTBOX
		);
	}

	public static function getReservedNameList()
	{
		return array('uid','name','uname','email','url','user_avatar','user_regdate','user_icq','user_from','user_sig','user_viewemail','actkey','user_aim','user_yim','user_msnm','pass','posts','attachsig','rank','level','theme','timezone_offset','last_login','umode','uorder','notify_method','notify_mode','user_occ','bio','user_intrest','user_mailok', 'user_name');
	}

	/**
	 * @public
	 */
	public function getValidationList()
	{
		return array("email");
	}
}

class Profile_FormType
{
	const STRING = 'string';
	const TEXT = 'text';
	const INT = 'int';
	const FLOAT = 'float';
	const DATE = 'date';
	const CHECKBOX = 'checkbox';
	const SELECTBOX = 'selectbox';

	/**
	 * @public
	 */
	public static function getXObjType($type)
	{
		switch($type){
			case self::STRING:
				return XOBJ_DTYPE_STRING;
				break;
			case self::TEXT:
				return XOBJ_DTYPE_TEXT;
				break;
			case self::INT:
				return XOBJ_DTYPE_INT;
				break;
			case self::FLOAT:
				return XOBJ_DTYPE_FLOAT;
				break;
			case self::DATE:
				return XOBJ_DTYPE_INT;
				break;
			case self::CHECKBOX:
				return XOBJ_DTYPE_BOOL;
				break;
			case self::SELECTBOX:
				return XOBJ_DTYPE_STRING;
				break;
		}
	}
}

?>
