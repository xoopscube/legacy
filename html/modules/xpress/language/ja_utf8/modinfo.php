<?php
if( ! defined( 'XP2_MODINFO_LANG_INCLUDED' ) ) {
	define( 'XP2_MODINFO_LANG_INCLUDED' , 1 ) ;

	// The name of this module admin menu
	define("_MI_XP2_MENU_SYS_INFO","システム情報");
	define("_MI_XP2_MENU_BLOCK_ADMIN","ブロック権限");
	define("_MI_XP2_MENU_BLOCK_CHECK","ブロックチェック");
	define("_MI_XP2_MENU_WP_ADMIN","WordPress管理");
	define("_MI_XP2_MOD_ADMIN","モジュール管理");

	// The name of this module
	define("_MI_XP2_NAME","ブログ");

	// A brief description of this module
	define("_MI_XP2_DESC","WordPressMEをXOOPSモジュール化したものです。");

	// Sub menu titles
	define("_MI_XP2_MENU_POST_NEW","新規投稿");
	define("_MI_XP2_MENU_EDIT","編集");
	define("_MI_XP2_MENU_ADMIN","WordPress管理");
	define("_MI_XP2_MENU_XPRESS","XPressME設定");
	define("_MI_XP2_MENU_TO_MODULE","モジュールへ");
	define("_MI_XP2_TO_UPDATE","アップデート");

	// Module Config
	define("_MI_LIBXML_PATCH","ブロックでlibxml2 バグに対するパッチを強制適応する");
	define("_MI_LIBXML_PATCH_DESC","libxml2 Ver 2.70-2.72には'<'と'>'が取り除かれるバグがあります。
XPressMEはlibxml2のバージョンを自動的に取得し、必要であればパッチが適応されます。
XPressMEがlibxml2のバージョンを取得できない場合、このオプションで強制的にパッチを適応させることができます。");
	
	define("_MI_MEMORY_LIMIT","モジュールに最低限必要なメモリ(MB)");
	define("_MI_MEMORY_LIMIT_DESC","php.iniのmemory_limit値がこの値より小さいとき、可能であればini_set('memory_limit', Value);を実行しmemory_limitを再設定する");

	// Block Name
	define("_MI_XP2_BLOCK_COMMENTS","最近のコメント");
	define("_MI_XP2_BLOCK_CONTENT","最近の記事内容");
	define("_MI_XP2_BLOCK_POSTS","最近の記事");
	define("_MI_XP2_BLOCK_CALENDER","カレンダー");
	define("_MI_XP2_BLOCK_POPULAR","人気記事リスト");
	define("_MI_XP2_BLOCK_ARCHIVE","アーカイブ");
	define("_MI_XP2_BLOCK_AUTHORS","投稿者");
	define("_MI_XP2_BLOCK_PAGE","ページ");
	define("_MI_XP2_BLOCK_SEARCH","検索");
	define("_MI_XP2_BLOCK_TAG","タグクラウド");
	define("_MI_XP2_BLOCK_CATEGORY","カテゴリー");
	define("_MI_XP2_BLOCK_META","メタ情報");
	define("_MI_XP2_BLOCK_SIDEBAR","サイドバー");
	define("_MI_XP2_BLOCK_WIDGET","ウィジェット");
	define("_MI_XP2_BLOCK_ENHANCED","拡張ブロック");
	define("_MI_XP2_BLOCK_BLOG_LIST","ブログリスト");
	define("_MI_XP2_BLOCK_GLOBAL_POSTS","最近の記事(全ブログ)");
	define("_MI_XP2_BLOCK_GLOBAL_COMM","最近のコメント(全ブログ)");
	define("_MI_XP2_BLOCK_GLOBAL_POPU","人気記事リスト(全ブログ)");

	// Notify Categories
	define('_MI_XP2_NOTCAT_GLOBAL', 'ブログ全体');
	define('_MI_XP2_NOTCAT_GLOBALDSC', 'ブログ全体における通知オプション');
	define('_MI_XP2_NOTCAT_CAT', '選択中のカテゴリ');
	define('_MI_XP2_NOTCAT_CATDSC', '選択中のカテゴリに対する通知オプション');
	define('_MI_XP2_NOTCAT_AUTHOR', '選択中の投稿者'); 
	define('_MI_XP2_NOTCAT_AUTHORDSC', '選択中の投稿者に対する通知オプション');
	define('_MI_XP2_NOTCAT_POST', '表示中の記事'); 
	define('_MI_XP2_NOTCAT_POSTDSC', '表示中の記事に対する通知オプション');

	// Each Notifications
	define('_MI_XP2_NOTIFY_GLOBAL_WAITING', '承認待ち');
	define('_MI_XP2_NOTIFY_GLOBAL_WAITINGCAP', '承認を要する投稿・編集が行われた場合に通知します。管理者専用');
	define('_MI_XP2_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: 承認待ち');

	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOST', '記事投稿');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOSTCAP', 'このブログ全体のいずれかに記事の投稿があった場合に通知する');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{XPRESS_BLOG_NAME}]記事: "{XPRESS_POST_TITLE}"');

	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENT', 'コメント投稿');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENTCAP', 'このブログ全体のいずれかにコメントの投稿があった場合に通知する');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]コメント: "{XPRESS_POST_TITLE}"');

	define('_MI_XP2_NOTIFY_CAT_NEWPOST', '選択カテゴリへの記事投稿');
	define('_MI_XP2_NOTIFY_CAT_NEWPOSTCAP', 'このカテゴリに記事投稿があった場合に通知する');
	define('_MI_XP2_NOTIFY_CAT_NEWPOSTSBJ', '[{XPRESS_BLOG_NAME}]記事: "{XPRESS_POST_TITLE}" (条件:カテゴリ="{XPRESS_CAT_TITLE}")');

	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENT', '選択カテゴリへのコメント投稿');
	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENTCAP', 'このカテゴリにコメント投稿があった場合に通知する');
	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]コメント: (記事"{XPRESS_POST_TITLE}") (条件:カテゴリ="{XPRESS_CAT_TITLE}")');

	define('_MI_XP2_NOTIFY_AUT_NEWPOST', '選択投稿者による記事投稿');
	define('_MI_XP2_NOTIFY_AUT_NEWPOSTCAP', 'この投稿者から記事投稿があった場合に通知する');
	define('_MI_XP2_NOTIFY_AUT_NEWPOSTSBJ', '[{XPRESS_BLOG_NAME}]記事: "{XPRESS_POST_TITLE}" (条件:投稿者="{XPRESS_AUTH_NAME}")');

	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENT', '選択投稿者記事へのコメント投稿');
	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENTCAP', 'この投稿者による記事へコメント投稿があった場合に通知する');
	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]コメント: (記事"{XPRESS_POST_TITLE}") (条件:投稿者="{XPRESS_AUTH_NAME}")');

	define('_MI_XP2_NOTIFY_POST_EDITPOST', '記事変更');
	define('_MI_XP2_NOTIFY_POST_EDITPOSTCAP', '表示中の記事に変更があった場合に通知する');
	define('_MI_XP2_NOTIFY_POST_EDITPOSTSBJ', '[{XPRESS_BLOG_NAME}]記事: "{XPRESS_POST_TITLE}"変更 (条件:記事指定)');

	define('_MI_XP2_NOTIFY_POST_NEWCOMMENT', '記事へのコメント投稿');
	define('_MI_XP2_NOTIFY_POST_NEWCOMMENTCAP', '表示中の記事にコメントの投稿があった場合に通知する');
	define('_MI_XP2_NOTIFY_POST_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]コメント: (記事"{XPRESS_POST_TITLE}") (条件:記事指定)');

}
?>