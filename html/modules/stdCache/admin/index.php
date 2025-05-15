<?php
/**
 * Standard cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8 
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/class/ActionFrame.class.php';

$root =& XCube_Root::getSingleton();
$controller = $root->mController;

// Module Admin Default 
$actionName = isset($_GET['action']) ? trim($_GET['action']) : 'CacheStats';

// Remove 'Action' suffix if present
if (substr($actionName, -6) === 'Action') {
    $actionName = substr($actionName, 0, -6);
}

$moduleRunner = new stdCache_ActionFrame(true);
$moduleRunner->setActionName($actionName);

$controller->mExecute->add([&$moduleRunner, 'execute']);

$controller->execute();

require_once XOOPS_ROOT_PATH . '/footer.php';
