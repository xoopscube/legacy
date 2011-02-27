<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageFilterForm.class.php,v 1.3 2008/09/25 15:12:39 kilica Exp $
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
define('IMAGE_SORT_KEY_MAXVALUE', 8);

define('IMAGE_SORT_KEY_DEFAULT', IMAGE_SORT_KEY_IMAGE_WEIGHT);

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

		$this->mNavi->addExtra('target', xoops_getrequest('target'));
	
		if (isset($_REQUEST['imgcat_id'])) {
			$this->mNavi->addExtra('imgcat_id', xoops_getrequest('imgcat_id'));
			$this->_mCriteria->add(new Criteria('imgcat_id', array(XOBJ_DTYPE_INT, xoops_getrequest('imgcat_id'))));
		}
		else {
			$this->_mCriteria->add(new Criteria('imgcat_id', 0));
		}
		
		$this->_mCriteria->add(new Criteria('image_display', 1));

		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
		if (abs($this->mSort) != IMAGE_SORT_KEY_IMAGE_WEIGHT) {
			$this->_mCriteria->addSort(IMAGE_SORT_KEY_IMAGE_WEIGHT, $this->getOrder());
		}
	}
}

?>
