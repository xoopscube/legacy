<?php
/**
 * $Id: deldel.inc.php,v 1.15 2011/11/26 12:03:10 nao-pon Exp $
 * ORG: deldel.inc.php 161 2005-06-28 12:58:13Z okkez $
 *
 * 色んなものを一括削除するプラグイン
 * @version 1.60
 * @lisence PukiWiki本体と同じGPL2
 *
	 */
//require_once(PLUGIN_DIR.'backup.inc.php');

/**
 * プラグインのメッセージを初期化する
 * @todo いずれPlus!版と統合。
	 */

/**
 * plugin_deldel_action
 * 色んなものを一括削除する
 *
 * @access	  private
 * @param	  String	NULL		ページ名
 *
 * @return	  Array		ページタイトルと内容。
	 */
/**
 * page_list2
 * ページ一覧の作成 page_list()の一部を変更
 *
 * @access public
 * @param  Array   $pages		 ページ名配列
 * @param  String  $cmd			 コマンド
 * @param  Boolean $withfilename ファイルネームを返す(true)返さない(false)
 *
 * @return String				 整形済みのページリスト
	 */

/**
 * make_body
 * DATA_DIR,BACKUP_DIR,DIFF_DIR,COUNTER_DIRの一覧を作る。
 *
 * @param  String  $cmd コマンド
 * @param  String  $dir DATA_DIR or BACKUP_DIR のどちらか一方。省略不可
 * @param  Boolean $retry リトライかどうか。TRUE:リトライ,FALSE:リトライではない。
 * @return String		一覧表示のbody部分を返す。
	 */

/**
 * make_confirm
 * 確認画面を作る
 * globalで変数を引き回すのはあまりよくない気がしたので引数で渡してみた
 *
 * @access public
 * @param  String  $cmd	   コマンド [deldel|freeze2|unfreeze2]
 * @param  String  $dir	   $vars['dir']を使う
 * @param  String  $pages  $vars['pages']を使う
 *
 * @return Array   ページタイトルと内容
	 */

/**
 * AttachFile2
 * AttachFileを継承したクラス
 * toStringメソッドをcheckboxと凍結フラグを表示するように変更
 * &amp;の位置を変更している
	 */
/**
 * AttachFiles2
 * AttachFilesを継承したクラス
 * AttachFile2を使うようにだけ修正
	 */
/**
 * AttachPages2
 * AttachPagesを継承したクラス
 * コンストラクタをちょこっと変更
	 */
/**
 * attach_list2
 * 添付ファイルの一覧取得 attach_list()をちょこっと改変
 *
 * @access private
 * @param  Void	   引数はなし
 *
 * @return Array   PukiWikiのプラグイン仕様に従ったもの
 *
 */

/**
 * get_filename2
 * Get physical file name of the page
 *
 * @param  String $dir	  ディレクトリ名 counter or diff
 * @param  String $page	  ページ名
 * @return String		  物理ファイル名
 */

/**
 * sweap_cache();
 * キャッシュのお掃除。元ファイルの存在しないキャッシュを問答無用で削除する。
 * @return Array 削除したファイル名=>削除したファイル名をデコードしたもの
 */

require_once(dirname(__FILE__).'/attach.inc.php');

class xpwiki_plugin_deldel extends xpwiki_plugin {

