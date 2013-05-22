<?php
/**
 * @file
 * @brief The page controller in the directory
 * @package xupdate
 * @version $Id$
**/

// for tag update on GET request @todo more smart
if (preg_match('/action=(?:ModuleView|ModuleStore|ThemeStore)/', $_SERVER['QUERY_STRING'])) {
	$_SERVER['REQUEST_METHOD'] = 'POST';
}

require_once '../../../mainfile.php';

require_once XOOPS_TRUST_PATH . '/modules/xupdate/admin/index.php';

?>
