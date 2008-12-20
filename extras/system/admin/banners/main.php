<?php
// $Id: main.php,v 1.1 2007/05/15 02:35:09 minahito Exp $
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
// ------------------------------------------------------------------------- //

if ( !is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid()) ) {
    exit("Access Denied");
}
include_once XOOPS_ROOT_PATH."/modules/system/admin/banners/banners.php";
include_once XOOPS_ROOT_PATH."/class/module.textsanitizer.php";

$op = "BannersAdmin";
if (isset($_GET['op'])) {
    $op = $_GET['op'];
} elseif (isset($_POST['op'])) {
    $op = $_POST['op'];
}

switch ( $op ) {
case "BannersAdmin":
    BannersAdmin();
    break;

case "BannersAdd":
    if (!XoopsMultiTokenHandler::quickValidate('banners_BannersAdd')) {
        redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top");
    }
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
    $imageurl = isset($_POST['imageurl']) ? trim($_POST['imageurl']) : '';
    $clickurl = isset($_POST['clickurl']) ? trim($_POST['clickurl']) : '';
    $imptotal = isset($_POST['imptotal']) ? intval($_POST['imptotal']) : 0;
    $htmlbanner = isset($_POST['htmlbanner']) ? intval($_POST['htmlbanner']) : 0;
    $htmlcode = isset($_POST['htmlcode']) ? trim($_POST['htmlcode']) : '';
    if ($cid <= 0) {
        redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top");
    }
    $db =& Database::getInstance();
    $myts =& MyTextSanitizer::getInstance();
    $newid = $db->genId($db->prefix("banner")."_bid_seq");
    $sql = sprintf("INSERT INTO %s (bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date, htmlbanner, htmlcode) VALUES (%d, %d, %d, 1, 0, %s, %s, %d, %d, %s)", $db->prefix("banner"), intval($newid), $cid, $imptotal, $db->quoteString($myts->stripSlashesGPC($imageurl)), $db->quoteString($myts->stripSlashesGPC($clickurl)), time(), $htmlbanner, $db->quoteString($myts->stripSlashesGPC($htmlcode)));
    $db->query($sql);
    redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top",1,_AM_DBUPDATED);
    exit();
    break;

case "BannerAddClient":
    if (!XoopsSingleTokenHandler::quickValidate('banners_AddClient')) {
        redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top");
    }
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $passwd = isset($_POST['passwd']) ? trim($_POST['passwd']) : '';
    $extrainfo = isset($_POST['extrainfo']) ? trim($_POST['extrainfo']) : '';
    $db =& Database::getInstance();
    $myts =& MyTextSanitizer::getInstance();
    $newid = $db->genId($xoopsDB->prefix("bannerclient")."_cid_seq");
    $sql = sprintf("INSERT INTO %s (cid, name, contact, email, login, passwd, extrainfo) VALUES (%d, %s, %s, %s, %s, %s, %s)", $db->prefix("bannerclient"), intval($newid), $db->quoteString($myts->stripSlashesGPC($name)), $db->quoteString($myts->stripSlashesGPC($contact)), $db->quoteString($myts->stripSlashesGPC($email)), $db->quoteString($myts->stripSlashesGPC($login)), $db->quoteString($myts->stripSlashesGPC($passwd)), $db->quoteString($myts->stripSlashesGPC($extrainfo)));
    $db->query($sql);
    redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top",1,_AM_DBUPDATED);
    exit();
    break;

case "BannerFinishDelete":
    xoops_cp_header();
    xoops_token_confirm(array('op' => 'BannerFinishDelete2', 'bid' => intval($_GET['bid']), 'fct' => 'banners'), 'admin.php', _AM_SUREDELE);
    xoops_cp_footer();
    break;

case "BannerFinishDelete2":
    $bid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
    if ($bid <= 0 || !xoops_confirm_validate() ) {
        redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top");
    }
    $db =& Database::getInstance();
    $sql = sprintf("DELETE FROM %s WHERE bid = %u", $db->prefix("bannerfinish"), $bid);
    $db->query($sql);
    redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top",1,_AM_DBUPDATED);
    exit();
    break;

case "BannerDelete":
    $bid = isset($_GET['bid']) ? intval($_GET['bid']) : 0;
    if ($bid > 0) {
        BannerDelete($bid);
    }
    break;

case "BannerDelete2":
    $bid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
    if ($bid <= 0 || !xoops_confirm_validate()) {
        redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top");
    }
    $db =& Database::getInstance();
    $sql = sprintf("DELETE FROM %s WHERE bid = %u", $db->prefix("banner"), $bid);
    $db->query($sql);
    redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top",1,_AM_DBUPDATED);
    break;

case "BannerEdit":
    $bid = isset($_GET['bid']) ? intval($_GET['bid']) : 0;
    if ($bid > 0) {
        BannerEdit($bid);
    }
    break;

case "BannerChange":
    $bid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
    $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
    if ($cid <= 0 || $bid <= 0 || !XoopsMultiTokenHandler::quickValidate('banners_BannerChange')) {
        redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top");
    }
    $imageurl = isset($_POST['imageurl']) ? trim($_POST['imageurl']) : '';
    $clickurl = isset($_POST['clickurl']) ? trim($_POST['clickurl']) : '';
    $imptotal = isset($_POST['imptotal']) ? intval($_POST['imptotal']) : 0;
    $impadded = isset($_POST['impadded']) ? intval($_POST['impadded']) : 0;
    $htmlbanner = isset($_POST['htmlbanner']) ? intval($_POST['htmlbanner']) : 0;
    $htmlcode = isset($_POST['htmlcode']) ? trim($_POST['htmlcode']) : '';
    $db =& Database::getInstance();
    $myts =& MyTextSanitizer::getInstance();
    $sql = sprintf("UPDATE %s SET cid = %d, imptotal = %d, imageurl = %s, clickurl = %s, htmlbanner = %d, htmlcode = %s WHERE bid = %d", $db->prefix("banner"), $cid, $imptotal + $impadded, $db->quoteString($myts->stripSlashesGPC($imageurl)), $db->quoteString($myts->stripSlashesGPC($clickurl)), $htmlbanner, $db->quoteString($myts->stripSlashesGPC($htmlcode)), $bid);
    $db->query($sql);
    redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top",1,_AM_DBUPDATED);
    break;

case "BannerClientDelete":
    $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
    if ($cid > 0) {
        BannerClientDelete($cid);
    }
    break;

case "BannerClientDelete2":
    $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
    $db =& Database::getInstance();
    if ($cid <= 0 || !xoops_confirm_validate()) {
        redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top");
    }
    $sql = sprintf("DELETE FROM %s WHERE cid = %u", $db->prefix("banner"), $cid);
    $db->query($sql);
    $sql = sprintf("DELETE FROM %s WHERE cid = %u", $db->prefix("bannerclient"), $cid);
    $db->query($sql);
    redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top",1,_AM_DBUPDATED);
    break;

case "BannerClientEdit":
    $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
    if ($cid > 0) {
        BannerClientEdit($cid);
    }
    break;

case "BannerClientChange":
    $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
    if ($cid <= 0 || !XoopsSingleTokenHandler::quickValidate('banners_ClientChange')) {
        redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top");
    }
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $passwd = isset($_POST['passwd']) ? trim($_POST['passwd']) : '';
    $extrainfo = isset($_POST['extrainfo']) ? trim($_POST['extrainfo']) : '';
    $db =& Database::getInstance();
    $myts =& MyTextSanitizer::getInstance();
    $sql = sprintf("UPDATE %s SET name = %s, contact = %s, email = %s, login = %s, passwd = %s, extrainfo = %s WHERE cid = %d", $db->prefix("bannerclient"), $db->quoteString($myts->stripSlashesGPC($name)), $db->quoteString($myts->stripSlashesGPC($contact)), $db->quoteString($myts->stripSlashesGPC($email)), $db->quoteString($myts->stripSlashesGPC($login)), $db->quoteString($myts->stripSlashesGPC($passwd)), $db->quoteString($myts->stripSlashesGPC($extrainfo)), $cid);
    $db->query($sql);
    redirect_header("admin.php?fct=banners&amp;op=BannersAdmin#top",1,_AM_DBUPDATED);
    break;

default:
    BannersAdmin();
    break;
}
?>