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
 * Interface of progress use delegate
**/
abstract class Legacy_AbstractProgressUseDelegate
{
    /**
     * getManagedModules    Legacy_Progress.GetManagedModules
     *
     * @param mix[] &$list
     *  $list['dirname']
     *  $list['target_name']
     *
     * @return  void
     */ 
    abstract public function getManagedModules(/*** array ***/ &$list);

    /**
     * getSourceUrl Legacy_Progress.GetSourceUrl
     *
     * @param string &$url
     * @param string $dirname
     * @param string $target_name
     * @param int $target_id
     *
     * @return  void
     */ 
    abstract public function getSourceUrl(/*** string ***/ &$url, /*** string ***/ $dirname, /*** string ***/ $target_name, /*** int ***/ $target_id);

    /**
     * updateStatus Legacy_Progress.UpdateStatus
     *
     * @param string    &$result
     * @param string    $dirname
     * @param string    $target_name
     * @param int       $target_id
     * @param Enum      $status Legacy_ProgressStatus
     *
     * @return  void
     */ 
    abstract public function updateStatus(/*** string ***/ &$result, /*** string ***/ $dirname, /*** string ***/ $target_name, /*** int ***/ $target_id, /*** Enum ***/ $status);
}

?>
