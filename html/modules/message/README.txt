プライベートメッセージモジュールです。
Ver0.80よりPHP5専用になりました。

コア保有のPMの上位互換モジュールですので、PMモジュールはアンインストールしてください。
（どんな影響が出るか試してません。）


【モジュールの一般設定】
表示数：1ページに表示するリスト数（ユーザ毎に変更可能）
保存日数：メッセージを保存する日数（0で無期限）
          保存日数を超えた送受信メッセージを送信時に削除します。
          ＊保護された受信メッセージは削除されません。
新着ブロックの利用：マイフレンドモジュールの新着ブロックに新着PMを表示するかどうか
user_userinfo.htmlの差替え：user_userinfo.htmlを差替えるかどうか
未読メッセージの削除 ：保存期間の過ぎたメッセージを削除する際に未読メッセージを削除するかどうか


【ユーザ毎の設定】
プライベートメッセージを使う：プライベートメッセージを使いたくない場合は「いいえ」
                              これが「いいえ」になっているユーザにはメッセージを送れません。
メールに転送する：「はい」にするとメッセージ受信時にメール送信されます。
メールに本文を表示する。：送信されたメールに本文を含める場合は「はい」
1ページの表示数：1ページに表示するリスト数
受取拒否ユーザ：メッセージを受取りたくないユーザIDを,(カンマ)区切りで入力します。
                詳細リンクをクリックするとユーザ名で追加することが出来ます。


【メールのテンプレート】
送信されるメールの件名は言語ファイル（main.php）の_MD_MESSAGE_MAILSUBJECTになります。
本文は言語ファイルのディレクトリinvitation.tplになります。
本文を表示しない場合は言語ファイル（main.php）の_MD_MESSAGE_MAILBODYを利用します。{0}にはサイトのURLが入ります。
invitation.tplはSmartyを利用していて、アサインされている変数はsitename,uname,note,siteurlになります。
XCLのテンプレートと違い<{変数名}>ではなく{変数名}になります。


【テーマへアサインする】
<{message_newmessage}>をテーマの好きな位置（アサインした値を利用するより前の位置）に記述するだけで
new_messagesに未読数
open_message_alertに新着メッセージがある場合に1
がアサインされます。

アサインする変数名を変更したい場合は
<{message_newmessage name=未読数をアサインしたい変数名 open=アラートをアサインしたい変数名}>
にしてください。

ゲストの場合はnew_messagesにfalseがアサインされます。登録ユーザで未読がない場合は0がアサインされます。

また、Handlerの互換性を向上させたので従来のX2用のSmartyコードでもテーマへのアサインが可能です。
＊参考スレッド：http://www.xugj.org/modules/d3forum/index.php?post_id=5138


【モバイル用テンプレート】
モバイル テンプハウス様でモバイル用のテンプレートを配布してくれています
http://www.mc8.jp/HD/modules/xpwiki/45.html


templates
/message_new.htmlの26行目
<tr><td class="head"><{$smarty.const._MD_MESSAGE_TEMPLATE2}></td><td class="even"><{xoops_input name=uname size=30 maxlength=50 value=$mActionForm->get('uname')}></td></tr>
を
<tr><td class="head"><{$smarty.const._MD_MESSAGE_TEMPLATE2}></td><td class="even"><{message_userlist uname=$mActionForm->get('uname')}></td></tr>
に書き換えることでユーザ名を直接入力からリスト選択へ変更出来ます。
<tr><td class="head"><{$smarty.const._MD_MESSAGE_TEMPLATE2}></td><td class="even"><{message_suggestlist uname=$mActionForm->get('uname')}></td></tr>
に書き換えることでオートコンプリートのテキストボックスが利用出来ます。


