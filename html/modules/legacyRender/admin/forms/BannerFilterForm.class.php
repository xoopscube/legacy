<?php
/**
 * @package legacyRender
 * @version $Id: BannerFilterForm.class.php,v 1.1 2007/05/15 02:34:40 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractFilterForm.class.php";

define('BANNER_SORT_KEY_BID', 1);
define('BANNER_SORT_KEY_CID', 2);
define('BANNER_SORT_KEY_IMPTOTAL', 3);
define('BANNER_SORT_KEY_IMPMADE', 4);
define('BANNER_SORT_KEY_CLICKS', 5);
define('BANNER_SORT_KEY_IMAGEURL', 6);
define('BANNER_SORT_KEY_CLICKURL', 7);
define('BANNER_SORT_KEY_DATE', 8);
define('BANNER_SORT_KEY_HTMLBANNER', 9);
define('BANNER_SORT_KEY_HTMLCODE', 10);
define('BANNER_SORT_KEY_MAXVALUE', 10);

define('BANNER_SORT_KEY_DEFAULT', BANNER_SORT_KEY_BID);

class LegacyRender_BannerFilterForm extends LegacyRender_AbstractFilterForm
{
	var $mSortKeys = array(
		BANNER_SORT_KEY_BID => 'bid',
		BANNER_SORT_KEY_CID => 'cid',
		BANNER_SORT_KEY_IMPTOTAL => 'imptotal',
		BANNER_SORT_KEY_IMPMADE => 'impmade',
		BANNER_SORT_KEY_CLICKS => 'clicks',
		BANNER_SORT_KEY_IMAGEURL => 'imageurl',
		BANNER_SORT_KEY_CLICKURL => 'clickurl',
		BANNER_SORT_KEY_DATE => 'date',
		BANNER_SORT_KEY_HTMLBANNER => 'htmlbanner',
		BANNER_SORT_KEY_HTMLCODE => 'htmlcode'
	);

	function getDefaultSortKey()
	{
		return BANNER_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();
	
		if (isset($_REQUEST['cid'])) {
			$this->mNavi->addExtra('cid', xoops_getrequest('cid'));
			$this->_mCriteria->add(new Criteria('cid', xoops_getrequest('cid')));
		}
	
		if (isset($_REQUEST['htmlbanner'])) {
			$this->mNavi->addExtra('htmlbanner', xoops_getrequest('htmlbanner'));
			$this->_mCriteria->add(new Criteria('htmlbanner', xoops_getrequest('htmlbanner')));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
