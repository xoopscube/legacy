<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: default.ini.php,v 1.13 2011/06/01 06:27:51 nao-pon Exp $
// Copyright (C)
//   2003-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki setting file (user agent:default)

/////////////////////////////////////////////////
// Skin file

if (!empty($const['TDIARY_THEME'])) {
	$const['SKIN_FILE'] = $const['DATA_HOME'] . $const['TDIARY_DIR'] . 'tdiary.skin.php';
} else {
	$const['SKIN_FILE'] = $const['DATA_HOME'] . $const['SKIN_DIR'] . 'pukiwiki.skin.php';
}

/////////////////////////////////////////////////
// Ajax edit
$root->use_ajax_edit = 1;

/////////////////////////////////////////////////
// 雛形とするページの読み込みを可能にする
$root->load_template_func = 0;

/////////////////////////////////////////////////
// 編集フォームの詳細オプションを折りたたむ
$root->hide_extra_option_editform = 1;

/////////////////////////////////////////////////
// 編集フォームに添付ファイルリストを表示する
$root->show_attachlist_editform = 1;

/////////////////////////////////////////////////
// 検索文字列を色分けする
$root->search_word_color = 1;

/////////////////////////////////////////////////
// 一覧ページに頭文字インデックスをつける
$root->list_index = 1;

/////////////////////////////////////////////////
// リスト構造の左マージン
$root->_ul_left_margin = 0;   // リストと画面左端との間隔(px)
$root->_ul_margin = 16;       // リストの階層間の間隔(px)
$root->_ol_left_margin = 0;   // リストと画面左端との間隔(px)
$root->_ol_margin = 16;       // リストの階層間の間隔(px)
$root->_dl_left_margin = 0;   // リストと画面左端との間隔(px)
$root->_dl_margin = 16;        // リストの階層間の間隔(px)
//$root->_list_pad_str = ' class="list%d" style="padding-left:%dpx;margin-left:%dpx"';
$root->_list_pad_str = ' class="list%d"';

/////////////////////////////////////////////////
// テキストエリアのカラム数
$root->cols = 80;

/////////////////////////////////////////////////
// テキストエリアの行数
$root->rows = 20;

/////////////////////////////////////////////////
// 大・小見出しから目次へ戻るリンクの文字
$root->top = $root->_msg_content_back_to_top;

/////////////////////////////////////////////////
// 添付ファイルの一覧を常に表示する (負担がかかります)
$root->attach_link = 1;

/////////////////////////////////////////////////
// 関連するページのリンク一覧を常に表示する(負担がかかります)
$root->related_link = 1;
// 最大表示件数
$root->related_show_max = 100;

// リンク一覧の区切り文字
$root->related_str = "\n ";

// (#relatedプラグインが表示する) リンク一覧の区切り文字
$root->rule_related_str = "</li>\n<li>";

/////////////////////////////////////////////////
// 水平線のタグ
$root->hr = '<hr class="full_hr" />';

// ページ別名の入力欄
$root->alias_form = 'textarea|class="norich" style="width:40em;height:2.5em;" cols="40" rows="2" rel="nowikihelper"';

/////////////////////////////////////////////////
// 脚注機能関連

// 脚注のアンカーに埋め込む本文の最大長
$const['PKWK_FOOTNOTE_TITLE_MAX'] = 40; // Characters

// 脚注のアンカーを相対パスで表示する (0 = 絶対パス)
//  * 相対パスの場合、以前のバージョンのOperaで問題になることがあります
//  * 絶対パスの場合、calendar_viewerなどで問題になることがあります
// (詳しくは: BugTrack/698)
$const['PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR'] = 1;

// 文末の脚注の直前に表示するタグ
$root->note_hr = '<hr class="note_hr" />';

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$root->show_passage = 1;

/////////////////////////////////////////////////
// リンク表示をコンパクトにする
// * ページに対するハイパーリンクからタイトルを外す
// * Dangling linkのCSSを外す
$root->link_compact = 0;

/////////////////////////////////////////////////
// Attributes "alt"" & "title" of <img> by plugin "ref"
// Can set "title", "name", "size", "exif" join by ","
// Please set "$this->cont['PLUGIN_REF_GET_EXIF'] = TRUE;" in "plugin_ref_init()" if you use "exif".
$root->ref_img_alt = 'title,name';
$root->ref_img_title = 'title,name,size';

/////////////////////////////////////////////////
// フェイスマークを使用する
$root->usefacemark = 1;
// 追加(XOOPS)のフェイスマークを使用する
$root->use_extra_facemark = 1;

/////////////////////////////////////////////////
// メニューバーを表示する
$root->show_menu_bar = 0;

/////////////////////////////////////////////////
// 長い英数文字列を表示域に合わせて改行する設定
// Setting to which long character string is set
// to display region and it changes line.

// Insert to after '/' of pagename.
$root->hierarchy_insert = '&#8203;';

// Long word break limit
$root->word_break_limit = 0;

// WordBeark ('&#8203;' or '<wbr>' or '' etc.)
$root->word_breaker = '&#8203;';