	function plugin_deldel_action() {
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

		//変数の初期化
		$mode = isset($this->root->vars['mode']) ? $this->root->vars['mode'] : NULL;
		$status = array(0 => $this->msg['title_delete_error'],
					1 => $this->msg['btn_exec']);
		$is_cascade = (empty($this->root->vars['cascade'])) ? false : true;
		$move_to = (empty($this->root->vars['move'])) ? '' : $this->root->vars['movedir'];
		$this->root->vars['s_regxp'] = (empty($this->root->vars['regexp']))? "" : htmlspecialchars($this->root->vars['regexp']);
		$body = '';
		$moved = false;
		$script = $this->func->get_script_uri();

		if(!isset($mode) || !$this->root->userinfo['admin'])
		{
			if (!$this->root->userinfo['admin'])
			{
				$body .= "<p style=\"color:red;font-size:120%;font-weight:bold;\"><img src=\"image/alert.gif\" width=\"15\" height=\"15\" alt=\"alert\" /> ".$this->msg['msg_auth_error']."</p>";
			}

			//最初のページ
			$body .= "<h2>".$this->msg['msg_selectlist']."</h2>";
			$body .= "<form method='post' action=\"{$script}?cmd=deldel\">";
			$body .= "<input type=\"hidden\" name=\"dir\" value=\"DATA\"/>\n";
			$body .= "<input type=\"hidden\" name=\"mode\" value=\"select\"/>\n";
			$body .= "{$this->msg['msg_regexp_label']}<input type='text' name='regexp' value='{$this->root->vars['s_regxp']}' />\n";
			$body .= "<input type=\"submit\" value=\"{$this->msg['btn_search']}\" />";
			$body .= "<p>{$this->msg['msg_body_start']}</p>";
			$body .= "</form>";

			$body .= "<h2>".$this->msg['msg_dirlist']."</h2>";
			$body .= "<form method='post' action=\"{$script}?cmd=deldel\">";
			$body .= '<select name="dir" size="1">';
			$body .= '<option value="DATA">wiki</option>';
			$body .= '<option value="BACKUP">backup</option>';
			$body .= '<option value="UPLOAD">attach</option>';
			$body .= '<option value="DIFF">diff</option>';
			//$body .= '<option value="CACHE">cache</option>';
			$body .= '<option value="REFERER">referer</option>';
			$body .= '<option value="COUNTER">counter</option></select>';
			$body .= "<input type=\"hidden\" name=\"mode\" value=\"select\"/>\n";
			$body .= "<input type=\"submit\" value=\"{$this->msg['btn_search']}\" />";
			$body .= '<input type="checkbox" name="no_page" value="1"> List of no page';
			$body .= "<p>{$this->msg['msg_body_start']}</p>";
			$body .= "</form>";

			return array('msg'=>$this->msg['title_deldel'],'body'=>$body);
		}elseif(isset($mode) && $mode === 'select'){
			if($this->root->userinfo['admin']) {
				//認証が通ったらそれぞれページ名やファイル名の一覧を表示する
				$this->root->vars['pass'] = '';//認証が終わったのでパスを消去
				if(array_key_exists('regexp',$this->root->vars) && $this->root->vars['regexp'] != ''){
					//$pattern = $vars['regexp'];
					$pattern = "#".str_replace(array('#','"'),array('\#','\"'),$this->root->vars['regexp'])."#i";
					foreach ( $this->func->get_existpages(true) as $file => $page ) {
						//if (mb_eregi($pattern, $page)) {
						if (preg_match($pattern, $page)) {
							$target[$file] = $page;
						}
					}
					if(is_null($target)){
						$error_msg = "<p>{$this->msg['msg_regexp_error']}</p>\n";
						$error_msg .= "<p>". htmlspecialchars($this->root->vars['regexp']) ."</p>";
						$error_msg .= $this->make_body($this->root->vars['cmd'], $this->cont['DATA_DIR'], true);
						$error_msg .= "<p><a href=\"{$this->root->script}?cmd=deldel\">".$this->msg['msg_back_word']."</a></p>";
						return array('msg'=>$this->msg['title_delete_error'] ,'body'=>$error_msg);
					}
					$body .= $this->make_body($this->root->vars['cmd'], $this->cont['DATA_DIR'], false ,$target);
					return array('msg'=>$this->msg['title_list'],'body'=>$body);
				}elseif(isset($this->root->vars['dir']) && $this->root->vars['dir']==="DATA") {
					//ページ
					$body .= $this->make_body($this->root->vars['cmd'], $this->cont['DATA_DIR']);
					return array('msg'=>$this->msg['title_list'],'body'=>$body);
				}elseif(isset($this->root->vars['dir']) && $this->root->vars['dir']==="BACKUP"){
					//バックアップ
					$body .= $this->make_body($this->root->vars['cmd'], $this->cont['BACKUP_DIR']);
					return array('msg'=>$this->msg['title_backuplist'],'body'=>$body);
				}elseif(isset($this->root->vars['dir']) && $this->root->vars['dir']==="UPLOAD"){
					//添付ファイル
					$body .= "\n<form method=\"post\" action=\"{$script}?cmd=deldel\"><div>";
					$retval = $this->attach_list2();
					$body .= $retval['body'];
					$body .= "<input type=\"hidden\" name=\"mode\" value=\"confirm\"/>\n<input type=\"hidden\" name=\"dir\" value=\"{$this->root->vars['dir']}\"/>\n";
					$body .= "<input type=\"submit\" value=\"{$this->msg['btn_concern']}\"/></div>\n</form>";
					$body .= $this->msg['msg_check'];
					return array('msg'=>$this->msg['title_attachlist'],'body'=>$body);
				}elseif(isset($this->root->vars['dir']) && $this->root->vars['dir']==="DIFF") {
					//diff
					$body .= $this->make_body($this->root->vars['cmd'], $this->cont['DIFF_DIR']);
					return array('msg'=>$this->msg['title_difflist'], 'body'=>$body);
				}elseif(isset($this->root->vars['dir']) && $this->root->vars['dir']==="CACHE") {
					//cache
					$body .= "<ul>\n<li>rel\n<ul>";
					$deleted_caches = $this->sweap_cache();
					foreach($deleted_caches['rel'] as $key => $value) {
						$body .= '<li>'. $value. '<ul><li>'. $key. '</li></ul></li>'."\n";
					}
					$body .= "</ul></li></ul>\n";
					$body .= "<ul><li>ref\n<ul>";
					foreach($deleted_caches['ref'] as $key => $value) {
						$body .= '<li>'. $value. '<ul><li>'. $key. '</li></ul></li>'."\n";
					}
					$body .= '</ul></li></ul>';
					$body .= '<p>'. $this->msg['msg_delete_success']. '</p>';
					return array('msg'=>$this->msg['title_cachelist'], 'body'=>$body);
				}elseif(isset($this->root->vars['dir']) && $this->root->vars['dir']==="REFERER") {
					//リンク元referer
					$body .= $this->make_body($this->root->vars['cmd'], $this->cont['TRACKBACK_DIR']);
					return array('msg'=>$this->msg['title_refererlist'], 'body'=>$body);
				}elseif(isset($this->root->vars['dir']) && $this->root->vars['dir']==="COUNTER") {
					//カウンター*.count
					$body .= $this->make_body($this->root->vars['cmd'], $this->cont['COUNTER_DIR']);
					return array('msg'=>$this->msg['title_counterlist'], 'body'=>$body);
				}
			}elseif(isset($this->root->vars['pass']) && !$this->func->pkwk_login($this->root->vars['pass'])){
				//認証エラー
				return array('msg' => $this->msg['title_delete_error'],'body'=>$this->msg['msg_auth_error']);
			}
		}elseif(isset($mode) && $mode === 'confirm'){
			//確認画面+もう一回認証要求？
			if(array_key_exists('pages',$this->root->vars) and $this->root->vars['pages'] != ''){
				return $this->make_confirm('deldel', $this->root->vars['dir'], $this->root->vars['pages'], $is_cascade, $move_to);
			}else{
				//選択がなければエラーメッセージを表示する
				$error_msg = "<p>{$this->msg['msg_error']}</p><p><a href=\"{$this->root->script}?cmd=deldel\">".$this->msg['msg_back_word']."</a></p>";
				return array('msg'=>$this->msg['title_delete_error'] ,'body'=>$error_msg);
			}
		}elseif(isset($mode) && $mode === 'exec'){
			//削除
			if($this->root->userinfo['admin']) {
				$execution_time = intval(@ ini_get('max_execution_time'));
				switch($this->root->vars['dir']){
				  case 'DATA':
					// メール通知停止
					$this->root->notify = 0;
					$this->root->rtf['no_system_notification'] = TRUE;

					$mes = 'page';
					foreach($this->root->vars['pages'] as $page)
					{
						@set_time_limit($execution_time);

						$s_page = htmlspecialchars($page, ENT_QUOTES);
						if (!empty($this->root->vars['move_to'])) {
							static $to_obj;
							if (!$to_obj) {
								$to_obj = XpWiki::getInitedSingleton($this->root->vars['move_to']);
							}
							if (!$this->move_to($page, $to_obj)) {
								$flag[$s_page.'(Move)'] = false;
								continue;
							}
							$moved = true;
						}

						$pgid = $this->func->get_pgid_by_name($page);
						$target = $this->func->encode($page);

						if(is_file($this->func->get_filename($page)) && !$this->func->is_freeze($page)){
							$flag[$s_page] = true;
							$this->func->page_write($page, '');
						}else{
							$flag[$s_page] = false;
						}
						if ($this->root->vars['cascade'] == 1) {
							// BACKUP
							$f_page = $this->get_filename2('backup',$page);
							if(is_file($f_page)){
								$flag[$s_page] = unlink($f_page);
							}
							// DIFF
							$f_page = $this->get_filename2('diff',$page);
							if(is_file($f_page)){
								$flag[$s_page] = unlink($f_page);
							}
							// COUNTER
							$f_page = $this->get_filename2('counter',$page);
							if(is_file($f_page)){
								$flag[$s_page] = unlink($f_page);
							}
							// REFERER
							$f_page = $this->get_filename2('referer',$page);
							if(is_file($f_page)){
								$flag[$s_page] = unlink($f_page);
							}
							// CACHE
							//sweap_cache();

							// 添付ファイルDB
							$del_files = $this->func->attach_db_write(array('pgid'=>$pgid),'delete');

							$att = $thm = array();

							if (is_array($del_files) && $del_files)
							{
								foreach($del_files as $del_file)
								{
									$name = $target."_".$this->func->encode($del_file);
									// 添付ファイル
									if (is_file($this->cont['UPLOAD_DIR'].$name))
									{
										unlink($this->cont['UPLOAD_DIR'].$name);
										$att[] = $del_file." [$name]";
									}
									//サムネイル
									for ($i = 1; $i < 100; $i++)
									{
										$file = $target.'_'.$this->func->encode($i."%").$this->func->encode($del_file);
										if (is_file($this->cont['UPLOAD_DIR']."s/".$file))
										{
											unlink($this->cont['UPLOAD_DIR']."s/".$file);
											$thm[] = $i.'%'.$del_file." [$name]";
										}
									}
								}
							}
						}
					}
					break;
				  case 'BACKUP':
					$mes = 'backup';
					foreach($this->root->vars['pages'] as $page){
						@set_time_limit($execution_time);
						$s_page = htmlspecialchars($page, ENT_QUOTES);
						$f_page = $this->get_filename2($mes,$page);
						if(is_file($f_page) && !$this->func->is_freeze($page)){
							$flag[$s_page] = unlink($f_page);
						}else{
							$flag[$s_page] = false;
						}
					}
					break;
				  case 'UPLOAD':
					$attach = $this->func->get_plugin_instance('attach');
					$mes = 'attach';
					$size = count($this->root->vars['file_a']);
					for($i=0;$i<$size;$i++){
						@set_time_limit($execution_time);
						foreach (array('refer', 'file', 'age') as $var) {
							$this->root->vars[$var] = isset($this->root->vars[$var.'_a'][$i]) ? $this->root->vars[$var.'_a'][$i] : '';
						}
						$result = $attach->attach_delete();
						//それぞれのファイルについて成功|失敗のフラグを立てる
						switch($result['msg']){
						  case $this->root->_attach_messages['msg_deleted']:
							$flag["{$this->root->vars['refer']}/{$this->root->vars['file']}"] = true;
							break;
						  case $this->root->_attach_messages['err_notfound'] || $this->root->_attach_messages['err_noparm']:
							$flag["{$this->root->vars['refer']}/{$this->root->vars['file']}"] = false;
							break;
						  default:
							$flag["{$this->root->vars['refer']}/{$this->root->vars['file']}"] = false;
							break;
						}
					}
					break;
				  case 'DIFF' :
					$mes = 'diff';
					foreach($this->root->vars['pages'] as $page){
						@set_time_limit($execution_time);
						$s_page = htmlspecialchars($page, ENT_QUOTES);
						$f_page = $this->get_filename2($mes,$page);
						if(is_file($f_page) && !$this->func->is_freeze($spage)){
							$flag[$s_page] = unlink($f_page);
						}else{
							$flag[$s_page] = false;
						}
					}
					break;
				  case 'REFERER':
					$mes = 'referer';
					foreach($this->root->vars['pages'] as $page){
						@set_time_limit($execution_time);
						$s_page = htmlspecialchars($page, ENT_QUOTES);
						$f_page = $this->get_filename2($mes,$page);
						if(is_file($f_page) && !$this->func->is_freeze($spage)){
							$flag[$s_page] = unlink($f_page);
						}else{
							$flag[$s_page] = false;
						}
					}
					break;
				  case 'COUNTER':
					$mes = 'counter';
					foreach($this->root->vars['pages'] as $page){
						@set_time_limit($execution_time);
						$s_page = htmlspecialchars($page, ENT_QUOTES);
						$f_page = $this->get_filename2($mes,$page);
						if(is_file($f_page)){
							$flag[$s_page] = unlink($f_page);
						}else{
							$flag[$s_page] = false;
						}
						//カウンターDB
						$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_count")." WHERE `pgid` = '".$this->func->get_pgid_by_name($page)."' LIMIT 1;";
						$result=$this->xpwiki->db->queryF($query);
					}
					break;
				}
				if(in_array(false,$flag)){
					//削除失敗したものが一つでもある
					foreach($flag as $key=>$value)
					{
						$body .= "$key =&gt; {$status[(($value)? 1 : 0)]}<br/>\n";
					}
					$body .= "<p>{$this->msg['msg_delete_error']}</p>";
					if ($moved) {
						$body .= "<p><a href=\"{$to_obj->root->script}?cmd=dbsync\">".$this->msg['msg_back_dbsync']."</a></p>";
					} else {
						$body .= "<p><a href=\"{$this->root->script}?cmd=deldel\">".$this->msg['msg_back_word']."</a></p>";
					}
					return array('msg' => $this->msg['title_delete_error'],'body'=>$body);
				}else{
					//削除成功
					foreach($flag as $key=>$value){
						$body .= "$key<br/>\n";
					}
					$body .= "<p>{$this->msg['msg_delete_success']}</p>";
					$body .= $is_cascade ? "<p>{$this->msg['msg_together']}</p>" : "";
					if ($moved) {
						$body .= "<p><a href=\"{$to_obj->root->script}?cmd=dbsync\">".$this->msg['msg_back_dbsync']."</a></p>";
					} else {
						$body .= "<p><a href=\"{$this->root->script}?cmd=deldel\">".$this->msg['msg_back_word']."</a></p>";
					}
					return array('msg' => $this->msg['title_delete_'.$mes] ,'body' => $body);
				}
			}
			elseif(isset($this->root->vars['pass']) && !$this->func->pkwk_login($this->root->vars['pass'])){
				//認証エラー
				return array('msg' => $this->msg['title_delete_error'],'body'=>$this->msg['msg_auth_error']);
			}
		}
	}
	function page_list2($pages, $cmd = 'read', $withfilename = FALSE, $checked=FALSE)
	{
		// ソートキーを決定する。 ' ' < '[a-zA-Z]' < 'zz'という前提。
		$symbol = ' ';
		$other = 'zz';

		if($this->root->pagereading_enable) {
			mb_regex_encoding($this->cont['SOURCE_ENCODING']);
			list($readings, $titles) = $this->func->get_readings($pages);
		}
		//echo "Pages: ".count($pages);

		$list = $matches = array();
		foreach($pages as $file=>$page) {
		//foreach($pages as $page) {
			$r_page	 = rawurlencode($page);
			$s_page	 = htmlspecialchars($page, ENT_QUOTES);
			$passage = $this->func->get_pg_passage($page);
			// 変更ココから by okkez
			$checked = ($checked || !empty($this->root->post['no_page']))? " checked=\"true\"" : "";
			$freezed = $this->func->is_freeze($page) ? '<span class="new1"> * </span>' : '';
			$exist_page = $this->func->is_page($page) ? '' : '<span class="diff_added"> # </span>';
			if (!$exist_page && !empty($this->root->post['no_page'])) continue;
			$str = '   <li><input type="checkbox" name="pages[]" value="' . $s_page . '"'.$checked.' /><a href="' .
		$this->root->script . '?cmd=' . $cmd . '&amp;page=' . $r_page .
		'">' . $s_page . '</a>' . $passage . $freezed . $exist_page;
			// ココまで

			if ($withfilename) {
				$s_file = htmlspecialchars($file);
				$str .= "\n" . '	<ul><li>' . $s_file . '</li></ul>' .
			"\n" . '   ';
			}
			$str .= '</li>';

			// WARNING: Japanese code hard-wired
			if($this->root->pagereading_enable) {
				if(mb_ereg('^([A-Za-z])', mb_convert_kana($page, 'a'), $matches)) {
					$head = $matches[1];
				} elseif(mb_ereg('^([ァ-ヶ])', $readings[$page], $matches)) { // here
					$head = $matches[1];
				} elseif (mb_ereg('^[ -~]|[^ぁ-ん亜-熙]', $page)) { // and here
					$head = $symbol;
				} else {
					$head = $other;
				}
			} else {
				$head = (preg_match('/^([A-Za-z])/', $page, $matches)) ? $matches[1] :
				(preg_match('/^([ -~])/', $page, $matches) ? $symbol : $other);
			}

			$list[$head][$page] = $str;
		}
		ksort($list);

		$cnt = 0;
		$arr_index = array();
		$retval = '<ul>' . "\n";
		foreach ($list as $head=>$pages) {
			if ($head === $symbol) {
				$head = $this->root->_msg_symbol;
			} else if ($head === $other) {
				$head = $this->root->_msg_other;
			}

			if ($this->root->list_index) {
				++$cnt;
				$arr_index[] = '<a id="top_' . $cnt .
			'" href="#head_' . $cnt . '"><strong>' .
			$head . '</strong></a>';
				$retval .= ' <li><a id="head_' . $cnt . '" href="#top_' . $cnt .
			'"><strong>' . $head . '</strong></a>' . "\n" .
			'  <ul>' . "\n";
			}
			ksort($pages);
			$retval .= join("\n", $pages);
			if ($this->root->list_index)
			$retval .= "\n	</ul>\n </li>\n";
		}
		$retval .= '</ul>' . "\n";
		if ($this->root->list_index && $cnt > 0) {
			$top = array();
			while (! empty($arr_index))
			$top[] = join(' | ' . "\n", array_splice($arr_index, 0, 16)) . "\n";

			$retval = '<div id="top" style="text-align:center">' . "\n" .
		join('<br />', $top) . '</div>' . "\n" . $retval;
		}
		return $retval;
	}
	function make_body($cmd, $dir, $retry=false, $pages=array())
	{
		$select = '';
		if($dir === $this->cont['DATA_DIR']) {
			$ext = '.txt';

			$_dir = dirname($this->cont['DATA_HOME']);
			$items = array();
			if ($dh = opendir($_dir)) {
				while (($item = readdir($dh)) !== false) {
					if (is_dir($_dir.'/'.$item)) {
						if ($this->root->mydirname !== $item && is_file($_dir.'/'.$item.'/private/ini/pukiwiki.ini.php')) {
							$obj =& XpWiki::getInitedSingleton($item);
							if ($obj->root->module['mid']) {
								$items[] = $item;
							}
						}
					}
				}
				closedir($dh);
			}
			if ($items) {
				$select = '<div><input type="checkbox" name="move" value="1" /> ';
				$select .= $this->msg['msg_move_flag'];
				$select .= '<select name="movedir">';
				foreach($items as $_dir) {
					$select .= '<option value="'.$_dir.'">'.$_dir.'</option>';
				}
				$select .= '</select></div>';
			}
		}elseif($dir === $this->cont['BACKUP_DIR']) {
			$ext = (function_exists('gzfile'))? ".gz" : ".txt";
		}elseif($dir === $this->cont['DIFF_DIR']) {
			$ext = '.txt';
		}elseif($dir === $this->cont['TRACKBACK_DIR']) {
			$ext = '.ref';
		}elseif($dir === $this->cont['COUNTER_DIR']) {
			$ext = '.count';
		}
		$script = $this->func->get_script_uri();
		$body = '';
		$body .= "<form method='post' action=\"{$script}?cmd=$cmd\"><div>\n";
		if ($dir === $this->cont['DATA_DIR'])
		{
			$body .= "<input type=\"hidden\" name=\"dir\" value=\"DATA\"/>\n";
			$body .= "<input type=\"hidden\" name=\"mode\" value=\"select\"/>\n";
			$body .= "{$this->msg['msg_regexp_label']}<input type='text' name='regexp' value='{$this->root->vars['s_regxp']}' />\n";
			$body .= "<input type=\"submit\" value=\"{$this->msg['btn_research']}\" /></div></form>\n";
		}
		if ($retry === false) {
			$body .= "<form method='post' action=\"{$script}?cmd=$cmd\"><div>\n";
			if ($pages)
			{
				$body .= $this->page_list2($pages, 'read', FALSE, TRUE);
			}
			else if ($dir === $this->cont['DATA_DIR'])
			{
				$body .= '<p>'.$this->msg['msg_is_freezed'].'</p>';
				$body .= $this->page_list2($this->func->get_existpages(true));
			}
			/*
			else if ($dir === $this->cont['TRACKBACK_DIR'])
			{
				$dp = @opendir($dir)
					or $this->func->die_message($dir. ' is not found or not readable.');
				while ($file = readdir($dp))
				{
					$matches = array();
					if (preg_match("/^([\d]+)\.ref$/",$file,$matches))
					{
						$aryret[$file] = $this->func->get_pgname_by_id($matches[1]);
					}
				}
				closedir($dp);
				$body .= $this->page_list2($aryret);
			}
			*/
			else
			{
				$body .= '<p>'.$this->msg['msg_is_freezed'].'</p>';
				$body .= '<p>'.$this->msg['msg_is_deleted'].'</p>';
				$body .= $this->page_list2($this->func->get_existpages($dir, $ext, true));
			}
			if($dir === $this->cont['DATA_DIR']) {
				$dir = 'DATA';
				$body .= ($cmd === 'deldel') ? "<input type=\"checkbox\" name=\"cascade\" value=\"1\" checked=\"checked\"/> <span>{$this->msg['msg_together_flag']}</span><br />\n" : "";
				$body .= $select;
			}elseif($dir === $this->cont['BACKUP_DIR']) {
				$dir = 'BACKUP';
			}elseif($dir === $this->cont['DIFF_DIR']) {
				$dir = 'DIFF';
			}elseif($dir === $this->cont['TRACKBACK_DIR']) {
				$dir = 'REFERER';
			}elseif($dir === $this->cont['COUNTER_DIR']) {
				$dir = 'COUNTER';
			}
			$body .= "<input type=\"hidden\" name=\"mode\" value=\"confirm\"/>\n";
			$body .= "<input type=\"hidden\" name=\"dir\" value=\"{$dir}\"/>\n";
			$body .= "<input type=\"submit\" value=\"{$this->msg['btn_concern']}\" /></div></form>\n";
			$body .= $this->msg['msg_check'];
		}

		return $body;
	}
	function make_confirm($cmd, $dir, $pages, $is_cascade=false, $move_to = '')
	{
		$is_cascade = ($is_cascade)? "1" : "0";

		$i=0;
		$script = $this->func->get_script_uri();
		$body = '';
		$body .= "<form method=\"post\" action=\"{$script}?cmd=$cmd\">\n<ul>\n";
		switch($dir){
		  case 'DATA' :
		  case 'BACKUP' :
		  case 'DIFF' :
		  case 'REFERER' :
		  case 'COUNTER':
			foreach($pages as $page){
				$s_page = htmlspecialchars($page,ENT_QUOTES);
				$body .= "<li><input type=\"hidden\" name=\"pages[$i]\" value=\"$s_page\"/>$s_page<br/></li>\n";
				$i++;
			}
			break;
		  case 'UPLOAD' :
			foreach($pages as $page){
				$s_page = htmlspecialchars($page,ENT_QUOTES);
				$temp = split("=|&amp;",$s_page);
				$file = rawurldecode($temp[1]);
				$refer = rawurldecode($temp[3]);
				$age = isset($temp[5])? rawurldecode($temp[5]) : 0 ;
				$body .= "<li><input type=\"hidden\" name=\"pages[$i]\" value=\"$s_page\"/>$refer/$file";
				$body .= "<input type=\"hidden\" name=\"refer_a[$i]\" value=\"$refer\"/>";
				$body .= "<input type=\"hidden\" name=\"file_a[$i]\" value=\"$file\"/>";
				$body .= "<input type=\"hidden\" name=\"age_a[$i]\" value=\"$age\"/></li>\n";
				$i++;
			}
			break;
		  default :
			return array('msg' => $this->msg['title_delete_error'],'body'=>$this->msg['msg_fatal_error']);
		}
		$body .= "</ul>\n<div>";
		$body .= '<input type="hidden" name="mode" value="exec"/><input type="hidden" name="dir" value="'.$dir.'"/>'."\n";
		//$body .= '<input type="hidden" name="cascade" value="'.$is_cascade.'" />'."\n";
		if ($dir === 'DATA') {
			$cascade_disabled = '';
			if ($move_to) {
				$is_cascade = '1';
				$cascade_disabled = ' disabled="disabled"';
			}
			$body .= "<input type=\"checkbox\" name=\"cascade\" value=\"1\"".(($is_cascade)? " checked=\"true\"" : "").$cascade_disabled." /> <span>{$this->msg['msg_together_flag']}</span><br />\n";
			$body .= ($move_to)? '<div>' . $this->msg['msg_move_flag'] . htmlspecialchars($move_to).'<input type="hidden" name="move_to" value="'.htmlspecialchars($move_to).'" /></div>' : '';
		}
		$body .= "<input type=\"submit\" value=\"{$this->msg['btn_exec']}\"/>\n</div></form>\n";
		$body .= "<p>{$this->msg['msg_auth']}</p>";
		//$body .= $is_cascade ? "<p>{$_deldel_messages['msg_together_confirm']}</p>" : "";
		return array('msg'=>$this->msg['title_select_list'],'body'=>$body);
	}
	function attach_list2()
	{
	//	global $vars, $_attach_messages;

		$refer = isset($this->root->vars['refer']) ? $this->root->vars['refer'] : '';

		$obj = & new XpWikiAttachPages2($this->xpwiki, $refer);

		$msg = $this->root->_attach_messages[($refer === '') ? 'msg_listall' : 'msg_listpage'];
		$body = ($refer === '' || isset($obj->pages[$refer])) ?
		$obj->toString($refer, FALSE) :
		$this->root->_attach_messages['err_noexist'];

		return array('msg'=>$msg, 'body'=>$body);
	}
	function get_filename2($dir, $page, $obj=false)
	{
		if (!$obj) {
			$obj =& $this;
		}
		$pgid = $obj->func->get_pgid_by_name($page);
		$page = $obj->func->encode($page);
		switch($dir){
		  case 'wiki' :
			return $obj->cont['DATA_DIR'] . $page . '.txt' ;
		  case 'backup' :
			return $obj->cont['BACKUP_DIR'] . $page . ((function_exists('gzfile'))? ".gz" : ".txt") ;
		  case 'counter' :
			return $obj->cont['COUNTER_DIR'] . $page . '.count' ;
		  case 'diff' :
			return $obj->cont['DIFF_DIR'] . $page . '.txt' ;
		  case 'referer' :
			return $obj->cont['TRACKBACK_DIR'] . $page . '.ref';
		}
	}
	function sweap_cache()
	{
		$rel = $this->func->get_existpages($this->cont['CACHE_DIR'], '.rel');
		foreach($rel as $key => $value){
			if ($this->func->is_page($value)){
				continue;
			}else{
				unlink($this->cont['CACHE_DIR'].$key);
				$delete_rel[$key] = $value;
			}
		}
		$ref = $this->func->get_existpages($this->cont['CACHE_DIR'], '.ref');
		foreach($ref as $key => $value){
			if ($this->func->is_page($value)){
				continue;
			}else{
				unlink($this->cont['CACHE_DIR'].$key);
				$delete_ref[$key] = $value;
			}
		}
		natcasesort($delete_rel);
		natcasesort($delete_ref);
		return array('rel' => $delete_rel,
				 'ref' => $delete_ref);
	}

