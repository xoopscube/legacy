<?php
// $Id: install.php,v 1.3 2008/03/15 06:20:01 gusagi Exp $
define("_INSTALL_L0","XOOPS Cube 2.2 インストールウィザードへようこそ");
define("_INSTALL_L168","XOOPS Cube Legacy の動作には PHP5 以降が必要です");
define("_INSTALL_L70","サーバ上のmainfile.php への書き込み権限を与えてください。<br />（例：UNIX/LINUXサーバの場合はchmod 777 mainfile.php、Windowsサーバの場合は読み取り専用プロパティがセットされていないかチェックする。）<br />権限の設定完了後、ブラウザの「更新」ボタンを押してこのページを再度読み込んでください。");
//define("_INSTALL_L71","下記のボタンをクリックするとインストールを開始します。");
define("_INSTALL_L1","mainfile.phpを開き、31行目に以下のコードがあることを確認してください。");
define("_INSTALL_L2","この行を以下のように変更してください。");
define("_INSTALL_L3","次に、35行目の %s を %s へと変更してください。");
define("_INSTALL_L4","インストールを続ける");
define("_INSTALL_L5","注意");
define("_INSTALL_L6","mainfile.phpに設定してあるXOOPS_ROOT_PATHと、インストールウィザードが検知したパス設定が異なっています。");
define("_INSTALL_L7","mainfile.phpの設定：");
define("_INSTALL_L8","ウィザードが検知した設定：");
define("_INSTALL_L9","（Windows環境下では設定が正しくても注意のメッセージが表示されることがあります。設定に間違いが無い場合は、下記のボタンをクリックしてインストールを続けてください。）");
define("_INSTALL_L10","設定が間違いが無ければ、下記のボタンをクリックしてインストールを続けてください。");
define("_INSTALL_L11","XOOPS Cubeのディレクトリへのパス：");
define("_INSTALL_L12","XOOPS CubeへのURL：");
define("_INSTALL_L13","上記設定が正しい場合は、インストールを続けてください。<br />間違っている場合は、はじめからやり直してください。<br />または、mainfile.phpを直接編集して、このページを再読み込みしてください。");
define("_INSTALL_L14","次へ");
define("_INSTALL_L15","mainfile.phpを開き、必要な設定をすべて記入してください");
define("_INSTALL_L16","%s はデータベースサーバのホスト名です。");
define("_INSTALL_L17","%s はデータベースサーバにおけるユーザアカウント名です。");
define("_INSTALL_L18","%s はデータベースにアクセスするために必要なパスワードです。");
define("_INSTALL_L19","%s はXOOPS Cubeが使用するデータベースの名前です。");
define("_INSTALL_L20","%s はXOOPS Cubeが使用する各データベーステーブルに付加されるprefix（接頭語）です。prefixを付加することで、既存テーブルとのテーブル名称の重複を防ぎます。");
define("_INSTALL_L21","下記のデータベースが見つかりませんでした：");
define("_INSTALL_L22","このデータベースの作成を試みる場合は、インストールを続けてください。<br />データベース名が間違っている場合は、はじめからやり直してください。<br />設定されたユーザアカウントでは、このデータベースが作成できない場合は、別途作成し、このページを再読み込みしてください。");
define("_INSTALL_L23","はい");
define("_INSTALL_L24","いいえ");
define("_INSTALL_L25","mainfile.phpに記述された以下の設定に間違いがないか確認してください。");
define("_INSTALL_L26","データベース設定");
define("_INSTALL_L51","データベースサーバ");
define("_INSTALL_L66","　使用するデータベースサーバの種類を選択してください。");
define("_INSTALL_L27","データベースサーバのホスト名");
define("_INSTALL_L67","　使用するデータベースサーバのホスト名を入力してください。<br />　よく分からない場合は、「localhost」として、ほぼ問題はありません。");
define("_INSTALL_L28","データベースユーザ名");
define("_INSTALL_L65","　上記データベースサーバにおけるユーザアカウント名を入力してください。");
define("_INSTALL_L29","データベース名");
define("_INSTALL_L64","　使用するデータベース名を入力してください。<br />　見つからない場合は、この名称でデータベースの作成を試みます。");
define("_INSTALL_L52","データベースパスワード");
define("_INSTALL_L68","　上記ユーザアカウントのパスワードを入力してください。");
define("_INSTALL_L30","テーブル接頭語");
define("_INSTALL_L63","　各テーブル名にこの接頭語を付加し、既存テーブルとの名称の重複を防ぎます。<br />　よく分からない場合はデフォルトのままにしておいてください。");
define("_INSTALL_L54","データベースへ持続的接続");
define("_INSTALL_L69","　デフォルトは「いいえ」です。よく分からない場合は「いいえ」を選択してください。");
define("_INSTALL_L55","XOOPS Cubeへのパス");
define("_INSTALL_L59","　XOOPS Cubeが設置されているディレクトリへのフルパスを入力してください。<br />　末尾には「/」を付加しないでください。");
define("_INSTALL_L75","XOOPS_TRUST_PATH へのパス");
define("_INSTALL_L76","XOOPS_TRUST_PATH ディレクトリへのフルパスを入力してください。末尾には「/」を付加しないでください。<br />XOOPS_TRUST_PATH はドキュメントルートの外に置いてください（'public_html', 'html'などのディレクトリの下はNGです）。");
define("_INSTALL_L56","XOOPS CubeへのURL");
define("_INSTALL_L58","　XOOPS CubeにアクセスするURLを入力してください。<br />　末尾には「/」を付加しないでください。");

