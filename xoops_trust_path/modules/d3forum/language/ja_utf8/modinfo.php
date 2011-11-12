<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","フォーラム");

// A brief description of this module
define($constpref."_DESC","XOOPSフォーラムモジュール");

// Names of blocks for this module (Not all module has blocks)
define($constpref."_BNAME_LIST_TOPICS","トピック一覧");
define($constpref."_BDESC_LIST_TOPICS","汎用ブロック。「編集」で様々な機能を持たせることができます");
define($constpref."_BNAME_LIST_POSTS","投稿一覧");
define($constpref."_BNAME_LIST_FORUMS","フォーラム一覧");

// admin menu
define($constpref.'_ADMENU_CATEGORYACCESS','カテゴリー権限設定');
define($constpref.'_ADMENU_FORUMACCESS','フォーラム権限設定');
define($constpref.'_ADMENU_ADVANCEDADMIN','アドバンス管理');
define($constpref.'_ADMENU_POSTHISTORIES','投稿編集/削除履歴');
define($constpref.'_ADMENU_MYLANGADMIN' , '言語定数管理' ) ;
define($constpref.'_ADMENU_MYTPLSADMIN' , 'テンプレート管理' ) ;
define($constpref.'_ADMENU_MYBLOCKSADMIN' , 'ブロック管理/アクセス権限' ) ;
define($constpref.'_ADMENU_MYPREFERENCES' , '一般設定' ) ;

// configurations
define($constpref.'_TOP_MESSAGE','フォーラムトップのメッセージ');
define($constpref.'_TOP_MESSAGEDEFAULT','<h1 class="d3f_title">フォーラムトップ</h1><p class="d3f_welcome">興味のあるフォーラムへぜひご参加ください</p>');
define($constpref.'_SHOW_BREADCRUMBS','パンくずを表示する');
define($constpref.'_DEFAULT_OPTIONS','投稿オプションのデフォルト値');
define($constpref.'_DEFAULT_OPTIONSDSC','新規トピックや返信の初期状態で有効となっているオプションをカンマ(,)で区切って入力します。<br />インストール直後は smiley,xcode,br,number_entity となっています。<br />その他、 special_entity html attachsig u2t_marked 等が指定可能です');
define($constpref."_USENAME","ユーザー名表示");
define($constpref."_USENAMEDESC","ユーザー名表示に「uname」(ユーザーID)「name」（本名）どちらを使用するかを設定します。<br />xoopsデフォルトは「uname」(ユーザーID)");
define($constpref."_USENAME_UNAME","「uname」(ユーザーID)を使用");
define($constpref."_USENAME_NAME","「name」（本名）を使用");
define($constpref.'_ALLOW_HTML','投稿本文内のHTMLを許可する');
define($constpref.'_ALLOW_HTMLDSC','投稿本文のHTML特殊文字を許可します。不特定多数に許可すると、Script Insertion 脆弱性につながります');
define($constpref.'_ALLOW_TEXTIMG','投稿本文内の外部画像を許可する');
define($constpref.'_ALLOW_TEXTIMGDSC','投稿本文に[img]タグで外部サイトの画像を表示させると、このサイトの訪問者のIPやUser-Agentを抜かれることにつながります');
define($constpref.'_ALLOW_SIG','署名付与を許可する');
define($constpref.'_ALLOW_SIGDSC','投稿本文の下部に署名がつけられるようになります');
define($constpref.'_ALLOW_SIGIMG','署名内の外部画像を許可する');
define($constpref.'_ALLOW_SIGIMGDSC','署名に[img]タグで外部サイトの画像を表示させると、このサイトの訪問者のIPやUser-Agentを抜かれることにつながります');
define($constpref.'_USE_VOTE','投票機能を利用する');
define($constpref.'_USE_SOLVED','解決済機能を利用する');
define($constpref.'_ALLOW_MARK','注目トピック機能を利用する');
define($constpref.'_ALLOW_HIDEUID','ユーザが名前を隠して投稿することを許可する');
define($constpref.'_POSTS_PER_TOPIC','トピック内最大投稿数');
define($constpref.'_POSTS_PER_TOPICDSC','投稿数がこの数に到達したトピックは自動的にロックされます');
define($constpref.'_HOT_THRESHOLD','人気トピック投稿数');
define($constpref.'_HOT_THRESHOLDDSC','「盛り上がっている」スレッドかどうかを判断する基準となる投稿数です');
define($constpref.'_TOPICS_PER_PAGE','トピック一覧での表示トピック数');
define($constpref.'_TOPICS_PER_PAGEDSC','');
define($constpref.'_VIEWALLBREAK','トピック一覧でのページ分割単位');
define($constpref.'_VIEWALLBREAKDSC','');
define($constpref.'_SELFEDITLIMIT','自己編集タイムリミット(秒)');
define($constpref.'_SELFEDITLIMITDSC','一般ユーザが自分の投稿を編集する場合、投稿してから何秒まで内容の変更を許可するか。一般ユーザによる自己編集を禁止する場合は0を指定');
define($constpref.'_SELFDELLIMIT','自己削除タイムリミット(秒)');
define($constpref.'_SELFDELLIMITDSC','一般ユーザが自分の投稿を削除する場合、投稿してから何秒まで削除を許可するか。ただし、一般ユーザは、その下にレスポンスのついてしまった投稿は削除できません。一般ユーザによる自己削除を禁止する場合は0を指定');
define($constpref.'_CSS_URI','モジュール用CSSのURI');
define($constpref.'_CSS_URIDSC','このモジュール専用のCSSファイルのURIを相対パスまたは絶対パスで指定します。デフォルトは {mod_url}/index.php?page=main_css です。');
define($constpref.'_IMAGES_DIR','イメージファイルディレクトリ');
define($constpref.'_IMAGES_DIRDSC','このモジュール用のイメージが格納されたディレクトリをモジュールディレクトリからの相対パスで指定します。デフォルトはimagesです。');
define($constpref.'_BODY_EDITOR','本文編集エディタ');
define($constpref.'_BODY_EDITORDSC','WYSIWYGエディタは、HTMLタグを許可するフォーラムでのみ有効になります。HTMLタグを許可しないフォーラムでは無条件でxoopsdhtmlとなります。');
define($constpref.'_ANONYMOUS_NAME','ゲストユーザのデフォルト名');
define($constpref.'_ANONYMOUS_NAMEDSC','ゲスト用投稿フォームに最初に入力されている名前です。「匿名さん、お腹いっぱい」等');
define($constpref.'_ICON_MEANINGS','投稿（アイコン）の意味づけ');
define($constpref.'_ICON_MEANINGSDSC','投稿に性格を持たせるための選択肢です。パイプ(|)で区切ってください。最初が0で次が1と番号が割り当てられ、posticon(数字).gifがアイコンとして用いられます');
define($constpref.'_ICON_MEANINGSDEF','なし|通常|不満|満足|下げ|上げ|報告|質問');
define($constpref.'_GUESTVOTE_IVL','投稿へのゲスト投票');
define($constpref.'_GUESTVOTE_IVLDSC','ある投稿(post)へのゲストによる投票を禁止する場合は0を、投票を許可する場合は、同一IPからの再投票を禁止する秒数を指定します。');
define($constpref.'_ANTISPAM_GROUPS','SPAM投稿チェックを行うグループ');
define($constpref.'_ANTISPAM_GROUPSDSC','通常は全て未選択です。SPAM投稿チェックを行うべきグループがある場合のみ選択してください。');
define($constpref.'_ANTISPAM_CLASS','SPAM投稿チェック用クラス');
define($constpref.'_ANTISPAM_CLASSDSC','デフォルトは default です。ゲストについてもSPAM投稿チェックを行わない場合はここを空欄にします。');


