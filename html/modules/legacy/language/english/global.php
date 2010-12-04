<?php
// $Id: global.php,v 1.1 2007/05/15 02:35:28 minahito Exp $

define('_TOKEN_ERROR', 'Alert ! This prevent you from instantiating a malformed request or post. Please, submit again to confirm!');
define('_SYSTEM_MODULE_ERROR', 'Following Modules are not installed.');
define('_INSTALL','Install');
define('_UNINSTALL','Uninstall');
define('_SYS_MODULE_UNINSTALLED','Required(Not Installed)');
define('_SYS_MODULE_DISABLED','Required(Disabled)');
define('_SYS_RECOMMENDED_MODULES','Recommended Module');
define('_SYS_OPTION_MODULES','Optional Module');
define('_UNINSTALL_CONFIRM','Are you sure to uninstall system module?');

//%%%%%%	File Name mainfile.php 	%%%%%
define("_PLEASEWAIT","Please Wait");
define("_FETCHING","Loading...");
define("_TAKINGBACK","Taking you back to where you were....");
define("_LOGOUT","Logout");
define("_SUBJECT","Subject");
define("_MESSAGEICON","Message Icon");
define("_COMMENTS","Comments");
define("_POSTANON","Post Anonymously");
define("_DISABLESMILEY","Disable smiley");
define("_DISABLEHTML","Disable html");
define("_PREVIEW","Preview");

define("_GO","Go!");
define("_NESTED","Nested");
define("_NOCOMMENTS","No Comments");
define("_FLAT","Flat");
define("_THREADED","Threaded");
define("_OLDESTFIRST","Oldest First");
define("_NEWESTFIRST","Newest First");
define("_MORE","more...");
define("_MULTIPAGE","To have your article span multiple pages, insert the word <font color=red>[pagebreak]</font> (with brackets) in the article.");
define("_IFNOTRELOAD","If the page does not automatically reload, please click <a href='%s'>here</a>");
define("_WARNINSTALL2","WARNING: Directory %s exists on your server. Please remove this directory for security reasons.");
define("_WARNINWRITEABLE","WARNING: File %s is writeable by the server. Please change the permission of this file for security reasons. in Unix (444), in Win32 (read-only)");
define('_WARNPHPENV','WARNING: php.ini parameter "%s" is set to "%s". %s');
define('_WARNSECURITY','(It may cause a security problem)');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define("_PROFILE","Profile");
define("_POSTEDBY","Posted by");
define("_VISITWEBSITE","Visit Website");
define("_SENDPMTO","Send Private Message to %s");
define("_SENDEMAILTO","Send Email to %s");
define("_ADD","Add");
define("_REPLY","Reply");
define("_DATE","Date");   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define("_MAIN","Main");
define("_MANUAL","Manual");
define("_INFO","Info");
define("_CPHOME","Control Panel Home");
define("_YOURHOME","Home Page");

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define("_WHOSONLINE","Who's Online");
define('_GUESTS', 'Guests');
define('_MEMBERS', 'Members');
define("_ONLINEPHRASE","%s user(s) are online");
define("_ONLINEPHRASEX","%s user(s) are browsing %s");
define("_CLOSE","Close");  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define("_QUOTEC","Quote:");

//%%%%%%	File Name admin.php 	%%%%%
define("_NOPERM","Sorry, you don't have the permission to access this area.");

//%%%%%		Common Phrases		%%%%%
define("_NO","No");
define("_YES","Yes");
define("_EDIT","Edit");
define("_DELETE","Delete");
define("_VIEW","View");
define("_SUBMIT","Submit");
define("_MODULENOEXIST","Selected module does not exist!");
define("_ALIGN","Align");
define("_LEFT","Left");
define("_CENTER","Center");
define("_RIGHT","Right");
define("_FORM_ENTER", "Please enter %s");
// %s represents file name
define("_MUSTWABLE","File %s must be writable by the server!");
// Module info
define('_PREFERENCES', 'Preferences');
define("_VERSION", "Version");
define("_DESCRIPTION", "Description");
define("_ERRORS", "Errors");
define("_NONE", "None");
define('_ON','on');
define('_READS','reads');
define('_WELCOMETO','Welcome to %s');
define('_SEARCH','Search');
define('_ALL', 'All');
define('_TITLE', 'Title');
define('_OPTIONS', 'Options');
define('_QUOTE', 'Quote');
define('_LIST', 'List');
define('_LOGIN','User Login');
define('_USERNAME','Username: ');
define('_PASSWORD','Password: ');
define("_SELECT","Select");
define("_IMAGE","Image");
define("_SEND","Send");
define("_CANCEL","Cancel");
define("_ASCENDING","Ascending order");
define("_DESCENDING","Descending order");
define('_BACK', 'Back');
define('_NOTITLE', 'No title');
define('_RETURN_TOP', 'returns to the top');

