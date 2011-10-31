<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: keitai.skin.php,v 1.34 2010/07/25 06:51:38 nao-pon Exp $
// Copyright (C) 2003-2006 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Skin for Embedded devices

// ----
// Prohibit direct access
if (! isset($this->cont['UI_LANG'])) die('UI_LANG is not set');


$style = array(
	'siteTitle' => 'text-align:center;background-color:#A6DDAF;font-size:large',
	'easyLogin' => 'text-align:center;background-color:#DBBCA6;font-size:small',
	'wikiTitle' => 'text-align:center;background-color:#A6D5DB;font-size:large',
	'pageTitle' => 'text-align:center;background-color:#EAFFCC',
	'pageMenu'  => 'background-color:#CED9DB;font-size:small',
	'pageFooter'=> 'background-color:#CED9DB;font-size:small',
	'pageInfo'  => 'background-color:#EAFFCC;font-size:small',
);

/////////////////////////////////////////////////
// xpWiki run mode
if (! defined('HYP_WIZMOBILE_USE') && strtolower($this->root->keitai_output_filter) !== 'pass') {
	$this->root->runmode = 'standalone';
}

$pagename = (isset($this->root->vars['page']))? $this->root->vars['page'] : '';
$pageno = (isset($this->root->vars['p']) && is_numeric($this->root->vars['p'])) ? $this->root->vars['p'] : 0;
$edit = (isset($this->root->vars['cmd']) && $this->root->vars['cmd'] === 'edit') ||
	(isset($this->root->vars['plugin']) && $this->root->vars['plugin'] === 'edit');
$read = (isset($this->root->vars['cmd']) && $this->root->vars['cmd'] === 'read') ||
	(isset($this->root->vars['plugin']) && $this->root->vars['plugin'] === 'read');
$this->root->max_size = $this->root->max_size * 1024 - 500; // Make 500bytes spare for HTTP Header & Pageing navi.
$link = $_LINK;
$lang = $_LANG['skin'];
$rw = ! $this->cont['PKWK_READONLY'];
$dirname = $this->root->mydirname;

$no_accesskey = isset($this->root->rtf['no_accesskey']);

foreach($lang as $key => $val) {
	if (isset($lang[$key.'_s'])) {
		$lang[$key] = $lang[$key.'_s'];
	}
}

// ----
// Modify
$heads = array();

if ($subnote && $subnote = $this->do_plugin_inline('subnote', 'format:%s', 'Note|Main')) {
	$heads[] = $subnote;
}
if ($page_comments_count) {
	$heads[] = $page_comments_count;
}

if ($page_comments) {
	$body .= '<hr>' . $page_comments;
}

if ($heads) {
	$body ='<div style="text-align:right;font-size:x-small">[ ' . join(' ', $heads) . ' ]</div><hr>' . $body;
}

// Ignore _symbol_anchor
$body = preg_replace('#<a[^>]+?>' . preg_quote($this->root->_symbol_anchor, '#') . '</a>#S', '', $body);
$body = preg_replace('/<a href="#'.$this->root->mydirname.'_navigator"[^>]*?>.+?<\/a>/sS', '', $body);

$body = str_replace($this->root->_symbol_noexists, '<span style="font-size:xx-small">[emj:1014]</span>', $body);

$header = '';

if (! $no_accesskey && $this->root->runmode === 'standalone') {
	$header .= sprintf('<div style="%s" id="header">%s <a href="%s" %s="1">%s</a></div>',
		$style['siteTitle'],
		$this->make_link('&pb1;'),
		$this->cont['ROOT_URL'],
		$this->root->accesskey,
		htmlspecialchars($this->root->siteinfo['sitename']) );

	$header .= sprintf('<div style="%s">%s</div>',
		$style['easyLogin'],
		$this->do_plugin_convert('easylogin') );

	$header .= sprintf('<div style="%s">%s <a href="%s" %s="3">%s</a><a href="%s">%s</a></div>',
		$style['wikiTitle'],
		$this->make_link('&pb3;'),
		$link['top'],
		$this->root->accesskey,
		htmlspecialchars($this->root->module['title']),
		$link['rss'],
		'((e:f699))' );
}

if ($read && $pagename !== '') {
	$pageTitle = $this->make_pagelink($pagename) . '<a href="' . $link['related'] . '">[emj:119]</a>';
} else {
	$pageTitle = strip_tags($this->xpwiki->title);
}
$header .= sprintf('<div style="%s">%s</div>',
	$style['pageTitle'],
	$pageTitle );

