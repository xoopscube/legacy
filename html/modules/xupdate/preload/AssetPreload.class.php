<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once XOOPS_TRUST_PATH . '/modules/xupdate/preload/AssetPreload.class.php';
Xupdate_AssetPreloadBase::prepare(basename(dirname(dirname(__FILE__))));

?>
