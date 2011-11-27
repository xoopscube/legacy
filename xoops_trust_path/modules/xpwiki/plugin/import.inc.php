<?php
/*
 * Created on 2007/05/22 by nao-pon http://hypweb.net/
 * $Id: import.inc.php,v 1.13 2011/11/26 12:03:10 nao-pon Exp $
 */

class xpwiki_plugin_import extends xpwiki_plugin {

	function plugin_import_init()
	{
		$this->FILEMODE = 0666;
		
		$this->timelimit = 60;
	}
	
	function plugin_import_action() {
		// 権限チェック
		if (!$this->root->userinfo['admin']) {
			return $this->action_msg_admin_only();
		}

		// 言語ファイルの読み込み
		$this->load_language();
		
		// 管理画面モード指定
		if ($this->root->module['platform'] == "xoops") {
			$this->root->runmode = "xoops_admin";
		}

		$pmode = (empty($this->root->post['pmode']))? '' : $this->root->post['pmode'];
		$base = (empty($this->root->vars['base']))? '' : $this->root->vars['base'];
		if (!$pmode || !empty($this->root->post['go_first'])) {
			return $this->show_first();
		} else if ($pmode === 'select_option') {
			return $this->select_option();
		} else if ($pmode === 'confirm') {
			return $this->confirm();
		} else if ($pmode === 'do_check') {
			return $this->do_check();
		} else if ($pmode === 'do_copy') {
			return $this->do_copy();
		} else if ($pmode === 'do_convert') {
			return $this->do_convert();
		}
	}
	
	function replace_msg ($msg) {
		return str_replace(array('$to', '$from'), array($this->root->mydirname, $this->from_dir), $msg);
	}
	
	function show_first () {
		$dir = dirname($this->cont['DATA_HOME']);
		$items = array();
		if ($dh = opendir($dir)) {
			while (($item = readdir($dh)) !== false) {
				if (is_dir($dir.'/'.$item)) {
					if (is_file($dir.'/'.$item.'/pukiwiki.ini.php')) {
						if (is_file($dir.'/'.$item.'/db_func.php')) {
							$items[$item] = 'pwm';
						} else {
							$items[$item] = 'bwiki';
						}
					}
				}
			}
			closedir($dh);
		}
		$select = '<select name="dir">';
		foreach($items as $dir => $type) {
			$select .= '<option value="'.$dir.'#'.$type.'">'.$dir.'</option>'; 
		}
		$select .= '</select>';
		$script = $this->func->get_script_uri();
		$ret['msg'] = $this->msg['title_import_dir'];
		$ret['body'] = <<<EOD
<p>{$this->msg['import_dir']}</p>
<form action="{$script}" method="post">
{$select} <input type="submit" value="{$this->msg['btn_do_next']}" />
<input type="hidden" name="cmd" value="import" />
<input type="hidden" name="pmode" value="select_option" />
</form>
EOD;
		return $ret;

	}

