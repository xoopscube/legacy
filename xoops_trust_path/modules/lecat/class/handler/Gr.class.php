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
 * Lecat_GrObject
**/
class Lecat_GrObject extends XoopsSimpleObject
{
    public $mDirname = null;
    public $mCat = array();
    public $mTree = array();
    public $mCatCount = 0;
    protected $_mCatLoadedFlag = false;
    protected $_mTreeLoadedFlag = false;

    /**
     * __construct
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function __construct()
    {
        $this->initVar('gr_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('title', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('level', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('actions', XOBJ_DTYPE_TEXT, '', false);
    }

    /**
     * loadCat
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function loadCat()
    {
        if ($this->_mCatLoadedFlag == false) {
            $handler = $this->_getHandler('cat');
            $this->mCat = $handler->getObjects(new Criteria('gr_id', $this->get('gr_id')));
            $this->_mCatLoadedFlag = true;
        }
    }

    /**
     * getShowLevel
     * 
     * @param   void
     * 
     * @return  string
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
     * @param   void
     * 
     * @return  string[]
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
     * @param   void
     * 
     * @return  string[]
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
     * @param   void
     * 
     * @return  string[]
    **/
    public function getActions()
    {
        return ($this->get('actions')) ? unserialize($this->get('actions')) : array('key'=>array(),'title'=>array(),'default'=>array());
    }

    /**
     * setActions
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function setActions(/*** array ***/ $actionArr)
    {
        $this->set('actions', serialize($actionArr));
    }

    /**
     * loadTree
     * 
     * @param   int     $pid
     * @param   string  $module
     * 
     * @return  string[]
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
     * @param   int     $pid
     * @param   string  $module
     * 
     * @return  string[]
    **/
    protected function _loadTree(/*** int ***/ $p_id=0, /*** string ***/ $module="")
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gr_id', $this->get('gr_id')));
        $criteria->add(new Criteria('p_id', $p_id));
        $criteria->setSort('weight');
        $catArr =Lecat_Utils::getLecatHandler('cat', $this->getDirname())->getObjects($criteria);
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
     * @param   string  $action
     * @param   int     $uid
     * @param   bool    $deleteFlag
     * 
     * @return  void
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
     * @param   string  $tablename
     * 
     * @return  XoopsObjectHandler
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
 * Lecat_GrHandler
**/
class Lecat_GrHandler extends Lecat_ObjectGenericHandler
{
    /**
     * @brief   string
    **/
    public $mTable = '{dirname}_gr';

    /**
     * @brief   string
    **/
    public $mPrimary = 'gr_id';

    /**
     * @brief   string
    **/
    public $mClass = 'Lecat_GrObject';

    /**
     * delete
     * 
     * @param   XoopsSimpleObject  &$obj
     * 
     * @return  
    **/
    public function delete(&$obj)
    {
        $handler = $this->_getHandler('cat');
        $handler->deleteAll(new Criteria('gr_id', $obj->get('gr_id')));
        unset($handler);
    
        return parent::delete($obj);
    }
}

?>
