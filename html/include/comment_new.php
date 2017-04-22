<?php
// $Id: comment_new.php,v 1.2 2008/03/14 16:28:37 minahito Exp $
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

require_once XOOPS_MODULE_PATH . "/legacy/forms/CommentEditForm.class.php";

//
// Load message resource
//
$t_root =& XCube_Root::getSingleton();

$t_root->mLanguageManager->loadModuleMessageCatalog("legacy");


require_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
if ('system' != $xoopsModule->getVar('dirname') && XOOPS_COMMENT_APPROVENONE == $xoopsModuleConfig['com_rule']) {
    exit();
}

$t_root->mLanguageManager->loadPageTypeMessageCatalog('comment');    ///< Is this must?

$com_itemid = isset($_GET['com_itemid']) ? (int)$_GET['com_itemid'] : 0;

if ($com_itemid > 0) {
    include XOOPS_ROOT_PATH.'/header.php';
    if (isset($com_replytitle)) {
        if (isset($com_replytext)) {
            themecenterposts($com_replytitle, $com_replytext);
        }
        $myts =& MyTextSanitizer::sGetInstance();
        $com_title = $myts->htmlSpecialChars($com_replytitle);
        if (!preg_match("/^re:/i", $com_title)) {
            $com_title = "Re: ".xoops_substr($com_title, 0, 56);
        }
    } else {
        $com_title = '';
    }
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
    $noname = 0;

    $handler =& xoops_gethandler('comment');
    $comment =& $handler->create();

//
// Initialize manually.
//
$comment->set("com_itemid", $com_itemid);
    $comment->set("com_modid", $xoopsModule->get('mid'));
    $comment->set("com_title", $com_title);

    if (is_object($xoopsUser)) {
        $comment->set('uid', $xoopsUser->get('uid'));
    } else {
        $comment->set('uid', 0);
    }

//
// Create action form instance and load from a comment object.
//
if (is_object($xoopsUser) && $xoopsUser->isAdmin()) {
    $actionForm =new Legacy_CommentEditForm_Admin();
} else {
    $actionForm =new Legacy_CommentEditForm();
}
    $actionForm->prepare();
    $actionForm->load($comment);

//
// Get the icons of subject.
//
$handler =& xoops_gethandler('subjecticon');
    $subjectIcons =& $handler->getObjects();

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

    $extraParams = array();
    if ('system' != $xoopsModule->get('dirname')) {
        $comment_config = $xoopsModule->getInfo('comments');
        if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
            foreach ($comment_config['extraParams'] as $extra_param) {
                $extraParams[$extra_param] = xoops_getrequest($extra_param);
            }
        }
    }

    $renderTarget->setAttribute('extraParams', $extraParams);

//
// Rendering
//
$renderSystem->render($renderTarget);

//
// Display now.
//
print $renderTarget->getResult();

    require_once XOOPS_ROOT_PATH . "/footer.php";
}
