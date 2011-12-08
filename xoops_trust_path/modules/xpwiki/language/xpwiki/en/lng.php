<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: lng.php,v 1.30 2011/12/08 07:01:00 nao-pon Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki message file (English)


// NOTE: Encoding of this file, must equal to encoding setting

// Q & A Verification
$root->riddles = array(
//	'Question' => 'Answer',
	'a, b, c and next is?' => 'd',
	'1 + 1 = ?' => '2',
	'10 - 5 = ?' => '5',
	'a, *, c ... what is *?' => 'b',
	'Please rewrite "ABC" to lowercase.' => 'abc',
);

///////////////////////////////////////
// Page titles
$root->_title_cannotedit = ' $1 is not editable';
$root->_title_edit       = 'Edit of  $1';
$root->_title_preview    = 'Preview of  $1';
$root->_title_collided   = 'On updating  $1, a collision has occurred.';
$root->_title_updated    = ' $1 was updated';
$root->_title_deleted    = ' $1 was deleted';
$root->_title_help       = 'Help';
$root->_title_invalidwn  = 'It is not a valid WikiName';
$root->_title_backuplist = 'Backup list';
$root->_title_ng_riddle  = 'Failed in the Q & A verification.<br />Preview of  $1';
$root->_title_backlink   = 'Backlinks for: %s';

///////////////////////////////////////
// Messages
$root->_msg_unfreeze = 'Unfreeze';
$root->_msg_preview  = 'To confirm the changes, click the button at the bottom of the page';
$root->_msg_preview_delete = '(The contents of the page are empty. Updating deletes this page.)';
$root->_msg_collided = 'It seems that someone has already updated this page while you were editing it.<br />
 + is placed at the beginning of a line that was newly added.<br />
 ! is placed at the beginning of a line that has possibly been updated.<br />
 Edit those lines, and submit again.';

$root->_msg_collided_auto = 'It seems that someone has already updated this page while you were editing it.<br /> The collision has been corrected automatically, but there may still be some problems with the page.<br />
 To confirm the changes to the page, press [Update].<br />';


$root->_msg_invalidiwn  = ' $1 is not a valid $2.';
$root->_msg_invalidpass = 'Invalid password.';
$root->_msg_notfound    = 'The page was not found.';
$root->_msg_addline     = 'The added line is <span class="diff_added">THIS COLOR</span>.';
$root->_msg_delline     = 'The deleted line is <span class="diff_removed">THIS COLOR</span>.';
$root->_msg_goto        = 'Go to $1.';
$root->_msg_andresult   = 'In the page <strong> $2</strong>, <strong> $3</strong> pages that contain all the terms $1 were found.';
$root->_msg_orresult    = 'In the page <strong> $2</strong>, <strong> $3</strong> pages that contain at least one of the terms $1 were found.';
$root->_msg_notfoundresult = 'No page which contains $1 has been found.';
$root->_msg_symbol      = 'Symbols';
$root->_msg_other       = 'Others';
$root->_msg_help        = 'View Text Formatting Rules';
$root->_msg_week        = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
$root->_msg_content_back_to_top = '<div class="jumpmenu"><a href="#'.$root->mydirname.'_navigator" title="Page Top"><img src="'.$const['LOADER_URL'].'?src=arrow_up.png" alt="Page Top" width="16" height="16" /></a></div>';
$root->_msg_word        = 'These search terms have been highlighted:';
$root->_msg_not_readable   = 'You don\'t have enough permission for read.';
$root->_msg_not_editable   = 'You don\'t have enough permission for edit.';
$root->_msg_with_twitter   = 'Notifies to Twitter.';

///////////////////////////////////////
// Symbols
$root->_symbol_anchor   = 'src:anchor.png,width:12,height:12';
$root->_symbol_noexists = '<img src="'.$const['IMAGE_DIR'].'paraedit.png" alt="Edit" height="9" width="9" />';

