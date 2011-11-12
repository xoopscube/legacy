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
define($constpref.'_JQUERY_PROFILES', 'jQuery Mobile');
define($constpref.'_JQUERY_PROFILES_DESC', 'jQuery Mobile を適用するプロファイル名をカンマ区切りで記述。プロファイル名は携帯対応レンダラーで定義されていて、docomo, au, softbank, willcom, android, iphone, ipod, ipad, windows mobile などが使用できます。');
define($constpref.'_JQUERY_THEME', 'jqmテーマ');
define($constpref.'_JQUERY_THEME_DESC', 'ページ全体の jQuery Mobile のテーマ。標準では a, b, c, d, e が有効です。');
define($constpref.'_JQUERY_THEME_CONTENT', 'メイン部');
define($constpref.'_JQUERY_THEME_CONTENT_DESC', 'メインコンテンツに適用する jQuery Mobile のテーマ。');
define($constpref.'_JQUERY_THEME_BLOCK', 'ブロック部');
define($constpref.'_JQUERY_THEME_BLOCK_DESC', 'ブロックに適用する jQuery Mobile のテーマ。');
define($constpref.'_DISABLEDBLOCKIDS', '無効ブロック');
define($constpref.'_DISABLEDBLOCKIDS_DESC', 'モバイルアクセス時に選択されたブロックを無効にします。');
define($constpref.'_LIMITEDBLOCKIDS', '有効ブロック');
define($constpref.'_LIMITEDBLOCKIDS_DESC', 'モバイルアクセス時に選択されたブロックを有効にします。一つでも選択すると非選択のブロックはすべて無効になります。何も指定しないとフィルタリングはされません。');
define($constpref.'_SHOWBLOCKIDS', '展開ブロック');
define($constpref.'_SHOWBLOCKIDS_DESC', 'モバイルアクセス時に常に表示するブロック。<br />jQuery Mobile 使用時は折りたたみ表示が初期状態で展開されます。<br />従来の携帯表示では選択したブロックは表示され、非選択のブロックはそのブロックを表示するためのリンクになります。');

}
