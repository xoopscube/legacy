<?php
/*
 * Created on 2008/01/24 by nao-pon http://hypweb.net/
 * $Id: conf.lng.php,v 1.3 2012/01/14 11:56:35 nao-pon Exp $
 */

$msg = array(
	'title_form' => 'Preferências do xpWiki',
	'title_done' => 'Atualizadas as preferências do xpWiki',
	'btn_submit' => 'Apply this setting',
	'msg_done' => 'Salvo em "$cache_file" com a seguinte configuração.',
	'title_description' => 'Explicação das preferências do do xpWiki',
	'msg_description' => '<p>Nesta configuração das preferências apenas um item típico pode ser configurado pela configuração do item do "pukiwiki.ini.php".</p>'
	                   . '<p>In "$trust_ini_file", além destes, também tem vários itens configurados. </p>'
	                   . '<p>Please pull out and set the item of the correspondence to "$html_ini_file" when you want to change the item for you not to be found on this set screen.</p>'
	                   . '<p># The content of this set screen set is applied in top priority. </p>',

	'Yes' => 'Yes',
	'No' => 'No',

	'PKWK_READONLY' => array(
		'caption'     => 'Somente para leitura?',
		'description' => 'When it does only for reading, it is not possible to administer and every inclusion edit it.',
	),

	'function_freeze' => array(
		'caption'     => 'Is freeze function effective?',
		'description' => '',
	),

	'adminpass' => array(
		'caption'     => 'Senha do administrador',
		'description' => 'It is possible to specify it even by the clear text. However, please input the encrypted character string by using "<a href="?cmd=md5" target="_blank">cmd=md5</a>".<br />'
		               . 'Under "XOOPS", the problem is not in the administer password as cannot an attestation of everything as "{x-php-md5}" because of unnecessary if it logs it in as an administer. ',
	),

	'html_head_title' => array(
		'caption'     => '&lt;title&gt; format in &lt;head&gt;',
		'description' => 'The content displayed in the &lt;title&gt; tag in &lt;head&gt; of HTML is set.<br />'
		               . 'It is substituted by <b>$page_title</b>: Page name and <b>$content_title</b>: page title and <b>$module_title</b>: module title.',
	),

	'modifier' => array(
		'caption'     => 'Nome do Administrador',
		'description' => '',
	),

	'modifierlink' => array(
		'caption'     => 'Administer\'s site URL',
		'description' => '',
	),

	'notify' => array(
		'caption'     => 'Mail notified on page updated?',
		'description' => 'Mail is notified to the administer when page updated.',
	),

	'notify_diff_only' => array(
		'caption'     => 'Mail notification only diff?',
		'description' => 'The mail notification when the page is updated transmits only the change difference. When "No" is selected, the full text is transmitted.',
	),

	'defaultpage' => array(
		'caption'     => 'Default page',
		'description' => 'It is a top page, displayed when the page is not specified.',
	),

	'page_case_insensitive' => array(
		'caption'     => 'Is case insensitive of the page name?',
		'description' => 'Neither lower case nor upper case are distinguished of the page name.',
	),

	'SKIN_NAME' => array(
		'caption'     => 'Default Skin name',
		'description' => 'The skin name of default is specified.',
		'normalskin'  => 'Normal skins',
		'tdiarytheme' => 't-Diary\'s themes',
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
		'caption'     => 'Is the skin\'s change permitted?',
		'description' => 'The user comes to be able to select skin by selecting "Yes".<br />'
		               . 'Moreover, specifying the tdiary plugin etc. on each use page becomes possible.',
	),

	'referer' => array(
		'caption'     => 'Do get referer information?',
		'description' => 'It is a function to total where those who inspected it visited page each page.',
	),

	'allow_pagecomment' => array(
		'caption'     => 'Is page comment effective?',
		'description' => 'The comment integration of d3forum modules is provided and the comment function of
each use page can be provided.<br />'
		               . 'It is necessary to set the comment integration by a general setting to actually use it.',
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
		'caption'     => 'Is WikiName invalid?',
		'description' => 'The automatic link function to WikiName is invalidated.',
	),

	'relative_path_bracketname' => array(
		'caption'     => 'Relative path of BracketName',
		'description' => 'The method of displaying the relative path part when page name is specified by the relative path in the bracket name is set.',
		'remove'      => 'Remove',
		'full'        => 'Show real path',
		'as is'       => 'As is',
	),

	'pagename_num2str' => array(
		'caption'     => 'Is page name concretely displayed?',
		'description' => 'When the last hierarchical part, the number - (the hyphen) it consists, the part of that is substituted in page title.',
	),

	'pagelink_topicpath' => array(
		'caption'     => 'Show pagelink with topic path?',
		'description' => 'Page links (#recent and #ls2, etc.) except an AutoLink and the BracketLink are displayed with the Topic path.',
	),

	'static_url' => array(
		'caption'     => 'Page URL style',
		'description' => 'Select it excluding "?[PAGE]", and it behaves like URL on a static page.<br />'
		               . 'However, according to choices. It is necessary to do the following descriptions with ".htaccess" effectively.<br />'
		               . '<dl><dt>[ID].html</dt><dd><code>RewriteEngine on<br />RewriteRule ^([0-9]+)\.html$ index.php?pgid=$1 [qsappend,L]</code></dd></dl>'
		               . '<dl><dt>{$root->path_info_script}/[PAGE]</dt><dd><code>Options +MultiViews<br />&lt;FilesMatch "^{$root->path_info_script}$"&gt;<br />ForceType application/x-httpd-php<br />&lt;/FilesMatch&gt;</code></dd></dl>',
	),

	'url_encode_utf8' => array(
		'caption'     => 'Use "UTF-8" of URL?',
		'description' => '"[PAGE]" part of above-mentioned "Page URL style" is encoded by "UTF-8".<br />'
		               . 'However, when the character encoding of xpWiki is UTF-8, it always becomes "UTF-8".',
	),

	'link_target' => array(
		'caption'     => 'Ext.Link Attribute "target"',
		'description' => '"target" attribute of external link.',
	),

	'class_extlink' => array(
		'caption'     => 'Ext.Link Attribute "class"',
		'description' => '"class" attribute of external link.',
	),

	'nofollow_extlink' => array(
		'caption'     => 'Set "nofollow" in Ext.Link?',
		'description' => 'The "nofollow" attribute is applied to an external link.',
	),

	'LC_CTYPE' => array(
		'caption'     => 'Locale (LC_CTYPE)',
		'description' => 'The locale for character classification and conversion is set. Please set it according to the environment when expecting it when processing it by the regular expression such as auto links doesn\'t result. ',
	),

	'autolink' => array(
		'caption'     => 'AutoLink\'s bytes of page name',
		'description' => 'An autolink is a function that links automatically with page existing name.<br />'
		               . 'The number of page bytes that becomes effective is input. (It is invalid by 0.)<br />'
		               . 'Please note no number of characters it and becoming byte number specification.',
		'extention'   => 'Bytes',
	),

	'autolink_omissible_upper' => array(
		'caption'     => 'AutoLink, omits above hierarchy',
		'description' => 'It is the settings auto linked even if the above hierarchy is omitted. An autolink should be effective. <br />'
		               . 'It auto links with "/hoge/fuga" by writing "fuga" on the page "/hoge/hoge". <br />'
		               . 'It is byte number specification as well as an autolink. (Specify it by the number of bytes that corresponds to fuga. )',
		'extention'   => 'Bytes',
	),

	'autoalias' => array(
		'caption'     => 'AutoAlias\'s bytes of word',
		'description' => 'It is a function to put the link to specified "URI, page or InterWiki" on "Specified word" automatically.<br />'
		               . 'It becomes byte number specification as well as an autolink. (It specifies it by bytes for the substituted word. It is invalid by 0.)<br />'
		               . 'Config page: <a href="?'.rawurlencode($this->root->aliaspage).'" target="_blank">'.$this->root->aliaspage.'</a>',
		'extention'   => 'Bytes',
	),

	'autoalias_max_words' => array(
		'caption'     => 'AutoAlias\'s max pairs',
		'description' => 'Number of maximum dictionary item registration of autoalias.',
		'extention'   => 'pairs',
	),

	'plugin_follow_editauth' => array(
		'caption'     => 'Plugin follow to the edit auth?',
		'description' => 'The contribution by the plugin is not permitted when there is no page edit authority.',
	),

	'plugin_follow_freeze' => array(
		'caption'     => 'Plugin follow to page freezing?',
		'description' => 'The contribution by the plugin is not permitted when the page is freezed.',
	),

	'line_break' => array(
		'caption'     => 'Habilitar quebra de linha automática?',
		'description' => 'Line break is converted to "&lt;br /&gt;".',
	),

	'fixed_heading_anchor_edit' => array(
		'caption'     => 'Usar edição unitária de capítulo?',
		'description' => '',
	),

	'paraedit_partarea' => array(
		'caption'     => 'Range of chapter unit editing',
		'description' => 'The range of the chapter unit edit is set.<br />'
		               . 'The range in the chapter is begun by the head line that starts by * of the Wiki format.',
		'compat'      => 'Up to next',
		'level'       => 'Up to equality or higher level',
	),

	'contents_auto_insertion' => array(
		'caption'     => 'Auto TOC',
		'description' => 'Number of heading that inserts TOC("#contents") automatically. ( 0: Disabled )',
	),

	'fckeditor_path' => array(
		'caption'     => 'Path of "FCKeditor"',
		'description' => 'Please input continuation from <span style="font-weight:bold;">' . $this->cont['ROOT_PATH'] . '</span><br />'
		               . 'Please set the directory name with fckeditor.js. It is necessary for FCKeditor since Version 2.6.<br />'
		               . 'Please set empty when you do not use a rich editor by FCKeditor.',
	),

	'pagecache_min' => array(
		'caption'     => 'Page cache expiration time',
		'description' => 'The expiration time (Unit: Minute) when HTML that does the page in rendering is cached and it speeds it up is set.<br />'
		               . 'However, only when the guest account is accessed, it is cached. I will recommend being made to effective for the site where a lot of page views exist.',
		'extention'   => 'min',
	),

	'pre_width' => array(
		'caption'     => 'CSS:width of &lt;pre&gt;',
		'description' => 'The width value of CSS specified for &lt;pre&gt; tag is specified.',
	),

	'pre_width_ie' => array(
		'caption'     => 'CSS:width of &lt;pre&gt;(IE Only)',
		'description' => 'Here is a value only for IE of a browser. When the display falls into disorder because the theme of XOOPS is composed of &lt;Table&gt;, specify a fixed value such as "700px".',
	),

	'update_ping' => array(
		'caption'     => 'Send update pings?',
		'description' => '',
	),

	'update_ping_servers' => array(
		'caption'     => 'Update ping servers',
		'description' => 'Write a XML-RPC updates ping servers starting with "http" a line.<br />If you want to send "extendedPing", append [Space] + "E" after the URL.',
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
