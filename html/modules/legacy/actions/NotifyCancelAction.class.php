<?php
/**
 *
 * @package Legacy
 * @version $Id: NotifyCancelAction.class.php,v 1.3 2008/09/25 15:12:03 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_NotifyCancelAction extends Legacy_Action
{
	function getDefaultView(&$contoller, &$xoopsUser)
	{
		$contoller->executeForward(XOOPS_URL . '/');
	}

	function execute(&$contoller, &$xoopsUser)
	{
		$contoller->executeForward(XOOPS_URL . '/');
	}
}

?>
