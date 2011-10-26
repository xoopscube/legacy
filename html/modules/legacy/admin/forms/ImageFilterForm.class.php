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
	
	var $mKeyword = "";
	var $mOptionField = "";
	var $mOptionField2 = "";

	function getDefaultSortKey()
	{
		return IMAGE_SORT_KEY_DEFAULT;
	}
	
	function fetch()
	{
		parent::fetch();

		$root =& XCube_Root::getSingleton();
		$image_display = $root->mContext->mRequest->getRequest('image_display');
		$imgcat_id = $root->mContext->mRequest->getRequest('imgcat_id');
		$option_field = $root->mContext->mRequest->getRequest('option_field');
		$option_field2 = $root->mContext->mRequest->getRequest('option_field2');
		$search = $root->mContext->mRequest->getRequest('search');
	
		if (isset($_REQUEST['image_display'])) {
			$this->mNavi->addExtra('image_display', xoops_getrequest('image_display'));
			$this->_mCriteria->add(new Criteria('image_display', xoops_getrequest('image_display')));
		}
	
		if (isset($_REQUEST['imgcat_id'])) {
			$this->mNavi->addExtra('imgcat_id', xoops_getrequest('imgcat_id'));
		}


		if (isset($_REQUEST['option_field'])) {
			$this->mNavi->addExtra('option_field', $option_field);
			$this->mOptionField = $option_field;
			if ( $this->mOptionField == "visible" ) {
			$this->_mCriteria->add(new Criteria('image_display', '1'));
			}
			elseif ( $this->mOptionField == "invisible" ) {
			$this->_mCriteria->add(new Criteria('image_display', '0'));
			}
			else {
			//all
			}
		}

		if (isset($_REQUEST['option_field2'])) {
			$this->mNavi->addExtra('option_field2', $option_field2);
			$this->mOptionField2 = $option_field2;
			if ( $this->mOptionField2 == "gif" ) {
			$this->_mCriteria->add(new Criteria('image_mimetype', 'image/gif'));
			}
			elseif ( $this->mOptionField2 == "png" ) {
			$this->_mCriteria->add(new Criteria('image_mimetype', 'image/png'));
			}
			elseif ( $this->mOptionField2 == "jpeg" ) {
			$cri = new CriteriaCompo();
			$cri->add(new Criteria('image_mimetype', 'image/jpeg'));
			$cri->add(new Criteria('image_mimetype', 'image/pjpeg'), 'OR');
			$this->_mCriteria->add($cri);
			}
			else {
			//all
			}
		}

		//
		if (!empty($search)) {
			$this->mKeyword = $search;
			$this->mNavi->addExtra('search', $this->mKeyword);
			$this->_mCriteria->add(new Criteria('image_nicename', '%' . $this->mKeyword . '%', 'LIKE'));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
		if (abs($this->mSort) != IMAGE_SORT_KEY_IMAGE_WEIGHT) {
			$this->_mCriteria->addSort($this->mSortKeys[IMAGE_SORT_KEY_IMAGE_WEIGHT], $this->getOrder());
		}
	}
}

?>
