<?php
// xpWiki runmode
$this->root->runmode = "standalone";

// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: print.skin.php,v 1.2 2009/05/02 03:50:44 nao-pon Exp $
// Copyright (C)
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki default skin

// ------------------------------------------------------------
// Settings (define before here, if you want)

// Set site identities
$this->root->_IMAGE['skin']['logo']     = 'pukiwiki.png';
$this->root->_IMAGE['skin']['favicon']  = ''; // Sample: 'image/favicon.ico';

// $this->cont['SKIN_DEFAULT_DISABLE_TOPICPATH']
//   1 = Show reload URL
//   0 = Show topicpath
if (! isset($this->cont['SKIN_DEFAULT_DISABLE_TOPICPATH']))
	$this->cont['SKIN_DEFAULT_DISABLE_TOPICPATH'] =  0; // 1, 0

// Show / Hide navigation bar UI at your choice
// NOTE: This is not stop their functionalities!
if (! isset($this->cont['PKWK_SKIN_SHOW_NAVBAR']))
	$this->cont['PKWK_SKIN_SHOW_NAVBAR'] =  1; // 1, 0

// Show / Hide toolbar UI at your choice
// NOTE: This is not stop their functionalities!
if (! isset($this->cont['PKWK_SKIN_SHOW$toolbar']))
	$this->cont['PKWK_SKIN_SHOW$toolbar'] =  1; // 1, 0

// ------------------------------------------------------------
// Code start

// Prohibit direct access
if (! isset($this->cont['UI_LANG'])) die('UI_LANG is not set');
if (! isset($this->root->_LANG)) die('$_LANG is not set');
if (! isset($this->cont['PKWK_READONLY'])) die('PKWK_READONLY is not set');

$lang  = & $this->root->_LANG['skin'];
$link  = & $this->root->_LINK;
$image = & $this->root->_IMAGE['skin'];
$rw    = ! $this->cont['PKWK_READONLY'];
$can_attach = ($rw && $this->is_page($_page) && (!$this->cont['ATTACH_UPLOAD_EDITER_ONLY'] || $is_editable) && (bool)ini_get('file_uploads'));
$nolinks = (! empty($this->root->get['print_nolinks']));
$nocomments = (! empty($this->root->get['print_nocomments']));
$hasComments = ($page_comments && $this->count_page_comments($_page));

if (! $hasComments) {
	$page_comments = '';
}

$nolinks_org = ($nolinks)? '&amp;print_nolinks=1' : '';
$nocomments_org = $hasComments? '&amp;print_nocomments=1' : '';

// Decide charset for CSS
$css_charset = $this->cont['CSS_CHARSET'];

$favicon = ($image['favicon'])? "<link rel=\"SHORTCUT ICON\" href=\"{$image['favicon']}\" />" : "";
$dirname = $this->root->mydirname;

$head_tag .= ! empty($this->root->head_tags) ? "\n". join("\n", $this->root->head_tags) ."\n" : '';
$head_pre_tag .= ! empty($this->root->head_pre_tags) ? "\n". join("\n", $this->root->head_pre_tags) ."\n" : '';

$pre_width = 'auto';

$backURL = preg_replace('/(\?|&amp;)print=1/', '', $link['print']);

$thisHost = $this->root->siteinfo['host'];

