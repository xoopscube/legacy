<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: pukiwiki.skin.php,v 1.1 2011/11/18 14:33:49 nao-pon Exp $
// Copyright (C)
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki default skin

// ------------------------------------------------------------
// Settings (define before here, if you want)

// Set site identities
$_IMAGE['skin']['logo']     = 'pukiwiki.png';
$_IMAGE['skin']['favicon']  = ''; // Sample: 'image/favicon.ico';

// SKIN_DEFAULT_DISABLE_TOPICPATH
//   1 = Show reload URL
//   0 = Show topicpath
if (! isset($this->cont['SKIN_DEFAULT_DISABLE_TOPICPATH']))
	$this->cont['SKIN_DEFAULT_DISABLE_TOPICPATH'] = 0; // 1, 0

// Show / Hide navigation bar UI at your choice
// NOTE: This is not stop their functionalities!
if (! isset($this->cont['PKWK_SKIN_SHOW_NAVBAR']))
	$this->cont['PKWK_SKIN_SHOW_NAVBAR'] = 1; // 1, 0

// Show / Hide toolbar UI at your choice
// NOTE: This is not stop their functionalities!
if (! isset($this->cont['PKWK_SKIN_SHOW$toolbar']))
	$this->cont['PKWK_SKIN_SHOW$toolbar'] = 1; // 1, 0

// ------------------------------------------------------------
// Code start

$lang  = & $_LANG['skin'];
$link  = & $_LINK;
$image = & $_IMAGE['skin'];
$rw    = ! $this->cont['PKWK_READONLY'];
$can_attach = ($rw && $this->is_page($_page) && (!$this->cont['ATTACH_UPLOAD_EDITER_ONLY'] || $is_editable) && (bool)ini_get('file_uploads'));

// Decide charset for CSS
$css_charset = $this->cont['CSS_CHARSET'];

// ------------------------------------------------------------
// Output

$favicon = ($image['favicon'])? "<link rel=\"SHORTCUT ICON\" href=\"{$image['favicon']}\" />" : "";
$dirname = $this->root->mydirname;

$this->root->html_header = <<<EOD
$favicon
$head_pre_tag
<link rel="stylesheet" type="text/css" media="all" href="{$this->cont['LOADER_URL']}?skin={$this->cont['SKIN_NAME']}&amp;charset={$css_charset}&amp;pw={$this->root->pre_width}&amp;src=main.css" charset="{$css_charset}" />
<link rel="stylesheet" type="text/css" media="print"  href="{$this->cont['LOADER_URL']}?skin={$this->cont['SKIN_NAME']}&amp;charset={$css_charset}&amp;pw={$this->root->pre_width}&amp;media=print&amp;src=main.css" charset="{$css_charset}" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="{$link['rss']}" />
$head_tag
EOD;
?>

<div class="xpwiki_<?php echo $dirname ?>">

<div class="navigator" id="<?php echo $dirname ?>_navigator">

<?php if($this->cont['PKWK_SKIN_SHOW_NAVBAR']) { ?>

<div class="header" id="<?php echo $dirname ?>_header">

 <h1 class="title"><?php echo $page ?></h1>

<?php if ($is_page) { ?>
 <?php if($this->cont['SKIN_DEFAULT_DISABLE_TOPICPATH']) { ?>
   <a href="<?php echo $link['reload'] ?>"><span class="small"><?php echo $link['reload'] ?></span></a>
 <?php } else if (!$is_top) { ?>
   <span class="small">
   <?php echo $this->do_plugin_inline('topicpath'); ?>
   </span>
 <?php } ?>
<?php } ?>

</div>

<hr style="clear: both;" />

<?php } // PKWK_SKIN_SHOW_NAVBAR ?>
</div><!--/navigator-->

<div class="body">
<?php echo $body ?>
</div>

<?php if ($notes != '') { ?>
<div class="footnotes"><?php echo $notes ?></div>
<?php } ?>

<?php if ($attaches != '') { ?>
<div class="attach">
<?php echo $this->root->hr ?>
<?php echo $attaches ?>
</div>
<?php } ?>

<?php if ($page_comments) { ?>
<?php echo $this->root->hr ?>
<div class="commentbody"><?php echo $page_comments ?></div>
<?php } ?>

<?php echo $system_notification ?>

<?php echo $this->root->hr ?>

<?php if ($this->cont['PKWK_SKIN_SHOW$toolbar']) { ?>
<!-- Toolbar -->
<div class="toolbar">
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
<?php if ($rw) { ?>
	<?php if ($can_attach) { ?>
		<?php $toolbar($this, 'upload') ?>
	<?php } ?>
	<?php $toolbar($this, 'copy') ?>
	<?php $toolbar($this, 'rename') ?>
<?php } ?>
 <?php if ($is_page) { $toolbar($this, 'reload'); } ?>
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
</div>
<?php } // PKWK_SKIN_SHOW$toolbar ?>

<?php if ($is_page) echo $this->do_plugin_convert('counter') ?>

<?php if ($lastmodified != '') { ?>
<div class="lastmodified"><?php echo $lang['lastmodify'] ?>: <?php echo $lastmodified ?> by <?php echo $pginfo['lastuname'] ?></div>
<?php } ?>

<?php if ($related != '') { ?>
<div class="related"><?php echo $lang['linkpage'] ?>: <?php echo $related ?></div>
<?php } ?>
<div class="footer">
<?php if ($is_page) { ?>
 <div><?php echo $lang['pagealias'] ?>: <?php echo $pginfo['alias'] ?></div>
<?php } ?>
 <div><?php echo $lang['pageowner'] ?>: <?php echo $pginfo['uname'] ?></div>
 <div><?php echo $lang['siteadmin'] ?>: <a href="<?php echo $this->root->modifierlink ?>"><?php echo $this->root->modifier ?></a></div>
<?php if ($is_admin) { ?>
 <?php echo $this->cont['S_COPYRIGHT'] ?>.
 Powered by PHP <?php echo PHP_VERSION ?>. HTML convert time: <?php echo $taketime ?> sec.
<?php } // $is_admin ?>
</div>
</div>
