<?php
// $Id: install.php,v 1.3 2007/06/24 12:39:39 tom_g3x Exp $
define("_INSTALL_L0", "Welcome to the Install Wizard for XOOPS Cube 2.3");
define("_INSTALL_L168", "XOOPS Cube Legacy requires PHP5 or later");
define("_INSTALL_L70", "Please change the file permission for mainfile.php so that it is writeable by the server (i.e. chmod 777 mainfile.php on a UNIX/LINUX server, or check the properties of the file and make sure the read-only flag is not set on a Windows server ). Reload this page once you have changed the permission setting.");
//define("_INSTALL_L71","Click on the button below to begin the installation.");
define("_INSTALL_L1", "Open mainfile.php with your text editor and find the following codes on line 31:");
define("_INSTALL_L2", "Now, change this line to:");
define("_INSTALL_L3", "Next, on line 35, change %s to %s");
define("_INSTALL_L4", "OK, I have set the above settings, let me try again!");
define("_INSTALL_L5", "WARNING!");
define("_INSTALL_L6", "There is a mismatch between your XOOPS_ROOT_PATH configuration on line 31 of mainfile.php and the root path info we have detected.");
define("_INSTALL_L7", "Your setting: ");
define("_INSTALL_L8", "We detected: ");
define("_INSTALL_L9", "( On MS platforms, you may receive this error message even when your configuration is correct. If that is the case, please press the button below to continue)");
define("_INSTALL_L10", "Please press the button below to continue if this is really ok.");
define("_INSTALL_L11", "The server path to your XOOPS Cube root directory: ");
define("_INSTALL_L12", "URL to your XOOPS Cube root directory: ");
define("_INSTALL_L13", "If the above setting is correct, press the button below to continue.");
define("_INSTALL_L14", "Next");
define("_INSTALL_L15", "Please open mainfile.php and enter required DB settings data");
define("_INSTALL_L16", "%s is the hostname of your database server.");
define("_INSTALL_L17", "%s is the username of your database account.");
define("_INSTALL_L18", "%s is the password required to access your database.");
define("_INSTALL_L19", "%s is the name of your database in which XOOPS Cube tables will be created.");
define("_INSTALL_L20", "%s is the prefix for tables that will be made during the installation.");
define("_INSTALL_L21", "The following database was not found on the server:");
define("_INSTALL_L22", "Attempt to create it?");
define("_INSTALL_L23", "Yes");
define("_INSTALL_L24", "No");
define("_INSTALL_L25", "We have detected the following database information from your configuration in mainfile.php. Please fix it now if this is not correct.");
define("_INSTALL_L26", "Database Configuration");
define("_INSTALL_L51", "Database");
define("_INSTALL_L66", "Choose the database to be used");
define("_INSTALL_L27", "Database Hostname");
define("_INSTALL_L67", "Hostname of the database server. If you are unsure, 'localhost' works in most cases.");
define("_INSTALL_L28", "Database Username");
define("_INSTALL_L65", "Your database user account on the host");
define("_INSTALL_L29", "Database Name");
define("_INSTALL_L64", "The name of database on the host. The installer will attempt to create the database if not exist");
define("_INSTALL_L52", "Database Password");
define("_INSTALL_L68", "Password for your database user account");
define("_INSTALL_L30", "Table Prefix");
define("_INSTALL_L63", "This prefix will be added to all new tables created to avoid name conflict in the database. If you are unsure, just use the default.");
define("_INSTALL_L54", "Use persistent connection?");
define("_INSTALL_L69", "Default is 'NO'. Choose 'NO' if you are unsure.");
define("_INSTALL_L55", "XOOPS Cube Physical Path");
define("_INSTALL_L59", "Physical path to your main XOOPS Cube directory WITHOUT trailing slash");
define("_INSTALL_L75", "XOOPS_TRUST_PATH Physical Path");
define("_INSTALL_L76", "Physical path to your main XOOPS_TRUST_PATH directory WITHOUT trailing slash<br />You should set XOOPS_TRUST_PATH outside DocumentRoot.");

define("_INSTALL_L56", "XOOPS Cube Virtual Path (URL)");
define("_INSTALL_L58", "Virtual path to your main XOOPS Cube directory WITHOUT trailing slash");

