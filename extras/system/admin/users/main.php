<?php
// $Id: main.php,v 1.1 2007/05/15 02:34:54 minahito Exp $
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
}
$op = 'mod_users';
include_once XOOPS_ROOT_PATH."/modules/system/admin/users/users.php";
if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
    if (isset($_GET['uid'])) {
        $uid = intval($_GET['uid']);
    }
} elseif (!empty($_POST['op'])) {
    $op = $_POST['op'];
}
switch ($op) {

case "modifyUser":
    modifyUser($uid);
    break;

case "updateUser":
    if(!XoopsMultiTokenHandler::quickValidate('users_updateUser'))
        system_users_error("Ticket Error");

    $myts =& MyTextSanitizer::getInstance();
    $uid = !empty($_POST['uid']) ? intval($_POST['uid']) : 0;
    $username = !empty($_POST['username']) ? $myts->stripSlashesGPC(trim($_POST['username'])) : '';
    if ($uid > 0 && $username != '') {
        $member_handler =& xoops_gethandler('member');
        $edituser =& $member_handler->getUser($uid);
        $myts =& MyTextSanitizer::getInstance();
        if ($edituser->getVar('uname', 'n') != $username && $member_handler->getUserCount(new Criteria('uname', addslashes($username))) > 0) {
            xoops_cp_header();
            echo 'User name '.htmlspecialchars($username).' already exists';
            xoops_cp_footer();
            exit();
        } else {
            $edituser->setVar("name", $_POST['name']);
            $edituser->setVar("uname", $_POST['username']);
            $edituser->setVar("email", $_POST['email']);
            if (!empty($_POST['url'])) {
                $edituser->setVar("url", formatURL($_POST['url']));
            }
        //  $edituser->setVar("user_avatar", $_POST['user_avatar']);
            $edituser->setVar("user_icq", $_POST['user_icq']);
            $edituser->setVar("user_from", $_POST['user_from']);
            $edituser->setVar("user_sig", $_POST['user_sig']);
            $user_viewemail = !empty($_POST['user_viewemail']) ? 1 : 0;
            $edituser->setVar("user_viewemail", $user_viewemail);
            $edituser->setVar("user_aim", $_POST['user_aim']);
            $edituser->setVar("user_yim", $_POST['user_yim']);
            $edituser->setVar("user_msnm", $_POST['user_msnm']);
            $edituser->setVar("attachsig", (empty($_POST['attachsig']) ? 0 : 1));
            $edituser->setVar("timezone_offset", $_POST['timezone_offset']);
        //  $edituser->setVar("theme", $_POST['theme']);
            $edituser->setVar("uorder", $_POST['uorder']);
            $edituser->setVar("umode", $_POST['umode']);
            $edituser->setVar("notify_method", $_POST['notify_method']);
            $edituser->setVar("notify_mode", $_POST['notify_mode']);
            $edituser->setVar("bio", $_POST['bio']);
            $edituser->setVar("rank", $_POST['rank']);
            $edituser->setVar("user_occ", $_POST['user_occ']);
            $edituser->setVar("user_intrest", $_POST['user_intrest']);
            $edituser->setVar('user_mailok', $_POST['user_mailok']);
            if ($_POST['pass2'] != "") {
                if ( $_POST['pass'] != $_POST['pass2'] ) {
                    xoops_cp_header();
                    echo "<b>"._AM_STNPDNM."</b>";
                    xoops_cp_footer();
                    exit();
                }
                $edituser->setVar("pass", md5($_POST['pass']));
            }
            if (!$member_handler->insertUser($edituser)) {
                xoops_cp_header();
                echo $edituser->getHtmlErrors();
                xoops_cp_footer();
                exit();
            } else {
                if (!empty($_POST['groups'])) {
                    $oldgroups = $edituser->getGroups();
                    //If the edited user is the current user and the current user WAS in the webmaster's group and is NOT in the new groups array
                    if ($edituser->getVar('uid') == $xoopsUser->getVar('uid') && (in_array(XOOPS_GROUP_ADMIN, $oldgroups)) && !in_array(XOOPS_GROUP_ADMIN, $groups)) {
                        //Add the webmaster's group to the groups array to prevent accidentally removing oneself from the webmaster's group
                        array_push($_POST['groups'], XOOPS_GROUP_ADMIN);
                    }
                    $member_handler =& xoops_gethandler('member');
                    foreach ($oldgroups as $groupid) {
                        $member_handler->removeUsersFromGroup($groupid, array($edituser->getVar('uid')));
                    }
                    foreach ($_POST['groups'] as $groupid) {
                        $member_handler->addUserToGroup($groupid, $edituser->getVar('uid'));
                    }
                }
            }
        }
    }
    redirect_header("admin.php?fct=users",1,_AM_DBUPDATED);
    break;

case "delUser":
    xoops_cp_header();
    $member_handler =& xoops_gethandler('member');
    $userdata =& $member_handler->getUser($uid);
    xoops_token_confirm(array('fct' => 'users', 'op' => 'delUserConf', 'del_uid' => $userdata->getVar('uid')), 'admin.php', sprintf(_AM_AYSYWTDU,$userdata->getVar('uname')));
    xoops_cp_footer();
    break;
case "delete_many":
    xoops_cp_header();
    $count = count($_POST['memberslist_id']);

    $token=&XoopsSingleTokenHandler::quickCreate('users_deletemany');

    if ( $count > 0 ) {
        $list = $hidden = '';
        for ( $i = 0; $i < $count; $i++ ) {
            $id = intval($_POST['memberslist_id'][$i]);
            if ($id > 0) {
                $list .= ", <a href='".XOOPS_URL."/userinfo.php?uid=$id' rel='external'>".htmlspecialchars($_POST['memberslist_uname'][$id])."</a>";
                $hidden .= "<input type='hidden' name='memberslist_id[]' value='$id' />\n";
            }
        }
        echo "<div><h4>".sprintf(_AM_AYSYWTDU," ".$list." ")."</h4>";
        echo _AM_BYTHIS."<br /><br />
        <form action='admin.php' method='post'>
        <input type='hidden' name='fct' value='users' />
        <input type='hidden' name='op' value='delete_many_ok' />
        <input type='submit' value='"._YES."' />
        <input type='button' value='"._NO."' onclick='javascript:location.href=\"admin.php?op=adminMain\"' />";
        echo $token->getHtml();
        echo $hidden;
        echo "</form></div>";
    } else {
        echo _AM_NOUSERS;
    }
    xoops_cp_footer();
    break;
case "delete_many_ok":
    if(XoopsSingleTokenHandler::quickValidate('users_deletemany')) {
        $count = count($_POST['memberslist_id']);
        $output = "";
        $member_handler =& xoops_gethandler('member');
        for ( $i = 0; $i < $count; $i++ ) {
            $deluser =& $member_handler->getUser($_POST['memberslist_id'][$i]);
            if (is_object($deluser)) {
                $groups = $deluser->getGroups();
                if (in_array(XOOPS_GROUP_ADMIN, $groups)) {
                    $output .= sprintf('Admin user cannot be deleted. (User: %s)', $deluser->getVar("uname"))."<br />";
                } else {
                    if (!$member_handler->deleteUser($deluser)) {
                        $output .= "Could not delete ".$deluser->getVar("uname")."<br />";
                    } else {
                        $output .= $deluser->getVar("uname")." deleted<br />";
                    }
                    xoops_notification_deletebyuser($deluser->getVar('uid'));
                }
            }
            unset($deluser);
        }
        xoops_cp_header();
        echo $output;
        xoops_cp_footer();
    }
    else {
        xoops_cp_header();
        xoops_error('Ticket Error');
        xoops_cp_footer();
    }
    break;
case "delUserConf":
    if(!xoops_confirm_validate())
        system_users_error("Ticket Error");

    $del_uid = !empty($_POST['del_uid']) ? intval($_POST['del_uid']) : 0;
    if ($del_uid > 0) {
        $member_handler =& xoops_gethandler('member');
        $user =& $member_handler->getUser($del_uid);
        $groups = $user->getGroups();
        if (in_array(XOOPS_GROUP_ADMIN, $groups)) {
            xoops_cp_header();
            echo sprintf('Admin user cannot be deleted. (User: %s)', $user->getVar("uname"));
            xoops_cp_footer();
        } elseif (!$member_handler->deleteUser($user)) {
            xoops_cp_header();
            echo "Could not delete ".$deluser->getVar("uname");
            xoops_cp_footer();
        } else {
            $online_handler =& xoops_gethandler('online');
            $online_handler->destroy($del_uid);
            xoops_notification_deletebyuser($del_uid);
            redirect_header("admin.php?fct=users",1,_AM_DBUPDATED);
        }
    }
    break;
case "addUser":
    if(!XoopsMultiTokenHandler::quickValidate('users_addUser'))
        system_users_error("Ticket Error");

    $myts =& MyTextSanitizer::getInstance();
    $username = !empty($_POST['username']) ? $myts->stripSlashesGPC(trim($_POST['username'])) : '';
    $email = !empty($_POST['email']) ? $myts->stripSlashesGPC(trim($_POST['email'])) : '';
    $password = !empty($_POST['pass']) ? $myts->stripSlashesGPC(trim($_POST['pass'])) : '';
    if ($username == '' || $email == '' || $password == '') {
        $adduser_errormsg = _AM_YMCACF;
    } else {
        $member_handler =& xoops_gethandler('member');
        // make sure the username doesnt exist yet
        if ($member_handler->getUserCount(new Criteria('uname', addslashes($username))) > 0) {
            $adduser_errormsg = 'User name '.$username.' already exists';
        } else {
            $newuser =& $member_handler->createUser();
            if ( isset($_POST['user_viewemail']) ) {
                $newuser->setVar("user_viewemail",$_POST['user_viewemail']);
            }
            if ( isset($_POST['attachsig']) ) {
                $newuser->setVar("attachsig",$_POST['attachsig']);
            }
            $newuser->setVar("name", $_POST['name']);
            $newuser->setVar("uname", $_POST['username']);
            $newuser->setVar("email", $_POST['email']);
            $newuser->setVar("url", formatURL($_POST['url']));
            $newuser->setVar("user_avatar",'blank.gif');
            $newuser->setVar("user_icq", $_POST['user_icq']);
            $newuser->setVar("user_from", $_POST['user_from']);
            $newuser->setVar("user_sig", $_POST['user_sig']);
            $newuser->setVar("user_aim", $_POST['user_aim']);
            $newuser->setVar("user_yim", $_POST['user_yim']);
            $newuser->setVar("user_msnm", $_POST['user_msnm']);
            if ($_POST['pass2'] != "") {
                if ( $_POST['pass'] != $_POST['pass2'] ) {
                    xoops_cp_header();
                    echo "
                    <b>"._AM_STNPDNM."</b>";
                    xoops_cp_footer();
                    exit();
                }
                $newuser->setVar("pass", md5($_POST['pass']));
            }
            $newuser->setVar("timezone_offset", $_POST['timezone_offset']);
            $newuser->setVar("uorder", $_POST['uorder']);
            $newuser->setVar("umode", $_POST['umode']);
            $newuser->setVar("notify_method", $_POST['notify_method']);
            $newuser->setVar("notify_mode", $_POST['notify_mode']);
            $newuser->setVar("bio", $_POST['bio']);
            $newuser->setVar("rank", $_POST['rank']);
            $newuser->setVar("level", 1);
            $newuser->setVar("user_occ", $_POST['user_occ']);
            $newuser->setVar("user_intrest", $_POST['user_intrest']);
            $newuser->setVar('user_mailok', $_POST['user_mailok']);
            if (!$member_handler->insertUser($newuser)) {
                $adduser_errormsg = _AM_CNRNU;
            } else {
                if (!empty($_POST['groups'])) {
                    foreach ($_POST['groups'] as $groupid) {
                        $member_handler->addUserToGroup(intval($groupid), $newuser->getVar('uid'));
                    }
                }
                redirect_header("admin.php?fct=users",1,_AM_DBUPDATED);
                exit();
            }
        }
    }
    xoops_cp_header();
    xoops_error($adduser_errormsg);
    xoops_cp_footer();
    break;
case "synchronize":
    if(!XoopsMultiTokenHandler::quickValidate('users_synchronize'))
        system_users_error("Ticket Error");

    synchronize($_POST['id'], $_POST['type']);
    break;
case "reactivate":
    if(!xoops_confirm_validate())
        system_users_error("Ticket Error");

    $uid = !empty($_POST['uid']) ? intval($_POST['uid']) : 0;
    if ($uid > 0) {
        $result=$xoopsDB->query("UPDATE ".$xoopsDB->prefix("users")." SET level=1 WHERE uid=".$uid);
    }
    redirect_header("admin.php?fct=users&amp;op=modifyUser&amp;uid=".$uid,1,_AM_DBUPDATED);
    break;
case "mod_users":
default:
    include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
    displayUsers();
    break;
}
?>
