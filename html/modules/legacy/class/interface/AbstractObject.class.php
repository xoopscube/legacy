<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

/**
 * Legacy_AbstractObject
**/
abstract class Legacy_AbstractObject extends XoopsSimpleObject
{
    //const PRIMARY = '';
    //const DATANAME = '';

    protected $_mMainTable = null;  //module's main table name

    public /*** string[] ***/ $mChildList = array();    //Child table's name array
    public /*** string[] ***/ $mParentList = array();   //Parent table's name array
    public /*** XoopsSimpleObject[] ***/ $mTable = array();
    protected /*** bool[] ***/ $_mIsLoaded = array();

    protected /*** bool ***/ $_mIsTagLoaded = false;
    public /*** string[] ***/ $mTag = array();
    public /*** string ***/ $mTempImage = null;

    /**
     * __construct
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function __construct()
    {
        $this->_setupChildTables();
    }

    /**
     * _setupChildTables()
     * 
     * @param   void
     * 
     * @return  void
    **/
    protected function _setupChildTables()
    {
        foreach($this->mChildList as $table){
            $this->_mIsLoaded[$table] = false;
            $this->mTable[$table] = array();
        }
        foreach($this->mParentList as $table){
            $this->_mIsLoaded[$table] = false;
            $this->mTable[$table] = null;
        }
    }

    /**
     * load
     * 
     * @param   string  $table
     * @param   string  $dirname
     * 
     * @return  void
     */
    public function loadTable(/*** string ***/ $table, /*** string ***/ $dirname=null)
    {
        if ($this->_mIsLoaded[$table] === true) {
            return;
        }
    
        $dirname = isset($dirname) ? $dirname : $this->getDirname();
        $handler = Legacy_Utils::getModuleHandler($table, $dirname);
    
        if(in_array($table, $this->mChildList)){
            $this->mTable[$table] = $handler->getObjects(new Criteria($this->getPrimary(), $this->get($this->getPrimary())));
        }
        elseif(in_array($table, $this->mParentList)){
            $this->mTable[$table] = $handler->get($this->get($handler->mPrimary));
        }
        else{
            die('invalid load table');
        }
    
        $this->_mIsLoaded[$table] = true;
    }

    /**
     * getPrimary
     * 
     * @param   void
     * 
     * @return  string
    **/
    public function getPrimary()
    {
        return constant(get_class($this).'::PRIMARY');
    }

    /**
     * getDataname
     * 
     * @param   void
     * 
     * @return  string
    **/
    public function getDataname()
    {
        return constant(get_class($this).'::DATANAME');
    }

    /**
     * renderUri
     * 
     * @param   string  $action ex) 'edit', 'delete', 'view'
     * 
     * @return  string
     */
    public function renderUri(/*** string **/ $action=null)
    {
        $dataname = ($this->_mMainTable==$this->getDataname()) ? null : $this->getDataname();
        return Legacy_Utils::renderUri($this->getDirname(), $dataname, $this->get($this->getPrimary()), $action);
    }

    /**
     * getImages
     * 
     * @param   void
     * 
     * @return  void
     */
    public function getImages()
    {
        $imageObjs = array();
        XCube_DelegateUtils::call('Legacy_Image.GetImageObjects', new XCube_Ref($imageObjs), $this->getDirname(), $this->getDataname(), $this->get($this->getPrimary()));
        return $imageObjs;
    }

    /**
     * load tag array related to this page
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function loadTag()
    {
        $chandler = xoops_gethandler('config');
        $configArr = $chandler->getConfigsByDirname($this->getDirname());
    
        if($this->_mIsTagLoaded==false && $tagDirname = $configArr['tag_dirname']){
            $tagArr = array();
            if(! $this->isNew()){
                XCube_DelegateUtils::call('Legacy_Tag.'.$configArr['tag_dirname'].'.GetTags',
                    new XCube_Ref($tagArr),
                    $tagDirname,
                    $this->getDirname(),
                    $this->getDataname(),
                    $this->get($this->getPrimary())
                );
            }
            $this->mTag = $tagArr;
            $this->_mIsTagLoaded = true;
        }
    }

    public function setTempImage()
    {
        $this->mTempImage = $_FILES['img']['tmp_name'];
    }
}
