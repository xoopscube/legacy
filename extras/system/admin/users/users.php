<?php
// $Id: users.php,v 1.1 2007/05/15 02:34:54 minahito Exp $
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
/*********************************************************/
/* Users Functions                                       */
/*********************************************************/
include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/**
 * @brief display error message & exit (Tentative)
 */
function system_users_error($message)
{
    xoops_cp_header();
    xoops_error($message);
    xoops_cp_footer();
    exit();
}

function displayUsers()
{
    global $xoopsDB, $xoopsConfig, $xoopsModule;
    $userstart = isset($_GET['userstart']) ? intval($_GET['userstart']) : 0;
    xoops_cp_header();
    $member_handler =& xoops_gethandler('member');
    $usercount = $member_handler->getUserCount();
    $nav = new XoopsPageNav($usercount, 200, $userstart, "userstart", "fct=users");
    $editform = new XoopsThemeForm(_AM_EDEUSER, "edituser", "admin.php", 'GET');
    $user_select = new XoopsFormSelect('', "uid");
    $criteria = new CriteriaCompo();
    $criteria->setSort('uname');
    $criteria->setOrder('ASC');
    $criteria->setLimit(200);
    $criteria->setStart($userstart);
    $user_select->addOptionArray($member_handler->getUserList($criteria));
    $user_select_tray = new XoopsFormElementTray(_AM_NICKNAME, "<br />");
    $user_select_tray->addElement($user_select);
    $user_select_nav = new XoopsFormLabel('', $nav->renderNav(4));
    $user_select_tray->addElement($user_select_nav);
    $op_select = new XoopsFormSelect("", "op");
    $op_select->addOptionArray(array("modifyUser"=>_AM_MODIFYUSER, "delUser"=>_AM_DELUSER));
    $submit_button = new XoopsFormButton("", "submit", _AM_GO, "submit");
    $fct_hidden = new XoopsFormHidden("fct", "users");
    $editform->addElement($user_select_tray);
    $editform->addElement($op_select);
    $editform->addElement($submit_button);
    $editform->addElement($fct_hidden);
    $editform->display();

    echo "<br />\n";
    $uid_value = "";
    $uname_value = "";
    $name_value = "";
    $email_value = "";
    $email_cbox_value = 0;
    $url_value = "";
//  $avatar_value = "blank.gif";
//  $theme_value = $xoopsConfig['default_theme'];
    $timezone_value = $xoopsConfig['default_TZ'];
    $icq_value = "";
    $aim_value = "";
    $yim_value = "";
    $msnm_value = "";
    $location_value = "";
    $occ_value = "";
    $interest_value = "";
    $sig_value = "";
    $sig_cbox_value = 0;
    $umode_value = $xoopsConfig['com_mode'];
    $uorder_value = $xoopsConfig['com_order'];
    // RMV-NOTIFY
    include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
    $notify_method_value = XOOPS_NOTIFICATION_METHOD_PM;
    $notify_mode_value = XOOPS_NOTIFICATION_MODE_SENDALWAYS;
    $bio_value = "";
    $rank_value = 0;
    $mailok_value = 0;
    $op_value = "addUser";
    $form_title = _AM_ADDUSER;
    $form_isedit = false;
    $groups = array(XOOPS_GROUP_USERS);
    include XOOPS_ROOT_PATH."/modules/system/admin/users/userform.php";
        xoops_cp_footer();
}

