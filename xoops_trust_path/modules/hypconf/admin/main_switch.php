<?php
/*
 * Created on 2011/11/09 by nao-pon http://xoops.hypweb.net/
 * $Id: main_switch.php,v 1.1 2011/11/10 12:31:33 nao-pon Exp $
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
