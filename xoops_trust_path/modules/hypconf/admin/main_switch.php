<?php
/*
 * Created on 2011/11/09 by nao-pon http://xoops.hypweb.net/
 * $Id: main_switch.php,v 1.2 2011/12/13 08:12:18 nao-pon Exp $
 */

$config[] = array(
	'name' => 'use_set_query_words',
	'title' => $constpref.'_USE_SET_QUERY_WORDS',
	'description' => $constpref.'_USE_SET_QUERY_WORDS_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0,
	);
$config[] = array(
	'name' => 'use_words_highlight',
	'title' => $constpref.'_USE_WORDS_HIGHLIGHT',
	'description' => $constpref.'_USE_WORDS_HIGHLIGHT_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0,
	);
$config[] = array(
	'name' => 'use_proxy_check',
	'title' => $constpref.'_USE_PROXY_CHECK',
	'description' => $constpref.'_USE_PROXY_CHECK_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0,
	);
$config[] = array(
	'name' => 'input_filter_strength',
	'title' => $constpref.'_INPUT_FILTER_STRENGTH',
	'description' => $constpref.'_INPUT_FILTER_STRENGTH_DESC',
	'formtype' => 'select',
	'valuetype' => 'int',
	'options' => array(array('confop_value' => 0, 'confop_name' => $constpref.'_INPUT_FILTER_STRENGTH_0'),
	                   array('confop_value' => 1, 'confop_name' => $constpref.'_INPUT_FILTER_STRENGTH_1'),
	                   array('confop_value' => 2, 'confop_name' => $constpref.'_INPUT_FILTER_STRENGTH_2')),
	);
$config[] = array(
	'name' => 'use_dependence_filter',
	'title' => $constpref.'_USE_DEPENDENCE_FILTER',
	'description' => $constpref.'_USE_DEPENDENCE_FILTER_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0,
	);
$config[] = array(
	'name' => 'use_post_spam_filter',
	'title' => $constpref.'_USE_POST_SPAM_FILTER',
	'description' => $constpref.'_USE_POST_SPAM_FILTER_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0,
	);
$config[] = array(
	'name' => 'post_spam_trap_set',
	'title' => $constpref.'_POST_SPAM_TRAP_SET',
	'description' => $constpref.'_POST_SPAM_TRAP_SET_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0,
	);
$config[] = array(
	'name' => 'use_k_tai_render',
	'title' => $constpref.'_USE_K_TAI_RENDER',
	'description' => $constpref.'_USE_K_TAI_RENDER_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0,
	);
$config[] = array(
	'name' => 'use_smart_redirect',
	'title' => $constpref.'_USE_SMART_REDIRECT',
	'description' => $constpref.'_USE_SMART_REDIRECT_DESC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => 0,
	);
