<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once dirname(__FILE__) . '/AbstractObject.class.php';
/**
 * Abstract Category Class
**/
abstract class Legacy_AbstractCategoryObject extends Legacy_AbstractObject
{
    const PRIMARY = 'cat_id';
    const DATANAME = 'cat';
    public $mChildren = array();    //Legacy_AbstractCategoryObject[]

    public function Legacy_AbstractCategoryObject()
    {
        self::__construct();
    }

    public function __construct()
    {
        $this->initVar('cat_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('title', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('p_id', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('modules', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('description', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('weight', XOBJ_DTYPE_INT, '10', false);
        $this->initVar('options', XOBJ_DTYPE_TEXT, '', false);
    }

    /**
     * getPrimary
     * 
     * @param	void
     * 
     * @return	string
    **/
    public function getPrimary()
    {
        return self::PRIMARY;
    }

    /**
     * getDataname
     * 
     * @param	void
     * 
     * @return	string
    **/
    public function getDataname()
    {
        return self::DATANAME;
    }

    /**
     * getDepth
     * 
     * @params	string $module
     *
     * @return int
     */
    abstract public function getDepth();

    /**
     * checkPermitByUid
     * 
     * @params	string	$action
     * @params	int 	$uid
     * @params	string	$module
     *
     * @return bool
     */
    abstract public function checkPermitByUid(/*** string ***/ $action, /*** int ***/ $uid=0, /*** string ***/ $module="");

    /**
     * checkPermitByGroupid
     * 
     * @params	string	$action
     * @params	int 	$groupid
     * @params	string	$module
     *
     * @return bool
     */
    abstract public function checkPermitByGroupid(/*** string ***/ $action, /*** int ***/ $groupid=0, /*** string ***/ $module="");

    /**
     * renderUri
     * 
     * @param	string	$action
     * 
     * @return	string
     */
    public function renderUri($action=null)
    {
        return Legacy_Utils::renderUri($this->getDirname(), $this->getDataname(), $this->get($this->getPrimary()), $action);
    }
}
