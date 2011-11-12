<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: pukiwiki.skin.php,v 1.48 2011/10/28 13:35:31 nao-pon Exp $
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

// Set toolbar-specific images
$this->root->_IMAGE['skin']['reload']   = 'reload.png';
$this->root->_IMAGE['skin']['new']      = 'new.png';
$this->root->_IMAGE['skin']['newsub']   = 'src=newsub.png';
$this->root->_IMAGE['skin']['edit']     = 'edit.png';
$this->root->_IMAGE['skin']['freeze']   = 'freeze.png';
$this->root->_IMAGE['skin']['unfreeze'] = 'unfreeze.png';
$this->root->_IMAGE['skin']['diff']     = 'diff.png';
$this->root->_IMAGE['skin']['back']     = 'diff.png';
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
$this->root->_IMAGE['skin']['refer']    = 'src=referer.png';
$this->root->_IMAGE['skin']['topage']   = 'src=topage.gif';
$this->root->_IMAGE['skin']['pginfo']   = 'src=pginfo.gif';
$this->root->_IMAGE['skin']['print']    = 'src=print.png';
$this->root->_IMAGE['skin']['powered']  = 'src=cog.png';

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
$profile = $this->cont['UA_PROFILE'];
$rsstitle = 'RSS of ' . htmlspecialchars($this->root->module['title']);
$s_page = htmlspecialchars($_page);

$upload_js = ' onclick="return XpWiki.fileupFormPopup(\''.$dirname.'\',\''.str_replace('\'', '\\\'', $s_page).'\')"';

$this->root->html_header = <<<EOD
$favicon
$head_pre_tag
<link rel="stylesheet" type="text/css" media="all" href="{$this->cont['LOADER_URL']}?skin={$this->cont['SKIN_NAME']}&amp;charset={$css_charset}&amp;pw={$this->root->pre_width}&amp;{$cssprefix}src={$this->root->main_css}" charset="{$css_charset}" />
<link rel="stylesheet" type="text/css" media="print"  href="{$this->cont['LOADER_URL']}?skin={$this->cont['SKIN_NAME']}&amp;charset={$css_charset}&amp;pw={$this->root->pre_width}&amp;media=print&amp;{$cssprefix}src={$this->root->main_css}" charset="{$css_charset}" />
<link rel="alternate" type="application/rss+xml" title="{$rsstitle}" href="{$link['rss']}" />
$head_tag
EOD;
?>

<div class="xpwiki_<?php echo $dirname ?> xpwiki_<?php echo $dirname ?>_<?php echo $profile ?>">

<div class="navigator" id="<?php echo $dirname ?>_navigator">

<?php if($this->cont['PKWK_SKIN_SHOW_NAVBAR']) { ?>

<div class="header" id="<?php echo $dirname ?>_header">

<div class="navigator_page">
<div class="navigator_wiki">
 |
 <?php if ($rw) { ?>
	<?php if ($is_newable) { ?>
		<?php $navigator($this,'new','','',TRUE) ?>
	<?php } ?>
	<?php if ($is_newable2) { ?>
		<?php $navigator($this,'newsub','','',TRUE) ?>
	<?php } ?>
 <?php } ?>
 <?php if ($this->arg_check('list')) { ?>
	<?php $navigator($this,'filelist','','',TRUE) ?>
	<?php $navigator($this,'attaches','','',TRUE) ?>
 <?php } else { ?>
   <?php $navigator($this,'list','','',TRUE) ?>
 <?php } ?>
 <?php $navigator($this,'search','','',TRUE) ?>
 <?php $navigator($this,'recent','','',TRUE) ?>
 <?php $navigator($this,'rss','','','icon',14,14) ?>
 <?php $navigator($this,'help','','',TRUE)   ?>
</div><!--/navigator_wiki-->
<?php if ($is_page) { ?>
 <?php if (! $is_read) {
 	$navigator($this,'topage','','',TRUE);?>
 <?php } ?>
 <?php if ($rw) { ?>
	<?php if (!$is_freeze && $is_editable) { ?>
		<?php $navigator($this,'edit','',$ajax_edit_js,TRUE) ?>
	<?php } ?>
	<?php if ($is_owner) { ?>
		<?php if ($is_read && $this->root->function_freeze) { ?>
			<?php (! $is_freeze) ? $navigator($this,'freeze','','',TRUE) : $navigator($this,'unfreeze','','',TRUE) ?>
		<?php } ?>
		<?php $navigator($this,'pginfo','','',TRUE) ?>
	<?php } ?>
	<?php if ($is_newable) { ?>
		<?php $navigator($this,'copy','','',TRUE) ?>
	<?php } ?>
	<?php if ($is_owner) { ?>
		<?php $navigator($this,'rename','','',TRUE) ?>
	<?php } ?>
 <?php } ?>
 <?php $navigator($this,'back','','',TRUE) ?>
 <?php if ($can_attach) { ?>
	<?php $navigator($this,'upload','',$upload_js,TRUE) ?>
 <?php } ?>
 <?php if ($subnote) echo $this->do_plugin_inline('subnote', 'format:%s,popup,icon', 'Note|Main'); ?>
 <?php if ($this->root->referer) { ?>
  <?php $navigator($this,'refer','','',TRUE) ?>
 <?php } ?>
 <?php $navigator($this,'print','','',TRUE) ?>
<?php } else { ?>
 <?php $navigator($this,'top','','',TRUE)?>
 <?php $navigator($this,'print','','',TRUE) ?>
<?php } ?>
</div><!--/navigator_page-->

<div class="navigator_info">
 <span id="xpwiki_fusenlist" style="display:none;"><span class="button"><!--FU--><!--SEN--></span></span>
<?php if ($this->root->trackback) { ?>
 <span class="button"><?php $navigator($this,'trackback', $lang['trackback'] . '(' . $this->tb_count($_page) . ')',
 	($trackback_javascript == 1) ? 'onclick="OpenTrackback(this.href); return false"' : '') ?></span>
<?php } ?>
<?php if ($page_comments_count)   { ?>
 <span class="button"><?php echo $page_comments_count ?></span>
<?php } ?>
</div><!--/navigator_info-->

<h1 class="title"><?php echo $page ?></h1>

<div class="navigator_path">

<?php if ($is_page) { ?>
 <?php if($this->cont['SKIN_DEFAULT_DISABLE_TOPICPATH']) { ?>
   <a href="<?php echo $link['reload'] ?>"><span class="small"><?php echo $link['reload'] ?></span></a>
 <?php } else if (!$is_top) { ?>
   <?php echo $this->do_plugin_inline('topicpath', '/'); ?>
 <?php } ?>
<?php } ?>

</div><!--/navigator_path-->

</div><!--/header-->

<div class="navigator_hr">
	<hr />
</div>

<?php } // PKWK_SKIN_SHOW_NAVBAR ?>
</div><!--/navigator-->


