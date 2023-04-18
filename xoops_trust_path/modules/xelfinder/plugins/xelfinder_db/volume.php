<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 */

if (is_dir(XOOPS_TRUST_PATH . $path)) {

	$volumeOptions = [
     'driverSrc' => __DIR__ . '/driver.class.php',
     'driver'    => 'XoopsXelfinder_db',
     'mydirname' => $mydirname,
     'path'      => '1',
     'filePath'  => XOOPS_TRUST_PATH . $path . rawurlencode(defined('XELFINDER_DB_FILENAME_PREFIX')? XELFINDER_DB_FILENAME_PREFIX : substr(_MD_XELFINDER_SITEURL, strpos(_MD_XELFINDER_SITEURL, '://') + 3)) . '_' . $mydirname . '_',
     'URL'       => _MD_XELFINDER_MODULE_URL . '/' . $mydirname . '/index.php/view/',
     'alias'     => $title,
     'tmbURL'     => _MD_XELFINDER_MODULE_URL . '/'.$mydirname.'/cache/tmb/',
     'tmbPath'    => XOOPS_MODULE_PATH . '/'.$mydirname.'/cache/tmb',
     //'tmbSize'    => 140,
     //'tmbCrop'    => false,
     'uploadAllow'     => ['image'],
     // mimetypes not allowed to upload
     'uploadDeny'      => ['all'],
     // order to proccess uploadAllow and uploadDeny options
     'uploadOrder'     => ['deny', 'allow'],
     // regexp or function name to validate new file name
     'acceptedName'    => '/^[^\/\\?*:|"<>]{0,190}[^.\/\\?*:|"<>]$/u',
     'defaults' => ['read' => true, 'write' => true, 'hidden' => false, 'locked' => false],
     'icon'          => (defined('ELFINDER_IMG_PARENT_URL')? (rtrim(ELFINDER_IMG_PARENT_URL, '/').'/') : '').'img/volume_icon_sql.png',
     'uiCmdMap' => ['chmod' => 'perm'],
     'statOwner' => true,
 ];
}
