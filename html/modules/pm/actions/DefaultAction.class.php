<?php
/**
 * @package pm
 * @version $Id: DefaultAction.class.php,v 1.3 2007/10/29 09:39:30 tom_g3x Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_PageNavigator.class.php";
require_once XOOPS_MODULE_PATH . "/pm/forms/PmDeleteForm.class.php";

/***
 * @internal
 * [Notice]
 * This class has been checked in Alpha4. But, this class doesn't go along the
 * latest cubson style. And some problems (using core handler, naming rule and
 * etc) are there. Pm module is one of the most old code in Legacy.
 * 
 * [ToDo]
 * This class should use Action_FilterForm as well as other latest modules.
 */
class Pm_DefaultAction extends Pm_AbstractAction
{
	var $mActionForm = null;
	
	var $mPmObjects = array();
	
	var $mPageNavi = null;
	
	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
		$this->mActionForm =& new Pm_PmDeleteForm();
		$this->mActionForm->prepare();
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$pmHandler =& xoops_gethandler('privmessage');
		$total = $pmHandler->getCountByFromUid($xoopsUser->uid());

		$this->mPageNavi =& new XCube_PageNavigator(XOOPS_URL . "/viewpmsg.php", XCUBE_PAGENAVI_START);
		$this->mPageNavi->setTotalItems($total);
		$this->mPageNavi->fetch();

		$this->mPmObjects =& $pmHandler->getObjectsByFromUid($xoopsUser->uid(), $this->mPageNavi->getStart());

		return PM_FRAME_VIEW_INDEX;
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("viewpmsg.html");
		$render->setAttribute("pmObjects", $this->mPmObjects);
		$render->setAttribute("total_messages", count($this->mPmObjects));
		$render->setAttribute("currentUser", $xoopsUser);
		$render->setAttribute("anonymous", $controller->mRoot->mContext->getXoopsConfig('anonymous'));
		$render->setAttribute("pageNavi", $this->mPageNavi);
		$render->setAttribute("actionForm", $this->mActionForm);
	}
}

?>