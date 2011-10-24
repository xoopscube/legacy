<?php

define('_MD_D3PIPES_H2_INDEX','Index');
define('_MD_D3PIPES_H2_LATESTHEADLINES','The latest entries');
define('_MD_D3PIPES_H2_EACHPIPE','List entries of the pipe');
define('_MD_D3PIPES_H2_CLIPLIST','List of clippings');
define('_MD_D3PIPES_H2_CLIPPING','Detail of the clipping');

define('_MD_D3PIPES_JOINT_FETCH','Fetching from outside');
define('_MD_D3PIPES_JOINT_BLOCK','Fetching/Parsing from blocks');
define('_MD_D3PIPES_JOINT_PARSE','Parsing XML');
define('_MD_D3PIPES_JOINT_CACHE','Cache');
define('_MD_D3PIPES_JOINT_PING','Update Ping');
define('_MD_D3PIPES_JOINT_UTF8TO','Transfer encoding from UTF-8');
define('_MD_D3PIPES_JOINT_UTF8FROM','Transfer encoding to UTF-8');
define('_MD_D3PIPES_JOINT_REPLACE','Text replacement');
define('_MD_D3PIPES_JOINT_CLIP','Clipping into local');
define('_MD_D3PIPES_JOINT_FILTER','Filter entries by keywords');
define('_MD_D3PIPES_JOINT_REASSIGN','Reassign');
define('_MD_D3PIPES_JOINT_SORT','Sorting');
define('_MD_D3PIPES_JOINT_UNION','Union pipes');

define('_MD_D3PIPES_N4J_FETCH','Input URI of RSS/ATOM');
define('_MD_D3PIPES_N4J_PARSE','Input type of XML (eg. RDF/RSS/ATOM)');
define('_MD_D3PIPES_N4J_CACHE','Input cache time (sec)');
define('_MD_D3PIPES_N4J_UTF8TO','Normally, input internal encoding');
define('_MD_D3PIPES_N4J_UTF8FROM','Normally, input the encoding of the XML');
define('_MD_D3PIPES_N4J_FILTER','Input patterns/keywords to filter');
define('_MD_D3PIPES_N4J_REASSIGN','Input rules for re-assignment');
define('_MD_D3PIPES_N4J_UNION','pipe ids sep. by comma');

define('_MD_D3PIPES_N4J_WRITEURL','Input URL');
define('_MD_D3PIPES_N4J_WRITEPREG','Input regex by perl format');
define('_MD_D3PIPES_N4J_WRITEPOSIX','Input regex by POSIX format');
define('_MD_D3PIPES_N4J_CID','Category ID');
define('_MD_D3PIPES_N4J_UID','User ID');
define('_MD_D3PIPES_N4J_MAXENTRIES','Max entries');
define('_MD_D3PIPES_N4J_EACHENTRIES','Entries a pipe');
define('_MD_D3PIPES_N4J_KEEPPIPEINFO','Keep pipe info');
define('_MD_D3PIPES_N4J_TARGETMODULE','Target module');
define('_MD_D3PIPES_N4J_EXTRAOPTIONS','Extra options');
define('_MD_D3PIPES_N4J_ENTRIESFROMCLIP','Entries from clippings');
define('_MD_D3PIPES_N4J_CLIPLIFETIME','Lifetime of clippings (use preferences if blank)');
define('_MD_D3PIPES_N4J_WITHDESCRIPTION','Get description also');
define('_MD_D3PIPES_N4J_REPLACEFROM','search');
define('_MD_D3PIPES_N4J_REPLACETO','replacement');
define('_MD_D3PIPES_N4J_XSLTPATH','Path to XSLT (url or physical)');