/* Image manager */
define('_IMGMANAGER','Image Manager');
define('_NUMIMAGES', '%s images');
define('_ADDIMAGE','Add Image File');
define('_IMAGENAME','Name:');
define('_IMGMAXSIZE','Max size allowed (bytes):');
define('_IMGMAXWIDTH','Max width allowed (pixels):');
define('_IMGMAXHEIGHT','Max height allowed (pixels):');
define('_IMAGECAT','Category:');
define('_IMAGEFILE','Image file:');
define('_IMGWEIGHT','Display order in image manager:');
define('_IMGDISPLAY','Display this image?');
define('_IMAGEMIME','MIME type:');
define('_FAILFETCHIMG', 'Could not get uploaded file %s');
define('_FAILSAVEIMG', 'Failed storing image %s into the database');
define('_NOCACHE', 'No Cache');
define('_CLONE', 'Clone');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define("_STARTSWITH", "Starts with");
define("_ENDSWITH", "Ends with");
define("_MATCHES", "Matches");
define("_CONTAINS", "Contains");

//%%%%%%	File Name commentform.php 	%%%%%
define("_REGISTER","Register");

//%%%%%%	File Name xoopscodes.php 	%%%%%
define("_SIZE","SIZE");  // font size
define("_FONT","FONT");  // font family
define("_COLOR","COLOR");  // font color
define("_EXAMPLE","SAMPLE");
define("_ENTERURL","Enter the URL of the link you want to add:");
define("_ENTERWEBTITLE","Enter the web site title:");
define("_ENTERIMGURL","Enter the URL of the image you want to add.");
define("_ENTERIMGPOS","Now, enter the position of the image.");
define("_IMGPOSRORL","'R' or 'r' for right, 'L' or 'l' for left, or leave it blank.");
define("_ERRORIMGPOS","ERROR! Enter the position of the image.");
define("_ENTEREMAIL","Enter the email address you want to add.");
define("_ENTERCODE","Enter the codes that you want to add.");
define("_ENTERQUOTE","Enter the text that you want to be quoted.");
define("_ENTERTEXTBOX","Please input text into the textbox.");
define("_ALLOWEDCHAR","Allowed max chars length: ");
define("_CURRCHAR","Current chars length: ");
define("_PLZCOMPLETE","Please complete the subject and message fields.");
define("_MESSAGETOOLONG","Your message is too long.");

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 second');
define('_SECONDS', '%s seconds');
define('_MINUTE', '1 minute');
define('_MINUTES', '%s minutes');
define('_HOUR', '1 hour');
define('_HOURS', '%s hours');
define('_DAY', '1 day');
define('_DAYS', '%s days');
define('_WEEK', '1 week');
define('_MONTH', '1 month');

define('_HELP', "Help");

//%%%%%		   %%%%%
define('_CATEGORY', "Category");
define('_TAG', "Tag");
define('_STATUS', "Status");
define('_STATUS_DELETED', "Deleted");
define('_STATUS_REJECTED', "Rejected");
define('_STATUS_POSTED', "Posted");
define('_STATUS_PUBLISHED', "Published");

//%%%%% Group %%%%%
define('_GROUP', "Group");
define('_MEMBER', "Member");
define('_GROUP_RANK_GUEST', "Guest");
define('_GROUP_RANK_ASSOCIATE', "Associate");
define('_GROUP_RANK_REGULAR', "Regular");
define('_GROUP_RANK_STAFF', "Staff");
define('_GROUP_RANK_OWNER', "Owner");

?>