	function select_option($msg = '') {
		$dir = (empty($this->root->post['dir']))? '' : $this->root->post['dir'];
		$target_page = (empty($this->root->post['target_page']))? '' : $this->root->post['target_page'];
		$keep_pgid = (empty($this->root->post['keep_pgid']))? '' : intval($this->root->post['keep_pgid']);
		$keep_page = (empty($this->root->post['keep_page']))? '' : intval($this->root->post['keep_page']);
		
		list($mdir, $type) = explode('#', $dir);
		
		$keep_pgid_sel = array_pad(array(), 3, '');
		$keep_pgid_sel[$keep_pgid] = ' checked';
		$keep_page_sel = array_pad(array(), 3, '');
		$keep_page_sel[$keep_page] = ' checked';
		
		$target_module = $this->msg['target_module'] . htmlspecialchars($mdir) . htmlspecialchars(' -> ' . $this->root->mydirname);
		
		$this->from_dir = $mdir;
		$this->msg = array_map(array(& $this, 'replace_msg'), $this->msg);

		$script = $this->func->get_script_uri();
		$form = '<form action="'.$script.'" method="post">';
		$form .= $this->msg['target_page'].$this->msg['target_page_note'].'<br /><input type="text" name="target_page" value="'.htmlspecialchars($target_page).'" size="50" /><br />';
		if ($type === 'pwm') {
			$form .= $this->msg['keep_pgid'].'<br />';
			$form .= '&nbsp;&nbsp;<input type="radio" name="keep_pgid" value="1"'.$keep_pgid_sel[1].' /> '.$this->msg['keep_pgid_1'].'<br />';
			$form .= '&nbsp;&nbsp;<input type="radio" name="keep_pgid" value="2"'.$keep_pgid_sel[2].' /> '.$this->msg['keep_pgid_2'].'<br />';
			$form .= $this->msg['keep_page'].'<br />';
			$form .= '&nbsp;&nbsp;<input type="radio" name="keep_page" value="1"'.$keep_page_sel[1].' /> '.$this->msg['keep_page_1'].'<br />';
			$form .= '&nbsp;&nbsp;<input type="radio" name="keep_page" value="2"'.$keep_page_sel[2].' /> '.$this->msg['keep_page_2'].'<br />';
		} else if ($type === 'bwiki') {
			$form .= $this->msg['keep_page'].'<br />';
			$form .= '&nbsp;&nbsp;<input type="radio" name="keep_page" value="1"'.$keep_page_sel[1].' /> '.$this->msg['keep_page_1'].'<br />';
			$form .= '&nbsp;&nbsp;<input type="radio" name="keep_page" value="2"'.$keep_page_sel[2].' /> '.$this->msg['keep_page_2'].'<br />';
		}
		$dir = htmlspecialchars($dir);
		$form .= <<<EOD
<input type="submit" value="{$this->msg['btn_do_next']}" />
&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="go_first" value="{$this->msg['btn_go_first']}" />
<input type="hidden" name="cmd" value="import" />
<input type="hidden" name="dir" value="{$dir}" />
<input type="hidden" name="pmode" value="confirm" />
</form>
EOD;
		
		$ret['msg'] = $this->msg['title_select_option'];
		$ret['body'] = <<<EOD
{$target_module}
<hr />
<div>{$msg}</div>
{$form}
EOD;
		return $ret;
	}
	
	function confirm() {
		$dir = (empty($this->root->post['dir']))? '' : $this->root->post['dir'];
		$target_page = (empty($this->root->post['target_page']))? '' : $this->root->post['target_page'];
		$keep_pgid = (empty($this->root->post['keep_pgid']))? 0 : intval($this->root->post['keep_pgid']);
		$keep_page = (empty($this->root->post['keep_page']))? 0 : intval($this->root->post['keep_page']);
		
		list($mdir, $type) = explode('#', $dir);
		
		if ($type === 'bwiki') $keep_pgid = 2;

		$this->from_dir = $mdir;
		$this->msg = array_map(array(& $this, 'replace_msg'), $this->msg);

		if (!$keep_pgid || !$keep_page) return $this->select_option($this->msg['invalid_option']);
		
		$target_module = $this->msg['target_module'] . htmlspecialchars($mdir) . htmlspecialchars(' -> ' . $this->root->mydirname);
		if ($target_page) $target_page = join(' & ', preg_split('/\s*&\s*/',$target_page));
		$target_page = htmlspecialchars($target_page);
		
		$options = array();
		$options[$this->msg['target_page']] = $target_page ? $this->msg['target_page_sel'] . $target_page : $this->msg['target_page_all'];
		$options[$this->msg['keep_pgid']] = $this->msg['keep_pgid_'.$keep_pgid];
		$options[$this->msg['keep_page']] = $this->msg['keep_page_'.$keep_page];
		
		$option = '';
		foreach ($options as $key => $val) {
			$option .= $key . ': ' . $val . '<br />';
		}
		$option .= '<p>'.$this->msg['do_check_note'].'</p>';
		$script = $this->func->get_script_uri();
		$form = '';
		$form .= <<<EOD
<form action="{$script}" method="post">
<input type="submit" value="{$this->msg['btn_do_check']}" />
&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="go_first" value="{$this->msg['btn_go_first']}" />
<input type="hidden" name="cmd" value="import" />
<input type="hidden" name="dir" value="{$dir}" />
<input type="hidden" name="target_page" value="{$target_page}" />
<input type="hidden" name="keep_pgid" value="{$keep_pgid}" />
<input type="hidden" name="keep_page" value="{$keep_page}" />
<input type="hidden" name="pmode" value="do_check" />
</form>
EOD;

		$ret['msg'] = $this->msg['title_do_check'];
		$ret['body'] = <<<EOD
{$target_module}
<hr />
{$option}
<hr />
{$form}
EOD;
		return $ret;
	}

