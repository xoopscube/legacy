<?php
/**
 *
 * @package Legacy
 * @version $Id: commentstatus.php,v 1.3 2008/09/25 15:11:24 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';

class LegacyCommentstatusObject extends XoopsSimpleObject
{
	function LegacyCommentstatusObject()
	{
		$this->initVar('id', XOBJ_DTYPE_INT, '', true);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', true, 255);
	}
}

class LegacyCommentstatusHandler extends XoopsObjectHandler
{
	var $_mResults = array();
	
	function LegacyCommentstatusHandler(&$db)
	{
		$root =& XCube_Root::getSingleton();
		$language = $root->mContext->getXoopsConfig('language');
		$root->mLanguageManager->loadPageTypeMessageCatalog('comment');

		$this->_mResults[XOOPS_COMMENT_PENDING] =& $this->create();
		$this->_mResults[XOOPS_COMMENT_PENDING]->setVar('id', XOOPS_COMMENT_PENDING);
		$this->_mResults[XOOPS_COMMENT_PENDING]->setVar('name', _CM_PENDING);
		
		$this->_mResults[XOOPS_COMMENT_ACTIVE] =& $this->create();
		$this->_mResults[XOOPS_COMMENT_ACTIVE]->setVar('id', XOOPS_COMMENT_ACTIVE);
		$this->_mResults[XOOPS_COMMENT_ACTIVE]->setVar('name', _CM_ACTIVE);

		$this->_mResults[XOOPS_COMMENT_HIDDEN] =& $this->create();
		$this->_mResults[XOOPS_COMMENT_HIDDEN]->setVar('id', XOOPS_COMMENT_HIDDEN);
		$this->_mResults[XOOPS_COMMENT_HIDDEN]->setVar('name', _CM_HIDDEN);
	}
	
	function &create()
	{
		$ret =new LegacyCommentstatusObject();
		return $ret;
	}
	
	function &get($id)
	{
		if (isset($this->_mResults[$id])) {
			return $this->_mResults[$id];
		}
		
		$ret = null;
		return $ret;
	}
	
	function &getObjects($criteria = null, $id_as_key = false)
	{
		if ($id_as_key) {
			return $this->_mResults;
		}
		else {
			$ret = array();
		
			foreach (array_keys($this->_mResults) as $key) {
				$ret[] =& $this->_mResults[$key];
			}
			
			return $ret;
		}
	}
	
	function insert(&$obj)
	{
		return false;
	}

	function delete(&$obj)
	{
		return false;
	}
}

?>
