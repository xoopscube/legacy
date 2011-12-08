<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: lng.php,v 1.30 2011/12/08 07:01:00 nao-pon Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki message file (japanese)

// ※このファイルの文字コードは、エンコーディングの設定と一致
//   している必要があります

// Q & A 認証
$root->riddles = array(
//	'問題' => '答え',
	'「東京」の読みがな？(ひらがなで)' => 'とうきょう',
	'「名古屋」の読みがな？(ひらがなで)' => 'なごや',
	'「大阪」の読みがな？(ひらがなで)' => 'おおさか',
	'「京都」の読みがな？(ひらがなで)' => 'きょうと',
	'日本の首都は？(漢字で)' => '東京',
);

///////////////////////////////////////
// Page titles
$root->_title_cannotedit = '$1 は編集できません';
$root->_title_edit       = '$1 の編集';
$root->_title_preview    = '$1 のプレビュー';
$root->_title_collided   = '$1 で【更新の衝突】が起きました';
$root->_title_updated    = '$1 を更新しました';
$root->_title_deleted    = '$1 を削除しました';
$root->_title_help       = 'ヘルプ';
$root->_title_invalidwn  = '有効なWikiNameではありません';
$root->_title_backuplist = 'バックアップ一覧';
$root->_title_ng_riddle  = 'Q & A 認証に失敗しました<br />$1 をプレビューします';
$root->_title_backlink   = '%s へのリンクページ一覧';

///////////////////////////////////////
// Messages
$root->_msg_unfreeze       = '凍結解除';
$root->_msg_preview        = '以下のプレビューを確認して、よければページ下部のボタンで更新してください。';
$root->_msg_preview_delete = '（ページの内容は空です。更新するとこのページは削除されます。）';
$root->_msg_collided       = 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
今回追加した行は +で始まっています。<br />
!で始まる行が変更された可能性があります。<br />
!や+で始まる行を修正して再度ページの更新を行ってください。<br />';

$root->_msg_collided_auto  = 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
自動で衝突を解消しましたが、問題がある可能性があります。<br />
確認後、[ページの更新]を押してください。<br />';

$root->_msg_invalidiwn     = '$1 は有効な $2 ではありません。';
$root->_msg_invalidpass    = 'パスワードが間違っています。';
$root->_msg_notfound       = '指定されたページは見つかりませんでした。';
$root->_msg_addline        = '追加された行は<span class="diff_added">この色</span>です。';
$root->_msg_delline        = '削除された行は<span class="diff_removed">この色</span>です。';
$root->_msg_goto           = '$1 へ行く。';
$root->_msg_andresult      = '$1 のすべてを含むページは <strong>$3</strong> ページ中、 <strong>$2</strong> ページ見つかりました。';
$root->_msg_orresult       = '$1 のいずれかを含むページは <strong>$3</strong> ページ中、 <strong>$2</strong> ページ見つかりました。';
$root->_msg_notfoundresult = '$1 を含むページは見つかりませんでした。';
$root->_msg_symbol         = '記号';
$root->_msg_other          = '日本語';
$root->_msg_help           = 'テキスト整形のルールを表示する';
$root->_msg_week           = array('日','月','火','水','木','金','土');
$root->_msg_content_back_to_top = '<div class="jumpmenu"><a href="#'.$root->mydirname.'_navigator" title="Page Top"><img src="'.$const['LOADER_URL'].'?src=arrow_up.png" alt="Page Top" width="16" height="16" /></a></div>';
$root->_msg_word           = 'これらのキーワードがハイライトされています：';
$root->_msg_not_readable   = 'ページを表示する権限がありません。';
$root->_msg_not_editable   = 'ページを編集する権限がありません。';

///////////////////////////////////////
// Symbols
$root->_symbol_anchor   = 'src:anchor.png,width:12,height:12';
$root->_symbol_noexists = '<img src="'.$const['IMAGE_DIR'].'paraedit.png" alt="編集" height="9" width="9" />';

