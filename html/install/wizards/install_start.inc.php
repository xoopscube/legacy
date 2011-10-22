<?php
/**
 *
 * @package Legacy
 * @version $Id: install_start.inc.php,v 1.3 2008/09/25 15:12:31 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
	include './language/'.$language.'/welcome.php'; //This will set message to $content;
	$error = false;
 	if(substr(PHP_VERSION, 0, 1) < 5) {
 		$error = true;
 	}
	if(! $error) {
		$wizard->assign('welcome', $content);
	}else{
		$wizard->assign('message',_INSTALL_L168);
		$wizard->setReload(true);
	}
	$wizard->render('install_start.tpl.php');

?>
