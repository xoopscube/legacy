<?php
// Syntax replace define with const v2.3.0 2021/05/15 @gigamaster XCL-PHP7
const _INSTALL_L0 = '<span>Ẋ OOPS Cube Web Application Platform</span><br>Starting the installation wizard' ;
const _INSTALL_L168 = 'XCL 2.3 requires PHP7.x.x' ;
const _INSTALL_L70 = 'Please change the file permission for mainfile.php so that it is writeable by the server (i.e. chmod 777 mainfile.php on a UNIX/LINUX server, or check the properties of the file and make sure the read-only flag is not set on a Windows server ). Reload this page once you have changed the permission setting.';
//define("_INSTALL_L71","Click on the button below to begin the installation.");
const _INSTALL_L1 = 'Open mainfile.php with your text editor and find the following codes on line 31:' ;
const _INSTALL_L2 = 'Now, change this line to:' ;
const _INSTALL_L3 = 'Next, on line 35, change %s to %s' ;
const _INSTALL_L4 = 'OK, I have set the above settings, let me try again!' ;
const _INSTALL_L5 = 'WARNING!' ;
const _INSTALL_L6 = 'There is a mismatch between your XOOPS_ROOT_PATH configuration on line 31 of mainfile.php and the root path info we have detected.' ;
const _INSTALL_L7 = 'Your settings: ' ;
const _INSTALL_L8 = 'We detected: ' ;
const _INSTALL_L9 = '( On MS platforms, you may receive this error message even when your configuration is correct. If that is the case, please press the button below to continue)' ;
const _INSTALL_L10 = 'Please press the button below to continue if this is really ok.' ;
const _INSTALL_L11 = 'The server path to your XCL root directory' ;
const _INSTALL_L12 = 'The public URL to your XCL root directory' ;
const _INSTALL_L13 = 'If the above settings are correct, press the button below to continue.' ;
const _INSTALL_L14 = 'Next' ;
const _INSTALL_L15 = 'Please open mainfile.php and enter required DB settings data' ;
const _INSTALL_L16 = '%s is the hostname of your database server.' ;
const _INSTALL_L17 = '%s is the username of your database account.' ;
const _INSTALL_L18 = '%s is the password required to access your database.' ;
const _INSTALL_L19 = '%s is the name of your database in which XCL tables will be created.' ;
const _INSTALL_L20 = '%s is the prefix for tables that will be made during the installation.' ;
const _INSTALL_L21 = 'The following database was not found on the server:' ;
const _INSTALL_L22 = 'Attempt to create it?' ;
const _INSTALL_L23 = 'Yes' ;
const _INSTALL_L24 = 'No' ;
const _INSTALL_L25 = 'We have detected the following database information from your configuration in mainfile.php. If this is not correct, please fix it now.' ;
const _INSTALL_L26 = 'Database Configuration' ;
const _INSTALL_L51 = 'Database' ;
const _INSTALL_L66 = 'Select the database to be used' ;
const _INSTALL_L27 = 'Database Hostname' ;
const _INSTALL_L67 = "Hostname of the database server. If you are unsure, 'localhost' works in most cases." ;
const _INSTALL_L28 = 'Database Username' ;
const _INSTALL_L65 = 'Your database user account on the host' ;
const _INSTALL_L29 = 'Database Name' ;
const _INSTALL_L64 = 'The name of database on the host. The installer will attempt to create the database' ;
const _INSTALL_L52 = 'Database Password' ;
const _INSTALL_L68 = 'Password for your database user account' ;
const _INSTALL_L30 = 'Table Prefix' ;
const _INSTALL_L63 = 'This prefix will be added to all new tables created to avoid name conflict in the database. If you are unsure, just use the default.' ;
const _INSTALL_L54 = 'Use persistent connection?' ;
const _INSTALL_L69 = "Default is 'NO'. Choose 'NO' if you are unsure." ;
const _INSTALL_L55 = 'The XCL Physical Path' ;
const _INSTALL_L59 = 'Physical path to your main XCL directory WITHOUT trailing slash' ;
const _INSTALL_L75 = 'The XOOPS_TRUST_PATH Physical Path' ;
const _INSTALL_L76 = 'Physical path to your main XOOPS_TRUST_PATH directory WITHOUT trailing slash<br>You should set XOOPS_TRUST_PATH outside DocumentRoot.' ;

const _INSTALL_L56 = 'The XCL Virtual Path (URL)' ;
const _INSTALL_L58 = 'Virtual path to your main XCL directory WITHOUT trailing slash' ;

