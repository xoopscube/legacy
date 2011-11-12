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
 * Abstract Activity Class
**/
abstract class Legacy_AbstractActivityObject extends XoopsSimpleObject
{
	public function __construct()
	{
		$this->initVar('activity_id', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('dirname', XOBJ_DTYPE_STRING, '', false, 32);
		$this->initVar('dataname', XOBJ_DTYPE_STRING, '', false, 32);
		$this->initVar('data_id', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('uid', XOBJ_DTYPE_INT, '0', false);
		//cat_id is field in Legacy_AbstractCategoryObject
		$this->initVar('cat_id', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('title', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('description', XOBJ_DTYPE_TEXT, '', false);
		$this->initVar('pubdate', XOBJ_DTYPE_INT, time(), false);
		$this->initVar('category', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('author', XOBJ_DTYPE_STRING, '', false, 128);
		$this->initVar('link', XOBJ_DTYPE_TEXT, '', false);
		$this->initVar('latitude', XOBJ_DTYPE_FLOAT, '0.0', false);
		$this->initVar('longitude', XOBJ_DTYPE_FLOAT, '0.0', false);
		$this->initVar('posttime', XOBJ_DTYPE_INT, time(), false);
	}
}

/**
 * Abstract Group Activity Class
**/
abstract class Legacy_AbstractGroupActivityObject extends Legacy_AbstractActivityObject
{
	public function __construct()
	{
		parent::__construct();
		$this->initVar('group_id', XOBJ_DTYPE_INT, '0', false);
	}
}

/**
 * Abstract User Activity Class
**/
abstract class Legacy_AbstractUserActivityObject extends Legacy_AbstractActivityObject
{
	public function __construct()
	{
		parent::__construct();
		$this->initVar('guest_access', XOBJ_DTYPE_INT, '0', false);
	}
}

/**
 * Abstract Calendar Activity Class
**/
abstract class Legacy_AbstractCalendarObject extends Legacy_AbstractActivityObject{
	public function __construct()
	{
		parent::__construct();
		$this->initVar('location', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('starttime', XOBJ_DTYPE_STRING, '0', false);
		$this->initVar('endtime', XOBJ_DTYPE_STRING, '0', false);
	}
}

abstract class Legacy_AbstractActivityHandler extends XoopsObjectGenericHandler
{
	abstract public function getActivities(/*** int ***/ $data_id, /*** int ***/ $limit=20, /*** int ***/ $start=0);
	abstract public function getMyActivities(/*** int ***/ $uid, /*** int ***/ $limit=20, /*** int ***/ $start=0);

}

?>