define("_INSTALL_L31","データベースの作成に失敗しました。<br /><br />設定されたユーザアカウントの権限ではデータベースの作成ができない場合は、別途作成し、インストールを続けてください。<br />または、はじめからやり直してください。<br />よくわからない場合は、サーバ管理者にお問い合わせください。");
define("_INSTALL_L32","インストール第１ステップ完了");
define("_INSTALL_L33","インストールされたホームページを見るには<a href='../index.php'> ここ </a>をクリックしてください。");
define("_INSTALL_L35","インストール中にエラーが発生した場合は<a href='http://xoopscube.jp/'>XOOPS Cube日本語サイト</a>のサポートフォーラムをご利用ください。");
define("_INSTALL_L36","サイト管理者のユーザ名、ユーザパスワード、およびメールアドレスを入力してください。");
define("_INSTALL_L37","管理者ユーザ名");
define("_INSTALL_L38","管理者メールアドレス");
define("_INSTALL_L39","管理者パスワード");
define("_INSTALL_L74","管理者パスワード(再入力)");
define("_INSTALL_L77","タイムゾーン");

define("_INSTALL_L40","データベーステーブル作成");
define("_INSTALL_L41","必要なデータをすべて入力してください。");
define("_INSTALL_L42","戻る");
define("_INSTALL_L57","%sを入力してください");

// %s is database name
define("_INSTALL_L43","データベース%sを作成しました。");

// %s is table name
define("_INSTALL_L44","%sテーブルの作成に失敗しました。"); 
define("_INSTALL_L45","%sテーブルを作成しました。");

define("_INSTALL_L46","XOOPS Cubeのモジュールが正常に動作するには、下記のファイルがサーバにより書き込み可能になっている必要があります。（UNIX/LINUXサーバをご使用の場合、各ファイルのパーミッションを666または777に設定してください。）");
define("_INSTALL_L47","次へ");

define("_INSTALL_L53","設定内容の確認");

define("_INSTALL_L60","mainfile.phpの読み込みに失敗しました。ファイルパーミッションの設定を確認してください。");
define("_INSTALL_L61","mainfile.phpへの書き込みに失敗しました。サーバ管理者まで問い合わせください。");
define("_INSTALL_L62","設定を、mainfile.phpへ書き込みました。");
define("_INSTALL_L72","下記のディレクトリを、サーバによる書き込みが可能な設定で作成してください。（UNIX/LINUXサーバをご使用の場合、各ディレクトリのパーミッションを777に設定してください。）");
define("_INSTALL_L73","不正なメールアドレスです。");

