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
 * Interface of progress delegate
**/
abstract class Legacy_AbstractProgressDelegate
{
    /**
     * addItem
     *
     * @param string $title
     * @param string $dirname
     * @param string $target
     * @param int    $id
     *
     * @return  void
     */ 
    abstract public function addItem(/*** string ***/ $title, /*** string ***/ $dirname, /*** string ***/ $target, /*** int ***/ $id);

    /**
     * deleteItem
     *
     * @param string $dirname
     * @param string $target
     * @param int    $id
     *
     * @return  void
     */ 
    abstract public function deleteItem(/*** string ***/ $dirname, /*** string ***/ $target, /*** int ***/ $id);

    /**
     * getHistory
     *
     * @param XoopsSimpleObject[] &$historyArr
     * @param string $dirname
     * @param string $target
     * @param int    $id
     *
     * @return  void
     */ 
    abstract public function getHistory(/*** bool ***/ &$response, /*** string ***/ $dirname, /*** string ***/ $target, /*** int ***/ $id);


}

?>
