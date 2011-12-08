<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: lng.php,v 1.25 2011/12/08 07:01:00 nao-pon Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki message file (English)
//
// German Translation Version 1.0 (11.03.2008)
// Translation English --> German: Octopus (hunter0815@googlemail.com)
// sicherlich steckt hier noch reichlich Qualitätspotential in den Übersetzungen ;-)


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
$root->_title_cannotedit = ' $1 ist nicht editierbar';
$root->_title_edit       = 'Bearbeiten von  $1';
$root->_title_preview    = 'Vorschau von  $1';
$root->_title_collided   = 'Beim updaten von  $1, ist eine Überschneidung aufgetreten.';
$root->_title_updated    = ' $1 wurde aktualisiert';
$root->_title_deleted    = ' $1 wurde gelöscht';
$root->_title_help       = 'Hilfe';
$root->_title_invalidwn  = 'Das ist kein gültiger WikiName';
$root->_title_backuplist = 'Backup Liste';
$root->_title_ng_riddle  = 'Fehler in der Q & A Prüfung.<br />Vorschau von  $1';
$root->_title_backlink   = 'Backlinks for: %s';

///////////////////////////////////////
// Messages
$root->_msg_unfreeze = 'Entsperren';
$root->_msg_preview  = 'Um die Änderungen zu bestätigen, klicke auf den Button am Ende der Seite';
$root->_msg_preview_delete = '(Der Inhalt dieser Seite ist leer. Speichern löscht diese Seite!)';
$root->_msg_collided = 'Anscheinend hat jemand die Seite aktualisiert, während Du sie bearbeitet hast.<br />
 + ist vor den Zeilen vermerkt, die neu hinzugekommen sind.<br />
 ! ist vor den Zeilen vermerkt, die möglicherweise geändert wurden.<br />
 Ändere diese Zeilen und speichere sie erneut.';

$root->_msg_collided_auto = 'Anscheinend hat jemand die Seite aktualisiert, während Du sie bearbeitet hast.<br /> Die Überschneidung ist automatisch korrigiert worden. Möglicherweise bestehen trotzdem noch Probleme mit der Seite.<br />
 Um die Änderungen zu bestätigen drücke [Speichern].<br />';


$root->_msg_invalidiwn  = ' $1 ist nicht gültig $2.';
$root->_msg_invalidpass = 'Ungültiges Passwort.';
$root->_msg_notfound    = 'Die Seite wurde nicht gefunden.';
$root->_msg_addline     = 'Die hinzugefügte Zeile ist <span class="diff_added">THIS COLOR</span>.';
$root->_msg_delline     = 'Die gelöschte Zeile ist <span class="diff_removed">THIS COLOR</span>.';
$root->_msg_goto        = 'Gehe zu $1.';
$root->_msg_andresult   = 'Auf der Seite <strong> $2</strong>, <strong> $3</strong> wurden Seiten die alle Suchbegriffe enthalten $1 gefunden.';
$root->_msg_orresult    = 'Auf der Seite <strong> $2</strong>, <strong> $3</strong> wurden Seiten, die lediglich einen Suchbegriff enthalten $1 gefunden.';
$root->_msg_notfoundresult = 'Keine Seite, die $1 enthält wurde gefunden.';
$root->_msg_symbol      = 'Symbole';
$root->_msg_other       = 'Anderes';
$root->_msg_help        = 'Zeige Text Formatierungs-Regeln';
$root->_msg_week        = array('So','Mo','Di','Mi','Do','Fr','Sa');
$root->_msg_content_back_to_top = '<div class="jumpmenu"><a href="#'.$root->mydirname.'_navigator" title="Page Top"><img src="'.$const['LOADER_URL'].'?src=arrow_up.png" alt="Page Top" width="16" height="16" /></a></div>';
$root->_msg_word        = 'Diese Suchbegriffe wurden markiert:';
$root->_msg_not_readable   = 'Du hast keine Berechtigung zum Lesen.';
$root->_msg_not_editable   = 'Du hast keine Berechtigung zum Ändern.';
$root->_msg_with_twitter   = 'Benachrichtigt zu Twitter.';

///////////////////////////////////////
// Symbols
$root->_symbol_anchor   = 'src:anchor.png,width:12,height:12';
$root->_symbol_noexists = '<img src="'.$const['IMAGE_DIR'].'paraedit.png" alt="Edit" height="9" width="9">';

