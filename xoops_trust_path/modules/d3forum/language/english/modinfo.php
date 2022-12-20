<?php

if (defined('FOR_XOOPS_LANG_CHECKER')) {
	$mydirname = 'd3forum';
}
$constpref = '_MI_' . strtoupper($mydirname);

if (defined('FOR_XOOPS_LANG_CHECKER') || !defined($constpref . '_LOADED')) {

    define($constpref . '_LOADED', 1);

    // The name of this module
    define($constpref . '_NAME', 'Forum');

    // A brief description of this module
    define($constpref . '_DESC', 'Duplicatable module to manage Comments and Forums.');

    // Names of blocks for this module (Not all module has blocks)
    define($constpref . '_BNAME_LIST_TOPICS', 'Topics');
    define($constpref . '_BDESC_LIST_TOPICS', 'This block can be duplicated and used for multi-purpose.');
    define($constpref . '_BNAME_LIST_POSTS', 'Posts');
    define($constpref . '_BNAME_LIST_FORUMS', 'Forums');

    // admin menu
    define($constpref . '_ADMENU_CATEGORYACCESS', 'Category Permissions');
    define($constpref . '_ADMENU_FORUMACCESS', 'Forum Permissions');
    define($constpref . '_ADMENU_ADVANCEDADMIN', 'Advanced');
    define($constpref . '_ADMENU_POSTHISTORIES', 'Histories');
    define($constpref . '_ADMENU_MYLANGADMIN', 'Languages');
    define($constpref . '_ADMENU_MYTPLSADMIN', 'Templates');
    define($constpref . '_ADMENU_MYBLOCKSADMIN', 'Blocks Permissions');
    define($constpref . '_ADMENU_MYPREFERENCES', 'Preferences');

    // configurations
    define($constpref . '_TOP_MESSAGE', 'Description of TOP category [ html ]');
    define($constpref . '_TOP_MESSAGEDEFAULT', '<h2>Top Forum</h2><p>To start viewing messages, select a category and then a forum from the selection below.</p>');
    define($constpref . '_SHOW_BREADCRUMBS', 'Enable breadcrumbs');
    define($constpref . '_SHOW_RSS', 'Enable RSS');
    define($constpref . '_DEFAULT_OPTIONS', 'Default filters');
    define($constpref . '_DEFAULT_OPTIONSDSC', 'Specify filter names separated by comma ",". Example: smiley,xcode,br,number_entity<br>Available options: special_entity, html attachsig, u2t_marked');
    define($constpref . '_USENAME', 'Display Username or Real Name');
    define($constpref . '_USENAMEDESC', "Select the name to display : 'uname'(user ID) or 'name'(Real name). Default value (user ID): 'uname'");
    define($constpref . '_USENAME_UNAME', "use'uname'(user ID)");
    define($constpref . '_USENAME_NAME', "use'name'(Real name)");
    define($constpref . '_ALLOW_HTML', 'Enable HTML');
    define($constpref . '_ALLOW_HTMLDSC', 'Beware of the risks of Script injection attack by malicious users. Allow trusted user groups only.');
    define($constpref . '_ALLOW_TEXTIMG', 'Enable external images in comments and posts');
    define($constpref . '_ALLOW_TEXTIMGDSC', 'These images, sometimes called Web Beacons, can be used to track IPs or User-Agents of registered users. External images are not a security threat. However, it is recommended to turn off external images.');
    define($constpref . '_ALLOW_SIG', 'Enable Signature');
    define($constpref . '_ALLOW_SIGDSC', '');
    define($constpref . '_ALLOW_SIGIMG', 'Enable external images in signature');
    define($constpref . '_ALLOW_SIGIMGDSC', 'You can turn off automatic loading of external images as an additional privacy protection.');
    define($constpref . '_USE_VOTE', 'Enable Voting');
    define($constpref . '_USE_SOLVED', 'Enable Solved');
    define($constpref . '_ALLOW_MARK', 'Enable Marking');
    define($constpref . '_ALLOW_HIDEUID', 'Allow Users to Post Anonymously<br>Registered users can check the anonymous option when they write topics and posts.');
    define($constpref . '_POSTS_PER_TOPIC', 'Enable Auto-lock Topic');
    define($constpref . '_POSTS_PER_TOPICDSC', 'Set the maximum posts to automatically lock topic. Default value: 25');
    define($constpref . '_HOT_THRESHOLD', 'Enable Hot Topic Threshold');
    define($constpref . '_HOT_THRESHOLDDSC', 'Set the number of replies that a topic must receive for it to be considered a "hot topic". Default value: 10');
    define($constpref . '_TOPICS_PER_PAGE', 'Topics per page in the view forum');
    define($constpref . '_TOPICS_PER_PAGEDSC', '');
    define($constpref . '_VIEWALLBREAK', 'Topics per page in view all');
    define($constpref . '_VIEWALLBREAKDSC', '');
    define($constpref . '_SELFEDITLIMIT', 'Time limit to edit posts');
    define($constpref . '_SELFEDITLIMITDSC', 'This specifies the amount of time users have to re-edit forum postings. Disable with value set to 0. Default in seconds (4min): 240');
    define($constpref . '_SELFDELLIMIT', 'Time limit to delete posts');
    define($constpref . '_SELFDELLIMITDSC', 'Enable users to delete own posts. Parent posts cannot be removed. Set the value in seconds. Disable delete with value set to 0. ');
    define($constpref . '_CSS_URI', 'CSS file for this module');
    define($constpref . '_CSS_URIDSC', 'Relative or absolute path can be defined. Default value : {mod_url}/index.php?page=main_css');
    define($constpref . '_IMAGES_DIR', 'Directory for image files');
    define($constpref . '_IMAGES_DIRDSC', 'Relative path to module in the public directory e.g. dirname/images. Default value : images');
    define($constpref . '_BODY_EDITOR', 'Editor');
    define($constpref . '_BODY_EDITORDSC', 'WYSIWYG editor will be enabled under only forums allowing HTML. With forums escaping HTML specialchars, xoopsdhtml will be displayed automatically.');
    define($constpref . '_ANONYMOUS_NAME', 'Anonymous Name');
    define($constpref . '_ANONYMOUS_NAMEDSC', 'A pseudonym or alias is a fictitious name that a person or group assumes for a particular purpose, which differs from their original or true name. Default value: guest');
    define($constpref . '_ICON_MEANINGS', 'Alt attribute of icons');
    define($constpref . '_ICON_MEANINGSDSC', 'The alt attribute provides alternative information explaining the meaning of forum icons.<br>Specify each alt separated by pipe "|". The first alt corresponds to "posticon0.svg"<br>Default value: none|normal|unhappy|happy|lower it|raise it|report|question');
    define($constpref . '_ICON_MEANINGSDEF', 'none|normal|unhappy|happy|lower it|raise it|report|question');
    define($constpref . '_GUESTVOTE_IVL', 'Enable Voting from guests');
    define($constpref . '_GUESTVOTE_IVLDSC', 'Allow votes from the same IP with required delay in seconds. Disable with value set to 0. Default value: 86400.');
    define($constpref . '_ANTISPAM_GROUPS', 'Anti-SPAM ');
    define($constpref . '_ANTISPAM_GROUPSDSC', 'Spam filter settings can be applied to User groups. If guests are not allowed to post, you can leave all unchecked.');
    define($constpref . '_ANTISPAM_CLASS', ' Anti-SPAM Class name');
    define($constpref . '_ANTISPAM_CLASSDSC', 'If you disable Anti-SPAM for guests, leave input field blank. Default class name: defaultmobile<br>Available options : defaultmobilesmart, japanese and japanesemobilesmart require WizMobile by Gusagi or hyp_common ktai-renderer by Nao-pon.');
    define($constpref . '_RSS_SHOW_HIDDEN', 'Enable RSS Show hidden topics');
    define($constpref . '_RSS_SHOW_HIDDENDSC', 'Show hidden topics from comment-integration.');
    define($constpref . '_RSS_HIDDEN_TITLE', 'Enable RSS Show Title of hidden topics');
    define($constpref . '_RSS_HIDDEN_TITLEDSC', 'Default title used when empty value.');


    // Notify Categories
    define($constpref . '_NOTCAT_TOPIC', 'This topic');
    define($constpref . '_NOTCAT_TOPICDSC', 'Notifications about the targeted topic');
    define($constpref . '_NOTCAT_FORUM', 'This forum');
    define($constpref . '_NOTCAT_FORUMDSC', 'Notifications about the targeted forum');
    define($constpref . '_NOTCAT_CAT', 'This category');
    define($constpref . '_NOTCAT_CATDSC', 'Notifications about the targeted category');
    define($constpref . '_NOTCAT_GLOBAL', 'All categories');
    define($constpref . '_NOTCAT_GLOBALDSC', 'Notifications about all categories');

    // Each Notifications
    define($constpref . '_NOTIFY_TOPIC_NEWPOST', 'New post in the topic');
    define($constpref . '_NOTIFY_TOPIC_NEWPOSTCAP', 'Notify me of new posts in the current topic.');
    define($constpref . '_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} New post in topic {POST_TITLE}');

    define($constpref . '_NOTIFY_FORUM_NEWPOST', 'New post in the forum');
    define($constpref . '_NOTIFY_FORUM_NEWPOSTCAP', 'Notify me of new posts in the current forum.');
    define($constpref . '_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} New post in forum {POST_TITLE}');

    define($constpref . '_NOTIFY_FORUM_NEWTOPIC', 'New topic in the forum');
    define($constpref . '_NOTIFY_FORUM_NEWTOPICCAP', 'Notify me of new topics in the current forum.');
    define($constpref . '_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} New topic in forum {TOPIC_TITLE}');

    define($constpref . '_NOTIFY_CAT_NEWPOST', 'New post in the category');
    define($constpref . '_NOTIFY_CAT_NEWPOSTCAP', 'Notify me of new posts in the current category.');
    define($constpref . '_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New post in category {POST_TITLE}');

    define($constpref . '_NOTIFY_CAT_NEWTOPIC', 'New topic in the category');
    define($constpref . '_NOTIFY_CAT_NEWTOPICCAP', 'Notify me of new topics in the current category.');
    define($constpref . '_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New topic in category {TOPIC_TITLE}');

    define($constpref . '_NOTIFY_CAT_NEWFORUM', 'New forum in the category');
    define($constpref . '_NOTIFY_CAT_NEWFORUMCAP', 'Notify me of new forums in the current category.');
    define($constpref . '_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New forum in category');

    define($constpref . '_NOTIFY_GLOBAL_NEWPOST', 'New post into any category');
    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTCAP', 'Notify me of new posts into any category.');
    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: New post {POST_TITLE}');

    define($constpref . '_NOTIFY_GLOBAL_NEWTOPIC', 'New topic into any category');
    define($constpref . '_NOTIFY_GLOBAL_NEWTOPICCAP', 'Notify me of new topics into any category.');
    define($constpref . '_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: New topic {TOPIC_TITLE}');

    define($constpref . '_NOTIFY_GLOBAL_NEWFORUM', 'New forum into any category');
    define($constpref . '_NOTIFY_GLOBAL_NEWFORUMCAP', 'Notify me of new forums into any category.');
    define($constpref . '_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: New forum');

    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTFULL', 'New Post (Full Text)');
    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTFULLCAP', 'Notify me of any new posts (include full text in message).');
    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}');
    define($constpref . '_NOTIFY_GLOBAL_WAITING', 'New waiting');
    define($constpref . '_NOTIFY_GLOBAL_WAITINGCAP', 'Notify me of new posts waiting approval. For admins only');
    define($constpref . '_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: New waiting {POST_TITLE}');
}