	function move_to ($page, & $to_obj) {
		static $counter = null;

		if ($to_obj->func->is_page($page)) {
			return false;
		}
		$move_files = array();
//		foreach(array('wiki', 'backup', 'counter', 'diff', 'referer') as $dir) {
		foreach(array('wiki', 'backup', 'diff', 'referer') as $dir) {
			$from = $this->get_filename2($dir, $page);
			$to = $this->get_filename2($dir, $page, $to_obj);
			if (is_file($from)) {
				$move_files[$from] = $to;
			}
		}

		$pgid = $this->func->get_pgid_by_name($page);

		// Add
		$from = $this->cont['DIFF_DIR'] . $pgid . '.add' ;
		if (is_file($from)) {
			$toid = $to_obj->func->get_pgid_by_name($page, true, true);
			$to = $to_obj->cont['DIFF_DIR'] . $toid . '.add' ;
			$move_files[$from] = $to;
		}

		// CSS
		$from = $this->cont['CACHE_DIR'] . $pgid . '.css' ;
		if (is_file($from)) {
			$toid = $to_obj->func->get_pgid_by_name($page, true, true);
			$to = $to_obj->cont['CACHE_DIR'] . $toid . '.css' ;
			$move_files[$from] = $to;
		}

		// Attach
		$query = "SELECT name,age FROM `".$this->xpwiki->db->prefix($this->root->mydirname."_attach")."` WHERE `pgid` = {$pgid}";
		$result = $this->xpwiki->db->query($query);
		$_done = array();
		while($_row = $this->xpwiki->db->fetchRow($result))
		{
			$basename = $this->func->encode($page).'_'.$this->func->encode($_row[0]);
			$filename = $basename . ($_row[1] ? '.'.$_row[1] : '');
			$logname = $basename.'.log';

			$from = $this->cont['UPLOAD_DIR'].$filename;
			$to = $to_obj->cont['UPLOAD_DIR'].$filename;
			if (is_file($from)) {
				$move_files[$from] = $to;
			}

			if (empty($_done[$basename])) {
				$from = $this->cont['UPLOAD_DIR'].$logname;
				$to = $to_obj->cont['UPLOAD_DIR'].$logname;
				if (is_file($from)) {
					$move_files[$from] = $to;
				}
			}
			$_done[$basename] = true;
		}
		$ret = true;
		foreach($move_files as $from => $to) {
			$ret = ($ret && touch($to) && copy($from, $to));
			touch($to, filemtime($from));
		}

		// make .count
		if (!$counter) {
			$counter = $this->func->get_plugin_instance('counter');
		}
		$counters = $counter->plugin_counter_get_count($page, false);
		$to = $this->get_filename2('counter', $page, $to_obj);
		file_put_contents(join("\n", $counters) . "\n", $to);

		return $ret;
	}
}
class XpWikiAttachFile2 extends XpWikiAttachFile
{
	/**
	 * ページ名に対して色々なリンクを一つにまとめて返す。
	 *
	 * @param  hoge	 $showicon
	 * @param  hoge	 $showinfo
	 *
	 * @return String
	 */

