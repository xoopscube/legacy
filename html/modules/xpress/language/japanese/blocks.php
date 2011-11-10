<?php
if( ! defined( 'XP2_BLOCK_LANG_INCLUDED' ) ) {
	define( 'XP2_BLOCK_LANG_INCLUDED' , 1 ) ;
// general	
	define("_MB_XP2_COUNT",'表示数');
	define("_MB_XP2_COUNT_ZERO_ALL",'表示数(0の場合全てを表示)');
	define("_MB_XP2_LENGTH","長さ");
	define("_MB_XP2_ALL","すべて");
	define("_MB_XP2_BLOCK_CACHE_ERR","キャッシュが存在しません。<br />最初に%sモジュールにアクセスしてください。");
	define("_MB_XP2_SHOW_NUM_OF_POST","エントリー数の表示");
	define("_MB_XP2_SHOW_DROP_DOWN","ドロップダウンリストで表示");
	define("_MB_XP2_HIDE_EMPTY","エントリーのない項目をリスト一覧から除く");
	define("_MB_XP2_TITLE","タイトル");
	define("_MB_XP2_PUBLISH_DATE","投稿日付");
	define("_MB_XP2_SORT_ORDER","並び替え順序");
	define("_MB_XP2_SORT_ASC","昇順");
	define("_MB_XP2_SORT_DESC","降順");
	define("_MB_XP2_SHOW_DATE_SELECT","日付の表示");
	define("_MB_XP2_SHOW_DATE_NONE","表示しない");
	define("_MB_XP2_SHOW_POST_DATE","作成日を表示");
	define("_MB_XP2_SHOW_MODIFY_DATE","最終更新日を表示");
	define("_MB_XP2_SHOW_DATE","日付を表示する");
	define("_MB_XP2_DATE_FORMAT","日付のフォーマット(空白の場合WordPressでの設定が適用されます)");
	define("_MB_XP2_TIME_FORMAT","時刻のフォーマット(空白の場合WordPressでの設定が適用されます)");
	define("_MB_XP2_FLAT","フラット");
	define("_MB_XP2_LIST","リスト");
	define("_MB_XP2_FILE_NAME","ファイル名");
	define("_MB_XP2_THISTEMPLATE","このブロックのテンプレート");
	define("_MB_XP2_NO_JSCRIPT","ブラウザでJavascriptを有効にする必要があります。");
	define("_MB_XP2_CACHE_NOT_WRITABLE","キャッシュディレクトリへの書き込みが許可されていません。");
	
// recent comment block	
	define("_MB_XP2_COMM_DISP_AUTH","コメント投稿者を表示する");
	define("_MB_XP2_COMM_DISP_TYPE","コメントタイプを表示する");
	define("_MB_XP2_COM_TYPE","表示するコメントのタイプを選択");
	define("_MB_XP2_COMMENT","コメント");
	define("_MB_XP2_TRUCKBACK","トラックバック");
	define("_MB_XP2_PINGBACK","ピンバック");
	
// recent posts content
	define("_MB_XP2_P_EXCERPT","記事を概要で表示する");
	define("_MB_XP2_P_EXCERPT_SIZE","記事の概要文字数");
	define("_MB_XP2_CATS_SELECT","対象のカテゴリー選択");
	define("_MB_XP2_TAGS_SELECT","対象のタグ選択(複数ある場合はカンマ区切りで入力");
	define("_MB_XP2_DAY_SELECT","日付による抽出");
	define("_MB_XP2_NONE","なし");
	define("_MB_XP2_TODAY","本日の投稿");
	define("_MB_XP2_LATEST","最新の投稿");
	define("_MB_XP2_DAY_BETWEEN","");
	define("_MB_XP2_DAYS_AND","から");
	define("_MB_XP2_DAYS_AGO","日前までの間");
	define("_MB_XP2_CATS_DIRECT_SELECT","IDを直接指定(複数ある場合はカンマ区切りで入力)");
	
// recent posts list	
	define("_MB_XP2_REDNEW_DAYS","赤のNewマークを表示する日数");
	define("_MB_XP2_GREENNEW_DAYS","緑のNewマークを表示する日数");	

// calender		
	define("_MB_XP2_SUN_COLOR","日曜日の表示色");
	define("_MB_XP2_SAT_COLOR","土曜日の表示色");
	
// popular		
	define("_MB_XP2_MONTH_RANGE","指定月数内のものを表示(0;指定なし)");
	
// archives
	define("_MB_XP2_ARC_TYPE","アーカイブタイプ");
	define("_MB_XP2_ARC_YEAR","年別アーカイブ");
	define("_MB_XP2_ARC_MONTH","月別アーカイブ");
	define("_MB_XP2_ARC_WEEK","週別アーカイブ");
	define("_MB_XP2_ARC_DAY","日別アーカイブ");
	define("_MB_XP2_ARC_POST","個別記事アーカイブ");

// authors	
	define("_MB_XP2_EXCLUEDEADMIN","リスト一覧から管理人を除く");
	define("_MB_XP2_SHOW_FULLNAME","著者名をフルネームで表示");

// page 	
	define("_MB_XP2_PAGE_ORDERBY","ページリストのソート項目");
	define("_MB_XP2_PAGE_TITLE","タイトル名順");
	define("_MB_XP2_PAGE_MENU_ORDER","ページ順");
	define("_MB_XP2_PAGE_POST_DATE","作成日順");
	define("_MB_XP2_PAGE_POST_MODIFY","最終更新日順");
	define("_MB_XP2_PAGE_ID","ページ ID順");
	define("_MB_XP2_PAGE_AUTHOR","作成者ID 順");
	define("_MB_XP2_PAGE_SLUG","ページスラッグ順");
	define("_MB_XP2_PAGE_EXCLUDE","リストから除外するページID をカンマ区切りで昇順に指定。");
	define("_MB_XP2_PAGE_EXCLUDE_TREE","リストから除外するページID を指定(子ページも除外されます)。");
	define("_MB_XP2_PAGE_INCLUDE","指定したページID のみリストに表示。カンマ区切りで昇順に指定");
	define("_MB_XP2_PAGE_DEPTH","ページ階層のどのレベルまでをリストに出力するかを指定。 (0=全親子ページを出力）");
	define("_MB_XP2_PAGE_CHILD_OF","指定IDのページを親とする階層のページをリストに表示します。(0=全親子ページを出力）");
	define("_MB_XP2_PAGE_HIERARCHICAL","子ページをリスト表示するとき、インデント（字下げ）する。");
	define("_MB_XP2_PAGE_META_KEY","ここに記述したカスタムフィールドキーを持つページだけを表示します。");
	define("_MB_XP2_PAGE_META_VALUE","ここに記述したカスタムフィールド値を持つページだけを表示します。");
	
// Search
	define("_MB_XP2_SEARCH_LENGTH","検索BOXの長さ");
	
// tag cloud
	define("_MB_XP2_CLOUD_SMALLEST",'最少使用数のタグの表示に使うフォントサイズ');
	define("_MB_XP2_CLOUD_LARGEST",'最多使用数のタグの表示に使うフォントサイズ');
	define("_MB_XP2_CLOUD_UNIT","フォントサイズの単位。pt, px, em, % 等");
	define("_MB_XP2_CLOUD_NUMBER","クラウドに表示するタグ数。[0] を指定すると全タグを表示");
	define("_MB_XP2_CLOUD_FORMAT","クラウド表示のフォーマット");
	define("_MB_XP2_CLOUD_ORDERBY","タグの表示順とする項目");
	define("_MB_XP2_CLOUD_ORDER","ソート順（ランダムはWordPress2.5以上で指定可能）");
	define("_MB_XP2_CLOUD_EXCLUDE","除外するタグの term_id をカンマ区切りで指定");
	define("_MB_XP2_CLOUD_INCLUDE","表示対象とするタグの term_id をカンマ区切りで指定、空白時は全タグ対象");
	define("_MB_XP2_RAND","ランダム");
	define("_MB_XP2_TAG_NAME","タグ名");
	define("_MB_XP2_TAG_COUNT","使用回数");
	
// Categorie
	define("_MB_XP2_CAT_ALL_STR","全カテゴリへのリンクを示す文字を指定します。(空白時は表示なし）");
	define("_MB_XP2_CAT_ORDERBY","カテゴリのソート項目");
	define("_MB_XP2_CAT_NAME","カテゴリ名");
	define("_MB_XP2_CAT_COUNT","カテゴリの投稿数");
	define("_MB_XP2_CAT_ID","カテゴリID");
	define("_MB_XP2_SHOW_LAST_UPDATE","各カテゴリに属する投稿の最終更新日を表示する。");
	define("_MB_XP2_CAT_HIDE_EMPTY","投稿のないカテゴリを非表示にする。");
	define("_MB_XP2_DESC_FOR_TITLE","カテゴリの概要をリンクの title 属性に挿入する。");
	define("_MB_XP2_CAT_EXCLUDE","リストから除外するカテゴリID をカンマ区切りで昇順に指定。");
	define("_MB_XP2_CAT_INCLUDE","指定したカテゴリID のみリストに表示。カンマ区切りで昇順に指定");
	define("_MB_XP2_CAT_HIERARCHICAL","サブカテゴリーを表示するとき、インデント（字下げ）する。");
	define("_MB_XP2_CAT_DEPTH","カテゴリ階層のどのレベルまでをリストに出力するかを指定。 (0=全親子カテゴリを出力）");
	
// meta 
	define("_MB_XP2_META_WP_LINK","WordPressサイトへのリンクを表示");
	define("_MB_XP2_META_XOOPS_LINK","Xoopsサイトへのリンクを表示");
	define("_MB_XP2_META_POST_RSS","投稿のRSSを表示");
	define("_MB_XP2_META_COMMENT_RSS","コメントのRSSを表示");
	define("_MB_XP2_META_POST_NEW","新規投稿を表示");
	define("_MB_XP2_META_ADMIN","サイトの管理を表示");
	define("_MB_XP2_META_README","ReadMeを表示");
	define("_MB_XP2_META_CH_STYLE","表示モード切替を表示");

// widget 
	define("_MB_XP2_SELECT_WIDGET","表示するウィジェットを選択 (複数選択可)");
	define("_MB_XP2_NO_WIDGET","WordPress側で表示するウィジェットが選択されていません");
	define("_MB_XP2_WIDGET_TITLE_SHOW","単独ウィジェット選択時、ウィジェットのタイトルを表示");
	
// custom 
	define("_MB_XP2_ENHACED_FILE","カスタムブロックを表示するファイル名を入力してください。");
	define("_MB_XP2_MAKE_ENHACED_FILE","ここで指定したファイルをテーマ内のブロックディレクトリーに作成してください。");

// blog_list
	define("_MB_XP2_BLOG_ORDERBY","ブログのソート項目");
	define("_MB_XP2_BLOG_NAME","ブログ名");
	define("_MB_XP2_BLOG_COUNT","ブログの投稿数");
	define("_MB_XP2_BLOG_ID","ブログID");
// global_blog_list
	define("_MB_XP2_SHOW_BLOGS_SELECT","表示ブログの選択");
	define("_MB_XP2_EXCLUSION_BLOGS_SELECT","除外ブログの選択");
	define("_MB_XP2_BLOGS_DIRECT_SELECT","ブログIDを直接指定(複数ある場合はカンマ区切りで入力)");
	define("_MB_XP2_SHOWN_FOR_EACH_BLOG","ブログ毎に表示する。");

}
?>