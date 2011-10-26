<?php

define('_MD_A_MYMENU_MYTPLSADMIN','テンプレート管理');
define('_MD_A_MYMENU_MYBLOCKSADMIN','ブロック管理/アクセス権限');
define('_MD_A_MYMENU_MYPREFERENCES','一般設定');


define('_MD_A_D3PIPES_H2_PIPEADMINLIST','パイプ管理 - 一覧');
define('_MD_A_D3PIPES_H2_PIPEADMINEDIT_FMT','パイプ管理 - パイプ番号%dの編集');
define('_MD_A_D3PIPES_H2_PIPEADMINNEW','パイプ管理 - 新規パイプの作成');
define('_MD_A_D3PIPES_H2_WIZARDFETCH','RSS/Atom取得パイプ作成ウイザード');
define('_MD_A_D3PIPES_H2_WIZARDINNER','サイト内新着情報パイプ作成ウイザード');
define('_MD_A_D3PIPES_H2_CACHEADMIN','キャッシュ管理');
define('_MD_A_D3PIPES_H2_CLIPPINGADMIN','切り抜き管理');
define('_MD_A_D3PIPES_H2_JOINTADMIN','ジョイント初期設定');
define('_MD_A_D3PIPES_H2_JOINTCLASSADMIN','ジョイントクラス初期設定');

define('_MD_A_D3PIPES_TH_PIPEID','番号');
define('_MD_A_D3PIPES_TH_PIPETYPE','種別');
define('_MD_A_D3PIPES_TH_PIPENAME','名称');
define('_MD_A_D3PIPES_TH_PIPEURL','URL');
define('_MD_A_D3PIPES_TH_PIPEIMAGE','画像');
define('_MD_A_D3PIPES_TH_PIPEDESCRIPTION','詳細');
define('_MD_A_D3PIPES_TH_PIPEWEIGHT','順番');
define('_MD_A_D3PIPES_TH_MAINDISP','表示');
define('_MD_A_D3PIPES_TITLE_MAINDISP','メインパートとして表示可能');
define('_MD_A_D3PIPES_TH_MAINLIST','リスト');
define('_MD_A_D3PIPES_TITLE_MAINLIST','トップページにパイプ名を表示する');
define('_MD_A_D3PIPES_TH_MAINAGGR','集約');
define('_MD_A_D3PIPES_TITLE_MAINAGGR','トップページ集約表示の対象とする');
define('_MD_A_D3PIPES_TH_MAINRSS','RSS');
define('_MD_A_D3PIPES_TITLE_MAINRSS','RSS配信を許可する');
define('_MD_A_D3PIPES_TH_BLOCKDISP','Block');
define('_MD_A_D3PIPES_TITLE_BLOCKDISP','ブロック表示を許可する');
define('_MD_A_D3PIPES_TH_INSUBMENU','SUB');
define('_MD_A_D3PIPES_TITLE_INSUBMENU','サブメニュー表示');
define('_MD_A_D3PIPES_TH_MODIFIED','更新');
define('_MD_A_D3PIPES_TH_LASTFETCH','最終取得');
define('_MD_A_D3PIPES_TH_ACTIONS','操作');
define('_MD_A_D3PIPES_TH_WEIGHT','処理順');
define('_MD_A_D3PIPES_TH_JOINT','ジョイント');
define('_MD_A_D3PIPES_TH_JOINTCLASS','クラス');
define('_MD_A_D3PIPES_TH_OPTION','オプション');
define('_MD_A_D3PIPES_TH_ANALYZE','デバッグ');

define('_MD_A_D3PIPES_TH_WIZ_SITENAME','サイト名');
define('_MD_A_D3PIPES_TH_WIZ_SITEURL','サイトのURL');
define('_MD_A_D3PIPES_TH_WIZ_RSSURL','RSS/AtomのURL');
define('_MD_A_D3PIPES_TH_WIZ_RSSENCODING','RSS/Atomのエンコーディング');
define('_MD_A_D3PIPES_TH_WIZ_RSSENCODING_NOTE','※判らなければ空欄');
define('_MD_A_D3PIPES_TH_WIZ_CLIPPINGYN','取得したエントリを保存する');
define('_MD_A_D3PIPES_TH_WIZ_ALLOWHTMLYN','可能な限り配信されたHTMLのまま表示する');
define('_MD_A_D3PIPES_TH_WIZ_MODNAME','モジュール名');
define('_MD_A_D3PIPES_TH_WIZ_DIRNAME','ディレクトリ');
define('_MD_A_D3PIPES_TH_WIZ_JOINTS','利用可能なジョイント');
define('_MD_A_D3PIPES_TH_WIZ_CREATEUNIONPIPE','これらを統合表示するパイプも作る');
define('_MD_A_D3PIPES_TH_WIZ_BTN_CONFIRM','内容確認');
define('_MD_A_D3PIPES_TH_WIZ_BTN_REGISTER','登録');
define('_MD_A_D3PIPES_TH_WIZ_WARN_RSSURL','RSS/AtomのURLからは有効なXMLが取得できないようです。');
define('_MD_A_D3PIPES_TITLE_WIZ_INNERUNION','サイト内新着一覧');