	function toString($showicon, $showinfo)
	{
		$this->getstatus();
		$param	= 'file=' . rawurlencode($this->file) . '&amp;refer=' . rawurlencode($this->page) .
		($this->age ? '&amp;age=' . $this->age : '');
		$title = $this->time_str . ' ' . $this->size_str;
		$label = ($showicon ? $this->cont['PLUGIN_ATTACH_FILE_ICON'] : '') . htmlspecialchars($this->file);
		if ($this->age) {
			$label .= ' (backup No.' . $this->age . ')';
		}
		$info = $count = $retval = $freezed = '';
		if ($showinfo) {
			$_title = str_replace('$1', rawurlencode($this->file), $this->root->_attach_messages['msg_info']);
			$info = "\n<span class=\"small\">[<a href=\"{$this->root->script}?plugin=attach&amp;pcmd=info$param\" title=\"$_title\">{$this->root->_attach_messages['btn_info']}</a>]</span>\n";
			$count = ($showicon && ! empty($this->status['count'][$this->age])) ?
			sprintf($this->root->_attach_messages['msg_count'], $this->status['count'][$this->age]) : '';
		}
		$freezed = $this->status['freeze'] ? '<span class="new1"> * </span>' : '';
		$retval .= $this->root->vars['cmd'] === 'deldel' |
		$this->root->vars['cmd'] === 'freeze2' |
		$this->root->vars['cmd'] === 'unfreeze2' ?
		"<input type=\"checkbox\" name=\"pages[]\" value=\"$param\"/>" : '';
		$retval .= "<a href=\"{$this->root->script}?plugin=attach&amp;pcmd=open&amp;$param\" title=\"$title\">$label</a>$count$info$freezed";
		return $retval;
	}
}
class XpWikiAttachFiles2 extends XpWikiAttachFiles
{
	function add($file, $age)
	{
		$this->files[$file][$age] = & new XpWikiAttachFile2($this->xpwiki, $this->page, $file, $age);
	}