【suggest.js】
配布元：Enjoy*Studyさん(http://www.enjoyxstudy.com/)
ライセンス：MITライセンス



ToDo:
同報発信

【更新履歴】
Ver 1.18:pmモジュールとの互換性の向上
Ver 1.17:削除されたユーザへの返信でエラーになっていたのを修正
Ver 1.16:newAction.class.phpの修正
         My_Mailerクラスのbodyをセットする際にテキストの改行コードを統一するように変更
         mb_encode_mimeheaderに改行コードをセット
Ver 1.15:My_MailerクラスをXoopsMailerとメソッド名を同じに変更（一部だけ）
         定数_USE_XOOPSMAILERを追加し、XoopsMailerを利用できるように変更
         suggest.jsのアップデート
Ver 1.14:smarty_function_message_userlistの修正
Ver 1.13:英語の言語ファイルに定数追加
Ver 1.12:送信箱で受信者、件名での絞り込みに対応
         受信箱の絞込み条件追加
         自動削除時に未読メッセージを削除するか設定追加
         テンプレート、英語の言語ファイル、画像を変更
         管理画面(admin.php)にアクセス時、自動削除するようにプリロード追加
Ver 1.11:他人のメッセージが読めてしまう問題修正
Ver 1.10:PostgreSQL対応の廃止
         フロントコントローラー動作の廃止
         phpmailerのパッチ削除
Ver 1.02:ユーザリスト用のSmartyプラグイン追加
         suggest.jsを利用したSmartyプラグイン追加
Ver 1.01:Handlerの互換性の向上
         テーマへ新着数をアサインするSmartyプラグイン追加
         セキュリティ向上
         使われていないファイルの削除
         .htaccessのサンプル追加
Ver 1.00:受信メッセージ保護追加
         受信箱の画像変更
         受信メッセージのメール送信追加
         お問い合わせからの(送信ユーザのない)メッセージに返信ボタンが出るバグ修正
Ver 0.90:モジュール一般設定に保存日数追加
         モジュール一般設定に新着ブロックの利用追加
         モジュール一般設定にuser_userinfo.htmlの差替え追加
         デフォルトのPMモジュールからのmigration機能追加
         インストール時のチェック機能追加
         メールテンプレートの修正
Ver 0.83:ブロックのテンプレート修正
         お気に入りに追加時のWarning修正
Ver 0.82:ブロックファイル名のTYPO修正
Ver 0.81:メール転送設定のバグ修正
Ver 0.80:PHP5専用に変更
         フロントコントローラ動作時にお気に入りユーザの登録が出来なかったのを修正
         テンプレートの修正
         handlerクラスをhandlerディレクトリに移動
         PostgreSQL対応
Ver 0.76:ブラックリスト追加時のバグ修正
Ver 0.75:一覧での削除時の戻り先修正
Ver 0.74:一覧での削除時のエラー処理修正
Ver 0.73:ブラックリスト追加時のバグ修正
Ver 0.72:ブロックがPHP4で表示されなくなったのを修正
Ver 0.71:ブラックリストへ追加するuidが自分になっていたのを修正
         フロントコントローラーのプリロードに対応
Ver 0.70:message_userinfo.htmlのテンプレート追加し、userinfoのテンプレートを差し替え
         message_inboxにunameのフィールド追加し、ゲストからのPMを受取れるように変更（管理者向けで画面等は作成していません。）
         ブラックリストの設定画面追加
Ver 0.63:message_new.htmlで多重エスケープされていたのを修正
         一部の環境でページナビが機能しなかったのを修正
Ver 0.62:0.61で直せてなかったのでさらに修正
         メニューブロックに受信箱と送信箱の件数表示
Ver 0.61:受信箱での送信者の絞り込みを修正
Ver 0.60:本文をメール転送するかしないかの設定追加
         転送メールのテンプレート化
         ユーザ毎で1ページの表示数を設定出来るように設定追加
         受信箱で送信者、件名での絞り込みに対応
         ブラックリスト対応
Ver 0.53:requireをrequire_onceに変更
Ver 0.52:新着ブロックをPMメニューブロックに変更
         チェックボックスでまとめて削除に対応
Ver 0.51:PMの新着ブロック追加
Ver 0.50:ユーザがPMを使用するか選択出来るように機能追加
         PMを受け取ったときにメール転送追加（手抜き）
Ver 0.40:削除の出来ないバグ修正
         イベント通知、メールジョブ対応
Ver 0.31:テンプレートに日本語が書かれていたのを修正
Ver 0.30:お気に入りユーザ実装
         一部コードの書き直し
Ver 0.21:送信箱のページナビ修正
Ver 0.20:usersearchモジュールに対応
         ページナビに対応
