<?php
// xpWiki runmode
$this->root->runmode = "standalone";

// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: tdiary.skin.php,v 1.35 2011/11/26 12:03:10 nao-pon Exp $
// Copyright (C)
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// tDiary-wrapper skin (Updated for tdiary-theme 2.1.2)

// ------------------------------------------------------------
// Settings (define before here, if you want)

// Set site identities
$this->root->_IMAGE['skin']['favicon']  = ''; // Sample: 'image/favicon.ico';

// Select theme
if (! isset($this->cont['TDIARY_THEME']))
	$this->cont['TDIARY_THEME'] =  'loose-leaf'; // Default

// Show link(s) at your choice, with <div class="calendar"> design
// NOTE: Some theme become looking worse with this!
//   NULL = Show nothing
//   0    = Show topicpath
//   1    = Show reload URL
if (! isset($this->cont['TDIARY_CALENDAR_DESIGN']))
	$this->cont['TDIARY_CALENDAR_DESIGN'] = 0; // NULL, 0, 1

// Show / Hide navigation bar UI at your choice
// NOTE: This is not stop their functionalities!
if (! isset($this->cont['PKWK_SKIN_SHOW_NAVBAR']))
	$this->cont['PKWK_SKIN_SHOW_NAVBAR'] =  1; // 1, 0

// Show toolbar at your choice, with <div class="footer"> design
// NOTE: Some theme become looking worse with this!
if (! isset($this->cont['PKWK_SKIN_SHOW$toolbar']))
	$this->cont['PKWK_SKIN_SHOW$toolbar'] =  1; // 0, 1

// TDIARY_SIDEBAR_POSITION: See below

// ------------------------------------------------------------
// Code start

// Prohibit direct access
if (! isset($this->cont['UI_LANG'])) die('UI_LANG is not set');
if (! isset($this->root->_LANG)) die('$_LANG is not set');
if (! isset($this->cont['PKWK_READONLY'])) die('PKWK_READONLY is not set');

// ------------------------------------------------------------
// Check tDiary theme

if (! isset($this->cont['TDIARY_THEME']) || $this->cont['TDIARY_THEME'] == '') {
	die('Theme is not specified. Set "TDIARY_THEME" correctly');
} else {
	$theme = rawurlencode($this->cont['TDIARY_THEME']); // Supress all nasty letters
	$theme_css = $this->cont['DATA_HOME'] . 'skin/tdiary_theme/' . $theme . '/' . $theme . '.css';
	if (! is_file($theme_css)) {
		echo 'tDiary theme wrapper: ';
		echo 'Theme not found: ' . htmlspecialchars($theme_css) . '<br />';
		echo 'You can get tdiary-theme from: ';
		echo 'http://sourceforge.net/projects/tdiary/';
		exit;
	 }
}

// ------------------------------------------------------------
// tDiary theme: Exception

// Adjust DTD (bug between these theme(=CSS) and MSIE)
// NOTE:
//    PukiWiki default: $this->cont['PKWK_DTD_XHTML_1_1']
//    tDiary's default: $this->cont['PKWK_DTD_HTML_4_01_STRICT']
switch($this->cont['TDIARY_THEME']){
case 'christmas':
	$this->root->pkwk_dtd = $this->cont['PKWK_DTD_HTML_4_01_STRICT']; // or centering will be ignored via MSIE
	break;
}

// Adjust reverse-link default design manually
$disable_backlink = FALSE;
switch($this->cont['TDIARY_THEME']){
case 'hatena':		/* FALLTHROUGH */
case 'hatena-black':
case 'hatena-brown':
case 'hatena-darkgray':
case 'hatena-green':
case 'hatena-lightblue':
case 'hatena-lightgray':
case 'hatena-purple':
case 'hatena-red':
case 'hatena-white':
case 'hatena_cinnamon':
case 'hatena_japanese':
case 'hatena_leaf':
case 'hatena_water':
	$disable_backlink = TRUE; // or very viewable title color
	break;
}

// ------------------------------------------------------------
// tDiary theme: Select CSS color theme (Now testing:black only)

