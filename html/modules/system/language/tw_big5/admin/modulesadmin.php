<?php
// $Id: modulesadmin.php,v 1.1 2008/03/09 02:27:22 minahito Exp $
//%%%%%%	File Name  modulesadmin.php 	%%%%%
define("_MD_AM_MODADMIN","模組管理區");
define("_MD_AM_MODULE","模組");
define("_MD_AM_VERSION","版本");
define("_MD_AM_LASTUP","最後更新");
define("_MD_AM_DEACTIVATED","已停用");
define("_MD_AM_ACTION","模組狀態");
define("_MD_AM_DEACTIVATE","停用");
define("_MD_AM_ACTIVATE","啟用");
define("_MD_AM_UPDATE","更新");
define("_MD_AM_DUPEN","資料庫中模組有重複欄位。");
define("_MD_AM_DEACTED","目前模組已經停用了，你可以安全的解除安裝。");
define("_MD_AM_ACTED","目前模組啟用中。");
define("_MD_AM_UPDTED","模組已經更新。");
define("_MD_AM_SYSNO","系統模組無法停用。");
define("_MD_AM_STRTNO","此模組內建於系統模組中，請到系統管理區。");

// added in RC2
define("_MD_AM_PCMFM","請確認：");

// added in RC3
define("_MD_AM_ORDER","排序");
define("_MD_AM_ORDER0","(0 = 隱藏不顯示於主要選單)");
define("_MD_AM_ACTIVE","啟用");
define("_MD_AM_INACTIVE","停用");
define("_MD_AM_NOTINSTALLED","未安裝");
define("_MD_AM_NOCHANGE","未變更");
define("_MD_AM_INSTALL","安裝");
define("_MD_AM_UNINSTALL","解除安裝");
define("_MD_AM_SUBMIT","執行");
define("_MD_AM_CANCEL","取消");
define("_MD_AM_DBUPDATE","資料庫更新完成!");
define("_MD_AM_BTOMADMIN","回到模組管理選單");

// %s represents module name
define("_MD_AM_FAILINS","無法安裝 %s.");
define("_MD_AM_FAILACT","無法啟用 %s.");
define("_MD_AM_FAILDEACT","無法解除 %s 之啟用狀態");
define("_MD_AM_FAILUPD","無法更新 %s.");
define("_MD_AM_FAILUNINS","無法反安裝 %s.");
define("_MD_AM_FAILORDER","無法紀錄 %s.");
define("_MD_AM_FAILWRITE","無法寫入資料到主選單");
define("_MD_AM_ALEXISTS","%s 模組已經存在.");
define("_MD_AM_ERRORSC","錯誤:");
define("_MD_AM_OKINS","模組 %s 安裝完成.");
define("_MD_AM_OKACT","模組 %s 啟用完成.");
define("_MD_AM_OKDEACT","模組 %s 已停用.");
define("_MD_AM_OKUPD","模組 %s 更新完成.");
define("_MD_AM_OKUNINS","模組 %s 移除完成.");
define("_MD_AM_OKORDER","模組 %s 紀錄完成.");

define('_MD_AM_RUSUREINS', '按下按鍵安裝此模組');
define('_MD_AM_RUSUREUPD', '按下按鍵升級此模組');
define('_MD_AM_RUSUREUNINS', '你確定要反安裝此模組?');
define('_MD_AM_LISTUPBLKS', '以下區塊將一起更新.<br />選擇要更新的區塊內容 (格式)將一並更新.<br />');
define('_MD_AM_NEWBLKS', '新區塊');
define('_MD_AM_DEPREBLKS', '不更新區塊');
?>
