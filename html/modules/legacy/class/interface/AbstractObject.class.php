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
    public /*** Legacy_ImageObject[] ***/ $mImage = array();

    /**
     * __construct
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function __construct()
    {
    	parent::__construct();
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
        if($this->get($this->getPrimary())>0){
	        XCube_DelegateUtils::call('Legacy_Image.GetImageObjects', new XCube_Ref($imageObjs), $this->getDirname(), $this->getDataname(), $this->get($this->getPrimary()));
	    }
        return $imageObjs;
    }

    /**
     * get number of image used in this table
     * 
     * @param   void
     * 
     * @return  int
    **/
	public function getImageNumber()
	{
		return 0;
	}

    /**
     * Setup Image Objects linked to this object
     * 
     * @param   bool	$isPost
     * 
     * @return  void
    **/
	public function setupImages($isPost=true)
	{
		if(count($this->mImage)>0) return;
		$handler = Legacy_Utils::getModuleHandler($this->getDataname(), $this->getDirname());
	
		$n = $this->getImageNumber();
		if($n===0) return;
	
		$this->mImage = $this->getImages();
	
		$originalImage = array();
		XCube_DelegateUtils::call('Legacy_Image.CreateImageObject', new XCube_Ref($originalImage));
		$originalImage->set('title', $this->get($handler->getClientField('title')));
		$originalImage->set('uid', Legacy_Utils::getUid());
		$originalImage->set('dirname', $this->getDirname());
		$originalImage->set('dataname', $this->getDataname());
		$originalImage->set('data_id', $this->get($this->getPrimary()));
	
		for($i=1;$i<=$n;$i++){
			if(! isset($this->mImage[$i])){
				$this->mImage[$i] = clone $originalImage;
				$this->mImage[$i]->set('num', $i);
			}
			if($isPost===true){
				$this->mImage[$i]->setupPostData($i);
			}
		}
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
}
