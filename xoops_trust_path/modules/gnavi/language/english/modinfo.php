<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'gnavi' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

define($constpref."_NAME","gnavi");

// A brief description of this module
define($constpref."_DESC","Make regional guide with GoogleMap.");

// Names of blocks for this module (Not all module has blocks)
define( $constpref."_BNAME_RECENT","Recent Photos");
define( $constpref."_BNAME_HITS","Top Photos");
define( $constpref."_BNAME_RANDOM","Random Photo");
define( $constpref."_BNAME_RECENT_P","Recent Photos with thumbnails");
define( $constpref."_BNAME_HITS_P","Top Photos with thumbnails");
define( $constpref."_BNAME_MENU","Menu");
define( $constpref."_BNAME_ARCHIVE","Archive");

// Config Items
define( $constpref."_CFG_PHOTOSPATH" , "Path to photos" ) ;
define( $constpref."_CFG_DESCPHOTOSPATH" , "Path from the directory installed XOOPS.<br />(The first character must be '/'. The last character should not be '/'.)<br />This directory's permission is 777 or 707 in unix." ) ;
define( $constpref."_CFG_THUMBSPATH" , "Path to thumbnails" ) ;
define( $constpref."_CFG_DESCTHUMBSPATH" , "Same as 'Path to photos'." ) ;
define( $constpref."_CFG_IMAGINGPIPE" , "Package treating images" ) ;
define( $constpref."_CFG_DESCIMAGINGPIPE" , "Almost all PHP environments can use GD. But GD is functionally inferior than 2 other packages.<br />It is best to use ImageMagick or NetPBM if you can." ) ;
define( $constpref."_CFG_FORCEGD2" , "Force GD2 conversion" ) ;
define( $constpref."_CFG_DESCFORCEGD2" , "Even if the GD is a bundled version of PHP, it force GD2(truecolor) conversion.<br />Some configured PHP fails to create thumbnails in GD2<br />This configuration is significant only when using GD" ) ;
define( $constpref."_CFG_IMAGICKPATH" , "Path of ImageMagick" ) ;
define( $constpref."_CFG_DESCIMAGICKPATH" , "Although the full path to 'convert' should be written, leave it blank in most environments.<br />This configuration is significant only when using ImageMagick" ) ;
define( $constpref."_CFG_NETPBMPATH" , "Path of NetPBM" ) ;
define( $constpref."_CFG_DESCNETPBMPATH" , "Alhough the full path to 'pnmscale' should be written, leave it blank in most environments.<br />This configuration is significant only when using NetPBM" ) ;
define( $constpref."_CFG_POPULAR" , "Hits to be Popular" ) ;
define( $constpref."_CFG_NEWDAYS" , "Days between displaying icon of 'new'&'update'" ) ;
define( $constpref."_CFG_NEWPHOTOS" , "Number of Photos as New on Top Page" ) ;
define( $constpref."_CFG_DEFAULTORDER" , "Default order in category's view" ) ;
define( $constpref."_CFG_PERPAGE" , "Displayed Articles per Page" ) ;
define( $constpref."_CFG_DESCPERPAGE" , "Input selectable numbers separated with '|'<br />eg) 10|20|50|100" ) ;
define( $constpref."_CFG_ALLOWNOIMAGE" , "Allow a submit without images" ) ;
define( $constpref."_CFG_MAKETHUMB" , "Make Thumbnail Image" ) ;
define( $constpref."_CFG_DESCMAKETHUMB" , "When you change 'No' to 'Yes', You'd better 'Redo thumbnails'." ) ;
define( $constpref."_CFG_THUMBSIZE" , "Size of thumbnails (pixel)" ) ;
define( $constpref."_CFG_THUMBRULE" , "Calculation rule for building thumbnails" ) ;
define( $constpref."_CFG_WIDTH" , "Max photo width" ) ;
define( $constpref."_CFG_DESCWIDTH" , "This means the photo's width to be resized.<br />If you use GD without truecolor, this means the limitation of width." ) ;
define( $constpref."_CFG_HEIGHT" , "Max photo height" ) ;
define( $constpref."_CFG_DESCHEIGHT" , "This means the photo's height to be resized.<br />If you use GD without truecolor, this means the limitation of height." ) ;
define( $constpref."_CFG_FSIZE" , "Max file size" ) ;
define( $constpref."_CFG_DESCFSIZE" , "The limitation of the size of uploading file.(bytes)" ) ;
define( $constpref."_CFG_MIDDLEPIXEL" , "Max image size in single view" ) ;
define( $constpref."_CFG_DESCMIDDLEPIXEL" , "Specify (width)x(height)<br />(eg. 480x480)" ) ;
define( $constpref."_CFG_LIQUIDIMG" , "Draw option when two or more images" ) ;
define( $constpref."_CFG_DESCLIQUIDIMG" , "A specified image reducing displays each image according to the maximum image size in the above-mentioned single view at two three time." ) ;
define( $constpref."_CFG_ADDPOSTS" , "The number added User's posts by posting a photo." ) ;
define( $constpref."_CFG_DESCADDPOSTS" , "Normally, 0 or 1. Under 0 mean 0" ) ;
define( $constpref."_CFG_CATONSUBMENU" , "Register top categories into submenu" ) ;
define( $constpref."_CFG_NAMEORUNAME" , "Poster name displayed" ) ;
define( $constpref."_CFG_DESCNAMEORUNAME" , "Select which 'name' is displayed" ) ;
define( $constpref."_CFG_INDEXPAGE" , "Set module top page" ) ;
define( $constpref."_CFG_VIEWCATTYPE" , "Type of view in category" ) ;
define( $constpref."_CFG_COLSOFTABLEVIEW" , "Number of columns in table view" ) ;

