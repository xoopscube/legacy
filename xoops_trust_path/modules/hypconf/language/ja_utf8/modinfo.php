<?php
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'hypconf' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define($constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref.'_NAME', 'HypCommonの設定');

// A brief description of this module
define($constpref.'_DESC', 'HypCommonFunc 関連の設定');

define($constpref.'_MSG_SAVED' , '設定を保存しました。');
define($constpref.'_COUSTOM_BLOCK' , 'カスタムブロック');

// admin menus
define($constpref.'_ADMENU_CONTENTSADMIN' , '設定の確認');
define($constpref.'_ADMENU_MAIN_SWITCH' , 'メイン スイッチ');
define($constpref.'_ADMENU_K_TAI_CONF' , 'モバイル対応の設定');
define($constpref.'_ADMENU_MYBLOCKSADMIN' , 'アクセス権限設定');
define($constpref.'_ADMENU_XPWIKI_RENDER', 'xpWikiレンダラー設定');

// main_switch
define($constpref.'_USE_SET_QUERY_WORDS', '検索ワードを定数にセット');
define($constpref.'_USE_SET_QUERY_WORDS_DESC', '');
define($constpref.'_USE_WORDS_HIGHLIGHT', '検索ワードをハイライト表示');
define($constpref.'_USE_WORDS_HIGHLIGHT_DESC', '');
define($constpref.'_USE_PROXY_CHECK', '投稿時にプロキシチェックをする');
define($constpref.'_USE_PROXY_CHECK_DESC', '');
define($constpref.'_USE_DEPENDENCE_FILTER', '機種依存文字フィルター');
define($constpref.'_USE_DEPENDENCE_FILTER_DESC', '');
define($constpref.'_USE_POST_SPAM_FILTER', 'POST SPAM フィルター');
define($constpref.'_USE_POST_SPAM_FILTER_DESC', '');
define($constpref.'_POST_SPAM_TRAP_SET', 'ハニーポット(無効フィールドのBot罠)を自動で仕掛ける');
define($constpref.'_POST_SPAM_TRAP_SET_DESC', '');
define($constpref.'_USE_K_TAI_RENDER', 'モバイル対応機能を有効にする');
define($constpref.'_USE_K_TAI_RENDER_DESC', '');
define($constpref.'_USE_SMART_REDIRECT', 'スマートリダイレクトを有効にする');
define($constpref.'_USE_SMART_REDIRECT_DESC', '');