	function do_check() {
		$dir = (empty($this->root->post['dir']))? '' : $this->root->post['dir'];
		$target_page = (empty($this->root->post['target_page']))? '' : $this->root->post['target_page'];
		$keep_pgid = (empty($this->root->post['keep_pgid']))? 0 : intval($this->root->post['keep_pgid']);
		$keep_page = (empty($this->root->post['keep_page']))? 0 : intval($this->root->post['keep_page']);
		
		list($mdir, $type) = explode('#', $dir);
		$this->from_dir = $mdir;
		$this->msg = array_map(array(& $this, 'replace_msg'), $this->msg);

		
		$op = array(
			'mdir' => $mdir,
			'type' => $type,
			'target_page' => $target_page,
			'keep_pgid' => $keep_pgid,
			'keep_page' => $keep_page
		);
		
		$body = '';
		if ($type === 'pwm') {
			define('XOOPS_WIKI_PATH', XOOPS_ROOT_PATH . '/modules/' . $mdir);
			include (XOOPS_ROOT_PATH . '/modules/' . $mdir . '/pukiwiki.ini.php');
			list($result, $data, $files) = $this->file_check($op);
		} else if ($type === 'bwiki') {
			define('DATA_HOME', XOOPS_ROOT_PATH . '/modules/' . $mdir . '/');
			include (XOOPS_ROOT_PATH . '/modules/' . $mdir . '/pukiwiki.ini.php');
			list($result, $data, $files) = $this->file_check($op);
		}
		if ($result === false) {
			$script = $this->func->get_script_uri();
			$form = <<<EOD
<form action="{$script}" method="post">
<input type="submit" value="{$this->msg['btn_do_check']}" />
&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="go_first" value="{$this->msg['btn_go_first']}" />
<input type="hidden" name="cmd" value="import" />
<input type="hidden" name="dir" value="{$op['mdir']}#{$op['type']}" />
<input type="hidden" name="target_page" value="{$op['target_page']}" />
<input type="hidden" name="keep_pgid" value="{$op['keep_pgid']}" />
<input type="hidden" name="keep_page" value="{$op['keep_page']}" />
<input type="hidden" name="pmode" value="do_check" />
</form>
EOD;
			$ret['msg'] = $this->msg['msg_error'];
			$ret['body'] = $this->func->convert_html($data) . '<hr />' . $form;

			return $ret;
		} else {
			// コピーするファイルの情報
			$body = '';
			$cpage = false;
			if ($data) {
				foreach($data as $dir => $file) {
					$count = count($file);
					$body .= basename($dir) . ': ' . $count . ' files.<br />';
					if ($count) $cpage = true;
				}
			}
			$body .= ($body)? '<p>'.$this->msg['do_copy_note'].'</p>' : '<p>'.$this->msg['do_copy_nothing'].'</p>';
			$script = $this->func->get_script_uri();
			if ($cpage) {
				if ($fp = fopen($this->cont['CACHE_DIR'].'copy.import', 'wb')) {
					fwrite($fp, serialize(array($op, $files)));
					fclose($fp);
				}
				$form .= <<<EOD
<form action="{$script}" method="post">
<input type="submit" value="{$this->msg['btn_do_copy']}" />
&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="go_first" value="{$this->msg['btn_go_first']}" />
<input type="hidden" name="cmd" value="import" />
<input type="hidden" name="pmode" value="do_copy" />
</form>
EOD;
				$ret['msg'] = $this->msg['title_do_import'];
				$ret['body'] = <<<EOD
$body
<hr />
{$form}
EOD;
				return $ret;
			} else {
				$form .= <<<EOD
<form action="{$script}" method="post">
&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="go_first" value="{$this->msg['btn_go_first']}" />
<input type="hidden" name="cmd" value="import" />
</form>
EOD;
				$ret['msg'] = $this->msg['title_no_files'];
				$ret['body'] = <<<EOD
$body
<hr />
{$form}
EOD;
				return $ret;
			}
		}
	}

