<?php
/*
 * Created on 2008/01/24 by nao-pon http://hypweb.net/
 * $Id: conf.lng.php,v 1.19 2012/01/14 11:56:35 nao-pon Exp $
 */

$msg = array(
	'title_form' => 'xpWiki 環境設定',
	'title_done' => 'xpWiki 環境設定完了',
	'btn_submit' => 'この設定を適用する',
	'msg_done' => '以下の内容で、$cache_file に保存しました。',
	'title_description' => '環境設定の説明',
	'msg_description' => '<p>この環境設定では、pukiwiki.ini.php の設定項目で、代表的な項目のみを設定することができます。</p>'
	                   . '<p>$trust_ini_file には、このほかにも数多くの設定項目があります。</p>'
	                   . '<p>この設定画面にない設定項目を変更したい場合は、$html_ini_file に、該当の項目を抜き出して設定をしてください。</p>'
	                   . '<p>※ この設定画面の設定内容が最優先で適用されます。</p>',

	'Yes' => 'はい',
	'No' => 'いいえ',

	'PKWK_READONLY' => array(
		'caption'     => '読み取り専用にする',
		'description' => '読み取り専用にすると、管理者も含め誰も編集することができません。',
	),

	'function_freeze' => array(
		'caption'     => '凍結機能を有効にする',
		'description' => '',
	),

	'adminpass' => array(
		'caption'     => '管理者パスワード',
		'description' => 'クリアテキストでも指定できますが、<a href="?cmd=md5" target="_blank">cmd=md5</a> を使い暗号化した文字列を入力してください。<br />'
		               . 'XOOPS環境下では、管理者としてログインすれば、管理者パスワードは必要ないため"{x-php-md5}!"としてすべて認証不可としても問題ありません。',
	),

	'html_head_title' => array(
		'caption'     => '&lt;head&gt;内の&lt;title&gt;設定',
		'description' => 'HTML の &lt;head&gt; 内 &lt;title&gt;タグに表示する内容を設定します。<br />'
		               . '<b>$page_title</b>: ページ名, <b>$content_title</b>: ページタイトル, <b>$module_title</b>:モジュール名 に置換されます。',
	),

	'modifier' => array(
		'caption'     => '管理者名',
		'description' => '',
	),

	'modifierlink' => array(
		'caption'     => '管理者のサイトURL',
		'description' => '',
	),

	'notify' => array(
		'caption'     => 'ページ更新時メール通知する',
		'description' => 'ページか更新されたら、管理者にメール通知します。',
	),

	'notify_diff_only' => array(
		'caption'     => 'メール通知は差分のみ',
		'description' => 'ページ更新時のメール通知は、変更差分のみ送信します。「いいえ」を選択すると全文送信されます。',
	),

	'defaultpage' => array(
		'caption'     => 'デフォルトページ',
		'description' => 'ページを指定しない場合に表示されるページ、トップページです。',
	),

	'page_case_insensitive' => array(
		'caption'     => 'ページ名の小文字・大文字を区別しない',
		'description' => 'ページ名の内、英字(アルファベット)の大文字・小文字を区別しません。',
	),

	'SKIN_NAME' => array(
		'caption'     => 'デフォルトのスキン名',
		'description' => 'デフォルトのスキン名を指定します。',
		'normalskin'  => '通常のスキン',
		'tdiarytheme' => 't-Diaryのテーマ',
	),

	'skin_navigator_cmds' => array(
		'caption'     => 'スキンで表示するメニュー',
		'description' => 'スキンで表示可とするメニューのコマンド名をカンマ(,)区切りで入力します。<br />'
	                   . 'すべてのメニューを表示可とする場合は all と入力します。<br />'
	                   . '" add, atom, attaches, back, backup, copy, diff, edit, filelist, freeze, help, list, new, newsub, pginfo, print, rdf, recent, refer, related, reload, rename, rss, rss10, rss20, search, top, topage, trackback, unfreeze, upload " が指定できますが、表示されるか否かはスキンにより左右されます。' ,
	),

	'skin_navigator_disabled' => array(
		'caption'     => 'スキンで表示しないメニュー',
		'description' => 'スキンで表示不可とするメニューのコマンド名をカンマ(,)区切りで入力します。<br />'
	                   . '指定可能なコマンドは「スキンで表示するメニュー」と同じです。' ,
	),

	'SKIN_CHANGER' => array(
		'caption'     => 'スキンの変更を許可する',
		'description' => '「はい」を選択するとユーザーがスキンを選択できるようになります。<br />'
		               . 'また、tdiary プラグインなどを使いページ毎で指定することも可能になります。',
	),

	'referer' => array(
		'caption'     => '参照元を集計する',
		'description' => '閲覧者がどこからページに訪れたかをページ毎に集計する機能です。',
	),

	'allow_pagecomment' => array(
		'caption'     => 'ページコメント機能を有効にする',
		'description' => 'd3forum モジュールのコメント統合を使いページ毎にコメント機能を持たせることができます。<br />'
		               . '実際に使用するには、一般設定でコメント統合の設定をする必要があります。',
	),

	'use_root_image_manager' => array(
		'caption'     => 'イメージマネージャーを使用する',
		'description' => 'サイト標準のイメージマネージャーを利用し画像を挿入できるようにします。',
	),

	'use_title_make_search' => array(
		'caption'     => 'ページタイトルを使用する',
		'description' => 'コンテンツのタイトル部分の表示をページ名からページタイトルに変更します。',
	),

	'nowikiname' => array(
		'caption'     => 'WikiName を無効にする',
		'description' => 'WikiName への自動リンク機能を無効にします。',
	),

	'relative_path_bracketname' => array(
		'caption'     => 'ブラケットネームの相対パス',
		'description' => 'ブラケットネームにて相対パスでページ名を指定した場合の相対パス部分の表示方法を設定します。',
		'remove'      => '取り除く',
		'full'        => 'フルパスに変換',
		'as is'       => 'そのまま',
	),

	'pagename_num2str' => array(
		'caption'     => 'ページ名の具体表示をする',
		'description' => '二階層以上のページ名で最終階層部分が、数字と-(ハイフン)で構成されている場合にその部分をページタイトルに置換して表示します。',
	),

	'pagelink_topicpath' => array(
		'caption'     => 'ページリンクをパンくずリストにする',
		'description' => 'オートリンク、ブラケットリンクを除くページリンク(#recent, #ls2 など)をパンくずリスト(Topic path)方式で表示します。',
	),

	'static_url' => array(
		'caption'     => 'ページURLの形式',
		'description' => 'ページURLの形式を選択します。"?[PAGE]" 以外を選択すると静的なページのURLのように振舞います。<br />'
		               . 'ただし、選択肢によっては .htaccess にて以下の記述を有効にする必要があります。<br />'
		               . '<dl><dt>[ID].html</dt><dd><code>RewriteEngine on<br />RewriteRule ^([0-9]+)\.html$ index.php?pgid=$1 [qsappend,L]</code></dd></dl>'
		               . '<dl><dt>{$root->path_info_script}/[PAGE]</dt><dd><code>Options +MultiViews<br />&lt;FilesMatch "^{$root->path_info_script}$"&gt;<br />ForceType application/x-httpd-php<br />&lt;/FilesMatch&gt;</code></dd></dl>',
	),

	'url_encode_utf8' => array(
		'caption'     => 'ページURLを UTF-8 にする',
		'description' => '上記 "ページURLの形式" の "[PAGE]" 部分を "UTF-8" でエンコードします。<br />'
		               . 'ただし、xpWiki の文字エンコーディングが UTF-8 の場合は、常に "UTF-8" となります。',
	),

	'link_target' => array(
		'caption'     => '外部リンクの target 属性',
		'description' => '',
	),

	'class_extlink' => array(
		'caption'     => '外部リンクの class 属性',
		'description' => '',
	),

	'nofollow_extlink' => array(
		'caption'     => '外部リンクに nofollow 属性をつける',
		'description' => '',
	),

	'LC_CTYPE' => array(
		'caption'     => 'ロケール(LC_CTYPE)',
		'description' => '文字の分類と変換用のロケールを設定します。オートリンクなど正規表現で処理する場合に期待した結果にならない場合は、環境に合わせて設定してください。',
	),

	'autolink' => array(
		'caption'     => 'オートリンク有効ページ名バイト数',
		'description' => 'オートリンクとは、存在するページ名に自動的にリンクをする機能です。<br />'
		               . '有効になるページバイト数を入力します。(0 で無効)<br />'
		               . '文字数ではなくバイト数指定となりますので、ご注意ください。',
		'extention'   => 'バイト',
	),

	'autolink_omissible_upper' => array(
		'caption'     => '上階層を省略したオートリンク',
		'description' => '上階層を省略してもオートリンクする設定です。オートリンクが有効になっている必要があります。<br />'
		               . '/hoge/hoge というページで fuga と書くことで /hoge/fuga にオートリンクします。<br />'
		               . 'オートリンクと同様、バイト数指定となります。(fuga に相当するバイト数で指定)',
		'extention'   => 'バイト',
	),

	'autoalias' => array(
		'caption'     => 'オートエイリアス有効バイト数',
		'description' => '「指定した単語」に対し、指定した「URI、ページ、またはInterWiki」に対するリンクを自動的に張る機能です。<br />'
		               . 'オートリンクと同様、バイト数指定となります。(置換される文字列のバイト数で指定。0 で無効)<br />'
		               . '設定ページ: <a href="?'.rawurlencode($this->root->aliaspage).'" target="_blank">'.$this->root->aliaspage.'</a>',
		'extention'   => 'バイト',
	),

	'autoalias_max_words' => array(
		'caption'     => 'オートエイリアスの最大単語登録数',
		'description' => '',
		'extention'   => '組',
	),

	'plugin_follow_editauth' => array(
		'caption'     => 'プラグインにページ編集権限を適用する',
		'description' => 'ページ編集権限がない場合に、プラグインでの投稿を不許可にします。',
	),

	'plugin_follow_freeze' => array(
		'caption'     => 'プラグインにページ凍結を適用する',
		'description' => 'ページが凍結されている場合に、プラグインでの投稿を不許可にします。',
	),

	'line_break' => array(
		'caption'     => '改行を有効にする',
		'description' => '改行を &lt;br /&gt; に変換します。',
	),

	'fixed_heading_anchor_edit' => array(
		'caption'     => '章単位編集を有効にする',
		'description' => '',
	),

	'paraedit_partarea' => array(
		'caption'     => '章編集の範囲',
		'description' => '章編集の範囲を設定します。<br />'
		               . '章の範囲は、Wiki書式の * で始まる見出し行で開始されます。',
		'compat'      => '次の見出しまで',
		'level'       => '同レベル以上の見出しまで',
	),

	'contents_auto_insertion' => array(
		'caption'     => 'TOCの自動挿入',
		'description' => 'TOC("#contents")の自動挿入を行う章の数。( 0: 無効 )',
	),

	'amazon_AssociateTag' => array(
		'caption'     => 'Amazon AssociateTag',
		'description' => 'アソシエイトタグ(トラッキングID)<br />'
		               . 'この値が空値の場合 "trust/class/hyp_common/hsamazon/hyp_simple_amazon.ini" の設定値を用います。',
	),

	'amazon_AccessKeyId' => array(
		'caption'     => 'Amazon AccessKeyId',
		'description' => 'アクセスキーID（半角英数字で構成された20文字の文字列）<br />'
		               . 'この値が空値の場合 "trust/class/hyp_common/hsamazon/hyp_simple_amazon.ini" の設定値を用います。',
	),

	'amazon_SecretAccessKey' => array(
		'caption'     => 'Amazon SecretAccessKey',
		'description' => '秘密キー（40文字のシーケンス）<br />'
		               . 'この値が空値の場合 "trust/class/hyp_common/hsamazon/hyp_simple_amazon.ini" の設定値を用います。',
	),

	'amazon_UseUserPref' => array(
		'caption'     => 'ユーザー別 Amazon ID',
		'description' => 'ユーザー個別設定のアソシエイト ID を有効にする',
	),

	'bitly_clickable' => array(
		'caption'     => 'クリッカブルURL短縮',
		'description' => 'URLの自動リンクを <a href="http://bit.ly/" target="_blank">bitly</a> を使い短縮する。'
		               . '"bitly_login", "bitly_apiKey" の設定が必要です。'
	),

	'twitter_consumer_key' => array(
		'caption'     => 'Twitter Consumer key',
		'description' => '<a href="https://twitter.com/apps" target="_blank">Applications Using Twitter</a> で得られるカスタマーキー。'
	),

	'twitter_consumer_secret' => array(
		'caption'     => 'Twitter Consumer secret',
		'description' => '<a href="https://twitter.com/apps" target="_blank">Applications Using Twitter</a> で得られるシークレットキー。'
	),

	'fckeditor_path' => array(
		'caption'     => 'FCKeditor のパス',
		'description' => '<span style="font-weight:bold;">' . $this->cont['ROOT_PATH'] . '</span> からの続きを入力してください。<br />'
		               . 'fckeditor.js のあるディレクトリ名を設定してください。FCKeditor は、Version 2.6 以降が必要です。<br />'
		               . 'FCKeditor によるリッチエディタを使用しない場合は未入力としてください。',
	),

	'pagecache_min' => array(
		'caption'     => 'ページキャッシュ有効期限',
		'description' => 'ページレンダリングしたHTMLをキャッシュして高速化する場合の有効期限(単位:分)を設定します。<br />'
		               . 'ただし、ゲストアカウントアクセス時のみキャッシュされます。ページビューが多いサイトの場合は、有効にされることをお勧め致します。',
		'extention'   => '分',
	),

	'pre_width' => array(
		'caption'     => '&lt;pre&gt;のCSS:width指定',
		'description' => '&lt;pre&gt;タグに指定するCSSのwidth値を指定します。',
	),

	'pre_width_ie' => array(
		'caption'     => '&lt;pre&gt;のCSS:width指定(IE専用)',
		'description' => 'こちらはブラウザのIE専用値です。使用しているXOOPSのテーマが&lt;table&gt;構成の場合は、700px など固定値を指定すると表示の崩れが軽減されると思います。',
	),

	'moblog_pop_mail' => array(
		'caption'     => 'モブログ送信先メールアドレス',
		'description' => 'Gmail を利用してユーザー毎に専用アドレスを付与する場合は、"アカウント名+*@gmail.com" と設定します。(* にランダムな文字列が挿入されます)',
	),

	'moblog_pop_host' => array(
		'caption'     => 'モブログで利用するPOP3サーバー名',
		'description' => 'Gmail の場合、「ssl://pop.gmail.com」と設定します。<br />ただし、サーバーの PHP に OpenSSL が組み込まれていない場合は、ssl:// は使用できません。',
	),

	'moblog_pop_port' => array(
		'caption'     => 'モブログで利用するPOP3ポート番号',
		'description' => '通常は「110」、Gmail の場合は「995」と設定します。',
	),

	'moblog_pop_user' => array(
		'caption'     => 'モブログで利用するPOP3ログインID',
		'description' => 'Gmail の場合、最新モードに設定するため「recent:アカウント名@gmail.com」と設定します。',
	),

	'moblog_pop_pass' => array(
		'caption'     => 'モブログで利用するPOP3ログインパスワード',
		'description' => '',
	),

	'use_moblog_user_pref' => array(
		'caption'     => 'ユーザー設定でモブログの設定を許可する',
		'description' => '',
	),

	'moblog_page_recomend' => array(
		'caption'     => 'ユーザー設定のページ名設定ヒント',
		'description' => 'ユーザー設定での投稿先ページ名に対する説明(設定例などを記入する)',
	),

	'update_ping' => array(
		'caption'     => '更新Pingを送信する',
		'description' => '',
	),

	'update_ping_servers' => array(
		'caption'     => '更新Pingの送信先',
		'description' => '送信先サーバーURLを 1 行に 1 件、http から記述します。<br />URLの最後に、[半角スペース]で区切った"E"を入れた場合、extendedPingで送信します。',
	),

	'pagereading_enable' => array(
		'caption'     => 'ページ名読みで分類する',
		'description' => 'ページ一覧でページ名読みを利用して分類します。',
	),

	'pagereading_kanji2kana_converter' => array(
		'caption'     => 'ページ名読み取得方法',
		'description' => 'ページ名読みを取得する方法を選択してください。',
	),

	'pagereading_kanji2kana_encoding' => array(
		'caption'     => 'ページ名読み文字処理エンコーディング',
		'description' => 'サーバーが UNIX 系なら EUC-JP, Windows 系なら Shift-JIS が標準です。',
	),

	'pagereading_chasen_path' => array(
		'caption'     => 'ChaSen の絶対パス',
		'description' => '',
	),

	'pagereading_kakasi_path' => array(
		'caption'     => 'KAKASI の絶対パス',
		'description' => '',
	),

	'pagereading_config_dict' => array(
		'caption'     => 'ページ名読みの辞書ページ名',
		'description' => 'ページ名読み取得方法が"None"の場合使用されます。',
	),

);
?>