///////////////////////////////////////
// Form buttons
$root->_btn_preview   = 'Preview';
$root->_btn_repreview = 'Preview again';
$root->_btn_update    = 'Update';
$root->_btn_cancel    = 'Cancel';
$root->_btn_notchangetimestamp = 'Do not change timestamp';
$root->_btn_addtop    = 'Add to top of page';
$root->_btn_template  = 'Use page as template';
$root->_btn_load      = 'Load';
$root->_btn_edit      = 'Edit';
$root->_btn_delete    = 'Delete';
$root->_btn_reading   = 'Reading of a page initial';
$root->_btn_alias     = 'Page aliases <span class="edit_form_note">(Split with "<span style="color:red;font-weight:bold;font-size:120%;">:</span>"[Colon])</span>';
$root->_btn_alias_lf  = 'Page aliases <span class="edit_form_note">(Split with "<span style="color:red;font-weight:bold;font-size:120%;">Each line</span>")</span>';
$root->_btn_riddle    = 'Q &amp; A Verification: <span class="edit_form_note">Please answer next a question before page update. (needless at preview)</span>';
$root->_btn_pgtitle   = 'Page title<span class="edit_form_note">( Auto with blank )</span>';
$root->_btn_pgorder   = 'Page order<span class="edit_form_note">( 0-9 Decimal Default:1 )</span>';
$root->_btn_other_op  = 'Show detailed input items.';
$root->_btn_emojipad  = 'Pictogram pad';
$root->_btn_esummary  = 'Edit Summary';
$root->_btn_source    = 'Details';

///////////////////////////////////////
// Authentication
$root->_title_cannotread = ' $1 is not readable';
$root->_msg_auth         = 'PukiWikiAuth';

///////////////////////////////////////
// Page name
$root->rule_page = 'FormattingRules';	// Formatting rules
$root->help_page = 'Help';		// Help

///////////////////////////////////////
// TrackBack (REMOVED)
$root->_tb_date   = 'F j, Y, g:i A';

/////////////////////////////////////////////////
// No subject (article)
$root->_no_subject = 'no subject';

/////////////////////////////////////////////////
// No name (article,comment,pcomment)
$root->_no_name = '';

/////////////////////////////////////////////////
// Title of the page contents list
$root->contents_title = 'Table of contents';

/////////////////////////////////////////////////
// Skin
/////////////////////////////////////////////////

