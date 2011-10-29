<?php
require_once XOOPS_ROOT_PATH.'/class/template.php';
header ('Content-Type: text/javascript');
$tpl = new XoopsTpl();
$tpl->xoops_setCaching(2);
$tpl->xoops_setCacheTime(60*60*24*7);
$tpl->display("db:{$mydirname}_javascript.html");
?>