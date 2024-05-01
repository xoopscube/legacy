<?php
/**
 * D3Forum module for XCL
 *
 * @package XCL
 * @subpackage D3Forum
 * @version 2.3
 * @author Gijoe (Peak), Gigamaster (XCL)
 * @copyright  (c) 2005-2024 Authors
 * @license GPL v2.0
 */

if ( empty( $_POST['body_editor'] ) ) {

	$body_editor = @$xoopsModuleConfig['body_editor'];

} else {

	$body_editor = $_POST['body_editor'];

	// normal (xoopsdhtmltarea)
	$d3forum_wysiwyg_body = '';

	$d3forum_wysiwyg_header = '';

}
