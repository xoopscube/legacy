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
     * addItem	Legacy_Progress.AddItem
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
     * deleteItem	Legacy_Progress.DeleteItem
     *
     * @param string $dirname
     * @param string $target
     * @param int    $id
     *
     * @return  void
     */ 
    abstract public function deleteItem(/*** string ***/ $dirname, /*** string ***/ $target, /*** int ***/ $id);

    /**
     * getHistory	Legacy_Progress.GetHistory
     *
     * @param mix[] &$historyArr
     *	$hisotryArr['step']
     *	$hisotryArr['uid']
     *	$hisotryArr['result']
     *	$hisotryArr['comment']
     *	$hisotryArr['posttime']
     * @param string $dirname
     * @param string $target
     * @param int    $id
     *
     * @return  void
     */ 
    abstract public function getHistory(/*** mix[] ***/ &$historyArr, /*** string ***/ $dirname, /*** string ***/ $target, /*** int ***/ $id);


}

?>