const _INSTALL_L31 = 'Could not create database. Contact the server administrator for details.' ;
const _INSTALL_L32 = 'The 1st Step is completed successfully' ;
const _INSTALL_L33 = "Click <a href='../index.php'>HERE</a> to see the home page of your site." ;
const _INSTALL_L35 = "If you had any errors, please contact the dev team at <a href='https://github.com/xoopscube/' rel='external'>XOOPSCube Project</a>" ;
const _INSTALL_L36 = "Create the Administrator account." ;
const _INSTALL_L37 = 'Admin Name' ;
const _INSTALL_L38 = 'Admin Email' ;
const _INSTALL_L39 = 'Admin Password' ;
const _INSTALL_L74 = 'Confirm Password' ;
const _INSTALL_L77 = 'Set Default Timezone' ;
const _INSTALL_L40 = 'Create Tables' ;
const _INSTALL_L41 = 'Please go back and check all the required info and password field.' ;
const _INSTALL_L42 = 'Back' ;
const _INSTALL_L57 = 'Please enter %s' ;

// %s is database name
const _INSTALL_L43 = 'Database %s created!' ;

// %s is table name
const _INSTALL_L44 = 'Unable to make %s' ;
const _INSTALL_L45 = 'Table %s created.' ;

const _INSTALL_L46 = "In order for the modules included in the package to work correctly, the following files must be writeable by the server. Please change the permission settings for these files. (i.e. 'chmod 666 file_name' and 'chmod 777 dir_name' on a UNIX/LINUX server, or check the properties of the file and make sure the read-only flag is not set on a Windows server)" ;
const _INSTALL_L47 = 'Next' ;

const _INSTALL_L53 = 'Confirm Web server Settings' ;

const _INSTALL_L60 = 'Could not write into mainfile.php. Please check the file permission and try again.' ;
const _INSTALL_L61 = 'Could not write to mainfile.php. Contact the server administrator for details.' ;
const _INSTALL_L62 = 'Configuration data has been saved successfully to mainfile.php.' ;
const _INSTALL_L72 = "The following directories must be created with the write permission by the server. (i.e. 'chmod 777 directory_name' on a UNIX/LINUX server)" ;
const _INSTALL_L73 = 'Invalid Email' ;

// add by haruki
const _INSTALL_L80 = 'Introduction' ;
const _INSTALL_L81 = 'Check file permissions' ;
const _INSTALL_L82 = 'Checking file and directory permissions..' ;
const _INSTALL_L83 = 'File is NOT writable %s' ;
const _INSTALL_L84 = 'File is writable %s' ;
const _INSTALL_L85 = 'Directory is NOT writable %s' ;
const _INSTALL_L86 = 'Directory is writable %s' ;
const _INSTALL_L87 = 'No errors detected.' ;
const _INSTALL_L89 = 'General settings' ;
const _INSTALL_L90 = 'General configuration' ;
const _INSTALL_L91 = 'confirm' ;
const _INSTALL_L92 = 'Save settings' ;
const _INSTALL_L93 = 'Modify settings' ;
const _INSTALL_L88 = 'Saving configuration data..' ;
const _INSTALL_L166 = 'Check file permissions in TRUST_PATH' ;
const _INSTALL_L167 = 'Check Trust Path Permissions' ;
const _INSTALL_L94 = 'Check Public PATH and URL' ;
const _INSTALL_L127 = 'Checking file path & URL settings..' ;
const _INSTALL_L95 = 'Could not detect the physical path to your XOOPS directory.' ;
const _INSTALL_L96 = 'There is a conflict between the detected physical path (%s) and the one you input.' ;
const _INSTALL_L97 = '<b>Physical path</b> is correct.' ;

const _INSTALL_L99 = '<b>Physical path</b> must be a directory.' ;
const _INSTALL_L100 = '<b>Virtual path</b> is a valid URL.' ;
const _INSTALL_L101 = '<b>Virtual path</b> is not a valid URL.' ;
const _INSTALL_L102 = 'Confirm database settings' ;
const _INSTALL_L103 = 'Restart from the beginning' ;
const _INSTALL_L104 = 'Check database' ;
const _INSTALL_L105 = 'Attempt to create database' ;
const _INSTALL_L106 = 'Could not connect to the database server.' ;
const _INSTALL_L107 = 'Please check the database server and its configuration.' ;
const _INSTALL_L108 = 'Connection successfully established with the database server.' ;
const _INSTALL_L109 = 'Database does not exists %s' ;
const _INSTALL_L110 = 'Connection successfully to database %s' ;
const _INSTALL_L111 = 'Database connection is OK.<br>Press the button below to create database tables.' ;
const _INSTALL_L112 = 'Administrator Account' ;
const _INSTALL_L113 = 'Table %s deleted.' ;
const _INSTALL_L114 = 'Failed creating database tables.' ;
const _INSTALL_L115 = 'Database tables created.<h3>Attention !</h3>An error message might occurs when the specified table exists. Check for duplicate records that could cause problems. For example: groups.' ;
const _INSTALL_L116 = 'Add New Data' ;
const _INSTALL_L117 = 'Finish' ;