function modifyUser($user)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule;
    xoops_cp_header();
    $member_handler =& xoops_gethandler('member');
    $user =& $member_handler->getUser($user);
    if (is_object($user)) {
        if (!$user->isActive()) {
            xoops_token_confirm(array('fct' => 'users', 'op' => 'reactivate', 'uid' => $user->getVar('uid')), 'admin.php', _AM_NOTACTIVE);
            xoops_cp_footer();
            exit();
        }
        $uid_value = $user->getVar("uid");
        $uname_value = $user->getVar("uname", "E");
        $name_value = $user->getVar("name", "E");
        $email_value = $user->getVar("email", "E");
        $email_cbox_value = $user->getVar("user_viewemail") ? 1 : 0;
        $url_value = $user->getVar("url", "E");
//      $avatar_value = $user->getVar("user_avatar");
        $temp = $user->getVar("theme");
//      $theme_value = empty($temp) ? $xoopsConfig['default_theme'] : $temp;
        $timezone_value = $user->getVar("timezone_offset");
        $icq_value = $user->getVar("user_icq", "E");
        $aim_value = $user->getVar("user_aim", "E");
        $yim_value = $user->getVar("user_yim", "E");
        $msnm_value = $user->getVar("user_msnm", "E");
        $location_value = $user->getVar("user_from", "E");
        $occ_value = $user->getVar("user_occ", "E");
        $interest_value = $user->getVar("user_intrest", "E");
        $sig_value = $user->getVar("user_sig", "E");
        $sig_cbox_value = ($user->getVar("attachsig") == 1) ? 1 : 0;
        $umode_value = $user->getVar("umode");
        $uorder_value = $user->getVar("uorder");
        // RMV-NOTIFY
        $notify_method_value = $user->getVar("notify_method");
        $notify_mode_value = $user->getVar("notify_mode");
        $bio_value = $user->getVar("bio", "E");
        $rank_value = $user->rank(false);
        $mailok_value = $user->getVar('user_mailok', 'E');
        $op_value = "updateUser";
        $form_title = _AM_UPDATEUSER.": ".$user->getVar("uname");
        $form_isedit = true;
        $groups = array_values($user->getGroups());

        $token = XoopsMultiTokenHandler::quickCreate('users_synchronize');

        include XOOPS_ROOT_PATH."/modules/system/admin/users/userform.php";
        echo "<br /><b>"._AM_USERPOST."</b><br /><br />\n";
        echo "<table>\n";
        echo "<tr><td>"._AM_COMMENTS."</td><td>".$user->getVar("posts")."</td></tr>\n";
        echo "</table>\n";
        echo "<br />"._AM_PTBBTSDIYT."<br />\n";
        echo "<form action=\"admin.php\" method=\"post\">\n";
        echo $token->getHtml();
        echo "<input type=\"hidden\" name=\"id\" value=\"".$user->getVar("uid")."\" />";
        echo "<input type=\"hidden\" name=\"type\" value=\"user\" />\n";
        echo "<input type=\"hidden\" name=\"fct\" value=\"users\" />\n";
        echo "<input type=\"hidden\" name=\"op\" value=\"synchronize\" />\n";
        echo "<input type=\"submit\" value=\""._AM_SYNCHRONIZE."\" />\n";
        echo "</form>\n";
    } else {
        echo "<h4 style='text-align:left;'>";
        echo _AM_USERDONEXIT;
        echo "</h4>";
    }
    xoops_cp_footer();
}

function synchronize($id, $type)
{
    global $xoopsDB;
    switch($type) {
    case 'user':
        $id = intval($id);
        // Array of tables from which to count 'posts'
        $tables = array();
        // Count comments (approved only: com_status == XOOPS_COMMENT_ACTIVE)
        include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
        $tables[] = array ('table_name' => 'xoopscomments', 'uid_column' => 'com_uid', 'criteria' => new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
        // Count forum posts
        $tables[] = array ('table_name' => 'bb_posts', 'uid_column' => 'uid');

        $total_posts = 0;
        foreach ($tables as $table) {
            $criteria = new CriteriaCompo();
            $criteria->add (new Criteria($table['uid_column'], $id));
            if (!empty($table['criteria'])) {
                $criteria->add ($table['criteria']);
            }
            $sql = "SELECT COUNT(*) AS total FROM ".$xoopsDB->prefix($table['table_name']) . ' ' . $criteria->renderWhere();
            if ( $result = $xoopsDB->query($sql) ) {
                if ($row = $xoopsDB->fetchArray($result)) {
                    $total_posts = $total_posts + $row['total'];
                }
            }
        }
        $sql = "UPDATE ".$xoopsDB->prefix("users")." SET posts = $total_posts WHERE uid = $id";
        if ( !$result = $xoopsDB->query($sql) ) {
            exit(sprintf(_AM_CNUUSER %s ,$id));
        }
        break;
    case 'all users':
        $sql = "SELECT uid FROM ".$xoopsDB->prefix("users")."";
        if ( !$result = $xoopsDB->query($sql) ) {
            exit(_AM_CNGUSERID);
        }
        while ($row = $xoopsDB->fetchArray($result)) {
            $id = $row['uid'];
            synchronize($id, "user");
        }
        break;
    default:
        break;
    }
    redirect_header("admin.php?fct=users&amp;op=modifyUser&amp;uid=".$id,1,_AM_DBUPDATED);
    exit();
}
?>
