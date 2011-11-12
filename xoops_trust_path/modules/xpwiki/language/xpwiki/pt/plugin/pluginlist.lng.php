<?php
/*
 * Created on 2008/11/10 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: pluginlist.lng.php,v 1.2 2011/07/29 06:23:06 nao-pon Exp $
 */

$msg = array(
	'dirname' => 'DirName',
	'addline' => array(
		'title' => 'Add fixed contents',
		'block_usage' => '#addline(Config name[,above|,below|,up|,down][,number|,nonumber][,btn:<Button text>][,ltext:<Left text>][,rtext:<Right text>])',
		'inline_usage' => '&addline(Config name[,before|,after|,above|,below|,up|,down][,number|,nonumber]){<Button text>};'
	),
	'amazon' => array(
		'title' => 'Amazon',
		'block_usage' => '#amazon([ASIN-number][,left|,right][,book-title|,image|,delimage|,deltitle|,delete])',
		'inline_usage' => '&amazon(ASIN-number);'
	),
	'areaedit' => array(
		'title' => 'Partial edit',
		'block_usage' => '#areaedit([start|end][,btn:<text>][,nofreeze][,noauth][,collect[:<page>]])',
		'inline_usage' => '&areaedit([nofreeze][,noauth][,preview[:<num>]]){<Text>};'
	),
	'article' => array(
		'title' => 'Bulletin board',
		'block_usage' => '#article',
		'inline_usage' => ''
	),
	'attach' => array(
		'title' => 'Attached file form',
		'block_usage' => '#attach([,nolist][,noform][,noattach])',
		'inline_usage' => ''
	),
	'aws' => array(
		'title' => 'Amazon Web Service',
		'block_usage' => '#aws(<TemplateName>,<SearchIndex>,<Keyword>[,<BrowseNode>][,<SortMode>])',
		'inline_usage' => ''
	),
	'back' => array(
		'title' => '"Return" button',
		'block_usage' => '#back([text],[center|left|right][,0(no hr)[,Page-or-URI-to-back]])',
		'inline_usage' => ''
	),
	'block' => array(
		'title' => '<div> block',
		'block_usage' => "#block([end][,clear][,left|,center|,right][,around][,tate][,h:<Height>][,width:<Width>|w:<Width>][,class:<Class name>][,font-size:<Font size>][,round]){{\n<Wiki text>\n}}",
		'inline_usage' => ''
	),
	'calendar2' => array(
		'title' => 'Calendar',
		'block_usage' => '#calendar2([pagename|*][,yyyymm|,yyyymmdd][,off])',
		'inline_usage' => ''
	),
	'calendar9' => array(
		'title' => 'Ex.calendar',
		'block_usage' => '#calendar9([pagename|*][,yyyymm][,0-6])',
		'inline_usage' => ''
	),
	'calendar_viewer' => array(
		'title' => 'Calendar viewer',
		'block_usage' => '#calendar_viewer([yyyy-mm|n|this],[past|futur|view][,<Separater>])',
		'inline_usage' => ''
	),
	'capture' => array(
		'title' => 'Partial capture',
		'block_usage' => "#caputer(ID){{\n<Wiki text>\n}}",
		'inline_usage' => ''
	),
	'chat' => array(
		'title' => 'Insert AjaxChat',
		'block_usage' => '#chat([staypos:r][,height:<Height>][,id:ChatID])',
		'inline_usage' => ''
	),
	'clear' => array(
		'title' => 'Clear floating',
		'block_usage' => '#clear',
		'inline_usage' => ''
	),
	'code' => array(
		'title' => 'Source code display',
		'block_usage' => "#code([<Type>][,[\d]-[\d]][,number|nonumber][,outline|nooutline][,block|noblock][,literal|noliteral][,comment|nocomment][,menu|nomenu][,icon|noicon][,link|nolink]){{\n<Source code>\n}}",
		'inline_usage' => ''
	),
	'comment' => array(
		'title' => 'One-line comment',
		'block_usage' => '#comment([noname][,nodate][,above|,below][,cols:<cols>][,multi:<lines>])',
		'inline_usage' => ''
	),
	'contents' => array(
		'title' => 'Contents',
		'block_usage' => '#contents',
		'inline_usage' => ''
	),
	'counter' => array(
		'title' => 'Access counter',
		'block_usage' => '#counter',
		'inline_usage' => '&counter([total|today|yesterday]);'
	),
	'edit' => array(
		'title' => 'Edit link',
		'block_usage' => '',
		'inline_usage' => '&edit([<Page name>{[,nolabel][,noicon]}]){<Label>};'
	),
	'exifshowcase' => array(
		'title' => 'Image list',
		'block_usage' => '#exifshowcase([<Extraction pattern>][,Left|,Center|,Right][,Wrap|,Nowrap][,Around][,nolink][,noimg][,<Width>x<Height>][,<Ratio>%][,info][,nomapi][,nokash][,noexif][,reverse][,sort][,col:<Cols>][,row:<Rows>][,<Rows>])',
		'inline_usage' => '&exifshowcase([<Extraction pattern>][,nolink][,noimg][,<Width>x<Height>][,<Ratio>%][,info][,nomapi][,nokash][,noexif][,reverse][,sort][,col:<Cols>][,row:<Rows>][,<Rows>]);'
	),
	'footnotes' => array(
		'title' => 'Show or Config footnotes',
		'block_usage' => '#footnotes([<Catrgory>:<Mark>:][,<Catrgory>:<Mark>:]...)'."\n".'#footnotes([force][,nobr][,nohr][,<Target category>[,catrgory]]...)',
		'inline_usage' => ''
	),
	'fusen' => array(
		'title' => 'Tag panel',
		'block_usage' => '#fusen([refresh:<Refresh interval(sec)>][,height:<Height of tag area>][,off])',
		'inline_usage' => ''
	),
	'googlemaps2' => array(
		'title' => 'GoogleMap',
		'block_usage' => '#googlemaps2([mapname=<Map name>][,width=<Width>][,height=<Height>][,lat=<Latitude>][,lng=<Longitude>][,zoom=<Scale>][,mapctrl=<none|normal|smallzoom|small|large>][,type=<normal|satellite|hybrid>][,typectrl=<none|normal>][,scalectrl=<none|normal>][,overviewctrl=<none|normal>][,crossctrl=<none|show>][,overviewwidth=<Height>][,overviewheight=<Width>][,togglemarker=<true|false>][,noiconname=<Label without icon>][,dbclickzoom=<true|false>][,continuouszoom=<true|false>][,geoxml=<URL of KML or GeoRSS>][,googlebar=<true|false>][,importicon=][,backlinkmarker=<true|false>][,wikitag=<none|show>][,autozoom=<true|false>])',
		'inline_usage' => '&googlemaps2([mapname=<Map name>][,width=<Width>][,height=<Height>][,lat=<Latitude>][,lng=<Longitude>][,zoom=<Scale>][,mapctrl=<none|normal|smallzoom|small|large>][,type=<normal|satellite|hybrid>][,typectrl=<none|normal>][,scalectrl=<none|normal>][,overviewctrl=<none|normal>][,crossctrl=<none|show>][,overviewwidth=<Height>][,overviewheight=<Width>][,togglemarker=<true|false>][,noiconname=<Label without icon>][,dbclickzoom=<true|false>][,continuouszoom=<true|false>][,geoxml=<URL of KML or GeoRSS>][,googlebar=<true|false>][,importicon=][,backlinkmarker=<true|false>][,wikitag=<none|show>][,autozoom=<true|false>]);'
	),
	'googlemaps2_insertmarker' => array(
		'title' => '',
		'block_usage' => '#googlemaps2_insertmarker([mapname=<Map name>][,direction=<up|down>])',
		'inline_usage' => ''
	),
	'iframe' => array(
		'title' => 'Insert <IFRAME>',
		'block_usage' => '#iframe(<URL>[,style:<CSS Text>][,iestyle:<CSS Text>])',
		'inline_usage' => '&iframe(<URL>[,style:<CSS Text>][,iestyle:<CSS Text>]);'
	),
	'include' => array(
		'title' => 'Insert another page',
		'block_usage' => '#include(<Page name>[,title|,notitle])',
		'inline_usage' => ''
	),
	'isbn' => array(
		'title' => 'Amazon',
		'block_usage' => '#isbn(<ASIN>[,clear][,img][,info][,header][,left])',
		'inline_usage' => '&isbn(<ASIN>[,simg][,mimg][,limg])[{<Display text>}];'
	),
	'jsmath' => array(
		'title' => 'Expression(jsMath)',
		'block_usage' => "#jsmath([mimeTeX][,AMSmath][,AMSsymbols][,autobold][,boldsymbo][,verb][,smallFonts][,noImageFonts][,lobal][,noGlobal][,noCache][,CHMmode][,spriteImageFonts])[{{\n<Expression>\n}}]",
		'inline_usage' => '&jsmath([mimeTeX][,AMSmath][,AMSsymbols][,autobold][,boldsymbo][,verb][,smallFonts][,noImageFonts][,lobal][,noGlobal][,noCache][,CHMmode][,spriteImageFonts]){<Expression>};'
	),
	'lastmod' => array(
		'title' => 'Page last updated time',
		'block_usage' => '',
		'inline_usage' => '&lastmod([<Page name>]);'
	),
	'lookup' => array(
		'title' => 'InterWiki form',
		'block_usage' => '#lookup(<InterWikiName>[,<Button name>[,<Initial value>]])',
		'inline_usage' => ''
	),
	'ls2' => array(
		'title' => 'Page list',
		'block_usage' => '#ls2([[<Base page name>][,title][,include][,reverse][,compact][,link][,<Alias of link>])',
		'inline_usage' => ''
	),
	'lsx' => array(
		'title' => 'Ex. page list',
		'block_usage' => '#lsx([[prefix:]<Base page name>][,num:<Display number>][,depth:<Display hierarchy number>][,hierarchy][,basename][,tree:[leaf|dir]][,sort:[name|date]][,reverse][,non_list][,except:<Exclusion page regex>][,filter:<Inclusion page regex>][,date][,new][,order][,info:[date|new]][,contents][,include][,tag:<Tag>][,notitle])',
		'inline_usage' => ''
	),
	'memo' => array(
		'title' => 'Simple memo',
		'block_usage' => '#memo',
		'inline_usage' => ''
	),
	'moblog' => array(
		'title' => 'moblog Cron',
		'block_usage' => '#moblog',
		'inline_usage' => ''
	),
	'new' => array(
		'title' => 'Newly arrived formating',
		'block_usage' => '',
		'inline_usage' => "&new([<Page name>][,nolink]);\n&new([nodate][,class:<Class name>]){<Date string>};"
	),
	'navi' => array(
		'title' => 'Page navigation',
		'block_usage' => '#navi([<Top page name>])',
		'inline_usage' => ''
	),
	'newpage' => array(
		'title' => 'Page making form',
		'block_usage' => '#newpage([this|<Base page name>])',
		'inline_usage' => ''
	),
	'noattach' => array(
		'title' => 'Hide attached file list',
		'block_usage' => '#noattach',
		'inline_usage' => ''
	),
	'noautolink' => array(
		'title' => 'Disabled Auto link',
		'block_usage' => '#noautolink',
		'inline_usage' => ''
	),
	'nocontents' => array(
		'title' => 'Disabled Contents insertion',
		'block_usage' => '#nocontents',
		'inline_usage' => ''
	),
	'nofollow' => array(
		'title' => 'NOFOLLOW,NOINDEX',
		'block_usage' => '#nofollow',
		'inline_usage' => ''
	),
	'noheader' => array(
		'title' => 'Hide page header',
		'block_usage' => '#noheader',
		'inline_usage' => ''
	),
	'nopagecomment' => array(
		'title' => 'Disabled Page comment',
		'block_usage' => '#nopagecomment',
		'inline_usage' => ''
	),
	'norelated' => array(
		'title' => 'Hide related',
		'block_usage' => '#norelated',
		'inline_usage' => ''
	),
	'online' => array(
		'title' => 'Number of online users',
		'block_usage' => '#online',
		'inline_usage' => '&online;'
	),
	'pagepopup' => array(
		'title' => 'Pop up page link',
		'block_usage' => '',
		'inline_usage' => '&pagepopup(<Page name>)[{<Display text>}];'
	),
	'pcomment' => array(
		'title' => 'Ex. One-line comment',
		'block_usage' => '#pcomment([Comment saved page][,Display number][,noname][,nodate][,above|,below][,reply][,cols:<cols>][,multi:<lines>])',
		'inline_usage' => ''
	),
	'popular' => array(
		'title' => 'Several popular',
		'block_usage' => '#popular([[Number],[Off the page],[today|1|yesterday|-1|total|0],[<Base page name>],[0|1]])',
		'inline_usage' => ''
	),
	'pre' => array(
		'title' => 'Preformatted',
		'block_usage' => "#pre(){{\n<Preformatted text>\n}}",
		'inline_usage' => ''
	),
	'random' => array(
		'title' => 'Random jump',
		'block_usage' => '#random([<Display text>])',
		'inline_usage' => ''
	),
	'recent' => array(
		'title' => 'Several recent',
		'block_usage' => '#recent([<Base page name>][,<Number>][,<Future day>][,<UID>])',
		'inline_usage' => ''
	),
	'region' => array(
		'title' => 'Fold text',
		'block_usage' => "#region(<Title>){{\n<Folded Wiki text>\n}}",
		'inline_usage' => ''
	),
	'related' => array(
		'title' => 'Back link list',
		'block_usage' => '#related([<Max count>[,nopassage][,notitle][,context][,context:<Max bytes>/<Max Parts>][,separate][,highlight]])',
		'inline_usage' => ''
	),
	'relatedview' => array(
		'title' => 'Quotations from referer',
		'block_usage' => '#relatedview([noautolink][,nowikiname][,eachpage][,search:<PageName or Regex with "#">][,nosearch:<PageName or Regex with "#">])',
		'inline_usage' => ''
	),
	'rsslink' => array(
		'title' => 'RSS link',
		'block_usage' => '',
		'inline_usage' => '&rsslink([[<Base page name>][[,1.0|,2.0|,atom][,<Output number>]]]);'
	),
	'ruby' => array(
		'title' => 'Ruby',
		'block_usage' => '',
		'inline_usage' => '&ruby(<Ruby>){<Target text>};'
	),
	'search' => array(
		'title' => 'Search form',
		'block_usage' => '#search([Word 1[,Word 2][,Word n]...)',
		'inline_usage' => ''
	),
	'setlang' => array(
		'title' => 'Language specification',
		'block_usage' => "#setlang(ja|zh|cn|ko){{\n<Wiki text>\n}}",
		'inline_usage' => '&setlang(ja|zh|cn|ko){<Text>};'
	),
	'showrss' => array(
		'title' => 'External RSS display',
		'block_usage' => '#showrss(<RSS URL>[,[default|menubar|recent][,[<Cache TTL>[,<Show Timestamp;0|1>[,<Show Discription;0|1>[,<Number>]]]]])',
		'inline_usage' => ''
	),
	'siteimage' => array(
		'title' => 'External site thumbnail',
		'block_usage' => '#siteimage(<External site URL>[,nolink][,target:<Target name>][,size:s|m|l][,around][,right|center|left])',
		'inline_usage' => '&siteimage(<External site URL>[,nolink][,target:<Target name>][,size:s|m|l]);'
	),
	'skin_changer' => array(
		'title' => 'Select Skin',
		'block_usage' => '#skin_changer',
		'inline_usage' => '&skin_changer([<Skin name>])[{<Display text>}];'
	),
	'tag' => array(
		'title' => 'Set Tag, Tag cloud',
		'block_usage' => '#tag([<Tag cloud maximum display number>])',
		'inline_usage' => '&tag(<Tag 1>[,<Tag 2>][,<Tag n>]...);'
	),
	'tdiary' => array(
		'title' => 'tDiary theme',
		'block_usage' => '#tdiary(<tDiary theme name>)',
		'inline_usage' => ''
	),
	'temp' => array(
		'title' => 'Temporary template',
		'block_usage' => "#temp(<Template name>[,Arg 1][,Arg 2]...){{\n<Wiki text>\n}}",
		'inline_usage' => ''
	),
	'tracker' => array(
		'title' => 'Report form',
		'block_usage' => '#tracker([<Definition name>][,<Base page name>])',
		'inline_usage' => ''
	),
	'tracker_list' => array(
		'title' => 'Report list',
		'block_usage' => '#tracker_list([<Definition name>][,[<Base page name>][,[[<Sorting item>]:[SORT_ASC|SORT_DESC]][,<Max number>]]]])',
		'inline_usage' => ''
	),
	'ucomedit' => array(
		'title' => 'Edit EXIF Comments',
		'block_usage' => '#ucomedit(filename[,formonly])',
		'inline_usage' => ''
	),
	'urlbookmark' => array(
		'title' => 'Simple links',
		'block_usage' => '#urlbookmark([notitle][,nodate][,above|,below])',
		'inline_usage' => ''
	),
	'vote' => array(
		'title' => 'Vote form',
		'block_usage' => '#vote([<Item 1>][,<Item 2>]...[,#add][,#notimestamp][,#sort][,#ksort][,#nomail])',
		'inline_usage' => ''
	),
	'xoopsblock' => array(
		'title' => 'XOOPS block display',
		'block_usage' => '#xoopsblock([<Block ID 1>][,<Block ID 2>]...)',
		'inline_usage' => ''
	),
	'yahoo' => array(
		'title' => 'Yahoo! API',
		'block_usage' => '#yahoo(web|img|mov,<Word>[,type:and|or|word][,max:<Display number>][,col:<Cols>][,row:<Rows>][,target:<Target name>])',
		'inline_usage' => ''
	),
);
