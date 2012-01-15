<?php
/*
 * Created on 2008/01/24 by nao-pon http://hypweb.net/
 * $Id: conf.lng.php,v 1.15 2012/01/14 11:56:35 nao-pon Exp $
 */
//
// German Translation Version 1.0 (11.03.2008)
// Translation English --> German: Octopus (hunter0815@googlemail.com)
// sicherlich steckt hier noch reichlich Qualitätspotential in den Übersetzungen ;-)

$msg = array(
	'title_form' => 'xpWiki Einstellungen',
	'title_done' => 'xpWiki Einstellungen geändert',
	'btn_submit' => 'Bestätigen der Einstellung',
	'msg_done' => 'Gespeichert in "$cache_file" durch die folgende Einstellung.',
	'title_description' => 'Erklärung der xpWiki Einstellungen',
	'msg_description' => '<p>In diesen bevorzugten Einstellungen werden lediglich ausgewählte Dinge der Datei "pukiwiki.ini.php" gecustomized.</p>'
	                   . '<p>In "$trust_ini_file" können viele weitere Einstellungen vorgenommen werden.</p>'
	                   . '<p>Bitte ziehe die Einstellungen der Dinge in "$html_ini_file" vor, falls Du Änderungen vornehmen möchtest, die in diesem Menü hier nicht möglich sind.</p>'
	                   . '<p># Die Inhalte dieses Menüs sind aufgrund der hohen Priorität hier aufgeführt. </p>',

	'Yes' => 'Ja',
	'No' => 'Nein',

	'PKWK_READONLY' => array(
		'caption'     => 'Nur zum Lesen?',
		'description' => 'Falls lediglich Lesen eingestellt ist, ist es nicht möglich zu administrieren und Absätze o.ä. zu ändern.',
	),

	'function_freeze' => array(
		'caption'     => 'Sperrfunktion aktivieren?',
		'description' => '',
	),

	'adminpass' => array(
		'caption'     => 'Administrator Passwort',
		'description' => 'Es ist auch möglich ein leeres Passwort einzugeben. Aber gib bitte die verschlüsselte Zeichenfolge ein, indem Du folgendes nutzt: "<a href="?cmd=md5" target="_blank">cmd=md5</a>".<br />'
		               . 'Unter "XOOPS" ist das Administratorkennwort  the problem is not in the administer password as cannot an attestation of everything as "{x-php-md5}" because of unnecessary if it logs it in as an administer. ',
	),

	'html_head_title' => array(
		'caption'     => '&lt;title&gt; format in &lt;head&gt;',
		'description' => 'Der Titel wird in &lt;title&gt; tag angezeigt und im &lt;head&gt; vom HTML gesetzt.<br />'
		               . 'Unterteilt wird hier in <b>$page_title</b>: Seiten Name, <b>$content_title</b>: Seiten Titel und <b>$module_title</b>: Modul Titel.',
	),

	'modifier' => array(
		'caption'     => 'Name des Administrators',
		'description' => '',
	),

	'modifierlink' => array(
		'caption'     => 'Link zur URL des Administrators',
		'description' => 'Hier kann eine Internet-Adresse des Administrators hinterlegt werden.',
	),

	'notify' => array(
		'caption'     => 'Mail-Benachrichtigung bei Seiten-Aktualisierungen?',
		'description' => 'Mail wird an den Administrator geschickt, wenn Seiten aktualisiert werden.',
	),

	'notify_diff_only' => array(
		'caption'     => 'Mail-Benachrichtigung nur bei Seiten-Änderungen?',
		'description' => 'Die Mail-Benachrichtigung beinhaltet nur die Änderungen.  Falls "Nein" gewählt ist, wird der komplette Text übermittelt.',
	),

	'defaultpage' => array(
		'caption'     => 'Standard-Seite',
		'description' => 'Das ist die Standard-Seite, die angezeigt wird, wenn keine Seite festgelegt ist.',
	),

	'page_case_insensitive' => array(
		'caption'     => 'Sind Seitennamen unabhängig von Groß- und Kleinschreibung?',
		'description' => 'Groß- und Kleinschreibung wird in Seitennamen nicht berücksichtigt.',
	),

	'SKIN_NAME' => array(
		'caption'     => 'Standard Skin Name',
		'description' => 'Der standardmäßige Skin-Name wird hier festgelegt.',
		'normalskin'  => 'Normale Häute',
		'tdiarytheme' => 't-Diary\'s Themen',
	),

	'skin_navigator_cmds' => array(
		'caption'     => 'Menus on Skin',
		'description' => 'The command name of the menu assumed to be able to display by the skin is input by comma (,) delimitation.<br />'
	                   . 'All menus are enabled to be displayed when "all" is input.<br />'
		               . '" add, atom, attaches, back, backup, copy, diff, edit, filelist, freeze, help, list, new, newsub, pginfo, print, rdf, recent, refer, related, reload, rename, rss, rss10, rss20, search, top, topage, trackback, unfreeze, upload " can be specified. However, it is controlled whether displayed by the skin.' ,
	),

	'skin_navigator_disabled' => array(
		'caption'     => 'Dsabled menus on Skin',
		'description' => 'The command name of the menu assumed not to be able to display by the skin is input by comma (,) delimitation. <br />'
	                   . 'The command that can be specified is the same as "Menus on Skin". ' ,
	),

	'SKIN_CHANGER' => array(
		'caption'     => 'Sind Änderungen am Skin erlaubt?',
		'description' => 'Der Benutzer kann einen Skin wählen wenn hier "Ja" gewählt wird.<br />'
		               . 'Außerdem , wird es möglich das tdiary-Plugin auf jeder benutzten Seite anzugeben.',
	),

	'referer' => array(
		'caption'     => 'Möchtest Du Referer Informationen?',
		'description' => '´Diese Funktion ermöglicht die Kontrolle für alle Seiten, wer diese besucht hat.',
	),

	'allow_pagecomment' => array(
		'caption'     => 'Sollen Kommentare möglich sein?',
		'description' => 'Die Kommentar-Integration kann hier eingestellt werden.<br />'
		               . 'Es ist wichtig die Kommentarfunktion unter den allgemeinen Einstellungen zu aktivieren um sie wirklich nutzen zu können.',
	),

	'use_root_image_manager' => array(
		'caption'     => 'Use Image manager',
		'description' => 'The standard image manager for a site is used and it enables it to insert a picture.',
	),

	'use_title_make_search' => array(
		'caption'     => 'Use Page title',
		'description' => 'The display of the title part of contents is changed from page name to page title.',
	),

	'nowikiname' => array(
		'caption'     => 'Ist der WikiName ungültig?',
		'description' => 'Eine automatische Link-Funktion für ungültige Wiki-Links.',
	),

	'relative_path_bracketname' => array(
		'caption'     => 'Relative path of BracketName',
		'description' => 'The method of displaying the relative path part when page name is specified by the relative path in the bracket name is set.',
		'remove'      => 'Remove',
		'full'        => 'Show real path',
		'as is'       => 'As is',
	),

	'pagename_num2str' => array(
		'caption'     => 'Ist der Seitenname konkret angezeigt?',
		'description' => 'Falls der letzte hierarchische Teil, die Nummer - (der Bindestrich) existiert, ersetzt dieser Teil den Seiten-Titel.',
	),

	'pagelink_topicpath' => array(
		'caption'     => 'Zeigen Sie pagelink mit Topic path?',
		'description' => 'Page links (#recent and #ls2, etc.) except an AutoLink and the BracketLink are displayed with the Topic path.',
	),

	'static_url' => array(
		'caption'     => 'Page URL-Stil',
		'description' => 'Außer "?[PAGE]", wählen Sie es bitte, und ist wie eine URL einer statischen Seite.<br />'
		               . 'Aber, Wahlen zufolge, ist es notwendig, dass Du folgende Einträge in der ".htaccess" vornimmst.<br />'
		               . '<dl><dt>[ID].html</dt><dd><code>RewriteEngine on<br />RewriteRule ^([0-9]+)\.html$ index.php?pgid=$1 [qsappend,L]</code></dd></dl>'
		               . '<dl><dt>{$root->path_info_script}/[PAGE]</dt><dd><code>Options +MultiViews<br />&lt;FilesMatch "^{$root->path_info_script}$"&gt;<br />ForceType application/x-httpd-php<br />&lt;/FilesMatch&gt;</code></dd></dl>',
	),

	'url_encode_utf8' => array(
		'caption'     => 'Benutzen Sie "UTF-8" von URL?',
		'description' => '"[PAGE]" Teil obenerwähnten "Page URL-Stil" wird von "UTF-8" verschlüsselt.<br />'
		               . 'Aber, wenn die Charakterverschlüsselung von xpWiki ist UTF-8, es wird immer "UTF-8."',
	),

	'link_target' => array(
		'caption'     => 'Ext.Link Attribut "target"',
		'description' => '"target" Attribut vom externen Link.',
	),

	'class_extlink' => array(
		'caption'     => 'Ext.Link Attribut "class"',
		'description' => '"class" Attribut vom externen Link.',
	),

	'nofollow_extlink' => array(
		'caption'     => '"nofollow" im Ext.Link einstellen?',
		'description' => 'Das "nofollow" Attribute ist auf einen externen Link bezogen.',
	),

	'LC_CTYPE' => array(
		'caption'     => 'lokale (LC_CTYPE)',
		'description' => 'The locale for character classification and conversion is set. Please set it according to the environment when expecting it when processing it by the regular expression such as auto links doesn\'t result. ',
	),

	'autolink' => array(
		'caption'     => 'AutoLinks Bytes des Seiten-Namens',
		'description' => 'Ein Autolink ist eine Funktion, die automatisch auf den existierenden Seiten-Namen verlinkt.<br />'
		               . 'Die Nummer des Seiten Bytes wird mit der Eingabe wirksam. (Es ist ungültig mit einer 0.)<br />'
		               . 'Bitte nutze keine Buchstaben für die Byte-Nummer.',
		'extention'   => 'Bytes',
	),

	'autolink_omissible_upper' => array(
		'caption'     => 'AutoLink, berücksichtigt nicht die höherliegende Hierarchie',
		'description' => 'Es wird automatisch verlinkt, selbst wenn die höherliegende Hierarchie ausgelassen wird.<br />'
		               . 'Es wird automatisch verlinkt mit "/hoge/fuga" wenn "fuga" geschrieben wird auf der Seite "/hoge/hoge". <br />'
		               . 'Es ist genauso die Byte Nummer Angabe als auch ein AutoLink. (Gib eine Bytes-Nummer an, die fuga entspricht. )',
		'extention'   => 'Bytes',
	),

	'autoalias' => array(
		'caption'     => 'AutoAlias\' Bytes vom Wort',
		'description' => 'Diese Funktion verlinkt automatisch zu einer festgelegten "URL, Seite oder InterWiki" mit einem "vorgegebenem Wort".<br />'
		               . 'Es ist genauso die Byte Nummer Angabe als auch ein AutoLink. (Es gibt es byteweise aus für das ersetzte Wort. Es ist ungültig mit einer 0.)<br />'
		               . 'Konfigurations-Seite: <a href="?'.rawurlencode($this->root->aliaspage).'" target="_blank">'.$this->root->aliaspage.'</a>',
		'extention'   => 'Bytes',
	),

	'autoalias_max_words' => array(
		'caption'     => 'AutoAlias\' maximale pairs',
		'description' => 'Anzahl maximaler aufgelisteter Wörterbucheinträge für AutoAliase.',
		'extention'   => 'pairs',
	),

	'plugin_follow_editauth' => array(
		'caption'     => 'Soll das Plugin die "Änderungsberechtigung" übernehmen?',
		'description' => 'Die Bearbeitung durch das Plugin ist nicht möglich, wenn keine Seiten-Änderungsberechtigung vorliegt.',
	),

	'plugin_follow_freeze' => array(
		'caption'     => 'Soll das Plugin Seiten-Sperren berücksichtigen?',
		'description' => 'Die Bearbeitung durch das Plugin ist nicht möglich, wenn die Seite gesperrt ist.',
	),

	'line_break' => array(
		'caption'     => 'Automatischen Zeilenumbruch aktivieren?',
		'description' => 'Zeilenumbruch wird konvertiert zu "&lt;br /&gt;".',
	),

	'fixed_heading_anchor_edit' => array(
		'caption'     => 'Abschnittsweise Editierung nutzen?',
		'description' => '',
	),

	'paraedit_partarea' => array(
		'caption'     => 'Bereich des Kapitels editieren',
		'description' => 'Bereich des Kapitels ist gesetzt.<br />'
		               . 'Bereich des Kapitels beginnt bei der Überschrift und startet mit * des Wiki-Formats.',
		'compat'      => 'Weiter zum nächsten',
		'level'       => 'Weiter zum gleichen oder höheren Level',
	),

	'contents_auto_insertion' => array(
		'caption'     => 'Auto TOC',
		'description' => 'Number of heading that inserts TOC("#contents") automatically. ( 0: Disabled )',
	),

	'amazon_AssociateTag' => array(
		'caption'     => 'Amazon AssociateTag',
		'description' => 'Associate Tag(Tracking ID)<br />'
		               . 'If empty then uses "trust/class/hyp_common/hsamazon/hyp_simple_amazon.ini" setting.',
	),

	'amazon_AccessKeyId' => array(
		'caption'     => 'Amazon AccessKeyId',
		'description' => 'AccessKey ID (Character string of 20 characters)<br />'
		               . 'If empty then uses "trust/class/hyp_common/hsamazon/hyp_simple_amazon.ini" setting.',
	),

	'amazon_SecretAccessKey' => array(
		'caption'     => 'Amazon SecretAccessKey',
		'description' => 'Secret AccessKey (Sequence of 40 characters)<br />'
		               . 'If empty then uses "trust/class/hyp_common/hsamazon/hyp_simple_amazon.ini" setting.',
	),

	'amazon_UseUserPref' => array(
		'caption'     => 'User\'s Amazon ID',
		'description' => 'Enable Associate ID of User preference.',
	),

	'bitly_clickable' => array(
		'caption'     => 'Clickable URL shorten',
		'description' => 'Automatic links of URL are shortened with <a href="http://bit.ly/" target="_blank">bitly</a>. '
		               . '"It is necessary to set "Bitly_login" and "Bitly_apiKey".'
	),

	'twitter_consumer_key' => array(
		'caption'     => 'Twitter Consumer key',
		'description' => '"Customer key" obtained by <a href="https://twitter.com/apps" target="_blank">Applications Using Twitter</a>.'
	),

	'twitter_consumer_secret' => array(
		'caption'     => 'Twitter Consumer secret',
		'description' => '"Consumer secret key" obtained by <a href="https://twitter.com/apps" target="_blank">Applications Using Twitter</a>.'
	),

	'fckeditor_path' => array(
		'caption'     => 'Path of "FCKeditor"',
		'description' => 'Please input continuation from <span style="font-weight:bold;">' . $this->cont['ROOT_PATH'] . '</span><br />'
		               . 'Please set the directory name with fckeditor.js. It is necessary for FCKeditor since Version 2.6.<br />'
		               . 'Please set empty when you do not use a rich editor by FCKeditor.',
	),

	'pagecache_min' => array(
		'caption'     => 'Seiten Cache Ablauf Zeit',
		'description' => 'Während der Seiten Cache Ablauf Zeit (Einheit: Minute) wird, wenn HTML die Seite übersetzt, dies in den Cache gegeben und führt zu einer Beschleunigung.<br />'
		               . 'Aber nur wenn ein Gast-Account aufgerufen wird, wird gecached. Es wird empfohlen, wenn sehr viele Seitenaufrufe stattfinden.',
		'extention'   => 'Min.',
	),

	'pre_width' => array(
		'caption'     => 'CSS:Breit für &lt;pre&gt;',
		'description' => 'Der Wert Breite im CSS ist vorgegeben für &lt;pre&gt; Kennzeichnung ist vorgeschrieben.',
	),

	'pre_width_ie' => array(
		'caption'     => 'CSS:Breite für &lt;pre&gt;(nur IE)',
		'description' => 'Dieser Wert ist nur für den Internet Explorer. Falls die Anzeige nicht korrekt aussieht, da das Theme von XOOPS für &lt;Table&gt; entworfen wurde, gib einen festen Wert wie "700px" ein.',
	),

	'moblog_pop_mail' => array(
		'caption'     => 'Moblog destination mail address',
		'description' => 'When a special address is given to each user by using Gmail, it is set, "ACCOUNT NAME+*@gmail.com". (A random character string is inserted in "*")',
	),

	'moblog_pop_host' => array(
		'caption'     => 'POP3 server name used by moblog',
		'description' => 'It is set for Gmail as "ssl://pop.gmail.com".<br />However, when OpenSSL is not built into PHP of the server, "ssl://" cannot be used.',
	),

	'moblog_pop_port' => array(
		'caption'     => 'POP3 port number used by moblog',
		'description' => '"110" usually. It is set for Gmail as "995".',
	),

	'moblog_pop_user' => array(
		'caption'     => 'POP3 log in ID used by moblog',
		'description' => 'For Gmail to set it to the recent mode, it is set, "Recent:ACCOUNT NAME @gmail.com".',
	),

	'moblog_pop_pass' => array(
		'caption'     => 'POP3 log in password used by moblog',
		'description' => '',
	),

	'use_moblog_user_pref' => array(
		'caption'     => 'The setting of the moblog is permitted by the user preference.',
		'description' => '',
	),

	'moblog_page_recomend' => array(
		'caption'     => 'Setting of page name of user setting hint',
		'description' => 'Explanation to contribution page name in user setting (The setting example etc. are filled in).',
	),

	'update_ping' => array(
		'caption'     => 'Schicken Update Ping?',
		'description' => '',
	),

	'update_ping_servers' => array(
		'caption'     => 'Update Ping Server',
		'description' => 'Schreiben Sie einem XML-RPC Ping Server, die mit "http" eine Linie beginnen.<br />Wenn Sie "extendedPing" schicken wollen, befestigen Sie [Space] + "E" nach dem URL.',
	),

	'pagereading_enable' => array(
		'caption'     => 'Classify by page name reading?',
		'description' => 'The setting concerning page name reading is a setting only for a Japanese environment.',
	),

	'pagereading_kanji2kana_converter' => array(
		'caption'     => 'Page name reading converter',
		'description' => '',
	),

	'pagereading_kanji2kana_encoding' => array(
		'caption'     => 'Converter\'s encoding',
		'description' => '',
	),

	'pagereading_chasen_path' => array(
		'caption'     => 'ChaSen path',
		'description' => '',
	),

	'pagereading_kakasi_path' => array(
		'caption'     => 'KAKASI path',
		'description' => '',
	),

	'pagereading_config_dict' => array(
		'caption'     => 'Reading dictionary page',
		'description' => 'It is used for "None" the method of acquiring page name reading.',
	),

);
?>