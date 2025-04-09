<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

echo '<h2>wizard/install_updateModules.inc</h2>';

$b_next  = [ 'updateModules_go', _INSTALL_L137 ];
$content = '<p>' . _INSTALL_L141 . "</p>\n";

include './install_tpl.php';
