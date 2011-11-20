<?php
require ( $root->mytrustdirpath."/ini/".basename(__FILE__) );

/////////////////////////////////////////////////
// 以下のブロックはオリジナル設定です。
// XOOPS_TRUST_PATH/modules/xpwiki/ini/pukiwiki.ini.php
// から切り出して記述してください。
// 予め書いてあるのは代表的な設定項目のみです。

/////////////////////////////////////////////////
// Directory settings II (ended with '/')
// Skins / Stylesheets
$const['SKIN_DIR'] = 'skin/default/';

/////////////////////////////////////////////////
// Title of your Wikisite (Name this)
// Also used as RSS feed's channel name etc
$root->page_title = 'PukiWiki';

// Site admin's name (CHANGE THIS)
$root->modifier = 'anonymous';

// Site admin's Web page (CHANGE THIS)
$root->modifierlink = 'http://pukiwiki.example.com/';

// Default page name
$root->defaultpage  = 'FrontPage';     // Top / Default page

/////////////////////////////////////////////////
// Admin password for this Wikisite

// Default: always fail
$root->adminpass = '{x-php-md5}!';

// Sample:
//$root->adminpass = 'pass'; // Cleartext
//$root->adminpass = '{x-php-md5}1a1dc91c907325c69271ddf0c944bc72'; // PHP md5()  'pass'
//$root->adminpass = '{CRYPT}$1$AR.Gk94x$uCe8fUUGMfxAPH83psCZG/';   // LDAP CRYPT 'pass'
//$root->adminpass = '{MD5}Gh3JHJBzJcaScd3wyUS8cg==';               // LDAP MD5   'pass'
//$root->adminpass = '{SMD5}o7lTdtHFJDqxFOVX09C8QnlmYmZnd2Qx';      // LDAP SMD5  'pass'

// For XCL Pack2011
// Image pack name ( ex. 'extra' is $const['IMAGE_DIR'] become "$const['IMAGE_DIR']extra/" )
$root->image_pack_name = 'pack2011';