define("_INSTALL_L31", "Could not create database. Contact the server administrator for details.");
define("_INSTALL_L32", "The 1st Step Installation Complete");
define("_INSTALL_L33", "Click <a href='../index.php'>HERE</a> to see the home page of your site.");
define("_INSTALL_L35", "If you had any errors, please contact the dev team at <a href='https://github.com/xoopscube/legacy/' rel='external'>XOOPS Cube Project</a>");
define("_INSTALL_L36", "Please choose your site admin's name and password.");
define("_INSTALL_L37", "Admin Name");
define("_INSTALL_L38", "Admin Email");
define("_INSTALL_L39", "Admin Password");
define("_INSTALL_L74", "Confirm Password");
define("_INSTALL_L77", "Set Default Timezone");
define("_INSTALL_L40", "Create Tables");
define("_INSTALL_L41", "Please go back and type in all the required info.");
define("_INSTALL_L42", "Back");
define("_INSTALL_L57", "Please enter %s");

// %s is database name
define("_INSTALL_L43", "Database %s created!");

// %s is table name
define("_INSTALL_L44", "Unable to make %s");
define("_INSTALL_L45", "Table %s created.");

define("_INSTALL_L46", "In order for the modules included in the package to work correctly, the following files must be writeable by the server. Please change the permission setting for these files. (i.e. 'chmod 666 file_name' and 'chmod 777 dir_name' on a UNIX/LINUX server, or check the properties of the file and make sure the read-only flag is not set on a Windows server)");
define("_INSTALL_L47", "Next");

define("_INSTALL_L53", "Please confirm the following submitted data:");

define("_INSTALL_L60", "Could not write into mainfile.php. Please check the file permission and try again.");
define("_INSTALL_L61", "Could not write to mainfile.php. Contact the server administrator for details.");
define("_INSTALL_L62", "Configuration data has been saved successfully to mainfile.php.");
define("_INSTALL_L72", "The following directories must be created with the write permission by the server. (i.e. 'chmod 777 directory_name' on a UNIX/LINUX server)");
define("_INSTALL_L73", "Invalid Email");

// add by haruki
define("_INSTALL_L80", "Introduction");
define("_INSTALL_L81", "Check file permissions");
define("_INSTALL_L82", "Checking file and directory permissions..");
define("_INSTALL_L83", "File %s is NOT writable.");
define("_INSTALL_L84", "File %s is writable.");
define("_INSTALL_L85", "Directory %s is NOT writable.");
define("_INSTALL_L86", "Directory %s is writable.");
define("_INSTALL_L87", "No errors detected.");
define("_INSTALL_L89", "General settings");
define("_INSTALL_L90", "General configuration");
define("_INSTALL_L91", "confirm");
define("_INSTALL_L92", "Save settings");
define("_INSTALL_L93", "Modify settings");
define("_INSTALL_L88", "Saving configuration data..");
define("_INSTALL_L166", "Check file permissions in XOOPS_TRUST_PATH");
define("_INSTALL_L167", "Checking file and directory permissions..");
define("_INSTALL_L94", "Check path & URL");
define("_INSTALL_L127", "Checking file path & URL settings..");
define("_INSTALL_L95", "Could not detect the physical path to your XOOPS directory.");
define("_INSTALL_L96", "There is a conflict between the detected physical path (%s) and the one you input.");
define("_INSTALL_L97", "<b>Physical path</b> is correct.");

define("_INSTALL_L99", "<b>Physical path</b> must be a directory.");
define("_INSTALL_L100", "<b>Virtual path</b> is a valid URL.");
define("_INSTALL_L101", "<b>Virtual path</b> is not a valid URL.");
define("_INSTALL_L102", "Confirm database settings");
define("_INSTALL_L103", "Restart from the beginning");
define("_INSTALL_L104", "Check database");
define("_INSTALL_L105", "Attempt to create database");
define("_INSTALL_L106", "Could not connect to the database server.");
define("_INSTALL_L107", "Please check the database server and its configuration.");
define("_INSTALL_L108", "Connection to database server is OK.");
define("_INSTALL_L109", "Database %s does not exists.");
define("_INSTALL_L110", "Database %s exists and connectable.");
define("_INSTALL_L111", "Database connection is OK.<br />Press the button below to create database tables.");
define("_INSTALL_L112", "Admin user setting");
define("_INSTALL_L113", "Table %s deleted.");
define("_INSTALL_L114", "Failed creating database tables.");
define("_INSTALL_L115", "Database tables created.");
define("_INSTALL_L116", "Insert data");
define("_INSTALL_L117", "Finish");