if (! $no_accesskey) {
	$header .= '<div style="' . $style['pageMenu'] . '">';
	$header .= '<table><tr><td>';
	$header .= '<div style="' . $style['pageMenu'] . '">';

	$header .= sprintf('%s <a href="#header" %s="2">%s</a><br />',
	$this->make_link('&pb2;'),
	$this->root->accesskey,
	$lang['header']
	);

	$header .= sprintf('%s <a href="#footer" %s="8">%s</a><br />',
	$this->make_link('&pb8;'),
	$this->root->accesskey,
	$lang['footer']
	);

	if ($pagename !== '') {
		$header .= sprintf('%s <a href="%s?cmd=menu&amp;refer=%s" %s="5">%s</a><br />',
		$this->make_link('&pb5;'),
		$this->root->script,
		rawurlencode($pagename),
		$this->root->accesskey,
		$lang['menu']
		);
	} else {
		$header .= '<br />';
	}

	if (!$is_freeze && $is_editable) {
		$header .= sprintf('%s <a href="%s" %s="9">%s</a><br />',
		$this->make_link('&pb9;'),
		$link['edit'],
		$this->root->accesskey,
		$lang['edit']
		);
	} else {
		$header .= '<br />';
	}
	$header .= '</div>';
	$header .= '</td><td style="background-color:#fff"> </td><td>';
	$header .= '<div style="' . $style['pageMenu'] . ';text-align:right">';

	$header .= sprintf('<a href="%s" %s="7">%s</a> %s<br />',
	$link['new'],
	$this->root->accesskey,
	$lang['new'],
	$this->make_link('&pb7;') );

	$header .= sprintf('<a href="%s" %s="*">%s</a> %s<br />',
	$link['search'],
	$this->root->accesskey,
	$lang['search'],
	'[*]' );

	$header .= sprintf('<a href="%s" %s="0">%s</a> %s<br />',
	$link['recent'],
	$this->root->accesskey,
	$lang['recent'],
	$this->make_link('&pb0;') );

	$header .= sprintf('<a href="%s" %s="#">%s</a> %s<br />',
	$link['list'],
	$this->root->accesskey,
	$lang['list'],
	$this->make_link('&pb#;') );

	$header .= '</div>';
	$header .= '</td></tr></table>';
	$header .= '</div>';
}

$footnotes = '<hr />';
if ($notes) {
	$footnotes = '<div>' . $notes . '</div><hr>';
}

// page info
$pageinfo = '';
if ($is_page) {
	$pageinfo = <<<EOD
<div style="{$style['pageInfo']}">
<h4>{$lang['pageinfo']}</h4>
{$lang['pagename']} : $_page<br />
{$lang['pagealias']} : {$pginfo['alias']}<br />
{$lang['pageowner']} : {$pginfo['pageowner']}
<h4>{$lang['readable']}</h4>
{$lang['groups']} : {$pginfo['readableGroups']}<br />
{$lang['users']} : {$pginfo['readableUsers']}
<h4>{$lang['editable']}</h4>
{$lang['groups']} : {$pginfo['editableGroups']}<br />
{$lang['users']} : {$pginfo['editableUsers']}
</div>
EOD;
}


// Build footer
ob_start(); ?>
<div style="<?php echo $style['pageFooter'] ?>" id="footer">
<?php echo $footnotes ?>
<?php if ($is_page) echo $this->do_plugin_convert('counter') ?>
<?php if ($lastmodified != '') { ?>
<div><?php echo $lang['lastmodify'] ?>: <?php echo $lastmodified ?> by <?php echo $pginfo['lastuname'] ?></div>
<?php } ?>
<p><?php echo $lang['siteadmin'] ?>: <a href="<?php echo $this->root->modifierlink ?>"><?php echo $this->root->modifier ?></a></p>
</div>
<?php
$footer = ob_get_contents();
ob_end_clean();