define( $constpref."_CFG_SHOWPARENT" , "Display the article in the parents category" ) ;
define( $constpref."_CFG_DESCSHOWPARENT" , "Please keep effective when you display the article on the subcategory at the category view." ) ;

define( $constpref."_CFG_ALLOWEDEXTS" , "File extensions that can be uploaded" ) ;
define( $constpref."_CFG_DESCALLOWEDEXTS" , "Input extensions with separator '|'. (eg 'jpg|jpeg|gif|png') .<br />All characters must be lowercase. Don't insert periods or spaces<br />Never add php or phtml etc." ) ;
define( $constpref."_CFG_ALLOWEDMIME" , "MIME Types can be uploaded" ) ;
define( $constpref."_CFG_DESCALLOWEDMIME" , "Input MIME Types with separator '|'. (eg 'image/gif|image/jpeg|image/png')<br />If you want to be checked by MIME Type, leave this blank" ) ;

define( $constpref."_CFG_BODY_EDITOR" , "Advanced Text Editor" ) ;
define( $constpref."_CFG_DESCBODY_EDITOR" , "The administartors can use it. It is necessary to up-load the editor separately in the html/common folder." ) ;
define( $constpref."_CFG_ADDINFO" , "Add the item of each article" ) ;
define( $constpref."_CFG_DESCADDINFO" , "(Example  [cost:$50] , [Regular holiday:Sunday])" ) ;


