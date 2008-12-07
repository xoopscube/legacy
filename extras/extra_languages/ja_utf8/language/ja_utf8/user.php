<?php
//%%%%%%		File Name user.php 		%%%%%
define('_US_NOTREGISTERED','今すぐ<a href="register.php">登録</a>しませんか？');
define('_US_LOSTPASSWORD','パスワードを紛失されましたか？');
define('_US_NOPROBLEM','ご心配なく。まずはあなたが登録に使用したメールアドレスを入力し、ボタンをクリックしてください。 パスワード取得用のリンクが記載されたメールがあなたの登録メールアドレス宛に送られます。');
define('_US_YOUREMAIL','登録メールアドレス：');
define('_US_SENDPASSWORD','送信');
define('_US_LOGGEDOUT','ログアウトしました。');
define('_US_THANKYOUFORVISIT','当サイトをご利用いただきありがとうございました。');
define('_US_INCORRECTLOGIN','ログイン情報が間違っています。');
define('_US_LOGGINGU','%sさん、ようこそ。ログイン処理中です。');

// 2001-11-17 ADD
define('_US_NOACTTPADM','選択されたユーザはまだ存在しないか、承認が完了していません。<br />詳細についてはサイト管理者にお問合せください。');
define('_US_ACTKEYNOT','承認キーが間違っています。');
define('_US_ACONTACT','選択されたアカウントは既に承認が完了しています。');
define('_US_ACTLOGIN','アカウントを承認しました。登録の際に記入したパスワードを使用してログインしてください。');
define('_US_NOPERMISS','このユーザ情報を変更することはできません。');
define('_US_SURETODEL','ユーザアカウントを本当に削除しても良いですか？');
define('_US_REMOVEINFO','アカウントを削除した場合、全てのユーザ情報が失われます。');
define('_US_BEENDELED','アカウントを削除しました。');
//

//%%%%%%		File Name register.php 		%%%%%
define('_US_USERREG','ユーザ登録');
define('_US_NICKNAME','ユーザ名');
define('_US_EMAIL','メールアドレス');
define('_US_ALLOWVIEWEMAIL','このメールアドレスを公開する');
define('_US_WEBSITE','ホームページ');
define('_US_TIMEZONE','タイムゾーン');
define('_US_AVATAR','アバター');
define('_US_VERIFYPASS','パスワード確認');
define('_US_SUBMIT','送信');
define('_US_USERNAME','ユーザ名');
define('_US_FINISH','送信');
define('_US_REGISTERNG','登録できませんでした');
define('_US_MAILOK','当サイトの新着情報などを<br />メールで受け取る');

define('_US_INVALIDMAIL','不正なメールアドレスです。');
define('_US_EMAILNOSPACES','メールアドレスに空白を含めないでください。');
define('_US_INVALIDNICKNAME','不正なユーザ名です。');
define('_US_NICKNAMETOOLONG','ユーザ名が長すぎます。半角 %s 文字以内に収めてください。');
define('_US_NICKNAMETOOSHORT','ユーザ名が短すぎます。半角 %s 文字以上にしてください。');
define('_US_NAMERESERVED','このユーザ名は使用できません。');
define('_US_NICKNAMENOSPACES','ユーザ名に空白を含めないでください。');
define('_US_NICKNAMETAKEN','このユーザ名は既に使用されています。');
define('_US_EMAILTAKEN','このメールアドレスは既に使用されています。');
define('_US_ENTERPWD','パスワードを記入してください。');
define('_US_SORRYNOTFOUND','ユーザ情報が見つかりませんでした。');

define('_US_DISCLAIMER','免責');
define('_US_IAGREE','私は上記事項に同意します。');
define('_US_UNEEDAGREE', '申し訳ございませんが、登録するためには免責事項にご同意いただく必要があります。');
define('_US_NOREGISTER','申し訳ございませんが、現在このサイトでは新規ユーザの登録受付を行っておりません。');

// %s is username. This is a subject for email
define('_US_USERKEYFOR','%sさんの承認キーです');

