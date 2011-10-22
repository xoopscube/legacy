<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}
/**
 * Lecat_PermitObject
**/
class Lecat_PermitObject extends XoopsSimpleObject
{
	public $mCat = null;
	protected $_mCatLoadedFlag = false;

	/**
	 * __construct
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function __construct()
	{
		$this->initVar('permit_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('cat_id', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('groupid', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('permissions', XOBJ_DTYPE_TEXT, '', false);
	}

	/**
	 * loadCat
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function loadCat()
	{
		if ($this->_mCatLoadedFlag == false) {
			$handler = $this->_getHandler('cat');
			$this->mCat = $handler->get($this->get('cat_id'));
			$this->_mCatLoadedFlag = true;
		}
	}

	public function getPermissionArr()
	{
		return unserialize($this->get('permissions'));
	}

	/**
	 * @public
	 * check permission about given action
	 */
	public function checkPermit($action)
	{
		$permissions = $this->getPermissionArr();
		return ($permissions[$action]==1) ? true : false;
	}
}

/**
 * Lecat_PermitHandler
**/
class Lecat_PermitHandler extends XoopsObjectGenericHandler
{
	/**
	 * @brief	string
	**/
	public $mTable = '{dirname}_permit';

	/**
	 * @brief	string
	**/
	public $mPrimary = 'permit_id';

	/**
	 * @brief	string
	**/
	public $mClass = 'Lecat_PermitObject';

	/**
	 * __construct
	 * 
	 * @param	XoopsDatabase  &$db
	 * @param	string	$dirname
	 * 
	 * @return	void
	**/
	public function __construct(/*** XoopsDatabase ***/ &$db,/*** string ***/ $dirname)
	{
		$this->mTable = strtr($this->mTable,array('{dirname}' => $dirname));
		parent::XoopsObjectGenericHandler($db);
	}

	public function updatePermission($catId, $groupId, $permission)
	{
		$permissionArr = array();
		foreach(array_keys($permission[$groupId]) as $key){	//$key:action
			$permitArr[$key] = $permission[$groupId][$key];
		}
	
		$cri = new CriteriaCompo();
		$cri->add(new Criteria('cat_id', intval($catId)));
		$cri->add(new Criteria('groupid', intval($groupId)));
		$objs = $this->getObjects($cri);
		if($objs){
			$obj = array_shift($objs);
			/*
			if(! in_array(1, $permitArr)){
				$this->delete($obj);
				return true;
			}
			*/
		}
		else{
			/*
			if(! in_array(1, $permitArr)){
				return true;
			}
			*/
			$obj = $this->create();
			$obj->set('cat_id', intval($catId));
			$obj->set('groupid', intval($groupId));
		}
		$obj->set('permissions', serialize($permitArr));
		return ($this->insert($obj, true)) ? true : false;
	}
}

?>