$root->_LANG['skin']['topage']    = 'Back to page';
$root->_LANG['skin']['add']       = 'Add';
$root->_LANG['skin']['backup']    = 'Backup';
$root->_LANG['skin']['copy']      = 'Copy';
$root->_LANG['skin']['diff']      = 'Diff';
$root->_LANG['skin']['back']      = 'History';
$root->_LANG['skin']['edit']      = 'Edit';
$root->_LANG['skin']['filelist']  = 'Pages filename';	// List of filenames
$root->_LANG['skin']['attaches']  = 'Atattches';
$root->_LANG['skin']['freeze']    = 'Freeze';
$root->_LANG['skin']['help']      = 'Help';
$root->_LANG['skin']['list']      = 'Page list';
$root->_LANG['skin']['list_s']    = 'List';
$root->_LANG['skin']['new']       = 'New Page';
$root->_LANG['skin']['new_s']     = 'New';
$root->_LANG['skin']['newsub']    = 'New SubPage';
$root->_LANG['skin']['newsub_s']  = 'Sub';
$root->_LANG['skin']['menu']      = 'Menu';
$root->_LANG['skin']['header']    = 'Header';
$root->_LANG['skin']['footer']    = 'Foter';
$root->_LANG['skin']['rdf']       = 'RDF of recent changes';
$root->_LANG['skin']['recent']    = 'Recent changes';	// RecentChanges
$root->_LANG['skin']['recent_s']  = 'Recent';
$root->_LANG['skin']['refer']     = 'Referer';	// Show list of referer
$root->_LANG['skin']['reload']    = 'Reload';
$root->_LANG['skin']['rename']    = 'Rename';	// Rename a page (and related)
$root->_LANG['skin']['rss']       = 'RSS of recent changes';
$root->_LANG['skin']['rss10']     = $root->_LANG['skin']['rss'] . ' (RSS 1.0)';
$root->_LANG['skin']['rss20']     = $root->_LANG['skin']['rss'] . ' (RSS 2.0)';
$root->_LANG['skin']['atom']      = $root->_LANG['skin']['rss'] . ' (RSS Atom)';
$root->_LANG['skin']['search']    = 'Search';
$root->_LANG['skin']['search_s']  = 'Search';
$root->_LANG['skin']['top']       = 'Front page';	// Top page
$root->_LANG['skin']['trackback'] = 'Trackback';	// Show list of trackback
$root->_LANG['skin']['unfreeze']  = 'Unfreeze';
$root->_LANG['skin']['upload']    = 'Upload';	// Attach a file
$root->_LANG['skin']['pginfo']    = 'Permission';
$root->_LANG['skin']['comments']  = 'Comments';
$root->_LANG['skin']['lastmodify']= 'Last-modified';
$root->_LANG['skin']['linkpage']  = 'Links';
$root->_LANG['skin']['pagealias'] = 'Page aliases';
$root->_LANG['skin']['pageowner'] = 'Page owner';
$root->_LANG['skin']['siteadmin'] = 'Site admin';
$root->_LANG['skin']['none']      = 'None';
$root->_LANG['skin']['pageinfo']  = 'Page Info';
$root->_LANG['skin']['pagename']  = 'Page Name';
$root->_LANG['skin']['readable']  = 'Can Read';
$root->_LANG['skin']['editable']  = 'Can Edit';
$root->_LANG['skin']['groups']    = 'Groups';
$root->_LANG['skin']['users']     = 'Users';
$root->_LANG['skin']['perm']['all']  = 'All visitors';
$root->_LANG['skin']['perm']['none'] = 'No one';
$root->_LANG['skin']['print']     = 'Print View';
$root->_LANG['skin']['print_s']   = 'Print';
$root->_LANG['skin']['powered']   = 'Powered by xpWiki';
$root->_LANG['skin']['powered_s'] = 'xpWiki';
$root->_LANG['skin']['princeps']  = 'Princeps date';

///////////////////////////////////////
// Plug-in message
///////////////////////////////////////
// add.inc.php
$root->_title_add = 'Add to $1';
$root->_msg_add   = 'Two and the contents of an input are added for a new-line to the contents of a page of present addition.';
	// This message is such bad english that I don't understand it, sorry. --Bjorn De Meyer

///////////////////////////////////////
// article.inc.php
$root->_btn_name    = 'Name: ';
$root->_btn_article = 'Submit';
$root->_btn_subject = 'Subject: ';
$root->_msg_article_mail_sender = 'Author: ';
$root->_msg_article_mail_page   = 'Page: ';