// Notify Categories
define($constpref.'_NOTCAT_TOPIC', '表示中のトピック'); 
define($constpref.'_NOTCAT_TOPICDSC', '表示中のトピックに対する通知オプション');
define($constpref.'_NOTCAT_FORUM', '表示中のフォーラム'); 
define($constpref.'_NOTCAT_FORUMDSC', '表示中のフォーラムに対する通知オプション');
define($constpref.'_NOTCAT_CAT', '表示中のカテゴリ');
define($constpref.'_NOTCAT_CATDSC', '表示中のカテゴリに対する通知オプション');
define($constpref.'_NOTCAT_GLOBAL', '全カテゴリー');
define($constpref.'_NOTCAT_GLOBALDSC', '全カテゴリー共通の通知オプション');

// Each Notifications
define($constpref.'_NOTIFY_TOPIC_NEWPOST', 'トピック内投稿');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP', 'このトピックに投稿があった場合に通知する');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} トピック内投稿 {POST_TITLE}');

define($constpref.'_NOTIFY_FORUM_NEWPOST', 'フォーラム内投稿');
define($constpref.'_NOTIFY_FORUM_NEWPOSTCAP', 'このフォーラムに投稿があった場合に通知する');
define($constpref.'_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} フォーラム内投稿 {POST_TITLE}');

define($constpref.'_NOTIFY_FORUM_NEWTOPIC', 'フォーラム内新トピック');
define($constpref.'_NOTIFY_FORUM_NEWTOPICCAP', 'このフォーラムにおいて新規トピックが立てられた場合に通知する');
define($constpref.'_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} フォーラム内新トピック {TOPIC_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWPOST', 'カテゴリ内投稿');
define($constpref.'_NOTIFY_CAT_NEWPOSTCAP', 'このカテゴリに投稿があった場合に通知する');
define($constpref.'_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} カテゴリ内投稿 {POST_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWTOPIC', 'カテゴリ内新トピック');
define($constpref.'_NOTIFY_CAT_NEWTOPICCAP', 'このカテゴリにおいて新規トピックが立てられた場合に通知する');
define($constpref.'_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} カテゴリ内新トピック {TOPIC_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWFORUM', 'カテゴリ内新フォーラム');
define($constpref.'_NOTIFY_CAT_NEWFORUMCAP', 'このカテゴリにおいて新フォーラムが立てられた場合に通知する');
define($constpref.'_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} カテゴリ内新フォーラム {FORUM_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_NEWPOST', '新投稿全体');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP', '全カテゴリーのいずれかに投稿があった場合に通知する');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: 投稿 {POST_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_NEWTOPIC', '新トピック全体');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP', '全カテゴリーのいずれかに新規トピックが立てられた場合に通知する');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: 新トピック {TOPIC_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_NEWFORUM', '新フォーラム全体');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP', '全カテゴリーのいずれかに新フォーラムが立てられた場合に通知する');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: 新フォーラム {FORUM_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULL', '投稿全文');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP', '投稿全文を通知します。（対象は全カテゴリー）');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}');
define($constpref.'_NOTIFY_GLOBAL_WAITING', '承認待ち');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCAP', '承認を要する投稿・編集が行われた場合に通知します。管理者専用');
define($constpref.'_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: 承認待ち {POST_TITLE}');

}

?>