// k_tai_render
define($constpref.'_UA_REGEX', 'User agent');
define($constpref.'_UA_REGEX_DESC', 'モバイル対応機能で処理する User agent を PCRE(Perl互換)正規表現で記述。');
define($constpref.'_JQM_PROFILES', 'jQuery Mobile');
define($constpref.'_JQM_PROFILES_DESC', 'jQuery Mobile を適用するプロファイル名をカンマ区切りで記述。プロファイル名は携帯対応レンダラーで定義されていて、docomo, au, softbank, willcom, android, iphone, ipod, ipad, windows mobile などが使用できます。');
define($constpref.'_JQM_THEME', 'jqmテーマ');
define($constpref.'_JQM_THEME_DESC', 'ページ全体の jQuery Mobile のテーマ。標準では a, b, c, d, e が有効です。');
define($constpref.'_JQM_THEME_CONTENT', 'メイン部');
define($constpref.'_JQM_THEME_CONTENT_DESC', 'メインコンテンツに適用する jQuery Mobile のテーマ。');
define($constpref.'_JQM_THEME_BLOCK', 'ブロック部');
define($constpref.'_JQM_THEME_BLOCK_DESC', 'ブロックに適用する jQuery Mobile のテーマ。');
define($constpref.'_JQM_CSS', 'jqm 追加 CSS');
define($constpref.'_JQM_CSS_DESC', 'jQuery Mobile 用の追加の CSS を記述。<br />テーマ用 CSS の作成は <a href="http://jquerymobile.com/themeroller/" target="_blank">ThemeRoller | jQuery Mobile</a> や <a href="http://as001.productscape.com/themeroller.cfm" target="_blank">jQuery Mobile Themeroller</a> などを利用すると簡単です。');
define($constpref.'_JQM_REMOVE_FLASH' , 'Flash除去(jqm)');
define($constpref.'_JQM_REMOVE_FLASH_DESC' , 'jQuery Mobile 適用時に Flash を除去するプロファイル名をカンマ区切りで記述。プロファイル名は携帯対応レンダラーで定義されていて、docomo, au, softbank, willcom, android, iphone, ipod, ipad, windows mobile などが使用できます。');
define($constpref.'_JQM_RESOLVE_TABLE' , '入れ子テーブル展開(jqm)');
define($constpref.'_JQM_RESOLVE_TABLE_DESC' , 'jQuery Mobile 適用時に入れ子になっているテーブルを展開する。');
define($constpref.'_JQM_IMAGE_CONVERT' , '最大画像幅[px](jqm)');
define($constpref.'_JQM_IMAGE_CONVERT_DESC' , 'jQuery Mobile 適用時に画像を指定幅[px]サイズまで縮小する。「0」で無効になります。');
define($constpref.'_DISABLEDBLOCKIDS', '無効ブロック');
define($constpref.'_DISABLEDBLOCKIDS_DESC', 'モバイルアクセス時に選択されたブロックを無効にします。');
define($constpref.'_LIMITEDBLOCKIDS', '有効ブロック');
define($constpref.'_LIMITEDBLOCKIDS_DESC', 'モバイルアクセス時に選択されたブロックを有効にします。一つでも選択すると非選択のブロックはすべて無効になります。何も指定しないとフィルタリングはされません。');
define($constpref.'_SHOWBLOCKIDS', '展開ブロック');
define($constpref.'_SHOWBLOCKIDS_DESC', 'モバイルアクセス時に常に表示するブロック。<br />jQuery Mobile 使用時は折りたたみ表示が初期状態で展開されます。<br />従来の携帯表示では選択したブロックは表示され、非選択のブロックはそのブロックを表示するためのリンクになります。');

// xpwiki_render
define($constpref.'_XPWIKI_RENDER_NONE', '使用しない');
define($constpref.'_XPWIKI_RENDER_DIRNAME', 'xpWiki レンダラー');
define($constpref.'_XPWIKI_RENDER_DIRNAME_DESC', 'サイトワイド xpWiki レンダラー機能で使用する xpWiki を指定してください。<br />サイトワイドで xpWiki レンダラー機能を使用すると、ほとんどのモジュールで xpWiki(PukiWiki)の記法が使えるようになります。');
define($constpref.'_XPWIKI_RENDER_USE_WIKIHELPER', 'サイトワイド Wiki ヘルパー');
define($constpref.'_XPWIKI_RENDER_USE_WIKIHELPER_DESC', '「はい」を選択するとテキストエリアが機能拡張され Wiki ヘルパー及びリッチエディタをサイトワイドで使用できるようになります。');
define($constpref.'_XPWIKI_RENDER_NOTUSE_WIKIHELPER_MODULES', 'Wiki ヘルパー無効');
define($constpref.'_XPWIKI_RENDER_NOTUSE_WIKIHELPER_MODULES_DESC', 'サイトワイド Wiki ヘルパーを無効にするモジュールを選択して下さい。');
define($constpref.'_REQUERE_XCL', 'この設定は XOOPS Cube Legacy システムでのみ利用可能です。');
define($constpref.'_XCL_REQUERE_2_2_1', 'この機能は、XOOPS Cube Legacy 2.2.1 以降で有効になります。ただし、独自に "class/module.textsanitizer.php" を書き換えてこの機能を有効にしている場合は、このメッセージは無視して下さい。');
define($constpref.'_TEXTFILTER_ALREADY_EXISTS', 'preload ディレクトリに "SetupHyp_TextFilter.class.php" があります。それを削除するまでここでの設定は反映されません。');

}