if (isset($this->cont['TDIARY_COLOR_THEME'])) {
	$css_theme = rawurlencode($this->cont['TDIARY_COLOR_THEME']);
} else {
	$css_theme = '';

	switch($this->cont['TDIARY_THEME']){
	case 'alfa':
	case 'bill':
	case 'black-lingerie':
	case 'blackboard':
	case 'bubble':
	case 'cosmos':
	case 'darkness-pop':
	case 'digital_gadgets':
	case 'fine':
	case 'fri':
	case 'giza':
	case 'hatena-black':
	case 'hatena_savanna-blue':
	case 'hatena_savanna-green':
	case 'hatena_savanna-red':
	case 'kaizou':
	case 'lightning':
	case 'lime':
	case 'line':
	case 'midnight':
	case 'moo':
	case 'nachtmusik':
	case 'nebula':
	case 'nippon':
	case 'noel':
	case 'petith-b':
	case 'quiet_black':
	case 'redgrid':
	case 'starlight':
	case 'tinybox_green':
	case 'white-lingerie':
	case 'white_flower':
	case 'whiteout':
	case 'wine':
	case 'wood':
	case 'xmastree':
	case 'yukon':
		$css_theme = 'black';

	// Another theme needed?
	case 'bluely':
	case 'brown':
	case 'deepblue':
	case 'scarlet':
	case 'smoking_black':
		;
	}
}

// ------------------------------------------------------------
// tDiary theme: Page title design (which is fancy, date and text?)

if (isset($this->cont['TDIARY_TITLE_DESIGN_DATE']) &&
    ($this->cont['TDIARY_TITLE_DESIGN_DATE']  == 0 ||
     $this->cont['TDIARY_TITLE_DESIGN_DATE']  == 1 ||
     $this->cont['TDIARY_TITLE_DESIGN_DATE']  == 2)) {
	$title_design_date = $this->cont['TDIARY_TITLE_DESIGN_DATE'];
} else {
	$title_design_date = 1; // Default: Select the date desin, or 'the same design'
	switch($this->cont['TDIARY_THEME']){
	case '3minutes':	/* FALLTHROUGH */
	case '90':
	case 'aoikuruma':
	case 'black-lingerie':
	case 'blog':
	case 'book':
	case 'book2-feminine':
	case 'book3-sky':
	case 'candy':
	case 'cards':
	case 'desert':
	case 'dot':
	case 'himawari':
	case 'kitchen-classic':
	case 'kitchen-french':
	case 'kitchen-natural':
	case 'light-blue':
	case 'lovely':
	case 'lovely_pink':
	case 'lr':
	case 'magic':
	case 'maroon':
	case 'midnight':
	case 'momonga':
	case 'nande-ya-nen':
	case 'narrow':
	case 'natrium':
	case 'nebula':
	case 'orange':
	case 'parabola':
	case 'plum':
	case 'pool_side':
	case 'rainy-season':
	case 'right':
	case 's-blue':
	case 's-pink':
	case 'sky':
	case 'sleepy_kitten':
	case 'snow_man':
	case 'spring':
	case 'tag':
	case 'tdiarynet':
	case 'treetop':
	case 'white-lingerie':
	case 'white_flower':
	case 'whiteout':
	case 'wood':
		$title_design_date = 0; // Select text design	
		break;

	case 'aqua':
	case 'arrow':
	case 'fluxbox':
	case 'fluxbox2':
	case 'fluxbox3':
	case 'ymck':
		$title_design_date = 2; // Show both :)
		break;
	}
}

// ------------------------------------------------------------
// tDiary 'Sidebar' position

