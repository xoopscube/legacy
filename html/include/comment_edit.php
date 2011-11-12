<?php
// $Id: comment_edit.php,v 1.1 2007/05/15 02:34:18 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.xoops.org/ http://jp.xoops.org/  http://www.myweb.ne.jp/  //
// Project: The XOOPS Project (http://www.xoops.org/)                        //
// ------------------------------------------------------------------------- //

//
// Guard directly access.
//
if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
	exit();
}
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . "/include/comment_constants.php";

require_once XOOPS_MODULE_PATH . "/legacy/forms/CommentEditForm.class.php";

//
// Load message resource
//
$t_root =& XCube_Root::getSingleton();

$langManager =& $t_root->getLanguageManager();
$langManager->loadModuleMessageCatalog("legacy");

if ('system' != $xoopsModule->getVar('dirname') && XOOPS_COMMENT_APPROVENONE == $xoopsModuleConfig['com_rule']) {
	exit();
}

$t_root->mLanguageManager->loadPageTypeMessageCatalog('comment');

$com_id = isset($_GET['com_id']) ? (int)$_GET['com_id'] : 0;
$com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';
if ($com_mode == '') {
	if (is_object($xoopsUser)) {
		$com_mode = $xoopsUser->getVar('umode');
	} else {
		$com_mode = $xoopsConfig['com_mode'];
	}
}
if (!isset($_GET['com_order'])) {
	if (is_object($xoopsUser)) {
		$com_order = $xoopsUser->getVar('uorder');
	} else {
		$com_order = $xoopsConfig['com_order'];
	}
} else {
	$com_order = (int)$_GET['com_order'];
}
$comment_handler =& xoops_gethandler('comment');
$comment =& $comment_handler->get($com_id);
$dohtml = $comment->getVar('dohtml');
$dosmiley = $comment->getVar('dosmiley');
$dobr = $comment->getVar('dobr');
$doxcode = $comment->getVar('doxcode');
$com_icon = $comment->getVar('com_icon');
$com_itemid = $comment->getVar('com_itemid');
$com_title = $comment->getVar('com_title', 'E');
$com_text = $comment->getVar('com_text', 'E');
$com_pid = $comment->getVar('com_pid');
$com_status = $comment->getVar('com_status');
$com_rootid = $comment->getVar('com_rootid');

//
// Get the icons of subject.
//
$handler =& xoops_gethandler('subjecticon');
$subjectIcons =& $handler->getObjects();

if ($xoopsModule->getVar('dirname') != 'system') {
	if (is_object($xoopsUser) && $xoopsUser->isAdmin()) {
		$actionForm =new Legacy_CommentEditForm_Admin();
	}
	else {
		$actionForm =new Legacy_CommentEditForm();
	}
	$actionForm->prepare();
	$actionForm->load($comment);

	//
	// Render comment-form to render buffer with using Legacy_RenderSystem.
	//
	$renderSystem =& $t_root->getRenderSystem($t_root->mContext->mBaseRenderSystemName);
	$renderTarget =& $renderSystem->createRenderTarget('main');
	
	$renderTarget->setTemplateName("legacy_comment_edit.html");

	$renderTarget->setAttribute("actionForm", $actionForm);
	$renderTarget->setAttribute("subjectIcons", $subjectIcons);
	$renderTarget->setAttribute("xoopsModuleConfig", $xoopsModuleConfig);
	$renderTarget->setAttribute("com_order", $com_order);
	
	//
	// Rendering
	//
	$renderSystem->render($renderTarget);

	//
	// Display now.
	//
	print $renderTarget->getResult();

	require_once XOOPS_ROOT_PATH.'/footer.php';
} else {
	//
	// TODO
	//
	xoops_cp_header();
	require_once XOOPS_ROOT_PATH.'/include/comment_form.php';
	xoops_cp_footer();
}
?>
