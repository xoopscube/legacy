<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'protector' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {


// Appended by Xoops Language Checker -GIJOE- in 2009-11-17 18:12:57
define($constpref.'_FILTERS','filters enabled in this site');
define($constpref.'_FILTERSDSC','specify file names inside of filters_byconfig/ separated with LF');
define($constpref.'_MANIPUCHECK','enable manipulation checking');
define($constpref.'_MANIPUCHECKDSC','notify to admin if your root folder or index.php is modified.');
define($constpref.'_MANIPUVALUE','value for manipulation checking');
define($constpref.'_MANIPUVALUEDSC','do not edit this field');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","������ �����");

// A brief description of this module
define($constpref."_DESC","��� �������� ���� ������ ������� �� ������ �������� �������� ������");

// Menu
define($constpref."_ADMININDEX","��������");
define($constpref."_ADVISORY","���� �������");
define($constpref."_PREFIXMANAGER","����� ���� ����� ��������");
define($constpref.'_ADMENU_MYBLOCKSADMIN','��������') ;

// Configs
define($constpref.'_GLOBAL_DISBL','����� �������');
define($constpref.'_GLOBAL_DISBLDSC','����� ������ ������ ');

define($constpref.'_DEFAULT_LANG','�����');
define($constpref.'_DEFAULT_LANGDSC','common.php ��� ����� ���� ������� ��� ��� ���  ');

define($constpref.'_RELIABLE_IPS','��������� �������');
define($constpref.'_RELIABLE_IPSDSC',' |�� ��������� ���� ����� ����� ����� �������� ���� ���� ��������� ���� �������');

define($constpref.'_LOG_LEVEL','��� �������');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_BANIP_TIME0','��� ����� ������ ������� - ��������)');

define($constpref.'_LOGLEVEL0','����');
define($constpref.'_LOGLEVEL15','����');
define($constpref.'_LOGLEVEL63','����');
define($constpref.'_LOGLEVEL255','����');

define($constpref.'_HIJACK_TOPBIT','����� ������ ����� ������-�� ������� �������');
define($constpref.'_HIJACK_TOPBITDSC','������� ������ �� ���� �������  . �� ��� �� ���� ���� ����� 32 �� ��� ��� ���� ����� 24 ��������');
define($constpref.'_HIJACK_DENYGP','��������� ����� ����� ������ ��� ���� ����� ������');
define($constpref.'_HIJACK_DENYGPDSC','���� ��� ����� ������� �� ������:<br />����� �������� ����� ����� ��� ��������� ��� ���� ������� ����� ������ . �� ������� ������ ������ �������');
define($constpref.'_SAN_NULLBYTE','null-bytes ������� ������ �� ���');
define($constpref.'_SAN_NULLBYTEDSC','"\\0" �� ������� ����� ��� ������ ��� ��� ����� ����� �� ������ �� ������ �������');
define($constpref.'_DIE_NULLBYTE','"\\0" ������ �� ���� ����  ����� �� ��� ��� ����');
define($constpref.'_DIE_NULLBYTEDSC','"\\0" �� ������� ����� ��� ������ ��� ��� ����� ����� �� ������ �� ������ �������');
define($constpref.'_DIE_BADEXT','������ �� ���� ��� ��� ���');
define($constpref.'_DIE_BADEXTDSC','�� ���� ��� ��� ��� ����� �� ��� ��  �� ���� ���� ��� ����� ���<br />�� ��� �� ������ ���� ����� ����� �� ��� �� ��� �� ������ ��� ������ ');
define($constpref.'_CONTAMI_ACTION','������ ����� ������ �������� ������');
define($constpref.'_CONTAMI_ACTIONDS','����� ����� �� ���� ������ ������ ������  ������ �������� ������ ������<br />������� ��  ������ ���� �����');
define($constpref.'_ISOCOM_ACTION','����� ��� ������ ����� �����');
define($constpref.'_ISOCOM_ACTIONDSC','���� ���� �� �������:<br />"/*" ����� ��� ������� ��� ����� �� ����� ��<br />������� ���� ����� ��� ������ ����� ������� - ����� �������  �� ������ ����� �����');
define($constpref.'_UNION_ACTION','����� ��� ������ �� �� ����� �������');
define($constpref.'_UNION_ACTIONDSC','���� ����� �������:<br />����� ����� ��� ������ �� ����� ������ �� ������ ������� ������ ������� �� ����� �����<br />""union" ���� ���� ����� ���� ���  ������ ������');
define($constpref.'_ID_INTVAL','ID ����� ����� ������ �� �������');
define($constpref.'_ID_INTVALDSC','"*id" �� ������� ���� ����� ���� �����<br />����� ������ ���� �� ��� ������ ����<br />��� �������� ���� ������ ����� ����� ���� ���� �� �������  ��� �� ��� ���� �� ����');
define($constpref.'_FILE_DOTDOT','Directory Traversals����� �� ������ ������ ');
define($constpref.'_FILE_DOTDOTDSC','��� �� �������� ���� ����  ��� ���� ���� �������� ������ �������� ����� ���� �� ����� �������');

