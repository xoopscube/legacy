<?php

if (!defined('XOOPS_TRUST_PATH')) {
    die('set XOOPS_TRUST_PATH into mainfile.php');
}

$mydirname = basename(__DIR__);
$mydirpath = __DIR__;

require $mydirpath.'/mytrustdirname.php'; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/notification.php';