// Default position
if (isset($this->cont['TDIARY_SIDEBAR_POSITION'])) {
	$sidebar = $this->cont['TDIARY_SIDEBAR_POSITION'];
} else {
	$sidebar = 'another'; // Default: Show as an another page below

	// List of themes having sidebar CSS < (AllTheme / 2)
	// $ grep div.sidebar */*.css | cut -d: -f1 | cut -d/ -f1 | sort | uniq
	// $ wc -l *.txt
	//    142 list-sidebar.txt
	//    286 list-all.txt
	switch($this->cont['TDIARY_THEME']){
	case '3minutes':	/*FALLTHROUGH*/
	case '3pink':
	case 'aoikuruma':
	case 'aqua':
	case 'arrow':
	case 'artnouveau-blue':
	case 'artnouveau-green':
	case 'artnouveau-red':
	case 'asterisk-blue':
	case 'asterisk-lightgray':
	case 'asterisk-maroon':
	case 'asterisk-orange':
	case 'asterisk-pink':
	case 'autumn':
	case 'babypink':
	case 'be_r5':
	case 'bill':
	case 'bistro_menu':
	case 'bluely':
	case 'book':
	case 'book2-feminine':
	case 'book3-sky':
	case 'bright-green':
	case 'britannian':
	case 'bubble':
	case 'candy':
	case 'cat':
	case 'cherry':
	case 'cherry_blossom':
	case 'chiffon_leafgreen':
	case 'chiffon_pink':
	case 'chiffon_skyblue':
	case 'citrus':
	case 'clover':
	case 'colorlabel':
	case 'cool_ice':
	case 'cosmos':
	case 'curtain':
	case 'darkness-pop':
	case 'delta':
	case 'diamond_dust':
	case 'dice':
	case 'digital_gadgets':
	case 'dot-lime':
	case 'dot-orange':
	case 'dot-pink':
	case 'dot-sky':
	case 'dotted_line-blue':
	case 'dotted_line-green':
	case 'dotted_line-red':
	case 'emboss':
	case 'flower':
	case 'gear':
	case 'germany':
	case 'gray2':
	case 'green_leaves':
	case 'happa':
	case 'hatena':
	case 'hatena-black':
	case 'hatena-brown':
	case 'hatena-darkgray':
	case 'hatena-green':
	case 'hatena-lightblue':
	case 'hatena-lightgray':
	case 'hatena-lime':
	case 'hatena-orange':
	case 'hatena-pink':
	case 'hatena-purple':
	case 'hatena-red':
	case 'hatena-sepia':
	case 'hatena-tea':
	case 'hatena-white':
	case 'hatena_cinnamon':
	case 'hatena_japanese':
	case 'hatena_leaf':
	case 'hatena_rainyseason':
	case 'hatena_savanna-blue':
	case 'hatena_savanna-green':
	case 'hatena_savanna-red':
	case 'hatena_savanna-white':
	case 'hatena_water':
	case 'himawari':
	case 'jungler':
	case 'kaeru':
	case 'kitchen-classic':
	case 'kitchen-french':
	case 'kitchen-natural':
	case 'kotatsu':
	case 'light-blue':
	case 'loose-leaf':
	case 'marguerite':
	case 'matcha':
	case 'mizu':
	case 'momonga':
	case 'mono':
	case 'moo':
	case 'natrium':
	case 'nippon':
	case 'note':
	case 'old-pavement':
	case 'orange_flower':
	case 'pain':
	case 'pale':
	case 'paper':
	case 'parabola':
	case 'pettan':
	case 'pink-border':
	case 'plum':
	case 'puppy':
	case 'purple_sun':
	case 'rainy-season':
	case 'rectangle':
	case 'repro':
	case 'rim-daidaiiro':
	case 'rim-fujiiro':
	case 'rim-mizuiro':
	case 'rim-sakurairo':
	case 'rim-tanpopoiro':
	case 'rim-wakabairo':
	case 'russet':
	case 's-blue':
	case 'sagegreen':
	case 'savanna':
	case 'scarlet':
	case 'sepia':
	case 'simple':
	case 'sleepy_kitten':
	case 'smoking_black':
	case 'smoking_white':
	case 'spring':
	case 'sunset':
	case 'tdiarynet':
	case 'teacup':
	case 'thin':
	case 'tile':
	case 'tinybox':
	case 'tinybox_green':
	case 'treetop':
	case 'white_flower':
	case 'wine':
	case 'yukon':
	case 'zef':
		$sidebar = 'bottom'; // This is the default position of tDiary's.
		break;
	}

	// Manually adjust sidebar's default position
	switch($this->cont['TDIARY_THEME']){

	// 'bottom'
	case '90': // But upper navigatin UI will be hidden by sidebar
	case 'blackboard':
	case 'quirky':
	case 'quirky2':
		$sidebar = 'bottom';
		break;

	// 'top': Assuming sidebar is above of the body
	case 'autumn':	/*FALLTHROUGH*/
	case 'cosmos':
	case 'dice':	// Sidebar text (white) seems unreadable
	case 'happa':
	case 'kaeru':
	case 'note':
	case 'paper':	// Sidebar text (white) seems unreadable
	case 'sunset':
	case 'tinybox':	// For MSIE with narrow window width, seems meanless
	case 'tinybox_green':	// The same
	case 'ymck':
		$sidebar = 'top';
		break;

	// 'strict': Strict separation between sidebar and main contents needed
	case '3minutes':	/*FALLTHROUGH*/
	case '3pink':
	case 'aoikuruma':
	case 'aqua':
	case 'artnouveau-blue':
	case 'artnouveau-green':
	case 'artnouveau-red':
	case 'asterisk-blue':
	case 'asterisk-lightgray':
	case 'asterisk-maroon':
	case 'asterisk-orange':
	case 'asterisk-pink':
	case 'bill':
	case 'candy':
	case 'cat':
	case 'chiffon_leafgreen':
	case 'chiffon_pink':
	case 'chiffon_skyblue':
	case 'city':
	case 'clover':
	case 'colorlabel':
	case 'cool_ice':
	case 'dot-lime':
	case 'dot-orange':
	case 'dot-pink':
	case 'dot-sky':
	case 'dotted_line-blue':
	case 'dotted_line-green':
	case 'dotted_line-red':
	case 'flower':
	case 'germany':
	case 'green-tea':
	case 'hatena':
	case 'hatena-black':
	case 'hatena-brown':
	case 'hatena-darkgray':
	case 'hatena-green':
	case 'hatena-lightblue':
	case 'hatena-lightgray':
	case 'hatena-lime':
	case 'hatena-orange':
	case 'hatena-pink':
	case 'hatena-purple':
	case 'hatena-red':
	case 'hatena-sepia':
	case 'hatena-tea':
	case 'hatena-white':
	case 'hiki':
	case 'himawari':
	case 'kasumi':
	case 'kitchen-classic':
	case 'kitchen-french':
	case 'kitchen-natural':
	case 'kotatsu':
	case 'kurenai':
	case 'light-blue':
	case 'loose-leaf':
	case 'marguerite':
	case 'matcha':
	case 'memo':
	case 'memo2':
	case 'memo3':
	case 'mirage':
	case 'mizu':
	case 'mono':
	case 'moo':	// For MSIE, strict seems meanless
	case 'navy':
	case 'pict':
	case 'pokke-blue':
	case 'pokke-orange':
	case 'query000':
	case 'query011':
	case 'query101':
	case 'query110':
	case 'query111or':
	case 'puppy':
	case 'rainy-season':
	case 's-blue':	// For MSIE, strict seems meanless
	case 'sagegreen':
	case 'savanna':
	case 'scarlet':
	case 'sepia':
	case 'simple':
	case 'smoking_gray':
	case 'spring':
	case 'teacup':
	case 'wine':
		$sidebar = 'strict';
		break;

	// 'another': They have sidebar-design, but can not show it
	//  at the 'side' of the contents
	case 'babypink':	/*FALLTHROUGH*/
	case 'bubble':
	case 'cherry':
	case 'darkness-pop':
	case 'diamond_dust':
	case 'gear':
	case 'necktie':
	case 'pale':
	case 'pink-border':
	case 'rectangle':
	case 'russet':
	case 'smoking_black':
	case 'zef':
		$sidebar = 'another'; // Show as an another page below
		break;
	}

	// 'none': Show no sidebar
}
// Check menu (sidebar) is ready and $menubar is there
if ($sidebar == 'none') {
	$menu = FALSE;
} else {
	$menu = ($this->arg_check('read') && $this->is_page($this->root->menubar) &&
		$this->exist_plugin_convert('menu'));
	if ($menu) {
		$menu_body = preg_replace('#<h2 ([^>]*)>(.*?)</h2>#',
			'<h3 $1><span class="sanchor"></span> $2</h3>',
			$this->do_plugin_convert('menu'));
		// Reget
		list($head_pre_tag, $head_tag) = $this->get_additional_headtags();
	}
}

