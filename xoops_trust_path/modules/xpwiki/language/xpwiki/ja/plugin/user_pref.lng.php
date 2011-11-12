<?php
/*
 * Created on 2008/01/24 by nao-pon http://hypweb.net/
 * $Id: user_pref.lng.php,v 1.3 2010/06/23 08:02:52 nao-pon Exp $
 */

$msg = array(
	'title_form' => 'ユーザー設定',
	'title_done' => 'ユーザー設定完了',
	'btn_submit' => 'この設定を適用する',
	'msg_done' => '以下の内容で保存しました。',
	'title_description' => 'ユーザー設定の説明',
	'msg_description' => '<p>このユーザー設定では、ユーザー毎の設定することができます。</p>',

	'Yes' => 'はい',
	'No' => 'いいえ',

	'twitter_access_token' => array(
		'caption'     => 'Twitterアクセスキー',
		'description' => 'あなたのTwitterアカウントと連携するためのアクセスキー。<br />' .
				'連携を解除するには Twitter のサイトの連携アプリで連携解除をしてからこのページを再表示し「設定の適用ボタン」をクリックしてください。',
	),

	'twitter_access_token_secret' => array(
		'caption'     => 'Twitterアクセス秘密キー',
		'description' => '<a href="{$root->twitter_request_link}">アクセスキーを取得するにはここをクリックしてTwitterのサイトへ行き許可してください。</a>' .
				'許可後にこのページに戻ったら「設定の適用ボタン」をクリックしてください。',
	),

	'amazon_associate_tag' => array(
		'caption'     => 'Amazon アソシエイト ID',
		'description' => 'アソシエイト ID を登録すると、あなたが作成したページでのアマゾン系プラグインにこの ID が使用されます。',
	),

	'moblog_mail_address' => array(
		'caption'     => 'モブログメールアドレス',
		'description' => 'モブログ投稿に使用するあなたのメールアドレスを登録します。<br />モブログの送信先は「<a href="mailto:{$root->moblog_pop_mail}">{$root->moblog_pop_mail}</a>」です。',
	),

	'moblog_base_page' => array(
		'caption'     => 'モブログページ',
		'description' => 'モブログ投稿を保存するベースページ名を登録し、モブログ投稿を有効にします。<br />{$root->moblog_page_recomend}',
	),

	'moblog_user_mail' => array(
		'caption'     => 'モブログ送信先メールアドレス',
		'description' => '<img src="http://chart.apis.google.com/chart?chs=100x100&cht=qr&chl={$root->moblog_user_mail_rawurlenc}" width="100" height="100" alt="{$root->moblog_user_mail}" align="left" />あなた専用のモブログ送信先メールアドレスは「<a href="mailto:{$root->moblog_user_mail}">{$root->moblog_user_mail}</a>」です。<br />' .
				'このメールアドレス宛に送信された場合、送信元に関わらずあなたからの投稿として扱われますので、他人に知られることのないように十分に注意してください。<br />' .
				'このメールアドレスを変更したい場合は、「モブログページ」を一旦空にして登録すると、新しいメールアドレスになります。',
	),

	'moblog_auth_code' => array(
		'caption'     => 'モブログ認証コード[数値](任意)',
		'description' => 'モブログ認証コードを設定すると、メール題名の先頭に「*認証コード」がないメールは破棄されます。<br />' .
				'例: 認証コードを「1234」とした場合、メール題名を「*1234 投稿タイトル」とする。* と数値の間に空白を入れてはいけません。',
	),

	'moblog_to_twitter' => array(
		'caption'     => 'モブログ投稿を Twitter に通知する',
		'description' => 'ページ名、タイトルとブログへのリンクをあなたの Twitter アカウントでツイートします。',
	),

	'xmlrpc_pages' => array(
		'caption'     => 'XML-RPC のブログページ',
		'description' => 'MetaWeblog API をサポートする XML-RPC クライアントや対応サービスで利用するブログページを設定します。<br />' .
				'行区切りで複数指定することができます。<br />' .
				'XML-RPC API のエンドポイントは「<a href="{$root->script}{$root->xmlrpc_endpoint}" target="_blank"> {$root->script}{$root->xmlrpc_endpoint} </a>」になります。',
	),

	'xmlrpc_auth_key' => array(
		'caption'     => 'XML-RPC認証キー(パスワード)',
		'description' => 'XML-RPC クライアントや対応サービスに設定するパスワードです。<br />' .
				'任意の値に変更可能ですが、半角英数文字のみで設定してください。',
	),

	'xmlrpc_to_twitter' => array(
		'caption'     => 'XML-RPC投稿を Twitter に通知する',
		'description' => 'ページ名、タイトルとブログへのリンクをあなたの Twitter アカウントでツイートします。',
	),

);
?>