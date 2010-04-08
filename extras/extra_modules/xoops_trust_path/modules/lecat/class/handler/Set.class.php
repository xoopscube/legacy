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
 * Lecat_SetObject
**/
class Lecat_SetObject extends XoopsSimpleObject
{
	public $mCat = array();
	public $mTree = array();
	public $mCatCount = 0;
	protected $_mCatLoadedFlag = false;
	protected $_mTreeLoadedFlag = false;

	/**
	 * __construct
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function __construct()
	{
		$this->initVar('set_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('title', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('level', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('actions', XOBJ_DTYPE_TEXT, '', false);
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
			$this->mCat = $handler->getObjects(new Criteria('set_id', $this->get('set_id')));
			$this->_mCatLoadedFlag = true;
		}
	}

	/**
	 * getShowLevel
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	public function getShowLevel()
	{
		if ($this->get('level')==0) {
			return _MD_LECAT_LANG_LEVEL_UNLIMITED;
		}
		else{
			return $this->getShow('level');
		}
	}

	/**
	 * getDefaultPermissionList
	 * 
	 * @param	void
	 * 
	 * @return	string[]
	**/
	public function getDefaultPermissionList()
	{
		$permissions = array();
		$actions = $this->getActions();
		$i=0;
		foreach(array_keys($actions['title']) as $key){
			$permissions[$i]['key'] = $key;
			$permissions[$i]['title'] = $actions['title'][$key];
			$permissions[$i]['default'] = $actions['default'][$key];
			$i++;
		}
		return $permissions;
	}

	/**
	 * getDefaultPermissionForCheck
	 * 
	 * @param	void
	 * 
	 * @return	string[]
	**/
	public function getDefaultPermissionForCheck()
	{
		$permissions = array();
		$actions = $this->getActions();
		$i=0;
		foreach(array_keys($actions['title']) as $key){
			$permissions[$key] = $actions['default'][$key];
		}
		return $permissions;
	}

	/**
	 * getActions
	 * 
	 * @param	void
	 * 
	 * @return	string[]
	**/
	public function getActions()
	{
		return ($this->get('actions')) ? unserialize($this->get('actions')) : array('key'=>array(),'title'=>array(),'default'=>array());
	}

	/**
	 * setActions
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function setActions(/*** array ***/ $actionArr)
	{
		$this->set('actions', serialize($actionArr));
	}

	/**
	 * loadTree
	 * 
	 * @param	int 	$pid
	 * @param	string	$module
	 * 
	 * @return	string[]
	**/
	public function loadTree(/*** int ***/ $p_id=0, /*** string ***/ $module="")
	{
		if ($this->_mTreeLoadedFlag == false) {
			$this->_loadTree($p_id, $module);
		}
	}

	/**
	 * _loadTree
	 * 
	 * @param	int 	$pid
	 * @param	string	$module
	 * 
	 * @return	string[]
	**/
	protected function _loadTree(/*** int ***/ $p_id=0, /*** string ***/ $module="")
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('set_id', $this->get('set_id')));
		$criteria->add(new Criteria('p_id', $p_id));
		$criteria->setSort('weight');
		$catArr =Legacy_Utils::getModuleHandler('cat', $this->getDirname())->getObjects($criteria);
		foreach(array_keys($catArr) as $key){
			//check module confinement
			if($catArr[$key]->checkModule($module)){
				$this->mTree[$this->mCatCount] = $catArr[$key];
				$this->mCatCount++;
				$this->_loadTree($catArr[$key]->get('cat_id'), $module);
			}
		}
	}

	/**
	 * filterCategory
	 * 
	 * @param	string	$action
	 * @param	int 	$uid
	 * @param	bool	$deleteFlag
	 * 
	 * @return	void
	**/
	public function filterCategory($action, $uid=0, $deleteFlag=false)
	{
		//check permission of each cat in the given tree
		foreach(array_keys($this->mTree) as $key){
			if($this->mTree[$key]->checkPermitByUid($action, $uid)==false && $deleteFlag==true){
				unset($this->mTree[$key]);
			}
		}
	}

	/**
	 * _getHandler
	 * 
	 * @param	string	$tablename
	 * 
	 * @return	XoopsObjectHandler
	**/
	protected function _getHandler($tablename)
	{
		return Legacy_Utils::getModuleHandler($tablename, $this->getDirname());
	}
}

/**
 * Lecat_SetHandler
**/
class Lecat_SetHandler extends XoopsObjectGenericHandler
{
	/**
	 * @brief	string
	**/
	public $mTable = '{dirname}_set';

	/**
	 * @brief	string
	**/
	public $mPrimary = 'set_id';

	/**
	 * @brief	string
	**/
	public $mClass = 'Lecat_SetObject';

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

	/**
	 * delete
	 * 
	 * @param	XoopsSimpleObject  &$obj
	 * 
	 * @return	
	**/
	public function delete(&$obj)
	{
		$handler = $this->_getHandler('cat');
		$handler->deleteAll(new Criteria('set_id', $obj->get('set_id')));
		unset($handler);
	
		return parent::delete($obj);
	}
}

?>
