<?php
/**
 *
 * @package Legacy
 * @version $Id: CommentFilterForm.class.php,v 1.3 2008/09/25 15:10:34 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractFilterForm.class.php";

define('COMMENT_SORT_KEY_COM_ID', 1);
define('COMMENT_SORT_KEY_COM_PID', 2);
define('COMMENT_SORT_KEY_COM_ROOTID', 3);
define('COMMENT_SORT_KEY_COM_MODID', 4);
define('COMMENT_SORT_KEY_COM_ITEMID', 5);
define('COMMENT_SORT_KEY_COM_ICON', 6);
define('COMMENT_SORT_KEY_COM_CREATED', 7);
define('COMMENT_SORT_KEY_COM_MODIFIED', 8);
define('COMMENT_SORT_KEY_COM_UID', 9);
define('COMMENT_SORT_KEY_COM_IP', 10);
define('COMMENT_SORT_KEY_COM_TITLE', 11);
define('COMMENT_SORT_KEY_COM_TEXT', 12);
define('COMMENT_SORT_KEY_COM_SIG', 13);
define('COMMENT_SORT_KEY_COM_STATUS', 14);
define('COMMENT_SORT_KEY_COM_EXPARAMS', 15);
define('COMMENT_SORT_KEY_DOHTML', 16);
define('COMMENT_SORT_KEY_DOSMILEY', 17);
define('COMMENT_SORT_KEY_DOXCODE', 18);
define('COMMENT_SORT_KEY_DOIMAGE', 19);
define('COMMENT_SORT_KEY_DOBR', 20);

define('COMMENT_SORT_KEY_DEFAULT', COMMENT_SORT_KEY_COM_ID);
define('COMMENT_SORT_KEY_MAXVALUE', 20);

class Legacy_CommentFilterForm extends Legacy_AbstractFilterForm
{
	var $mSortKeys = array(
		COMMENT_SORT_KEY_COM_ID => 'com_id',
		COMMENT_SORT_KEY_COM_PID => 'com_pid',
		COMMENT_SORT_KEY_COM_ROOTID => 'com_rootid',
		COMMENT_SORT_KEY_COM_MODID => 'com_modid',
		COMMENT_SORT_KEY_COM_ITEMID => 'com_itemid',
		COMMENT_SORT_KEY_COM_ICON => 'com_icon',
		COMMENT_SORT_KEY_COM_CREATED => 'com_created',
		COMMENT_SORT_KEY_COM_MODIFIED => 'com_modified',
		COMMENT_SORT_KEY_COM_UID => 'com_uid',
		COMMENT_SORT_KEY_COM_IP => 'com_ip',
		COMMENT_SORT_KEY_COM_TITLE => 'com_title',
		COMMENT_SORT_KEY_COM_TEXT => 'com_text',
		COMMENT_SORT_KEY_COM_SIG => 'com_sig',
		COMMENT_SORT_KEY_COM_STATUS => 'com_status',
		COMMENT_SORT_KEY_COM_EXPARAMS => 'com_exparams',
		COMMENT_SORT_KEY_DOHTML => 'dohtml',
		COMMENT_SORT_KEY_DOSMILEY => 'dosmiley',
		COMMENT_SORT_KEY_DOXCODE => 'doxcode',
		COMMENT_SORT_KEY_DOIMAGE => 'doimage',
		COMMENT_SORT_KEY_DOBR => 'dobr'
	);

	function getDefaultSortKey()
	{
		return COMMENT_SORT_KEY_DEFAULT;
	}
	
	function fetch()
	{
		parent::fetch();
	
		if (isset($_REQUEST['com_modid']) && intval(xoops_getrequest('com_modid')) > 0) {
			$this->mNavi->addExtra('com_modid', xoops_getrequest('com_modid'));
			$this->_mCriteria->add(new Criteria('com_modid', xoops_getrequest('com_modid')));
		}
		elseif (isset($_REQUEST['dirname'])) {
			$this->mNavi->addExtra('dirname', xoops_getrequest('dirname'));

			$handler =& xoops_gethandler('module');
			$module =& $handler->getByDirname(xoops_getrequest('dirname'));
			if (is_object($module)) {
				$this->_mCriteria->add(new Criteria('com_modid', $module->get('mid')));
			}
		}
	
		if (isset($_REQUEST['com_icon'])) {
			$this->mNavi->addExtra('com_icon', xoops_getrequest('com_icon'));
			$this->_mCriteria->add(new Criteria('com_icon', xoops_getrequest('com_icon')));
		}
	
		if (isset($_REQUEST['com_uid'])) {
			$this->mNavi->addExtra('com_uid', xoops_getrequest('com_uid'));
			$this->_mCriteria->add(new Criteria('com_uid', xoops_getrequest('com_uid')));
		}
	
		if (isset($_REQUEST['com_ip'])) {
			$this->mNavi->addExtra('com_ip', xoops_getrequest('com_ip'));
			$this->_mCriteria->add(new Criteria('com_ip', xoops_getrequest('com_ip')));
		}
	
		if (xoops_getrequest('com_status') > 0) {
			$this->mNavi->addExtra('com_status', xoops_getrequest('com_status'));
			$this->_mCriteria->add(new Criteria('com_status', xoops_getrequest('com_status')));
		}

		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
