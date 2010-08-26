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
 * Interface of progress client delegate
**/
interface Legacy_iProgressClientDelegate
{
	/**
	 * updateStatus Legacy_ProgressClient.UpdateStatus
	 *
	 * @param string	&$result
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param int		$data_id
	 * @param Enum		$status Lenum_ProgressStatus
	 *
	 * @return	void
	 */ 
	public static function updateStatus(/*** string ***/ &$result, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id, /*** Enum ***/ $status);
}

?>
