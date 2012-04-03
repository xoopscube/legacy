<?php
/*
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 * $Id: volume.php,v 1.1 2012/01/20 13:32:02 nao-pon Exp $
 */

if (is_dir(XOOPS_ROOT_PATH . $path)) {

	require_once dirname(__FILE__) . '/driver.class.php';

	$volumeOptions = array(
		'driver'     => 'XoopsMailbbs',
		'mydirname'  => $mydirname,
		'path'       => XOOPS_ROOT_PATH . $path,
		'URL'        => XOOPS_URL . $path,
		'alias'      => $title,
		'defaults' => array('read' => true, 'write' => false),
		'attributes' => array(
			array(
				'pattern' => '#.+#',
				'read'    => true,
				'write'   => false,
				'locked'  => true,
				'hidden'  => false
			)
		)
	);

}
