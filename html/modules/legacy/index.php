<?php
/**
 *
 * @package Legacy
 * @version $Id: index.php,v 1.3 2008/09/25 14:31:43 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

require_once "../../mainfile.php";
require_once XOOPS_ROOT_PATH . "/header.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/ActionFrame.class.php";

$root =& XCube_Root::getSingleton();

$actionName = isset($_GET['action']) ? trim($_GET['action']) : "Default";

$moduleRunner =new Legacy_ActionFrame(false);
$moduleRunner->setActionName($actionName);

$root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

$root->mController->execute();

require_once XOOPS_ROOT_PATH . "/footer.php";

?>
