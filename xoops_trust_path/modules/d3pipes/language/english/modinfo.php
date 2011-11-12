<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3pipes' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","D3 PIPES");

// A brief description of this module
define($constpref."_DESC","Flexible syndication module");

// admin menus
define($constpref.'_ADMENU_PIPE','Pipes') ;
define($constpref.'_ADMENU_CACHE','Cache') ;
define($constpref.'_ADMENU_CLIPPING','Clippings') ;
define($constpref.'_ADMENU_JOINT','Joint initials') ;
define($constpref.'_ADMENU_JOINTCLASS','Class initials') ;
define($constpref.'_ADMENU_MYLANGADMIN','Languages') ;
define($constpref.'_ADMENU_MYTPLSADMIN','Templates') ;
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocks/Permissions') ;
define($constpref.'_ADMENU_MYPREFERENCES','Preferences') ;

// blocks
define($constpref.'_BNAME_ASYNC','List entries (Async)') ;
define($constpref.'_BNAME_SYNC','List entries (Sync)') ;

// configs
define($constpref.'_INDEXTOTAL','Total entries in the top of this module');
define($constpref.'_INDEXEACH','Max entries from a pipe in the top of this module');
define($constpref.'_INDEXKEEPPIPE','Displays upper pipes as possible in the top of this module');
define($constpref.'_ENTRIESAPIPE','Entries a view of each pipes');
define($constpref.'_ENTRIESAPAGE','Entries a page in clipping list');
define($constpref.'_ENTRIESARSS','Entries a RSS/Atom');
define($constpref.'_ENTRIESSMAP','Entries of sitemap xml for google etc');
define($constpref.'_ARCB_FETCHED','Auto expire by fetched time (days)');
define($constpref.'_ARCB_FETCHEDDSC','Specify days clippings should be removed. 0 means disable auto-expire. Clippings with comment/highlight are never removed.');
define($constpref.'_INTERNALENC','Internal encoding');
define($constpref.'_FETCHCACHELT','Fech cache life time (sec)');
define($constpref.'_REDIRECTWARN','Alert if rss/atom URI will be redirected');
define($constpref.'_SNP_MAXREDIRS','Max redirections for Snoopy');
define($constpref.'_SNP_MAXREDIRSDSC','After building pipes successfully, set this option 0');
define($constpref.'_SNP_PROXYHOST','Hostname of proxy server');
define($constpref.'_SNP_PROXYHOSTDSC','specify it by FQDN. Normally leave blank here');
define($constpref.'_SNP_PROXYPORT','Port of proxy server');
define($constpref.'_SNP_PROXYUSER','Username for proxy server');
define($constpref.'_SNP_PROXYPASS','Password for proxy server');
define($constpref.'_SNP_CURLPATH','curl path (default: /usr/bin/curl)');
define($constpref.'_TIDY_PATH','tidy path (default: /usr/bin/tidy)');
define($constpref.'_XSLTPROC_PATH','xsltproc path (default: /usr/bin/xsltproc)');
define($constpref.'_UPING_SERVERS','Update Ping Servers');
define($constpref.'_UPING_SERVERSDSC','Write a RPC end point starting with "http://" a line.<br />If you want to send extendedPing, append " E" after the URI.');
define($constpref.'_UPING_SERVERSDEF',"http://blogsearch.google.com/ping/RPC2 E\nhttp://rpc.weblogs.com/RPC2 E\nhttp://ping.blo.gs/ E");
define($constpref.'_CSS_URI','CSS URI');
define($constpref.'_CSS_URIDSC','relative or absolute path can be set. default: {mod_url}/index.css');
define($constpref.'_IMAGES_DIR','Directory for image files');
define($constpref.'_IMAGES_DIRDSC','relative path should be set in the module directory. default: images');
define($constpref.'_COM_DIRNAME','Comment-integration: dirname of d3forum');
define($constpref.'_COM_FORUM_ID','Comment-integration: forum ID');
define($constpref.'_COM_VIEW','View of comment-integration');
define($constpref.'_COM_ORDER','Order of comment-integration');
define($constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

}


?>