	function file_check($op)
	{
		error_reporting(E_ALL);
		
		$error = array('already'=>array(),'invalid'=>array(),'writable'=>array());
		
		$dirs = array(
			UPLOAD_DIR => $this->cont['UPLOAD_DIR'],
			BACKUP_DIR => $this->cont['BACKUP_DIR'],
			COUNTER_DIR => $this->cont['COUNTER_DIR'],
			DIFF_DIR => $this->cont['DIFF_DIR'],
			DATA_DIR => $this->cont['DATA_DIR']
		);
		
		$files = $files2 = array();
		foreach ($dirs as $from => $to) {
			
			$is_datadir = (DATA_DIR === $from);
			$from = preg_replace('#^\./#', XOOPS_ROOT_PATH.'/modules/'.$op['mdir'].'/', $from);
			if (!is_dir($from) or !$dp = @opendir($from)) {
				$error['writable'][] = array($this->msg['err_no_from_dir'],$from,'');
				continue;
			}
			if (!is_dir($to)) {
				$error['writable'][] = array($this->msg['err_no_to_dir'],$to,'');
				continue;
			}
			if (!is_writable($to)) {
				$error['writable'][] = array($this->msg['err_writable_to'],$to,'');
				continue;
			}
			
			while ($file = readdir($dp)) {
				if ($file{0} == '.' or !preg_match('/^((?:[0-9A-F]{2})+)/',$file,$matches)) {
					continue;
				}
				$page = $this->func->decode($matches[1]);
				
				if (preg_match('/^5B5B([^_]+)5D5D(.+)$/',$file,$matches)) {
					$newfile = $matches[1].$matches[2];
					$page = $this->func->decode($matches[1]);
					if (!$this->func->is_pagename($page)) {
						if ($is_datadir) $error['invalid'][] = array($page,$from,$newfile);
						continue;
					}
				} else if (!$this->func->is_pagename($page)) 	{
					if ($is_datadir) $error['invalid'][] = array($page,$from,$file);
					continue;
				} else 	{
					$newfile = $file;
				}
				
				if ($op['keep_page'] === 2) {
					if (is_file($to.$newfile)) continue;
				}
				
				// target_page check
				if ($op['target_page']) {
					$match = false;
					foreach(preg_split('/\s*&\s*/', $op['target_page']) as $_page) {
						if (strpos($newfile, $this->func->encode($_page)) === 0) {
							$match = true;
							break;
						}
					}
					if (!$match) continue;
				}
				
				$files[$from][$file] = $to.$newfile;
				$files2[$from.$file] = $to.$newfile;
			}
			closedir($dp);
		}
		
		$_error = FALSE;
		$result = '';
		
		foreach ($error as $key=>$array)
		{
			if (count($array))
			{
				$_error = TRUE;
				$result .= $this->msg['err_'.$key]."\n";
			}
			$dlist = '';
			$pre = '';
			foreach ($array as $arr)
			{
				$pre .= ' '.$arr[1].$arr[2]."\n";
				$dlist .= ':'.str_replace(array('|',':'),array('&#x7c;','&#x3a;'),htmlspecialchars($arr[0])).'|'.$arr[1].$arr[2]."\n";
			}
			$result .= $pre."\n\n".$dlist;
		}
		if ($_error) {
			$result = array(false, $this->msg['msg_error'].$result, array());
		} else {
			$result = array(true, $files, $files2);
		}
		return $result;
	}

