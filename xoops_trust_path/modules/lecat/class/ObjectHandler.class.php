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
 * LecatObjectHandler
**/
class LecatObjectHandler extends XoopsObjectGenericHandler
{
    /**
     * @brief   string
    **/
    public $mTable = null;

    /**
     * @brief   string
    **/
    public $mDirname = null;

    /**
     * @brief   string
    **/
    public $mPrimary = null;

    /**
     * @brief   string
    **/
    public $mClass = null;

    /**
     * __construct
     * 
     * @param   XoopsDatabase  &$db
     * @param   string  $dirname
     * 
     * @return  void
    **/
    public function __construct(/*** XoopsDatabase ***/ &$db,/*** string ***/ $dirname)
    {
    	$this->mDirname = $dirname;
        $this->mTable = strtr($this->mTable,array('{dirname}' => $this->getDirname()));
        parent::XoopsObjectGenericHandler($db);
    }

    /**
     * create
     * 
     * @param   bool $isNew
     * 
     * @return  XoopsSimpleObject  $obj
    **/
	public function create($isNew = true)
	{
		$obj = parent::create($isNew);
		$obj->mDirname = $this->getDirname();
		return $obj;
	}

    /**
     * create
     * 
	 * @param CriteriaElement $criteria
	 * @param int  $limit
	 * @param int  $start
	 * @param bool $id_as_key
     * 
     * @return  XoopsSimpleObject[]  $ret
    **/
	public function getObjects($criteria = null, $limit = null, $start = null, $id_as_key = false)
	{
		$ret = parent::getObjects($criteria, $limit, $start, $id_as_key);
		foreach(array_keys($ret) as $key){
			$ret[$key]->mDirname = $this->mDirname;
		}
		return $ret;
	}

    /**
     * _getHandler
     * 
     * @param   string  $dirname
     * @param   string  $tablename
     * 
     * @return  XoopsObjectHandler
    **/
	protected function _getHandler($tablename)
	{
		return Lecat_Utils::getLecatHandler($tablename, $this->getDirname());
	}

    /**
     * _getDirname
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

?>
