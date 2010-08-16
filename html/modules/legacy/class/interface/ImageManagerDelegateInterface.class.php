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
 * Interface of Image Manager delegate
**/
interface Legacy_iImageManagerDelegate
{
	/**
	 * getImageObject
	 * Create new Image Object
	 *
	 * @param string	&$obj
	 *
	 * @return	void
	 */ 
	public static function createImageObject(/*** Legacy_AbstractImageObject ***/ &$obj);

	/**
	 * saveImage
	 * 1) insert Legacy_AbstractImageObject to database
	 * 2) copy image from upload file($_FILES) to upload directory
	 * 3) create thumbnail if needed.
	 *
	 * @param bool		&$ret
	 * @param string	$file	path to file as $_FILES['name']['tmp_name']
	 * @param Abstract_ImageObject	$obj
	 *
	 * @return	void
	 */ 
	public static function saveImage(/*** bool ***/ &$ret, /*** string ***/ $file, /*** Legacy_AbstractImageObject ***/ $obj);

	/**
	 * deleteImage
	 * 1) delete thumbnails
	 * 2) delete image file
	 * 3) delete image data from database
	 *
	 * @param bool		&$ret
	 * @param Abstract_ImageObject	$obj
	 *
	 * @return	void
	 */ 
	public static function deleteImage(/*** bool ***/ &$ret, /*** Legacy_AbstractImageObject ***/ $obj);

	/**
	 * getImageObjects
	 * return requested image objects
	 *
	 * @param Legacy_AbstractImageObject[]	&$objects
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param int		$dataId
	 * @param int		$num
	 * @param int		$limit
	 * @param int		$start
	 *
	 * @return	void
	 */ 
	public static function getImageObjects(/*** Legacy_AbstractImageObject ***/ &$objects, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $dataId=0, /*** int ***/ $num=0, /*** int ***/ $limit=10, /*** int ***/ $start=0);
}

?>
