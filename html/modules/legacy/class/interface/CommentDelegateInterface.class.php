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
 * Interface of module's comment delegate
**/
interface Legacy_iCommentDelegate
{
	/**
	 * getComments	Legacy_Comment.{dirname}.GetComments
	 * This delegate point is used by smarty plugin smarty_function_legacy_comment.
	 * $comments is passed to the template of the comment module right away.
	 *
	 * @param mixed[]	&$comments
	 * @param string	$cDirname	comment module's dirname
	 * @param string	$dirname	client module's dirname
	 * @param string	$dataname	client module's dataname(tablename)
	 * @param int		$dataId		client module's primary key
	 * @param int		$categoryId	client module's category id
	 * @param int		$params		other arguments for comments filtering
	 * @param int		$limit		max number of comments
	 *
	 * @return	void
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

?>
