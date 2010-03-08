<?php
// $Id: mailusers.php,v 1.1 2007/05/15 02:35:20 minahito Exp $
//%%%%%%	Admin Module Name  MailUsers	%%%%%
define("_AM_DBUPDATED",_MD_AM_DBUPDATED);

//%%%%%%	mailusers.php 	%%%%%
define("_AM_SENDTOUSERS","Send message to users whose:");
define("_AM_SENDTOUSERS2","Send to:");
define("_AM_GROUPIS","Group is (optional)");
define("_AM_TIMEFORMAT", "(Format yyyy-mm-dd, optional)");
define("_AM_LASTLOGMIN","Last Login is after");
define("_AM_LASTLOGMAX","Last Login is before");
define("_AM_REGDMIN","Registered date is after");
define("_AM_REGDMAX","Registered date is before");
define("_AM_IDLEMORE","Last Login was more than X days ago (optional)");
define("_AM_IDLELESS","Last Login was less than X days ago (optional)");
define("_AM_MAILOK","Send message only to users that accept notification messages (optional)");
define("_AM_INACTIVE","Send message to inactive users only (optional)");
define("_AMIFCHECKD", "If this is checked, all the above plus private messaging will be ignored");
define("_AM_MAILFNAME","From Name (email only)");
define("_AM_MAILFMAIL","From Email (email only)");
define("_AM_MAILSUBJECT","Subject");
define("_AM_MAILBODY","Body");
define("_AM_MAILTAGS","Useful Tags:");
define("_AM_MAILTAGS1","{X_UID} will print user id");
define("_AM_MAILTAGS2","{X_UNAME} will print user name");
define("_AM_MAILTAGS3","{X_UEMAIL} will print user email");
define("_AM_MAILTAGS4","{X_UACTLINK} will print user activation link");
define("_AM_SENDTO","Send to");
define("_AM_EMAIL","Email");
define("_AM_PM","Private Message");
define("_AM_SENDMTOUSERS", "Send Message to Users");
define("_AM_SENT", "Sent Users");
define("_AM_SENTNUM", "%s - %s (total: %s users)");
define("_AM_SENDNEXT", "Next");
define("_AM_NOUSERMATCH", "No user matched");
define("_AM_SENDCOMP", "Sending message completed.");

?>