///////////////////////////////////////
// attach.inc.php
$root->_attach_messages = array(
	'msg_uploaded' => 'Uploaded the file to  $1',
	'msg_deleted'  => 'Deleted the file in  $1',
	'msg_freezed'  => 'The file has been frozen.',
	'msg_unfreezed'=> 'The file has been unfrozen',
	'msg_renamed'  => 'The file has been renamed',
	'msg_upload'   => 'Upload to $1',
	'msg_info'     => 'File information',
	'msg_confirm'  => '<p>Delete %s.</p>',
	'msg_list'     => 'List of attached file(s)',
	'msg_listpage' => 'File already exists in  $1',
	'msg_listall'  => 'Attached file list of all pages',
	'msg_file'     => 'Attach file',
	'msg_maxsize'  => 'Maximum file size is %s.',
	'msg_count'    => ' <span class="small">%sDL</span>',
	'msg_password' => 'Password to this file (required)',
	'msg_password2'=> 'Password for this file',
	'msg_adminpass'=> 'Administrator password',
	'msg_delete'   => 'Delete file.',
	'msg_backup'   => 'Make backup',
	'msg_freeze'   => 'Freeze file.',
	'msg_unfreeze' => 'Unfreeze file.',
	'msg_isfreeze' => 'File is frozen.',
	'msg_rename'   => 'Rename',
	'msg_newname'  => 'New file name',
	'msg_require'  => '(Require password specified when uploading.)',
	'msg_filesize' => 'size',
	'msg_date'     => 'date',
	'msg_dlcount'  => 'access count',
	'msg_md5hash'  => 'MD5 hash',
	'msg_page'     => 'Page',
	'msg_filename' => 'Stored filename',
	'msg_owner'    => 'Owner',
	'err_noparm'   => 'Cannot upload/delete file in  $1',
	'err_exceed'   => 'File size too large to  $1',
	'err_exists'   => 'File already exists in  $1',
	'err_notfound' => 'Could not fid the file in  $1',
	'err_noexist'  => 'File does not exist.',
	'err_delete'   => 'Cannot delete file in  $1',
	'err_rename'   => 'Cannot rename this file',
	'err_password' => 'Wrong password.',
	'err_adminpass'=> 'Wrong administrator password',
	'err_nopage'   => 'A page "$1" not found. Please make a page before.',
	'btn_upload'   => 'Upload',
	'btn_upload_fm'=> 'Upload Form',
	'btn_info'     => 'Info',
	'btn_submit'   => 'Submit',
	'msg_copyrighted'  => 'The attached file was copyrighting protected.',
	'msg_uncopyrighted'=> 'The copyright protection of the attached file was released.',
	'msg_copyright'  => 'The attached file was copyrighting protected.',
	'msg_copyright0' => 'This file is mine or copyright-free.',
	'err_copyright'  => 'This file cannot not be displayed and be downloaded because it is not protected by the copyright.',
	'msg_noinline1'  => 'Prohibit the inline display.',
	'msg_noinline0-1'=> 'Release the inline display prohibition.',
	'msg_noinline-1' => 'Permit the inline display.',
	'msg_noinline01' => 'Release the inline display permission.',
	'msg_noinlined'  => 'The setting of the inline display of the attached file was registered.',
	'msg_unnoinlined'=> 'The setting of the inline display of the attached file was released.',
	'msg_nopcmd'     => 'Operation is not specified.',
	'err_extension'=> 'The extension cannot append the file of $1 because there is no ownerd authority on this page.',
	'msg_set_css'  => '$1 style sheet was set up.',
	'msg_unset_css'=> '$1 style sheet was canceled.',
	'msg_untar'    => 'UNTAR',
	'msg_search_updata'=> 'The uploaded data to this page is looked for.',
	'msg_paint_tool'=> 'Painting tool',
	'msg_shi'      => 'SHI PAINTER',
	'msg_shipro'   => 'SHI PAINTER Pro',
	'msg_width'    => 'Width',
	'msg_height'   => 'Height',
	'msg_max'      => 'Max size',
	'msg_do_paint' => 'Do painting',
	'msg_save_movie'=> 'Animation recording',
	'msg_adv_setting'=> '--- Extended specification ---',
	'msg_init_image'=> 'The picture file read into canvas (JPEG or GIF)',
	'msg_fit_size' => 'Canvas size is united with this picture.',
	'msg_extensions' => 'Extension of file that can be appended ( $1 )',
	'msg_rotated_ok' => 'Image was rotated.<br />It might not be correctly displayed by a browser as no reload.',
	'msg_rotated_ng' => 'Image was not able to be rotated.',
	'err_isflash' => 'Can not upload a Flash file.',
	'msg_make_thumb' => 'Make a thumbnail.(Image file only): ',
	'msg_sort_time' => 'Sort Time',
	'msg_sort_name' => 'Sort Name',
	'msg_list_view' => 'List View',
	'msg_image_view' => 'Image View',
	'msg_insert' => 'Insert',
	'msg_select_current' => ' (Current)',
	'msg_select_useful' => 'Pages for uploading',
	'msg_select_manyitems' => 'Pages with many files',
	'msg_noupload' => 'Cannot upload any files to $1.',
	'msg_show_all_pages' => 'Display on all pages',
	'msg_page_select' => 'Select a page',
	'msg_send_mms' => 'Send by MMS Mail',
	'msg_drop_files_here' => 'Drop files here to upload',
	'msg_for_upload' => 'There is no authority uploaded to this page.<br />In order to upload, please choose a page like "<span class="attachable">This Style</span>" at the <img src="'.$const['LOADER_URL'].'?src=page_attach.png" alt="Page" /> page selection.',
);