// ------------------------------------------------------------
// Code continuing ...

$lang  = & $this->root->_LANG['skin'];
$link  = & $this->root->_LINK;
$image = & $this->root->_IMAGE['skin'];
$rw    = ! $this->cont['PKWK_READONLY'];

// Decide charset for CSS
$css_charset = 'iso-8859-1';
switch($this->cont['UI_LANG']){
	case 'ja': $css_charset = 'Shift_JIS'; break;
}

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
}

$favicon = ($image['favicon'])? "<link rel=\"SHORTCUT ICON\" href=\"{$image['favicon']}\" />" : "";
$dirname = $this->root->mydirname;

$css_prefix_id = $this->root->css_prefix ? ' id="' . ltrim($this->root->css_prefix, '#') . '"' : '';

?>
<head>
 <?php echo $meta_content_type ?>
 <meta http-equiv="content-style-type" content="text/css" />
<?php if ($this->root->nofollow || ! $is_read)  { ?> <meta name="robots" content="NOINDEX,NOFOLLOW" /><?php } ?>
<?php if ($this->cont['PKWK_ALLOW_JAVASCRIPT'] && isset($this->root->javascript)) { ?> <meta http-equiv="Content-Script-Type" content="text/javascript" /><?php } ?>

 <title><?php echo htmlspecialchars($this->root->pagetitle) ?> - <?php echo $this->root->siteinfo['sitename'] ?></title>

