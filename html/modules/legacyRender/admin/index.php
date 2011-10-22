<?php
/**
 * @package legacyRender
 * @version $Id: index.php,v 1.1 2007/05/15 02:35:26 minahito Exp $
 */

require_once "../../../mainfile.php";
require_once XOOPS_ROOT_PATH . "/header.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/class/ActionFrame.class.php";

$root =& XCube_Root::getSingleton();

$actionName = isset($_GET['action']) ? trim($_GET['action']) : "TplsetList";

$moduleRunner =new LegacyRender_ActionFrame(true);
$moduleRunner->setActionName($actionName);

$root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

$root->mController->execute();

require_once XOOPS_ROOT_PATH . "/footer.php";

?>
