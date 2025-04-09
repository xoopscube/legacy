<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */
echo '<h2>wizard/install_updateComments.inc</h2>';
$content = '<p>' . _INSTALL_L149 . "</p>";
$b_next  = [ 'updateComments_go', _INSTALL_L138 ];

include './install_tpl.php';
