<?php
/**
 * @package legacyRender
 * @version $Id: BannerclientFilterForm.class.php,v 1.1 2007/05/15 02:34:40 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractFilterForm.class.php";

define('BANNERCLIENT_SORT_KEY_CID', 1);
define('BANNERCLIENT_SORT_KEY_NAME', 2);
define('BANNERCLIENT_SORT_KEY_CONTACT', 3);
define('BANNERCLIENT_SORT_KEY_EMAIL', 4);
define('BANNERCLIENT_SORT_KEY_LOGIN', 5);
define('BANNERCLIENT_SORT_KEY_PASSWD', 6);
define('BANNERCLIENT_SORT_KEY_EXTRAINFO', 7);
define('BANNERCLIENT_SORT_KEY_MAXVALUE', 7);

define('BANNERCLIENT_SORT_KEY_DEFAULT', BANNERCLIENT_SORT_KEY_CID);

class LegacyRender_BannerclientFilterForm extends LegacyRender_AbstractFilterForm
{
	var $mSortKeys = array(
		BANNERCLIENT_SORT_KEY_CID => 'cid',
		BANNERCLIENT_SORT_KEY_NAME => 'name',
		BANNERCLIENT_SORT_KEY_CONTACT => 'contact',
		BANNERCLIENT_SORT_KEY_EMAIL => 'email',
		BANNERCLIENT_SORT_KEY_LOGIN => 'login',
		BANNERCLIENT_SORT_KEY_PASSWD => 'passwd',
		BANNERCLIENT_SORT_KEY_EXTRAINFO => 'extrainfo'
	);
	
	function getDefaultSortKey()
	{
		return BANNERCLIENT_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();

		if (isset($_REQUEST['name'])) {
			$this->mNavi->addExtra('name', xoops_getrequest('name'));
			// TODO Changet to 'LIKE' (?)
			$this->_mCriteria->add(new Criteria('name', xoops_getrequest('name')));
		}
	
		if (isset($_REQUEST['contact'])) {
			$this->mNavi->addExtra('contact', xoops_getrequest('contact'));
			// TODO Changet to 'LIKE' (?)
			$this->_mCriteria->add(new Criteria('contact', xoops_getrequest('contact')));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