<?php echo $head_pre_tag?>
 <?php echo $favicon ?>
 <link rel="stylesheet" type="text/css" media="all" href="<?php echo "{$this->cont['HOME_URL']}{$this->cont['TDIARY_DIR']}" ?>base.css" />
 <link rel="stylesheet" type="text/css" media="all" href="<?php echo "{$this->cont['HOME_URL']}{$this->cont['TDIARY_DIR']}" ?><?php echo $theme ?>/<?php echo $theme ?>.css" />
 <link rel="stylesheet" type="text/css" media="screen" href="<?php echo "{$this->cont['HOME_URL']}{$this->cont['TDIARY_DIR']}" ?>tdiary.css.php?charset=<?php echo $css_charset ?>&amp;color=<?php echo $css_theme ?>" charset="<?php echo $css_charset ?>" />
 <link rel="stylesheet" type="text/css" media="print"  href="<?php echo "{$this->cont['HOME_URL']}{$this->cont['TDIARY_DIR']}" ?>tdiary.css.php?charset=<?php echo $css_charset ?>&amp;color=<?php echo $css_theme ?>&amp;media=print" charset="<?php echo $css_charset ?>" />
 <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $link['rss'] ?>" /><?php // RSS auto-discovery ?>
<?php echo $head_tag ?>
</head>
<body<?php echo $css_prefix_id ?>><!-- Theme:<?php echo htmlspecialchars($theme) . ' Sidebar:' . $sidebar ?> -->
<div class="xpwiki_<?php echo $dirname ?>" style="position:relative;">

<?php if ($menu && $sidebar == 'strict') { ?>
<!-- Sidebar top -->
<div class="sidebar">
	<div class="menubar">
		<?php echo $menu_body ?>
	</div>
</div><!-- class="sidebar" -->

<div class="pkwk_body">
<div class="main">
<?php } // if ($menu && $sidebar == 'strict') ?>

