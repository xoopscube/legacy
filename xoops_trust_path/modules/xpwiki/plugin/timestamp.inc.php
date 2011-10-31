<?php
/*
 * Created on 2007/06/03 by nao-pon http://hypweb.net/
 * $Id: timestamp.inc.php,v 1.5 2008/04/25 02:27:38 nao-pon Exp $
 */

class xpwiki_plugin_timestamp extends xpwiki_plugin {
	
	function plugin_timestamp_action () {
		// 権限チェック
		if (!$this->root->userinfo['admin']) {
			return $this->action_msg_admin_only();
		}
		
		// 管理画面モード指定
		if ($this->root->module['platform'] == "xoops") {
			$this->root->runmode = "xoops_admin";
		}
		
		$pmode = (isset($this->root->vars['pmode']))? $this->root->vars['pmode'] : ''; 
		if ($pmode === 'makedata') { return $this->makedata(); }
		if ($pmode === 'restore') { return $this->restore(); }
		else { 
			$ret['msg'] = 'Utility of time stamp of Wiki data file';
			$ret['body'] = <<<EOD
<ul>
 <li><a href="?cmd=timestamp&amp;pmode=makedata">Make time stamp data. (.timestamp)</a></li>
 <li><a href="?cmd=timestamp&amp;pmode=restore">Restor from stamp data.</a></li>
</ul>
EOD;
			return $ret;
		}
	}
	
	function makedata () {
		$dat = '';
		if ($dir = @opendir($this->cont['DATA_DIR'])) {
			while($file = readdir($dir)) {
				if (substr($file, -4) === '.txt' && $file !== '526563656E744368616E676573.txt') {
					$time = filemtime($this->cont['DATA_DIR'].$file);
					$dat .= $file."\t".$time."\n";
				}
			}
		}
		
		$ng = '';
		$datafile = $this->cont['DATA_DIR'].'.timestamp';
		if ($fp = fopen($datafile, 'wb')) {
			fwrite($fp, $dat);
			fclose($fp);
		} else {
			$ng = 'NOT ';
		}

		$ret['msg'] = $ng.'Maked timestamp data.';
		$ret['body'] = $ng.'Maked a file "'.$this->cont['DATA_DIR'].'.timestamp"';
		return $ret;
	}
	
	function restore () {
		foreach(file($this->cont['DATA_DIR'] . '.timestamp') as $line) {
			list($file, $time) = explode("\t", $line);
			$page = $this->func->decode(substr($file, 0, strlen($file) - 4));
			$this->func->touch_page($page, intval(trim($time)));
		}
		$ret['msg'] = 'Resored timestamp data.';
		$ret['body'] = 'Resored from "'.$this->cont['DATA_DIR'].'.timestamp"';
		return $ret;
	}
}
?>
