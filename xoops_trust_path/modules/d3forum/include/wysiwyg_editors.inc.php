<?php
/**
 * D3Forum module for XCL
 *
 * @package XCL
 * @subpackage D3Forum
 * @version 2.3
 * @author Gijoe (Peak), Gigamaster (XCL)
 * @copyright  (c) 2005-2022 Author
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 */

if ( empty( $_POST['body_editor'] ) ) {

	$body_editor = @$xoopsModuleConfig['body_editor'];

} else {

	$body_editor = $_POST['body_editor'];

	// normal (xoopsdhtmltarea)
	$d3forum_wysiwyg_body = '';

	$d3forum_wysiwyg_header = '';

}
