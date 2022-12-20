<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
    $mydirname = 'pico';
}
$constpref = '_MI_' . strtoupper( $mydirname );

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED' ) ) {

    define( $constpref . '_LOADED', 1 );

    // The name of this module
    define( $constpref . '_NAME', 'pico' );

    // A brief description of this module
    define( $constpref . '_DESC', 'Content management with CKEditor, versioning, revision history, diff and granular permissions.' );

    // admin menus
    define( $constpref . '_ADMENU_CONTENTSADMIN', 'Contents' );
    define( $constpref . '_ADMENU_CATEGORYACCESS', 'Permissions' );
    define( $constpref . '_ADMENU_IMPORT', 'Import-Sync' );
    define( $constpref . '_ADMENU_TAGS', 'Tags' );
    define( $constpref . '_ADMENU_EXTRAS', 'Extra' );
    define( $constpref . '_ADMENU_MYLANGADMIN', 'Language' );
    define( $constpref . '_ADMENU_MYTPLSADMIN', 'Templates' );
    define( $constpref . '_ADMENU_MYBLOCKSADMIN', 'Blocks-Permissions' );
    define( $constpref . '_ADMENU_MYPREFERENCES', 'Preferences' );

    // configurations
    define( $constpref . '_USE_WRAPSMODE', 'Enable wraps mode. <br>Note: it is recommend to use a div wrapper for the content.' );
    define( $constpref . '_ERR_DOCUMENT_404', 'Custom page for error : 404 Not Found ' );
    define( $constpref . '_ERR_DOCUMENT_404DSC', 'Example: <b>root_path/404.html</b> redirects to public root path <br><i>' . XOOPS_ROOT_PATH . '/404.html</i> <br><b>trust_path/404.html</b> redirects to trust path<br><i>' . XOOPS_TRUST_PATH . '/404.html</i>' );
    define( $constpref . '_USE_REWRITE', 'Enable mod_rewrite' );
    define( $constpref . '_USE_REWRITEDSC', 'Rename .htaccess under public_html/modules/(dirname)/<br>Default: <i>htaccess.rewrite_normal</i><br>Wraps: <i>htaccess.rewrite_wraps</i>' );
    define( $constpref . '_WRAPSAUTOREGIST', 'Enable auto-save HTML wrapped files in the database as contents' );
    define( $constpref . '_AUTOREGISTCLASS', 'Class name to register/unregister HTML wrapped files' );
    define( $constpref . '_TOP_MESSAGE', 'Description of TOP category [ html ]' );
    define( $constpref . '_TOP_MESSAGEDEFAULT', '' );
    define( $constpref . '_MENUINMODULETOP', 'Display menu (index) in the top of this module' );
    define( $constpref . '_LISTASINDEX', "Display table of contents (TOC) or custom page" );
    define( $constpref . '_LISTASINDEXDSC', 'YES - a table of contents (TOC) is auto-generated and displayed on the main page.<br> NO - the content with the highest priority (order, weight) is displayed instead of TOC.' );
    define( $constpref . '_SHOW_BREADCRUMBS', 'Enable breadcrumbs' );
    define( $constpref . '_SHOW_RSS', 'Enable RSS' );
    define( $constpref . '_SHOW_PAGENAVI', 'Enable page navigation' );
    define( $constpref . '_SHOW_PRINTICON', 'Enable printer friendly icon' );
    define( $constpref . '_SHOW_TELLAFRIEND', 'Enable tell a friend icon' );
    define( $constpref . '_SEARCHBYUID', 'Enable the concept of authorship' );
    define( $constpref . '_SEARCHBYUIDDSC', 'Publications are listed in user profile. This option can be turned off for static content.' );
    define( $constpref . '_USE_TAFMODULE', 'Use the module "tellafriend".<br>Please refer to X-Update Manager for download and deploy.' );
    define( $constpref . '_FILTERS', 'Default filter set' );
    define( $constpref . '_FILTERSDSC', 'Specify filter names separated by pipe "|". Example : xcode|smiley|nl2br|textwiki' );
    define( $constpref . '_FILTERSDEFAULT', '' );
    define( $constpref . '_FILTERSF', 'Forced filters' );
    define( $constpref . '_FILTERSFDSC', 'Specify filter names separated with comma ","<br>filter:LAST means all other filters have higher priority.' );
    define( $constpref . '_FILTERSP', 'Prohibited filters' );
    define( $constpref . '_FILTERSPDSC', 'Specify filter names separated with comma ","' );
    define( $constpref . '_SUBMENU_SC', 'Enable contents in submenu' );
    define( $constpref . '_SUBMENU_SCDSC', 'Default mode only displays the categories. This feature displays all content marked "menu".' );
    define( $constpref . '_SITEMAP_SC', 'Enable content in Sitemap module' );
    define( $constpref . '_USE_VOTE', 'Enable Voting feature' );
    define( $constpref . '_GUESTVOTE_IVL', 'Enable Voting from guests' );
    define( $constpref . '_GUESTVOTE_IVLDSC', 'Allow votes from the same IP with required delay in seconds. Disable with value set to 0. Default value: 86400' );
    define( $constpref . '_HTMLHEADER', 'Common HTML header [ CSS, JS ]' );
    define( $constpref . '_ALLOWEACHHEAD', 'Enable custom HTML header for each content' );
    define( $constpref . '_CSS_URI', 'CSS file for this module' );
    define( $constpref . '_CSS_URIDSC', 'Relative or absolute path can be defined. Default value : {mod_url}/index.php?page=main_css' );
    define( $constpref . '_IMAGES_DIR', 'Directory for image files' );
    define( $constpref . '_IMAGES_DIRDSC', 'Relative path to module in the public directory e.g. dirname/images. Default value : images' );
    define( $constpref . '_BODY_EDITOR', 'WYSIWYG HTML editor to simplify content creation' );
    define( $constpref . '_HTMLPR_EXCEPT', 'Select Trusted Users Groups.' );
    define( $constpref . '_HTMLPR_EXCEPTDSC', 'HTML sanitization with HTMLPurifier to secure against XSS attacks.' );
    define( $constpref . '_HISTORY_P_C', 'Document Version Control - How many revisions are stored in database. Default value: 4' );
    define( $constpref . '_MLT_HISTORY', 'Document Version Control - Minimum lifetime for each revision. Default value in seconds: 300 ' );
    define( $constpref . '_BRCACHE', 'Cache lifetime for image files (only with wraps mode)' );
    define( $constpref . '_BRCACHEDSC', 'Files other than HTML are cached by the web browser. Default value in seconds (60 minutes): 3600<br>PageSpeed Insights recommend a minimum cache time of one week and preferably up to one year for static assets.' );
    define( $constpref . '_EF_CLASS', 'Class for extra_fields' );
    define( $constpref . '_EF_CLASSDSC', 'Developers can override the class name and method name parameters of the handler for extra_fields. The default value is PicoExtraFields' );
    define( $constpref . '_URIM_CLASS', 'Class mapping URI' );
    define( $constpref . '_URIM_CLASSDSC', 'Developers can override the URI mapper. The default value is PicoUriMapper' );
    define( $constpref . '_EFIMAGES_DIR', 'Directory for extra_fields' );
    define( $constpref . '_EFIMAGES_DIRDSC', 'Relative path to the public directory e.g. public_html/.<br>First, create and chmod 777 the directory. Default value: uploads/dirname' );
    define( $constpref . '_EFIMAGES_SIZE', 'Pixels for extra images' );
    define( $constpref . '_EFIMAGES_SIZEDSC', 'main_width x main_height small_width x small_height. Default value: 480x480 150x150' );
    define( $constpref . '_IMAGICK_PATH', 'Path for ImageMagick binaries' );
    define( $constpref . '_IMAGICK_PATHDSC', 'Leave blank, or set it like /usr/X11R6/bin/' );
    define( $constpref . '_COM_DIRNAME', 'Comment-integration: dirname of d3forum' );
    define( $constpref . '_COM_FORUM_ID', 'Comment-integration: forum ID' );
    define( $constpref . '_COM_VIEW', 'Comment-integration : View' );
    define( $constpref . '_COM_ORDER', 'Comment-integration : Order' );
    define( $constpref . '_COM_POSTSNUM', 'Comment-integration : Maximum number of comments per page' );

    // blocks
    define( $constpref . '_BNAME_MENU', 'Menu' );
    define( $constpref . '_BNAME_CONTENT', 'Content' );
    define( $constpref . '_BNAME_LIST', 'List' );
    define( $constpref . '_BNAME_SUBCATEGORIES', 'Subcategories' );
    define( $constpref . '_BNAME_MYWAITINGS', 'My waiting posts' );
    define( $constpref . '_BNAME_TAGS', 'Tags' );

    // Notify Categories
    define( $constpref . '_NOTCAT_GLOBAL', 'global' );
    define( $constpref . '_NOTCAT_GLOBALDSC', 'notifications of all categories' );
    define( $constpref . '_NOTCAT_CATEGORY', 'category' );
    define( $constpref . '_NOTCAT_CATEGORYDSC', 'notifications of this category' );
    define( $constpref . '_NOTCAT_CONTENT', 'content' );
    define( $constpref . '_NOTCAT_CONTENTDSC', 'notifications of this content' );

    // Each Notifications
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENT', '' );
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'Notify when new posts or modifications wait approval (Just notify admins or moderators)' );
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}: waiting' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENT', 'new content' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENTCAP', 'Notify when new content is created. (approved content only)' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE} : a new content {CONTENT_SUBJECT}' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENT', 'new content' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENTCAP', 'Notify when a new content is created. (approved content only)' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} a new content {CONTENT_SUBJECT}' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENT', 'new comment' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENTCAP', 'Notify when new comment is posted. (approved content only)' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENTSBJ', '[{X_SITENAME}] {X_MODULE} : a new comment' );
}