define( $constpref."_CFG_USEVOTE" , "Use vote function" ) ;
define( $constpref."_CFG_DESCUSEVOTE" , "The user can apply the evaluation to each article. The sorting function in the order of the evaluation becomes effective." ) ;
define( $constpref."_CFG_USEGMAP" , "Use GoogleMap" ) ;
define( $constpref."_CFG_DESCGMAP" , "The map management function is added to contents. The location information can be added to each page." ) ;
define( $constpref."_CFG_GMAPKEY" , "GoogleMapAPI Key" ) ;
define( $constpref."_CFG_DESCGMAPKEY" , "When GoogleMap is used, GoogleMapAPI Key is needed. Please acquire key from following URL.<br /><a href='http://www.google.com/apis/maps/signup.html'>http://www.google.com/apis/maps/signup.html</a>" ) ;
define( $constpref."_CFG_DEFLAT" , "default Latitude" ) ;
define( $constpref."_CFG_DESCDEFLAT" , "" ) ;
define( $constpref."_CFG_DEFLNG" , "default Longitude" ) ;
define( $constpref."_CFG_DESCDEFLNG" , "" ) ;
define( $constpref."_CFG_DEFZOOM" , "default Zoom Level" ) ;
define( $constpref."_CFG_DESCDEFZOOM" , "" ) ;
define( $constpref."_CFG_DEFMTYPE" , "default Map Type" ) ;
define( $constpref."_CFG_DESCDEFMTYPE" , "Can select Satellite and the Geography maps.In addition, can select a special map of Mars, the moon, and starry sky." ) ;
define( $constpref."_ICON_BYLID" , "The icon of each article can be specified. (Each category usually. )" ) ;
define( $constpref."_CFG_USE_RSS" , "Display RSS feed in the article page." ) ;
define( $constpref."_CFG_DESC_USE_RSS" , "Input num of Feed lines.<br />When this function is made effective, the textbox to input RSS link is displayed on the submit pages. The outline is displayed in the article page.<br />Powerd By <a href='http://code.google.com/intl/ja/apis/ajaxfeeds/'>GoogleAjaxFeedAPI</a>" ) ;
define( $constpref."_CFG_PE_APPKEY" , "Use PlaceEngineAPI" ) ;
define( $constpref."_CFG_DESC_PE_APPKEY" , "PlaceEngine is service to which the present place is presumed with Wifi. Please acquire the application key in the following address to make this function effective and input it to the right. <br /><a href='http://www.placeengine.com/appk' target='_blank'>http://www.placeengine.com/appk</a><br />*Please fill it in to the address of the module on the item of URL. <br />(example:http://xoops.iko-ze.net/modules/gnavi)<br />(Powerd By <a href='http://www.koozyt.com/'>Koozyt</a>)" ) ;


define( $constpref."_CFG_MOBILEMAPSIZE" , "GoogleMap size displayed with portable terminal¡ÊwidthxHeight¡Ë" ) ;
define( $constpref."_CFG_DESCMOBILEMAPSIZE" , "Please input it like 240x180. Portable Map is not made at the uninput. " ) ;
define( $constpref."_CFG_MOBILEAGENT" , "Character string for portable terminal distinction(regular expression)" ) ;
define( $constpref."_CFG_DESCMOBILEAGENT" , "Input the regular expression to distinguish the portable terminal from agent information. <BR>This function is an experimental mounting. When [agent=mobile] is specified for the GET parameter, a portable screen can be displayed by a browser (for debugging). " ) ;
define( $constpref."_CFG_MOBILEENCORDING" , "Encode of character on portable page" ) ;
define( $constpref."_CFG_DESCMOBILEENCORDING" , "Input the encode output to carrying. It might be important for the multi byte. " ) ;
define( $constpref."_CFG_MOBILEUSEQRC" , "Use QR code. (set size)" ) ;
define( $constpref."_CFG_DESCMOBILEUSEQRC" , "The QR code to read to input the value of one or more in the article by carrying is made. It becomes invalid 0, and the input value becomes the size of the QR code. (The recommended value is <B>3</B> or <B>4</B>.)<br />The QR code is preserved in the directory of [qr] following passing specified by [Path to photos]. The QR code is made only initial displaying once of the article. Therefore, please delete and apply the [qr] directory when you change the size here. " ) ;


define( $constpref.'_COM_DIRNAME','Comment-integration: dirname of d3forum');
define( $constpref.'_COM_FORUM_ID','Comment-integration: forum ID');
define( $constpref.'_COM_VIEW','View of Comment-integration');