///////////////////////////////////////
// Form buttons
$root->_btn_preview   = 'プレビュー';
$root->_btn_repreview = '再度プレビュー';
$root->_btn_update    = 'ページの更新';
$root->_btn_cancel    = 'キャンセル';
$root->_btn_notchangetimestamp = 'タイムスタンプを変更しない';
$root->_btn_addtop    = 'ページの上に追加';
$root->_btn_template  = '雛形とするページ';
$root->_btn_load      = '読込';
$root->_btn_edit      = '編集';
$root->_btn_delete    = '削除';
$root->_btn_reading   = 'ページ頭文字読み';
$root->_btn_alias     = 'ページ別名<span class="edit_form_note">(複数は"<span style="color:red;font-weight:bold;font-size:120%;">:</span>"[コロン]で区切る)</span>';
$root->_btn_alias_lf  = 'ページ別名<span class="edit_form_note">(複数は[<span style="color:red;">改行</span>]で区切る)</span>';
$root->_btn_riddle    = 'Q &amp; A 認証:<span class="edit_form_note"> ページ更新時は次の質問にお答えください。(プレビュー時は必要ありません)</span>';
$root->_btn_pgtitle   = 'ページタイトル<span class="edit_form_note">( 空白で自動設定 )</span>';
$root->_btn_pgorder   = 'ページ並び順<span class="edit_form_note">( 0-9 小数可 標準:1 )</span>';
$root->_btn_other_op  = '詳細な入力項目を表示';
$root->_btn_emojipad  = '絵文字パッド';
$root->_btn_esummary  = '編集の要約';
$root->_btn_source    = 'ページ内容';

///////////////////////////////////////
// Authentication
$root->_title_cannotread = '$1 は閲覧できません';
$root->_msg_auth         = 'PukiWikiAuth';

///////////////////////////////////////
// Page name
$root->rule_page = 'FormattingRules';	// Formatting rules
$root->help_page = 'Help';		// Help

///////////////////////////////////////
// TrackBack (REMOVED)
$root->_tb_date  = 'Y年n月j日 H:i:s';

/////////////////////////////////////////////////
// 題名が未記入の場合の表記 (article)
$root->_no_subject = '無題';

/////////////////////////////////////////////////
// 名前が未記入の場合の表記 (article, comment, pcomment)
$root->_no_name = '';

/////////////////////////////////////////////////
// Title of the page contents list
$root->contents_title = 'ページ内コンテンツ';

/////////////////////////////////////////////////
// Skin
/////////////////////////////////////////////////

