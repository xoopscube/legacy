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

// Appended by Xoops Language Checker -GIJOE- in 2009-07-06 05:46:53
define($constpref.'_DBTRAPWOSRV','Never checking _SERVER for anti-SQL-Injection');
define($constpref.'_DBTRAPWOSRVDSC','Some servers always enable DB Layer trapping. It causes wrong detections as SQL Injection attack. If you got such errors, turn this option on. You should know this option weakens the security of DB Layer trapping anti-SQL-Injection.');

// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:53
define($constpref.'_DBLAYERTRAP','Enable DB Layer trapping anti-SQL-Injection');
define($constpref.'_DBLAYERTRAPDSC','Almost SQL Injection attacks will be canceled by this feature. This feature is required a support from databasefactory. You can check it on Security Advisory page.');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-21 04:44:31
define($constpref.'_DEFAULT_LANG','Default language');
define($constpref.'_DEFAULT_LANGDSC','Specify the language set to display messages before processing common.php');
define($constpref.'_BWLIMIT_COUNT','Bandwidth limitation');
define($constpref.'_BWLIMIT_COUNTDSC','Specify the max access to mainfile.php during watching time. This value should be 0 for normal environments which have enough CPU bandwidth. The number fewer than 10 will be ignored.');

// Appended by Xoops Language Checker -GIJOE- in 2007-07-30 16:31:33
define($constpref.'_BANIP_TIME0','Banned IP suspension time (sec)');
define($constpref.'_OPT_BIPTIME0','Ban the IP (moratorium)');
define($constpref.'_DOSOPT_BIPTIME0','Ban the IP (moratorium)');

// Appended by Xoops Language Checker -GIJOE- in 2007-04-11 05:08:26
define($constpref.'_ADMENU_MYBLOCKSADMIN','Permissions');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","����");

// A brief description of this module
define($constpref."_DESC","���� ������ �������� ��� ���� �� ���� XOOPS �� ���������� ���� ����, ����� ���: DoS, SQL Injection � ����� ����������.");

// Menu
define($constpref."_ADMININDEX","�������");
define($constpref."_ADVISORY","���������");
define($constpref."_PREFIXMANAGER","���������� ��������� ��");

// Configs
define($constpref.'_GLOBAL_DISBL','�������� ��������');
define($constpref.'_GLOBAL_DISBLDSC','��� ������� ������ �������� ���������.<br />�� �������� �������� �� ����� ���������� ����� ������� � �������������');

define($constpref.'_RELIABLE_IPS','���������� ������');
define($constpref.'_RELIABLE_IPSDSC','���������� ������ ��� ������ ��� ������� �������� ������������ �� ����������. ���������� ������ ����� ������ "|". "^" ������������� ������ ������, "$" ������������� ����� ������.');

define($constpref.'_LOG_LEVEL','������ �������');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_LOGLEVEL0','������ ��������');
define($constpref.'_LOGLEVEL15','������� �������');
define($constpref.'_LOGLEVEL63','������� �������');
define($constpref.'_LOGLEVEL255','��� �������');