	function toString($flat)
	{
		global $_title_cannotread;

		if (! $this->func->check_readable($this->page, FALSE, FALSE)) {
			return str_replace('$1', make_pagelink($this->page), $_title_cannotread);
		} else if ($flat) {
			return $this->to_flat();
		}

		$ret = '';
		$files = array_keys($this->files);
		sort($files);

		foreach ($files as $file) {
			$_files = array();
			foreach (array_keys($this->files[$file]) as $age) {
				$_files[$age] = $this->files[$file][$age]->toString(FALSE, TRUE);
			}
			if (! isset($_files[0])) {
				$_files[0] = htmlspecialchars($file);
			}
			ksort($_files);
			$_file = $_files[0];
			unset($_files[0]);
			$ret .= " <li>$_file\n";
			if (count($_files)) {
				$ret .= "<ul>\n<li>" . join("</li>\n<li>", $_files) . "</li>\n</ul>\n";
			}
			$ret .= " </li>\n";
		}
		return $this->func->make_pagelink($this->page) . "\n<ul>\n$ret</ul>\n";
	}
}
class XpWikiAttachPages2 extends XpWikiAttachPages
{
	function XpWikiAttachPages2(& $xpwiki, $page = '', $age = NULL)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;


		$dir = opendir($this->cont['UPLOAD_DIR']) or
		die('directory ' . $this->cont['UPLOAD_DIR'] . ' is not exist or not readable.');