<!-- Navigation buttuns -->
<?php if ($this->cont['PKWK_SKIN_SHOW_NAVBAR']) { ?>
<div class="adminmenu"><div class="navigator">
 <?php $navigator($this, 'top') ?> &nbsp;

<?php if ($is_page) { ?>
  <?php if ($rw) { ?>
  	<?php if (!$is_freeze && $is_editable) { ?>
		<?php $navigator($this, 'edit') ?>
	<?php } ?>
	<?php if ($is_read && $this->root->function_freeze) { ?>
		<?php (! $is_freeze) ? $navigator($this, 'freeze') : $navigator($this, 'unfreeze') ?>
	<?php } ?>
	<?php if ($is_owner) { ?>
		<?php $navigator($this,'pginfo') ?>
	<?php } ?>
 <?php } ?>
   <?php $navigator($this, 'diff') ?>
 <?php if ($this->root->do_backup) { ?>
	<?php $navigator($this, 'backup') ?>
 <?php } ?>
 <?php if ($rw && (bool)ini_get('file_uploads')) { ?>
	<?php $navigator($this, 'upload') ?>
 <?php } ?>
   <?php $navigator($this, 'reload') ?>
   &nbsp;
<?php } ?>

 <?php if ($rw) { ?>
	<?php $navigator($this, 'new') ?>
 <?php } ?>
 <?php if ($this->arg_check('list')) { ?>
	<?php $navigator($this,'filelist') ?>
	<?php $navigator($this,'attaches') ?>
 <?php } else { ?>
   <?php $navigator($this,'list') ?>
 <?php } ?>
   <?php $navigator($this, 'search') ?>
   <?php $navigator($this, 'recent') ?>
   <?php $navigator($this, 'help')   ?>
   &nbsp;
   <span id="xpwiki_fusenlist" style="display:none;">&nbsp;<!--FU--><!--SEN--></span>
   <?php if ($subnote) echo $this->do_plugin_inline('subnote', 'format:%s,popup', 'Note|Main'); ?>
<?php if ($this->root->trackback) { ?> &nbsp;
   <?php $navigator($this, 'trackback', $lang['trackback'] . '(' . $this->tb_count($_page) . ')',
 	($this->root->trackback_javascript == 1) ? 'onclick="OpenTrackback(this.href); return false"' : '') ?>
<?php } ?>
<?php if ($this->root->referer)   { ?> &nbsp;
   <?php $navigator($this, 'refer') ?>
<?php } ?>
<?php if ($page_comments_count)   { ?> &nbsp;
   <?php echo $page_comments_count ?>
<?php } ?>
</div></div>
<?php } else { ?>
<div class="navigator"></div>
<?php } // $this->cont['PKWK_SKIN_SHOW_NAVBAR'] ?>

<h1><a href="<?php echo $this->root->siteinfo['rooturl'] ?>" title="Site Top"><?php echo $this->root->siteinfo['sitename'] ?></a> / <?php echo $this->root->module_title ?></h1>

<div class="calendar">
<?php if ($is_page && $this->cont['TDIARY_CALENDAR_DESIGN'] !== NULL) { ?>
	<?php if($this->cont['TDIARY_CALENDAR_DESIGN']) { ?>
		<a href="<?php echo $link['reload'] ?>"><span class="small"><?php echo $link['reload'] ?></span></a>
	<?php } else  if (!$is_top) { ?>
		<?php echo $this->do_plugin_inline('topicpath'); ?>
	<?php } ?>
<?php } ?>
</div>


<?php if ($menu && $sidebar == 'top') { ?>
<!-- Sidebar compat top -->
<div class="sidebar">
	<div class="menubar">
		<?php echo $menu_body ?>
	</div>
</div><!-- class="sidebar" -->
<?php } // if ($menu && $sidebar == 'top') ?>


<?php if ($menu && ($sidebar == 'top' || $sidebar == 'bottom')) { ?>
<div class="pkwk_body">
<div class="main">
<?php } ?>

<hr class="sep" />

<div class="day">

<?php
// Page title (page name)
$title = '';
if ($disable_backlink) {
	if ($_page !== '') {
		$title = htmlspecialchars($_page);
	} else {
		$title = $page; // Search, or something message
	}
} else {
	if ($page !== '') {
		$title = $page;
	} else {
		$title =  htmlspecialchars($_page);
	}
}
$title_date = $title_text = '';
switch($title_design_date){
case 1: $title_date = & $title; break;
case 0: $title_text = & $title; break;
default:
	// Show both (for debug or someting)
	$title_date = & $title;
	$title_text = & $title;
	break;
}
?>
<h2><span class="date"><?php  echo $title_date ?></span>
    <span class="title"><?php echo $title_text ?></span></h2>

<div class="body">
	<div class="section">
