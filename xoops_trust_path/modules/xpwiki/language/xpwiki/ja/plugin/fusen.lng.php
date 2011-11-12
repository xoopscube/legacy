<?php
/*
 * Created on 2008/01/07 by nao-pon http://hypweb.net/
 * $Id: fusen.lng.php,v 1.2 2008/01/16 05:30:32 nao-pon Exp $
 */

$msg = array(
	'cap_refresh' => '自動更新',
	'cap_none' => 'なし',
	'cap_second' => '秒',
	'cap_dustbox_empty' => 'ごみ箱を空にする',
	'cap_empty' => '空に',
	'cap_fusen_menu' => '付箋メニュー',
	'cap_fusen_func' => '付箋機能',
	'cap_menu_new' => '新しい付箋を貼る',
	'btn_menu_new' => '新規',
	'cap_menu_dust' => 'ごみ箱を表示/非表示',
	'btn_menu_dust' => 'ごみ箱',
	'cap_menu_transparent' => '付箋をを透明化/非透明化',
	'btn_menu_transparent' => '透明',
	'cap_menu_refresh' => '最新の状態に更新',
	'btn_menu_refresh' => '更新',
	'cap_menu_list' => 'このページの付箋リスト',
	'btn_menu_list' => 'リスト',
	'cap_menu_help' => '使い方',
	'btn_menu_help' => 'ヘルプ',
	'cap_menu_search' => '付箋検索',
	'msg_not_work' => '<strong>JavaScript未動作</strong>: 付箋を編集できません。また、付箋の表示位置がずれている場合があります。',
	'msg_show_fusen' => '$1 の付箋を表示',
	'cap_fusen_edit' => '付箋の編集',
	'cap_fore_color' => '文字色',
	'cap_black' => '黒',
	'cap_gray' => '灰',
	'cap_red' => '赤',
	'cap_green' => '緑',
	'cap_blue' => '青',
	'cap_white' => '白',
	'cap_back_color' => '背景色',
	'cap_lightred' => '薄赤',
	'cap_lightgreen' => '薄緑',
	'cap_lightblue' => '薄青',
	'cap_lightyellow' => '薄黄',
	'cap_transparent' => '透明',
	'cap_name' => 'お名前',
	'cap_lineid' => '線接続id',
	'btn_write' => '書き込み',
	'btn_close' => '閉じる',

	'js_messages' => array(
		'now_communicating' => '只今サーバーと通信中です。',
		'fusen_func' => '付箋機能',
		'com_comp' => '通信完了',
		'refreshing' => '自動更新',
		'waiting' => '待機中',
		'stopping' => '停止中',
		'connecting' => 'サーバーに接続中...',
		'err_posting' => 'データの送信に失敗しました。 付箋機能の「更新」をクリックして状態を確認してください。',
		'communicating' => 'サーバーと通信中...',
		'err_notconnect' => 'サーバーに接続できませんでした。再試行しますか？',
		'err_baddata' => '無効なデータです。',
		'err_notcommunicating' => '付箋通信ができませんでした。',
		'msg_retryto' => '再試行しますか？ 接続先:',
		'err_nottext' => '内容を記入してください。',
		'msg_burn' => '完全削除しますか？',
		'msg_dustbox' => 'ゴミ箱へ入れますか？',
		'msg_dustall' => '選択した付箋をゴミ箱へ入れますか？',
		'msg_emptydustbox' => 'ゴミ箱を空にしますか？',
		'emptydustbox' => 'ゴミ箱を空にしました。',
		'recover' => 'ゴミ箱から戻す',
		'dustbox' => 'ごみ箱',
		'burn' => '完全削除',
		'unlock' => 'ロック解除',
		'new_with_line' => '線を繋げて新規作成',
		'edit' => '編集',
		'lock' => 'ロック',
		'to_dustbox' => 'ゴミ箱へ',
		'auto_resize' => 'サイズ自動調整',
		'owner' => '付箋オーナー',
		'lastedit_time' => '最終更新日時',
		'dbc2edit' => 'ダブルクリック->編集',
		'dbc2showall' => 'ダブルクリック->すべて表示',
		'fusen' => '付箋',
		'resizing' => 'をリサイズ中...',
		'moving' => 'を移動中...',
		'help_html' => '<ul>
<li>ダブルクリックで新しい付箋を作成できます。</li>
<li>書き込むと、付箋が表示されます。</li>
<li>付箋はドラッグして位置を移動できます。</li>
<li>"edit"を押す、または付箋をダブルクリックすると、その付箋を編集できます。<br />
※自分で作成した付箋のみ編集できます。</li>
<li>"lock"を押すと、編集・移動を禁止します。lockした付箋は"unlock"で元に戻せます。<br />
※自分で作成した付箋のみlockできます。</li>
<li>"del"を押すと、付箋をゴミ箱へ移動します。ゴミ箱の付箋は"recover"で元に戻せます。<br />
ゴミ箱の付箋で"del"を押すと、付箋を完全に削除します。<br />
※自分で作成した付箋のみdelできます。</li>
</ul>
<dl>
<dt>[新規]</dt>
<dd>新しい付箋の編集画面を表示します。</dd>
<dt>[ゴミ箱]</dt>
<dd>ゴミ箱に入れられた付箋を表示します。</dd>
<dt>[透明]</dt>
<dd>すべての付箋を半透明表示にします。</dd>
<dt>[更新]</dt>
<dd>付箋を最新の状態に更新します。</dd>
<dt>[リスト]</dt>
<dd>このページの付箋を一覧表示します。</dd>
<dt>[ヘルプ]</dt>
<dd>この説明書きを表示します。</dd>
<dt>検索</dt>
<dd>入力したキーワードを持つ付箋のみ表示します。</dd>
</dl>',
		'burn_checked' => 'チェックした付箋をごみ箱へ捨てる',
		'dust_checked' => 'チェックをごみ箱へ',
		'empty' => 'ごみ箱を空にする',
		'close' => '閉じる',
		'newtag' => '新しい付箋を貼る',
		'new' => '新規',
		'howto' => '使い方',
		'help' => 'ヘルプ',
		'notag' => 'このページに付箋はありません。',
	),
);
?>