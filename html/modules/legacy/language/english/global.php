<?php

// ADMIN QUICK LINKS
define( '_AD_ACCOUNT' , 'Account');
define( '_AD_AVATAR' , 'Avatar');
define( '_AD_BANNERS' , 'Ad Banners');
define( '_AD_BLOCKS' , 'Blocks');
define( '_AD_CENSOR' , 'Word censor');
define( '_AD_CLOSE_SITE' , 'Close site');
define( '_AD_DEBUG' , 'Debug');
define( '_AD_EMOTICON' , 'Smilies');
define( '_AD_LOCALE' , 'language'); /* localization Localize your app Translation Translation (language )*/
define( '_AD_MAIL_SETUP' , 'Mail-setup');
define( '_AD_MAILING' , 'Mailing');
define( '_AD_META_SEO' , 'Meta SEO');
define( '_AD_MODULES' , 'Modules');
define( '_AD_PROFILE' , 'Profile');
define( '_AD_RANKS' , 'Ranking');
define( '_AD_SEARCH' , 'Search');
define( '_AD_SETTINGS' , 'Settings');
define( '_AD_TEMPLATES' , 'Templates');
define( '_AD_TEMPLATE_SET' , 'Template set');
define( '_AD_THEMES' , 'Theme');
define( '_AD_URL_REWRITE' , 'URL Rewrite');
define( '_AD_USER_SEARCH' , 'Search user'); /* ( find user) */


// ADMIN BLOCKS
define( '_AD_BLOCK_ACCOUNT' , 'Admin Account');
define( '_AD_BLOCK_ADMIN' , 'Admin Theme Options');
define( '_AD_BLOCK_ASIDE' , 'Admin Menu');
define( '_AD_BLOCK_MENU' , 'Modules Menu');
define( '_AD_BLOCK_ONLINE' , 'Users Online');
define( '_AD_BLOCK_OVERVIEW' , 'Overview Stats');
define( '_AD_BLOCK_PHP' , 'PHP Settings');
define( '_AD_BLOCK_SEARCH' , 'Action Search');
define( '_AD_BLOCK_SERVER' , 'Server Environment');
define( '_AD_BLOCK_THEME' , 'Select Theme Color');
define( '_AD_BLOCK_TIPS' , 'Modules Help Tips');
define( '_AD_BLOCK_TOOLTIP' , 'Disable action tooltip');

// ADMIN NAV
define( '_LINKS_TIP' , 'Admin Quick Links');
define( '_THEME_TIP' , 'Dark or Light Theme');
define( '_TIME_TIP' , 'Current timestamp');

// ADMIN DASHBOARD - TABS
define( '_ABOUT' , 'About XOOPSCube');
define( '_START' , 'Get Started');
define( '_SOURCE' , 'Source code');