///////////////////////////////////////
// Form buttons
$root->_btn_preview   = 'Vorschau';
$root->_btn_repreview = 'erneute Vorschau';
$root->_btn_update    = 'Speichern';
$root->_btn_cancel    = 'Abbrechen';
$root->_btn_notchangetimestamp = 'Den Zeitstempel nicht ändern';
$root->_btn_addtop    = 'Füge zum Seitenanfang hinzu';
$root->_btn_template  = 'Benutze Seite als Vorlage';
$root->_btn_load      = 'Laden';
$root->_btn_edit      = 'Bearbeiten';
$root->_btn_delete    = 'Löschen';
$root->_btn_reading   = 'Lesen einer Einstiegsseite';
$root->_btn_alias     = 'Seiten Aliases <span class="edit_form_note">(Trenne mit "<span style="color:red;font-weight:bold;font-size:120%;">:</span>"[Colon])</span>';
$root->_btn_alias_lf  = 'Seiten Aliases <span class="edit_form_note">(Trenne mit "<span style="color:red;font-weight:bold;font-size:120%;">Each line</span>")</span></span>';
$root->_btn_riddle    = 'Q & A Bestätigung: <span class="edit_form_note">Bitte beantworte die nächste Frage vor dem Speichern der Seite. (nicht nötig für die Vorschau)</span>';
$root->_btn_pgtitle   = 'Page Titel<span class="edit_form_note">( Auto mit Lücke )</span>';
$root->_btn_pgorder   = 'Page Reihenfolge<span class="edit_form_note">( 0-9 Dezimal Versäumnis:1 )</span>';
$root->_btn_other_op  = 'Show listete Eingabestücke auf.';
$root->_btn_esummary  = 'Bearbeiten Zusammenfassung';
$root->_btn_source    = 'Details';

///////////////////////////////////////
// Authentication
$root->_title_cannotread = ' $1 ist nicht lesbar';
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
$root->_no_subject = 'keine Überschrift';

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
$root->_LANG['skin']['add']       = 'AHinzufügen';
$root->_LANG['skin']['backup']    = 'Backup';
$root->_LANG['skin']['copy']      = 'Kopieren';
$root->_LANG['skin']['diff']      = 'Diff';
$root->_LANG['skin']['back']      = 'History';
$root->_LANG['skin']['edit']      = 'Ändern';
$root->_LANG['skin']['filelist']  = 'Seiten-Dateien Liste';	// List of filenames
$root->_LANG['skin']['attaches']  = 'Anhänge';
$root->_LANG['skin']['freeze']    = 'Sperren';
$root->_LANG['skin']['help']      = 'Hilfe';
$root->_LANG['skin']['list']      = 'Seiten Liste';
$root->_LANG['skin']['list_s']    = 'Liste';
$root->_LANG['skin']['new']       = 'Neu Page';
$root->_LANG['skin']['new_s']     = 'Neu';
$root->_LANG['skin']['newsub']    = 'Neu SubPage';
$root->_LANG['skin']['newsub_s']  = 'Sub';
$root->_LANG['skin']['menu']      = 'Menu';
$root->_LANG['skin']['header']    = 'Header';
$root->_LANG['skin']['footer']    = 'Foter';
$root->_LANG['skin']['rdf']       = 'RDF der letzten Änderungen';
$root->_LANG['skin']['recent']    = 'letzte Änderungen';	// RecentChanges
$root->_LANG['skin']['recent_s']  = 'letzte';
$root->_LANG['skin']['refer']     = 'Autoren';	// Show list of referer
$root->_LANG['skin']['reload']    = 'neu laden';
$root->_LANG['skin']['rename']    = 'Umbenennen';	// Rename a page (and related)
$root->_LANG['skin']['rss']       = 'RSS der letzten Änderungen';
$root->_LANG['skin']['rss10']     = $root->_LANG['skin']['rss'] . ' (RSS 1.0)';
$root->_LANG['skin']['rss20']     = $root->_LANG['skin']['rss'] . ' (RSS 2.0)';
$root->_LANG['skin']['atom']      = $root->_LANG['skin']['rss'] . ' (RSS Atom)';
$root->_LANG['skin']['search']    = 'Suche';
$root->_LANG['skin']['search_s']  = 'Suche';
$root->_LANG['skin']['top']       = 'Hauptseite';	// Top page
$root->_LANG['skin']['trackback'] = 'Trackback';	// Show list of trackback
$root->_LANG['skin']['unfreeze']  = 'Entsperren';
$root->_LANG['skin']['upload']    = 'Upload';	// Attach a file
$root->_LANG['skin']['pginfo']    = 'Berechtigung';
$root->_LANG['skin']['comments']  = 'Kommentare';
$root->_LANG['skin']['lastmodify']= 'letzte Änderung';
$root->_LANG['skin']['linkpage']  = 'Links';
$root->_LANG['skin']['pagealias'] = 'Seiten Aliases';
$root->_LANG['skin']['pageowner'] = 'Seiten Inhaber';
$root->_LANG['skin']['siteadmin'] = 'Seiten Admin';
$root->_LANG['skin']['none']      = 'None';
$root->_LANG['skin']['pageinfo']  = 'Page Info';
$root->_LANG['skin']['pagename']  = 'Page Name';
$root->_LANG['skin']['readable']  = 'Can Read';
$root->_LANG['skin']['editable']  = 'Can Edit';
$root->_LANG['skin']['groups']    = 'Groups';
$root->_LANG['skin']['users']     = 'Users';
$root->_LANG['skin']['perm']['all']  = 'All visitors';
$root->_LANG['skin']['perm']['none'] = 'No one';
$root->_LANG['skin']['print']     = 'Anzeichen für Druck';
$root->_LANG['skin']['print_s']   = 'Druck';
$root->_LANG['skin']['powered']   = 'Powered by xpWiki';
$root->_LANG['skin']['powered_s'] = 'xpWiki';
$root->_LANG['skin']['princeps']  = 'Princeps-Datum';