/////////////////////////////////////////////////
// ユーザ定義ルール
//
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。
//
/////////////////////////////////////////////////
// ユーザ定義ルール(コンバート時に置換)
$root->line_rules = array(
	'COLOR\(([^\(\)]*)\){([^}]*)}'	=> '<span style="color:$1">$2</span>',
	'SIZE\(([^\(\)]*)\){([^}]*)}'	=> '<span style="font-size:$1px">$2</span>',
	'COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)'	=> '<span style="color:$1">$2</span>',
	'SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)'	=> '<span class="size$1">$2</span>',
	'%%%(?!%)((?:(?!%%%).)*)%%%'	=> '<ins>$1</ins>',
	'%%(?!%)((?:(?!%%).)*)%%'	=> '<del>$1</del>',
	"'''(?!')((?:(?!''').)*)'''"	=> '<em>$1</em>',
	"''(?!')((?:(?!'').)*)''"	=> '<strong>$1</strong>',
);

/////////////////////////////////////////////////
// フェイスマーク定義ルール(コンバート時に置換)

// $usefacemark = 1ならフェイスマークが置換されます
// 文章内にXDなどが入った場合にfacemarkに置換されてしまうので
// 必要のない方は $usefacemarkを0にしてください。

$root->facemark_rules = array(
	// Face marks
	'\s(\:\))'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/smile.png" />',
	'\s(\:D)'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/bigsmile.png" />',
	'\s(\:p)'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/huh.png" />',
	'\s(\:d)'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/huh.png" />',
	'\s(XD)'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/oh.png" />',
	'\s(X\()'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/oh.png" />',
	'\s(;\))'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/wink.png" />',
	'\s(;\()'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/sad.png" />',
	'\s(\:\()'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/sad.png" />',
	'&amp;(smile);'	=> ' <img alt="[$1]" src="' . $const['IMAGE_DIR'] . 'face/smile.png" />',
	'&amp;(bigsmile);'=>' <img alt="[$1]" src="' . $const['IMAGE_DIR'] . 'face/bigsmile.png" />',
	'&amp;(huh);'	=> ' <img alt="[$1]" src="' . $const['IMAGE_DIR'] . 'face/huh.png" />',
	'&amp;(oh);'	=> ' <img alt="[$1]" src="' . $const['IMAGE_DIR'] . 'face/oh.png" />',
	'&amp;(wink);'	=> ' <img alt="[$1]" src="' . $const['IMAGE_DIR'] . 'face/wink.png" />',
	'&amp;(sad);'	=> ' <img alt="[$1]" src="' . $const['IMAGE_DIR'] . 'face/sad.png" />',
	'&amp;(heart);'	=> ' <img alt="[$1]" src="' . $const['IMAGE_DIR'] . 'face/heart.png" />',
	'&amp;(worried);'=>' <img alt="[$1]" src="' . $const['IMAGE_DIR'] . 'face/worried.png" />',

	// Face marks, Japanese style
	'\s(\(\^\^\))'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/smile.png" />',
	'\s(\(\^-\^)'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/bigsmile.png" />',
	'\s(\(\.\.;)'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/oh.png" />',
	'\s(\(\^_-\))'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/wink.png" />',
	'\s(\(--;)'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/sad.png" />',
	'\s(\(\^\^;\))'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/worried.png" />',
	'\s(\(\^\^;)'	=> ' <img alt="$1" src="' . $const['IMAGE_DIR'] . 'face/worried.png" />',

	// Push buttons, 0-9 and sharp (Compatibility with cell phones)
	'&amp;(pb1);'	=> '[1]',
	'&amp;(pb2);'	=> '[2]',
	'&amp;(pb3);'	=> '[3]',
	'&amp;(pb4);'	=> '[4]',
	'&amp;(pb5);'	=> '[5]',
	'&amp;(pb6);'	=> '[6]',
	'&amp;(pb7);'	=> '[7]',
	'&amp;(pb8);'	=> '[8]',
	'&amp;(pb9);'	=> '[9]',
	'&amp;(pb0);'	=> '[0]',
	'&amp;(pb#);'	=> '[#]',

	// Other icons (Compatibility with cell phones)
	'&amp;(zzz);'	=> '[zzz]',
	'&amp;(man);'	=> '[man]',
	'&amp;(clock);'	=> '[clock]',
	'&amp;(mail);'	=> '[mail]',
	'&amp;(mailto);'=> '[mailto]',
	'&amp;(phone);'	=> '[phone]',
	'&amp;(phoneto);'=>'[phoneto]',
	'&amp;(faxto);'	=> '[faxto]',
);

$root->wikihelper_facemarks = array(
	':)'	=> $const['IMAGE_DIR'] . 'face/smile.png',
	':D'	=> $const['IMAGE_DIR'] . 'face/bigsmile.png',
	':p'	=> $const['IMAGE_DIR'] . 'face/huh.png',
	'XD'	=> $const['IMAGE_DIR'] . 'face/oh.png',
	';)'	=> $const['IMAGE_DIR'] . 'face/wink.png',
	';('	=> $const['IMAGE_DIR'] . 'face/sad.png',
	'&worried;'=> $const['IMAGE_DIR'] . 'face/worried.png',
	'&heart;'	=> $const['IMAGE_DIR'] . 'face/heart.png',
);
?>