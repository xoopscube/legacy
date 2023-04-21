<?php

define( '_TPLSADMIN_INTRO', 'テンプレートをコンパイルするためのフックの導入');

define( '_TPLSADMIN_DESC', 'コンパイル フックは、ビジュアル編集ヘルパーをテンプレートに挿入し、Smarty 変数を収集する簡単な方法を提供します。
これらの関数は、それらが作成されたフロントエンド テンプレートおよび複製モジュールでのみ使用できます。  ');

define( '_TPLSADMIN_NOTE', 'Important : ビジュアルヘルパーは、レイアウトやテンプレートの構造を強調するためのものですが、コンポーネントやカスタムテンプレートなど、機能ベースの認識には限界があります。');

define( '_TPLSADMIN_TASK_Title', 'このタスクを実行する理由と時期');
define( '_TPLSADMIN_TASK', '
コンパイルされたテンプレートを使用して、次のタスクを完了できます。<br>
<ul>
<li>機能設計の欠陥の認識を容易にする構造の概要</li>
<li>含まれているコンポーネントとテンプレートごとにレンダリングされるオーバーレイ要素を挿入する
<li>コード コメントを挿入してソース コードの編集を容易にする</li>
<li>テンプレートの設計と実装の違いを検出して解決する</li>
<li>テンプレートで使用されるアプリケーション コードを生成し、Smarty 変数を収集します。</li>
</ul>');

define( '_TPLSADMIN_CACHE_TITLE', 'コンパイルされたテンプレート');
define( '_TPLSADMIN_CACHE_DESC' , 'ソース テンプレートは変更されません。 ほとんどの場合、コンパイル済みのテンプレート ファイルをすべて削除して、<b>Normalize</b> を実行できます。 キャッシュから削除されたテンプレートはすぐに再生成されます。' );

define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYCOMMENT' , '%d 個のコンパイル済テンプレートキャッシュに、コメントを埋め込みました');
define( '_TPLSADMIN_DT_ENCLOSEBYCOMMENT' , 'テンプレート名をコメントとして埋め込む');
define( '_TPLSADMIN_DD_ENCLOSEBYCOMMENT' , 'テンプレート名は、各テンプレートの最初と最後に HTML コメントの形式で埋め込まれます。 デザインへの影響が少ないので、HTMLソースコードを編集するプロの方におすすめです。');
define( '_TPLSADMIN_CNF_ENCLOSEBYCOMMENT' , 'キャッシュされたコンパイル済みモデルに「tplsadmin」コメントを追加します。 続行するかキャンセルするかを確認してください。');


define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYBORDEREDDIV' , '%d 個のコンパイル済テンプレートキャッシュに、divタグを埋め込みました');
define( '_TPLSADMIN_DT_ENCLOSEBYBORDEREDDIV' , 'テンプレートを枠で囲う');
define( '_TPLSADMIN_DD_ENCLOSEBYBORDEREDDIV' , 'テンプレート変数情報一覧を取得するための前段階。コンパイル済のテンプレートキャッシュにロジックを埋め込んでから、各ページを表示することで、テンプレート変数情報が蓄積されていきます。適当なタイミングで、下のボタンから情報を取得してください。このロジックを外す際は、コンパイルキャッシュをクリアしてください。');
define( '_TPLSADMIN_CNF_ENCLOSEBYBORDEREDDIV' , 'キャッシュされたテンプレートを div タグでラップします。 続行するかキャンセルするかを確認してください!');

define( '_TPLSADMIN_FMT_MSG_HOOKSAVEVARS' , '%d 個のコンパイル済テンプレートキャッシュにテンプレート変数情報取得ロジックを埋め込みました');
define( '_TPLSADMIN_DT_HOOKSAVEVARS' , 'テンプレート変数を収集するロジックを挿入する');
define( '_TPLSADMIN_DD_HOOKSAVEVARS' , 'テンプレート変数情報一覧を取得するための前段階。コンパイル済のテンプレートキャッシュにロジックを埋め込んでから、各ページを表示することで、テンプレート変数情報が蓄積されていきます。適当なタイミングで、下のボタンから情報を取得してください。このロジックを外す際は、コンパイルキャッシュをクリアしてください。');
define( '_TPLSADMIN_CNF_HOOKSAVEVARS' , '現在のコンパイル済テンプレートキャッシュファイルに、テンプレート変数情報取得ロジックを埋め込みますか？');

define( '_TPLSADMIN_FMT_MSG_REMOVEHOOKS' , '%d 個のコンパイル済テンプレートキャッシュを通常状態に戻しました');
define( '_TPLSADMIN_DT_REMOVEHOOKS' , 'テンプレートキャッシュを通常状態に戻す');
define( '_TPLSADMIN_DD_REMOVEHOOKS' , 'コンパイル済テンプレートキャッシュから、上の操作によって埋め込まれた部分を削除します。なんらかの不具合が出た場合は、キャッシュファイルを消してください。（自動的に再生成されます）');
define( '_TPLSADMIN_CNF_REMOVEHOOKS' , '削除処理を実行しますか？');


define( '_TPLSADMIN_MSG_CLEARCACHE' , 'キャッシュをクリアしました');
define( '_TPLSADMIN_MSG_CREATECOMPILECACHEFIRST' , 'コンパイルキャッシュが生成されていません。先に、編集目的のページを一通り表示しコンパイルキャッシュが生成されてから、再度このコマンドを実行してください。');

define( '_TPLSADMIN_CNF_DELETEOK' , '削除してよろしいですか?');


define( '_TPLSADMIN_DT_GETTPLSVARSINFO_DW' , 'テンプレート変数情報をDreamWeaver用に取得する');
define( '_TPLSADMIN_DD_GETTPLSVARSINFO_DW' , 'まずは Macromedia Extension Manager がインストールされていることを確認し、起動しておいてください。<br>ダウンロードしたファイルを解凍して、拡張子mxiのファイルを実行することで、お使いのDreamWeaverにExtensionとしてインストールされます。DreamWeaver再起動後に、Snippetから利用できます。');

define( '_TPLSADMIN_DT_GETTEMPLATES' , 'テンプレートをダウンロードする');
define( '_TPLSADMIN_DD_GETTEMPLATES' , 'テンプレートセットを選択してから、希望のアーカイブタイプボタンを押してください');

define( '_TPLSADMIN_FMT_MSG_PUTTEMPLATES' , '%d 個のテンプレートをインポートしました');
define( '_TPLSADMIN_DT_PUTTEMPLATES' , 'テンプレートをアップロードする');
define( '_TPLSADMIN_DD_PUTTEMPLATES' , '置き換える一連のテンプレートを選択します。<br> テンプレート (.html) を含む <b>tar</b> ファイルを選択します。<br> ディレクトリ ツリー構造に関係なく、すべてのテンプレートを自動的に抽出します。');


define( '_TPLSADMIN_ERR_NOTUPLOADED' , 'ファイルがアップロードされていません');
define( '_TPLSADMIN_ERR_EXTENSION' , '未対応のファイル種別です');
define( '_TPLSADMIN_ERR_INVALIDARCHIVE' , 'アーカイブから読み出せません');
define( '_TPLSADMIN_ERR_INVALIDTPLSET' , 'テンプレートセット指定が不正です');

define( '_TPLSADMIN_ERR_NOTPLSVARSINFO' , 'テンプレート変数情報ファイルが作成されていません');

define( '_TPLSADMIN_NUMCAP_COMPILEDCACHES' , 'キャッシュ ディレクトリにコンパイルされたテンプレート');
define( '_TPLSADMIN_NUMCAP_TPLSVARS' , '編集用のオプションでコンパイルされたテンプレート');