	function do_copy() {
		// 実行時間セット
		@ set_time_limit($this->timelimit);
		$timelimit = $this->cont['UTC'] + (min(ini_get('max_execution_time'),$this->timelimit)) - 5;

		list($op, $files) = unserialize(file_get_contents($this->cont['CACHE_DIR'].'copy.import'));
		// proceed
		$umask = umask(0777 xor $this->FILEMODE);
		
		$pages = array();
		$_files = $files;
		foreach ($files as $old => $newfile) {
			// get owner
			copy($old, $newfile);
			touch($newfile,filemtime($old));
			
			//echo "touch($newfile,filemtime($dir.$file))<br />";
			if ($op['type'] === 'pwm' && basename(dirname($newfile)) === basename($this->cont['DATA_DIR'])) {
				$pages[] = $this->func->decode(preg_replace('/.txt$/', '', basename($newfile)));
			}
			unset($_files[$old]);
			if ($timelimit < time()) break;
		}
		
		if ($_files) {
			if ($fp = fopen($this->cont['CACHE_DIR'].'copy.import', 'wb')) {
				fwrite($fp, serialize(array($op, $_files)));
				fclose($fp);
			}
		} else {
			unlink($this->cont['CACHE_DIR'].'copy.import');
		}
		
		// 要書式コンバートページデータファイル作成
		if (is_file($this->cont['CACHE_DIR'].'convert.import')) {
			$_pages = file($this->cont['CACHE_DIR'].'convert.import');
			$_pages = array_map('trim', $_pages);
			array_shift($_pages);
			$pages = array_merge($_pages, $pages);
			$pages = array_unique($pages);
		}
		if ($pages) {
			array_unshift($pages, $op['mdir']);
			if ($fp = fopen($this->cont['CACHE_DIR'].'convert.import', 'wb')) {
				fwrite($fp, join("\n", $pages));
				fclose($fp);
			}
		} else {
			@ unlink($this->cont['CACHE_DIR'].'convert.import');
		}
		
		umask($umask);
		
		// 続きあり
		if ($_files) {
			return $this->do_more_form ('do_copy', $this->msg['more_copy_note'], count($_files));
		}
		if (is_file($this->cont['CACHE_DIR'].'convert.import')) {
			if ($op['type'] === 'pwm') {
				$this->set_pgid($op);
			}
			return $this->get_do_convert_form();
		}
		$this->done_all();
	}

	function do_more_form ($pmode, $note, $count) {
		$body = str_replace('$count', $count, $this->msg['do_more']);
		$script = $this->func->get_script_uri();
		$form = <<<EOD
<form action="{$script}" method="post">
<input type="submit" value="{$this->msg['btn_do_more']}" />
<input type="hidden" name="cmd" value="import" />
<input type="hidden" name="pmode" value="{$pmode}" />
</form>
EOD;
		$ret['msg'] = $this->msg['title_do_more'];
		$ret['body'] = <<<EOD
$note
$body
<hr />
{$form}
EOD;
		return $ret;
	}