define($constpref.'_BF_COUNT','���� ������ ����� ������ ��������');
define($constpref.'_BF_COUNTDSC','��� ��� ������ ������� ����� ��� ������ ����� ����� �� ��� ����� ���� ����� ������ ���� ����');

define($constpref.'_BWLIMIT_COUNT','����� ���� ��� ����� ������� - ����������');
define($constpref.'_BWLIMIT_COUNTDSC','mainfile.php �� ��� ������� ���� ����� ���� ���� ��� ������� ��� ����� �� �� ������  ��� ��� ��� �� 10 ���� ������ -��� ��� ������ ���� ������ ������ ���� ����� ���');

define($constpref.'_DOS_SKIPMODS',' Crawler ������� ����� ����� ����� ��������');
define($constpref.'_DOS_SKIPMODSDSC','|�� ������ ����� ��������� ���� ���� ��������� �� ��������  ���� ��� ������� ��������');

define($constpref.'_DOS_EXPIRE','������ ����� ��� ������ ��������');
define($constpref.'_DOS_EXPIREDSC','F5��� �������� ������� ����� ������ ��� ������ �� ���� ����� ����� ���� �� ��� ������� ���� ����� �� ������ ������ �������� ������ ');

define($constpref.'_DOS_F5COUNT',' F5��� ������ ��������� ����');
define($constpref.'_DOS_F5COUNTDSC','������� ��  ����� �������� ������ ������ ����� ���� ������� ���� �� ���');
define($constpref.'_DOS_F5ACTION',' F5 ����� ��� ������ ���� �� ���');

define($constpref.'_DOS_CRCOUNT','��� ���� ��������� �� ��� ������ ����� ��� ������ ������� ����');
define($constpref.'_DOS_CRCOUNTDSC','����� �� �� �������� ���� ���� ������� ������� �� ����� ������ ����� ������ ��� ����');
define($constpref.'_DOS_CRACTION','����� ��� ������ ������ ����� ��� ���� ��� ������');

define($constpref.'_DOS_CRSAFE','������ ����� ������� ��� ');
define($constpref.'_DOS_CRSAFEDSC','�� ������ ����� ������� ������ �� ����� ������ ��� ���� �� ���� ��� ��� ������<br />���<br />eg) /(msnbot|Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','����� ��� ��� �������');
define($constpref.'_OPT_SAN','����� �����');
define($constpref.'_OPT_EXIT','���� �����');
define($constpref.'_OPT_BIP','��� ������ �����');
define($constpref.'_OPT_BIPTIME0','��� ������ ����');

define($constpref.'_DOSOPT_NONE','����� ��� ��� �������');
define($constpref.'_DOSOPT_SLEEP','��� �������-����');
define($constpref.'_DOSOPT_EXIT','���� �����');
define($constpref.'_DOSOPT_BIP','��� ������ �����');
define($constpref.'_DOSOPT_BIPTIME0','��� ������ ����');
define($constpref.'_DOSOPT_HTA','.htaccess ����� ����');

define($constpref.'_BIP_EXCEPT','��������  ���� �� ��� ����� ����');
define($constpref.'_BIP_EXCEPTDSC','��� ���� ����   ������� �� ����� �� ������<br />(�� ������� ��� ���� ������');

define($constpref.'_DISABLES','XOOPS �����  ����� ����� �� ����');

define($constpref.'_DBLAYERTRAP','����� ������ ���� ������ �����');
define($constpref.'_DBLAYERTRAPDSC','��� �������� ���� ������ �� ������ ����� . ���� ���� ������ �� ���� ������� ������ �� �� ��� ���� ������ �� ������');
define($constpref.'_DBTRAPWOSRV','����� ��� ����� ������� �� ���� �����');
define($constpref.'_DBTRAPWOSRVDSC',' ���� ������� ����� ���� ���� ����� �� ����� �������� - �� ����� ����� ������ �� ������ ��� ��������');

define($constpref.'_BIGUMBRELLA','anti-XSS (BigUmbrella)������� �� ������ �� ���');
define($constpref.'_BIGUMBRELLADSC','��� ����� ���� ������� ������ ����� �� ����� ����� ���� ����� ������ �������� ��� ������ ����� �� ���� ������. ������ ������ ����� ����� ���� �����  ������� ����� ������ ');

define($constpref.'_SPAMURI4U','���� ������ �������');
define($constpref.'_SPAMURI4UDSC','�� ����� �� ����� �� ��� ������� ����� ��� ����� �� ������� ������ ���� ���� ��� ���� ����� ��������');
define($constpref.'_SPAMURI4G','���� ������ ������');
define($constpref.'_SPAMURI4GDSC','�� ����� �� ����� ����� ��� ����� �� ������� ������ ���� ��� ��� ���� ������ ��������');

}

?>