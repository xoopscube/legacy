<?php
//
// Created on 2006/11/17 by nao-pon http://hypweb.net/
// $Id: dbsync.inc.php,v 1.45 2011/12/13 07:45:14 nao-pon Exp $
//

class xpwiki_plugin_dbsync extends xpwiki_plugin {

	function plugin_dbsync_init() {
		$this->conf['timelimit'] = 120;
		$this->conf['time_margin'] = 10;
	}

	function plugin_dbsync_action()
	{
		// 権限チェック
		if (!$this->root->userinfo['admin']) {
			return $this->action_msg_admin_only();
		}

		$max_execution_time = intval(ini_get('max_execution_time'));

		if ($max_execution_time < $this->conf['timelimit']) {
			@ set_time_limit($this->conf['timelimit']);
			$max_execution_time = intval(ini_get('max_execution_time'));
		}
		if ($max_execution_time < 1) {
			$this->conf['timelimit'] -= $this->conf['time_margin'];
		} else {
			if ($max_execution_time < $this->conf['time_margin'] * 2) {
				$this->conf['time_margin'] = intval($max_execution_time * 0.2);
			}
			$this->conf['timelimit'] = min($max_execution_time, $this->conf['timelimit']) - $this->conf['time_margin'];
		}

		// 言語ファイルの読み込み
		$this->load_language();

		$pmode = (empty($this->root->post['pmode']))? '' : $this->root->post['pmode'];
		$page = (empty($this->root->vars['page']))? '' : $this->root->vars['page'];

		if ($pmode === 'update') {
			// DB Update
			return $this->do_dbupdate();
		} else {
			// 管理画面モード指定
			if ($this->root->module['platform'] == "xoops") {
				$this->root->runmode = "xoops_admin";
			}
			return $this->show_admin_form();
		}
	}

