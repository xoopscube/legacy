<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

include './language/' . $language . '/finish.php'; //This will set message to $content;

$wizard->assign( 'finish', $content );
$wizard->render( 'install_finish.tpl.php' );