///////////////////////////////////////
// back.inc.php
$root->_msg_back_word = 'Back';

///////////////////////////////////////
// backup.inc.php
$root->_title_backup_delete  = 'Deleting backup of $1';
$root->_title_backupdiff     = 'Backup diff of $1(No. $2)';
$root->_title_backupnowdiff  = 'Backup diff of $1 vs current(No. $2)';
$root->_title_backupsource   = 'Backup source of  $1(No. $2)';
$root->_title_backup         = 'Backup of $1(No. $2)';
$root->_title_pagebackuplist = 'Backup list of $1';
$root->_title_backuplist     = 'Backup list';
$root->_msg_backup_deleted   = 'Backup of $1 has been deleted.';
$root->_msg_backup_adminpass = 'Please input the password for deleting.';
$root->_msg_backuplist       = 'List of Backups';
$root->_msg_nobackup         = 'There are no backup(s) of $1.';
$root->_msg_diff             = 'diff';
$root->_msg_nowdiff          = 'diff current';
$root->_msg_source           = 'source';
$root->_msg_backup           = 'backup';
$root->_msg_view             = 'View the $1.';
$root->_msg_deleted          = ' $1 has been deleted.';
$root->_msg_backupedit       = 'Edit Backup No.$1 as current.';
$root->_msg_current          = 'Cur';
$root->_title_backuprewind   = 'Rewind to backup No.$2 of $1.';
$root->_title_dorewind       = 'Rewind content & timestamp with a time "$1"';
$root->_msg_rewind           = 'Rewind';
$root->_msg_dorewind         = 'Rewind to backup No.$1';
$root->_msg_rewinded         = 'Rewound in backup No.$1.';
$root->_msg_nobackupnum      = 'Missing backup No.$1.';

///////////////////////////////////////
// calendar_viewer.inc.php
$root->_err_calendar_viewer_param2   = 'Wrong second parameter.';
$root->_msg_calendar_viewer_right    = 'Next %d&gt;&gt;';
$root->_msg_calendar_viewer_left     = '&lt;&lt; Prev %d';
$root->_msg_calendar_viewer_restrict = 'Due to the blocking, the calendar_viewer cannot refer to $1.';

///////////////////////////////////////
// calendar2.inc.php
$root->_calendar2_plugin_edit  = '[edit]';
$root->_calendar2_plugin_empty = '%s is empty.';

///////////////////////////////////////
// comment.inc.php
$root->_btn_name    = 'Name: ';
$root->_btn_comment = 'Post Comment';
$root->_msg_comment = 'Comment: ';
$root->_title_comment_collided = 'On updating  $1, a collision has occurred.';
$root->_msg_comment_collided   = 'It seems that someone has already updated the page you were editing.<br />
 The comment was added, alhough it may be inserted in the wrong position.<br />';

///////////////////////////////////////
// deleted.inc.php
$root->_deleted_plugin_title = 'The list of deleted pages';
$root->_deleted_plugin_title_withfilename = 'The list of deleted pages (with filename)';

///////////////////////////////////////
// diff.inc.php
$root->_title_diff         = 'Diff of  $1';
$root->_title_diff_delete  = 'Deleting diff of  $1';
$root->_msg_diff_deleted   = 'Diff of  $1 has been deleted.';
$root->_msg_diff_adminpass = 'Please input the password for deleting.';

///////////////////////////////////////
// filelist.inc.php (list.inc.php)
$root->_title_filelist = 'List of page files';