	function show_admin_form () {
		//error_reporting(E_ERROR);

		$timelimit = $this->conf['timelimit'];
		$this->msg['msg_usage'] = str_replace(array("%1d","%2d"),array(ini_get('max_execution_time'), $timelimit),$this->msg['msg_usage']);
		$body = $this->func->convert_html($this->msg['msg_usage']);

		if ($this->root->userinfo['admin'])
		{
			$not = array();
			foreach(array("i","c","p","a") as $type)
			{
				if (is_file($this->cont['CACHE_DIR']."dbsync_".$type.".dat"))
				{
					$not[$type] = '<span style="color:red;font-weight:bold;"> *</span>';
				}
				else
				{
					$not[$type] = "";
				}
			}
			$script = $this->func->get_script_uri();
			$body .= <<<__EOD__
<script>
<!--
var xpwiki_dbsync_doing = false;
var xpwiki_dbsync_timerID;
function xpwiki_dbsync_done()
{
	document.getElementById('xpwiki_dbsync_submit').style.visibility = "visible";
}
function xpwiki_dbsync_blink(mode)
{
	var timer;
	clearTimeout(xpwiki_dbsync_timerID);

	if (mode == 'start') {
		document.getElementById('xpwiki_dbsync_submit').style.visibility = "hidden";
	}

	if (mode == 'stop')
	{
		xpwiki_dbsync_doing = false;
	}
	else
	{
		xpwiki_dbsync_doing = true;
	}

	if (!xpwiki_dbsync_doing || document.getElementById('xpwiki_dbsync_doing').style.visibility == "visible")
	{
		document.getElementById('xpwiki_dbsync_doing').style.visibility = "hidden";
		timer = 200;
	}
	else
	{
		document.getElementById('xpwiki_dbsync_doing').style.visibility = "visible";
		timer = 800;
	}

	if (mode == 'start') {xpwiki_dbsync_setmsg('xpwiki_dbsync_doing','{$this->msg['msg_now_doing']}');}

	if (mode == 'continue')
	{
		xpwiki_dbsync_setmsg('xpwiki_dbsync_doing','{$this->msg['msg_next_do']}');
		document.getElementById('xpwiki_dbsync_doing').style.visibility = "visible";
	}

	if (xpwiki_dbsync_doing && mode != 'continue')
	{
		xpwiki_dbsync_timerID = setTimeout("xpwiki_dbsync_blink()", timer);
	}
}
function xpwiki_dbsync_setmsg(id,msg)
{
	document.getElementById(id).innerHTML = msg;
}
-->
</script>
<form target="pukiwiki_dbsync_work" style= "margin:0px;" method="POST" action="{$script}">
 <div>
  <input type="hidden" name="plugin" value="dbsync" />
  <input type="hidden" name="pmode" value="update" />
  {$this->msg['msg_hint']}
  <div style="margin-left:20px;">
  <input type="checkbox" name="init" value="on" checked="true" />{$this->msg['msg_init']}{$not['i']}<br />
  &nbsp;&#9500;<input type="checkbox" name="reading" value="on" />{$this->msg['msg_reading']}<br />
  &nbsp;&#9500;<input type="radio" name="title" value="" checked="true" />{$this->msg['msg_noretitle']}<br />
  &nbsp;&#9492;<input type="radio" name="title" value="on" />{$this->msg['msg_retitle']}<br />
  <input type="checkbox" name="count" value="on" checked="true" />{$this->msg['msg_count']}{$not['c']}<br />
  <input type="checkbox" name="attach" value="on" checked="true" />{$this->msg['msg_attach_init']}{$not['a']}<br />
  <input type="checkbox" name="plain" value="on" checked="true" />{$this->msg['msg_plain_init']}{$not['p']}
  <input type="checkbox" name="p_info" value="on" />{$this->msg['msg_moreinfo']}<br />
  &nbsp;&#9500;<input type="radio" name="plain_all" value="" checked="true" />{$this->msg['msg_plain_init_notall']}<br />
  &nbsp;&#9492;<input type="radio" name="plain_all" value="on" />{$this->msg['msg_plain_init_all']}<br />
  &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="plain_bg" value="on" />{$this->msg['msg_background']}
 </div>
  <p>
   Time limit: <input type="text" size="4" name="timelimit" value="{$this->conf['timelimit']}" /> sec ( Max {$this->conf['timelimit']} )
  </p>
  <p>
   <input id="xpwiki_dbsync_submit" type="submit" value="{$this->msg['btn_submit']}" onClick="xpwiki_dbsync_blink('start');return true;" />
  </p>
 </div>
</form>
<div id="xpwiki_dbsync_doing" style="color:red;background-color:white;visibility:hidden;width:500px;text-align:center;">{$this->msg['msg_now_doing']}</div>
<div>{$this->msg['msg_progress_report']}</div>
<iframe src="" height="350" width="500" name="pukiwiki_dbsync_work"></iframe>
__EOD__;
			return array(
				'msg'=>$this->msg['title_update'],
				'body'=>$body
			);
		}
	}

