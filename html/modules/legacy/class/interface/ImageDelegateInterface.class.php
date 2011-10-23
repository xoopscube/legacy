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
 * Interface of Image delegate
 * Legacy_Image module must be unique.
 * You can get its dirname by constant LEGACY_IMAGE_DIRNAME
**/
interface Legacy_iImageDelegate
{
	/**
	 * createImageObject	Legacy_Image.CreateImageObject
	 * Create new Image Object
	 * must be 'setNew()'.
	 *
	 * @param Legacy_AbstractImageObject	&$obj
	 *
	 * @return	void
	 */ 
	public static function createImageObject(/*** Legacy_AbstractImageObject ***/ &$obj);

	/**
	 * saveImage	Legacy_Image.SaveImage
	 * 1) insert Legacy_AbstractImageObject to database
	 * 2) copy image from upload file($_FILES['legacy_image']) to upload directory
	 * 3) create thumbnail if needed.
	 *
	 * @param bool		&$ret
	 * @param Abstract_ImageObject	$obj
	 *
	 * @return	void
	 */ 
	public static function saveImage(/*** bool ***/ &$ret, /*** Legacy_AbstractImageObject ***/ $obj);

	/**
	 * deleteImage	Legacy_Image.DeleteImage
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
	 * getImageObjects	Legacy_Image.GetImageObjects
	 * return requested image objects
	 *
	 * @param Legacy_AbstractImageObject[]	&$objects
	 * @param string	$dirname	client module dirname
	 * @param string	$dataname	client module dataname
	 * @param int		$dataId		client module primary key
	 * @param int		$num		image serial number in a client data
	 * @param int		$limit		the number of images 
	 * @param int		$start		offset value
	 *
	 * @return	void
	 */ 
	public static function getImageObjects(/*** Legacy_AbstractImageObject[] ***/ &$objects, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $dataId=0, /*** int ***/ $num=0, /*** int ***/ $limit=10, /*** int ***/ $start=0);
}

?>