$root->_LANG['skin']['topage']    = 'ページへ戻る';
$root->_LANG['skin']['add']       = '追加';
$root->_LANG['skin']['backup']    = 'バックアップ';
$root->_LANG['skin']['copy']      = '複製';
$root->_LANG['skin']['diff']      = '差分';
$root->_LANG['skin']['back']      = '履歴';
$root->_LANG['skin']['edit']      = '編集';
$root->_LANG['skin']['filelist']  = 'ファイル名一覧';	// List of filenames
$root->_LANG['skin']['attaches']  = '添付ファイル一覧';
$root->_LANG['skin']['freeze']    = '凍結';
$root->_LANG['skin']['help']      = 'ヘルプ';
$root->_LANG['skin']['list']      = '全ページ一覧';
$root->_LANG['skin']['list_s']    = '一覧';	// List of pages
$root->_LANG['skin']['new']       = 'ページ新規作成';
$root->_LANG['skin']['new_s']     = '新規';
$root->_LANG['skin']['newsub']    = '下位ページ新規作成';
$root->_LANG['skin']['newsub_s']  = '下位';
$root->_LANG['skin']['menu']      = 'メニュー';
$root->_LANG['skin']['header']    = '頁上';
$root->_LANG['skin']['footer']    = '頁下';
$root->_LANG['skin']['rdf']       = '最終更新のRDF';	// RDF of RecentChanges
$root->_LANG['skin']['recent']    = '最新ページの一覧';	// RecentChanges
$root->_LANG['skin']['recent_s']  = '最新';
$root->_LANG['skin']['refer']     = 'リンク元';	// Show list of referer
$root->_LANG['skin']['reload']    = 'リロード';
$root->_LANG['skin']['rename']    = '名前変更';	// Rename a page (and related)
$root->_LANG['skin']['rss']       = '最新ページのRSS';	// RSS of RecentChanges
$root->_LANG['skin']['rss10']     = $root->_LANG['skin']['rss'] . ' 1.0';
$root->_LANG['skin']['rss20']     = $root->_LANG['skin']['rss'] . ' 2.0';
$root->_LANG['skin']['atom']      = $root->_LANG['skin']['rss'] . ' Atom';
$root->_LANG['skin']['search']    = '単語検索';
$root->_LANG['skin']['search_s']  = '検索';
$root->_LANG['skin']['top']       = 'トップ';	// Top page
$root->_LANG['skin']['trackback'] = 'Trackback';	// Show list of trackback
$root->_LANG['skin']['unfreeze']  = '凍結解除';
$root->_LANG['skin']['upload']    = '添付';	// Attach a file
$root->_LANG['skin']['pginfo']    = '権限';
$root->_LANG['skin']['comments']  = 'コメント';
$root->_LANG['skin']['lastmodify']= '最終更新';
$root->_LANG['skin']['linkpage']  = 'リンクページ';
$root->_LANG['skin']['pagealias'] = 'ページ別名';
$root->_LANG['skin']['pageowner'] = 'ページ作成';
$root->_LANG['skin']['siteadmin'] = 'サイト管理';
$root->_LANG['skin']['none']      = '未設定';
$root->_LANG['skin']['pageinfo']  = 'ぺージ情報';
$root->_LANG['skin']['pagename']  = 'ぺージ名';
$root->_LANG['skin']['readable']  = '閲覧可';
$root->_LANG['skin']['editable']  = '編集可';
$root->_LANG['skin']['groups']    = 'グループ';
$root->_LANG['skin']['users']     = 'ユーザー';
$root->_LANG['skin']['perm']['all']  = 'すべての訪問者';
$root->_LANG['skin']['perm']['none'] = 'なし';
$root->_LANG['skin']['print']     = '印刷に適した表示';
$root->_LANG['skin']['print_s']   = '印刷';
$root->_LANG['skin']['powered']   = 'Powered by xpWiki';
$root->_LANG['skin']['powered_s'] = 'xpWiki';
$root->_LANG['skin']['princeps']  = '初版日時';

///////////////////////////////////////
// Plug-in message
///////////////////////////////////////
// add.inc.php
$root->_title_add = '$1 への追加';
$root->_msg_add   = 'ページへの追加は、現在のページ内容に改行が二つと入力内容が追加されます。';

///////////////////////////////////////
// article.inc.php
$root->_btn_name    = 'お名前';
$root->_btn_article = '記事の投稿';
$root->_btn_subject = '題名: ';
$root->_msg_article_mail_sender = '投稿者: ';
$root->_msg_article_mail_page   = '投稿先: ';


