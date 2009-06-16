<?php
/**
 *
 * @package Legacy
 * @version $Id: ImagecategoryFilterForm.class.php,v 1.4 2008/09/25 15:11:17 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractFilterForm.class.php";

define('IMAGECATEGORY_SORT_KEY_IMGCAT_ID', 1);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_NAME', 2);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_MAXSIZE', 3);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_MAXWIDTH', 4);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_MAXHEIGHT', 5);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_DISPLAY', 6);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT', 7);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_TYPE', 8);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_STORETYPE', 9);

define('IMAGECATEGORY_SORT_KEY_DEFAULT', IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT);
define('IMAGECATEGORY_SORT_KEY_MAXVALUE', 9);

class Legacy_ImagecategoryFilterForm extends Legacy_AbstractFilterForm
{
	var $mSortKeys = array(
		IMAGECATEGORY_SORT_KEY_IMGCAT_ID => 'imgcat_id',
		IMAGECATEGORY_SORT_KEY_IMGCAT_NAME => 'imgcat_name',
		IMAGECATEGORY_SORT_KEY_IMGCAT_MAXSIZE => 'imgcat_maxsize',
		IMAGECATEGORY_SORT_KEY_IMGCAT_MAXWIDTH => 'imgcat_maxwidth',
		IMAGECATEGORY_SORT_KEY_IMGCAT_MAXHEIGHT => 'imgcat_maxheight',
		IMAGECATEGORY_SORT_KEY_IMGCAT_DISPLAY => 'imgcat_display',
		IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT => 'imgcat_weight',
		IMAGECATEGORY_SORT_KEY_IMGCAT_TYPE => 'imgcat_type',
		IMAGECATEGORY_SORT_KEY_IMGCAT_STORETYPE => 'imgcat_storetype'
	);

	var $mKeyword = "";
	var $mOptionField = "";
	var $mOptionField2 = "";

	function getDefaultSortKey()
	{
		return IMAGECATEGORY_SORT_KEY_DEFAULT;
	}
	
	function fetch()
	{
		parent::fetch();

		$root =& XCube_Root::getSingleton();
		$imgcat_name = $root->mContext->mRequest->getRequest('imgcat_name');
		$imgcat_display = $root->mContext->mRequest->getRequest('imgcat_display');
		$imgcat_type = $root->mContext->mRequest->getRequest('imgcat_type');
		$imgcat_storetype = $root->mContext->mRequest->getRequest('imgcat_storetype');
		$option_field = $root->mContext->mRequest->getRequest('option_field');
		$option_field2 = $root->mContext->mRequest->getRequest('option_field2');
		$search = $root->mContext->mRequest->getRequest('search');

		
		if (isset($_REQUEST['imgcat_name'])) {
			$this->mNavi->addExtra('imgcat_name', xoops_getrequest('imgcat_name'));
			$this->_mCriteria->add(new Criteria('imgcat_name', xoops_getrequest('imgcat_name')));
		}
	
		if (isset($_REQUEST['imgcat_display'])) {
			$this->mNavi->addExtra('imgcat_display', xoops_getrequest('imgcat_display'));
			$this->_mCriteria->add(new Criteria('imgcat_display', xoops_getrequest('imgcat_display')));
		}
	
		if (isset($_REQUEST['imgcat_type'])) {
			$this->mNavi->addExtra('imgcat_type', xoops_getrequest('imgcat_type'));
			$this->_mCriteria->add(new Criteria('imgcat_type', xoops_getrequest('imgcat_type')));
		}
	
		if (isset($_REQUEST['imgcat_storetype'])) {
			$this->mNavi->addExtra('imgcat_storetype', xoops_getrequest('imgcat_storetype'));
			$this->_mCriteria->add(new Criteria('imgcat_storetype', xoops_getrequest('imgcat_storetype')));
		}

		if (isset($_REQUEST['option_field'])) {
			$this->mNavi->addExtra('option_field', xoops_getrequest('option_field'));
			$this->mOptionField = $option_field;
			if ( $this->mOptionField == "visible" ) {
			$this->_mCriteria->add(new Criteria('imgcat_display', xoops_getrequest('1')));
			}
			elseif ( $this->mOptionField == "invisible" ) {
			$this->_mCriteria->add(new Criteria('imgcat_display', xoops_getrequest('0')));
			}
			else {
			//all
			}
		}

		if (isset($_REQUEST['option_field2'])) {
			$this->mNavi->addExtra('option_field2', xoops_getrequest('option_field2'));
			$this->mOptionField2 = $option_field2;
			if ( $this->mOptionField2 == "file" ) {
			$this->_mCriteria->add(new Criteria('imgcat_storetype', xoops_getrequest('file')));
			}
			elseif ( $this->mOptionField2 == "db" ) {
			$this->_mCriteria->add(new Criteria('imgcat_storetype', xoops_getrequest('db')));
			}
			else {
			//all
			}
		}

		//
		if (!empty($search)) {
			$this->mKeyword = $search;
			$this->mNavi->addExtra('search', $this->mKeyword);
			$this->_mCriteria->add(new Criteria('imgcat_name', '%' . $this->mKeyword . '%', 'LIKE'));
		}
		
		//
		// Set sort conditions.
		//
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
		if (abs($this->mSort) != IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT) {
			$this->_mCriteria->addSort($this->mSortKeys[IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT], $this->getOrder());
		}
	}
}

?>