define( $constpref.'_MAP_DRAW','Draw in the marker with GeoXML.');
define( $constpref.'_DESC_MAP_DRAW','(recommendation: No)It is made to draw in the map display by KML. Please try when processing is heavy etc.It becomes somewhat different movement. ');
define( $constpref.'_INCLUDE_KML','Display of external KML file');
define( $constpref.'_DESC_INCLUDE_KML','specify KML file (.kml,.kmz) that can be displayed with GoogleEarth. Please input it with one URL a line that starts from "http://". <br />Example¡Ë<br />http://xoops.iko-ze.net/modules/gnavi/kml.php');




define( $constpref."_OPT_USENAME" , "Real Name" ) ;
define( $constpref."_OPT_USEUNAME" , "Login Name" ) ;

define( $constpref."_OPT_CALCFROMWIDTH" , "width:specified  height:auto" ) ;
define( $constpref."_OPT_CALCFROMHEIGHT" , "width:auto  width:specified" ) ;
define( $constpref."_OPT_CALCWHINSIDEBOX" , "put in specified size squre" ) ;

define( $constpref."_OPT_VIEWLIST" , "List View" ) ;
define( $constpref."_OPT_VIEWTABLE" , "Table View" ) ;


// Sub menu titles
define( $constpref."_TEXT_SMNAME1","Submit");
define( $constpref."_TEXT_SMNAME2","Popular");
define( $constpref."_TEXT_SMNAME3","Top Rated");
define( $constpref."_TEXT_SMNAME4","My Articles");
define( $constpref."_TEXT_SMNAME5","Show Map");
define( $constpref."_TEXT_SMNAME6","Article list");

// Names of admin menu items
define( $constpref."_ADMENU_MYCATEGOLY","Add/Edit Categories");
define( $constpref."_ADMENU_MYICON","Add/Edit Icons");
define( $constpref."_ADMENU_MYPHOTOMANAGER","Photo Management");
define( $constpref."_ADMENU_MYLADMISSION","Submitted Photos");
define( $constpref."_ADMENU_MYGROUPPERM","Global Permissions");
define( $constpref."_ADMENU_MYCHECKCONFIGS","Check Configuration & Environment");
define( $constpref."_ADMENU_MYBATCH","Batch Register");
define( $constpref."_ADMENU_MYREDOTHUMBS","Rebuild Thumbnails");

define( $constpref.'_ADMENU_MYLANGADMIN' , 'Languages' ) ;
define( $constpref.'_ADMENU_MYTPLSADMIN' , 'Templates' ) ;
define( $constpref.'_ADMENU_MYBLOCKSADMIN' , 'Blocks/Permissions' ) ;
define( $constpref.'_ADMENU_MYPREFERENCES' , 'Preferences' ) ;

// Text for notifications
define( $constpref.'_GLOBAL', 'Global');
define( $constpref.'_GLOBALDSC', 'Global notification options');
define( $constpref.'_CATEGORY', 'Category');
define( $constpref.'_CATEGORYDSC', 'Notification options that apply to the current category');
define( $constpref.'_ITEM', 'article');
define( $constpref.'_ITEMDSC', 'Notification options that apply to the current article');

define( $constpref.'_NOTIFY_GLOBAL_NEWITEM', 'New Photo');
define( $constpref.'_NOTIFY_GLOBAL_NEWITEMCAP', 'Notify me when any new articles are posted');
define( $constpref.'_NOTIFY_GLOBAL_NEWITEMCONTENTCAP', 'Receive notification when a new articles description is posted.');
define( $constpref.'_NOTIFY_GLOBAL_NEWITEMBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : New photo');

define( $constpref.'_NOTIFY_CATEGORY_NEWITEM', 'New Photo');
define( $constpref.'_NOTIFY_CATEGORY_NEWITEMCAP', 'Notify me when a new article is posted to the current category');
define( $constpref.'_NOTIFY_CATEGORY_NEWITEMCONTENTCAP', 'Receive notification when a new article description is posted to the current category');
define( $constpref.'_NOTIFY_CATEGORY_NEWITEMBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : New photo');

}

?>