if ($this->root->runmode === 'standalone') {
	$ctype = 'text/html';
	if (HypCommonFunc::get_version() >= '20080617.2') {
		HypCommonFunc::loadClass('HypKTaiRender');
		if (HypCommonFunc::get_version() >= '20080925') {
			$r =& HypKTaiRender::getSingleton();
		} else {
			$r = new HypKTaiRender();
		}
		$r->set_myRoot($this->root->siteinfo['host']);
		$r->Config_hypCommonURL = $this->cont['ROOT_URL'] . 'class/hyp_common';
		$r->Config_redirect = $this->root->k_tai_conf['redirect'];
		$r->Config_emojiDir = $this->cont['ROOT_URL'] . 'images/emoji';
		if (! empty($this->root->k_tai_conf['showImgHosts'])) {
			$r->Config_showImgHosts = $this->root->k_tai_conf['showImgHosts'];
		}
		if (! empty($this->root->k_tai_conf['directImgHosts'])) {
			$r->Config_directImgHosts = $this->root->k_tai_conf['directImgHosts'];
		}
		if (! empty($this->root->k_tai_conf['directLinkHosts'])) {
			$r->Config_directLinkHosts = $this->root->k_tai_conf['directLinkHosts'];
		}
		if ($this->cont['PKWK_ENCODING_HINT']) {
			$r->Config_encodeHintWord = $this->cont['PKWK_ENCODING_HINT'];
		}
		if (! empty($this->root->k_tai_conf['icon'])) {
			$r->Config_icons = array_merge($r->Config_icons, $this->root->k_tai_conf['icon']);
		}
		if (! empty($this->root->k_tai_conf['getKeys'])) {
			$r->pagekey = $this->root->k_tai_conf['getKeys']['page'];
			$r->hashkey = $this->root->k_tai_conf['getKeys']['hash'];
		}
		if (! empty($this->root->k_tai_conf['pictSizeMax'])) {
			$r->Config_pictSizeMax = $this->root->k_tai_conf['pictSizeMax'];
		}
		if (! empty($this->root->k_tai_conf['docomoGuidTTL'])) {
			$r->Config_docomoGuidTTL = $this->root->k_tai_conf['docomoGuidTTL'];
		}
		if (! empty($this->root->k_tai_conf['urlRewrites'])) {
			$r->marge_urlRewites('urlRewrites', $this->root->k_tai_conf['urlRewrites']);
		}
		if (! empty($this->root->k_tai_conf['urlImgRewrites'])) {
			$r->marge_urlRewites('urlImgRewrites', $this->root->k_tai_conf['urlImgRewrites']);
		}
		if (! empty($this->root->k_tai_conf['googleAdsense']['config'])) {
			$r->Config_googleAdSenseConfig = $this->root->k_tai_conf['googleAdsense']['config'];
			$r->Config_googleAdSenseBelow = $this->root->k_tai_conf['googleAdsense']['below'];
		}

		$googleAnalytics = '';
		if ($this->root->k_tai_conf['googleAnalyticsId']) {
			$googleAnalytics = $r->googleAnalyticsGetImgTag($this->root->k_tai_conf['googleAnalyticsId'], $title);
		}

		$r->inputEncode = $this->cont['SOURCE_ENCODING'];
		$r->outputEncode = $this->root->keitai_output_filter;
		$r->outputMode = 'xhtml';
		$r->langcode = $this->cont['LANG'];

		if (! empty($_SESSION['hyp_redirect_message'])){
			$header = $this->root->k_tai_conf['rebuilds']['redirectMessage']['above'] . $_SESSION['hyp_redirect_message'] . $this->root->k_tai_conf['rebuilds']['redirectMessage']['below'] . $header;
			unset($_SESSION['hyp_redirect_message']);
		}

		$r->contents['header'] = $header . $googleAnalytics;
		$r->contents['body'] = $body . $pageinfo;
		$r->contents['footer'] = $footer;

		$r->doOptimize();

		$charset = (strtoupper($r->outputEncode) === 'UTF-8')? 'UTF-8' : 'Shift_JIS';

		if (method_exists($r, 'getHtmlDeclaration')) {
			$htmlDec = $r->getHtmlDeclaration();
		} else {
			$htmlDec = '<?xml version="1.0" encoding="Shift_JIS"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">';
		}
		$body = $r->outputBody;
		$ctype = $r->getOutputContentType();
		$r = NULL;
		unset($r);
	} else {
		$body = '"keitai.skin" require HypCommonFunc >= 20080617';
	}

	$head = '<head><title>' . mb_convert_encoding($title, $this->root->keitai_output_filter, $this->cont['SOURCE_ENCODING']) . '</title></head>';

	$out = $htmlDec . $head . '<body>' .  $body . '</body></html>';

	// ----
	// Output HTTP headers
	$this->pkwk_headers_sent();
	// Force Shift JIS encode for Japanese embedded browsers and devices
	header('Content-Type: '.$ctype.'; charset=' . $charset);
	header('Content-Length: ' . strlen($out));
	header('Cache-Control: no-cache');

	// Output
	echo $out;
} else {
	echo $header . $body . $pageinfo . $footer;
}