	function do_dbupdate() {

		if (XC_CLASS_EXISTS('XoopsErrorHandler')) {
			$xoopsErrorHandler =& XoopsErrorHandler::getInstance();
			$xoopsErrorHandler->activate(true);
		}
		error_reporting(E_ERROR);
		//error_reporting(E_ALL);

		if (! $this->func->refcheck()) {
			exit('Invalid REFERER.');
		}

		$this->conf['start_time'] = $this->cont['UTC'];

		header ("Content-Type: text/html; charset=".$this->cont['CONTENT_CHARSET']);

		// 出力をバッファリングしない
		$this->func->clear_output_buffer();
		ob_implicit_flush(true);
		echo str_pad('',256); //for IE

		echo <<<__EOD__
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset={$this->cont['CONTENT_CHARSET']}" />
</head>
<body>
__EOD__;

		$this->root->post['init'] = (!empty($this->root->post['init']))? "on" : "";
		$this->root->post['count'] = (!empty($this->root->post['count']))? "on" : "";
		$this->root->post['reading'] = (!empty($this->root->post['reading']))? "on" : "";
		$this->root->post['title'] = (!empty($this->root->post['title']))? "on" : "";
		$this->root->post['plain'] = (!empty($this->root->post['plain']))? "on" : "";
		$this->root->post['plain_all'] = (!empty($this->root->post['plain_all']))? "on" : "";
		$this->root->post['attach'] = (!empty($this->root->post['attach']))? "on" : "";
		$this->root->post['p_info'] = (!empty($this->root->post['p_info']))? "on" : "";
		$this->root->post['plain_bg'] = (!empty($this->root->post['plain_bg']))? "on" : "";
		$this->root->post['timelimit'] = (!empty($this->root->post['timelimit']))? intval($this->root->post['timelimit']) : $this->conf['timelimit'];

		$this->conf['timelimit'] = min( $this->root->post['timelimit'], $this->conf['timelimit']);

		if ($this->root->post['init']) $this->pginfo_db_init();
		if ($this->root->post['count']) $this->count_db_init();
		if ($this->root->post['attach']) $this->attach_db_init();
		if ($this->root->post['plain']) $this->plain_db_init();

		// 各種キャッシュファイルの削除
		// For AutoLink
		$this->func->autolink_dat_update();

		// Update autoalias.dat (AutoAliasName)
		$aliases = $this->func->get_autoaliases();
		if (empty($aliases)) {
			// Remove
			@unlink($this->cont['CACHE_DIR'] . $this->cont['PKWK_AUTOALIAS_REGEX_CACHE']);
		} else {
			// Create or Update
			$this->func->autolink_pattern_write($this->cont['CACHE_DIR'] . $this->cont['PKWK_AUTOALIAS_REGEX_CACHE'],
				$this->func->get_autolink_pattern(array_keys($aliases), $this->root->autoalias, true));
		}

		// Clear cache *.autolink.api
		$base = $this->cont['CACHE_DIR'];
		if (function_exists('glob')) {
			chdir($base);
			if ($files = glob("*.autolink.api")) {
				foreach ($files as $file) {
					unlink($base.$file);
				}
			}
			chdir($this->cont['DATA_HOME']);
		} else {
			if ($dir = @opendir($base)) {
				while($file = readdir($dir)) {
					if (substr($file, -13) === '.autolink.api') unlink($base . $file);
				}
			}
		}

		// Remove facemarks.js
		@ unlink($this->cont['CACHE_DIR'] . md5(rtrim($this->cont['HOME_URL'], '/')) . '_facemarks.js');

		echo $this->msg['msg_done'];
		echo "<script>parent.xpwiki_dbsync_done();parent.xpwiki_dbsync_blink('stop');</script>";
		echo "</body></html>";
		exit();
	}