		$page_pattern = ($page === '') ? '(?:[0-9A-F]{2})+' : preg_quote($this->func->encode($page), '/');
		$age_pattern = ($age === NULL) ?
		'(?:\.([0-9]+))?' : ($age ?	 "\.($age)" : '');
		$pattern = "/^({$page_pattern})_((?:[0-9A-F]{2})+){$age_pattern}$/";

		$matches = array();
		while ($file = readdir($dir)) {
			if (! preg_match($pattern, $file, $matches))
			continue;

			$_page = $this->func->decode($matches[1]);
			$_file = $this->func->decode($matches[2]);
			$_age  = isset($matches[3]) ? $matches[3] : 0;
			if (! isset($this->pages[$_page])) {
				$this->pages[$_page] = & new XpWikiAttachFiles2($this->xpwiki, $_page);
			}
			$this->pages[$_page]->add($_file, $_age);
		}
		closedir($dir);
	}

	function toString($page = '', $flat = FALSE)
	{
		if ($page !== '') {
			if (! isset($this->pages[$page])) {
				return '';
			} else {
				return $this->pages[$page]->toString($flat);
			}
		}
		$ret = '';

		$pages = array_keys($this->pages);
		sort($pages);

		foreach ($pages as $page) {
			if ($this->func->check_non_list($page)) continue;
			$ret .= '<li>' . $this->pages[$page]->toString($flat) . '</li>' . "\n";
		}
		return "\n" . '<ul>' . "\n" . $ret . '</ul>' . "\n";
	}
}
?>