// add by haruki
define("_INSTALL_L80","イントロダクション");
define("_INSTALL_L81","アクセス権のチェック");
define("_INSTALL_L82","ファイルのアクセス権のチェック");
define("_INSTALL_L83","ファイル%sは、書込不可となっています。chmod 666してください。");
define("_INSTALL_L84","ファイル%sは、書込可です。");
define("_INSTALL_L85","ディレクトリ%sは、書込不可となっています。chmod 777してください。");
define("_INSTALL_L86","ディレクトリ%sは、書込可です。");
define("_INSTALL_L87","アクセス権に問題はありません。");
define("_INSTALL_L88","ファイル・ディレクトリのアクセス権をチェックしてください。");
define("_INSTALL_L166","XOOPS_TRUST_PATH のアクセス権チェック");
define("_INSTALL_L167","XOOPS_TRUST_PATH のファイルのアクセス権のチェック");
define("_INSTALL_L89","設定の入力");
define("_INSTALL_L90","データベース、およびパス・URLの設定");
define("_INSTALL_L91","確認");
define("_INSTALL_L92","設定の保存");
define("_INSTALL_L93","設定の再入力");
define("_INSTALL_L94","パス・URLのチェック");
define("_INSTALL_L127","ファイルのパスとURLをチェックしています…");
define("_INSTALL_L95","ルートディレクトリのパスを検知できません。");
define("_INSTALL_L96","検知されたルートディレクトリのパスは、設定されたもの(XOOPS_ROOT_PATH)と異なります。");
define("_INSTALL_L97","検知されたルートディレクトリのパスは、設定されたもの(XOOPS_ROOT_PATH)と一致しています。");

define("_INSTALL_L99","設定されたルートディレクトリのパスは、ディレクトリではありません。");
define("_INSTALL_L100","設定されたURLは、正しい形式です。");
define("_INSTALL_L101","設定されたURLは、不正な形式です。");
define("_INSTALL_L102","データベース設定の確認");
define("_INSTALL_L103","はじめからやり直す");
define("_INSTALL_L104","データベースをチェック");
define("_INSTALL_L105","データベース作成を試みる");
define("_INSTALL_L106","データベースサーバに接続できません。");
define("_INSTALL_L107","データベース設定に誤りは無いか、データベースサーバが正しく動作しているか確認してください。");
define("_INSTALL_L108","データベースサーバへ接続できます。");
define("_INSTALL_L109","データベース%sは存在しません。");
define("_INSTALL_L110","データベース%sは存在し、接続可能です。");
define("_INSTALL_L111","データベースサーバへの接続に問題はありません。<br />下記のボタンをクリックすると、データベーステーブルを作成します。");
define("_INSTALL_L112","サイト管理者についての設定");
define("_INSTALL_L113","テーブル%sが削除されました。");
define("_INSTALL_L114","データベーステーブルの作成に失敗しました。");
define("_INSTALL_L115","データベーステーブルが作成されました。");
define("_INSTALL_L116","データの生成");
define("_INSTALL_L117","完了");

define("_INSTALL_L118","データベース%sの作成に失敗しました。");
define("_INSTALL_L119","%d個のデータがデータベース%sにインサートされました。");
define("_INSTALL_L120","%d個のデータをデータベース%sにインサートすることに失敗しました。");

define("_INSTALL_L121","定数%sが%sに設定されました。");
define("_INSTALL_L122","定数%sの書込みに失敗しました。");

define("_INSTALL_L123","ファイル%sがcache/ディレクトリに書込まれました。");
define("_INSTALL_L124","ファイル%sの書込みに失敗しました。");

define("_INSTALL_L125","ファイル%sがファイル%sで上書きされました。");
define("_INSTALL_L126","ファイル%sを上書きできませんでした。");

