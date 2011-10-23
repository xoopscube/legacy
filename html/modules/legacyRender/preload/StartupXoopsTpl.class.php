<?php
/**
 * @package legacyRender
 * @version $Id: StartupXoopsTpl.class.php,v 1.2 2007/06/07 05:26:13 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * @internal
 * Some XOOPS2 resources need $xoopsTpl, before Legacy_RenderSystem will be
 * prepared under XOOPS Cube regular process. For that, this action filter
 * tries to get 'Legacy_RenderSystem' as dummy.
 */
class LegacyRender_StartupXoopsTpl extends XCube_ActionFilter
{
	function postFilter()
	{
		$dmy =& $this->mRoot->getRenderSystem('Legacy_RenderSystem');
	}
}

?>
