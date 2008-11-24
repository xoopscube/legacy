<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractFilterForm.class.php";

define('TPLFILE_SORT_KEY_TPL_ID', 1);
define('TPLFILE_SORT_KEY_TPL_REFID', 2);
define('TPLFILE_SORT_KEY_TPL_MODULE', 3);
define('TPLFILE_SORT_KEY_TPL_TPLSET', 4);
define('TPLFILE_SORT_KEY_TPL_FILE', 5);
define('TPLFILE_SORT_KEY_TPL_DESC', 6);
define('TPLFILE_SORT_KEY_TPL_LASTMODIFIED', 7);
define('TPLFILE_SORT_KEY_TPL_LASTIMPORTED', 8);
define('TPLFILE_SORT_KEY_TPL_TYPE', 9);
define('TPLFILE_SORT_KEY_MAXVALUE', 9);

define('TPLFILE_SORT_KEY_DEFAULT', TPLFILE_SORT_KEY_TPL_FILE);

class LegacyRender_TplfileFilterForm extends LegacyRender_AbstractFilterForm
{
	var $mSortKeys = array(
		TPLFILE_SORT_KEY_TPL_ID => 'tpl_id',
		TPLFILE_SORT_KEY_TPL_REFID => 'tpl_refid',
		TPLFILE_SORT_KEY_TPL_MODULE => 'tpl_module',
		TPLFILE_SORT_KEY_TPL_TPLSET => 'tpl_tplset',
		TPLFILE_SORT_KEY_TPL_FILE => 'tpl_file',
		TPLFILE_SORT_KEY_TPL_DESC => 'tpl_desc',
		TPLFILE_SORT_KEY_TPL_LASTMODIFIED => 'tpl_lastmodified',
		TPLFILE_SORT_KEY_TPL_LASTIMPORTED => 'tpl_lastimported',
		TPLFILE_SORT_KEY_TPL_TYPE => 'tpl_type'
	);
	
	var $mTplset = null;
	var $mModule = null;

	function getDefaultSortKey()
	{
		return TPLFILE_SORT_KEY_DEFAULT;
	}
	
	function fetch()
	{
		parent::fetch();
		$this->additionalFetch();
	}
	
	function additionalFetch()
	{
		if (isset($_REQUEST['tpl_module'])) {
			$this->mNavi->addExtra('tpl_module', xoops_getrequest('tpl_module'));
			$this->_mCriteria->add(new Criteria('tpl_module', array(XOBJ_DTYPE_STRING, xoops_getrequest('tpl_module'))));
			
			$handler =& xoops_gethandler('module');
			$this->mModule =& $handler->getByDirname(xoops_getrequest('tpl_module'));
		}
	
		if (isset($_REQUEST['tpl_type'])) {
			$this->mNavi->addExtra('tpl_type', xoops_getrequest('tpl_type'));
			$this->_mCriteria->add(new Criteria('tpl_type', array(XOBJ_DTYPE_STRING, xoops_getrequest('tpl_type'))));
		}
		
		if (isset($_REQUEST['tpl_file'])) {
			$this->mNavi->addExtra('tpl_file', xoops_getrequest('tpl_file'));
			$this->_mCriteria->add(new Criteria('tpl_file', '%' . xoops_getrequest('tpl_file') . '%', 'LIKE'));
		}

		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