///////////////////////////////////////
// Plug-in message
///////////////////////////////////////
// add.inc.php
$root->_title_add = 'Füge zu $1 hinzu';
$root->_msg_add   = 'Zwei und der Inhalt einer Eingabe wurden in einer neuen Zeile dem bestehenden Inhalt der Seite hinzugefügt.';
	// This message is such bad english that I don't understand it, sorry. --Bjorn De Meyer

///////////////////////////////////////
// article.inc.php
$root->_btn_name    = 'Name: ';
$root->_btn_article = 'Speichern';
$root->_btn_subject = 'Titel: ';
$root->_msg_article_mail_sender = 'Author: ';
$root->_msg_article_mail_page   = 'Seite: ';

///////////////////////////////////////
// attach.inc.php
$root->_attach_messages = array(
	'msg_uploaded' => 'Datei hochgeladen zu  $1',
	'msg_deleted'  => 'Datei gelöscht in  $1',
	'msg_freezed'  => 'Die Datei wurde gesperrt',
	'msg_unfreezed'=> 'Die Datei wurde entsperrt',
	'msg_renamed'  => 'Die Datei wurde umbenannt',
	'msg_upload'   => 'Hochgeladen zu $1',
	'msg_info'     => 'Datei Information',
	'msg_confirm'  => '<p>Lösche %s.</p>',
	'msg_list'     => 'Liste von angehängten Dateien:',
	'msg_listpage' => 'Die Datei ist bereits in  $1',
	'msg_listall'  => 'Liste der angehängten Dateien von allen Seiten',
	'msg_file'     => 'Datei anhängen',
	'msg_maxsize'  => 'Maximale Dateigröße %s.',
	'msg_count'    => ' <span class="small">%sDL</span>',
	'msg_password' => 'Paßwort zu dieser Akte (verlangen)',
	'msg_password2'=> 'Paßwort für diese Akte',
	'msg_adminpass'=> 'Administrator Passwort',
	'msg_delete'   => 'Datei löschen.',
	'msg_backup'   => 'Unterstützen',
	'msg_freeze'   => 'Datei sperren.',
	'msg_unfreeze' => 'Datei entsperren.',
	'msg_isfreeze' => 'Datei ist gesperrt.',
	'msg_rename'   => 'Umbenennen',
	'msg_newname'  => 'Neuer Dateiname',
	'msg_require'  => '(Administrator Passwort erforderlich)',
	'msg_filesize' => 'Größe',
	'msg_date'     => 'Datum',
	'msg_dlcount'  => 'Zugangszähler',
	'msg_md5hash'  => 'MD5 hash',
	'msg_page'     => 'Seite',
	'msg_filename' => 'gespeicherter Dateiname',
	'msg_owner'    => 'Inhaber',
	'err_noparm'   => 'Datei kann nicht hochgeladen/gelöscht werden in  $1',
	'err_exceed'   => 'Datei´größe zu groß für  $1',
	'err_exists'   => 'Datei existiert bereits in  $1',
	'err_notfound' => 'Datei kann nicht gefunden werden in  $1',
	'err_noexist'  => 'Datei existiert nicht.',
	'err_delete'   => 'Datei kann nicht gelöscht werden in  $1',
	'err_rename'   => 'Datei kan nicht umbenannt werden',
	'err_password' => 'falsches Passwort.',
	'err_adminpass'=> 'Falsches Administrator Passwort',
	'err_nopage'   => 'Eine Seite "$1" nicht gefunden. bitte erzeuge die Seite zuerst.',
	'btn_upload'   => 'Upload',
	'btn_upload_fm'=> 'Upload Form',
	'btn_info'     => 'Info',
	'btn_submit'   => 'Speichern',
	'msg_copyrighted'  => 'Die angehängte Datei ist kopiergeschützt.',
	'msg_uncopyrighted'=> 'Der Kopierschutz der angehängten Datei wurde freigegeben.',
	'msg_copyright'  => 'Die angehängte Datei war kopiergeschützt.',
	'msg_copyright0' => 'Diese Datei ist mine oder ohne Kopierschutz.',
	'err_copyright'  => 'Diese Datei kann nicht angezeigt oder heruntergeladen werden weil sie nicht copyright geschützt ist.',
	'msg_noinline1'  => 'Prohibit the inline display.',
	'msg_noinline0-1'=> 'Release the inline display prohibition.',
	'msg_noinline-1' => 'Permit the inline display.',
	'msg_noinline01' => 'Release the inline display permission.',
	'msg_noinlined'  => 'The setting of the inline display of the attached file was registered.',
	'msg_unnoinlined'=> 'The setting of the inline display of the attached file was released.',
	'msg_nopcmd'     => 'Operation is not specified.',
	'err_extension'=> 'Die Erweiterung kann nicht angehängt werden an das File von $1 weil keine Inhaber-Befugnis auf dieser Seite besteht.',
	'msg_set_css'  => '$1 style sheet wurde eingerichtet.',
	'msg_unset_css'=> '$1 style sheet wurde abgebrochen.',
	'msg_untar'    => 'UNTAR',
	'msg_search_updata'=> 'Die hochgeladenen Daten dieser Seite sind herzlich willkommen :-)!.',
	'msg_paint_tool'=> 'Malwerkzeug',
	'msg_shi'      => 'SHI PAINTER',
	'msg_shipro'   => 'SHI PAINTER Pro',
	'msg_width'    => 'Breite',
	'msg_height'   => 'Höhe',
	'msg_max'      => 'maximale Größe',
	'msg_do_paint' => 'Male',
	'msg_save_movie'=> 'Animation recording',
	'msg_adv_setting'=> '--- ergänzende Beschreibung ---',
	'msg_init_image'=> 'Die Bild-Datei wurde in das Gemälde geladen (JPEG or GIF)',
	'msg_fit_size' => 'Gemälde-Größe ist verbunden mit diesem Bild.',
	'msg_extensions' => 'Ergänzungen zur Datei können beigefügt werden ( $1 )',
	'msg_rotated_ok' => 'Bild wurde gedreht.<br />Ggf. wird es bis zu einer Seitenaktualisierung des Browsers nicht richtig angezeigt.',
	'msg_rotated_ng' => 'Das Bild konte nicht gedreht werden.',
	'err_isflash' => 'Kann keine Flash-Datei hochladen.',
	'msg_make_thumb' => 'Mache ein Vorschaubild.(nur Bild-Dateien): ',
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
$root->_msg_back_word = 'zurück';

///////////////////////////////////////
// backup.inc.php
$root->_title_backup_delete  = 'Lösche Backup von  $1';
$root->_title_backupdiff     = 'Backup diff von  $1(Nr. $2)';
$root->_title_backupnowdiff  = 'Backup diff von  $1 vs aktuellen(Nr. $2)';
$root->_title_backupsource   = 'Backup Quelle von  $1(No. $2)';
$root->_title_backup         = 'Backup von  $1(No. $2)';
$root->_title_pagebackuplist = 'Backup Liste von  $1';
$root->_title_backuplist     = 'Backup Liste';
$root->_msg_backup_deleted   = 'Backup von $1 wurde gelöscht.';
$root->_msg_backup_adminpass = 'Bitte gib das Passwort zum Löschen ein.';
$root->_msg_backuplist       = 'Liste der Backups';
$root->_msg_nobackup         = 'Es gibt keine Backups von  $1.';
$root->_msg_diff             = 'diff';
$root->_msg_nowdiff          = 'diff Aktuelle';
$root->_msg_source           = 'Quelle';
$root->_msg_backup           = 'Backup';
$root->_msg_view             = 'Zeige das $1.';
$root->_msg_deleted          = ' $1 wurde gelöscht.';
$root->_msg_backupedit       = 'Ändere Backup Nr.$1 als aktuelles.';
$root->_msg_current          = 'Cur';
$root->_title_backuprewind   = 'Spulen Sie zu Unterstützung No.$2 von $1 zurück.';
$root->_title_dorewind       = 'Spulen Sie Inhalt & Zeitbriefmarke mit einer Zeit "$1" zurück.';
$root->_msg_rewind           = 'Spulen Sie zurück';
$root->_msg_dorewind         = 'Spulen Sie zu Hilfs No.$1 zurück';
$root->_msg_rewinded         = 'Zurückgespult in Backup No.$1.';
$root->_msg_nobackupnum      = 'Das Danebengehen Backup No.$1.';

///////////////////////////////////////
// calendar_viewer.inc.php
$root->_err_calendar_viewer_param2   = 'Falscher zweiter Parameter.';
$root->_msg_calendar_viewer_right    = 'Nächster %d&gt;&gt;';
$root->_msg_calendar_viewer_left     = '&lt;&lt; Prev %d';
$root->_msg_calendar_viewer_restrict = 'Aufgrund der Blockierung kann der calendar_viewer nicht verweisen auf $1.';

///////////////////////////////////////
// calendar2.inc.php
$root->_calendar2_plugin_edit  = '[ändern]';
$root->_calendar2_plugin_empty = '%s ist leer.';

///////////////////////////////////////
// comment.inc.php
$root->_btn_name    = 'Name: ';
$root->_btn_comment = 'Schreibe Kommentar';
$root->_msg_comment = 'Kommentar: ';
$root->_title_comment_collided = 'Beim Ändern  $1, ist eine Überschneidung aufgetreten.';
$root->_msg_comment_collided   = 'Anscheinend hat jemand während Deiner Änderung die Seite bereits aktualisiert.<br />
 Der Kommentar wurde hinzugefügt, ggf. wurde er aber an einer falschen Position eingefügt.<br />';

///////////////////////////////////////
// deleted.inc.php
$root->_deleted_plugin_title = 'Die Liste der gelöschten Seiten';
$root->_deleted_plugin_title_withfilename = 'Die Liste der gelöschten Seiten (inkl. Dateiname)';

///////////////////////////////////////
// diff.inc.php
$root->_title_diff         = 'Diff von  $1';
$root->_title_diff_delete  = 'Löschen diff von  $1';
$root->_msg_diff_deleted   = 'Diff von  $1 wurde gelöscht.';
$root->_msg_diff_adminpass = 'bitte das Passwort zum Löschen eingeben.';

///////////////////////////////////////
// filelist.inc.php (list.inc.php)
$root->_title_filelist = 'Liste der Seiten Dateien';

///////////////////////////////////////
// freeze.inc.php
$root->_title_isfreezed = ' $1 wurde bereits gesperrt';
$root->_title_freezed   = ' $1 wurde gesperrt.';
$root->_title_freeze    = 'Sperre  $1';
$root->_msg_freezing    = 'Bitte gib das Passwort zum Sperren ein.';
$root->_btn_freeze      = 'Sperre';

///////////////////////////////////////
// include.inc.php
$root->_msg_include_restrict = 'Aufgrund einer Sperre, $1 kann nicht eingefügt werden(d).';

///////////////////////////////////////
// insert.inc.php
$root->_btn_insert = 'hinzufügen';

///////////////////////////////////////
// interwiki.inc.php
$root->_title_invalidiwn = 'Das ist kein gültiger InterWikiName';

///////////////////////////////////////
// list.inc.php
$root->_title_list = 'Liste der Seiten';

///////////////////////////////////////
// ls2.inc.php
$root->_ls2_err_nopages = '<p>Es gibt keine Child-Seite in \' $1\'</p>';
$root->_ls2_msg_title   = 'Liste der Seiten die beginnen mit \' $1\'';

///////////////////////////////////////
// memo.inc.php
$root->_btn_memo_update = 'Aktualisierung';

///////////////////////////////////////
// navi.inc.php
$root->_navi_prev = 'Vorheriger';
$root->_navi_next = 'Nächster';
$root->_navi_up   = 'hoch';
$root->_navi_home = 'Home';

///////////////////////////////////////
// newpage.inc.php
$root->_msg_newpage = 'Neue Seite';

///////////////////////////////////////
// paint.inc.php
$root->_paint_messages = array(
	'field_name'    => 'Name',
	'field_filename'=> 'Dateiname',
	'field_comment' => 'Kommentar',
	'btn_submit'    => 'malen',
	'msg_max'       => '(Max %d x %d)',
	'msg_title'     => 'Malen und anhängen bei  $1',
	'msg_title_collided' => 'Beim Aktualisieren von  $1, ist eine Überschneidung aufgetreten.',
	'msg_collided'  => 'Anscheinend hat jemand diese Seite aktualisiert während Du sie gerade bearbeitest hast.<br />
 Das Bild und der Kommentar wurden hinzugefügt, evt. kann aber ein Problem bestehen.<br />'
);

///////////////////////////////////////
// pcomment.inc.php
$root->_pcmt_messages = array(
	'btn_name'       => 'Name: ',
	'btn_comment'    => 'Kommentar schreiben',
	'msg_comment'    => 'Kommentar: ',
	'msg_recent'     => 'Zeige letzte %d Kommentare.',
	'msg_all'        => 'Zur kommentarseite gehen.',
	'msg_none'       => 'Keine Kommentare.',
	'title_collided' => 'Bei der Aktualisierung  $1, war eine Überschneidung.',
	'msg_collided'   => 'nscheinend hat jemand diese Seite aktualisiert während Du sie gerade bearbeitest hast.<br />
	Der Kommentar wurde hinzugefügt, evt. kann aber ein Problem bestehen.<br />',
	'err_pagename'   => '[[%s]] : kein gültiger Seitenname.',
);
$root->_msg_pcomment_restrict = 'Aufgrund der Blockierung können keine Kommentare von   $1 gelesen werden.';

///////////////////////////////////////
// popular.inc.php
$root->_popular_plugin_frame       = '<h5>Populär(%1$d)%3$s</h5><div>%2$s</div>';
$root->_popular_plugin_today_frame = '<h5>Heute\'s(%1$d)%3$s</h5><div>%2$s</div>';
$root->_popular_plugin_yesterday_frame = '<h5>Gestern\'s(%1$d)%3$s</h5><div>%2$s</div>';

///////////////////////////////////////
// recent.inc.php
$root->_recent_plugin_frame = '<h5>%srecent(%d)</h5>
 <div>%s</div>';

///////////////////////////////////////
// referer.inc.php
$root->_referer_msg = array(
	'msg_H0_Refer'       => 'Referer',
	'msg_Hed_LastUpdate' => 'letzte Aktualisierung',
	'msg_Hed_1stDate'    => 'registriert seit',
	'msg_Hed_RefCounter' => 'RefZähler',
	'msg_Hed_Referer'    => 'Referer',
	'msg_Fmt_Date'       => 'F j, Y, g:i A',
	'msg_Chr_uarr'       => '&uArr;',
	'msg_Chr_darr'       => '&dArr;',
);

///////////////////////////////////////
// rename.inc.php
$root->_rename_messages  = array(
	'err'            => '<p>Fehler:%s</p>',
	'err_nomatch'    => 'keine passenden Seiten',
	'err_notvalid'   => 'der neue Name ist ungültig.',
	'err_adminpass'  => 'Ungütliges Administrator Passwort.',
	'err_notpage'    => '%s ist kein gültiger Seitenname.',
	'err_norename'   => 'kann nicht umbennenen %s.',
	'err_already'    => 'Die folgenden Seiten existieren bereits.%s',
	'err_already_below' => 'TDie folgenden Seiten existieren bereits.',
	'msg_title'      => 'Seite umbenennen',
	'msg_page'       => 'spezifiziere Quell Seiten Namen',
	'msg_regex'      => 'Regular expressions',
	'msg_part_rep'   => 'Umbenennen mit den Bezeichnungen.',
	'msg_related'    => 'verknüpfte Seiten',
	'msg_do_related' => 'Eine verknüpfte Seite wurde ebenfalls umbenannt.',
	'msg_rename'     => 'umbenennen %s',
	'msg_oldname'    => 'aktueller Seitenname',
	'msg_newname'    => 'neuer Seitenname',
	'msg_adminpass'  => 'Administrator Passwort',
	'msg_arrow'      => '->',
	'msg_exist_none' => 'Seite wird nicht verarbeitet, wenn sie bereits existiert.',
	'msg_exist_overwrite' => 'Seite wird überschrieben wenn Sie bereits existiert.',
	'msg_confirm'    => 'Die folgenden Dateien werden umbenannt.',
	'msg_result'     => 'Die folgenden Dateien wurden überschrieben.',
	'btn_submit'     => 'Speichern',
	'btn_next'       => 'Weiter'
);

///////////////////////////////////////
// search.inc.php
$root->_title_search  = 'Suchen';
$root->_title_result  = 'Suchergebnis von  $1';
$root->_msg_searching = 'Groß- oder Kleinschreibung der Keywörter wird nicht beachtet. Es wird in allen Seiten gesucht.';
$root->_btn_search    = 'Suche';
$root->_btn_and       = 'Und';
$root->_btn_or        = 'Oder';
$root->_search_pages  = 'Suche nach Seiten startet von $1';
$root->_search_all    = 'Suche nach allen Seiten';

///////////////////////////////////////
// source.inc.php
$root->_source_messages = array(
	'msg_title'    => 'Quelle von  $1',
	'msg_notfound' => ' $1 wurde nicht gefunden.',
	'err_notfound' => 'die Quellseite kann nicht angezeigt werden.'
);

///////////////////////////////////////
// template.inc.php
$root->_msg_template_start   = 'Start:<br />';
$root->_msg_template_end     = 'Ende:<br />';
$root->_msg_template_page    = '$1/Kopie';
$root->_msg_template_refer   = 'Seite:';
$root->_msg_template_force   = 'Ändere mit einem Seitennamen der bereits existiert.';
$root->_err_template_already = ' $1 existiert bereits.';
$root->_err_template_invalid = ' $1 ist kein gültiger Seitenname.';
$root->_btn_template_create  = 'Anlegen';
$root->_title_templatei      = 'Neue Seite anlegen und  $1 als Vorlage benutzen.';

///////////////////////////////////////
// tracker.inc.php
$root->_tracker_messages = array(
	'msg_list'   => 'Liste mit Einträgen von  $1',
	'msg_back'   => '<p> $1</p>',
	'msg_limit'  => 'Top  $2 Resultate von  $1.',
	'btn_page'   => 'Seite',
	'btn_name'   => 'Name',
	'btn_real'   => 'Richtiger Name',
	'btn_submit' => 'Hinzufügen',
	'btn_date'   => 'Datum',
	'btn_refer'  => 'Refer Seite',
	'btn_base'   => 'Ausgangsseite',
	'btn_update' => 'Aktualisieren',
	'btn_past'   => 'Past',
);

///////////////////////////////////////
// unfreeze.inc.php
$root->_title_isunfreezed = ' $1 ist nicht gesperrt';
$root->_title_unfreezed   = ' $1 wurde entsperrt.';
$root->_title_unfreeze    = 'Entsperre  $1';
$root->_msg_unfreezing    = 'Bitte gib das Passwort zum Ensperren ein.';
$root->_btn_unfreeze      = 'Entsperren';

///////////////////////////////////////
// versionlist.inc.php
$root->_title_versionlist = 'Versions-Liste';

///////////////////////////////////////
// vote.inc.php
$root->_vote_plugin_choice = 'Selektion';
$root->_vote_plugin_votes  = 'Abstimmen';

///////////////////////////////////////
// yetlist.inc.php
$root->_title_yetlist = 'Liste an Seiten, die noch nicht angelegt wurden.';
$root->_err_notexist  = 'Alle Seiten wurden angelegt.';
