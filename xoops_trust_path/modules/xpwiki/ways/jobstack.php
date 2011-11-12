<?php
/*
 * Created on 2008/05/13 by nao-pon http://hypweb.net/
 * $Id: jobstack.php,v 1.10 2011/10/31 16:04:47 nao-pon Exp $
 */

error_reporting(0);

ignore_user_abort(TRUE);

$file = $mytrustdirpath . '/skin/image/gif/blank.gif';

header('Content-Type: image/gif');
header('Content-Length: ' . filesize($file));
header('Expires: Thu, 01 Dec 1994 16:00:00 GMT');
header('Last-Modified: '. gmdate('D, d M Y H:i:s'). ' GMT');
header('Cache-Control: no-cache, no-store, must-revalidate, pre-check=0, post-check=0');
header('Pragma: no-cache');

HypCommonFunc::readfile($file);

flush();

include_once $mytrustdirpath . '/include.php';

$xpwiki = new XpWiki($mydirname);
$xpwiki->init('#RenderMode');

$max_execution_time = intval(ini_get('max_execution_time'));

// It is all as for the one executed soon. (ttl = 0)
$sql = 'SELECT `key`, `data` FROM '.$xpwiki->db->prefix($xpwiki->root->mydirname.'_cache').' WHERE `plugin`=\'jobstack\' AND `mtime` <= '.$xpwiki->cont['UTC'].' AND `ttl`=0 ORDER BY `mtime` ASC LIMIT 1';
if ($res = $xpwiki->db->query($sql)) {
	$row = $xpwiki->db->fetchRow($res);
	while($row) {
		if ($max_execution_time) @ ini_set('max_execution_time', (string)$max_execution_time);
		xpwiki_jobstack_switch($xpwiki, $row);
		$res = $xpwiki->db->query($sql);
		$row = $xpwiki->db->fetchRow($res);
	}
}

// Additionally, the one executed sequentially
$sql = 'SELECT `key`, `data` FROM '.$xpwiki->db->prefix($xpwiki->root->mydirname.'_cache').' WHERE `plugin`=\'jobstack\' AND `mtime` <= '.$xpwiki->cont['UTC'].' ORDER BY `mtime` ASC LIMIT 1';
if ($res = $xpwiki->db->query($sql)) {
	if ($row = $xpwiki->db->fetchRow($res)) {
		if ($max_execution_time) @ ini_set('max_execution_time', (string)$max_execution_time);
		xpwiki_jobstack_switch($xpwiki, $row);
	}
}

function xpwiki_jobstack_switch (& $xpwiki, $row) {
	list($key, $data) = $row;
	$xpwiki->func->cache_del_db($key, 'jobstack');
	$data = unserialize($data);
	switch ($data['action']) {
		case 'http_get':
			$xpwiki->func->http_request($data['url']);
			break;
		case 'xmlrpc_ping_send':
			$xpwiki->func->send_update_ping();
			break;
		case 'plain_up':
		case 'plugin_func':
			$func = 'xpwiki_jobstack_' . $data['action'];
			$func($xpwiki, $data);
			break;
	}
}

function xpwiki_jobstack_plain_up (& $xpwiki, $data) {
	$mode = $data['mode'];
	$notimestamp = FALSE;
	if ($mode === 'update_notimestamp') {
		$notimestamp = TRUE;
		$mode = 'update';
	}
	$xpwiki->func->plain_db_write($data['page'], $mode, FALSE, $notimestamp);

	// 古いレンダーキャッシュファイルの削除 (1日1回程度)
	$pagemove_time = @ filemtime($xpwiki->cont['CACHE_DIR'] . 'pagemove.time');
	if ($pagemove_time) {
		$render_cache_clr = @ filemtime($xpwiki->cont['CACHE_DIR'] . 'render_cache_clr.time');
		if ($render_cache_clr < $xpwiki->cont['UTC'] - 86400) {
			$xpwiki->func->pkwk_touch_file($xpwiki->cont['CACHE_DIR'] . 'render_cache_clr.time');
			if ($handle = opendir($xpwiki->cont['RENDER_CACHE_DIR'])) {
				while (false !== ($file = readdir($handle))) {
					if (substr($file, 0, 7) === 'render_') {
						$file = $xpwiki->cont['RENDER_CACHE_DIR'] . $file;
						if (filemtime($file) < $pagemove_time) {
							unlink($file);
						}
					}
				}
				closedir($handle);
			}
		}
	}
}

function xpwiki_jobstack_plugin_func (& $xpwiki, $data) {
	$plugin = $data['plugin'];
	$func = $data['func'];
	if ($plugin = $xpwiki->func->get_plugin_instance($plugin)) {
		if (method_exists($plugin, $func)) {
			$plugin->$func($data['args']);
		}
	}
}
?>