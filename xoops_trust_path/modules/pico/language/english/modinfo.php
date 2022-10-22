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
    define( $constpref . '_USE_WRAPSMODE', 'Enable wraps mode. All content should be wrapped in a div' );
    define( $constpref . '_ERR_DOCUMENT_404', 'Custom page for error : 404 Not Found ' );
    define( $constpref . '_ERR_DOCUMENT_404DSC', 'Example: <b>xoops_root_path/404.html</b> redirects to public root path <b>' . XOOPS_ROOT_PATH . '/404.html</b> and<br> <b>xoops_trust_path/404.html</b> redirects to trust patht <b>' . XOOPS_TRUST_PATH . '/404.html</b>' );
    define( $constpref . '_USE_REWRITE', 'Enable mod_rewrite' );
    define( $constpref . '_USE_REWRITEDSC', 'Rename .htaccess.rewrite_wraps (with wraps) or htaccess.rewrite_normal (without wraps) to .htaccess under XOOPS_ROOT_PATH/modules/(dirname)/' );
    define( $constpref . '_WRAPSAUTOREGIST', 'Enable auto-registering HTML wrapped files into DB as contents' );
    define( $constpref . '_AUTOREGISTCLASS', 'Class name to register/unregister HTML wrapped files' );
    define( $constpref . '_TOP_MESSAGE', 'Description of TOP category' );
    define( $constpref . '_TOP_MESSAGEDEFAULT', '' );
    define( $constpref . '_MENUINMODULETOP', 'Display menu (index) in the top of this module' );
    define( $constpref . '_LISTASINDEX', "Display contents index in category's top" );
    define( $constpref . '_LISTASINDEXDSC', 'YES - a table of contents (TOC) is auto-generated and displayed on the main page.<br> NO - the content with the highest priority (order, weight) is displayed instead of TOC.' );
    define( $constpref . '_SHOW_BREADCRUMBS', 'Display the breadcrumbs' );
    define( $constpref . '_SHOW_PAGENAVI', 'Display the page navigation' );
    define( $constpref . '_SHOW_PRINTICON', 'Display the printer friendly icon' );
    define( $constpref . '_SHOW_TELLAFRIEND', 'Display tell a friend icon' );
    define( $constpref . '_SEARCHBYUID', 'Enable the concept of authorship' );
    define( $constpref . '_SEARCHBYUIDDSC', 'Publications are listed in user profile. This option can be turned off for static content.' );
    define( $constpref . '_USE_TAFMODULE', 'Use the module "tellafriend".' );
    define( $constpref . '_FILTERS', 'Default filter set' );
    define( $constpref . '_FILTERSDSC', 'Specified filter names separated by "|" (pipe). Example: xcode|smiley|nl2br|textwiki' );
    define( $constpref . '_FILTERSDEFAULT', '' );
    define( $constpref . '_FILTERSF', 'Forced filters' );
    define( $constpref . '_FILTERSFDSC', 'input filter names separated with ,(comma). filter:LAST means the filter is passed in the last phase. The other filters are passed in the first phase.' );
    define( $constpref . '_FILTERSP', 'Prohibited filters' );
    define( $constpref . '_FILTERSPDSC', 'Select filter names separated with ,(comma).' );
    define( $constpref . '_SUBMENU_SC', 'Show contents in submenu' );
    define( $constpref . '_SUBMENU_SCDSC', 'In the default mode, only the categories are displayed. If you activate this function, the content marked "menu" will also be displayed.' );
    define( $constpref . '_SITEMAP_SC', 'Display the content in the sitemap module' );
    define( $constpref . '_USE_VOTE', 'Use the VOTE function' );
    define( $constpref . '_GUESTVOTE_IVL', 'Vote from guests' );
    define( $constpref . '_GUESTVOTE_IVLDSC', 'Set this value to 0, to disable guest voting. Any other number refers to the time (sec.) needed to allow a second message from the same IP.' );
    define( $constpref . '_HTMLHEADER', 'Common HTML header' );
    define( $constpref . '_ALLOWEACHHEAD', 'Allow custom HTML header (CSS, JS) for each content' );
    define( $constpref . '_CSS_URI', 'URI of CSS file for this module' );
    define( $constpref . '_CSS_URIDSC', 'a relative or absolute path can be defined. Default value: {mod_url}/index.php?page=main_css' );
    define( $constpref . '_IMAGES_DIR', 'Directory for image files' );
    define( $constpref . '_IMAGES_DIRDSC', 'the relative path should be defined in the module directory. Default value : images' );
    define( $constpref . '_BODY_EDITOR', 'WYSIWYG HTML editor to simplify the content creation' );
    define( $constpref . '_HTMLPR_EXCEPT', 'Select Trusted Users Groups.' );
    define( $constpref . '_HTMLPR_EXCEPTDSC', 'HTML sanitization with HTMLPurifier to secure against XSS attacks.' );
    define( $constpref . '_HISTORY_P_C', 'How many revisions are stored in DB' );
    define( $constpref . '_MLT_HISTORY', 'Minimum lifetime of each revisions (sec)' );
    define( $constpref . '_BRCACHE', 'Cache life time for image files (only with wraps mode)' );
    define( $constpref . '_BRCACHEDSC', 'Files other than HTML will be cached by web browser in this second (0 means disabled)' );
    define( $constpref . '_EF_CLASS', 'class for extra_fields' );
    define( $constpref . '_EF_CLASSDSC', 'Override the handler for extra_fields. The default value is PicoExtraFields' );
    define( $constpref . '_URIM_CLASS', 'class mapping URI' );
    define( $constpref . '_URIM_CLASSDSC', 'Override the URI mapper. The default value is PicoUriMapper' );
    define( $constpref . '_EFIMAGES_DIR', 'directory for extra_fields' );
    define( $constpref . '_EFIMAGES_DIRDSC', 'set relative path from XOOPS_ROOT_PATH. Create and chmod 777 the directory first. default) uploads/(module dirname)' );
    define( $constpref . '_EFIMAGES_SIZE', 'pixels for extra images' );
    define( $constpref . '_EFIMAGES_SIZEDSC', '(main_width)x(main_height) (small_width)x(small_height) default) 480x480 150x150' );
    define( $constpref . '_IMAGICK_PATH', 'Path for ImageMagick binaries' );
    define( $constpref . '_IMAGICK_PATHDSC', 'Leave blank normal, or set it like /usr/X11R6/bin/' );
    define( $constpref . '_COM_DIRNAME', 'Comment-integration: dirname of d3forum' );
    define( $constpref . '_COM_FORUM_ID', 'Comment-integration: forum ID' );
    define( $constpref . '_COM_VIEW', 'View of comment-integration' );
    define( $constpref . '_COM_ORDER', 'Order of comment-integration' );
    define( $constpref . '_COM_POSTSNUM', 'Max posts displayed in comment-integration' );

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