	function get_do_convert_form () {
		$wiki = $this->func->convert_html($this->msg['do_convert_wiki']);
		$script = $this->func->get_script_uri();
		$ret['msg'] = $this->msg['title_convert'];
		$ret['body'] = <<<EOD
<p>{$this->msg['do_convert_note']}</p>
<hr />
{$wiki}
<form action="{$script}" method="post">
<input type="submit" value="{$this->msg['do_convert']}" />
<input type="hidden" name="cmd" value="import" />
<input type="hidden" name="pmode" value="do_convert" />
</form>
EOD;
		return $ret;
	}

	function set_pgid($op) {
		if ($op['type'] === 'pwm') {
			
			$pages = file($this->cont['CACHE_DIR'].'convert.import');
			$mdir = rtrim(array_shift($pages));
			$pages = array_map('trim', $pages);
			
			$db =& $this->xpwiki->db;

			// インポート元 pginfoDB 読み込み
			preg_match( '/^(\D+)(\d*)$/' , $op['mdir'] , $regs );
			$dir_num = ($regs[2] === '')? '' : intval( $regs[2] ) ;

			$query = "SELECT `id`, `name` FROM ".$db->prefix('pukiwikimod'.$dir_num.'_pginfo');
			$res = $db->query($query);
			$pgid_from = array();
			if ($res) {
				while($row = $db->fetchRow($res)) {
					if (in_array($row[1], $pages)) {
						$pgid_from['0'.strval($row[0])] = $row[1];
					}
				}
			}
			//var_dump($pgid_from);
			//exit();
			
			// あて先 pginfoDB 読み込み
			$db =& $this->xpwiki->db;
			$query = "SELECT `pgid`, `name` FROM ".$db->prefix($this->root->mydirname.'_pginfo');
			$res = $db->query($query);
			$pgid_to = array();
			if ($res) {
				while($row = $db->fetchRow($res)) {
					$pgid_to['0'.strval($row[0])] = $row[1];
				}
			}
			if ($op['keep_pgid'] === 1) {
				// インポート元のpgidにそろえる
				// pgid 継続のページ
				$id_nc = array_diff($pgid_from, $pgid_to);
				// pgid が変わるページ
				$id_def = array_diff($pgid_from, $id_nc);
				// 登録処理するページ
				$pgid = array_merge($pgid_to, $id_nc);
			} else {
				// インポート先のpgidにそろえる
				$id_def = array();
				$pgid = array_merge($pgid_from, array_diff($pgid_to, $pgid_from));
			}
			
			// TRUNCATE TABLE `hyp_xc_xpwiki_import_pginfo` 
			$query = 'TRUNCATE TABLE `'.$db->prefix($this->root->mydirname.'_pginfo').'`';
			$res = $db->query($query);
			if (!$res) return false;

			foreach($id_def as $id => $name) {
					$query = 'DELETE FROM `'.$db->prefix($this->root->mydirname.'_plain').'` WHERE `pgid`='.$id.' LIMIT 1';
					$res = $db->query($query);
			}
			
			foreach($pgid as $id => $name) {
				$id = intval($id);
				if ($id) {
					$query = 'INSERT INTO `'.$db->prefix($this->root->mydirname.'_pginfo').'` ( `pgid` , `name` , `name_ci`) VALUES ( \''.$id.'\', \''.addslashes($name).'\', \''.addslashes($name).'\' )'; 
					$res = $db->query($query);
				}
			}
			
			return true;
		}
	}

