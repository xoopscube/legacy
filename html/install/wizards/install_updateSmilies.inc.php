<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.3
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

echo '<h2>wizard/install_updateSmilies.inc</h2>';

$content = '<p>' . _INSTALL_L150 . '</p>';
$b_next = [ 'updateSmilies_go', _INSTALL_L140 ];

include './install_tpl.php';
