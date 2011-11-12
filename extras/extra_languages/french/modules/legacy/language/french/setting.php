<?php

//%%%%%     TIME FORMAT SETTINGS   %%%%%
if (!defined('_DATESTRING')) define('_DATESTRING','Y/n/j G:i:s');
if (!defined('_MEDIUMDATESTRING')) define('_MEDIUMDATESTRING','Y/n/j G:i');
if (!defined('_SHORTDATESTRING')) define('_SHORTDATESTRING','Y/n/j');
define('_JSDATEPICKSTRING','yy-mm-dd');
define('_PHPDATEPICKSTRING','Y-m-d');

/*
The following characters are recognized in the format string:
a - "am" or "pm"
A - "AM" or "PM"
d - day of the month, 2 digits with leading zeros; i.e. "01" to "31"
D - day of the week, textual, 3 letters; i.e. "Fri"
F - month, textual, long; i.e. "January"
h - hour, 12-hour format; i.e. "01" to "12"
H - hour, 24-hour format; i.e. "00" to "23"
g - hour, 12-hour format without leading zeros; i.e. "1" to "12"
G - hour, 24-hour format without leading zeros; i.e. "0" to "23"
i - minutes; i.e. "00" to "59"
j - day of the month without leading zeros; i.e. "1" to "31"
l (lowercase 'L') - day of the week, textual, long; i.e. "Friday"
L - boolean for whether it is a leap year; i.e. "0" or "1"
m - month; i.e. "01" to "12"
n - month without leading zeros; i.e. "1" to "12"
M - month, textual, 3 letters; i.e. "Jan"
s - seconds; i.e. "00" to "59"
S - English ordinal suffix, textual, 2 characters; i.e. "th", "nd"
t - number of days in the given month; i.e. "28" to "31"
T - Timezone setting of this machine; i.e. "MDT"
U - seconds since the epoch
w - day of the week, numeric, i.e. "0" (Sunday) to "6" (Saturday)
Y - year, 4 digits; i.e. "1999"
y - year, 2 digits; i.e. "99"
z - day of the year; i.e. "0" to "365"
Z - timezone offset in seconds (i.e. "-43200" to "43200")
*/


//%%%%%     LANGUAGE SPECIFIC SETTINGS   %%%%%
if (!defined('_CHARSET')) define('_CHARSET', 'ISO-8859-1');
if (!defined('_LANGCODE')) define('_LANGCODE', 'fr');
// change 0 to 1 if this language is a multi-bytes language
if (!defined('XOOPS_USE_MULTIBYTES')) define('XOOPS_USE_MULTIBYTES', '0');


//%%%%%     REQUSTED DATA SETTINGS   %%%%%
if (!defined('_REQUESTED_DATA_NAME')) define('_REQUESTED_DATA_NAME', 'requested_data_name');
if (!defined('_REQUESTED_ACTION_NAME')) define('_REQUESTED_ACTION_NAME', 'requested_action_name');
if (!defined('_REQUESTED_DATA_ID')) define('_REQUESTED_DATA_ID', 'requested_data_id');
?>
