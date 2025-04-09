<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

echo '<h2>wizard/install_updateConfig.inc</h2>';

$b_next = [ 'updateConfig_go', _INSTALL_L144 ];
$content = '<p>' . _INSTALL_L143 . '</p>';

include './install_tpl.php';
