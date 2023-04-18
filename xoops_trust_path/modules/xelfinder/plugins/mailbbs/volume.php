<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

if (is_dir(XOOPS_ROOT_PATH . $path)) {

	$volumeOptions = ['driverSrc'  => __DIR__ . '/driver.class.php', 'driver'     => 'XoopsMailbbs', 'mydirname'  => $mydirname, 'path'       => XOOPS_ROOT_PATH . $path, 'URL'        => _MD_XELFINDER_SITEURL . $path, 'alias'      => $title, 'quarantine' => '', 'readonly'   => true, 'icon'       => is_file(XOOPS_MODULE_PATH . '/'.$mydirname.'/imgs/elfinder_volume_icon.png')? _MD_XELFINDER_MODULE_URL . '/'.$mydirname.'/imgs/elfinder_volume_icon.png' : '', 'defaults' => ['read' => true, 'write' => false, 'locked' => true]];

}
