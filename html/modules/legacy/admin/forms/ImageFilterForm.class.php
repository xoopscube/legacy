<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageFilterForm.class.php,v 1.4 2008/09/25 15:11:10 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractFilterForm.class.php";

define('IMAGE_SORT_KEY_IMAGE_ID', 1);
define('IMAGE_SORT_KEY_IMAGE_NAME', 2);
define('IMAGE_SORT_KEY_IMAGE_NICENAME', 3);
define('IMAGE_SORT_KEY_IMAGE_MIMETYPE', 4);
define('IMAGE_SORT_KEY_IMAGE_CREATED', 5);
define('IMAGE_SORT_KEY_IMAGE_DISPLAY', 6);
define('IMAGE_SORT_KEY_IMAGE_WEIGHT', 7);
define('IMAGE_SORT_KEY_IMGCAT_ID', 8);

define('IMAGE_SORT_KEY_DEFAULT', IMAGE_SORT_KEY_IMAGE_WEIGHT);
define('IMAGE_SORT_KEY_MAXVALUE', 8);

class Legacy_ImageFilterForm extends Legacy_AbstractFilterForm
{
	var $mSortKeys = array(
		IMAGE_SORT_KEY_IMAGE_ID => 'image_id',
		IMAGE_SORT_KEY_IMAGE_NAME => 'image_name',
		IMAGE_SORT_KEY_IMAGE_NICENAME => 'image_nicename',
		IMAGE_SORT_KEY_IMAGE_MIMETYPE => 'image_mimetype',
		IMAGE_SORT_KEY_IMAGE_CREATED => 'image_created',
		IMAGE_SORT_KEY_IMAGE_DISPLAY => 'image_display',
		IMAGE_SORT_KEY_IMAGE_WEIGHT => 'image_weight',
		IMAGE_SORT_KEY_IMGCAT_ID => 'imgcat_id'
	);
	
	function getDefaultSortKey()
	{
		return IMAGE_SORT_KEY_DEFAULT;
	}
	
	function fetch()
	{
		parent::fetch();
	
		if (isset($_REQUEST['image_display'])) {
			$this->mNavi->addExtra('image_display', xoops_getrequest('image_display'));
			$this->_mCriteria->add(new Criteria('image_display', xoops_getrequest('image_display')));
		}
	
		if (isset($_REQUEST['imgcat_id'])) {
			$this->mNavi->addExtra('imgcat_id', xoops_getrequest('imgcat_id'));
			$this->_mCriteria->add(new Criteria('imgcat_id', xoops_getrequest('imgcat_id')));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
		if (abs($this->mSort) != IMAGE_SORT_KEY_IMAGE_WEIGHT) {
			$this->_mCriteria->addSort($this->mSortKeys[IMAGE_SORT_KEY_IMAGE_WEIGHT], $this->getOrder());
		}
	}
}

?>