define('_US_YOURREGISTERED','登録が完了しました。記載されたメールを登録メールアドレス宛に承認キーを送信しました。メールの指示に従い、承認を完了してください。');
define('_US_YOURREGMAILNG','登録が完了しました。しかし、サーバ内部エラーにより承認キーが記載されたメールを送信することができませんでした。大変申し訳ありませんが、サイト管理者までお問い合わせください。');
define('_US_YOURREGISTERED2','登録が完了しました。サイト管理者がアカウントを承認するまでお待ちください。承認完了時にはメールにてお知らせします。');

// %s is your site name
define('_US_NEWUSERREGAT','新規登録ユーザ＠%s');
// %s is a username
define('_US_HASJUSTREG','新規登録ユーザがありました！　ユーザ名：%s');

// %s is your site name
define('_US_NEWPWDREQ','新規パスワードのリクエスト＠%s');
define('_US_YOURACCOUNT', '%sでのユーザアカウント');

define('_US_MAILPWDNG','mail_password: ユーザ情報の更新に失敗しました。お手数ですが、サイト管理者までお問合せください。');

// %s is a username
define('_US_PWDMAILED','%sさん宛にパスワードを送信しました。');
define('_US_CONFMAIL','パスワード取得用リンクが記載されたメールを%sさん宛に送信しました。');
define('_US_ACTVMAILNG', '%sさんへのメール送信に失敗しました。');
define('_US_ACTVMAILOK', '%sさんへメールを送信しました。');

//%%%%%%		File Name userinfo.php 		%%%%%
define('_US_SELECTNG','ユーザが選択されていません');
define('_US_PM','PM');
define('_US_ICQ','ICQ');
define('_US_AIM','AIM');
define('_US_YIM','YIM');
define('_US_MSNM','Windows Live ID');
define('_US_LOCATION','居住地');
define('_US_OCCUPATION','職業');
define('_US_INTEREST','趣味');
define('_US_SIGNATURE','署名');
define('_US_EXTRAINFO','その他');
define('_US_EDITPROFILE','プロフィールの編集');
define('_US_LOGOUT','ログアウト');
define('_US_INBOX','受信箱');
define('_US_MEMBERSINCE','登録日');
define('_US_RANK','ランク');
define('_US_POSTS','投稿数');
define('_US_LASTLOGIN','最終ログイン日時');
define('_US_ALLABOUT','%sさんの基本情報');
define('_US_STATISTICS','統計情報');
define('_US_MYINFO','個人情報');//My Info');
define('_US_BASICINFO','基本情報');
define('_US_MOREABOUT','個人情報詳細');//More About Me');
define('_US_SHOWALL','すべて表示');


//%%%%%%		File Name edituser.php 		%%%%%
define('_US_PROFILE','プロフィール');
define('_US_REALNAME','本名');
define('_US_SHOWSIG','投稿に署名を必ず追加する');
define('_US_CDISPLAYMODE','コメント表示モード');
define('_US_CSORTORDER','コメントの並び順');
define('_US_PASSWORD','パスワード');
define('_US_TYPEPASSTWICE','（パスワードを変更する場合のみ記入してください）');
define('_US_SAVECHANGES','変更を保存');
define('_US_NOEDITRIGHT','このユーザ情報を変更する権限がありません。');
define('_US_PASSNOTSAME','パスワードが正しくありません。同じパスワードを二度入力してください。');
define('_US_PWDTOOSHORT','パスワードは半角<b>%s</b>文字以上にしてください。');
define('_US_PROFUPDATED','プロフィールを更新しました。');
define('_US_USECOOKIE','ユーザ名を１年間クッキーに保存する');
define('_US_NO','いいえ');
define('_US_DELACCOUNT','アカウントを削除する');
define('_US_MYAVATAR', 'アップロード済みアバター');
define('_US_UPLOADMYAVATAR', 'アバターをアップロードする');
define('_US_MAXPIXEL','最大ピクセル数');
define('_US_MAXIMGSZ','最大ファイルサイズ');
define('_US_SELFILE','ファイル選択');
define('_US_OLDDELETED','古いアバター画像は上書きされます。');
define('_US_CHOOSEAVT', 'アバターを一覧から選択してください。');
define('_US_PRESSLOGIN', '下記ボタンをクリックしてログインしてください。');
define('_US_ADMINNO', '管理者グループに属するユーザは削除できません');
define('_US_GROUPS', '所属グループ');
?>