define("_INSTALL_L130","インストーラがデータベースよりXOOPS 1.3.x のテーブルを見つけました。<br />インストーラは今から、データベースをXOOPS2へアップグレードすることを試みます。");
define("_INSTALL_L131","XOOPS2のテーブルがデータベースに既に存在します。");
define("_INSTALL_L132","テーブルのアップデート");
define("_INSTALL_L133","テーブル %s をアップデートしました。");
define("_INSTALL_L134","テーブル %s のアップデートが失敗しました。");
define("_INSTALL_L135","データベーステーブルのアップデートは失敗しました。");
define("_INSTALL_L136","データベーステーブルをアップデートしました。");
define("_INSTALL_L137","モジュールのアップデート");
define("_INSTALL_L138","コメントのアップデート");
define("_INSTALL_L139","アバターのアップデート");
define("_INSTALL_L140","顔アイコンのアップデート");
define("_INSTALL_L141","インストーラは今から、XOOPS Cubeで動くように各モジュールをアップデートします。<br />XOOPS Cubeのパッケージに含まれるすべてのファイルがサーバにアップロードされているか確認してください。<br />これが完了するまでには、しばらく時間が掛かるかもしれません。");
define("_INSTALL_L142","モジュールのアップデート中…");
define("_INSTALL_L143","The installer will now update configuration data of XOOPS 1.3.x to be used with XOOPS Cube.");	//[MADA]
define("_INSTALL_L144","コンフィギュレーションのアップデート");
define("_INSTALL_L145","コメント(ID: %s)をデータベースに格納しました。");
define("_INSTALL_L146","コメント(ID: %s)がデータベースに格納できません。");
define("_INSTALL_L147","コメントのアップデート中…");
define("_INSTALL_L148","アップデートが完了しました。");
define("_INSTALL_L149","インストーラは今から、XOOPS Cubeで使えるようにXOOPS 1.3.xのコメントポストをアップデートします。<br />これが完了するまでには、しばらく時間が掛かるかもしれません。");
define("_INSTALL_L150","インストーラは今から、XOOPS Cubeで使えるように顔アイコンとユーザランキングの画像をアップデートします。<br />これが完了するまでには、しばらく時間が掛かるかもしれません。");
define("_INSTALL_L151","インストーラは今から、XOOPS Cubeで使えるようにユーザアバターの画像をアップデートします。<br />これが完了するまでには、しばらく時間が掛かるかもしれません。");
define("_INSTALL_L155","顔アイコンとユーザランキング画像のアップデート中…");
define("_INSTALL_L156","ユーザアバター画像のアップデート中…");
define("_INSTALL_L157","各グループについてデフォルトのグループを選択してください。");
define("_INSTALL_L158","バージョン1.3.x");
define("_INSTALL_L159","管理者グループ");
define("_INSTALL_L160","登録ユーザグループ");
define("_INSTALL_L161","匿名ユーザグループ");
define("_INSTALL_L162","各グループタイプについて、デフォルトのグループを選択してください。");
define("_INSTALL_L163","テーブル %s を削除しました。");
define("_INSTALL_L164","テーブル %s の削除に失敗しました。");
define("_INSTALL_L165","このサイトはただいまメインテナンスです。後程お越しください。");

// %s is filename
define("_INSTALL_L152","ファイル %s を開けませんでした");
define("_INSTALL_L153","ファイル %s を更新できませんでした");
define("_INSTALL_L154","ファイル %s を更新しました");

define('_INSTALL_L128', 'インストール作業に使用する言語を選択してください');
define('_INSTALL_L200', '再読込');
define('_INSTALL_L210', 'インストール第２ステップ');


define('_INSTALL_CHARSET','EUC-JP');
//define('_CHARSET','EUC-JP');

define('_INSTALL_LANG_XOOPS_SALT', "SALT");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "暗号・トークンを生成するための補助的な情報です。特に変更する必要はありません。");

define('_INSTALL_HEADER_MESSAGE','画面上の指示に従って設定を行ってください');

if (function_exists("mb_language")) { 
	mb_language( 'Japanese' ) ;
	mb_internal_encoding( 'EUC-JP' ) ;
	mb_http_output( 'EUC-JP' ) ;
}
@ini_set('default_charset', _INSTALL_CHARSET);

?>
