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
 * Interface of group client delegate
 * Modules which uses Legacy_Comment must implement this interface.
**/
interface Legacy_iCommentClientDelegate
{
    /**
     * getClientList	Legacy_CommentClient.{dirname}.GetClientList
     *
     * @param mixed[]	&$list
     *  string	$list[]['dirname']	client module's dirname
     *  string	$list[]['dataname']	client module's dataname(tablename)
     *  string	$list[]['access_controller']
     * @param string	$cDirname	comment module's dirname
     *
     * @return	void
     */
    public static function getClientList(/*** mixed[] ***/ &$list, /*** string ***/ $cDirname);
}
