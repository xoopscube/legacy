<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit();
}

/**
 * Abstract Category Class
**/
abstract class Legacy_AbstractCategoryObject extends XoopsSimpleObject
{
    public function __construct
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
     * getDepth
     * 
     * @params  string $module
     *
     * @return int
     */
    abstract public function getDepth()

    /**
     * getChildList
     * 
     * @params  string $module
     *
     * @return int
     */
    abstract public function getChildList(/*** string ***/ $module="")

    /**
     * checkPermitByUid
     * 
     * @params  string  $action
     * @params  int     $uid
     * @params  string  $module
     *
     * @return bool
     */
    abstract public function checkPermitByUid(/*** string ***/ $action, /*** int ***/ $uid=0, /*** string ***/ $module="")

    /**
     * checkPermitByGroupid
     * 
     * @params  string  $action
     * @params  int     $groupid
     * @params  string  $module
     *
     * @return bool
     */
    public function checkPermitByGroupid(/*** string ***/ $action, /*** int ***/ $groupid=0, /*** string ***/ $module="")

}

?>
