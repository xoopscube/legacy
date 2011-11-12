<?php
/**
 *
 * @package Legacy
 * @version $Id: NotifyDeleteForm.class.php,v 1.3 2008/09/25 15:12:39 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Legacy_NotifyDeleteForm extends XCube_ActionForm
{
	var $mNotifiyIds = array();
	var $mFatalError = false;
	
	function getTokenName()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST' ? "module.legacy.NotifyDeleteForm.TOKEN" : null;
	}

	function fetch()
	{
		parent::fetch();
		
		$root =& XCube_Root::getSingleton();
		$t_arr = $root->mContext->mRequest->getRequest('del_not');
		
		if (!is_array($t_arr)) {
			$this->addErrorMessage(_MD_LEGACY_LANG_ERROR);
			$this->mFatalError = true;
			return;
		}
		
		foreach ($t_arr as $t_modid => $t_idArr) {
			if (!is_array($t_idArr)) {
				$this->addErrorMessage(_MD_LEGACY_LANG_ERROR);
				$this->mFatalError = true;
				return;
			}
			foreach ($t_idArr as $t_id) {
				$this->mNotifiyIds[] = array('modid' => intval($t_modid), 'id' => intval($t_id));
			}
		}
	}
}

?>
