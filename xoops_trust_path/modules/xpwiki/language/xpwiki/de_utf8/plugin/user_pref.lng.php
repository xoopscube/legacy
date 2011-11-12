<?php
/*
 * Created on 2008/01/24 by nao-pon http://hypweb.net/
 * $Id: user_pref.lng.php,v 1.3 2010/06/23 08:02:52 nao-pon Exp $
 */

$msg = array(
	'title_form' => 'User preference',
	'title_done' => 'Saved user preference',
	'btn_submit' => 'Apply this setting',
	'msg_done' => 'Saved it by the following setting.',
	'title_description' => 'Explanation of user preference',
	'msg_description' => '<p>In this user setting, it is possible to set it about each user.</p>',

	'Yes' => 'Yes',
	'No' => 'No',

	'twitter_access_token' => array(
		'caption'     => 'Twitter accesstoken',
		'description' => 'The access key to cooperate with Twitter.<br />' .
				'Please display this page again after doing coordinated release by a coordinated application program of the site of Twitter to release cooperation and click "Apply this setting". ',
	),

	'twitter_access_token_secret' => array(
		'caption'     => 'Twitter access_token secret',
		'description' => '<a href="{$root->twitter_request_link}">Please click here to acquire the access key and approve on the site of Twitter.</a> ' .
				'Please click "Apply this setting" when it returns to this page after it permits.',
	),

	'amazon_associate_tag' => array(
		'caption'     => 'Amazon associate ID',
		'description' => 'When associate ID is registered, this ID is used for an Amazon plug-in on the page that you made.',
	),

	'moblog_mail_address' => array(
		'caption'     => 'Your mail address for "moblog"',
		'description' => 'Setting of your mail address used for moblog.<br />The address of the moblog is "{$root->moblog_pop_mail}". ',
	),

	'moblog_base_page' => array(
		'caption'     => 'Base page name for "moblog"',
		'description' => 'Registration of base page name that moblog preserves. ',
	),

	'moblog_user_mail' => array(
		'caption'     => 'Mail address for your moblog',
		'description' => '<img src="http://chart.apis.google.com/chart?chs=100x100&cht=qr&chl={$root->moblog_user_mail_rawurlenc}" width="100" height="100" alt="{$root->moblog_user_mail}" align="left" />Your moblog destination mail address is "<a href="mailto:{$root->moblog_user_mail}">{$root->moblog_user_mail}</a>".<br />' .
				'The E-mail transmitted to this mail address appropriating is always treated as a contribution from you. Therefore, do not inform others of it.<br />' .
				'If "Moblog page" is emptied once and registered, it becomes a new mail address to change this mail address.',
	),

	'moblog_auth_code' => array(
		'caption'     => 'Moblog validation code [numerical](optional)',
		'description' => 'When the moblog validation code is set, E-mail that there is no "*VALIDATION CODE" in the head of the subject of mail is annulled<br />' .
				'Example:When the validation code is assumed to be "1234", the mail subject is assumed to be "*1234 CONTRIBUTION SUBJECT". Do not put the blank between "*" and the numerical value.',
	),

	'moblog_to_twitter' => array(
		'caption'     => 'The moblog contribution is notified to Twitter.',
		'description' => 'Page name, title, and link to blog is twiited by your Twitter account.',
	),

	'xmlrpc_pages' => array(
		'caption'     => 'Blog page name for XML-RPC',
		'description' => 'Blog page name used by XML-RPC client that supports MetaWeblog API and the correspondence service.<br />' .
				'Plurals can be specified by each line.<br />' .
				'XML-RPC API end-point is "<a href="{$root->script}{$root->xmlrpc_endpoint}" target="_blank"> {$root->script}{$root->xmlrpc_endpoint} </a>"',
	),

	'xmlrpc_auth_key' => array(
		'caption'     => 'XML-RPC authentication key (password)',
		'description' => 'It is a password set to service for XML-RPC.',
	),

	'xmlrpc_to_twitter' => array(
		'caption'     => 'The XML-RPC contribution is notified to Twitter.',
		'description' => 'Page name, title, and link to blog is twiited by your Twitter account.',
	),

);
?>