define('_MD_A_D3PIPES_LABEL_DELETEFSCACHE','TRUST_PATH/cache下の外部取得・キャッシュ・切り抜き各ジョイントのキャッシュをすべて削除する');
define('_MD_A_D3PIPES_LABEL_CLEARLASTFETCH','全パイプの最終取得時間をクリアする');
define('_MD_A_D3PIPES_LABEL_PROTECTBYCOMMENT','コメントのついた切り抜きは保護する');
define('_MD_A_D3PIPES_LABEL_PROTECTBYHIGHLIGHT','注目マークのついた切り抜きは保護する');
define('_MD_A_D3PIPES_LABEL_DELETEDEADLINKTOPIPEID','パイプとリンクしていない切り抜きを削除する');
define('_MD_A_D3PIPES_LABEL_FMT_DELETEOLDERTHAN','発行日が%s日以上前の切り抜きを削除する');
define('_MD_A_D3PIPES_LABEL_TOTALCLIPPINGS','保存された切り抜きの総件数');
define('_MD_A_D3PIPES_LABEL_SELECTEDCLIPPINGS','削除条件に合致した切り抜きの件数');

define('_MD_A_D3PIPES_LINK_MAKENEWPIPE','新規パイプ作成（上級者用）');
define('_MD_A_D3PIPES_LINK_WIZARDFETCH','RSS/Atom取得パイプ作成ウイザード');
define('_MD_A_D3PIPES_LINK_WIZARDINNER','サイト内新着情報パイプ作成ウイザード');
define('_MD_A_D3PIPES_LINK_ANALYZETHEJOINT','途中経過');
define('_MD_A_D3PIPES_LINK_PUBLICVIEW','公開側表示');

define('_MD_A_D3PIPES_BTN_UPDATE','更新');
define('_MD_A_D3PIPES_BTN_SAVE','保存');
define('_MD_A_D3PIPES_BTN_SAVEASNEWPIPE','新規パイプとして保存');
define('_MD_A_D3PIPES_BTN_RESET','リセット');
define('_MD_A_D3PIPES_BTN_EXECUTE','実行');
define('_MD_A_D3PIPES_BTN_COUNTNUM','件数確認');

define('_MD_A_D3PIPES_MSG_UPDATED','更新しました');
define('_MD_A_D3PIPES_MSG_PIPEUPDATED','パイプを更新しました');
define('_MD_A_D3PIPES_MSG_PIPEDELETED','パイプを削除しました');
define('_MD_A_D3PIPES_MSG_CACHEDELETED','キャッシュを削除しました');
define('_MD_A_D3PIPES_MSG_CLIPPINGUPDATED','切り抜きを更新/削除しました');

define('_MD_A_D3PIPES_CNFM_REGISTERASIS','以下の内容で登録しますか？');
define('_MD_A_D3PIPES_CNFM_DELETE','本当に削除してよろしいですか？');
define('_MD_A_D3PIPES_CNFM_PIPEDELETE','本当にパイプを削除しますか？ このパイプによって保存された切り抜きは削除されません。必要に応じて、「切り抜き管理」より操作してください');

define('_MD_A_D3PIPES_ERR_INVALIDSTARTJOINT_FMT','開始ジョイントとして指定できるのは、%s のいずれかだけです');
define('_MD_A_D3PIPES_ERR_CORRESPONDPARSENOTFOUND','外部より取得したXMLを解析するジョイントが指定されていません');

define('_MD_A_D3PIPES_TYPE_FETCH','外部取得');
define('_MD_A_D3PIPES_TYPE_BLOCK','内部取得(ブロック)');
define('_MD_A_D3PIPES_TYPE_LOCAL','内部取得');
define('_MD_A_D3PIPES_TYPE_UNION','連結');
define('_MD_A_D3PIPES_TYPE_OTHER','特殊');
define('_MD_A_D3PIPES_TYPE_CLIP','（保存有）');


?>