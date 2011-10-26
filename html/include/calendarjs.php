<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

$root =& XCube_Root::getSingleton();
$root->mLanguageManager->loadPageTypeMessageCatalog('calendar');

?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo XOOPS_URL;?>/include/calendar-blue.css" />
<script type="text/javascript" src="<?php echo XOOPS_URL.'/include/calendar.js';?>"></script>
<script type="text/javascript">
<!--
var calendar = null;

function selected(cal, date) {
  cal.sel.value = date;
}

function closeHandler(cal) {
  cal.hide();
  Calendar.removeEvent(document, "mousedown", checkCalendar);
}

function checkCalendar(ev) {
  var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
  for (; el != null; el = el.parentNode)
    if (el == calendar.element || el.tagName == "A") break;
  if (el == null) {
    calendar.callCloseHandler(); Calendar.stopEvent(ev);
  }
}
function showCalendar(id) {
  var el = xoopsGetElementById(id);
  if (calendar != null) {
    calendar.hide();
  } else {
    var cal = new Calendar(true, <?php if (isset($jstime)) { echo $jstime; } else { echo 'null';}?>, selected, closeHandler);
    calendar = cal;
    cal.setRange(2000, 2015);
    calendar.create();
  }
  calendar.sel = el;
  calendar.parseDate(el.value);
  calendar.showAtElement(el);
  Calendar.addEvent(document, "mousedown", checkCalendar);
  return false;
}

Calendar._DN = new Array
("<?php echo _CAL_SUNDAY;?>",
 "<?php echo _CAL_MONDAY;?>",
 "<?php echo _CAL_TUESDAY;?>",
 "<?php echo _CAL_WEDNESDAY;?>",
 "<?php echo _CAL_THURSDAY;?>",
 "<?php echo _CAL_FRIDAY;?>",
 "<?php echo _CAL_SATURDAY;?>",
 "<?php echo _CAL_SUNDAY;?>");
Calendar._MN = new Array
("<?php echo _CAL_JANUARY;?>",
 "<?php echo _CAL_FEBRUARY;?>",
 "<?php echo _CAL_MARCH;?>",
 "<?php echo _CAL_APRIL;?>",
 "<?php echo _CAL_MAY;?>",
 "<?php echo _CAL_JUNE;?>",
 "<?php echo _CAL_JULY;?>",
 "<?php echo _CAL_AUGUST;?>",
 "<?php echo _CAL_SEPTEMBER;?>",
 "<?php echo _CAL_OCTOBER;?>",
 "<?php echo _CAL_NOVEMBER;?>",
 "<?php echo _CAL_DECEMBER;?>");

Calendar._TT = {};
Calendar._TT["TOGGLE"] = "<?php echo _CAL_TGL1STD;?>";
Calendar._TT["PREV_YEAR"] = "<?php echo _CAL_PREVYR;?>";
Calendar._TT["PREV_MONTH"] = "<?php echo _CAL_PREVMNTH;?>";
Calendar._TT["GO_TODAY"] = "<?php echo _CAL_GOTODAY;?>";
Calendar._TT["NEXT_MONTH"] = "<?php echo _CAL_NXTMNTH;?>";
Calendar._TT["NEXT_YEAR"] = "<?php echo _CAL_NEXTYR;?>";
Calendar._TT["SEL_DATE"] = "<?php echo _CAL_SELDATE;?>";
Calendar._TT["DRAG_TO_MOVE"] = "<?php echo _CAL_DRAGMOVE;?>";
Calendar._TT["PART_TODAY"] = "(<?php echo _CAL_TODAY;?>)";
Calendar._TT["MON_FIRST"] = "<?php echo _CAL_DISPM1ST;?>";
Calendar._TT["SUN_FIRST"] = "<?php echo _CAL_DISPS1ST;?>";
Calendar._TT["CLOSE"] = "<?php echo _CLOSE;?>";
Calendar._TT["TODAY"] = "<?php echo _CAL_TODAY;?>";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "y-mm-dd";
Calendar._TT["TT_DATE_FORMAT"] = "y-mm-dd";

Calendar._TT["WK"] = "";
//-->
</script>