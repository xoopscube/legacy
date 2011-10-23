<?php
// $Id: modulesadmin.php,v 1.1 2007/05/15 02:35:21 minahito Exp $
//%%%%%%	File Name  modulesadmin.php 	%%%%%
define("_MD_AM_MODADMIN","Modules Administration");
define("_MD_AM_MODULE","Module");
define("_MD_AM_VERSION","Version");
define("_MD_AM_LASTUP","Last Update");
define("_MD_AM_DEACTIVATED","Deactivated");
define("_MD_AM_ACTION","Action");
define("_MD_AM_DEACTIVATE","Deactivate");
define("_MD_AM_ACTIVATE","Activate");
define("_MD_AM_UPDATE","Update");
define("_MD_AM_DUPEN","Duplicate entry in modules table!");
define("_MD_AM_DEACTED","The selected module has been deactivated. You can now safely uninstall the module.");
define("_MD_AM_ACTED","The selected module has been activated!");
define("_MD_AM_UPDTED","The selected module has been updated!");
define("_MD_AM_SYSNO","System module cannot be deactivated.");
define("_MD_AM_STRTNO","This module is set as your default start page. Please change the start module to whatever suits your preferences.");

// added in RC2
define("_MD_AM_PCMFM","Please confirm:");

// added in RC3
define("_MD_AM_ORDER","Order");
define("_MD_AM_ORDER0","(0 = hide)");
define("_MD_AM_ACTIVE","Active");
define("_MD_AM_INACTIVE","Inactive");
define("_MD_AM_NOTINSTALLED","Not Installed");
define("_MD_AM_NOCHANGE","No Change");
define("_MD_AM_INSTALL","Install");
define("_MD_AM_UNINSTALL","Uninstall");
define("_MD_AM_SUBMIT","Submit");
define("_MD_AM_CANCEL","Cancel");
define("_MD_AM_DBUPDATE","Database updated successfully!");
define("_MD_AM_BTOMADMIN","Back to Module Administration page");

// %s represents module name
define("_MD_AM_FAILINS","Unable to install %s.");
define("_MD_AM_FAILACT","Unable to activate %s.");
define("_MD_AM_FAILDEACT","Unable to deactivate %s.");
define("_MD_AM_FAILUPD","Unable to update %s.");
define("_MD_AM_FAILUNINS","Unable to uninstall %s.");
define("_MD_AM_FAILORDER","Unable to reorder %s.");
define("_MD_AM_FAILWRITE","Unable to write to main menu.");
define("_MD_AM_ALEXISTS","Module %s already exists.");
define("_MD_AM_ERRORSC", "Error(s):");
define("_MD_AM_OKINS","Module %s installed successfully.");
define("_MD_AM_OKACT","Module %s activated successfully.");
define("_MD_AM_OKDEACT","Module %s deactivated successfully.");
define("_MD_AM_OKUPD","Module %s updated successfully.");
define("_MD_AM_OKUNINS","Module %s uninstalled successfully.");
define("_MD_AM_OKORDER","Module %s changed successfully.");

define('_MD_AM_RUSUREINS', 'Press the button below to install this module');
define('_MD_AM_RUSUREUPD', 'Press the button below to update this module');
define('_MD_AM_RUSUREUNINS', 'Are you sure you would like to uninstall this module?');
define('_MD_AM_LISTUPBLKS', 'The following blocks will be updated.<br />Select the blocks of which contents (template and options) may be overwritten.<br />');
define('_MD_AM_NEWBLKS', 'New Blocks');
define('_MD_AM_DEPREBLKS', 'Deprecated Blocks');
?>