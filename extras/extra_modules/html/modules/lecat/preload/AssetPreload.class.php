<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}


require_once XOOPS_TRUST_PATH . '/modules/lecat/preload/AssetPreload.class.php';
Lecat_AssetPreloadBase::prepare(basename(dirname(dirname(__FILE__))));

?>
