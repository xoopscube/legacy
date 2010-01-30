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

require_once LECAT_TRUST_PATH . '/class/ObjectHandler.class.php';

/**
 * Lecat_CatObject
**/
class Lecat_CatObject extends XoopsSimpleObject
{
	protected $_mGrLoadedFlag = false;
	protected $_mPermitLoadedFlag = false;
	protected $_mPcatLoadedFlag = false;
	protected $_mChildrenLoadedFlag = false;
	protected $_mCatPathLoadedFlag = false;
	public $mTargetFlag = false;
	public $mDirname = null;

    /**
     * __construct
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function __construct()
    {
        $this->initVar('cat_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('title', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('gr_id', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('p_id', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('modules', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('description', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('depth', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('weight', XOBJ_DTYPE_INT, '10', false);
        $this->initVar('options', XOBJ_DTYPE_TEXT, '', false);
    }


	/**
	 * @public
	 * load Gr Object of this category.
	 */
	public function loadGr()
	{
		if ($this->_mGrLoadedFlag == false) {
			$handler = Lecat_Utils::getLecatHandler('gr', $this->getDirname());
			$this->mGr =& $handler->get($this->get('gr_id'));
			$this->_mGrLoadedFlag = true;
		}
	}

	/**
	 * @public
	 * load Permit Objects of this category.
	 */
	public function loadPermit()
	{
		if ($this->_mPermitLoadedFlag == false) {
			$handler =Lecat_Utils::getLecatHandler('permit', $this->getDirname());
			$this->mPermit =& $handler->getObjects(new Criteria('cat_id', $this->get('cat_id')));
			$this->_mPermitLoadedFlag = true;
		}
	}

	/**
	 * @public
	 * load parent category Object of this category.
	 */
	public function loadPcat()
	{
		if ($this->_mPcatLoadedFlag == false) {
			$handler =Lecat_Utils::getLecatHandler('cat', $this->getDirname());
			$this->mPcat =& $handler->get($this->get('p_id'));
			$this->_mPcatLoadedFlag = true;
		}
	}

	/**
	 * @public
	 * 
	 */
	public function getDepth()
	{
		$this->loadCatPath($this->getDirname());
		return count($this->mCatPath['cat_id']) + 1;
	}