	// ページ情報データベース初期化
	function pginfo_db_init()
	{
	//	global $xoopsDB,$whatsnew,$post;

		if ($dir = @opendir($this->cont['DATA_DIR']))
		{
			$this->root->rtf['is_init'] = true;
			// 処理済ファイルデーター
			$work = $this->cont['CACHE_DIR']."dbsync_i.dat";
			$domix = $dones = array();
			$done = 0;
			if (is_file($work))
			{
				$dones = unserialize(file_get_contents($work));
				if (!isset($dones[1])) $dones[1] = array();
				$docnt = count($dones[1]);
				$domix = array_merge($dones[0],$dones[1]);
				$done = count($domix);
			}
			if ($done)
			{
				echo "<div style=\"font-size:14px;\"><b>DB '".$this->root->mydirname."_pginfo' Already converted {$docnt} pages.</b></div>";
			}

			echo "<div style=\"font-size:14px;\"><b>DB '".$this->root->mydirname."_pginfo' Now converting... </b>( * = 10 Pages)<hr>";

			$fcounter = $counter = 0;

			$files = array();
			while($file = readdir($dir))
			{
				$files[] = $file;
			}
			sort($files);

			$pginfo_obj =& $this->func->get_plugin_instance('pginfo');

			foreach(array_diff($files,$domix) as $file)
			{
				if($file == ".." || $file == "." || strstr($file,".txt")===FALSE)
				{
					$dones[0][] = $file;
					continue;
				}

				$name=$aids=$gids=$vaids=$vgids= "";
				$buildtime=$editedtime=$lastediter=$uid=$freeze=$unvisible = 0;

				unset($this->root->post['uid'], $this->root->post['einherit'], $this->root->post['vinherit'],
					$this->root->post['eaid'], $this->root->post['egid'], $this->root->post['vaid'], $this->root->post['vgid']);

				$page = $this->func->decode(trim(preg_replace("/\.txt$/"," ",$file)));

				if ($page === $this->root->whatsnew)
				{
					$dones[0][] = $file;
					@unlink($this->cont['DATA_DIR'].$file);
					continue;
				}

				$name = $this->func->strip_bracket($page);

				// id取得
				$id = $this->func->get_pgid_by_name($page);

				$reading = $this->root->post['reading']? addslashes($this->func->get_page_reading($name, TRUE)) : '';
				$name = addslashes($name);
				$editedtime = filemtime($this->cont['DATA_DIR'].$file) - $this->cont['LOCALZONE'];

				// ページ作成日を取得 (filectime, 最古バックアップ, DB:buildtime) の最小値
				$buildtime = filectime($this->cont['DATA_DIR'].$file) - $this->cont['LOCALZONE'];
				$backup = $this->func->_backup_file_exists($page) ? $this->func->get_backup($page, 1) : array();
				if ($backup) {
					$buildtime = min($backup['time'], $buildtime);
				}
				list($_buildtime) = $this->func->get_page_time_db($page);
				if ($_buildtime) {
					$buildtime = min($_buildtime, $buildtime);
				}
				if (!$buildtime || $buildtime > $editedtime) $buildtime = $editedtime;

				$src = $this->func->get_source($page, TRUE, TRUE);
				if (! $src) {
					@unlink($this->cont['DATA_DIR'].$file);
					continue;
				}

				// 凍結？
				if (preg_match("/^#freeze\s*?\n/", $src)) {
					$freeze = 1;
				}

				// 初回 or 所有者情報がないページ
				if (! $id || ! preg_match('/^#pginfo\b.*$/sm', $src)) {
					$this->root->post['uid'] = $this->root->userinfo['uid'];
				}

				// 管理領域設定 (#xoopsadmin)
				if (preg_match('/^#xoopsadmin\b.*$/sm', $src)) {
					$this->root->post['einherit'] = $this->root->post['vinherit'] = 0;
					//$pginfo['einherit'] = $pginfo['vinherit'] = 0;
					$this->root->post['eaid'] = $this->root->post['egid'] = $this->root->post['vaid'] = $this->root->post['vgid'] = 'none';
					//$pginfo['eaids'] = $pginfo['egids'] = $pginfo['vaids'] = $pginfo['vgids'] = 'none';
				}

				$pginfo_res = $pginfo_obj->save_parm($page);

				// pginfo
				// $pginfo = $this->func->get_pginfo($page, false);
				// $pginfo = $this->func->get_pginfo($page, substr($src, 0, 4096));
				$pginfo = $pginfo_res['pginfo'];

				foreach (array('uid', 'ucd', 'uname', 'einherit', 'vinherit', 'lastuid', 'lastucd', 'lastuname', 'pgorder') as $key) {
					$$key = addslashes($pginfo[$key]);
				}
				foreach (array('eaids', 'egids', 'vaids', 'vgids') as $key) {
					if ($pginfo[$key] === 'all' || $pginfo[$key] === 'none') {
						$$key = $pginfo[$key];
					} else {
						$$key = '&'.$pginfo[$key].'&';
					}
				}

				// タイトル情報
				$title = "";
				if (!$id || !empty($this->root->post['title']) || !$this->func->get_heading($page, true))
				{
					$title = addslashes($this->func->get_heading_init($page));
				}

				if (!$id)
				{
					// 新規作成
					$query = "INSERT INTO ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo").
						" (`name`,`title`,`buildtime`,`editedtime`,`uid`,`ucd`,`uname`,`freeze`,`einherit`,`eaids`,`egids`,`vinherit`,`vaids`,`vgids`,`lastuid`,`lastucd`,`lastuname`,`update`,`reading`,`name_ci`,`pgorder`)" .
						" values('$name','$title','$buildtime','$editedtime','$uid','$ucd','$uname','$freeze','$einherit','$eaids','$egids','$vinherit','$vaids','$vgids','$lastuid','$lastucd','$lastuname','1','$reading','$name','$pgorder')";
				}
				else
				{
					// アップデート
					if ($title)
					{
						$title = ",`title`='$title'";
					}
					//echo $title;
					if ($reading) {
						$reading = ',`reading`=\''.$reading.'\'';
					}
					$value =
						 "`name`='$name'"
						.$title
						.$reading
						.",`buildtime`='$buildtime'"
						.",`editedtime`='$editedtime'"
						.",`uid`='$uid'"
						.",`ucd`='$ucd'"
						.",`uname`='$uname'"
						.",`freeze`='$freeze'"
						.",`einherit`='$einherit'"
						.",`eaids`='$eaids'"
						.",`egids`='$egids'"
						.",`vinherit`='$vinherit'"
						.",`vaids`='$vaids'"
						.",`vgids`='$vgids'"
						.",`lastuid`='$lastuid'"
						.",`lastucd`='$lastucd'"
						.",`lastuname`='$lastuname'"
						.",`update`='1'"
						.",`name_ci`='$name'"
						.",`pgorder`='$pgorder'";
					$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE pgid = '$id' LIMIT 1;";
				}
				if (! $result = $this->xpwiki->db->queryF($query)) {
					echo 'SQL Error: ' . $query . '<br />';
				}

				$counter++;
				$dones[1][] = $file;
				if (($counter/10) == (floor($counter/10)))
				{
					echo "*";

				}
				if (($counter/100) == (floor($counter/100)))
				{
					echo " ( Done ".$counter." Pages !)<br />";

				}

				if ($this->check_time_limit())
				{
					// 処理済ファイルリスト保存
					if ($fp = fopen($work,"wb"))
					{
						fputs($fp,serialize($dones));
						fclose($fp);
					}
					closedir($dir);
					$this->plugin_dbsync_next_do();
				}
			}
			closedir($dir);

			echo " ( Done ".$counter." Pages !)<hr />";
			echo "</div>";

			// アップデートしなかったページ情報(テキストファイルがないページ)を削除済み(editedtime=0)にする
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET `editedtime` = '0' WHERE `update` = '0';";
			$result=$this->xpwiki->db->queryF($query);

			// アップデートフラグ戻し
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET `update` = '0';";
			$result=$this->xpwiki->db->queryF($query);

			@unlink ($work);
		}
		$this->root->post['init'] = "";
	}

	// ページカウンターデータベース初期化
	function count_db_init()
	{
	//	global $xoopsDB,$whatsnew,$post;

		// カウント情報
		if ($dir = @opendir($this->cont['COUNTER_DIR']))
		{
			// 処理済ファイルリストデーター
			$work = $this->cont['CACHE_DIR']."dbsync_c.dat";
			$domix = $dones = array();
			$done = 0;
			if (is_file($work))
			{
				$dones = unserialize(file_get_contents($work));
				if (!isset($dones[1])) $dones[1] = array();
				$docnt = count($dones[1]);
				$domix = array_merge($dones[0],$dones[1]);
				$done = count($domix);
			}
			if ($done)
			{
				echo "<div style=\"font-size:14px;\"><b>DB '".$this->root->mydirname."_counter' Already converted {$docnt} pages.</b></div>";
			}
			//else
			//{
			//	$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_count");
			//	$result=$this->xpwiki->db->queryF($query);
			//}

			echo "<div style=\"font-size:14px;\"><b>DB '".$this->root->mydirname."_counter' Now converting... </b>( * = 10 Pages)<hr>";


			$counter = 0;

			$files = array();
			while($file = readdir($dir))
			{
				$files[] = $file;
			}

			foreach(array_diff($files,$domix) as $file)
			{
				if($file == ".." || $file == "." || strstr($file,".count")===FALSE)
				{
					$dones[0][] = $file;
					continue;
				}

				$name=$today=$ip="";
				$count=$today_count=$yesterday_count=0;

				$page = $this->func->decode(trim(preg_replace("/\.count$/"," ",$file)));
				// 存在しないページ
				if ($page === $this->root->whatsnew || !is_file($this->cont['DATA_DIR'].$this->func->encode($page).".txt"))
				{
					@unlink($this->cont['COUNTER_DIR'].$file);
					$dones[0][] = $file;
					continue;
				}

				if (! $pgid = $this->func->get_pgid_by_name($page)) {
					//@unlink($this->cont['COUNTER_DIR'].$file);
					$dones[0][] = $file;
					continue;
				}

				$array = array_pad(file($this->cont['COUNTER_DIR'].$file), 5, '');
				$count = intval(rtrim($array[0]));
				$today = intval(rtrim($array[1]));
				$today_count = intval(rtrim($array[2]));
				$yesterday_count = intval(rtrim($array[3]));
				$ip = rtrim($array[4]);

				$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_count")." SET count=$count,today='$today',today_count=$today_count,yesterday_count=$yesterday_count,ip='$ip' WHERE pgid='$pgid' LIMIT 1;";
 				if (! $result = $this->xpwiki->db->queryF($query)) {
					echo 'SQL Error: ' . $query . '<br />';
				} else {
					if (! $this->xpwiki->db->getAffectedRows()) {
						$query = "INSERT INTO ".$this->xpwiki->db->prefix($this->root->mydirname."_count")." (pgid,count,today,today_count,yesterday_count,ip) values('$pgid',$count,'$today',$today_count,$yesterday_count,'$ip');";
						if (! $result = $this->xpwiki->db->queryF($query)) {
							echo 'SQL Error: ' . $query . '<br />';
						} else {
							@unlink($this->cont['COUNTER_DIR'].$file);
						}
					} else {
						@unlink($this->cont['COUNTER_DIR'].$file);
					}
				}

				$counter++;
				if (($counter/10) == (floor($counter/10)))
				{
					echo "*";

				}
				if (($counter/100) == (floor($counter/100)))
				{
					echo " ( Done ".$counter." Pages !)<br />";

				}

				$dones[1][] = $file;

				if ($this->check_time_limit())
				{
					// 処理済ファイルリスト保存
					if ($fp = fopen($work,"wb"))
					{
						fputs($fp,serialize($dones));
						fclose($fp);
					}
					closedir($dir);
					$this->plugin_dbsync_next_do();
				}
			}
			closedir($dir);
			echo " ( Done ".$counter." Pages !)<hr />";
			echo "</div>";

			@unlink ($work);
		}
		$this->root->post['count'] = "";
	}

	// 検索用 plain DB 再設定
	function plain_db_init()
	{
	//	global $xoopsDB,$whatsnew,$vars,$post,$get,$related,$comment_no;

		if ($dir = @opendir($this->cont['DATA_DIR']))
		{
			$this->root->rtf['is_init'] = true;
			// 処理済ファイルリストデーター
			$work = $this->cont['CACHE_DIR']."dbsync_p.dat";
			$domix = $dones = array();
			$done = 0;
			if (is_file($work))
			{
				$dones = unserialize(file_get_contents($work));
				if (!isset($dones[1])) $dones[1] = array();
				$docnt = count($dones[1]);
				$domix = array_merge($dones[0],$dones[1]);
				$done = count($domix);
			}
			if ($done)
			{
				echo "<div style=\"font-size:14px;\"><b>DB '".$this->root->mydirname."_plain' Already converted {$docnt} pages.</b></div>";
			}

			echo "<div style=\"font-size:14px;\"><b>DB '".$this->root->mydirname."_plain' Now converting... </b>( * = 10 Pages)<hr>";


			$counter = 0;

			$files = array();
			while($file = readdir($dir))
			{
				$files[] = $file;
			}

			// PHP の上限メモリーサイズ
			$memory_limit = HypCommonFunc::return_bytes(ini_get('memory_limit'));

			$debug = array();

			foreach(array_diff($files,$domix) as $file)
			{
				if($file == ".." || $file == "." || strstr($file,".txt")===FALSE)
				{
					$dones[0][] = $file;
					continue;
				}

				$this->root->related = array();
				$page = $this->func->decode(trim(preg_replace("/\.txt$/"," ",$file)));
				$this->root->vars['page']=$this->root->get['page']=$this->root->post['page'] = $page;
				$this->root->comment_no = 0;

				if($page === $this->root->whatsnew)
				{
					$dones[0][] = $file;
					continue;
				}

				$id = $this->func->get_pgid_by_name($page, FALSE, TRUE);
				$query = "SELECT plain FROM `".$this->xpwiki->db->prefix($this->root->mydirname."_plain")."` WHERE `pgid` = ".$id.";";
				$result = $this->xpwiki->db->query($query);
				if ($result && mysql_num_rows($result))
				{
					list($text) = $this->xpwiki->db->fetchRow( $result );
					if ($text && !$this->root->post['plain_all'])
					{
						$dones[0][] = $file;
						continue;
					}
					$mode = "update";
				}
				else
				{
					$mode = "insert";
				}

				$s_page = htmlspecialchars($page);
				if ($this->root->post['p_info']) { echo 'Start: '. $s_page . '<br />'; }
				if ($this->root->post['plain_bg'] || $this->func->plain_db_write($page,$mode,TRUE))
				{
					if ($this->root->post['plain_bg']) {
						$this->func->need_update_plaindb($page, $mode, FALSE, FALSE);
					}

					$dones[1][] = $file;
					$counter++;
					if ($this->root->post['p_info']) {
						echo 'Finish: ' . $s_page . ((function_exists('memory_get_usage'))? ' - M: ' . number_format(memory_get_usage()) : '') . '<br />';
					} else {
						if (($counter/10) == (floor($counter/10))) {
							echo "*";
						}
					}
					if (($counter/100) == (floor($counter/100))) {
						echo " ( Done ".$counter." Pages !)<br />";
					}
				}
				else
				{
					if ($this->root->post['p_info']) { echo 'Error: '. $s_page . '<br />'; }
					$dones[0][] = $file;
				}

				if (function_exists('memory_get_usage')) {
					// メモリーチェック マージン 1MB (1024 * 1024 = 1048576)
					$memory_full = ($memory_limit && memory_get_usage() + 1048576 > $memory_limit);
				} else {
					$memory_full = false;
				}

				if ($memory_full || $this->check_time_limit())
				{
					// 処理済ファイルリスト保存
					if ($fp = fopen($work,"wb"))
					{
						fputs($fp,serialize($dones));
						fclose($fp);
					}
					closedir($dir);
					$this->plugin_dbsync_next_do();
				}
			}
			closedir($dir);
			$this->root->vars['page']=$this->root->get['page']=$this->root->post['page'] = "";
			$this->root->post['plain'] = "";
			$this->func->autolink_dat_update();
			echo " ( Done ".$counter." Pages !)<hr />";
			if (!empty($debug['donepage']) && $this->root->post['p_info']) {
				echo join('<br />', $debug['donepage']);
			}
			echo "</div>";

			@unlink ($work);
		}
	}

	// 添付ファイル DB 再設定
	function attach_db_init()
	{
	//	global $xoopsDB,$vars,$post,$get;
		if (!$this->func->exist_plugin('attach')) return;
		$attach = $this->func->get_plugin_instance('attach');
		if (!$attach) return;
		if ($dir = @opendir($this->cont['UPLOAD_DIR']))
		{
			// 処理済ファイルリストデーター
			$work = $this->cont['CACHE_DIR']."dbsync_a.dat";
			$domix = $dones = array();
			$done = 0;
			if (is_file($work))
			{
				$dones = unserialize(file_get_contents($work));
				if (!isset($dones[1])) $dones[1] = array();
				$docnt = count($dones[1]);
				$domix = array_merge($dones[0],$dones[1]);
				$done = count($domix);
			}
			if ($done)
			{
				echo "<div style=\"font-size:14px;\"><b>DB '".$this->root->mydirname."_attach' Already converted {$docnt} pages.</b></div>";
			}
			else
			{
				$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_attach");
				if (! $result = $this->xpwiki->db->queryF($query)) {
					echo 'SQL Error: ' . $query . '<br />';
				}
			}
			echo "<div style=\"font-size:14px;\"><b>DB '".$this->root->mydirname."_attach' Now converting... </b>( * = 10 Pages)<hr>";

			$counter = 0;

			$page_pattern = '(?:[0-9A-F]{2})+';
			$age_pattern = '(?:\.([0-9]+))?';
			$pattern = "/^({$page_pattern})_((?:[0-9A-F]{2})+){$age_pattern}$/";

			$files = array();
			while($file = readdir($dir))
			{
				$files[] = $file;
			}

			foreach(array_diff($files,$domix) as $file)
			{
				$matches = array();
				if (!preg_match($pattern,$file,$matches))
				{
					$dones[0][] = $file;
					continue;
				}
				$page = $this->func->decode($matches[1]);
				$name = $this->func->decode($matches[2]);
				$age = array_key_exists(3,$matches) ? $matches[3] : 0;

				// サムネイルは除外
				if (preg_match("/^\d\d?%/",$name))
				{
					$dones[0][] = $file;
					continue;
				}

				$obj = &new XpWikiAttachFile($this->xpwiki, $page,$name,$age);
				$obj->getstatus();

				$obj->status['md5'] = md5_file($obj->filename);
				$obj->putstatus();

				$data['pgid'] = $this->func->get_pgid_by_name($page);
				$data['name'] = $name;
				$data['mtime'] = $obj->time;
				$data['size'] = $obj->size;
				$data['type'] = $obj->type;
				$data['status'] = $obj->status;
				$data['status']['age'] = $age;

				// ページが存在しない
				if (! $data['pgid']) {
					$dones[0][] = $file;
					continue;
				}

				if ($this->func->attach_db_write($data,"insert"))
				{
					//echo "$page::$name;:$age<br >";
					$counter++;
					$dones[1][] = $file;
					if (($counter/10) == (floor($counter/10)))
					{
						echo "*";

					}
					if (($counter/100) == (floor($counter/100)))
					{
						echo " ( Done ".$counter." Files !)<br />";

					}
				}
				else
				{
					$dones[0][] = $file;
				}

				if ($this->check_time_limit())
				{
					// 処理済ファイルリスト保存
					if ($fp = fopen($work,"wb"))
					{
						fputs($fp,serialize($dones));
						fclose($fp);
					}
					closedir($dir);
					$this->plugin_dbsync_next_do();
				}
			}
			closedir($dir);
			echo " ( Done ".$counter." Files !)<hr />";
			echo "</div>";

			@unlink ($work);
		}
		$this->root->post['attach'] = "";
	}

	function plugin_dbsync_next_do()
	{
	//	global $script,$post,$_links_messages;

		//$token = $this->func->get_token_html();
		$token = '';
		$script = $this->func->get_script_uri();
		$html = <<<__EOD__
<form method="POST" action="{$script}" onsubmit="return pukiwiki_check(this);">
 <div>
  {$token}
  <input type="hidden" name="encode_hint" value="ぷ" />
  <input type="hidden" name="plugin" value="dbsync" />
  <input type="hidden" name="pmode" value="update" />
  <input type="hidden" name="init" value="{$this->root->post['init']}" />
  <input type="hidden" name="reading" value="{$this->root->post['reading']}" />
  <input type="hidden" name="title" value="{$this->root->post['title']}" />
  <input type="hidden" name="plain" value="{$this->root->post['plain']}" />
  <input type="hidden" name="plain_all" value="{$this->root->post['plain_all']}" />
  <input type="hidden" name="attach" value="{$this->root->post['attach']}" />
  <input type="hidden" name="p_info" value="{$this->root->post['p_info']}" />
  <input type="hidden" name="plain_bg" value="{$this->root->post['plain_bg']}" />
  <input type="hidden" name="timelimit" value="{$this->conf['timelimit']}" />
  <input type="submit" value="{$this->msg['btn_next_do']}" onClick="parent.xpwiki_dbsync_blink('start');return true;" />
 </div>
</form>
<script>
<!--
parent.xpwiki_dbsync_blink('continue');
-->
</script>
</body></html>
__EOD__;
		echo $html;

		exit();
	}

	function check_time_limit() {
		return ($this->conf['start_time'] + $this->conf['timelimit'] < time());
	}
}
?>