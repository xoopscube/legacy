<?php

define('_MD_D3PIPES_H2_INDEX','インデックス');
define('_MD_D3PIPES_H2_LATESTHEADLINES','最新記事一覧');
define('_MD_D3PIPES_H2_EACHPIPE','記事一覧');
define('_MD_D3PIPES_H2_CLIPLIST','切り抜き一覧');
define('_MD_D3PIPES_H2_CLIPPING','切り抜き詳細');

define('_MD_D3PIPES_JOINT_FETCH','外部から取得');
define('_MD_D3PIPES_JOINT_BLOCK','ブロック関数からの取得/解析');
define('_MD_D3PIPES_JOINT_PARSE','XML解析');
define('_MD_D3PIPES_JOINT_CACHE','キャッシュ');
define('_MD_D3PIPES_JOINT_PING','更新Ping');
define('_MD_D3PIPES_JOINT_UTF8TO','コード変換(UTF8から)');
define('_MD_D3PIPES_JOINT_UTF8FROM','コード変換(UTF8へ)');
define('_MD_D3PIPES_JOINT_REPLACE','テキスト置換');
define('_MD_D3PIPES_JOINT_CLIP','ローカル保存');
define('_MD_D3PIPES_JOINT_FILTER','絞り込み');
define('_MD_D3PIPES_JOINT_REASSIGN','再割り当て');
define('_MD_D3PIPES_JOINT_SORT','エントリの順序');
define('_MD_D3PIPES_JOINT_UNION','他パイプの連結');

define('_MD_D3PIPES_N4J_FETCH','RSS/ATOM等のURLを記入');
define('_MD_D3PIPES_N4J_PARSE','RDF/RSS/ATOMの別を記入（判らなければ空欄）');
define('_MD_D3PIPES_N4J_CACHE','キャッシュ時間を記入(単位は秒)');
define('_MD_D3PIPES_N4J_UTF8TO','通常は、内部エンコーディングを記入');
define('_MD_D3PIPES_N4J_UTF8FROM','通常は、XMLのエンコーディングを記入');
define('_MD_D3PIPES_N4J_FILTER','正規表現など絞り込むためのパターンを記入');
define('_MD_D3PIPES_N4J_REASSIGN','再割り当てのルールを記入');
define('_MD_D3PIPES_N4J_UNION','統合するパイプ番号(カンマ区切り)');

define('_MD_D3PIPES_N4J_WRITEURL','URLを記入');
define('_MD_D3PIPES_N4J_WRITEPREG','perl正規表現を記入');
define('_MD_D3PIPES_N4J_WRITEPOSIX','POSIX正規表現を記入');
define('_MD_D3PIPES_N4J_CID','カテゴリーID');
define('_MD_D3PIPES_N4J_UID','ユーザーID');
define('_MD_D3PIPES_N4J_MAXENTRIES','最大件数');
define('_MD_D3PIPES_N4J_EACHENTRIES','各パイプからの取得数');
define('_MD_D3PIPES_N4J_KEEPPIPEINFO','パイプ情報の保存');
define('_MD_D3PIPES_N4J_TARGETMODULE','対象モジュール');
define('_MD_D3PIPES_N4J_EXTRAOPTIONS','追加オプション');
define('_MD_D3PIPES_N4J_ENTRIESFROMCLIP','切抜きから次へ渡す最低エントリ数');
define('_MD_D3PIPES_N4J_CLIPLIFETIME','切抜きの保存日数(空欄なら一般設定値)');
define('_MD_D3PIPES_N4J_WITHDESCRIPTION','詳細情報も取得');
define('_MD_D3PIPES_N4J_REPLACEFROM','検索パターン');
define('_MD_D3PIPES_N4J_REPLACETO','置換パターン');
define('_MD_D3PIPES_N4J_XSLTPATH','XSLTファイルのパス(URLも可)');