	//変換
	function do_convert() {
		
		// 実行時間セット
		@ set_time_limit($this->timelimit);
		$timelimit = $this->cont['UTC'] + (min(ini_get('max_execution_time'),$this->timelimit)) - 5;
		
		//更新したページ名
		$result = array();
		
		// ページ名の列挙
		$pages = file($this->cont['CACHE_DIR'].'convert.import');
		$mdir = rtrim(array_shift($pages));
		$pages = array_map('trim', $pages);
		
		// 変換
		while ($page = array_shift($pages)) {
			//$page = array_shift($pages);
			$data = $this->func->get_source($page);
			$this->page_convert($page,$data,$result,$mdir);
			$this->func->file_write($this->cont['DATA_DIR'],$page,$data,TRUE);
			if ($timelimit < time()) break;
		}
		
		/*
		// 結果
		$count = count($result);
		$postdata = join('',get_source(CONVERT_LOGPAGE));
		$postdata .= $_convert_messages['title_convert']."\n\n";
		$postdata .= str_replace('$1',$count,$_convert_messages['msg_count'])."\n";
		
		if ($count)
		{
			$postdata .= "\n----\n".$_convert_messages['msg_convert']."\n";
			$postdata .= join("\n",$result);
		}
		
		$this->func->page_write(CONVERT_LOGPAGE,$postdata);
		
		$vars['refer'] = CONVERT_LOGPAGE;
		return array('msg'=>'','body'=>'');
		*/
		
		if ($pages) {
			$count = count($pages);
			array_unshift($pages, $mdir);
			if ($fp = fopen($this->cont['CACHE_DIR'].'convert.import', 'wb')) {
				fwrite($fp, join("\n", $pages));
				fclose($fp);
			}
			return $this->do_more_form ('do_convert', $this->msg['more_convert_note'], $count);
			//return $this->get_do_convert_form();
		} else {
			$this->done_all();
		}
	}