///////////////////////////////////////
// freeze.inc.php
$root->_title_isfreezed = ' $1 has already been frozen';
$root->_title_freezed   = ' $1 has been frozen.';
$root->_title_freeze    = 'Freeze  $1';
$root->_msg_freezing    = 'Please input the password for freezing.';
$root->_btn_freeze      = 'Freeze';

///////////////////////////////////////
// include.inc.php
$root->_msg_include_restrict = 'Due to the blocking, $1 cannot be include(d).';

///////////////////////////////////////
// insert.inc.php
$root->_btn_insert = 'add';

///////////////////////////////////////
// interwiki.inc.php
$root->_title_invalidiwn = 'This is not a valid InterWikiName';

///////////////////////////////////////
// list.inc.php
$root->_title_list = 'List of pages';

///////////////////////////////////////
// ls2.inc.php
$root->_ls2_err_nopages = '<p>There is no child page in \' $1\'</p>';
$root->_ls2_msg_title   = 'List of pages which begin with \' $1\'';

///////////////////////////////////////
// memo.inc.php
$root->_btn_memo_update = 'update';

///////////////////////////////////////
// navi.inc.php
$root->_navi_prev = 'Prev';
$root->_navi_next = 'Next';
$root->_navi_up   = 'Up';
$root->_navi_home = 'Home';

///////////////////////////////////////
// newpage.inc.php
$root->_msg_newpage = 'New page';

///////////////////////////////////////
// paint.inc.php
$root->_paint_messages = array(
	'field_name'    => 'Name',
	'field_filename'=> 'Filename',
	'field_comment' => 'Comment',
	'btn_submit'    => 'paint',
	'msg_max'       => '(Max %d x %d)',
	'msg_title'     => 'Paint and Attach to  $1',
	'msg_title_collided' => 'On updating  $1, there was a collision.',
	'msg_collided'  => 'It seems that someone has already updated this page while you were editing it.<br />
 The picture and the comment were added to this page, but there may be a problem.<br />'
);

///////////////////////////////////////
// pcomment.inc.php
$root->_pcmt_messages = array(
	'btn_name'       => 'Name: ',
	'btn_comment'    => 'Post Comment',
	'msg_comment'    => 'Comment: ',
	'msg_recent'     => 'Show recent %d comments.',
	'msg_all'        => 'Go to the comment page.',
	'msg_none'       => 'No comment.',
	'title_collided' => 'On updating  $1, there was a collision.',
	'msg_collided'   => 'It seems that someone has already updated this page while you were editing it.<br />
	The comment was added to the page, but there may be a problem.<br />',
	'err_pagename'   => '[[%s]] : not a valid page name.',
);
$root->_msg_pcomment_restrict = 'Due to the blocking, no comments could be read from  $1 at all.';

///////////////////////////////////////
// popular.inc.php
$root->_popular_plugin_frame       = '<h5>Popular(%1$d)%3$s</h5><div>%2$s</div>';
$root->_popular_plugin_today_frame = '<h5>Today\'s(%1$d)%3$s</h5><div>%2$s</div>';
$root->_popular_plugin_yesterday_frame = '<h5>Yesterday\'s(%1$d)%3$s</h5><div>%2$s</div>';

///////////////////////////////////////
// recent.inc.php
$root->_recent_plugin_frame = '<h5>%srecent(%d)</h5>
 <div>%s</div>';

///////////////////////////////////////
// referer.inc.php
$root->_referer_msg = array(
	'msg_H0_Refer'       => 'Referer',
	'msg_Hed_LastUpdate' => 'LastUpdate',
	'msg_Hed_1stDate'    => 'First Register',
	'msg_Hed_RefCounter' => 'RefCounter',
	'msg_Hed_Referer'    => 'Referer',
	'msg_Fmt_Date'       => 'F j, Y, g:i A',
	'msg_Chr_uarr'       => '&uArr;',
	'msg_Chr_darr'       => '&dArr;',
);

