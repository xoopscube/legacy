<?php
// Sitemap Module 
define('_MI_SITEMAP_NAME' , 'Sitemap');
define('_MI_SITEMAP_ADMENU_OVERVIEW' , 'Overview');
define('_MI_SITEMAP_DESC' , 'Automated XML Sitemap generator for search engines and humains');
define('_MI_SITEMAP_LIST_KEYWORD', 'search engine XML sitemaps generator map conzact address footer credits');
define('_MI_SITEMAP_ADMENU_PAGESPEED', 'Pagespeed');
define('_MI_SITEMAP_PAGESPEED_KEYWORD', 'Google pagespeed check');

// config option
define('_MI_SITEMAP_MESSAGE' , '<p>The sitemap is a special page intended to serve as a guide for the website.<br>
It is a visual representation of the information space to help visitors find specific pages more efficiently.</p>');
define('_MI_SITEMAP_ADMENU_TOP' , 'TOP');
define('_MI_SITEMAP_ADMENU_MYBLOCKSADMIN' , 'Blocs');
define('_MI_MESSAGE' , 'Message [html]');
define('_MI_MESSAGEEDSC' , 'Message to appear on the page');
define('_MI_SHOW_SUBCATEGORIES' , 'Show sub-categories');
define('_MI_SHOW_SUBCATEGORIESDSC' , '');
define('_MI_ALLTIME_GUEST' , 'All-time guest mode');
define('_MI_ALLTIME_GUESTDSC' , "If you turn this module's cache on, set this option to 'yes'");
define('_MI_INVISIBLE_WEIGHTS' , 'Invisible Modules');
define('_MI_INVISIBLE_WEIGHTSDSC' , 'Exclude Modules from the Sitemap using the same value of the "order" field (next to the module name in Module Management). The "order" numbers must be separated by commas. Default is : 0 or blank.');
define('_MI_INVISIBLE_DIRNAMES' , 'Hide directory names from Sitemap');
define('_MI_INVISIBLE_DIRNAMESDSC' , "Specify the directory name of modules to hide from Sitemap.<br>Dirnames must be separated by commas, eg: downloads,widget");
define('_MI_SITEMAP_DEFAULT_CHANGEFREQ', 'Default Change Frequency');
define('_MI_SITEMAP_DEFAULT_CHANGEFREQ_DESC', 'Select the default change frequency for URLs in the XML sitemap if not provided by a module.');
define('_MI_SITEMAP_FREQ_ALWAYS', 'Always');
define('_MI_SITEMAP_FREQ_HOURLY', 'Hourly');
define('_MI_SITEMAP_FREQ_DAILY', 'Daily');
define('_MI_SITEMAP_FREQ_WEEKLY', 'Weekly');
define('_MI_SITEMAP_FREQ_MONTHLY', 'Monthly');
define('_MI_SITEMAP_FREQ_YEARLY', 'Yearly');
define('_MI_SITEMAP_FREQ_NEVER', 'Never');
define('_MI_SITEMAP_DEFAULT_PRIORITY', 'Default Priority');
define('_MI_SITEMAP_DEFAULT_PRIORITY_DESC', 'Select the default priority (0.0 to 1.0) for URLs in the XML sitemap if not provided by a module.');
define('_MI_SITEMAP_SHOW_MODULE_SUBLINKS', "Show Module Sublinks");
define('_MI_SITEMAP_SHOW_MODULE_SUBLINKS_DESC', "Choose 'Yes' to display the sublinks (admin menu items) for each module in the HTML sitemap.");

// Robots.txt
define('_MI_SITEMAP_ADMENU_ROBOTS', 'Robots.txt Editor');
define('_MI_SITEMAP_ROBOTS_KEYWORD', 'robots txt editor seo sitemap');
define('_MI_SITEMAP_ROBOTS_TITLE', 'Robots.txt Editor');
define('_MI_SITEMAP_ROBOTS_DESC', 'Manage your robots.txt file. This tool allows you to view, edit, and save the robots.txt file located in your website\'s root directory.');
define('_MI_SITEMAP_ROBOTS_CURRENT_CONTENT', 'Current robots.txt Content:');
define('_MI_SITEMAP_ROBOTS_EDIT_AREA', 'Edit robots.txt:');
define('_MI_SITEMAP_ROBOTS_RECOMMENDED_CONTENT_BTN', 'Load Recommended Content');
define('_MI_SITEMAP_ROBOTS_SAVE_BTN', 'Save robots.txt');
define('_MI_SITEMAP_ROBOTS_SAVE_SUCCESS', 'robots.txt has been saved successfully.');
define('_MI_SITEMAP_ROBOTS_SAVE_ERROR_PERMISSION', 'ERROR: Could not write to robots.txt. Please check file permissions on the XOOPS root directory or the robots.txt file itself. The web server needs write access.');
define('_MI_SITEMAP_ROBOTS_SAVE_ERROR_GENERAL', 'ERROR: An unspecified error occurred while trying to save robots.txt.');
define('_MI_SITEMAP_ROBOTS_READ_ERROR', 'ERROR: Could not read robots.txt. The file might not exist or is not readable by the web server.');
define('_MI_SITEMAP_ROBOTS_FILE_NOT_EXIST', 'robots.txt does not currently exist in your website root. You can create it by entering content below and clicking "Save".');
define('_MI_SITEMAP_ROBOTS_WARNING_OVERWRITE', 'WARNING: Saving will overwrite the current robots.txt file in your website root!');
define('_MI_SITEMAP_ROBOTS_CONFIRM_LOAD_RECOMMENDED', 'Are you sure you want to replace the current content in the editor with the recommended settings? Any unsaved changes will be lost.');

// Block
define('_MI_BLOCK_BLOCKNAME' , 'Sitemap Menu');
define('_MI_BLOCK_BLOCKNAME_DESC' , 'Use Sitemap to create a block menu.');
define('_MI_BLOCK_MAP' , 'Sitemap Map');
define('_MI_BLOCK_MAP_DESC' , 'Use the Map block to display an interactive map.');

define('_MI_SHOW_SITENAME', 'Show the site name');
define('_MI_SHOW_SLOGAN', 'Show the site slogan');
define('_MI_SHOW_MAP', 'Enable Map');
define('_MI_SHOW_MAP_CODE', 'Add the code from Map service, usually an iframe.');
define('_MI_SHOW_ADDRESS', 'Enable Address');
define('_MI_SHOW_ADDRESS_CODE', 'Add an address using HTML the tag and href with tel+');
