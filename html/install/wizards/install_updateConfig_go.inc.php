<?php
/**
 *
 * @package Legacy
 * @version $Id: install_updateConfig_go.inc.php,v 1.3 2008/09/25 15:12:23 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    include_once "../mainfile.php";

    $language = check_language($language);
    if (file_exists("./language/".$language."/install2.php")) {
        include_once "./language/".$language."/install2.php";
    } elseif (file_exists("./language/english/install2.php")) {
        include_once "./language/english/install2.php";
        $language = 'english';
    } else {
        echo 'no language file (install2.php).';
        exit();
    }
    include_once './class/dbmanager.php';
    $dbm = new db_manager;

    // default settings
    $xoopsConfig['sitename'] = 'XOOPS Cube Site';
    $xoopsConfig['slogan'] = 'Just use it!';
    $xoopsConfig['adminmail'] = '';
    $xoopsConfig['language'] = 'english';
    $xoopsConfig['anonymous'] = 'Anonymous';
    $xoopsConfig['minpass'] = 5;
    $xoopsConfig['anonpost'] = 0;
    $xoopsConfig['new_user_notify'] = 0;
    $xoopsConfig['new_user_notify_group'] = 1;
    $xoopsConfig['self_delete'] = 0;
    $xoopsConfig['gzip_compression'] = 0;
    $xoopsConfig['uname_test_level'] = 0;
    $xoopsConfig['usercookie'] = "xcl_user";
    $xoopsConfig['sessioncookie'] = "xcl_session";
    $xoopsConfig['sessionexpire'] = 4500;
    $xoopsConfig['server_TZ'] = 0;
    $xoopsConfig['default_TZ'] = 0;
    $xoopsConfig['banners'] = 1;
    $xoopsConfig['com_mode'] = "nest";
    $xoopsConfig['com_order'] = 1;
    $xoopsConfig['my_ip'] = "127.0.0.1";
    $xoopsConfig['avatar_allow_upload'] = 0;
    $xoopsConfig['avatar_width'] = 120;
    $xoopsConfig['avatar_height'] = 120;
    $xoopsConfig['avatar_maxsize'] = 15000;

    // override deafault with 1.3.x settings if any
    if (file_exists('../modules/system/cache/config.php')) {
        include_once('../modules/system/cache/config.php');
    }

    $dbm->insert('config', " VALUES (1, 0, 1, 'sitename', '_MD_AM_SITENAME', '".addslashes($xoopsConfig['sitename'])."', '_MD_AM_SITENAMEDSC', 'textbox', 'text', 0)");
    $dbm->insert('config', " VALUES (2, 0, 1, 'slogan', '_MD_AM_SLOGAN', '".addslashes($xoopsConfig['slogan'])."', '_MD_AM_SLOGANDSC', 'textbox', 'text', 2)");
    $dbm->insert('config', " VALUES (3, 0, 1, 'language', '_MD_AM_LANGUAGE', '".$xoopsConfig['language']."', '_MD_AM_LANGUAGEDSC', 'language', 'other', 4)");
    $dbm->insert('config', " VALUES (4, 0, 1, 'startpage', '_MD_AM_STARTPAGE', '--', '_MD_AM_STARTPAGEDSC', 'startpage', 'other', 6)");
    $dbm->insert('config', " VALUES (5, 0, 1, 'server_TZ', '_MD_AM_SERVERTZ', '".addslashes($xoopsConfig['server_TZ'])."', '_MD_AM_SERVERTZDSC', 'timezone', 'float', 8)");
    $dbm->insert('config', " VALUES (6, 0, 1, 'default_TZ', '_MD_AM_DEFAULTTZ', '".addslashes($xoopsConfig['default_TZ'])."', '_MD_AM_DEFAULTTZDSC', 'timezone', 'float', 10)");
    $dbm->insert('config', " VALUES (7, 0, 1, 'theme_set', '_MD_AM_DTHEME', 'xcl_default', '_MD_AM_DTHEMEDSC', 'theme', 'other', 12)");
    $dbm->insert('config', " VALUES (8, 0, 1, 'anonymous', '_MD_AM_ANONNAME', '".addslashes($xoopsConfig['anonymous'])."', '_MD_AM_ANONNAMEDSC', 'textbox', 'text', 15)");
    $dbm->insert('config', " VALUES (9, 0, 1, 'gzip_compression', '_MD_AM_USEGZIP', '".intval($xoopsConfig['gzip_compression'])."', '_MD_AM_USEGZIPDSC', 'yesno', 'int', 16)");
    $dbm->insert('config', " VALUES (11, 0, 1, 'session_expire', '_MD_AM_SESSEXPIRE', '15', '_MD_AM_SESSEXPIREDSC', 'textbox', 'int', 22)");
    $dbm->insert('config', " VALUES (13, 0, 1, 'debug_mode', '_MD_AM_DEBUGMODE', '1', '_MD_AM_DEBUGMODEDSC', 'select', 'int', 24)");
    $dbm->insert('config', " VALUES (14, 0, 1, 'my_ip', '_MD_AM_MYIP', '".addslashes($xoopsConfig['my_ip'])."', '_MD_AM_MYIPDSC', 'textbox', 'text', 29)");
    $dbm->insert('config', " VALUES (15, 0, 1, 'use_ssl', '_MD_AM_USESSL', '0', '_MD_AM_USESSLDSC', 'yesno', 'int', 30)");
    $dbm->insert('config', " VALUES (16, 0, 1, 'session_name', '_MD_AM_SESSNAME', 'xcl_session', '_MD_AM_SESSNAMEDSC', 'textbox', 'text', 20)");
    $dbm->insert('config', " VALUES (32, 0, 1, 'com_mode', '_MD_AM_COMMODE', '".addslashes($xoopsConfig['com_mode'])."', '_MD_AM_COMMODEDSC', 'select', 'text', 34)");
    $dbm->insert('config', " VALUES (33, 0, 1, 'com_order', '_MD_AM_COMORDER', '".intval($xoopsConfig['com_order'])."', '_MD_AM_COMORDERDSC', 'select', 'int', 36)");
    $dbm->insert('config', " VALUES (37, 0, 1, 'bad_ips', '_MD_AM_BADIPS', '".addslashes(serialize(array('127.0.0.1')))."', '_MD_AM_BADIPSDSC', 'textarea', 'array', 42)");
    $dbm->insert('config', " VALUES (40, 0, 4, 'censor_enable', '_MD_AM_DOCENSOR', '0', '_MD_AM_DOCENSORDSC', 'yesno', 'int', 0)");
    $dbm->insert('config', " VALUES (41, 0, 4, 'censor_words', '_MD_AM_CENSORWRD', '".addslashes(serialize(array('fuck', 'shit')))."', '_MD_AM_CENSORWRDDSC', 'textarea', 'array', 1)");
    $dbm->insert('config', " VALUES (42, 0, 4, 'censor_replace', '_MD_AM_CENSORRPLC', '#OOPS#', '_MD_AM_CENSORRPLCDSC', 'textbox', 'text', 2)");
    $dbm->insert('config', " VALUES (44, 0, 5, 'enable_search', '_MD_AM_DOSEARCH', '1', '_MD_AM_DOSEARCHDSC', 'yesno', 'int', 0)");
    $dbm->insert('config', " VALUES (45, 0, 5, 'keyword_min', '_MD_AM_MINSEARCH', '5', '_MD_AM_MINSEARCHDSC', 'textbox', 'int', 1)");
    $dbm->insert('config', " VALUES (47, 0, 1, 'enable_badips', '_MD_AM_DOBADIPS', '0', '_MD_AM_DOBADIPSDSC', 'yesno', 'int', 40)");
    $dbm->insert('config', " VALUES (53, 0, 1, 'use_mysession', '_MD_AM_USEMYSESS', '0', '_MD_AM_USEMYSESSDSC', 'yesno', 'int', 19)");
    $dbm->insert('config', " VALUES (57, 0, 1, 'theme_fromfile', '_MD_AM_THEMEFILE', '0', '_MD_AM_THEMEFILEDSC', 'yesno', 'int', 13)");
    $dbm->insert('config', " VALUES (58, 0, 1, 'closesite', '_MD_AM_CLOSESITE', '0', '_MD_AM_CLOSESITEDSC', 'yesno', 'int', 26)");
    $dbm->insert('config', " VALUES (59, 0, 1, 'closesite_okgrp', '_MD_AM_CLOSESITEOK', '".addslashes(serialize(array('1')))."', '_MD_AM_CLOSESITEOKDSC', 'group_multi', 'array', 27)");
    $dbm->insert('config', " VALUES (60, 0, 1, 'closesite_text', '_MD_AM_CLOSESITETXT', '"._INSTALL_L165."', '_MD_AM_CLOSESITETXTDSC', 'textarea', 'text', 28)");
    $dbm->insert('config', " VALUES (61, 0, 1, 'sslpost_name', '_MD_AM_SSLPOST', 'xcl_ssl', '_MD_AM_SSLPOSTDSC', 'textbox', 'text', 31)");
    $dbm->insert('config', " VALUES (62, 0, 1, 'module_cache', '_MD_AM_MODCACHE', '', '_MD_AM_MODCACHEDSC', 'module_cache', 'array', 50)");
    $dbm->insert('config', " VALUES (63, 0, 1, 'template_set', '_MD_AM_DTPLSET', 'xcl_default', '_MD_AM_DTPLSETDSC', 'tplset', 'other', 14)");
    $dbm->insert('config', " VALUES (64,0,6,'mailmethod','_MD_AM_MAILERMETHOD','mail','_MD_AM_MAILERMETHODDESC','select','text',4)");
    $dbm->insert('config', " VALUES (65,0,6,'smtphost','_MD_AM_SMTPHOST','a:1:{i:0;s:0:\"\";}', '_MD_AM_SMTPHOSTDESC','textarea','array',6)");
    $dbm->insert('config', " VALUES (66,0,6,'smtpuser','_MD_AM_SMTPUSER','','_MD_AM_SMTPUSERDESC','textbox','text',7)");
    $dbm->insert('config', " VALUES (67,0,6,'smtppass','_MD_AM_SMTPPASS','','_MD_AM_SMTPPASSDESC','password','text',8)");
    $dbm->insert('config', " VALUES (68,0,6,'sendmailpath','_MD_AM_SENDMAILPATH','/usr/sbin/sendmail','_MD_AM_SENDMAILPATHDESC','textbox','text',5)");
    $dbm->insert('config', " VALUES (69,0,6,'from','_MD_AM_MAILFROM','','_MD_AM_MAILFROMDESC','textbox','text',1)");
    $dbm->insert('config', " VALUES (70,0,6,'fromname','_MD_AM_MAILFROMNAME','','_MD_AM_MAILFROMNAMEDESC','textbox','text',2)");
    $dbm->insert('config', " VALUES (71, 0, 1, 'sslloginlink', '_MD_AM_SSLLINK', 'https://', '_MD_AM_SSLLINKDSC', 'textbox', 'text', 33)");
    $dbm->insert('config', " VALUES (72, 0, 1, 'theme_set_allowed', '_MD_AM_THEMEOK', '".serialize(array('default'))."', '_MD_AM_THEMEOKDSC', 'theme_multi', 'array', 13)");
    $dbm->insert('config', " VALUES (73,0,6,'fromuid','_MD_AM_MAILFROMUID','1','_MD_AM_MAILFROMUIDDESC','user','int',3)");


    // default the default theme

    $time = time();
    $dbm->insert('tplset', " VALUES (1, 'xcl_default', 'XCL 2.3 Default Theme', '', ".$time.")");

//  include_once './class/cachemanager.php';
//    $cm = new cache_manager;
//  $skinfiles = array('1' => 'skin.html', '2' => 'style.css'
//                        , '3' => 'styleNN.css','4' =>  'styleMAC.css'
//                        , '5' => 'skin_blockleft.html', '6' => 'skin_blockright.html'
//                        , '7' => 'skin_blockcenter_l.html', '8' => 'skin_blockcenter_c.html'
//                        , '9' => 'skin_blockcenter_r.html');
//    foreach ($skinfiles as $key => $skinfile) {
//        if(preg_match('/\.css$/', $skinfile)) {
//            $type = 'css';
//        }else{
//            $type = 'skin';
//        }
//        $dbm->insert('tplfile', " VALUES ($key, 0, '', 'default', '$skinfile', '', $time, $time, '$type')");

//        $fp = fopen('./templates/default_skin/'.$skinfile, 'r');
//        $skinsource = fread($fp, filesize('./templates/default_skin/'.$skinfile));
//        fclose($fp);
//        $dbm->insert('tplsource', " (tpl_id, tpl_source) VALUES ($key, '".addslashes($skinsource)."')");
//        if(preg_match('/\.css$/',$skinfile)) {
//            $cm->write($skinfile, $skinsource);
//        }
//    }

        $dbm->query("INSERT INTO ".$dbm->prefix('group_permission')." (gperm_groupid, gperm_itemid) SELECT groupid, block_id FROM ".$dbm->prefix('groups_blocks_link'));
        $dbm->query("UPDATE ".$dbm->prefix('group_permission')." SET gperm_name = 'block_read'");
        $dbm->query("INSERT INTO ".$dbm->prefix('group_permission')." (gperm_groupid, gperm_itemid) SELECT groupid, mid FROM ".$dbm->prefix('groups_modules_link') ." WHERE type='A'");
        $dbm->query("UPDATE ".$dbm->prefix('group_permission')." SET gperm_name = 'module_admin' WHERE gperm_name = ''");
        $dbm->query("INSERT INTO ".$dbm->prefix('group_permission')." (gperm_groupid, gperm_itemid) SELECT groupid, mid FROM ".$dbm->prefix('groups_modules_link')." WHERE type='R'");
        $dbm->query("UPDATE ".$dbm->prefix('group_permission')." SET gperm_name = 'module_read' WHERE gperm_name = ''");
        $dbm->query("UPDATE ".$dbm->prefix('group_permission')." SET gperm_modid = 1");
        $dbm->query('DROP TABLE '.$dbm->prefix('groups_blocks_link'));
        $dbm->query('DROP TABLE '.$dbm->prefix('groups_modules_link'));

    // insert some more data
    $result = $dbm->queryFromFile('./sql/'.((XOOPS_DB_TYPE === 'mysqli')? 'mysql' : XOOPS_DB_TYPE).'.data.sql');

    $content = $dbm->report();
    //$content .= $cm->report();
    $b_next = array('updateModules', _INSTALL_L14);
    include './install_tpl.php';
