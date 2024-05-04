<?php
/**
 * Legacy_AbstractObject
 * This class is generated by Cube tool.
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     code generator
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit;
}


abstract class Legacy_AbstractObject extends XoopsSimpleObject
{
    //const PRIMARY = '';
    //const DATANAME = '';

    protected $_mMainTable = null;  //module's main table name

    /*** string[] ***/ public $mChildList = [];    //Child table's name array
    /*** string[] ***/ public $mParentList = [];   //Parent table's name array
    /*** XoopsSimpleObject[] ***/ public $mTable = [];
    /*** bool[] ***/ protected $_mIsLoaded = [];

    /*** bool ***/ protected $_mIsTagLoaded = false;
    /*** string[] ***/ public $mTag = [];
    /*** Legacy_ImageObject[] ***/ public $mImage = [];

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
        foreach ($this->mChildList as $table) {
            $this->_mIsLoaded[$table] = false;
            $this->mTable[$table] = [];
        }
        foreach ($this->mParentList as $table) {
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
        if (true === $this->_mIsLoaded[$table]) {
            return;
        }

        $dirname ??= $this->getDirname();
        $handler = Legacy_Utils::getModuleHandler($table, $dirname);

        if (in_array($table, $this->mChildList)) {
            $this->mTable[$table] = $handler->getObjects(new Criteria($this->getPrimary(), $this->get($this->getPrimary())));
        } elseif (in_array($table, $this->mParentList)) {
            $this->mTable[$table] = $handler->get($this->get($handler->mPrimary));
        } else {
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
     * @return array
     */
    public function getImages()
    {
        $imageObjs = [];
        if ($this->get($this->getPrimary())>0) {
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
        if (count($this->mImage)>0) {
            return;
        }
        $handler = Legacy_Utils::getModuleHandler($this->getDataname(), $this->getDirname());

        $n = $this->getImageNumber();
        if (0 === $n) {
            return;
        }

        $this->mImage = $this->getImages();

        $originalImage = [];
        XCube_DelegateUtils::call('Legacy_Image.CreateImageObject', new XCube_Ref($originalImage));
        $originalImage->set('title', $this->get($handler->getClientField('title')));
        $originalImage->set('uid', Legacy_Utils::getUid());
        $originalImage->set('dirname', $this->getDirname());
        $originalImage->set('dataname', $this->getDataname());
        $originalImage->set('data_id', $this->get($this->getPrimary()));

        for ($i=1;$i<=$n;$i++) {
            if (! isset($this->mImage[$i])) {
                $this->mImage[$i] = clone $originalImage;
                $this->mImage[$i]->set('num', $i);
            }
            if (true === $isPost) {
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

        if (false == $this->_mIsTagLoaded && $tagDirname = $configArr['tag_dirname']) {
            $tagArr = [];
            if (! $this->isNew()) {
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

    public function onWorkflow()
    {
    }
}
