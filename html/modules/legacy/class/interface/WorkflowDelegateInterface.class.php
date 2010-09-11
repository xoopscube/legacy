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
 * Interface of workflow delegate
**/
interface Legacy_iWorkflowDelegate
{
	/**
	 * addItem	Legacy_Workflow.AddItem
	 *
	 * @param string $title
	 * @param string $dirname
	 * @param string $dataname
	 * @param int	 $data_id
	 * @param string $url
	 *
	 * @return	void
	 */ 
	public static function addItem(/*** string ***/ $title, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id, /*** string ***/ $url);

	/**
	 * deleteItem	Legacy_Workflow.DeleteItem
	 *
	 * @param string $dirname
	 * @param string $dataname
	 * @param int	 $data_id
	 *
	 * @return	void
	 */ 
	public static function deleteItem(/*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id);

	/**
	 * getHistory	Legacy_Workflow.GetHistory
	 *
	 * @param mix[] &$historyArr
	 *	$hisotryArr['step']
	 *	$hisotryArr['uid']
	 *	$hisotryArr['result']
	 *	$hisotryArr['comment']
	 *	$hisotryArr['posttime']
	 * @param string $dirname
	 * @param string $dataname
	 * @param int	 $data_id
	 *
	 * @return	void
	 */ 
	public static function getHistory(/*** mix[] ***/ &$historyArr, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id);


}

?>
