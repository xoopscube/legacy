<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * @package    Altsys
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

if ( file_exists( __DIR__ . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/modinfo.php' ) ) {
	include_once __DIR__ . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/modinfo.php';
} elseif ( file_exists( __DIR__ . '/language/english/modinfo.php' ) ) {
	include_once __DIR__ . '/language/english/modinfo.php';
}

$controllers = [
	'myblocksadmin',
	'compilehookadmin',
	'get_templates',
	'get_tplsvarsinfo',
	'mypreferences',
	'mytplsadmin',
	'mytplsform',
	'put_templates',
	'mylangadmin',
];
