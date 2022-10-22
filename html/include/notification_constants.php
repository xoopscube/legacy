<?php
/**
 * Notification constants
 * @package    XCL
 * @subpackage core
 * @version    XCL 2.3.1
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

// RMV-NOTIFY

define('XOOPS_NOTIFICATION_MODE_SENDALWAYS', 0);
define('XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE', 1);
define('XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT', 2);
define('XOOPS_NOTIFICATION_MODE_WAITFORLOGIN', 3);

define('XOOPS_NOTIFICATION_METHOD_DISABLE', 0);
define('XOOPS_NOTIFICATION_METHOD_PM', 1);
define('XOOPS_NOTIFICATION_METHOD_EMAIL', 2);

define('XOOPS_NOTIFICATION_DISABLE', 0);
define('XOOPS_NOTIFICATION_ENABLEBLOCK', 1);
define('XOOPS_NOTIFICATION_ENABLEINLINE', 2);
define('XOOPS_NOTIFICATION_ENABLEBOTH', 3);