<?php if ($this->arg_check('read') && $this->exist_plugin_convert('menu') && $this->root->show_menu_bar) { ?>
<table border="0" style="width:100%">
 <tr>
  <td class="menubar">
   <div class="menubar"><?php echo $this->do_plugin_convert('menu') ?></div>
  </td>
  <td valign="top">
   <div class="body"><?php echo $body ?></div>
  </td>
 </tr>
</table>
<?php } else { ?>
<div class="body"><?php echo $body ?></div>
<?php } ?>

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
<div class="commentbody">
 <div style="width:100%;">
  <?php echo $page_comments ?>
 </div>
</div>
<?php } ?>

<?php echo $system_notification ?>

<?php echo $this->root->hr ?>

<?php if ($this->cont['PKWK_SKIN_SHOW$toolbar']) { ?>
<!-- Toolbar -->
<div class="toolbar">

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
		<?php $toolbar($this, 'upload', 20, 20, $upload_js) ?>
	<?php } ?>
	<?php $toolbar($this, 'copy') ?>
	<?php $toolbar($this, 'rename') ?>
<?php } ?>
 <?php $toolbar($this, 'reload') ?>
<?php } ?>
<?php $toolbar($this, 'print') ?>
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
 <?php $toolbar($this, 'powered', 16, 16, 'class="ext_noicon"') ?>
</div>
<?php } // PKWK_SKIN_SHOW$toolbar ?>

<?php if ($is_page) { ?>
<table class="footer_pginfo">
<tr><th colspan="2"><?php echo $lang['pageinfo'] ?></th></tr>
<tr><td><?php echo $lang['pagename'] ?> :</td><td><?php echo $_page ?></td></tr>
<tr><td><?php echo $lang['pagealias'] ?> :</td><td><?php echo $pginfo['alias'] ?></td></tr>
<tr><td><?php echo $lang['pageowner'] ?> :</td><td><?php echo $pginfo['pageowner'] ?></td></tr>
<tr><th colspan="2"><?php echo $lang['readable'] ?></th></tr>
<tr><td><?php echo $lang['groups'] ?> :</td><td><?php echo $pginfo['readableGroups'] ?></td></tr>
<tr><td><?php echo $lang['users'] ?> :</td><td><?php echo $pginfo['readableUsers'] ?></td></tr>
<tr><th colspan="2"><?php echo $lang['editable'] ?></th></tr>
<tr><td><?php echo $lang['groups'] ?> :</td><td><?php echo $pginfo['editableGroups'] ?></td></tr>
<tr><td><?php echo $lang['users'] ?> :</td><td><?php echo $pginfo['editableUsers'] ?></td></tr>
</table>
<?php } ?>

<?php if ($is_page) echo $this->do_plugin_convert('counter') ?>

<?php if ($princeps_date != '') { ?>
<div class="lastmodified"><?php echo $lang['princeps'] ?>: <?php echo $princeps_date ?></div>
<?php } ?>
<?php if ($lastmodified != '') { ?>
<div class="lastmodified"><?php echo $lang['lastmodify'] ?>: <?php echo $lastmodified ?> by <?php echo $pginfo['lastuname'] ?></div>
<?php } ?>

<?php if ($related != '') { ?>
<div class="related"><?php echo $lang['linkpage'] ?>: <?php echo $related ?></div>
<?php } ?>
<div class="footer">
 <p><?php echo $lang['siteadmin'] ?>: <a href="<?php echo $this->root->modifierlink ?>"><?php echo $this->root->modifier ?></a></p>
<?php if ($is_admin) { ?>
 <?php echo $this->cont['S_COPYRIGHT'] ?>.
 Powered by PHP <?php echo PHP_VERSION ?>. HTML convert time: <?php echo $taketime ?> sec.
<?php } // $is_admin ?>
</div>
</div>