///////////////////////////////////////
// attach.inc.php
$root->_attach_messages = array(
	'msg_uploaded' => '$1 にアップロードしました',
	'msg_deleted'  => '$1 からファイルを削除しました',
	'msg_freezed'  => '添付ファイルを凍結しました。',
	'msg_unfreezed'=> '添付ファイルを凍結解除しました。',
	'msg_renamed'  => '添付ファイルの名前を変更しました。',
	'msg_upload'   => '$1 への添付',
	'msg_info'     => '添付ファイルの情報',
	'msg_confirm'  => '<p>%s を削除します。</p>',
	'msg_list'     => '添付ファイル一覧',
	'msg_listpage' => '$1 の添付ファイル一覧',
	'msg_listall'  => '全ページの添付ファイル一覧',
	'msg_file'     => '添付ファイル',
	'msg_maxsize'  => 'アップロード可能最大ファイルサイズは %s です。',
	'msg_count'    => ' <span class="small">%s件</span>',
	'msg_password' => 'ファイルに設定するパスワード(必須)',
	'msg_password2'=> 'ファイルに設定したパスワード',
	'msg_adminpass'=> '管理者パスワード',
	'msg_delete'   => 'このファイルを削除します。',
	'msg_backup'   => 'バックアップする',
	'msg_freeze'   => 'このファイルを凍結します。',
	'msg_unfreeze' => 'このファイルを凍結解除します。',
	'msg_isfreeze' => 'このファイルは凍結されています。',
	'msg_rename'   => '名前を変更します。',
	'msg_newname'  => '新しい名前',
	'msg_require'  => '(アップロード時に設定したパスワードが必要です)',
	'msg_filesize' => 'サイズ',
	'msg_date'     => '登録日時',
	'msg_dlcount'  => 'アクセス数',
	'msg_md5hash'  => 'MD5ハッシュ値',
	'msg_page'     => 'ページ',
	'msg_filename' => '格納ファイル名',
	'msg_owner'    => '所有者',
	'err_noparm'   => '$1 へはアップロード・削除はできません',
	'err_exceed'   => '$1 へのファイルサイズが大きすぎます',
	'err_exists'   => '$1 に同じファイル名が存在します',
	'err_notfound' => '$1 にそのファイルは見つかりません',
	'err_noexist'  => '添付ファイルがありません。',
	'err_delete'   => '$1 からファイルを削除できませんでした',
	'err_rename'   => 'ファイル名を変更できませんでした',
	'err_password' => 'パスワードが一致しません。',
	'err_adminpass'=> '管理者パスワードが一致しません。',
	'err_nopage'   => 'ページ「$1」がありません。先にページを作成してください。',
	'btn_upload'   => 'アップロード',
	'btn_upload_fm'=> 'アップロードフォーム',
	'btn_info'     => '詳細',
	'btn_submit'   => '実行',
	'msg_copyrighted'  => '添付ファイルを著作権保護しました。',
	'msg_uncopyrighted'=> '添付ファイルの著作権保護を解除しました。',
	'msg_copyright'  => 'このファイルは著作権上、保護する必要があります。',
	'msg_copyright0' => 'このファイルは 私の著作物 または 著作権フリー です。',
	'msg_copyright_s'=> '他人の著作物',
	'err_copyright'  => 'このファイルは著作権上、保護されているため 表示・ダウンロード はできません。',
	'msg_noinline1'  => 'インライン表示を禁止。',
	'msg_noinline0-1'=> 'インライン表示禁止を解除。',
	'msg_noinline-1' => 'インライン表示を許可。',
	'msg_noinline01' => 'インライン表示許可を解除。',
	'msg_noinlined'  => '添付ファイルのインライン表示の設定を登録しました。',
	'msg_unnoinlined'=> '添付ファイルのインライン表示の設定を解除しました。',
	'msg_nopcmd'     => '動作が指定されていません。',
	'err_extension'=> 'このページのオーナー権限がないため、拡張子が $1 のファイルは添付できません。',
	'msg_set_css'  => '$1 へスタイルシートを設定しました。',
	'msg_unset_css'=> '$1 のスタイルシートを解除しました。',
	'msg_untar'    => 'TAR形式を解凍する',
	'msg_search_updata'=> 'このページへのアップロード済みデータを探す。',
	'msg_paint_tool'=> 'お絵かきツール',
	'msg_shi'      => 'しぃペインター',
	'msg_shipro'   => 'しぃペインターPro',
	'msg_width'    => '横',
	'msg_height'   => '縦',
	'msg_max'      => '最大',
	'msg_do_paint' => 'お絵かきする',
	'msg_save_movie'=> '動画記録',
	'msg_adv_setting'=> '---　拡張指定　---',
	'msg_init_image'=> 'キャンバスに読み込む画像ファイル(JPEG or GIF)',
	'msg_fit_size' => 'キャンバスサイズをこの画像に合わせる',
	'msg_extensions' => '添付可能なファイルの拡張子( $1 )',
	'msg_rotated_ok' => '画像を回転しました。<br />ブラウザでリロードしないと正しく表示されていないかもしれません。',
	'msg_rotated_ng' => '画像を回転できませんでした。',
	'err_isflash' => 'Flashファイルをアップロードする権限がありません。',
	'msg_make_thumb' => 'サムネイルを作成(画像ファイルのみ): ',
	'msg_sort_time' => '最新順',
	'msg_sort_name' => 'ファイル名順',
	'msg_list_view' => 'リスト表示',
	'msg_image_view' => 'イメージ表示',
	'msg_insert' => '挿入',
	'msg_select_current' => ' (参照中)',
	'msg_select_useful' => 'ファイル添付用ページ',
	'msg_select_manyitems' => '添付ファイルの多いページ',
	'msg_noupload' => '$1 へファイルをアップロードする権限がありません。',
	'msg_show_all_pages' => 'すべてのページから表示',
	'msg_page_select' => 'ページを選択',
	'msg_send_mms' => 'MMS でメール送信',
	'msg_drop_files_here' => 'アップロードするには、ここにファイルをドロップ',
	'msg_for_upload' => 'このページにアップロードする権限がありません。<br />アップロードするには、<img src="'.$const['LOADER_URL'].'?src=page_attach.png" alt="Page" />ページ選択で "<span class="attachable">このような表示</span>" のページを選択して下さい。',
);