///////////////////////////////////////
// rename.inc.php
$root->_rename_messages  = array(
	'err'            => '<p>error:%s</p>',
	'err_nomatch'    => 'no matching page(s)',
	'err_notvalid'   => 'the new name is invalid.',
	'err_adminpass'  => 'Incorrect administrator password.',
	'err_notpage'    => '%s is not a valid pagename.',
	'err_norename'   => 'cannot rename %s.',
	'err_already'    => 'The following pages already exists.%s',
	'err_already_below' => 'The following files already exist.',
	'msg_title'      => 'Rename page',
	'msg_page'       => 'specify source page name',
	'msg_regex'      => 'rename with regular expressions.',
	'msg_regex'      => 'Regular expressions',
	'msg_part_rep'   => 'Replaces partial matches',
	'msg_related'    => 'related pages',
	'msg_do_related' => 'A related page is also renamed.',
	'msg_rename'     => 'rename %s',
	'msg_oldname'    => 'current page name',
	'msg_newname'    => 'new page name',
	'msg_adminpass'  => 'Administrator password',
	'msg_arrow'      => '->',
	'msg_exist_none' => 'page is not processed when it already exists.',
	'msg_exist_overwrite' => 'page is overwritten when it already exists.',
	'msg_confirm'    => 'The following files will be renamed.',
	'msg_result'     => 'The following files have been overwritten.',
	'btn_submit'     => 'Submit',
	'btn_next'       => 'Next'
);

///////////////////////////////////////
// search.inc.php
$root->_title_search  = 'Search';
$root->_title_result  = 'Search result of  $1';
$root->_msg_searching = 'Key words are case-insenstive, and are searched for in all pages.';
$root->_btn_search    = 'Search';
$root->_btn_and       = 'AND';
$root->_btn_or        = 'OR';
$root->_search_pages  = 'Search for page starts from $1';
$root->_search_all    = 'Search for all pages';

///////////////////////////////////////
// source.inc.php
$root->_source_messages = array(
	'msg_title'    => 'Source of  $1',
	'msg_notfound' => ' $1 was not found.',
	'err_notfound' => 'cannot display the page source.'
);

///////////////////////////////////////
// template.inc.php
$root->_msg_template_start   = 'Start:<br />';
$root->_msg_template_end     = 'End:<br />';
$root->_msg_template_page    = '$1/copy';
$root->_msg_template_refer   = 'Page:';
$root->_msg_template_force   = 'Edit with a page name which already exists';
$root->_err_template_already = ' $1 already exists.';
$root->_err_template_invalid = ' $1 is not a valid page name.';
$root->_btn_template_create  = 'Create';
$root->_title_templatei      = 'create a new page, using  $1 as a template.';

///////////////////////////////////////
// tracker.inc.php
$root->_tracker_messages = array(
	'msg_list'   => 'List items of  $1',
	'msg_back'   => '<p> $1</p>',
	'msg_limit'  => 'top  $2 results out of  $1.',
	'btn_page'   => 'Page',
	'btn_name'   => 'Name',
	'btn_real'   => 'Realname',
	'btn_submit' => 'Add',
	'btn_date'   => 'Date',
	'btn_refer'  => 'Refer page',
	'btn_base'   => 'Base page',
	'btn_update' => 'Update',
	'btn_past'   => 'Past',
);

///////////////////////////////////////
// unfreeze.inc.php
$root->_title_isunfreezed = ' $1 is not frozen';
$root->_title_unfreezed   = ' $1 has been unfrozen.';
$root->_title_unfreeze    = 'Unfreeze  $1';
$root->_msg_unfreezing    = 'Please input the password for unfreezing.';
$root->_btn_unfreeze      = 'Unfreeze';

///////////////////////////////////////
// versionlist.inc.php
$root->_title_versionlist = 'version list';

///////////////////////////////////////
// vote.inc.php
$root->_vote_plugin_choice = 'Selection';
$root->_vote_plugin_votes  = 'Vote';

///////////////////////////////////////
// yetlist.inc.php
$root->_title_yetlist = 'List of pages which have not yet been created.';
$root->_err_notexist  = 'All pages have been created.';