// Make links list
$links = '';
if (! $nolinks) {
	$switchLinksURL = $link['print'] . '&amp;print_nolinks=1' . $nocomments_org;
	$switchLinks = '&minus; Links';
	$switchLinksCss = '';
	
	$this->root->rtf['SkinLinks'] = $this->root->rtf['SkinTexts'] = array();
	$linkReg = '#(<a ([^>]*?)>)(.+?)</a>#';
	$body = preg_replace_callback($linkReg, array(& $this, 'skin_link_extractor'), $body);
	$this->root->rtf['SkinLinks'] = array_flip($this->root->rtf['SkinLinks']);
	$links = array();
	ksort($this->root->rtf['SkinLinks']);
	foreach($this->root->rtf['SkinLinks'] as $i => $_link) {
		if ($_link[0] === '/') {
			$_link = $this->root->siteinfo['host'] . $_link;
		}
		$_link = htmlspecialchars(str_replace('&amp;', '&', $_link));
		$_link = preg_replace('#^'.preg_quote($this->root->siteinfo['host'], '#').'#', '<sup class="links">(This host)</sup>', $_link);
		//$links[] = '<sup class="links">['.$i.']</sup> ' . htmlspecialchars(str_replace('&amp;', '&', $link));
		$links[] = $_link;
	}
	if ($links) {
		$thisHost = '(This host) = ' . htmlspecialchars($this->root->siteinfo['host']);
		//$links = '<hr /><dl><dt>Links list <sub>'.$thisHost.'</sub><dt><dd>' . join('<br />', $links) . '</dd></dl>';
		$links = '<hr /><h2>Links list</h2><p><small>'.$thisHost.'</small></p><ol class="list1 links"><li>' . join('</li><li>', $links) . '</li></ol>';
	} else {
		$links = '';
	}
} else {
	$switchLinksURL = $link['print'] . $nocomments_org;
	$switchLinks = '+ Links';
	$switchLinksCss = '<link rel="stylesheet" type="text/css" media="all" href="'.$this->cont['LOADER_URL'].'?src=main_print_nolinks.css" charset="'.$css_charset.'" />';
}
if ($hasComments) {
	if (! $nocomments) {
		$switchCommentsURL = $link['print'] . '&amp;print_nocomments=1' . $nolinks_org;
		$switchComments = '&minus; Comments';
	} else {
		$switchCommentsURL = $link['print'] . $nolinks_org;
		$switchComments = '+ Comments';
		
		$page_comments = '';
	}
}

//$body = preg_replace('/[a-zA-Z0-9]/', '$0&#8203;', $body);
//$body = HypCommonFunc::html_wordwrap($body);

/*
$body = preg_replace_callback('/(<pre[^>]*?>)(.*?)(</pre>)/',
create_function('$arg',
	'$arg[2] = preg_replace_callback(\'/(<[^>]*>)|([!=\x23-\x3b\x3f-\x7e])/isS\',
	create_function(\\\'$arg\\\',
		\\\'if ($arg[1]) { return $arg[1]; } else { return $arg[2] . "&#8203;";}\\\'
	),$arg[2]);'
), $body);
*/

/*
$body = preg_replace_callback('/(<(script|textarea|style|option).*?<\/\\2>|<[^>]*>)|((?>&#?[a-z0-9]+;|\(\([eis]:[0-9a-f]{4}\)\)|[!=\x23-\x3b\x3f-\x7e]){36})/isS',
create_function('$arg',
	'if ($arg[1]) { return $arg[1]; } else { return $arg[3] . "&#8203;";}'
),$body);
*/

$js = <<<EOD
<script>
if (! Prototype.Browser.IE) {
(function () {
	var resolver = document.createNSResolver(document.documentElement);
	var nodes = document.evaluate(
			'/descendant::*[not(contains(" TITLE STYLE SCRIPT TEXTAREA XMP ", concat(" ", local-name(), " ")))]/child::text()',
			document.documentElement,
			resolver,
			XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,
			null
		);
	var regexp = new RegExp("([!-%'-/:=\\?@\\[-`\\{-~]|&amp;)");
	var range  = document.createRange();
	var wbr    = document.createElement('wbr');
	var lastIndex;
	var node;
	for (var i = 0; i < nodes.snapshotLength; i++)
	{
		node = nodes.snapshotItem(i);
		range.selectNode(node);
		while (node && (lastIndex = range.toString().search(regexp)) > -1)
		{
			range.setStart(node, lastIndex+RegExp.$1.length);
			range.insertNode(wbr.cloneNode(true));
			node = node.nextSibling.nextSibling;
			range.selectNode(node);
		}
	}
	range.detach();
})();
}
</script>
EOD;



// ------------------------------------------------------------
// Output

// HTTP headers
$this->pkwk_common_headers();
header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=' . $this->cont['CONTENT_CHARSET']);

// HTML DTD, <html>, and receive content-type
if (isset($this->root->pkwk_dtd)) {
	$meta_content_type = $this->pkwk_output_dtd($this->root->pkwk_dtd);
} else {
	$meta_content_type = $this->pkwk_output_dtd();
}?>
<head>
 <?php echo $meta_content_type?>
 <meta http-equiv="content-style-type" content="text/css" />
 <meta name="robots" content="NOINDEX,NOFOLLOW" />