///////////////////////////////////////
// back.inc.php
$root->_msg_back_word = '戻る';

///////////////////////////////////////
// backup.inc.php
$root->_title_backup_delete  = '$1 のバックアップを削除';
$root->_title_backupdiff     = '$1 のバックアップ差分(No.$2)';
$root->_title_backupnowdiff  = '$1 のバックアップの現在との差分(No.$2)';
$root->_title_backupsource   = '$1 のバックアップソース(No.$2)';
$root->_title_backup         = '$1 のバックアップ(No.$2)';
$root->_title_pagebackuplist = '$1 のバックアップ一覧';
$root->_title_backuplist     = 'バックアップ一覧';
$root->_msg_backup_deleted   = '$1 のバックアップを削除しました。';
$root->_msg_backup_adminpass = '削除用のパスワードを入力してください。';
$root->_msg_backuplist       = 'バックアップ一覧';
$root->_msg_nobackup         = '$1 のバックアップはありません。';
$root->_msg_diff             = '差分';
$root->_msg_nowdiff          = '現在との差分';
$root->_msg_source           = 'ソース';
$root->_msg_backup           = 'バックアップ';
$root->_msg_view             = '$1 を表示';
$root->_msg_deleted          = '$1 は削除されています。';
$root->_msg_backupedit       = 'バックアップ No.$1 を復元して編集';
$root->_msg_current          = '現';
$root->_title_backuprewind   = '$1 のバックアップ(No.$2)へ巻き戻し';
$root->_title_dorewind       = '$1 時点の以下の内容にタイムスタンプも含めて復元します。';
$root->_msg_rewind           = '巻き戻し';
$root->_msg_dorewind         = 'バックアップ No.$1 へ巻き戻す';
$root->_msg_rewinded         = 'バックアップ No.$1 への巻き戻しが完了しました。';
$root->_msg_nobackupnum      = 'バックアップ No.$1 はありません。';

///////////////////////////////////////
// calendar_viewer.inc.php
$root->_err_calendar_viewer_param2 = '第2引数が変だよ';
$root->_msg_calendar_viewer_right  = '次の%d件&gt;&gt;';
$root->_msg_calendar_viewer_left   = '&lt;&lt;前の%d件';
$root->_msg_calendar_viewer_restrict = '$1 は閲覧制限がかかっているためcalendar_viewerによる参照はできません';

///////////////////////////////////////
// calendar2.inc.php
$root->_calendar2_plugin_edit  = '[この日記を編集]';
$root->_calendar2_plugin_empty = '%sは空です。';

///////////////////////////////////////
// comment.inc.php
$root->_btn_name    = 'お名前: ';
$root->_btn_comment = 'コメントの挿入';
$root->_msg_comment = 'コメント: ';
$root->_title_comment_collided = '$1 で【更新の衝突】が起きました';
$root->_msg_comment_collided   = 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
コメントを追加しましたが、違う位置に挿入されているかもしれません。<br />';

///////////////////////////////////////
// deleted.inc.php
$root->_deleted_plugin_title = '削除ページの一覧';
$root->_deleted_plugin_title_withfilename = '削除ページファイルの一覧';

///////////////////////////////////////
// diff.inc.php
$root->_title_diff = '$1 の変更点';
$root->_title_diff_delete  = '$1 の差分を削除';
$root->_msg_diff_deleted   = '$1 の差分を削除しました。';
$root->_msg_diff_adminpass = '削除用のパスワードを入力してください。';

///////////////////////////////////////
// filelist.inc.php (list.inc.php)
$root->_title_filelist = 'ページファイルの一覧';