<?php
	// For read and preview: tDiary have no <h2> inside body
	if($this->root->fixed_heading_anchor_edit) {
	    $body = preg_replace('#<h2 (.*?)>(.*?)<a class="anchor_super" (.*?)>.*?</a> (<a .*?</a>)</h2>#',
	                         '<h3 $1><a $3><span class="sanchor">_</span></a> $2 $4</h3>', $body);
	    $body = preg_replace('#<h([34]) (.*?)>(.*?)<a class="anchor_super" (.*?)>.*?</a> (<a .*?</a>)</h\1>#',
	                         '<h$1 $2><a $4>_</a> $3 $5</h$1>', $body);
	    $body = preg_replace('#<h2 ([^>]*)>(.*?)</h2>#',
	                         '<h3 $1><span class="sanchor">_</span> $2</h3>', $body);
	} else {
	
		$body = preg_replace('#<h2 ([^>]*)>(.*?)<a class="anchor_super" ([^>]*)>.*?</a></h2>#',
			'<h3 $1><a $3><span class="sanchor">_</span></a> $2</h3>', $body);
		$body = preg_replace('#<h([34]) ([^>]*)>(.*?)<a class="anchor_super" ([^>]*)>.*?</a></h\1>#',
			'<h$1 $2><a $4>_</a> $3</h$1>', $body);
		$body = preg_replace('#<h2 ([^>]*)>(.*?)</h2>#',
			'<h3 $1><span class="sanchor">_</span> $2</h3>', $body);
	}
	if ($is_read) {
		// Read
		echo $body;
	} else {
		// Edit, preview, search, etc
		echo preg_replace('/(<form) (action="' . preg_quote($this->root->script, '/') .
			')/', '$1 class="update" $2', $body);
	}
?>
	</div>
</div><!-- class="body" -->


<?php if ($notes != '' || $page_comments) { ?>
<div class="comment"><!-- Design for tDiary "Comments" -->
	<div class="caption">&nbsp;</div>
	<div class="commentbody">
	 <br />
	 	<?php if ($notes != '') { ?>
			<?php
			$notes = preg_replace('#<span class="small">(.*?)</span>#', '<p>$1</p>', $notes);
			echo preg_replace('#<a (id="notefoot_[^>]*)>(.*?)</a>#',
				'<div class="commentator"><a $1><span class="canchor"></span> ' .
				'<span class="commentator">$2</span></a>' .
				'<span class="commenttime"></span></div>', $notes);
			echo $this->root->hr;
			?>
		<?php } ?>
		<?php if ($page_comments) { ?>
			<?php echo $page_comments ?>
		<?php } ?>
		<?php echo $system_notification ?>
	</div>
</div>
<?php } ?>

<?php if ($attaches != '') { ?>
<div class="comment">
	<div class="caption">&nbsp;</div>
	<div class="commentshort">
		<?php echo $attaches ?>
	</div>
</div>
<?php } ?>

<?php if ($related != '') { ?>
<div class="comment">
	<div class="caption">&nbsp;</div>
	<div class="commentshort">
		<?php echo $lang['linkpage'] ?>: <?php echo $related ?>
	</div>
</div>
<?php } ?>

<!-- Design for tDiary "Today's referrer" -->

<div class="referer">
<?php if ($is_page) echo $this->do_plugin_convert('counter') ?>
<?php if ($lastmodified != '') echo $lang['lastmodify'].': ' . $lastmodified; ?> by <?php echo $pginfo['lastuname'] ?>
</div>

</div><!-- class="day" -->

<hr class="sep" />

<?php if ($menu && $sidebar == 'another') { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->

<!-- Sidebar another -->
<div class="pkwk_body">
	<h1>&nbsp;</h1>
	<div class="calendar"></div>
	<hr class="sep" />
	<div class="day">
		<h2><span class="date"></span><span class="title">&nbsp;</span></h2>
		<div class="body">
			<div class="section">
				<?php echo $menu_body ?>
			</div>
		</div>
		<div class="referer"></div>
	</div>
	<hr class="sep" />
</div><!-- class="pkwk_body" -->

<div class="pkwk_body">
<div class="main">
<?php } // if ($menu && $sidebar == 'another') ?>


<?php if ($menu && ($sidebar == 'top' || $sidebar == 'bottom')) { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->
<?php } ?>


<?php if ($menu && $sidebar == 'bottom') { ?>
<!-- Sidebar compat bottom -->
<div class="sidebar">
	<div class="menubar">
		<?php echo $menu_body ?>
	</div>
</div><!-- class="sidebar" -->
<?php } // if ($menu && $sidebar == 'bottom') ?>

