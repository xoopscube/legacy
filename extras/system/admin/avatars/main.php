<?php
// $Id: main.php,v 1.1 2007/05/15 02:34:44 minahito Exp $
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
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if ( !is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid()) ) {
    exit("Access Denied");
} else {
    $op = 'list';
    if (isset($_GET['op'])) {
        $op = trim($_GET['op']);
    } elseif (isset($_POST['op'])) {
        $op = trim($_POST['op']);
    }
    if ($op == 'list') {
        xoops_cp_header();
        echo '<h4 style="text-align:left">'._MD_AVATARMAN.'</h4>';
        $avt_handler =& xoops_gethandler('avatar');
        $savatar_count = $avt_handler->getCount(new Criteria('avatar_type', 'S'));
        $cavatar_count = $avt_handler->getCount(new Criteria('avatar_type', 'C'));
        echo '<ul><li>'._MD_SYSAVATARS.' ('.sprintf(_NUMIMAGES, '<b>'.$savatar_count.'</b>').') [<a href="admin.php?fct=avatars&amp;op=listavt&amp;type=S">'._LIST.'</a>]</li><li>'._MD_CSTAVATARS.' ('.sprintf(_NUMIMAGES, '<b>'.$cavatar_count.'</b>').') [<a href="admin.php?fct=avatars&amp;op=listavt&amp;type=C">'._LIST.'</a>]</li></ul>';
        include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
        $form = new XoopsThemeForm(_MD_ADDAVT, 'avatar_form', 'admin.php');
        $form->setExtra('enctype="multipart/form-data"');
        $form->addElement(new XoopsFormToken(XoopsMultiTokenHandler::quickCreate('avatars_addfile')));
        $form->addElement(new XoopsFormText(_IMAGENAME, 'avatar_name', 50, 255), true);
        $form->addElement(new XoopsFormFile(_IMAGEFILE, 'avatar_file', 500000));
        $form->addElement(new XoopsFormText(_IMGWEIGHT, 'avatar_weight', 3, 4, 0));
        $form->addElement(new XoopsFormRadioYN(_IMGDISPLAY, 'avatar_display', 1, _YES, _NO));
        $form->addElement(new XoopsFormHidden('op', 'addfile'));
        $form->addElement(new XoopsFormHidden('fct', 'avatars'));
        $form->addElement(new XoopsFormButton('', 'avt_button', _SUBMIT, 'submit'));
        $form->display();
        xoops_cp_footer();
        exit();
    }

    if ($op == 'listavt') {
        $avt_handler =& xoops_gethandler('avatar');
        xoops_cp_header();
        $type = (isset($_GET['type']) && $_GET['type'] == 'C') ? 'C' : 'S';
        echo '<a href="admin.php?fct=avatars">'. _MD_AVATARMAN .'</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;';
        if ($type == 'S') {
            echo _MD_SYSAVATARS;
        } else {
            echo _MD_CSTAVATARS;
        }
        echo '<br /><br />';
        $criteria = new Criteria('avatar_type', $type);
        $avtcount = $avt_handler->getCount($criteria);
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $criteria->setStart($start);
        $criteria->setLimit(10);
        $avatars =& $avt_handler->getObjects($criteria, true);

        $token =& XoopsMultiTokenHandler::quickCreate('avatars_save');

        if ($type == 'S') {
            foreach (array_keys($avatars) as $i) {
                echo '<form action="admin.php" method="post">';
                echo $token->getHtml();
                $id = $avatars[$i]->getVar('avatar_id');
                echo '<table class="outer" cellspacing="1" width="100%"><tr><td align="center" width="30%" rowspan="6"><img src="'.XOOPS_UPLOAD_URL.'/'.$avatars[$i]->getVar('avatar_file').'" alt="" /></td><td class="head">'._IMAGENAME,'</td><td class="even"><input type="hidden" name="avatar_id[]" value="'.$id.'" /><input type="text" name="avatar_name[]" value="'.$avatars[$i]->getVar('avatar_name', 'E').'" size="20" maxlength="255" /></td></tr><tr><td class="head">'._IMAGEMIME.'</td><td class="odd">'.$avatars[$i]->getVar('avatar_mimetype').'</td></tr><tr><td class="head">'._MD_USERS.'</td><td class="even">'.$avatars[$i]->getUserCount().'</td></tr><tr><td class="head">'._IMGWEIGHT.'</td><td class="odd"><input type="text" name="avatar_weight[]" value="'.$avatars[$i]->getVar('avatar_weight').'" size="3" maxlength="4" /></td></tr><tr><td class="head">'._IMGDISPLAY.'</td><td class="even"><input type="checkbox" name="avatar_display[]" value="1"';
                if ($avatars[$i]->getVar('avatar_display') == 1) {
                    echo ' checked="checked"';
                }
                echo ' /></td></tr><tr><td class="head">&nbsp;</td><td class="even"><a href="admin.php?fct=avatars&amp;op=delfile&amp;avatar_id='.$id.'">'._DELETE.'</a></td></tr></table><br />';
            }
        } else {
            foreach (array_keys($avatars) as $i) {
                echo '<table cellspacing="1" class="outer" width="100%"><tr><td width="30%" rowspan="6" align="center"><img src="'.XOOPS_UPLOAD_URL.'/'.$avatars[$i]->getVar('avatar_file').'" alt="" /></td><td class="head">'._IMAGENAME,'</td><td class="even"><a href="'.XOOPS_URL.'/userinfo.php?uid=';
                $userids =& $avt_handler->getUser($avatars[$i]);
                echo $userids[0].'">'.$avatars[$i]->getVar('avatar_name').'</a></td></tr><tr><td class="head">'._IMAGEMIME.'</td><td class="odd">'.$avatars[$i]->getVar('avatar_mimetype').'</td></tr><tr><td class="head">&nbsp;</td><td align="center" class="even"><a href="admin.php?fct=avatars&amp;op=delfile&amp;avatar_id='.$avatars[$i]->getVar('avatar_id').'&amp;user_id='.$userids[0].'">'._DELETE.'</a></td></tr></table><br />';
            }
        }
        if ($avtcount > 0) {
            if ($avtcount > 10) {
                include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
                $nav = new XoopsPageNav($avtcount, 10, $start, 'start', 'fct=avatars&amp;type='.$type.'&amp;op=listavt');
                echo '<div style="text-align:right;">'.$nav->renderImageNav().'</div>';
            }
            if ($type == 'S') {
                echo '<div style="text-align:center;"><input type="hidden" name="op" value="save" /><input type="hidden" name="fct" value="avatars" /><input type="submit" name="submit" value="'._SUBMIT.'" /></div></form>';
            }
        }
        xoops_cp_footer();
        exit();
    }

    if ($op == 'save') {
        if(!XoopsMultiTokenHandler::quickValidate('avatars_save')) {
            xoops_cp_header();
            xoops_error('Ticket Error');
            xoops_cp_footer();
            exit();
        }

        $count = count($_POST['avatar_id']);
        if ($count > 0) {
            $avt_handler =& xoops_gethandler('avatar');
            $error = array();
            for ($i = 0; $i < $count; $i++) {
                $avatar =& $avt_handler->get($_POST['avatar_id'][$i]);
                if (!is_object($avatar)) {
                    $error[] = sprintf(_FAILGETIMG, $_POST['avatar_id'][$i]);
                    continue;
                }
                $avatar_display[$i] = empty($_POST['avatar_display'][$i]) ? 0 : 1;
                $avatar->setVar('avatar_display', $avatar_display[$i]);
                $avatar->setVar('avatar_weight', $_POST['avatar_weight'][$i]);
                $avatar->setVar('avatar_name', $_POST['avatar_name'][$i]);
                if (!$avt_handler->insert($avatar)) {
                    $error[] = sprintf(_FAILSAVEIMG, $_POST['avatar_id'][$i]);
                }
                unset($avatar_id[$i]);
                unset($avatar_name[$i]);
                unset($avatar_weight[$i]);
                unset($avatar_display[$i]);
            }
            if (count($error) > 0) {
                xoops_cp_header();
                foreach ($error as $err) {
                    echo $err.'<br />';
                }
                xoops_cp_footer();
                exit();
            }
        }
        redirect_header('admin.php?fct=avatars',2,_MD_AM_DBUPDATED);
    }

    if ($op == 'addfile') {
        if(!XoopsMultiTokenHandler::quickValidate('avatars_addfile')) {
            xoops_cp_header();
            xoops_error('Ticket Error');
            xoops_cp_footer();
            exit();
        }

        include_once XOOPS_ROOT_PATH.'/class/uploader.php';
        $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png'), 500000);
        $uploader->setAllowedExtensions(array('gif', 'jpeg', 'jpg', 'png'));
        $uploader->setPrefix('savt');
        $err = array();
        $ucount = count($_POST['xoops_upload_file']);
        for ($i = 0; $i < $ucount; $i++) {
            if ($uploader->fetchMedia($_POST['xoops_upload_file'][$i])) {
                if (!$uploader->upload()) {
                    $err[] = $uploader->getErrors();
                } else {
                    $avt_handler =& xoops_gethandler('avatar');
                    $avatar =& $avt_handler->create();
                    $avatar->setVar('avatar_file', $uploader->getSavedFileName());
                    $avatar->setVar('avatar_name', $_POST['avatar_name']);
                    $avatar->setVar('avatar_mimetype', $uploader->getMediaType());
                    $avatar_display = empty($_POST['avatar_display']) ? 0 : 1;
                    $avatar->setVar('avatar_display', $avatar_display);
                    $avatar->setVar('avatar_weight', $_POST['avatar_weight']);
                    $avatar->setVar('avatar_type', 'S');
                    if (!$avt_handler->insert($avatar)) {
                        $err[] = sprintf(_FAILSAVEIMG, $avatar->getVar('avatar_name'));
                    }
                }
            } else {
                $err[] = sprintf(_FAILFETCHIMG, $i);
            }
        }
        if (count($err) > 0) {
            xoops_cp_header();
            xoops_error($err);
            xoops_cp_footer();
            exit();
        }
        redirect_header('admin.php?fct=avatars',2,_MD_AM_DBUPDATED);
    }

    if ($op == 'delfile') {
        xoops_cp_header();
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        xoops_token_confirm(array('op' => 'delfileok', 'avatar_id' => intval($_GET['avatar_id']), 'fct' => 'avatars', 'user_id' => $user_id), 'admin.php', _MD_RUDELIMG);
        xoops_cp_footer();
        exit();
    }

    if ($op == 'delfileok') {
        if(!xoops_confirm_validate()) {
            xoops_cp_header();
            xoops_error("Ticket Error");
            xoops_cp_footer();
            exit();
        }

        $avatar_id = intval($_POST['avatar_id']);
        if ($avatar_id <= 0) {
            redirect_header('admin.php?fct=avatars',1);
        }
        $avt_handler = xoops_gethandler('avatar');
        $avatar =& $avt_handler->get($avatar_id);
        if (!is_object($avatar)) {
            redirect_header('admin.php?fct=avatars',1);
        }
        if (!$avt_handler->delete($avatar)) {
            xoops_cp_header();
            xoops_error(sprintf(_MD_FAILDEL, $avatar->getVar('avatar_id')));
            xoops_cp_footer();
            exit();
        }
        $file = $avatar->getVar('avatar_file');
        @unlink(XOOPS_UPLOAD_PATH.'/'.$file);
        if (isset($user_id) && $avatar->getVar('avatar_type') == 'C') {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix('users')." SET user_avatar='blank.gif' WHERE uid=".intval($user_id));
        } else {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix('users')." SET user_avatar='blank.gif' WHERE user_avatar='".$file."'");
        }
        redirect_header('admin.php?fct=avatars',2,_MD_AM_DBUPDATED);
    }
}
?>