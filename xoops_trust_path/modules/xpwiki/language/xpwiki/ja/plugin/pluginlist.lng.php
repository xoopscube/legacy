<?php
/*
 * Created on 2008/11/10 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: pluginlist.lng.php,v 1.12 2011/07/29 06:23:06 nao-pon Exp $
 */

$msg = array(
	'dirname' => 'ディレクトリ名',
	'addline' => array(
		'title' => '定型文追加',
		'block_usage' => '#addline(設定名[,above|,below|,up|,down][,number|,nonumber][,btn:<ボタンテキスト>][,ltext:<左テキスト>][,rtext:<右テキスト>])',
		'inline_usage' => '&addline(設定名[,before|,after|,above|,below|,up|,down][,number|,nonumber]){<ボタンテキスト>};'
	),
	'amazon' => array(
		'title' => 'Amazon',
		'block_usage' => '#amazon([ASIN-number][,left|,right][,book-title|,image|,delimage|,deltitle|,delete])',
		'inline_usage' => '&amazon(ASIN-number);'
	),
	'areaedit' => array(
		'title' => '部分編集',
		'block_usage' => '#areaedit([start|end][,btn:<text>][,nofreeze][,noauth][,collect[:<page>]])',
		'inline_usage' => '&areaedit([nofreeze][,noauth][,preview[:<num>]]){<Text>};'
	),
	'article' => array(
		'title' => '掲示板',
		'block_usage' => '#article',
		'inline_usage' => ''
	),
	'attach' => array(
		'title' => '添付ファイルフォーム',
		'block_usage' => '#attach([,nolist][,noform][,noattach])',
		'inline_usage' => ''
	),
	'aws' => array(
		'title' => 'Amazon Web Service',
		'block_usage' => '#aws(<TemplateName>,<SearchIndex>,<Keyword>[,<BrowseNode>][,<SortMode>])',
		'inline_usage' => ''
	),
	'back' => array(
		'title' => '「戻る」ボタン',
		'block_usage' => '#back([text],[center|left|right][,0(no hr)[,Page-or-URI-to-back]])',
		'inline_usage' => ''
	),
	'block' => array(
		'title' => '<div>ブロック',
		'block_usage' => "#block([end][,clear][,left|,center|,right][,around][,tate][,h:<高さ>][,width:<幅>|w:<幅>][,class:<クラス名>][,font-size:<フォントサイズ>][,round]){{\n<Wikiテキスト>\n}}",
		'inline_usage' => ''
	),
	'calendar2' => array(
		'title' => 'カレンダー',
		'block_usage' => '#calendar2([pagename|*][,yyyymm|,yyyymmdd][,off])',
		'inline_usage' => ''
	),
	'calendar9' => array(
		'title' => '拡張カレンダー',
		'block_usage' => '#calendar9([pagename|*][,yyyymm][,0-6])',
		'inline_usage' => ''
	),
	'calendar_viewer' => array(
		'title' => 'カレンダー表示',
		'block_usage' => '#calendar_viewer([yyyy-mm|n|this],[past|futur|view][,<Separater>])',
		'inline_usage' => ''
	),
	'capture' => array(
		'title' => '部分収集',
		'block_usage' => "#caputer(ID){{\n<Wikiテキスト>\n}}",
		'inline_usage' => ''
	),
	'chat' => array(
		'title' => 'AjaxChat挿入',
		'block_usage' => '#chat([staypos:r][,height:<高さ>][,id:ChatID])',
		'inline_usage' => ''
	),
	'clear' => array(
		'title' => '回り込み解除',
		'block_usage' => '#clear',
		'inline_usage' => ''
	),
	'code' => array(
		'title' => 'ソースコード表示',
		'block_usage' => "#code([<Type>][,[\d]-[\d]][,number|nonumber][,outline|nooutline][,block|noblock][,literal|noliteral][,comment|nocomment][,menu|nomenu][,icon|noicon][,link|nolink]){{\n<ソースコード>\n}}",
		'inline_usage' => ''
	),
	'comment' => array(
		'title' => '一行コメント',
		'block_usage' => '#comment([noname][,nodate][,above|,below][,cols:<桁数>][,multi:<行数>])',
		'inline_usage' => ''
	),
	'contents' => array(
		'title' => '目次',
		'block_usage' => '#contents',
		'inline_usage' => ''
	),
	'counter' => array(
		'title' => 'ページカウンタ',
		'block_usage' => '#counter',
		'inline_usage' => '&counter([total|today|yesterday]);'
	),
	'edit' => array(
		'title' => '編集リンク',
		'block_usage' => '',
		'inline_usage' => '&edit([<ページ名>{[,nolabel][,noicon]}]){<ラベル名>};'
	),
	'exifshowcase' => array(
		'title' => '画像一覧',
		'block_usage' => '#exifshowcase([<抽出パターン>][,Left|,Center|,Right][,Wrap|,Nowrap][,Around][,nolink][,noimg][,<幅>x<高さ>][,<拡大率>%][,info][,nomapi][,nokash][,noexif][,reverse][,sort][,col:<カラム数>][,row:<列数>][,<列数>])',
		'inline_usage' => '&exifshowcase([<抽出パターン>][,nolink][,noimg][,<幅>x<高さ>][,<拡大率>%][,info][,nomapi][,nokash][,noexif][,reverse][,sort][,col:<カラム数>][,row:<列数>][,<列数>]);'
	),
	'footnotes' => array(
		'title' => '脚注設定・表示',
		'block_usage' => '#footnotes([<カテゴリ>:<記号>:][,<カテゴリ名>:<記号>:]...)'."\n".'#footnotes([force][,nobr][,nohr][,<対象カテゴリ>[,catrgory]]...)',
		'inline_usage' => ''
	),
	'fusen' => array(
		'title' => '付箋パネル',
		'block_usage' => '#fusen([refresh:<自動更新秒数>][,height:<付箋エリアの高さ>][,off])',
		'inline_usage' => ''
	),
	'googlemaps2' => array(
		'title' => 'GoogleMap',
		'block_usage' => '#googlemaps2([mapname=<Map名>][,width=<幅>][,height=<高さ>][,lat=<緯度>][,lng=<経度>][,zoom=<縮尺値>][,mapctrl=<none|normal|smallzoom|small|large>][,type=<normal|satellite|hybrid>][,typectrl=<none|normal>][,scalectrl=<none|normal>][,overviewctrl=<none|normal>][,crossctrl=<none|show>][,overviewwidth=<高さ>][,overviewheight=<幅>][,togglemarker=<true|false>][,noiconname=<アイコン無しのラベル>][,dbclickzoom=<true|false>][,continuouszoom=<true|false>][,geoxml=<KML, GeoRSSのURL>][,googlebar=<true|false>][,importicon=][,backlinkmarker=<true|false>][,wikitag=<none|show>][,autozoom=<true|false>])',
		'inline_usage' => '&googlemaps2([mapname=<Map名>][,width=<幅>][,height=<高さ>][,lat=<緯度>][,lng=<経度>][,zoom=<縮尺値>][,mapctrl=<none|normal|smallzoom|small|large>][,type=<normal|satellite|hybrid>][,typectrl=<none|normal>][,scalectrl=<none|normal>][,overviewctrl=<none|normal>][,crossctrl=<none|show>][,overviewwidth=<高さ>][,overviewheight=<幅>][,togglemarker=<true|false>][,noiconname=<アイコン無しのラベル>][,dbclickzoom=<true|false>][,continuouszoom=<true|false>][,geoxml=<KML, GeoRSSのURL>][,googlebar=<true|false>][,importicon=][,backlinkmarker=<true|false>][,wikitag=<none|show>][,autozoom=<true|false>]);'
	),
	'googlemaps2_insertmarker' => array(
		'title' => '',
		'block_usage' => '#googlemaps2_insertmarker([mapname=<Map名>][,direction=<up|down>])',
		'inline_usage' => ''
	),
	'iframe' => array(
		'title' => '<IFRAME> 挿入',
		'block_usage' => '#iframe(<URL>[,style:<CSS Text>][,iestyle:<CSS Text>])',
		'inline_usage' => '&iframe(<URL>[,style:<CSS Text>][,iestyle:<CSS Text>]);'
	),
	'include' => array(
		'title' => '他ページ挿入',
		'block_usage' => '#include(<ページ名>[,title|,notitle])',
		'inline_usage' => ''
	),
	'isbn' => array(
		'title' => 'Amazon',
		'block_usage' => '#isbn(<ASIN>[,clear][,img][,info][,header][,left])',
		'inline_usage' => '&isbn(<ASIN>[,simg][,mimg][,limg])[{<表示テキスト>}];'
	),
	'jsmath' => array(
		'title' => '数式(jsMath)',
		'block_usage' => "#jsmath([mimeTeX][,AMSmath][,AMSsymbols][,autobold][,boldsymbo][,verb][,smallFonts][,noImageFonts][,lobal][,noGlobal][,noCache][,CHMmode][,spriteImageFonts])[{{\n<数式>\n}}]",
		'inline_usage' => '&jsmath([mimeTeX][,AMSmath][,AMSsymbols][,autobold][,boldsymbo][,verb][,smallFonts][,noImageFonts][,lobal][,noGlobal][,noCache][,CHMmode][,spriteImageFonts]){<数式>};'
	),
	'lastmod' => array(
		'title' => 'ページ最終更新日時',
		'block_usage' => '',
		'inline_usage' => '&lastmod([<ページ名>]);'
	),
	'lookup' => array(
		'title' => 'InterWikiフォーム',
		'block_usage' => '#lookup(<InterWikiName>[,<ボタン名>[,<入力欄の初期値>]])',
		'inline_usage' => ''
	),
	'ls2' => array(
		'title' => 'ページ一覧',
		'block_usage' => '#ls2([[<ベースページ名>][,title][,include][,reverse][,compact][,link][,<linkの別名表示>])',
		'inline_usage' => ''
	),
	'lsx' => array(
		'title' => '拡張ページ一覧',
		'block_usage' => '#lsx([[prefix:]<ベースページ>][,num:<表示件数>][,depth:<表示階層数>][,hierarchy][,basename][,tree:[leaf|dir]][,sort:[name|date]][,reverse][,non_list][,except:<除外ページ名正規表現>][,filter:<対象ページ名正規表現>][,date][,new][,order][,info:[date|new]][,contents][,include][,tag:<タグ>][,notitle])',
		'inline_usage' => ''
	),
	'memo' => array(
		'title' => '簡易メモ',
		'block_usage' => '#memo',
		'inline_usage' => ''
	),
	'moblog' => array(
		'title' => 'moblog Cron',
		'block_usage' => '#moblog',
		'inline_usage' => ''
	),
	'new' => array(
		'title' => '新着書式化',
		'block_usage' => '',
		'inline_usage' => "&new([<ページ名>][,nolink]);\n&new([nodate][,class:<クラス名>]){<日付文字列>};"
	),
	'navi' => array(
		'title' => 'ページナビゲーション',
		'block_usage' => '#navi([<目次ページ>])',
		'inline_usage' => ''
	),
	'newpage' => array(
		'title' => 'ページ作成フォーム',
		'block_usage' => '#newpage([this|<ベースページ名>])',
		'inline_usage' => ''
	),
	'noattach' => array(
		'title' => '添付ファイルリスト非表示',
		'block_usage' => '#noattach',
		'inline_usage' => ''
	),
	'noautolink' => array(
		'title' => 'オートリンク無効',
		'block_usage' => '#noautolink',
		'inline_usage' => ''
	),
	'nocontents' => array(
		'title' => '目次挿入解除',
		'block_usage' => '#nocontents',
		'inline_usage' => ''
	),
	'nofollow' => array(
		'title' => 'NOFOLLOW,NOINDEX',
		'block_usage' => '#nofollow',
		'inline_usage' => ''
	),
	'noheader' => array(
		'title' => 'ページヘッダ非表示',
		'block_usage' => '#noheader',
		'inline_usage' => ''
	),
	'nopagecomment' => array(
		'title' => 'ページコメント無効',
		'block_usage' => '#nopagecomment',
		'inline_usage' => ''
	),
	'norelated' => array(
		'title' => '関連ページ非表示',
		'block_usage' => '#norelated',
		'inline_usage' => ''
	),
	'online' => array(
		'title' => 'オンラインユーザー数',
		'block_usage' => '#online',
		'inline_usage' => '&online;'
	),
	'pagepopup' => array(
		'title' => 'ポップアップ ページリンク',
		'block_usage' => '',
		'inline_usage' => '&pagepopup(<ページ名>)[{<表示テキスト>}];'
	),
	'pcomment' => array(
		'title' => '拡張一行コメント',
		'block_usage' => '#pcomment([コメント記録ページ][,表示件数][,noname][,nodate][,above|,below][,reply][,cols:<桁数>][,multi:<行数>])',
		'inline_usage' => ''
	),
	'popular' => array(
		'title' => '人気の数件',
		'block_usage' => '#popular([[件数],[対象外ページ],[today|1|yesterday|-1|total|0],[<ベースページ名>],[0|1]])',
		'inline_usage' => ''
	),
	'pre' => array(
		'title' => '整形済み',
		'block_usage' => "#pre(){{\n<整形済みテキスト>\n}}",
		'inline_usage' => ''
	),
	'random' => array(
		'title' => 'ランダムジャンプ',
		'block_usage' => '#random([<表示テキスト>])',
		'inline_usage' => ''
	),
	'recent' => array(
		'title' => '最近の数件',
		'block_usage' => '#recent([<ベースページ名>][,<件数>][,<対象未来日数>][,<対象uid>])',
		'inline_usage' => ''
	),
	'region' => array(
		'title' => '折りたたみテキスト',
		'block_usage' => "#region(<タイトル>){{\n<折りたたまれるWikiテキスト>\n}}",
		'inline_usage' => ''
	),
	'related' => array(
		'title' => '参照ページ一覧',
		'block_usage' => '#related([<表示件数>[,nopassage][,notitle][,context][,context:<最大バイト数>/<最大分割数>][,separate][,highlight]])',
		'inline_usage' => ''
	),
	'relatedview' => array(
		'title' => '参照元引用一覧',
		'block_usage' => '#relatedview([noautolink][,nowikiname][,eachpage][,search:<ページ名またはデリミタ"#"の正規表現)>][,nosearch:<ページ名またはデリミタ"#"の正規表現>])',
		'inline_usage' => ''
	),
	'rsslink' => array(
		'title' => 'RSSリンク',
		'block_usage' => '',
		'inline_usage' => '&rsslink([[<ベースページ名>][[,1.0|,2.0|,atom][,<出力件数>]]]);'
	),
	'ruby' => array(
		'title' => 'ルビ打ち',
		'block_usage' => '',
		'inline_usage' => '&ruby(<ふりがな>){<対象文字列>};'
	),
	'search' => array(
		'title' => '検索フォーム',
		'block_usage' => '#search([抽出条件1[,抽出条件2][,抽出条件n]...)',
		'inline_usage' => ''
	),
	'setlang' => array(
		'title' => '言語指定',
		'block_usage' => "#setlang(ja|zh|cn|ko){{\n<Wikiテキスト>\n}}",
		'inline_usage' => '&setlang(ja|zh|cn|ko){<表示文字列>};'
	),
	'showrss' => array(
		'title' => '外部RSS表示',
		'block_usage' => '#showrss(<RSS URL>[,[default|menubar|recent][,[<キャッシュ生存時間>[,<更新日時表示;0|1>[,<Discription表示;0|1>[,<表示件数>]]]]])',
		'inline_usage' => ''
	),
	'siteimage' => array(
		'title' => '外部サイトサムネイル',
		'block_usage' => '#siteimage(<外部サイトURL>[,nolink][,target:<ターゲット名>][,size:s|m|l][,around][,right|center|left])',
		'inline_usage' => '&siteimage(<外部サイトURL>[,nolink][,target:<ターゲット名>][,size:s|m|l]);'
	),
	'skin_changer' => array(
		'title' => 'スキンの選択',
		'block_usage' => '#skin_changer',
		'inline_usage' => '&skin_changer([<スキン名>])[{<表示テキスト>}];'
	),
	'tag' => array(
		'title' => 'タグ設定, タグクラウド',
		'block_usage' => '#tag([<タグクラウド最大表示件数>])',
		'inline_usage' => '&tag(<タグ1>[,<タグ2>][,<タグ3>]...);'
	),
	'tdiary' => array(
		'title' => 'tDiaryテーマ反映',
		'block_usage' => '#tdiary(<tDiaryテーマ名>)',
		'inline_usage' => ''
	),
	'temp' => array(
		'title' => '一時テンプレート',
		'block_usage' => "#temp(<テンプレート名>[,引数1][,引数2]...){{\n<Wikiテキスト>\n}}",
		'inline_usage' => ''
	),
	'tracker' => array(
		'title' => '報告フォーム',
		'block_usage' => '#tracker([<定義名>][,<ベースページ名>])',
		'inline_usage' => ''
	),
	'tracker_list' => array(
		'title' => '報告リスト',
		'block_usage' => '#tracker_list([<定義名>][,[<ベースページ名>][,[[<ソート項目>]:[SORT_ASC|SORT_DESC]][,<表示上限数>]]]])',
		'inline_usage' => ''
	),
	'ucomedit' => array(
		'title' => 'EXIFコメント編集',
		'block_usage' => '#ucomedit(filename[,formonly])',
		'inline_usage' => ''
	),
	'urlbookmark' => array(
		'title' => '簡易リンク集',
		'block_usage' => '#urlbookmark([notitle][,nodate][,above|,below])',
		'inline_usage' => ''
	),
	'vote' => array(
		'title' => '投票フォーム',
		'block_usage' => '#vote([<項目1>][,<項目2>]...[,#add][,#notimestamp][,#sort][,#ksort][,#nomail])',
		'inline_usage' => ''
	),
	'xoopsblock' => array(
		'title' => 'XOOPSブロック表示',
		'block_usage' => '#xoopsblock([<ブロックID 1>][,<ブロックID 2>]...)',
		'inline_usage' => ''
	),
	'yahoo' => array(
		'title' => 'Yahoo! API',
		'block_usage' => '#yahoo(web|img|mov,<検索語>[,type:and|or|word][,max:<表示件数>][,col:<表示桁数>][,row:<表示行数>][,target:<ターゲット名>])',
		'inline_usage' => ''
	),
);
