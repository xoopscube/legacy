<?php
/**
 * This Interface is generated by Cube tool.
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     code generator
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Interface of module's comment delegate
**/
interface Legacy_iCommentDelegate
{
    /**
     * getComments    Legacy_Comment.{dirname}.GetComments
     * This delegate point is used by smarty plugin smarty_function_legacy_comment.
     * $comments is passed to the template of the comment module right away.
     *
     * @param mixed[]    &$comments
     * @param string      $cDirname   comment module's dirname
     * @param string      $dirname    client module's dirname
     * @param string      $dataname   client module's dataname(tablename)
     * @param int         $dataId     client module's primary key
     * @param int         $categoryId client module's category id
     * @param int         $params     other arguments for comments filtering
     * @return    void
     */
    public static function getComments(/*** mixed[] ***/ &$comments, /*** string ***/ $cDirname, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $dataId, /*** int ***/ $categoryId, /*** mixed[] ***/ $params);

    /**
     * count	Legacy_Comment.{dirname}.Count
     *
     * @param int		&$count
     * @param int		$cDirname	comment module's dirname
     * @param string	$dirname	client module's dirname
     * @param string	$dataname	client module's dataname(tablename)
     * @param int		$dataId		client module's primary key
     *
     * @return	void
     */
    public static function count(/*** int ***/ &$count, /*** string ***/ $cDirname, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $dataId);
}