<div class="footer">
<?php if ($this->cont['PKWK_SKIN_SHOW$toolbar']) { ?>
<!-- Toolbar -->
<?php

// Set toolbar-specific images
$this->root->_IMAGE['skin']['reload']   = 'reload.png';
$this->root->_IMAGE['skin']['new']      = 'new.png';
$this->root->_IMAGE['skin']['edit']     = 'edit.png';
$this->root->_IMAGE['skin']['freeze']   = 'freeze.png';
$this->root->_IMAGE['skin']['unfreeze'] = 'unfreeze.png';
$this->root->_IMAGE['skin']['diff']     = 'diff.png';
$this->root->_IMAGE['skin']['upload']   = 'file.png';
$this->root->_IMAGE['skin']['copy']     = 'copy.png';
$this->root->_IMAGE['skin']['rename']   = 'rename.png';
$this->root->_IMAGE['skin']['top']      = 'top.png';
$this->root->_IMAGE['skin']['list']     = 'list.png';
$this->root->_IMAGE['skin']['search']   = 'search.png';
$this->root->_IMAGE['skin']['recent']   = 'recentchanges.png';
$this->root->_IMAGE['skin']['backup']   = 'backup.png';
$this->root->_IMAGE['skin']['help']     = 'help.png';
$this->root->_IMAGE['skin']['rss']      = 'feed-rss.png';
$this->root->_IMAGE['skin']['rss10']    = 'feed-rss1.png';
$this->root->_IMAGE['skin']['rss20']    = 'feed-rss2.png';
$this->root->_IMAGE['skin']['atom']     = 'feed-atom.png';
$this->root->_IMAGE['skin']['rdf']      = 'rdf.png';
?>
 <?php $toolbar($this, 'top') ?>

<?php if ($is_page) { ?>
 &nbsp;
 <?php if ($rw) { ?>
  	<?php if (!$is_freeze && $is_editable) { ?>
		<?php $toolbar($this, 'edit') ?>
	<?php } ?>
	<?php if ($is_read && $this->root->function_freeze) { ?>
		<?php if (! $is_freeze) { $toolbar($this, 'freeze'); } else { $toolbar($this, 'unfreeze'); } ?>
	<?php } ?>
 <?php } ?>
 <?php $toolbar($this, 'diff') ?>
<?php if ($this->root->do_backup) { ?>
	<?php $toolbar($this, 'backup') ?>
<?php } ?>
 <?php if ($rw && (bool)ini_get('file_uploads')) { ?>
	<?php $toolbar($this, 'upload') ?>
 <?php } ?>
 <?php if ($rw) { ?>
	<?php $toolbar($this, 'copy') ?>
	<?php $toolbar($this, 'rename') ?>
 <?php } ?>
 <?php $toolbar($this, 'reload') ?>
<?php } ?>
 &nbsp;
 <?php if ($rw) { ?>
	<?php $toolbar($this, 'new') ?>
 <?php } ?>
 <?php $toolbar($this, 'list')   ?>
 <?php $toolbar($this, 'search') ?>
 <?php $toolbar($this, 'recent') ?>
 &nbsp; <?php $toolbar($this, 'help') ?>
 &nbsp; <?php $toolbar($this, 'rss10', 14, 14) ?>
 <?php $toolbar($this, 'rss20', 14, 14) ?>
 <?php $toolbar($this, 'atom', 14, 14) ?>
 <br />
<?php } // $this->cont['PKWK_SKIN_SHOW$toolbar'] ?>

<!-- Copyright etc -->
<?php if ($is_page) { ?>
<div><?php echo $lang['pagealias'] ?>: <?php echo $pginfo['alias'] ?></div>
<?php } ?>
<div><?php echo $lang['pageowner'] ?>: <?php echo $pginfo['uname'] ?></div>
<div><?php echo $lang['siteadmin'] ?>: <a href="<?php echo $this->root->modifierlink ?>"><?php echo $this->root->modifier ?></a></div>
<?php if ($is_admin) { ?>
 <?php echo $this->cont['S_COPYRIGHT'] ?>.
 Powered by PHP <?php echo PHP_VERSION ?><br />
 HTML convert time: <?php echo $taketime ?> sec.
<?php } // $is_admin ?>
</div><!-- class="footer" -->

<?php if ($menu && ($sidebar != 'top' && $sidebar != 'bottom')) { ?>
</div><!-- class="main" -->
</div><!-- class="pkwk_body" -->
<?php } ?>

</div><!-- class="xpwiki_dirname" -->
</body>
</html>
