<?php
/*
 * hdinstaller-ini.php
 *
 * update:
 */
$config = array(
	'use_gettext' => 1,
	
	'allow_language' => array(
		'ja' => array(
			'lang' => 'ja_JP',
			'name' => '日本語',
			),
		'en' => array(
			'lang' => 'en_US',
			'name' => 'English',
			),
		),
    // log
    'log_facility'          => 'echo',
    'log_level'             => 'fatal',
    'log_option'            => 'pid,function,pos',
    'log_filter_do'         => '',
    'log_filter_ignore'     => 'Undefined index.*%%.*tpl',
);
?>