define($constpref.'_HIJACK_TOPBIT','���������� ���� IP ��� ������');
define($constpref.'_HIJACK_TOPBITDSC','����-����� ������:<br />�������� �� ��������� 32 (���). 
 (��� ���� ��������)<br />����� ��� IP �� ��������, ���������� �������� IP ������ �����.<br />(������) ���� ��� IP ����� ��������� � �������� 192.168.0.0-192.168.0.255, ���������� 24 (���) �����');
define($constpref.'_HIJACK_DENYGP','������ ��� ������� ��������� ������ � ������ ����� ������ ���������');
define($constpref.'_HIJACK_DENYGPDSC','������� � ������������ ������:<br />
	�������� ������ ��� ������� ����� � �������� ����� ������ ���������.<br />
	(������������� ������ �������� � ������ ����� ������ ��������������� �����.)');
define($constpref.'_SAN_NULLBYTE','�������� ������ � ������� �����');
define($constpref.'_SAN_NULLBYTEDSC','����������� ������ "\\0" ����� ������������ � ��������� ����� ����.<br />
	���� ������ ����� ������� �� ������.<br />(������������� ������ �������� ������ ���������)');
define($constpref.'_DIE_NULLBYTE','�������� ������ � ������� �����');
define($constpref.'_DIE_NULLBYTEDSC','����������� ������ "\0" ����� ������������ � ��������� ����� ����.<br />(������������� ������ �������� ������ ���������)');
define($constpref.'_DIE_BADEXT','�������� ���������� ��� �������� �������� �����');
define($constpref.'_DIE_BADEXTDSC','� ������ ����� ���-���� ���������� ��������� �� ���� ���� ������� ������� ���������� (�������� .php) - �������� �������� ����� ��������. ���� ��� ����� ���������� ��������� ����� ����� (�������� ��� ������� B-Wiki ��� PukiWikiMod) - ��������� ������ ��������.');
define($constpref.'_CONTAMI_ACTION','�������� ��� ����������� "�������" ����������');
define($constpref.'_CONTAMI_ACTIONDS','�������� �������� ����������� � ������ ����� ���-���� �������� �������� ������ ������� "�������" ��������� ���������� XOOPS. (�������������: ������ �����)');
define($constpref.'_ISOCOM_ACTION','�������� ��� ����������� �������������� �����������');
define($constpref.'_ISOCOM_ACTIONDSC','�������� �������� ����������� ��� ����������� ������ "/*" ��� ������������.<br />"�������" ������������� ���������� ������������ �������� "*/".<br />(�������������: ��������)');
define($constpref.'_UNION_ACTION','�������� ��� ����������� ��������� ����� UNION');
define($constpref.'_UNION_ACTIONDSC','�������� �������� ����������� ��� ����������� ��������� ����� UNION. "�������" ������������ ��������� ���� ��������� ������� ����� "UNI-ON". (�������������: ��������)');
define($constpref.'_ID_INTVAL','�������������� �������������� ������������ ���������� (�������� id)');
define($constpref.'_ID_INTVALDSC','��� ������� ����: "*id" ����� ���������� ��� ����� �����.<br />���� �������� �������� ��� �� ��������� ����� XSS � SQL Injections ����.<br />
	������������� �������� ���� �������� � ��������� ������ ��� ������������� ������� � ������������� �����-���� �������.');
define($constpref.'_FILE_DOTDOT','������ �� Directroy Traversals');
define($constpref.'_FILE_DOTDOTDSC','������� ��� ��������� ������������������ ".." �� ���� �������� ���������� ��� Directory Traversals');

define($constpref.'_BF_COUNT','������ �� ������� ������');
define($constpref.'_BF_COUNTDSC','���������� ������������ ���������� ������� ����� ������������ �� 10 �����. � ������ ���� ���-���� ���������� ������������ ������� ��� ������� ���������� ��� - ��� ����� ����� ������� � ������ ������.');

define($constpref.'_DOS_SKIPMODS','���������� ������� �� DoS/Crawler ������');
define($constpref.'_DOS_SKIPMODSDSC','������� ����� ��������� ����������� �������� "|" ��� ������� � ������� ����� ��������� DoS/Crawler ������. ���� �������� � ��������� ������ �������� � ������� ���� � ������ ������� ��� ������� ������ ��������� � ���������� ������� �������� ������.');

define($constpref.'_DOS_EXPIRE','����� �������� ��� ����������� ������� �������� (���)');
define($constpref.'_DOS_EXPIREDSC','������ �������� ��������� ����� �������� �� ��������� �������� �������� �������� ("����� F5" � ������ ������������� ������)');

define($constpref.'_DOS_F5COUNT','������� ��� "����� F5"');
define($constpref.'_DOS_F5COUNTDSC','�������� �� DoS ����.<br />
	��� �������� ��������� ���������� �������� �������� ���������� �������� �� ������������ ����� ����� �������� ������������ ��� �������������� �����.');
define($constpref.'_DOS_F5ACTION','�������� ��� ����������� ������� ���������� �������');

define($constpref.'_DOS_CRCOUNT','������� ��� �������');
define($constpref.'_DOS_CRCOUNTDSC','������������� ������� �������� ������� �������� ��������� ������. �������� �������� ������ ���������� �������� ���������� �������� �� ������������ ����� ����� �������� ������������ ��� ��������� "������������" �������');
define($constpref.'_DOS_CRACTION','�������� ��� ����������� "������" �������.');

define($constpref.'_DOS_CRSAFE','������ ������������ (User-Agent) �� ������������ ��� "������"');
define($constpref.'_DOS_CRSAFEDSC','���������� ��������� perl ��� ���� ������ ������������ (User-Agent).<br />� ������ ���������� ������ ���������� � �������� ���������� - ����� ������� �� ������������ ��� "������".<br />������: /(msnbot|Googlebot|Yandex|Yahoo! Slurp|StackRambler)/i');

define($constpref.'_OPT_NONE','������ (������ ������ � �������)');
define($constpref.'_OPT_SAN','�������');
define($constpref.'_OPT_EXIT','������ �����');
define($constpref.'_OPT_BIP','�������� ����� � ������ ������');

define($constpref.'_DOSOPT_NONE','������ (������ ������ � �������)');
define($constpref.'_DOSOPT_SLEEP','�������');
define($constpref.'_DOSOPT_EXIT','������ �����');
define($constpref.'_DOSOPT_BIP','�������� ����� � ������ ������');
define($constpref.'_DOSOPT_HTA','��������� ������ ��������� .htaccess (����������������)');

define($constpref.'_BIP_EXCEPT','����� ������������� ������� �� ���������� � ������ ������.');
define($constpref.'_BIP_EXCEPTDSC','������������� ������ ��������� � ���� ������ ������ ��������������� �����.');

define($constpref.'_DISABLES','�������������� ������������ ������� ������� XOOPS');

define($constpref.'_BIGUMBRELLA','�������� anti-XSS (BigUmbrella)');
define($constpref.'_BIGUMBRELLADSC','��� �������� �������� ��� �� ��������� ����� ���������� XSS. �������� �� 100%!!');

define($constpref.'_SPAMURI4U','anti-SPAM: ����������� ������ ��� �������������');
define($constpref.'_SPAMURI4UDSC','���� ����������� ������  � ���������� �� ������������� (����� ���������������), ��������� ���������, ��������� ������������ ��� ����.<br /> 0 - ���������.');
define($constpref.'_SPAMURI4G','anti-SPAM: ����������� ������ ��� ������');
define($constpref.'_SPAMURI4GDSC','���� ����������� ������  � ���������� �� ������, ��������� ���������, ��������� ������������ ��� ����.<br />  0 - ���������.');

}

?>
