<?php
//%%%%%%	Admin Module Name  MailUsers	%%%%%
define("_AM_DBUPDATED",_MD_AM_DBUPDATED);

//%%%%%%	mailusers.php 	%%%%%
define("_AM_SENDTOUSERS","送信先ユーザの選択：");
define("_AM_SENDTOUSERS2","送信先:");
define("_AM_GROUPIS","グループ（省略可）");
define("_AM_TIMEFORMAT", "（yyyy-mm-dd形式で記入　省略可）");
define("_AM_LASTLOGMIN","最終ログイン日時が下記の日時よりも後");
define("_AM_LASTLOGMAX","最終ログイン日時が下記の日時よりも前");
define("_AM_REGDMIN","登録日時が下記の日時よりも後");
define("_AM_REGDMAX","登録日時が下記の日時よりも前");
define("_AM_IDLEMORE","最終ログイン日時がX日前以上（省略可）");
define("_AM_IDLELESS","最終ログイン日時がX日前以内（省略可）");
define("_AM_MAILOK","当サイトからのメール配信を希望しているユーザのみに送信する（省略可）");
define("_AM_INACTIVE","非アクティブユーザ宛にのみ送信（省略可）");
define("_AMIFCHECKD", "チェックした場合、上の設定は無視されます。また、プライベートメッセージの送信は行われません。");
define("_AM_MAILFNAME","送信者（メール使用時）");
define("_AM_MAILFMAIL","送信者メールアドレス（メール使用時）");
define("_AM_MAILSUBJECT","表題");
define("_AM_MAILBODY","メッセージ本文");
define("_AM_MAILTAGS","使用可能なタグ：");
define("_AM_MAILTAGS1","{X_UID} はユーザIDを表示します");
define("_AM_MAILTAGS2","{X_UNAME} はユーザ名を表示します");
define("_AM_MAILTAGS3","{X_UEMAIL} はユーザのメールアドレスを表示します");
define("_AM_MAILTAGS4","{X_UACTLINK} は登録を承認するためのページへのリンクを表示します");
define("_AM_SENDTO","送信方法");
define("_AM_EMAIL","メール");
define("_AM_PM","プライベートメッセージ");
define("_AM_SENDMTOUSERS", "メッセージの送信");
define("_AM_SENT", "送信済ユーザ");
define("_AM_SENTNUM", "%s - %s （宛先ユーザ数合計： %s 人）");
define("_AM_SENDNEXT", "続ける");
define("_AM_NOUSERMATCH", "条件に合うユーザは見つかりませんでした");
define("_AM_SENDCOMP", "メッセージの送信を完了しました");
?>