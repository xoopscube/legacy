<?php
/**
 * @package XUpgrade
 * @version $Id: Updater.class.php,v 1.1 2007/05/15 02:35:21 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/modules/XUpgrade/admin/class/UpgradeProcessor.class.php";

class XUpgrade_Updater extends Legacy_ModulePhasedUpgrader
{
	function _processScript()
	{
		$procedure =& new XUpgrade_UpgradeProcessor(false);
		$procedure->execute($this->_mTargetXoopsModule, $this->mLog);
	}
}

?>