///////////////////////////////////////
// freeze.inc.php
$root->_title_isfreezed = '$1 はすでに凍結されています';
$root->_title_freezed   = '$1 を凍結しました';
$root->_title_freeze    = '$1 の凍結';
$root->_msg_freezing    = '凍結用のパスワードを入力してください。';
$root->_btn_freeze      = '凍結';

///////////////////////////////////////
// insert.inc.php
$root->_btn_insert = '追加';

///////////////////////////////////////
// include.inc.php
$root->_msg_include_restrict = '$1 は閲覧制限がかかっているためincludeできません';

///////////////////////////////////////
// interwiki.inc.php
$root->_title_invalidiwn = '有効なInterWikiNameではありません';

///////////////////////////////////////
// list.inc.php
$root->_title_list = 'ページの一覧';

///////////////////////////////////////
// ls2.inc.php
$root->_ls2_err_nopages = '<p>\'$1\' には、下位層のページがありません。</p>';
$root->_ls2_msg_title   = '\'$1\'で始まるページの一覧';

///////////////////////////////////////
// memo.inc.php
$root->_btn_memo_update = 'メモ更新';

///////////////////////////////////////
// navi.inc.php
$root->_navi_prev = 'Prev';
$root->_navi_next = 'Next';
$root->_navi_up   = 'Up';
$root->_navi_home = 'Home';

///////////////////////////////////////
// newpage.inc.php
$root->_msg_newpage = 'ページ新規作成';

///////////////////////////////////////
// paint.inc.php
$root->_paint_messages = array(
	'field_name'    => 'お名前',
	'field_filename'=> 'ファイル名',
	'field_comment' => 'コメント',
	'btn_submit'    => 'paint',
	'msg_max'       => '(最大 %d x %d)',
	'msg_title'     => 'Paint and Attach to $1',
	'msg_title_collided' => '$1 で【更新の衝突】が起きました',
	'msg_collided'  => 'あなたが画像を編集している間に、他の人が同じページを更新してしまったようです。<br />
画像とコメントを追加しましたが、違う位置に挿入されているかもしれません。<br />'
);

///////////////////////////////////////
// pcomment.inc.php
$root->_pcmt_messages = array(
	'btn_name'     => 'お名前: ',
	'btn_comment'  => 'コメントの挿入',
	'msg_comment'  => 'コメント: ',
	'msg_recent'   => '最新の%d件を表示しています。',
	'msg_all'      => 'コメントページを参照',
	'msg_none'     => 'コメントはありません。',
	'title_collided' => '$1 で【更新の衝突】が起きました',
	'msg_collided' => 'あなたがこのページを編集している間に、他の人が同じページを更新してしまったようです。<br />
コメントを追加しましたが、違う位置に挿入されているかもしれません。<br />',
	'err_pagename' => 'ページ名 [[%s]] は使用できません。 正しいページ名を指定してください。',
);
$root->_msg_pcomment_restrict = '閲覧制限がかかっているため、$1からはコメントを読みこむことができません。';

///////////////////////////////////////
// popular.inc.php
$root->_popular_plugin_frame       = '<h5>%3$s人気の%1$d件</h5><div>%2$s</div>';
$root->_popular_plugin_today_frame = '<h5>%3$s今日の%1$d件</h5><div>%2$s</div>';
$root->_popular_plugin_yesterday_frame = '<h5>%3$s昨日の%1$d件</h5><div>%2$s</div>';

///////////////////////////////////////
// recent.inc.php
$root->_recent_plugin_frame = '<h5>%s最新の%d件</h5>
<div>%s</div>';

///////////////////////////////////////
// referer.inc.php
$root->_referer_msg = array(
	'msg_H0_Refer'       => 'リンク元の表示',
	'msg_Hed_LastUpdate' => '最終更新日時',
	'msg_Hed_1stDate'    => '初回登録日時',
	'msg_Hed_RefCounter' => 'カウンタ',
	'msg_Hed_Referer'    => 'Referer',
	'msg_Fmt_Date'       => 'Y年n月j日 H:i',
	'msg_Chr_uarr'       => '↑',
	'msg_Chr_darr'       => '↓',
);