define( '_WAP_LICENSE' , 'Open Source Licenses');
define( '_WAP_LICENSE_DSC' , "Open-source licensed software is usually available for free, but this is not always the case. XCL's source code is designed to be publicly available. Anyone can view, modify and distribute the code as they see fit.
Modules and themes are released under the BSD, GPL and MIT licenses.<br>
The BSD license of the core XCube allows proprietary use and permits incorporation of the software into proprietary products.");

define( '_WAP_BUNDLE' , 'XCL Bundle Package');
define( '_WAP_BUNDLE_DSC' , "XCL is a general-purpose, open-source web application maintained on GitHub. One of the main advantages of the XCL Bundle package is that it is ready to use right out of the box. You can manage your data and utilize cloud storage by following the simple instructions provided by elFinder, the web-based file manager. There are no contracts, hidden costs, limitations, or restrictions.");

define( '_WAP_B2C' , 'B2B and B2C Services');
define( '_WAP_B2C_DSC' , "For instance, individual developers and agencies might create their own free or paid packages, personalize them, and offer specific features tailored to a wide range of industries. Professional distributors may charge service fees to cover administrative or processing costs, as well as technical support and maintenance services.");

// System
define( '_TOKEN_ERROR' , 'Alert ! This prevent you from instantiating a malformed request or post. Please, submit again to confirm!');
define( '_SYSTEM_MODULE_ERROR' , 'The following modules are required.');
define( '_INSTALL' , 'Install');
define( '_UNINSTALL' , 'Uninstall');
define( '_SYS_MODULE_UNINSTALLED' , 'Required (Not Installed)');
define( '_SYS_MODULE_DISABLED' , 'Required (Disabled)');
define( '_SYS_RECOMMENDED_MODULES' , 'Recommended Module');
define( '_SYS_OPTION_MODULES' , 'Optional Module');
define( '_UNINSTALL_CONFIRM' , 'Are you sure to uninstall system module?');

//%%%%%%	File Name mainfile.php 	%%%%%
define( '_PLEASEWAIT' , 'Please Wait');
define( '_FETCHING' , 'Loading...');
define( '_TAKINGBACK' , 'Taking you back to where you were....');
define( '_LOGOUT' , 'Logout');
define( '_SUBJECT' , 'Subject');
define( '_MESSAGEICON' , 'Message Icon');
define( '_COMMENTS' , 'Comments');
define( '_POSTANON' , 'Post Anonymously');
define( '_DISABLESMILEY' , 'Disable smiley');
define( '_DISABLEHTML' , 'Disable html');
define( '_PREVIEW' , 'Preview');

define( '_GO' , 'Apply');
define( '_NESTED' , 'Nested');
define( '_NOCOMMENTS' , 'No Comments');
define( '_FLAT' , 'Flat');
define( '_THREADED' , 'Threaded');
define( '_OLDESTFIRST' , 'Oldest First');
define( '_NEWESTFIRST' , 'Newest First');
define( '_MORE' , 'more...');
define( '_MULTIPAGE' , 'To have your article span multiple pages, insert the word <span style="color:red">[pagebreak]</span> (with brackets) in the article.');
define( '_IFNOTRELOAD' , "If the page does not automatically reload, please [ <a href='%s'>click here</a> ]");
define( '_WARNINSTALL2' , '<span>WARNING: <b>Install</b> folder exists!<br><span class="alert-install">{0}</span><br>Edit the file <span class="alert-install">install/passwd.php</span> to add a password or delete this folder for security reasons.</span>');
define( '_WARNINWRITEABLE' , "<span>WARNING: <b>Mainfile</b> is writable !<br><span class='alert-install'>{0}</span><br> Change this file permissions for security reasons: Unix (0444), Windows (read-only)</span>");
define( '_WARNPHPENV' , 'WARNING: php.ini parameter "%s" is set to "%s". %s');
define( '_WARNSECURITY' , '(It may cause a security problem)');
define( '_WARN_INSTALL_TIP' , 'Activate the Preload — For development purposes only!<br>This allows you to keep the <code>install</code> directory.<br>Remember to chmod read-only <code>mainfile.php</code> and <strong>delete</strong> <code>install</code> to prevent any security issues.');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define( '_PROFILE' , 'Profile');
define( '_POSTEDBY' , 'Posted by');
define( '_VISITWEBSITE' , 'Visit Website');
define( '_SENDPMTO' , 'Send Private Message to %s');
define( '_SENDEMAILTO' , 'Send Email to %s');
define( '_ADD' , 'Add');
define( '_REPLY' , 'Reply');
define( '_DATE' , 'Date');   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define( '_MAIN' , 'Main');
define( '_MANUAL' , 'Manual');
define( '_INFO' , 'Info');
define( '_CPHOME' , 'Control Panel');
define( '_YOURHOME' , 'Home Page');

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define( '_WHOSONLINE' , "Users Online");
define( '_GUESTS' , 'Guests');
define( '_MEMBERS' , 'Members');
define( '_ONLINEPHRASE' , '%s user(s) are online');
define( '_ONLINEPHRASEX' , '%s user(s) are browsing %s');
define( '_CLOSE' , 'Close');  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define( '_QUOTEC' , 'Quote:');

//%%%%%%	File Name admin.php 	%%%%%
define( '_NOPERM' , "Sorry, you don't have the permission to access this area.");

//%%%%%		Common ACTIONS		%%%%%
define( '_NO' , 'No');
define( '_YES' , 'Yes');
define( '_EDIT' , 'Edit');
define( '_DELETE' , 'Delete');
define( '_VIEW' , 'View');
define( '_SAVE' , 'Save');
define( '_SUBMIT' , 'Submit');

define( '_ALIGN' , 'Align');
define( '_LEFT' , 'Left');
define( '_CENTER' , 'Center');
define( '_RIGHT' , 'Right');
define( '_UPLOAD' , 'UPLOAD');
define( '_DOWNLOAD' , 'DOWNLOAD');

define( '_MODULENOEXIST' , 'Selected module does not exist!');
define( '_FORM_ENTER' , 'Please enter %s');

// %s represents file name
define( '_MUSTWABLE' , 'File %s must be writable by the server!');
// Module info
define( '_PREFERENCES' , 'Preferences');
define( '_VERSION' , 'Version');
define( '_DESCRIPTION' , 'Description');
define( '_ERRORS' , 'Errors');
define( '_NONE' , 'None');
define( '_ON' , 'on');
define( '_READS' , 'reads');
define( '_WELCOMETO' , 'Welcome to %s');
define( '_SEARCH' , 'Search');
define( '_ALL' , 'All');
define( '_TITLE' , 'Title');
define( '_OPTIONS' , 'Options');
define( '_QUOTE' , 'Quote');
define( '_LIST' , 'List');
define( '_LOGIN' , 'User Login');
define( '_USERNAME' , 'Username');
define( '_PASSWORD' , 'Password');
define( '_SELECT' , 'Select');
define( '_IMAGE' , 'Image');
define( '_SEND' , 'Send');
define( '_CANCEL' , 'Cancel');
define( '_ASCENDING' , 'Ascending order');
define( '_DESCENDING' , 'Descending order');
define( '_BACK' , 'Back');
define( '_NOTITLE' , 'No title');
define( '_RETURN_TOP' , '↑ Top');

/* Image manager */
define( '_IMGMANAGER' , 'Image Manager');
define( '_NUMIMAGES' , '%s images');
define( '_ADDIMAGE' , 'Add Image File');
define( '_IMAGENAME' , 'Name:');
define( '_IMGMAXSIZE' , 'Max size allowed (bytes):');
define( '_IMGMAXWIDTH' , 'Max width allowed (pixels):');
define( '_IMGMAXHEIGHT' , 'Max height allowed (pixels):');
define( '_IMAGECAT' , 'Category:');
define( '_IMAGEFILE' , 'Image file:');
define( '_IMGWEIGHT' , 'Display order in image manager:');
define( '_IMGDISPLAY' , 'Display this image?');
define( '_IMAGEMIME' , 'MIME type:');
define( '_FAILFETCHIMG' , 'Could not get uploaded file %s');
define( '_FAILSAVEIMG' , 'Failed storing image %s into the database');
define( '_NOCACHE' , 'No Cache');
define( '_CLONE' , 'Clone');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define( '_STARTSWITH' , 'Starts with');
define( '_ENDSWITH' , 'Ends with');
define( '_MATCHES' , 'Matches');
define( '_CONTAINS' , 'Contains');

//%%%%%%	File Name commentform.php 	%%%%%
define( '_REGISTER' , 'Register');

//%%%%%%	File Name xoopscodes.php 	%%%%%
define( '_SIZE' , 'SIZE');  // font size
define( '_FONT' , 'FONT');  // font family
define( '_COLOR' , 'COLOR');  // font color
define( '_EXAMPLE' , 'SAMPLE');
define( '_ENTERURL' , 'Enter the URL of the link you want to add:');
define( '_ENTERWEBTITLE' , 'Enter the web site title:');
define( '_ENTERIMGURL' , 'Enter the URL of the image you want to add.');
define( '_ENTERIMGPOS' , 'Now, enter the position of the image.');
define( '_IMGPOSRORL' , "'R' or 'r' for right, 'L' or 'l' for left, or leave it blank.");
define( '_ERRORIMGPOS' , 'ERROR! Enter the position of the image.');
define( '_ENTEREMAIL' , 'Enter the email address you want to add.');
define( '_ENTERCODE' , 'Enter the codes that you want to add.');
define( '_ENTERQUOTE' , 'Enter the text that you want to be quoted.');
define( '_ENTERTEXTBOX' , 'Please input text into the textbox.');
define( '_ALLOWEDCHAR' , 'Allowed max chars length: ');
define( '_CURRCHAR' , 'Current chars length: ');
define( '_PLZCOMPLETE' , 'Please complete the subject and message fields.');
define( '_MESSAGETOOLONG' , 'Your message is too long.');

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define( '_SECOND' , '1 second');
define( '_SECONDS' , '%s seconds');
define( '_MINUTE' , '1 minute');
define( '_MINUTES' , '%s minutes');
define( '_HOUR' , '1 hour');
define( '_HOURS' , '%s hours');
define( '_DAY' , '1 day');
define( '_DAYS' , '%s days');
define( '_WEEK' , '1 week');
define( '_MONTH' , '1 month');

define( '_ACTION' , 'Action');
define( '_HELP' , 'Help');
define( '_MENU' , 'Menu');

//%%%%%		   %%%%%
define( '_CATEGORY' , 'Category');
define( '_TAG' , 'Tag');
define( '_STATUS' , 'Status');
define( '_STATUS_DELETED' , 'Deleted');
define( '_STATUS_REJECTED' , 'Rejected');
define( '_STATUS_POSTED' , 'Posted');
define( '_STATUS_PUBLISHED' , 'Published');

//%%%%% Group %%%%%
define( '_GROUP' , 'Group');
define( '_MEMBER' , 'Member');
define( '_GROUP_RANK_GUEST' , 'Guest');
define( '_GROUP_RANK_ASSOCIATE' , 'Associate');
define( '_GROUP_RANK_REGULAR' , 'Regular');
define( '_GROUP_RANK_STAFF' , 'Staff');
define( '_GROUP_RANK_OWNER' , 'Owner');

//%%%%% System %%%%%
define( '_DEBUG_MODE' , 'Debug');
define( '_DEBUG_MODE_PHP' , 'PHP');
define( '_DEBUG_MODE_SQL' , 'SQL');
define( '_DEBUG_MODE_SMARTY' , 'Smarty');
define( '_DEBUG_MODE_DESC' , 'Disable debug mode in production. Admin > Settings > Debug mode [Off].');

//%%%%% System Control Panel %%%%%
define( '_ACCOUNT' , 'Account');
define( '_BANNERS' , 'Banners');
//define( '_BLOCKS' , 'Blocks');
define( '_GROUPS' , 'Groups');
define( '_MAILING', 'Mailing');
define( '_MODULES' , 'Modules');
define( '_RANKS' , 'Ranks');
define( '_TRANSLATION' , 'Translation');
define( '_USERS' , 'Users');

define( '_SYS_OS' , 'OS');
define( '_SYS_SERVER' , 'Server');
define( '_SYS_USERAGENT' , 'User agent');
define( '_SYS_PHPVERSION' , 'PHP version');
define( '_SYS_MYSQLVERSION' , 'MySQL version');