	/**
	 * @public
	 * load child categories Objects of this category.
	 */
	public function loadChildren($module)
	{
		if ($this->_mChildrenLoadedFlag == false) {
			$handler = $this->_getHandler('cat');
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('p_id', $this->get('cat_id')));
			$criteria->setSort('weight', 'ASC');
			$children =& $handler->getObjects(new Criteria('p_id', $this->get('cat_id')));
			//check module confinement
			foreach(array_keys($children) as $key){
				if($children[$key]->checkModule($module)){
					$this->mChildren[] = $children[$key];
				}
			}
			$this->_mChildrenLoadedFlag = true;
		}
	}

	/**
	 * @public
	 * get child categories' id and title array.
	 */
	public function getChildList($module)
	{
		$this->loadChildren($this->getDirname(), $module);
		foreach(array_keys($this->mChildren) as $key){
			$children['cat_id'][$key] = $this->mChildren[$key]->getShow('cat_id');
			$children['cat_title'][$key] = $this->mChildren[$key]->getShow('title');
		}
		return $children;
	}

	/**
	 * @public
	 * call load category function if not loaded yet.
	 */
	public function loadCatPath()
	{
		//set this category's parent cat_id
		if($this->_mCatPathLoadedFlag==false){
			$handler = $this->_getHandler('cat');
			$this->mCatPath['cat_id'] = array();
			$this->mCatPath['title'] = array();
			$this->mCatPath['modules'] = array();
			$p_id = $this->get('p_id');
			$this->_loadCatPath($p_id, $handler);
			$this->_mCatPathLoadedFlag=true;
		}
	}

	/**
	 * @protected
	 * load category path array retroactively.
	 */
	protected function _loadCatPath($p_id, $handler)
	{
		$cat =& $handler->get($p_id);
		if(is_object($cat)){
			$this->mCatPath['cat_id'][] = $cat->getShow('cat_id');
			$this->mCatPath['title'][] = $cat->getShow('title');
			$this->mCatPath['modules'][] = $cat->getShow('modules');
			$this->_loadCatPath($cat->get('p_id'), $handler);
		}
	}

	protected function _getPermit($groupid=0)
	{
		$handler = $this->_getHandler('permit');
		$criteria=new CriteriaCompo();
		$criteria->add(new Criteria('cat_id', $this->get('cat_id')));
		if(intval($groupid)>0){
			$criteria->add(new Criteria('groupid', $groupid));
		}
		return $handler->getObjects($criteria);
	}

	/**
	 * @public
	 * get the permissions for this category.
	 * At first, check the permission of this category.
	 * If the permission is not set, check the upper category's permission retroactively.
	*/
	public function getThisPermit($groupId=0)
	{
		//if this category don't have permissions, check the upper category's permissions retroactively
		if($permitArr = $this->_getPermit($groupId)){
			$this->mTargetFlag = 'cur';	//current cat has permission
			return $permitArr;
		}
		else{
			//get the category path from the top category in descendant order
			$this->loadCatPath();
			//set default permissions from Gr, if any permission is set in this Category Tree
			if(! $permitArr=Lecat_Utils::getInheritPermission($this->getDirname(), $this->mCatPath['cat_id'], $groupId)){
				$this->loadGr();
				$permissions = $this->mGr->getDefaultPermissionForCheck();
				if(intval($groupId)>0){
					$permitArr[0] = $this->_getHandler('permit')->create();
					$permitArr[0]->set('cat_id', $this->get('cat_id'));
					$permitArr[0]->set('permissions', serialize($permissions));
					$permitArr[0]->set('groupid', $groupId);
				}
				else{
					$groupHandler =& xoops_gethandler('member');
					$group =& $groupHandler->getGroups();
					foreach(array_keys($group) as $keyM){
						$permitArr[$keyM] = $this->_getHandler('permit')->create();
						$permitArr[$keyM]->set('cat_id', $this->get('cat_id'));
						$permitArr[$keyM]->set('permissions', serialize($permissions));
						$permitArr[$keyM]->set('groupid', $group[$keyM]->get('groupid'));
					}
				}
			}
		
			$this->mTargetFlag = 'anc';	//ancestoral cat has permission
			return $permitArr;
		}
	}

	/**
	 * @public
	 * check permission about the given uid and action.
	 * check about all groups the user is belong to.
	*/
	public function checkPermitByUid($action, $uid=0, $module="")
	{
		//check this category is for specific dirname ?
		if(! $this->checkModule($module)) return false;
	
		$groupHandler = Lecat_Utils::getXoopsHandler('group');
		//check group permission
		if(intval($uid)>0){
			$handler =Lecat_Utils::getXoopsHandler('user');
			$groupIds = $handler->get($uid)->getGroups();
			foreach($groupIds as $gid){
				$groups[] = $groupHandler->get($gid);
			}
		}
		else{	//case:guest
			$groups = $groupHandler->getObjects(new Criteria('group_type', 'Anonymous'));
		}
		foreach(array_keys($groups) as $keyG){
			if($this->checkPermitByGroupid($action, $groups[$keyG]->get('groupid'), $module)){
				return true;
			}
		}
		return false;
	}

	/**
	 * @public
	 * check permission about the given groupid and action.
	*/
	public function checkPermitByGroupid($action, $groupid, $module="")
	{
		//check this category is for specific dirname ?
		if(! $this->checkModule($module)) return false;
	
		$permitArr = $this->getThisPermit($groupid);
		//check illegal permission settings
		if(count($permitArr)>1){
			echo "duplicated permission settings about the combination of groupid and cat_id";
			die();
		}
		elseif(count($permitArr)==0){
			return false;
		}
		$permissions =$permitArr[0]->getPermissionArr();
		//var_dump($permissions);die();
		return (@$permissions[$action]==1) ? true : false;
	}

	/**
	 *	@public
	 *  module confinement function
	 *  check specified dirname is set in the categories' modules field ?
	 */
	public function checkModule(/*** string ***/ $module="")
	{
		if(! $module){
			return true;
		}
	
		$moduleArr = $this->getModuleArr();
		if(count($moduleArr)>0) return true;
	
		return (in_array($module, $moduleArr)) ? true : false;
	}

	/**
	 *	@public
	 *  module confinement function
	 *  get modules field of the nearest category from this one.
	 */
	public function getModuleArr()
	{
		if($this->get('modules')){
			return explode(',', $this->get('modules'));
		}
	
		$this->loadCatPath();
		//search parent categories' modules confinement
		foreach(array_keys($this->mCatPath['modules']) as $key){
			if($this->mCatPath['modules'][$key]){
				return explode(',', $this->mCatPath['modules'][$key]);
			}
		}
	
		return array();
	}

    /**
     * _getHandler
     * 
     * @param   string  $tablename
     * 
     * @return  XoopsObjectHandleer
    **/
	protected function _getHandler($tablename)
	{
		return Lecat_Utils::getLecatHandler($tablename, $this->getDirname());
	}

    /**
     * getDirname
     * 
     * @param   void
     * 
     * @return  string
    **/
	public function getDirname()
	{
		return $this->mDirname;
	}
}

/**
 * Lecat_CatHandler
**/
class Lecat_CatHandler extends LecatObjectHandler
{
    /**
     * @brief   string
    **/
    public $mTable = '{dirname}_cat';

    /**
     * @brief   string
    **/
    public $mPrimary = 'cat_id';

    /**
     * @brief   string
    **/
    public $mClass = 'Lecat_CatObject';

    /**
     * delete
     * 
     * @param   XoopsSimpleObject  &$obj
     * 
     * @return  
    **/
	public function delete(&$obj)
	{
		$handler = $this->_getHandler('permit');
		$handler->deleteAll(new Criteria('cat_id', $obj->get('cat_id')));
		unset($handler);
	
		return parent::delete($obj);
	}
}

?>