define("_INSTALL_L118", "Failed creating table %s.");
define("_INSTALL_L119", "%d entries inserted to table %s.");
define("_INSTALL_L120", "Failed inserting %d entries to table %s.");

define("_INSTALL_L121", "Constant %s written to %s.");
define("_INSTALL_L122", "Failed writing constant %s.");

define("_INSTALL_L123", "File %s stored in cache/ directory.");
define("_INSTALL_L124", "Failed storing file %s to cache/ directory.");

define("_INSTALL_L125", "File %s overwritten by %s.");
define("_INSTALL_L126", "Could not write to file %s.");

define("_INSTALL_L130", "The installer has detected tables for XOOPS 1.3.x in your database.<br />The installer will now attempt to upgrade your database to XOOPS2.");
define("_INSTALL_L131", "Tables for XOOPS Cube Legacy already exist in your database.");
define("_INSTALL_L132", "update tables");
define("_INSTALL_L133", "Table %s updated.");
define("_INSTALL_L134", "Failed updating table %s.");
define("_INSTALL_L135", "Failed updating database tables.");
define("_INSTALL_L136", "Database tables updated.");
define("_INSTALL_L137", "update modules");
define("_INSTALL_L138", "update comments");
define("_INSTALL_L139", "update avatars");
define("_INSTALL_L140", "update smilies");
define("_INSTALL_L141", "The installer will now update each module to work with XOOPS Cube.<br />Make sure that you have uploaded all files in XOOPS Cube package to your server.<br />This may take a while to complete.");
define("_INSTALL_L142", "Updating modules..");
define("_INSTALL_L143", "The installer will now update configuration data of XOOPS 1.3.x to be used with XOOPS Cube.");
define("_INSTALL_L144", "update config");
define("_INSTALL_L145", "Comment (ID: %s) inserted to the database.");
define("_INSTALL_L146", "Could not insert comment (ID: %s) to the database.");
define("_INSTALL_L147", "Updating comments..");
define("_INSTALL_L148", "Update complete.");
define("_INSTALL_L149", "The installer will now update comment posts in XOOPS 1.3.x to be used in XOOPS Cube.<br />This may take a while to complete.");
define("_INSTALL_L150", "The installer will now update the smiley and user rank images to be used with XOOPS Cube.<br />This may take a while to complete.");
define("_INSTALL_L151", "The installer will now update the user avatar images to be used in XOOPS Cube.<br />This may take a while to complete.");
define("_INSTALL_L155", "Updating smiley/rank images..");
define("_INSTALL_L156", "Updating user avatar images..");
define("_INSTALL_L157", "Select the default user group for each group type");
define("_INSTALL_L158", "Groups in 1.3.x");
define("_INSTALL_L159", "Webmasters");
define("_INSTALL_L160", "Register Users");
define("_INSTALL_L161", "Anonymous Users");
define("_INSTALL_L162", "You must select a default group for each group type.");
define("_INSTALL_L163", "Table %s dropped.");
define("_INSTALL_L164", "Failed deleting table %s.");
define("_INSTALL_L165", "The site is currently closed for maintenance. Please come back later.");

// %s is filename
define("_INSTALL_L152", "Could not open %s.");
define("_INSTALL_L153", "Could not update %s.");
define("_INSTALL_L154", "%s updated.");

define('_INSTALL_L128', 'Choose language to be used for the installation process');
define('_INSTALL_L200', 'Reload');
define("_INSTALL_L210", "The 2nd Step Installation");


define('_INSTALL_CHARSET', 'UTF-8');

define('_INSTALL_LANG_XOOPS_SALT', "SALT");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "This plays a supplementary role to generate secret code and token. You don't need to change the default value.");

define('_INSTALL_HEADER_MESSAGE', 'Please follow the onscreen instructions to install.');