define('_MD_D3PIPES_CLASS_FETCHSNOOPY','Snoopyによる取得 (推奨)');
define('_MD_D3PIPES_CLASS_FETCHFOPEN','URL fopenによる取得');
define('_MD_D3PIPES_CLASS_PARSEKEITHXML','汎用XML解析 (推奨)');
define('_MD_D3PIPES_CLASS_PARSESIMPLEHTML','特定HTMLタグの抽出');
define('_MD_D3PIPES_CLASS_PARSELINKHTML','AタグによるHTML解析');
define('_MD_D3PIPES_CLASS_FILTERPCRE','pcreによるエントリ抽出');
define('_MD_D3PIPES_CLASS_FILTERPCRE_EXCEPT','pcreによるエントリ除外');
define('_MD_D3PIPES_CLASS_FILTERMBREGEX','mbregexによるエントリ抽出');
define('_MD_D3PIPES_CLASS_FILTERMBREGEX_EXCEPT','mbregexによるエントリ除外');
define('_MD_D3PIPES_CLASS_CLIPMODULEDB','DB内へ切り抜きとして保存');
define('_MD_D3PIPES_CLASS_REASSIGNCONTENTENCODED','description→content');
define('_MD_D3PIPES_CLASS_REASSIGNALLOWHTML','HTML表示許可');
define('_MD_D3PIPES_CLASS_REASSIGNSTRIPTAGS','HTMLタグ削除');
define('_MD_D3PIPES_CLASS_REASSIGNDEFAULTLINK','記事リンクURL設定');
define('_MD_D3PIPES_CLASS_REASSIGNHTMLENTITYDECODE','誤ったHTMLエンティティのデコード');
define('_MD_D3PIPES_CLASS_REASSIGNTRUNCATE','文字列長の切り詰め');
define('_MD_D3PIPES_CLASS_CACHETRUSTPATH','この時点のキャッシュ(trust/cache)');
define('_MD_D3PIPES_CLASS_PINGXMLRPC2','XMLRPC2更新Ping');
define('_MD_D3PIPES_CLASS_SORTPUBTIMEDSC','発行日時新着順ソート');
define('_MD_D3PIPES_CLASS_SORTHEADLINESTRASC','エントリ名辞書順昇順ソート');
define('_MD_D3PIPES_CLASS_SORTHEADLINEINTASC','エントリ名数字順昇順ソート');
define('_MD_D3PIPES_CLASS_UNIONMERGESORT','新着順アグリゲーション');
define('_MD_D3PIPES_CLASS_UNIONSEPARATED','並列化（ソート無）');
define('_MD_D3PIPES_CLASS_UNIONTHEOTHERD3PIPES','他d3pipesからの連結');

define('_MD_D3PIPES_TH_PUBTIME','発行日時');
define('_MD_D3PIPES_TH_PIPENAME','パイプ名');
define('_MD_D3PIPES_TH_HEADLINE','見出し');
define('_MD_D3PIPES_TH_LINKURL','リンクURL');
define('_MD_D3PIPES_TH_DESCRIPTION','記事詳細');
define('_MD_D3PIPES_TH_ACTIONTOCLIPPING','この切り抜きへの操作');

define('_MD_D3PIPES_LABEL_HIGHLIGHTCLIPPING','注目マークをつける');
define('_MD_D3PIPES_LABEL_DELETECLIPPING','切り抜きを削除する');
define('_MD_D3PIPES_LABEL_VISIBLECLIPPING','この切り抜きを表示する');

define('_MD_D3PIPES_BTN_UPDATE','更新する');

define('_MD_D3PIPES_LINK_SITEMAPS','Sitemaps');

define('_MD_D3PIPES_FMT_LINKTOCLIPLIST','切り抜き一覧へ (切り抜き総数 %s件)');
define('_MD_D3PIPES_FMT_EXTERNALLINK','%sへの外部リンク');

define('_MD_D3PIPES_MSG_CLIPPINGUPDATED','切り抜きを更新しました');
define('_MD_D3PIPES_MSG_CLIPPINGDELETED','切り抜きを削除しました');
define('_MD_D3PIPES_MSG_CLIPPINGCANNOTDELETED','コメントが存在するために削除できません。先にコメントを削除してください');

define('_MD_D3PIPES_ERR_INVALIDCLIPPINGID','該当する切り抜きはありません');
define('_MD_D3PIPES_ERR_INVALIDPIPEID','該当するパイプがありません');
define('_MD_D3PIPES_ERR_PERMISSION','操作権限がありません');
define('_MD_D3PIPES_ERR_INVALIDPIPEIDINBLOCK','該当するパイプを表示できません。ブロック管理の「編集」からパイプを再度指定し直してください');
define('_MD_D3PIPES_ERR_REDIRECTED','取得先URIからリダイレクト指令を受けました。無駄なトラフィックや待ち時間を削減するために、パイプ管理において下のリダイレクト先URIを指定することをお勧めします。');
define('_MD_D3PIPES_ERR_ERRORBEFOREPARSE','XML解析する前の段階（通常は取得）でエラーが発生しているようです。パイプ管理で確認してください。');
define('_MD_D3PIPES_ERR_PARSETYPEMISMATCH','XML解析のタイプがマッチしていないため、エントリを抽出できません。パイプ管理で確認してください');
define('_MD_D3PIPES_ERR_CACHEFOLDERNOTWRITABLE','キャッシュ用ディレクトリがないか書込可能になっていません');
define('_MD_D3PIPES_ERR_INVALIDURIINFETCH','取得ジョイントで有効なURI指定がされていません');
define('_MD_D3PIPES_ERR_CANTCONNECTINFETCH','取得先に接続できません');
define('_MD_D3PIPES_ERR_DOUBTFULPROXY','Proxy設定を確認してください');
define('_MD_D3PIPES_ERR_DOUBTFULCURLPATH','curlパスを確認してください');
define('_MD_D3PIPES_ERR_URLFOPENINFETCH','allow_url_fopenがoffの場合はfopenは利用できません');
define('_MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK','ブロック関数ジョイントでモジュールディレクトリ名の指定にミスがあります');
define('_MD_D3PIPES_ERR_INVALIDFILEINBLOCK','ブロック関数ジョイントでファイル名指定にミスがあります');
define('_MD_D3PIPES_ERR_INVALIDFUNCINBLOCK','ブロック関数ジョイントで関数名指定にミスがあります');


?>