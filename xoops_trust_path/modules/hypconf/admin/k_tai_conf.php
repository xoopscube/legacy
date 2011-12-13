<?php
/*
 * Created on 2011/11/09 by nao-pon http://xoops.hypweb.net/
 * $Id: k_tai_conf.php,v 1.4 2011/12/13 08:12:18 nao-pon Exp $
 */

$config['main_switch'] = 'use_k_tai_render';

$config[] = array(
	'name' => 'ua_regex',
	'title' => $constpref.'_UA_REGEX',
	'description' => $constpref.'_UA_REGEX_DESC',
	'formtype' => 'textarea',
	'valuetype' => 'text',
	'size' => 80
	);
$config[] = array(
	'name' => 'jquery_profiles',
	'title' => $constpref.'_JQM_PROFILES',
	'description' => $constpref.'_JQM_PROFILES_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text'
	);
$config[] = array(
	'name' => 'jquery_theme',
	'title' => $constpref.'_JQM_THEME',
	'description' => $constpref.'_JQM_THEME_DESC',
	'formtype' => 'select',
	'valuetype' => 'text',
	'options' => array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','x','y','z')
	);
$config[] = array(
	'name' => 'jquery_theme_content',
	'title' => $constpref.'_JQM_THEME_CONTENT',
	'description' => $constpref.'_JQM_THEME_CONTENT_DESC',
	'formtype' => 'select',
	'valuetype' => 'text',
	'options' => array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','x','y','z')
	);
$config[] = array(
	'name' => 'jquery_theme_block',
	'title' => $constpref.'_JQM_THEME_BLOCK',
	'description' => $constpref.'_JQM_THEME_BLOCK_DESC',
	'formtype' => 'select',
	'valuetype' => 'text',
	'options' => array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','x','y','z')
	);
$config[] = array(
	'name' => 'jqm_css',
	'title' => $constpref.'_JQM_CSS',
	'description' => $constpref.'_JQM_CSS_DESC',
	'formtype' => 'textarea',
	'valuetype' => 'file:jqm-custom.css',
	'size' => 80
	);
$config[] = array(
	'name' => 'jquery_remove_flash',
	'title' => $constpref.'_JQM_REMOVE_FLASH',
	'description' => $constpref.'_JQM_REMOVE_FLASH_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text'
	);
$config[] = array(
	'name' => 'jquery_resolve_table',
	'title' => $constpref.'_JQM_RESOLVE_TABLE',
	'description' => $constpref.'_JQM_RESOLVE_TABLE_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int'
	);
$config[] = array(
	'name' => 'jquery_image_convert',
	'title' => $constpref.'_JQM_IMAGE_CONVERT',
	'description' => $constpref.'_JQM_IMAGE_CONVERT_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'size' => 4
	);
$config[] = array(
	'name' => 'disabledBlockIds',
	'title' => $constpref.'_DISABLEDBLOCKIDS',
	'description' => $constpref.'_DISABLEDBLOCKIDS_DESC',
	'formtype' => 'check',
	'valuetype' => 'array',
	'options' => 'blocks',
	);
$config[] = array(
	'name' => 'limitedBlockIds',
	'title' => $constpref.'_LIMITEDBLOCKIDS',
	'description' => $constpref.'_LIMITEDBLOCKIDS_DESC',
	'formtype' => 'check',
	'valuetype' => 'array',
	'options' => 'blocks'
	);
$config[] = array(
	'name' => 'showBlockIds',
	'title' => $constpref.'_SHOWBLOCKIDS',
	'description' => $constpref.'_SHOWBLOCKIDS_DESC',
	'formtype' => 'check',
	'valuetype' => 'array',
	'options' => 'blocks'
	);