define('_MD_D3PIPES_CLASS_FETCHSNOOPY','by Snoopy (recommended)');
define('_MD_D3PIPES_CLASS_FETCHFOPEN','by URL fopen');
define('_MD_D3PIPES_CLASS_PARSEKEITHXML','KeithXML (recommended)');
define('_MD_D3PIPES_CLASS_PARSESIMPLEHTML','HTML parser by &lt;hn&gt; tag');
define('_MD_D3PIPES_CLASS_PARSELINKHTML','HTML parser by &lt;a&gt; tag');
define('_MD_D3PIPES_CLASS_FILTERPCRE','Select by regex of pcre');
define('_MD_D3PIPES_CLASS_FILTERPCRE_EXCEPT','Exclude by regex of pcre');
define('_MD_D3PIPES_CLASS_FILTERMBREGEX','Select by mbregex');
define('_MD_D3PIPES_CLASS_FILTERMBREGEX_EXCEPT','Exclude by mbregex');
define('_MD_D3PIPES_CLASS_CLIPMODULEDB','Keep into DB');
define('_MD_D3PIPES_CLASS_REASSIGNCONTENTENCODED','description to content');
define('_MD_D3PIPES_CLASS_REASSIGNALLOWHTML','Allow HTML');
define('_MD_D3PIPES_CLASS_REASSIGNSTRIPTAGS','Strip HTML tags');
define('_MD_D3PIPES_CLASS_REASSIGNDEFAULTLINK','Specify URL of link');
define('_MD_D3PIPES_CLASS_REASSIGNHTMLENTITYDECODE','Fix extra htmlentity');
define('_MD_D3PIPES_CLASS_REASSIGNTRUNCATE','Truncate');
define('_MD_D3PIPES_CLASS_CACHETRUSTPATH','Make cache under trust/cache/');
define('_MD_D3PIPES_CLASS_PINGXMLRPC2','XMLRPC2 weblogUpdates.ping');
define('_MD_D3PIPES_CLASS_SORTPUBTIMEDSC','Published time DESC');
define('_MD_D3PIPES_CLASS_SORTHEADLINESTRASC','Dictionary order ASC');
define('_MD_D3PIPES_CLASS_SORTHEADLINEINTASC','Integer order ASC');
define('_MD_D3PIPES_CLASS_UNIONMERGESORT','Aggregation and Sort');
define('_MD_D3PIPES_CLASS_UNIONSEPARATED','Parallel without Sort');
define('_MD_D3PIPES_CLASS_UNIONTHEOTHERD3PIPES','From the other d3pipes');

define('_MD_D3PIPES_TH_PUBTIME','published time');
define('_MD_D3PIPES_TH_PIPENAME','name');
define('_MD_D3PIPES_TH_HEADLINE','headline');
define('_MD_D3PIPES_TH_LINKURL','Link URI');
define('_MD_D3PIPES_TH_DESCRIPTION','Description');
define('_MD_D3PIPES_TH_ACTIONTOCLIPPING','Action to this clipping');

define('_MD_D3PIPES_LABEL_HIGHLIGHTCLIPPING','Highlight it');
define('_MD_D3PIPES_LABEL_DELETECLIPPING','Delete this clipping');
define('_MD_D3PIPES_LABEL_VISIBLECLIPPING','Display this clipping');

define('_MD_D3PIPES_BTN_UPDATE','Update');

define('_MD_D3PIPES_LINK_SITEMAPS','Sitemaps');

define('_MD_D3PIPES_FMT_LINKTOCLIPLIST','Go to list of clippings (Total: %s entries)');
define('_MD_D3PIPES_FMT_EXTERNALLINK','External link to %s');

define('_MD_D3PIPES_MSG_CLIPPINGUPDATED','The clipping updated successfully');
define('_MD_D3PIPES_MSG_CLIPPINGDELETED','The clipping deleted successfully');
define('_MD_D3PIPES_MSG_CLIPPINGCANNOTDELETED','This clipping cannot be deleted because of existing comment(s). Remove/move comment(s) first.');

define('_MD_D3PIPES_ERR_INVALIDCLIPPINGID','Invalid clipping ID');
define('_MD_D3PIPES_ERR_INVALIDPIPEID','Invalid pipe ID');
define('_MD_D3PIPES_ERR_PERMISSION','Permission error');
define('_MD_D3PIPES_ERR_INVALIDPIPEIDINBLOCK','Invalid pipe_id. Go to blocks admin and edit the pipe_id');
define('_MD_D3PIPES_ERR_REDIRECTED','The URI of RSS/Atom has been redirected. you\'d better change the URI of the pipe for reducing useless traffic.');
define('_MD_D3PIPES_ERR_ERRORBEFOREPARSE','Perhaps, error has occurred in fetching stage. confim it by pipe admin');
define('_MD_D3PIPES_ERR_PARSETYPEMISMATCH','XML type is not matched. confim it by pipe admin');
define('_MD_D3PIPES_ERR_CACHEFOLDERNOTWRITABLE','Cache folder does not exist or is not writable');
define('_MD_D3PIPES_ERR_INVALIDURIINFETCH','Invalid URI specified as fetch joint\'s option');
define('_MD_D3PIPES_ERR_CANTCONNECTINFETCH','Cannot access to outer contents');
define('_MD_D3PIPES_ERR_DOUBTFULPROXY','Confirm the setting of proxy');
define('_MD_D3PIPES_ERR_DOUBTFULCURLPATH','Confirm the setting of curl path');
define('_MD_D3PIPES_ERR_URLFOPENINFETCH','You cannot use "fopen" under allow_url_fopen=off');
define('_MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK','Invalid dirname on the block joint');
define('_MD_D3PIPES_ERR_INVALIDFILEINBLOCK','Invalid blockfile on the block joint');
define('_MD_D3PIPES_ERR_INVALIDFUNCINBLOCK','Invalid blockfunc on the block joint');


?>