<?php if ($this->cont['PKWK_ALLOW_JAVASCRIPT'] && isset($this->root->javascript)) {?> <meta http-equiv="Content-Script-Type" content="text/javascript" /><?php }?>

 <title><?php echo htmlspecialchars($this->root->pagetitle) ?> - <?php echo $this->root->siteinfo['sitename'] ?></title>

<?php echo $head_pre_tag?>
<?php echo <<<EOD
 $favicon
 <link rel="stylesheet" type="text/css" media="all" href="{$this->cont['LOADER_URL']}?skin={$this->cont['SKIN_NAME']}&amp;charset={$css_charset}&amp;pw={$this->root->pre_width}&amp;src=main.css" charset="{$css_charset}" />
 <link rel="stylesheet" type="text/css" media="all" href="{$this->cont['LOADER_URL']}?skin={$this->cont['SKIN_NAME']}&amp;charset={$css_charset}&amp;pw={$this->root->pre_width}&amp;media=print&amp;src=main.css" charset="{$css_charset}" />
 {$switchLinksCss}
 <link rel="alternate" type="application/rss+xml" title="RSS" href="{$link['rss']}" />
EOD;
?>

<?php echo $head_tag?>
 <script>XpWiki.printing = true;</script>
 <style type="text/css" media="print">
 <!--
 body {
 	width: auto;
 }
 div.printButton {
   display: none;
 }
 -->
 </style>
</head>
<body>
<div class="xpwiki_<?php echo $dirname ?>">

<div class="printButton">
<p><span class="button"><a href="<?php echo $backURL ?>"><?php echo $lang['topage'] ?></a></span></p>
<?php if ($links || $nolinks) { ?>
<p><span class="button"><a href="<?php echo $switchLinksURL ?>"><?php echo $switchLinks ?></a></span></p>
<?php } ?>
<?php if ($hasComments) { ?>
<p><span class="button"><a href="<?php echo $switchCommentsURL ?>"><?php echo $switchComments ?></a></span></p>
<?php } ?>
<p><span class="button" onclick="window.print()">&nbsp;<?php echo $lang['print_s'] ?>&nbsp;</span></p>
</div>

<div class="header" id="<?php echo $dirname ?>_header">
 <a href="<?php echo $link['top']?>"><img class="logo" name="logo" src="<?php echo $this->cont['IMAGE_DIR'] . $image['logo']?>" width="80" height="80" alt="[PukiWiki]" title="[PukiWiki]" /></a>

 <h1 class="title"><?php echo $page?> :: <a href="<?php echo $this->root->siteinfo['rooturl'] ?>" title="Site Top"><?php echo $this->root->siteinfo['sitename'] ?></a></h1>

<?php if ($is_page) {?>
 <?php if($this->cont['SKIN_DEFAULT_DISABLE_TOPICPATH']) {?>
   <a href="<?php echo $link['reload']?>"><span class="small"><?php echo $link['reload']?></span></a>
 <?php } else if (!$is_top) {?>
   <span class="small">
   <?php echo $this->do_plugin_inline('topicpath','/'); ?>
   </span>
 <?php }?>
<?php }?>

</div>

<hr style="clear:both;">

<div class="body"><?php echo $body?></div>

<?php if ($notes != '') {?>
<div class="note"><?php echo $notes?></div>
<?php }?>

<?php if ($page_comments) { ?>
<?php echo $this->root->hr ?>
<div class="commentbody"><?php echo $page_comments ?></div>
<?php } ?>

<?php echo $this->root->hr?>

<?php if ($lastmodified != '') { ?>
<div class="lastmodified">Last-modified: <?php echo $lastmodified ?> by <?php echo $pginfo['lastuname'] ?></div>
<?php } ?>

<div class="footer">
<?php if ($is_page) { ?>
 <div><?php echo $lang['pagealias'] ?>: <?php echo $pginfo['alias'] ?></div>
<?php } ?>
 <div><?php echo $lang['pageowner'] ?>: <?php echo $pginfo['uname'] ?></div>
 <div><?php echo $lang['siteadmin'] ?>: <a href="<?php echo $this->root->modifierlink ?>"><?php echo $this->root->modifier ?></a></div>
</div>

<div class="linkslist">
<?php echo $links ?>
</div>
</body>
</html>