///////////////////////////////////////
// rename.inc.php
$root->_rename_messages  = array(
	'err' => '<p>エラー:%s</p>',
	'err_nomatch'    => 'マッチするページがありません。',
	'err_notvalid'   => 'リネーム後のページ名が正しくありません。',
	'err_adminpass'  => '管理者パスワードが正しくありません。',
	'err_notpage'    => '%sはページ名ではありません。',
	'err_norename'   => '%sをリネームすることはできません。',
	'err_already'    => '以下のページがすでに存在します。%s',
	'err_already_below' => '以下のファイルがすでに存在します。',
	'msg_title'      => 'ページ名の変更',
	'msg_page'       => '変更元ページを指定',
	'msg_regex'      => '正規表現',
	'msg_part_rep'   => '部分一致置換',
	'msg_related'    => '関連ページ',
	'msg_do_related' => '関連ページもリネームする',
	'msg_rename'     => '%sの名前を変更します。',
	'msg_oldname'    => '現在の名前',
	'msg_newname'    => '新しい名前',
	'msg_adminpass'  => '管理者パスワード',
	'msg_arrow'      => '→',
	'msg_exist_none' => 'そのページを処理しない',
	'msg_exist_overwrite' => 'そのファイルを上書きする',
	'msg_confirm'    => '以下のファイルをリネームします。',
	'msg_result'     => '以下のファイルを上書きしました。',
	'btn_submit'     => '実行',
	'btn_next'       => '次へ'
);

///////////////////////////////////////
// search.inc.php
$root->_title_search  = '単語検索';
$root->_title_result  = '$1 の検索結果';
$root->_msg_searching = '全てのページから単語を検索します。大文字小文字の区別はありません。';
$root->_btn_search    = '検索';
$root->_btn_and       = 'AND検索';
$root->_btn_or        = 'OR検索';
$root->_search_pages  = '$1 から始まるページを検索';
$root->_search_all    = '全てのページを検索';

///////////////////////////////////////
// source.inc.php
$root->_source_messages = array(
	'msg_title'    => '$1のソース',
	'msg_notfound' => '$1が見つかりません',
	'err_notfound' => 'ページのソースを表示できません。'
);

///////////////////////////////////////
// template.inc.php
$root->_msg_template_start   = '開始行:<br />';
$root->_msg_template_end     = '終了行:<br />';
$root->_msg_template_page    = '$1/複製';
$root->_msg_template_refer   = 'ページ名:';
$root->_msg_template_force   = '既存のページ名で編集する';
$root->_err_template_already = '$1 はすでに存在します。';
$root->_err_template_invalid = '$1 は有効なページ名ではありません。';
$root->_btn_template_create  = '作成';
$root->_title_template       = '$1 をテンプレートにして作成';

///////////////////////////////////////
// tracker.inc.php
$root->_tracker_messages = array(
	'msg_list'   => '$1 の項目一覧',
	'msg_back'   => '<p>$1</p>',
	'msg_limit'  => '全$1件中、上位$2件を表示しています。',
	'btn_page'   => 'ページ名',
	'btn_name'   => 'ページ名',
	'btn_real'   => 'ページ名',
	'btn_submit' => '追加',
	'btn_date'   => '日付',
	'btn_refer'  => '参照',
	'btn_base'   => '基底',
	'btn_update' => '更新日時',
	'btn_past'   => '経過',
);

///////////////////////////////////////
// unfreeze.inc.php
$root->_title_isunfreezed = '$1 は凍結されていません';
$root->_title_unfreezed   = '$1 の凍結を解除しました';
$root->_title_unfreeze    = '$1 の凍結解除';
$root->_msg_unfreezing    = '凍結解除用のパスワードを入力してください。';
$root->_btn_unfreeze      = '凍結解除';

///////////////////////////////////////
// versionlist.inc.php
$root->_title_versionlist = '構成ファイルのバージョン一覧';

///////////////////////////////////////
// vote.inc.php
$root->_vote_plugin_choice = '選択肢';
$root->_vote_plugin_votes  = '投票';

///////////////////////////////////////
// yetlist.inc.php
$root->_title_yetlist = '未作成のページ一覧';
$root->_err_notexist  = '未作成のページはありません。';