	//書式を変換
	function page_convert($page,&$data,&$convert,$mdir) {
		
		$newfreeze = (preg_match('/^#newfreeze/m', join('',$data)))? 1 : 0;

		$data = preg_replace("/^(#(?:new)?freeze|#unvisible|\/\/ author(?:_ucd)?:).*$/s","",$data);

		$db =& $this->xpwiki->db;

		// インポート元 pginfoDB 読み込み
		$dir_num = '';
		if (preg_match( '/^(\D+)(\d+)$/' , $mdir , $regs )) {
			$dir_num = intval( $regs[2] );
		}

		$query = "SELECT `aids`,`gids`,`vaids`,`vgids`,`lastediter`,`uid`,`freeze`,`unvisible` FROM ".$db->prefix('pukiwikimod'.$dir_num.'_pginfo').' WHERE name="'.addslashes($page).'" LIMIT 1';
		if ($res = $db->query($query)) {
			list($aids, $gids, $vaids, $vgids, $lastediter, $uid, $freeze, $unvisible) = $db->fetchRow($res);
		}
		
		$uid = intval($uid);
		$user = $this->func->get_userinfo_by_id ($uid);
		$lastediter = intval($lastediter);
		$lastuser = $this->func->get_userinfo_by_id ($lastediter);
		
		$pginfo['uid']       = (int)$uid;
		$pginfo['ucd']       = '';
		$pginfo['uname']     = $user['uname'];
		$pginfo['einherit']  = ($freeze)? $newfreeze : 3;
		$pginfo['eaids']     = (!$aids || $aids === '&0&')? 'none' : ((strpos($aids,'all') !== false)? 'all' : trim($aids, '&'));
		$pginfo['egids']     = (!$gids || $gids === '&0&')? 'none' : ((strpos($gids,'all') !== false || strpos($gids,'&3&') !== false)? 'all' : trim($gids, '&'));
		$pginfo['vinherit']  = ($unvisible)? 1 : 3;
		$pginfo['vaids']     = (!$vaids || $vaids === '&0&')? 'none' : ((strpos($vaids,'all') !== false)? 'all' : trim($vaids, '&'));
		$pginfo['vgids']     = (!$vgids || $vgids === '&0&')? 'none' : ((strpos($vgids,'all') !== false || strpos($vgids,'&3&') !== false)? 'all' : trim($vgids, '&'));
		$pginfo['lastuid']   = $lastediter;
		$pginfo['lastucd']   = '';
		$pginfo['lastuname'] = $lastuser['uname'];
		$pginfo = '#pginfo('.join("\t",$pginfo).')'."\n";

		$bq = $last_bq = 0;
		$block = $last_block = '';
		$result = array();
		$modify = array();
		$in_multi_pre = false;

		foreach ($data as $line)
		{
			if (!$line)	{ continue; }
			
			// 複数pre行
			if (preg_match('/^<<</',$line)) {
				$result[] = '#code(){{{'."\n";
				$in_multi_pre = true;
				continue;
			}
			if (preg_match('/^>>>/',$line)) {
				$result[] = '}}}'."\n";
				$in_multi_pre = false;
				continue;				
			}
			if ($in_multi_pre || substr($line,0,2) == '//') {
				$result[] = $line;
				continue;
			}
			
			// #category を &tag(); に変換
			if (preg_match('/^#category\(([^\)]*)\)/', $line, $args)) {
				$cats = array();
				$align = "\n";
				foreach(explode(',', $args[1]) as $arg) {
					$arg = str_replace(" ","",$arg);
					if ($arg) {
						if ($arg{0} == ":")	{
							continue;
						} else if ($arg{0} == "#")	{
							$option = substr($arg,1);
							if (preg_match("/(left|center|right)/i",substr($arg,1),$option))
								$align = strtoupper($option[1]).":";
						} else {
							$cats[] = $arg;					
						}
					}
				}
				$result[] = $align . '&tag(' . join(',',$cats) . ');' . "\n";
				continue;
			}
			
			// attachref を ref に変換
			$line = preg_replace('/^#attachref/', '#ref', $line);
			$line = preg_replace('/&attachref/', '&ref', $line);


			//行頭書式をチェック
			$head = substr($line,0,1);
			$block = '';
			if (strpos('-+:>',$head) !== FALSE && substr(rtrim($line), -1) !== '~') //次の行を食うブロック
			{
				$block = $head;
			}
			else if (preg_match('/^(LEFT|CENTER|RIGHT):/',$line,$matches)) //次の行を食うブロック(Align)
			{
				$block = $matches[1];
			}
			
			//ネスト可能なブロック要素の直後の行かどうか
			if (
				$last_block != '' and               //前の行が"次の行を食うブロック要素で
				$block != $last_block and           //前の行と現在行の種類が違って
				($line != "\n" and $line != "\r\n") //現在行が空行でない場合
			)
			{
				$result[] = "\n"; //空行をはさむ
				$modify['nest'] = '--modify nest.';
			}
			
			//行頭+/-の直後のチルダをスペースでエスケープ
			if (preg_match("/^([\-\+]{1,3})(~.*)$/", $line, $matches)) //マッチしなかったら無視
			{
				$line = "{$matches[1]} {$matches[2]}\n";
				$modify['tilde'] = '--modify (+/-)...~. ';
			}
	/*		 
			//ブロッククオートの修正
			if ($head == '>' and preg_match("/^(>{1,3})(.*)$/",$line,$matches)) //マッチしなかったら無視
			{
				$bq = strlen($matches[1]);
				if ($bq == $last_bq) {
					$line = "{$matches[2]}\n";
					$modify['bq'] = '--modify blockquote.';
				}
			}
			else
			{
				$bq = 0;
			}
	*/		
			//定義リストの修正
			if ($head == ':' and preg_match("/^:([^:]+):(.*)/s",$line,$matches)) //マッチしなかったら無視
			{
				$line = ":{$matches[1]}|{$matches[2]}";
				$modify['dl'] = '--modify dl.';
			}
			
			$result[] = $line;

			$last_bq = $bq;
			$last_block = $block;
		}
		if (count($modify))
		{
			$_page = $this->func->strip_bracket($page);
			$convert[] = "-[[$_page]]\n".join("\n",$modify);
		}
		
		$data = $pginfo . join('',$result);
		$data = $this->func->make_str_rules($data);
	}

	function done_all () {
		$this->func->redirect_header($this->root->script.'?cmd=dbsync', 3, $this->msg['msg_all_done']);
	}
}
?>