const _INSTALL_L118 = 'Failed creating table %s.' ;
const _INSTALL_L119 = '%d entries inserted to table %s.' ;
const _INSTALL_L120 = 'Failed inserting %d entries to table %s.' ;

const _INSTALL_L121 = 'Constant %s written to %s.' ;
const _INSTALL_L122 = 'Failed writing constant %s.' ;

const _INSTALL_L123 = 'File %s stored in cache/ directory.' ;
const _INSTALL_L124 = 'Failed storing file %s to cache/ directory.' ;

const _INSTALL_L125 = 'File %s overwritten by %s.' ;
const _INSTALL_L126 = 'Could not write to file %s.' ;

const _INSTALL_L130 = 'The installation Wizard has detected anterior tables in your database.<br>The installer will now attempt to upgrade your database.' ;
const _INSTALL_L131 = 'Tables for XCL already exist in your database.' ;
const _INSTALL_L132 = 'update tables' ;
const _INSTALL_L133 = 'Table %s updated.' ;
const _INSTALL_L134 = 'Failed updating table %s.' ;
const _INSTALL_L135 = 'Failed updating database tables.' ;
const _INSTALL_L136 = 'Database tables updated.' ;
const _INSTALL_L137 = 'update modules' ;
const _INSTALL_L138 = 'update comments' ;
const _INSTALL_L139 = 'update avatars' ;
const _INSTALL_L140 = 'update smilies' ;
const _INSTALL_L141 = 'The installer will now update each module to work with XCL.<br>Make sure that you have uploaded all files in XCL package to your server.<br>This may take a while to complete.' ;
const _INSTALL_L142 = 'Updating modules..' ;
const _INSTALL_L143 = 'The installer will now update configuration data of XOOPS 1.3.x to be used with XCL.' ;
const _INSTALL_L144 = 'update config' ;
const _INSTALL_L145 = 'Comment (ID: %s) inserted to the database.' ;
const _INSTALL_L146 = 'Could not insert comment (ID: %s) to the database.' ;
const _INSTALL_L147 = 'Updating comments..' ;
const _INSTALL_L148 = 'Update complete.' ;
const _INSTALL_L149 = 'The installer will now update comment posts in XOOPS 1.3.x to be used in XCL.<br>This may take a while to complete.' ;
const _INSTALL_L150 = 'The installer will now update the smiley and user rank images to be used with XCL.<br>This may take a while to complete.' ;
const _INSTALL_L151 = 'The installer will now update the user avatar images to be used in XCL.<br>This may take a while to complete.' ;
const _INSTALL_L155 = 'Updating smiley/rank images..' ;
const _INSTALL_L156 = 'Updating user avatar images..' ;
const _INSTALL_L157 = 'Select the default user group for each group type' ;
const _INSTALL_L158 = 'Groups in 1.3.x' ;
const _INSTALL_L159 = 'Webmasters' ;
const _INSTALL_L160 = 'Register Users' ;
const _INSTALL_L161 = 'Anonymous Users' ;
const _INSTALL_L162 = 'You must select a default group for each group type.' ;
const _INSTALL_L163 = 'Table %s dropped.' ;
const _INSTALL_L164 = 'Failed deleting table %s.' ;
const _INSTALL_L165 = 'The site is currently closed for maintenance. Please come back later.' ;

// %s is filename
const _INSTALL_L152 = 'Could not open %s.' ;
const _INSTALL_L153 = 'Could not update %s.' ;
const _INSTALL_L154 = '%s updated.' ;

const _INSTALL_L128 = 'Select a language for the installation process' ;
const _INSTALL_L200 = 'Reload' ;
const _INSTALL_L210 = 'XCL Global Settings (2nd step)' ;


const _INSTALL_CHARSET = 'UTF-8' ;

const _INSTALL_LANG_XOOPS_SALT = 'SALT' ;
const _INSTALL_LANG_XOOPS_SALT_DESC = "This plays a supplementary role to generate secret code and token. You don't need to change the default value." ;

const _INSTALL_HEADER_MESSAGE = 'Please follow the onscreen instructions to install.' ;
