<?php
/*
 * Created on 2008/03/24 by nao-pon http://hypweb.net/
 * $Id: attach.php,v 1.41 2012/01/30 12:04:06 nao-pon Exp $
 */

//-------- クラス
//ファイル
class XpWikiAttachFile
{
	var $page,$file,$age,$basename,$filename,$logname,$copyright;
	var $time = 0;
	var $size = 0;
	var $type = '';
	var $pgid = 0;
	var $time_str = '';
	var $size_str = '';
	var $owner_str = '';
	var $status = array(
			'count'    => array(0),
			'age'      => '',
			'pass'     => '',
			'freeze'   => FALSE,
			'copyright'=> FALSE,
			'owner'    => 0,
			'ucd'      => '',
			'uname'    => '',
			'md5'      => '',
			'admins'   => 0,
			'org_fname'=> '',
			'imagesize'=> NULL,
			'noinline' => 0,
			'mime'     => ''
		);
	var $action = 'update';
	var $dbinfo = array();

	function XpWikiAttachFile(& $xpwiki, $page, $file, $age=0, $pgid=0)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;

		$this->page = $page;
		$this->pgid = ($pgid)? $pgid : $this->func->get_pgid_by_name($page);
		$this->file = $this->func->basename($file);
		$this->age  = is_numeric($age) ? $age : 0;
		$this->id   = $this->get_id();

		$this->basename = $this->cont['UPLOAD_DIR'].$this->func->encode($page).'_'.$this->func->encode($this->file);
		$this->filename = $this->basename . ($age ? '.'.$age : '');
		$this->logname = $this->basename.'.log';

		if ($this->id) {
			$this->get_dbinfo();
			$this->exist = TRUE;
			$this->time = $this->dbinfo['mtime'];
		} else {
			$this->exist = is_file($this->filename);
			$this->time = $this->exist ? filemtime($this->filename) - $this->cont['LOCALZONE'] : 0;
		}
		$this->owner_id = 0;
	}

	// イメージサイズの取得
	function getimagesize() {
		$size = false;
		$mime = '';
		if ($this->type) {
			$mime = $this->type;
		} else {
			$mime = xpwiki_plugin_attach::attach_mime_content_type($this->filename, $this->status);
		}
		list($type) = explode('/', $mime);
		$type = strtolower($type);

		if ($type === 'video' || $mime === 'application/vnd.rn-realmedia') {
//			$extension = 'ffmpeg';
//			// load extension
//			$extension_soname = $extension . '.' . PHP_SHLIB_SUFFIX;
//			$extension_fullname = PHP_EXTENSION_DIR . '/' . $extension_soname;
//			if(! extension_loaded($extension) && ini_get('enable_dl') && ! ini_get('safe_mode') && function_exists('dl') && is_file($extension_fullname)) {
//			    dl($extension_soname);
//			}
//			if (extension_loaded($extension)) {
//				$movie = new ffmpeg_movie($this->filename);
//				$duration = $movie->getDuration();
//				$framerate = $movie->getFrameRate();
//				$h = $movie->getFrameHeight();
//				$w = $movie->getFrameWidth();
//				$movie = null;
//				unset($movie);
//				$size = array();
//				$size[0] = intval($w);
//				$size[1] = intval($h);
//				$size[2] = 0;
//				$size[3] = 'width="'.$size[0].'" height="'.$size[1].'"';
//				$size['bits'] = null;
//				$size['channels'] = null;
//				$size['mime'] = $mime;
//				$size['duration'] = $duration;
//				$size['framerate'] = $framerate;
//			} else if (HypCommonFunc::loadClass('getID3')) {
			if (HypCommonFunc::loadClass('getID3')) {
				$getID3 = new getID3;
				$info = $getID3->analyze($this->filename);
				if ($info && ! empty($info['video'])) {
					$size = array();
					$size[0] = intval($info['video']['resolution_x']);
					$size[1] = intval($info['video']['resolution_y']);
					$size[2] = 0;
					$size[3] = 'width="'.$size[0].'" height="'.$size[1].'"';
					$size['bits'] = null;
					$size['channels'] = null;
					$size['mime'] = $mime;
					$size['duration'] = $info['playtime_seconds'];
					$size['framerate'] = $info['video']['frame_rate'];
				}
				$getID3 = null;
				unset($getID3);
			}
		} else if ($type === 'image') {
			$size = @ getimagesize($this->filename);
			if (! $size) {
				$size = array();
				$width = $height = 0;
				if (substr($mime, 6,3) === 'svg') {
					$src = $this->func->file_get_contents($this->filename, false, null, 0, 4096);
					if (preg_match('#<svg([^>]+)>#i', $src, $match)) {
						if (preg_match('#width="([\d.]+)(?:px)?"#i', $match[1], $dig)) {
							$width = $dig[1];
						}
						if (preg_match('#height="([\d.]+)(?:px)?"#i', $match[1], $dig)) {
							$height = $dig[1];
						}
					}
				}
				$size[0] = $width;
				$size[1] = $height;
				$size[2] = 0;
				$size[3] = '';
				$size['bits'] = null;
				$size['channels'] = null;
				$size['mime'] = $mime;
			}
		}
		return $size;
	}

	// ファイル情報取得
	function getstatus()
	{
		if (! $this->exist && ! is_file($this->logname)) {
			return FALSE;
		} else {
			// ログファイル取得
			$data = array_pad(file($this->logname), count($this->status), '');
			foreach ($this->status as $key=>$value)
			{
				$this->status[$key] = chop(array_shift($data));
			}
			$this->status['count'] = explode(',',$this->status['count']);
			if (empty($this->status['org_fname'])) $this->status['org_fname'] = $this->file;
			if (is_null($this->status['imagesize']) || $this->status['imagesize'] === '') {
				$this->status['imagesize'] = $this->getimagesize($this->filename);
				$this->putstatus(FALSE);
			} else {
				$this->status['imagesize'] = unserialize($this->status['imagesize']);
			}
			if (! $this->status['mime']) {
				$this->status['mime'] = $this->type = isset($this->dbinfo['type'])? $this->dbinfo['type'] : xpwiki_plugin_attach::attach_mime_content_type($this->filename, $this->status);
			} elseif (! $this->type) {
				$this->type = $this->status['mime'];
			}
		}
		$this->time_str = $this->func->get_date('Y/m/d H:i:s',$this->time);
		$this->size = isset($this->dbinfo['size'])? $this->dbinfo['size'] : filesize($this->filename);
		$this->size_str = $this->func->bytes2KMT($this->size);
		$this->owner_id = intval($this->status['owner']);
		$user = $this->func->get_userinfo_by_id($this->status['owner']);
		$user = $user['uname_s'];
		if (!$this->status['owner']) {
			if ($this->status['uname']) {
				$user = htmlspecialchars($this->status['uname']);
			}
			$user = $user . " [".$this->status['ucd'] . "]";
		}
		$this->owner_str = $user;

		return TRUE;
	}
	//ステータス保存
	function putstatus($dbup = TRUE)
	{
		if ($dbup) $this->update_db();
		$status = $this->status;
		$status['count'] = join(',', $status['count']);
		$status['imagesize'] = serialize($status['imagesize']);
		$fp = fopen($this->logname,'wb')
			or $this->func->die_message('cannot write '.$this->logname);
		flock($fp,LOCK_EX);
		foreach ($status as $key=>$value)
		{
			fwrite($fp,$value."\n");
		}
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	// DB id 取得
	function get_id() {
		return $this->func->get_attachfile_id($this->page, $this->file, $this->age);
	}

	// Get attachDB info
	function get_dbinfo () {
		$this->dbinfo = $this->func->get_attachdbinfo($this->id);
	}

	// attach DB 更新
	function update_db()
	{
		if ($this->action == "insert")
		{
			if (! $this->size) $this->size = filesize($this->filename);
			if (! $this->type) $this->type = xpwiki_plugin_attach::attach_mime_content_type($this->filename, $this->status);
			if (! $this->time) $this->time = filemtime($this->filename) - $this->cont['LOCALZONE'];
		}
		$data['id']   = $this->id;
		$data['pgid'] = $this->pgid;
		$data['name'] = $this->file;
		$data['mtime'] = $this->time;
		$data['size'] = $this->size;
		$data['type'] = $this->type;
		$data['status'] = $this->status;

		$this->func->attach_db_write($data,$this->action);

	}
	// 日付の比較関数
	function datecomp($a,$b)
	{
		return ($a->time == $b->time) ? 0 : (($a->time > $b->time) ? -1 : 1);
	}
	function toString($showicon,$showinfo,$mode="")
	{
		$this->getstatus();
		$is_owner = $this->is_owner();
		$file_e = rawurlencode($this->file);
		$page_e = rawurlencode($this->page);
		$param = '&amp;refer='.$page_e
		       . ($this->age ? '&amp;age='.$this->age : '')
		       . '&amp;';
		$param2 = 'file='.$file_e;
		$title = $this->time_str.' '.$this->size_str;
		$label = ($showicon ? $this->cont['FILE_ICON'] : '').htmlspecialchars($this->status['org_fname']);
		if ($this->age) {
			if ($mode == "imglist"){
				$label = 'backup No.'.$this->age;
			} else {
				$label .= ' (backup No.'.$this->age.')';
			}
		}

		$info = $count = '';
		if ($showinfo) {
			$_title = str_replace('$1',$file_e,$this->root->_attach_messages['msg_info']);
			if (isset($this->root->vars['popup']) && $this->root->vars['cmd'] !== 'read') {
				$info = '&build_js(refInsert,"'.str_replace('|', '&#124;', $this->file).'",'.$this->type.');';
			} else {
				$returi = $_SERVER['REQUEST_URI'];
				if ($mode == "imglist") {
					$info = "[ [[{$this->root->_attach_messages['btn_info']}:{$this->root->script}?plugin=attach&pcmd=info".str_replace("&amp;","&", ($param . $param2))."]] ]";
					if ($is_owner) $info .= ' &build_js(attachDel,'.str_replace('|', '&#124;', $this->page).','.str_replace('|', '&#124;', $this->file).','.$this->age.','.$returi.');';
				} else {
					$info = "\n<span class=\"small\">[<a href=\"{$this->root->script}?plugin=attach&amp;pcmd=info{$param}{$param2}\" title=\"$_title\">{$this->root->_attach_messages['btn_info']}</a>]</span>";
					if ($is_owner) $info .= '<a href="'.$this->root->script.'?plugin=attach&pcmd=delete'.$param.$param2.'&amp;returi='.rawurlencode($returi).'" title="'.$this->root->_btn_delete.'" onclick="return confirm(\''.htmlspecialchars($this->file, ENT_QUOTES).': '.htmlspecialchars($this->root->_attach_messages['msg_delete'], ENT_QUOTES).'\')"><img src="'.$this->cont['LOADER_URL'].'?src=trash_16.gif" alt="'.$this->root->_btn_delete.'" /></a>';
				}
			}
			$count = ($showicon and !empty($this->status['count'][$this->age])) ?
				sprintf($this->root->_attach_messages['msg_count'],$this->status['count'][$this->age]) : '';
		}
		if ($mode == "imglist") {
			if ($this->age) {
				return '&size(12){'.$label.' '.$info.'};';
			} else {
				$ref_option = ($this->status['imagesize'] && $this->status['imagesize'][2] && $this->status['imagesize'][2] > 0 && $this->status['imagesize'][2] < 4)? $this->cont['ATTACH_CONFIG_REF_OPTION_IMG'] : $this->cont['ATTACH_CONFIG_REF_OPTION'];
				return "&size(12){&ref(\"".str_replace(array('"', '|'), array('""', '&#124;'), $this->page."/".$this->file)."\"".$ref_option.");&br();".$info."};";
			}
		} else {
			$filename = $this->status['org_fname'];
			$filename = str_replace(array(':', '*', '?', '"', '<', '>', '|'), '_', $filename);
			if (! $this->age && $this->cont['PLUGIN_ATTACH_SHORTURL']) {
				if ($filename !== $this->file) {
					$filename = '/' . rawurlencode($filename);
				} else {
					$filename = '';
				}
				return '<a href="'.$this->cont['HOME_URL'].'ref0/'.str_replace('%2F', '%252F', $page_e).'/'.$file_e.$filename.'" title="'.$title.'">'.$label.'</a>'.$count.$info;
			} else {
				$filename = '/' . rawurlencode($filename);
				return "<a href=\"{$this->cont['HOME_URL']}gate.php{$filename}?way=attach&amp;_noumb{$param}open{$param2}\" title=\"{$title}\">{$label}</a>{$count}{$info}";
			}
		}
	}
	// 情報表示
	function info($err) {

		$r_page = rawurlencode($this->page);
		$s_page = htmlspecialchars($this->page);
		$s_file = htmlspecialchars($this->file);
		$s_err = ($err == '') ? '' : '<p style="font-weight:bold">'.$this->root->_attach_messages[$err].'</p>';
		$ref = "";
		$img_info = "";
		$script = $this->func->get_script_uri();
		$pass = '';
		$msg_require = '';
		$msg_copyright = '';
		$msg_noinline = '';
		$msg_freezed = '';
		$msg_freeze  = '';
		$is_editable = $this->is_owner();
		$is_owner = $this->is_owner(TRUE);
		if ($this->cont['ATTACH_PASSWORD_REQUIRE'] && !$this->cont['ATTACH_UPLOAD_ADMIN_ONLY'] && !$is_editable)
		{
			$title = $this->root->_attach_messages[$this->cont['ATTACH_UPLOAD_ADMIN_ONLY'] ? 'msg_adminpass' : 'msg_password2'];
			$pass = $title.': <input type="password" name="pass" size="8" />';
			$msg_require = $this->root->_attach_messages['msg_require'];
		}
		$ref_option = $this->cont['ATTACH_CONFIG_REF_OPTION'];
		$msg_rename = '';

		// videoの場合の情報再取得ボタン
		$reinfo = '';
		list($type) = explode('/', strtolower($this->type));
		if ($is_editable && ($type === 'video' || $type === 'image' || $this->type === 'application/vnd.rn-realmedia') && (! $this->status['imagesize'] || ! $this->status['imagesize'][0])) {
			$reinfo = <<<EOD
<dd>
<form action="{$script}" method="post">
 <div>
  $img_info
  <input type="hidden" name="plugin" value="attach" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="file" value="$s_file" />
  <input type="hidden" name="age" value="{$this->age}" />
  <input type="hidden" name="pcmd" value="reinfo" />
  <input type="submit" value="Refresh info" />
 </div>
</form>
</dd>
EOD;
		}

		if ($this->age)
		{
			$msg_delete  = '<input type="radio" id="pcmd_d" name="pcmd" value="delete" /><label for="pcmd_d">'.$this->root->_attach_messages['msg_delete'].'</label>';
			$msg_delete .= '<br />';
		}
		else
		{
			// イメージファイルの場合
			$isize = $this->status['imagesize'];
			if (is_array($isize) && $isize[2] > 0 && $isize[2] < 4)
			{
				$ref_option = $this->cont['ATTACH_CONFIG_REF_OPTION_IMG'];
				$img_info = "Image: {$isize[0]} x {$isize[1]} px";
				if ($is_editable && (defined('HYP_JPEGTRAN_PATH') || $isize[2] == 2))
				{
					$img_info = <<<EOD
<form action="{$script}" method="post">
 <div>
  $img_info
  <input type="hidden" name="plugin" value="attach" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="file" value="$s_file" />
  <input type="hidden" name="age" value="{$this->age}" />
  <input type="hidden" name="pcmd" value="rotate" />
  [ Rotate:
  <input type="radio" id="rotate90" name="rd" value="1" /> <label for="rotate90">90&deg;</label>
  <input type="radio" id="rotate180" name="rd" value="2" /> <label for="rotate180">180&deg;</label>
  <input type="radio" id="rotate270" name="rd" value="3" /> <label for="rotate270">270&deg;</label>
  $pass
  <input type="submit" value="{$this->root->_attach_messages['btn_submit']}" /> ]
 </div>
</form>
EOD;
				}
			}

			// refプラグインで表示
			if ($this->func->exist_plugin_inline("ref"))
			{
				$ref .= "<dd><hr /></dd><dd>".$this->func->do_plugin_inline("ref", '"'. $this->page.'/'.str_replace('"', '""', $this->file) . '"' . $ref_option)."</dd>\n";
			}

			if ($this->status['freeze'])
			{
				$msg_freezed = "<dd>{$this->root->_attach_messages['msg_isfreeze']}</dd>";
				$msg_delete = '';
				if ($is_owner) {
					$msg_freeze  = '<input type="radio" id="pcmd_u" name="pcmd" value="unfreeze" /><label for="pcmd_u">'.$this->root->_attach_messages['msg_unfreeze'].'</label>';
					$msg_freeze .= $msg_require.'<br />';
				}
			}
			else
			{
				$backup_checked = (! $is_owner || ! $this->cont['ATTACH_DELETE_ADMIN_NOBACKUP'])? ' checked="checked"' : '';
				$backup_disabled = (! $is_owner)? ' disabled="disabled"' : '';
				$msg_backup = '(<input type="checkbox" id="backup" name="backup" value="1"'.$backup_checked.$backup_disabled.' /><label for="backup">'.$this->root->_attach_messages['msg_backup'].'</label>)';
				$msg_freezed = '';
				$msg_delete = '<input type="radio" id="pcmd_d" name="pcmd" value="delete" /><label for="pcmd_d">'.$this->root->_attach_messages['msg_delete'].'</label>';
				$msg_delete .= $msg_backup;
				$msg_delete .= $msg_require.'<br />';
				if ($is_owner) {
					$msg_freeze  = '<input type="radio" id="pcmd_f" name="pcmd" value="freeze" /><label for="pcmd_f">'.$this->root->_attach_messages['msg_freeze'].'</label>';
					$msg_freeze .= $msg_require.'<br />';
				}
				if ($this->cont['PLUGIN_ATTACH_RENAME_ENABLE']) {
					$msg_rename  = '<input type="radio" name="pcmd" id="_p_attach_rename" value="rename" />' .
						'<label for="_p_attach_rename">' .  $this->root->_attach_messages['msg_rename'] .
						$msg_require . '</label><br />&nbsp;&nbsp;&nbsp;&nbsp;' .
						'<label for="_p_attach_newname">' . $this->root->_attach_messages['msg_newname'] .
						':</label> ' .
						'<input type="text" name="newname" id="_p_attach_newname" size="40" value="' .
						(htmlspecialchars(empty($this->status['org_fname'])? $this->file : $this->status['org_fname'])) . '" /><br />';
				}
				if ($this->status['copyright']) {
					$msg_copyright  = '<input type="radio" id="pcmd_c" name="pcmd" value="copyright0" /><label for="pcmd_c">'.$this->root->_attach_messages['msg_copyright0'].'</label>';
				} else {
					$msg_copyright  = '<input type="radio" id="pcmd_c" name="pcmd" value="copyright1" /><label for="pcmd_c">'.$this->root->_attach_messages['msg_copyright'].'</label>';
				}
				$msg_copyright .= $msg_require.'<br />';
				if ($this->root->userinfo['admin']) {
					$allow_inlne = $this->is_allow_inline()? '1' : '-1';
					$noinline_m = $noinline = (intval($this->status['noinline']) === 0)? $allow_inlne : '0';
					if ($noinline === '0') {
						$noinline_m .= $allow_inlne;
					}
					$msg_noinline = '<input type="radio" id="pcmd_n" name="pcmd" value="noinline'.$noinline.'" /><label for="pcmd_n">'.$this->root->_attach_messages['msg_noinline'.$noinline_m].'</label>';
					$msg_noinline .= '<br />';
				} else {
					$msg_noinline = '';
				}
			}
		}
		$info = $this->toString(TRUE,FALSE);
		$copyright = ($this->status['copyright'])? ' checked=TRUE' : '';

		$retval = array('msg'=>sprintf($this->root->_attach_messages['msg_info'],htmlspecialchars($this->file)));
		$page_link = $this->func->make_pagelink($s_page);
		$ex_tags = '';
		if ($this->status['imagesize']) {
			if ($this->status['imagesize'][2] === IMG_JPG) {
				//EXIF DATA
				$exif_data = $this->func->get_exif_data($this->filename);
				if ($exif_data){
					$ex_tags = $exif_data['title'];
					foreach($exif_data as $key => $value){
						if ($key != "title") $ex_tags .= "<br />$key: $value";
					}
				}
				if ($is_owner) {
					if ($exif_data = $this->func->get_exif_data($this->filename, true)) {
						$ex_tags .= '<div><span class="button" onclick="$(\'xpwiki_attach_fullexif\').toggle();">Original EXIF (Show/Hide)</span></div><div id="xpwiki_attach_fullexif" style="display:none;">';
						foreach($exif_data as $key => $value){
							if (is_array($value)) {
								foreach($value as $_key => $_val) {
									$ex_tags .= "<div>$key.$_key: $_val</div>";
								}
							} else {
								$ex_tags .= "<div>$key: $value</div>";
							}
						}
						$ex_tags .= '</div>';
					}

				}
			}
			if ($this->status['imagesize'][2] < 1) {
				if (! empty($this->status['imagesize'][0])) {
					$ex_tags .= 'Width: ' . $this->status['imagesize'][0] . ' px<br />';
				}
				if (! empty($this->status['imagesize'][1])) {
					$ex_tags .= 'Height: ' . $this->status['imagesize'][1] . ' px<br />';
				}
				if (! empty($this->status['imagesize']['duration'])) {
					$duration = $this->status['imagesize']['duration'];
					if ($duration > 60) {
						$duration = intval($duration / 60) . ':' . intval($duration % 60);
					} else {
						$duration = intval($duration);
					}
					$ex_tags .= 'Duration: ' . $duration . ' s<br />';
				}
				if (! empty($this->status['imagesize']['framerate'])) {
					$ex_tags .= 'FrameRate: ' . sprintf('%02.2f', $this->status['imagesize']['framerate']) . ' fps<br />';
				}
				if (! empty($this->status['imagesize']['videocodec'])) {
					$ex_tags .= 'VideoCodec: ' . $this->status['imagesize']['videocodec'] . '<br />';
				}
				if (! empty($this->status['imagesize']['audiocodec'])) {
					$ex_tags .= 'AudioCodec: ' . $this->status['imagesize']['audiocodec'] . '<br />';
				}
			}
			if ($ex_tags) {
				$ex_tags = '<hr />' . $ex_tags;
			}
		}
		$v_filename = "<dd>{$this->root->_attach_messages['msg_filename']}:".$s_file;
		if ($this->root->userinfo['admin']) {
			$v_filename .=  '<br />&nbsp;&nbsp;&nbsp;'.basename($this->filename).'</dd>';
		} else {
			$v_filename .=  '</dd>';
		}
		$v_md5hash  = ($this->status['copyright'])? "" : "<dd>{$this->root->_attach_messages['msg_md5hash']}:{$this->status['md5']}</dd>";
		if ($img_info) $img_info = "<dd>{$img_info}</dd>";
		if ($ex_tags) $exif_tags = "<dd>{$ex_tags}</dd>";

		$retval['body'] = <<<EOD
<p class="small">
 [<a href="{$this->root->script}?plugin=attach&amp;pcmd=list&amp;refer=$r_page">{$this->root->_attach_messages['msg_list']}</a>]
 [<a href="{$this->root->script}?plugin=attach&amp;pcmd=list">{$this->root->_attach_messages['msg_listall']}</a>]
</p>
<dl style="word-break: break-all;">
 <dt>$info</dt>
 <dd>{$this->root->_attach_messages['msg_page']}:$page_link</dd>
 {$v_filename}
 {$v_md5hash}
 <dd>{$this->root->_attach_messages['msg_filesize']}:{$this->size_str} ({$this->size} bytes)</dd>
 <dd>Content-type:{$this->type}</dd>
 <dd>{$this->root->_attach_messages['msg_date']}:{$this->time_str}</dd>
 <dd>{$this->root->_attach_messages['msg_dlcount']}:{$this->status['count'][$this->age]}</dd>
 <dd>{$this->root->_attach_messages['msg_owner']}:{$this->owner_str}</dd>
 $ref
 $img_info
 $exif_tags
 $reinfo
 $msg_freezed
</dl>
$s_err
EOD;
		if ($is_editable || (! $this->owner_id && $pass && $this->status['uname'] !== 'System'))
		{
			$retval['body'] .= <<<EOD
<hr />
<form action="{$script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="attach" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="file" value="$s_file" />
  <input type="hidden" name="age" value="{$this->age}" />
  <input type="hidden" name="docmd" value="1" />
  $msg_delete
  $msg_freeze
  $msg_rename
  $msg_copyright
  $msg_noinline
  $pass
  <input type="submit" value="{$this->root->_attach_messages['btn_submit']}" />
 </div>
</form>
EOD;
		}
		return $retval;
	}
	function delete($pass)
	{
		if ($this->status['freeze'])
		{
			return xpwiki_plugin_attach::attach_info('msg_isfreeze');
		}

		$uid = $this->func->get_pg_auther($this->root->vars['page']);

		if (! $this->is_owner()) {
			// 管理者とページ作成者とファイル所有者以外
			if (! $this->func->pkwk_login($pass)) {
				if (($this->cont['ATTACH_PASSWORD_REQUIRE'] && (!$pass || md5($pass) != $this->status['pass'])) || $this->status['owner'])
					return xpwiki_plugin_attach::attach_info('err_password');

				if ($this->cont['ATTACH_DELETE_ADMIN_ONLY'] || $this->age)
					return xpwiki_plugin_attach::attach_info('err_adminpass');
			}
		}

		//バックアップ
		if ($this->age ||
			//($this->is_owner(TRUE) && $this->cont['ATTACH_DELETE_ADMIN_NOBACKUP'])) {
			($this->is_owner(TRUE) && empty($this->root->vars['backup']))) {
			@unlink($this->filename);
			$this->del_thumb_files();
			$this->func->attach_db_write(array('pgid'=>$this->pgid,'name'=>$this->file,'status'=>array('age'=>$this->age)),"delete");
		} else {
			$this->status['age'] = max(array_keys($this->status['count']));
			do {
				$age = ++$this->status['age'];
			} while (is_file($this->basename.'.'.$age));

			if (!rename($this->basename,$this->basename.'.'.$age)) {
				// 削除失敗 why?
				return array('msg'=>$this->root->_attach_messages['err_delete']);
			}

			$this->del_thumb_files();

			$this->status['count'][$age] = $this->status['count'][0];
			$this->status['count'][0] = 0;
			$this->putstatus(TRUE);
		}
		if ($this->func->is_page($this->page)) {
			$this->root->rtf['esummary'] = 'Deleted an attach file: ' . htmlspecialchars($this->file);
			$this->func->touch_page($this->page, NULL, TRUE);
		}

		$redirect = ($this->root->vars['returi'])? $this->root->siteinfo['host'] . $this->root->vars['returi'] : $this->root->script."?plugin=attach&pcmd=upload&page=".rawurlencode($this->page);

		return array('msg'=>$this->root->_attach_messages['msg_deleted'],'redirect'=>$redirect);
	}

	function rename($pass, $newname)
	{
		if ($this->status['freeze']) return xpwiki_plugin_attach::attach_info('msg_isfreeze');

		if (! $this->func->pkwk_login($pass)) {
			if ($this->cont['PLUGIN_ATTACH_DELETE_ADMIN_ONLY']) {
				return xpwiki_plugin_attach::attach_info('err_adminpass');
			} else if ($this->cont['PLUGIN_ATTACH_PASSWORD_REQUIRE'] &&
				md5($pass) != $this->status['pass']) {
				return xpwiki_plugin_attach::attach_info('err_password');
			}
		}

		$fname = xpwiki_plugin_attach::regularize_fname ($newname, $this->page);

		$hasBackup = count($this->status['count']) - 1;

		$this->status['count'] = array($this->status['count'][0]);
		$this->status['org_fname'] = $newname;

		$newbase = $this->cont['UPLOAD_DIR'] . $this->func->encode($this->page) . '_' . $this->func->encode($fname);
		if (is_file($newbase)) {
			return array('msg'=>$this->root->_attach_messages['err_exists']);
		}
		if (! $this->cont['PLUGIN_ATTACH_RENAME_ENABLE'] || ! rename($this->basename, $newbase)) {
			return array('msg'=>$this->root->_attach_messages['err_rename']);
		}

		if (! $hasBackup) @unlink($this->logname);

		//$this->rename_thumb_files($fname);
		$this->del_thumb_files();

		$this->file = $fname;
		$this->basename = $newbase;
		$this->filename = $this->basename;
		$this->logname  = $this->basename . '.log';

		if (is_file($this->logname)) {
			// found backup
			$_arr = file($this->logname);
			$counts = explode(',', rtrim($_arr[0]));
			$counts[0] = $this->status['count'][0];
			$this->status['count'] = $counts;
		}

		$this->action = 'update';

		$this->putstatus();

		return array('msg'=>$this->root->_attach_messages['msg_renamed']);
	}

	function freeze($freeze,$pass)
	{
		$uid = $this->func->get_pg_auther($this->root->vars['page']);
		if (!$this->is_owner())
		// 管理者とページ作成者とファイル所有者以外
		{
			if (! $this->func->pkwk_login($pass)) {
				if (($this->cont['ATTACH_PASSWORD_REQUIRE'] and (!$pass || md5($pass) != $this->status['pass'])) || $this->status['owner'])
					return xpwiki_plugin_attach::attach_info('err_password');
			}
		}
		$this->getstatus();
		$this->status['freeze'] = $freeze;
		$this->putstatus();

		$param  = '&file='.rawurlencode($this->file).'&refer='.rawurlencode($this->page).
			($this->age ? '&age='.$this->age : '');
		$redirect = "{$this->root->script}?plugin=attach&pcmd=info$param";

		return array('msg'=>$this->root->_attach_messages[$freeze ? 'msg_freezed' : 'msg_unfreezed'],'redirect'=>$redirect);
	}
	function rotate($count,$pass)
	{
		$uid = $this->func->get_pg_auther($this->root->vars['page']);
		if (!$this->is_owner())
		// 管理者とページ作成者とファイル所有者以外
		{
			if (! $this->func->pkwk_login($pass)) {
				if (($this->cont['ATTACH_PASSWORD_REQUIRE'] and (!$pass || md5($pass) != $this->status['pass'])) || $this->status['owner'])
					return xpwiki_plugin_attach::attach_info('err_password');
			}
		}

		$filemtime = filemtime($this->filename);
		$ret = HypCommonFunc::rotateImage($this->filename, $count);

		if ($ret) {
			$this->del_thumb_files();
			$this->func->pkwk_touch_file($this->filename, $filemtime);
			$this->getstatus();
			$this->status['imagesize'] = $this->getimagesize($this->filename);
			$this->putstatus();
		}

		$param  = '&file='.rawurlencode($this->file).'&refer='.rawurlencode($this->page).
			($this->age ? '&age='.$this->age : '');
		$redirect = "{$this->root->script}?plugin=attach&pcmd=info$param";

		return array('msg'=>$this->root->_attach_messages[$ret ? 'msg_rotated_ok' : 'msg_rotated_ng'],'redirect'=>$redirect);
	}
	function copyright($copyright,$pass)
	{
		$uid = $this->func->get_pg_auther($this->root->vars['page']);
		if (!$this->is_owner())
		// 管理者とページ作成者とファイル所有者以外
		{
			if (! $this->func->pkwk_login($pass)) {
				if (($this->cont['ATTACH_PASSWORD_REQUIRE'] and (!$pass || md5($pass) != $this->status['pass'])) || $this->status['owner'])
					return xpwiki_plugin_attach::attach_info('err_password');
			}
		}

		$this->getstatus();
		$this->status['copyright'] = $copyright;
		$this->putstatus();

		$param  = '&file='.rawurlencode($this->file).'&refer='.rawurlencode($this->page).
			($this->age ? '&age='.$this->age : '');
		$redirect = "{$this->root->script}?plugin=attach&pcmd=info$param";

		return array('msg'=>$this->root->_attach_messages[$copyright ? 'msg_copyrighted' : 'msg_uncopyrighted'],'redirect'=>$redirect);
	}

	function noinline($noinline,$pass)
	{
		$uid = $this->func->get_pg_auther($this->root->vars['page']);
		if (!$this->root->userinfo['admin'])
		// 管理者以外
		{
			if (! $this->func->pkwk_login($pass)) {
				return xpwiki_plugin_attach::attach_info('err_adminpass');
			}
		}

		$this->getstatus();
		$this->status['noinline'] = $noinline;
		$this->putstatus();

		$param  = '&file='.rawurlencode($this->file).'&refer='.rawurlencode($this->page).
			($this->age ? '&age='.$this->age : '');
		$redirect = "{$this->root->script}?plugin=attach&pcmd=info$param";

		return array('msg'=>$this->root->_attach_messages[$noinline != 0 ? 'msg_noinlined' : 'msg_unnoinlined'],'redirect'=>$redirect);
	}

	function reinfo() {
		if (!$this->is_owner())
		// 管理者とページ作成者とファイル所有者以外
		{
			return xpwiki_plugin_attach::attach_info('err_password');
		}

		$this->getstatus();
		$this->status['imagesize'] = $this->getimagesize($this->filename);
		$this->putstatus(FALSE);

		$param  = '&file='.rawurlencode($this->file).'&refer='.rawurlencode($this->page).
			($this->age ? '&age='.$this->age : '');
		$redirect = "{$this->root->script}?plugin=attach&pcmd=info$param";

		$msg = str_replace('$1', htmlspecialchars($this->status['org_fname']), $this->root->_title_updated);

		return array('msg' => $msg, 'redirect' => $redirect);

	}

	function open()
	{
		$this->getstatus();

		// clear output buffer
		$this->func->clear_output_buffer();

		$etag = $this->status['md5'] . ($this->status['copyright']? '1' : '0') . $this->status['noinline'];
		$expires = 'Expires: ' . gmdate( "D, d M Y H:i:s", $this->cont['UTC'] + $this->cont['BROWSER_CACHE_MAX_AGE'] ) . ' GMT';
		if ($etag == @ $_SERVER["HTTP_IF_NONE_MATCH"]) {
			header('HTTP/1.1 304 Not Modified' );
			header('Cache-Control: private');
			header('Pragma:');
			header($expires);
			exit();
		}

		if (!$this->is_owner())
		{
			if ($this->status['copyright'])
				return xpwiki_plugin_attach::attach_info('err_copyright');
		}

		// video, image でサイズが未取得の場合は取得しておく
		list($type) = explode('/', strtolower($this->type));
		if (($type === 'video' || $type === 'image') && (! $this->status['imagesize'] || ! $this->status['imagesize'][0])) {
			$this->status['imagesize'] = $this->getimagesize($this->filename);
		}

		$this->status['count'][$this->age]++;
		$this->putstatus();

		$filename = $this->status['org_fname'];

		$format = 'name="%1$s"';
		$encode = $this->cont['SOURCE_ENCODING'];
		// Care for Japanese-character-included file name
		if ($this->cont['LANG'] === 'ja') {
			switch($this->cont['UA_NAME']){
				case 'Opera':
				case 'Firefox':
					// RFC 2231 ( http://www.ietf.org/rfc/rfc2231.txt )
					$format = 'name*=%2$s\'ja\'%1$s';
					$filename = rawurlencode($filename);
					break;
				case 'MSIE':
					$filename = mb_convert_encoding($filename, 'SJIS-WIN', $this->cont['SOURCE_ENCODING']);
					break;
				default:
					if ($this->cont['SOURCE_ENCODING'] === 'UTF-8') {
						// RFC 2231 ( http://www.ietf.org/rfc/rfc2231.txt )
						$format = 'name*=%2$s\'ja\'%1$s';
						$filename = rawurlencode($filename);
					} else {
						$format = 'name="%1$s"; charset=UTF-8';
						$encode = 'UTF-8';
						$filename = mb_convert_encoding($filename, $encode, $this->cont['SOURCE_ENCODING']);
					}
				}
		}
		if (strpos(strtolower($this->root->ua), 'windows') !== FALSE) {
			$filename = str_replace(array(':', '*', '?', '"', '<', '>', '|'), '_', $filename);
		}
		$filename = sprintf($format, $filename, $encode);

		ini_set('default_charset','');
		mb_http_output('pass');

		// 画像以外(管理者所有を除く)はダウンロード扱いにする(XSS対策)
		if ($this->is_allow_inline()) {
			// リファラチェック
			if ($this->cont['OPEN_MEDIA_REFCHECK']
			 && in_array(strtolower(substr($this->type, 0, 5)), array('image','audio','video'))) {
				if (! $this->func->refcheck($this->cont['OPEN_MEDIA_REFCHECK'] - 1)) {
					exit('Access Denied!');
				}
			}
			header('Content-Disposition: inline; file' . $filename);
		} else 	{
			header('Content-Disposition: attachment; file' . $filename);
		}
		header('Content-Length: '.$this->size);
		header('Content-Type: '.$this->type.'; '.$filename);
		header('Last-Modified: '  . gmdate( "D, d M Y H:i:s", $this->time ) . " GMT" );
		header('Etag: '. $etag);
		header('Cache-Control: private');
		header('Pragma:');
		header($expires);

		HypCommonFunc::readfile($this->filename);
		exit;
	}

	// 該当ファイルのサムネイルを削除
	function del_thumb_files(){
		$dir = opendir($this->cont['UPLOAD_DIR']."s/")
			or die('directory '.$this->cont['UPLOAD_DIR'].'s/ is not exist or not readable.');

		$root = $this->cont['UPLOAD_DIR']."s/".$this->func->encode($this->page).'_';
		$_file = preg_split('/(\.[a-zA-Z]+)?$/', $this->file, -1, PREG_SPLIT_DELIM_CAPTURE);
		// Check original filename extention (for Renderer mode)
		if (! $_file[1] && preg_match('/(\.[a-zA-Z]+)$/', $this->status['org_fname'], $_match)) {
			$_file[1] = $_match[1];
		}
		$_file = $this->func->encode($_file[0]) . $_file[1];
		for ($i = 1; $i < 100; $i++)
		{
			$file = $root . $i . '_' . $_file;
			if (is_file($file))
			{
				unlink($file);
			}
		}
	}

/* remove
	// 該当ファイルのサムネイルをリネーム
	function rename_thumb_files($newname){
		$dir = opendir($this->cont['UPLOAD_DIR']."s/")
			or die('directory '.$this->cont['UPLOAD_DIR'].'s/ is not exist or not readable.');

		$root = $this->cont['UPLOAD_DIR']."s/".$this->func->encode($this->page).'_';
		for ($i = 1; $i < 100; $i++)
		{
			$base    = $root.$this->func->encode($i."%");
			$file    = $base.$this->func->encode($this->file);
			$newfile = $base.$this->func->encode($newname);
			if (is_file($file))
			{
				rename($file, $newfile);
			}
		}
	}
*/

	// 管理者、ページ作成者またはファイル所有者か？
	function is_owner($real = FALSE) {
		if (! $real && $this->cont['ATTACH_DISABLED_OWNER_CHECK']) return TRUE;
		if ($this->func->is_owner($this->page)) return TRUE;
		if ($this->age) return FALSE;
		if ($this->owner_id) {
			if ($this->root->userinfo['uid'] === $this->owner_id) return TRUE;
		} else {
			if ((! $real && ! $this->age && ! $this->cont['ATTACH_PASSWORD_REQUIRE'] && $this->status['uname'] !== 'System') ||
				$this->root->userinfo['ucd'] === $this->status['ucd']) return TRUE;
		}
		return FALSE;
	}

	function is_allow_inline () {
		if (!empty($this->root->get['ni'])) return false;

		$status = $this->status;
		$noinline = intval($status['noinline']);

		$return = false;
		if ($noinline > 0) {
			$return = false;
		} else if ($noinline < 0) {
			$return = true;
		} else {
			if ($status['imagesize']) {
				if ($status['imagesize'][2] === 4 || $status['imagesize'][2] === 13) {
					// Flash のインライン表示権限チェック
					if ($this->cont['PLUGIN_REF_FLASH_INLINE'] === 3) {
						// すべて許可
						$return = true;
					} else if ($this->cont['PLUGIN_REF_FLASH_INLINE'] === 2) {
						// 登録ユーザー所有のみ許可
						if ($status['owner'] > 0) {
							$return = true;
						}
					} else if ($this->cont['PLUGIN_REF_FLASH_INLINE'] === 1) {
						// 管理人所有のみ許可
						if ($status['admins']) {
							$return = true;
						}
					}
				} else {
					$return = true;
				}
			} else {
				if ($status['admins']) {
					$return = true;
				}
			}
		}
		return $return;
	}
}

// ファイルコンテナ
class XpWikiAttachFiles
{
	var $page;
	var $pgid;
	var $files = array();
	var $count = 0;
	var $max = 50;
	var $start = 0;
	var $order = "";

	function XpWikiAttachFiles(& $xpwiki, $page)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;

		$this->page = $page;
		$this->is_popup = (isset($this->root->vars['popup']) && $this->root->vars['cmd'] !== 'read');
	}
	function add($file,$age)
	{
		$this->files[$file][$age] = &new XpWikiAttachFile($this->xpwiki, $this->page,$file,$age,$this->pgid);
	}
	// ファイル一覧を取得
	function toString($flat,$fromall=FALSE,$mode="")
	{
		if (!$this->func->check_readable($this->page,FALSE,FALSE))
		{
			return str_replace('$1',$this->func->make_pagelink($this->page),$this->root->_title_cannotread);
		}
		if ($flat)
		{
			return $this->to_flat();
		}

		$this->func->add_tag_head('attach.css');

		$ret = '';
		$files = array_keys($this->files);
		$navi = "";
		$pcmd = ($mode == "imglist")? "imglist" : "list";
		$pcmd2 = ($mode == "imglist")? "list" : "imglist";

		$otherkeys = array('cols', 'max', 'base', 'mode', 'winop', 'basedir', 'encode_hint', 'word');
		if ($this->is_popup) {
			$otherkeys[] = 'popup';
		}
		if (! isset($this->root->vars['basedir'])) {
			$this->root->vars['basedir'] = $this->root->mydirname;
		}
		$otherparm = '';
		$otherprams = array();
		foreach($otherkeys as $key) {
			if (isset($this->root->vars[$key])) {
				$otherprams[] = rawurlencode($key) . '=' . rawurlencode($this->root->vars[$key]);
			}
		}
		if (! isset($this->root->vars['max'])) {
			$otherprams[] = 'max=' . $this->max;
		}
		if ($otherprams) {
			$otherparm = '&amp;' . join('&amp;', $otherprams);
		}

		if (!$fromall)
		{
			$url = $this->root->script."?plugin=attach&amp;pcmd={$pcmd}&amp;refer=".rawurlencode($this->page).$otherparm."&amp;order=".$this->order."&amp;start=";
			$url2 = $this->root->script."?plugin=attach&amp;pcmd={$pcmd}&amp;refer=".rawurlencode($this->page).$otherparm."&amp;start=";
			$url3 = $this->root->script."?plugin=attach&amp;pcmd={$pcmd2}&amp;refer=".rawurlencode($this->page).$otherparm."&amp;order=".$this->order."&amp;start=".$this->start;
			$sort_time = ($this->order == "name")? " [ <a href=\"{$url2}0&amp;order=time\">{$this->root->_attach_messages['msg_sort_time']}</a> |" : " [ <b>{$this->root->_attach_messages['msg_sort_time']}</b> |";
			$sort_name = ($this->order == "name")? " <b>{$this->root->_attach_messages['msg_sort_name']}</b> ] " : " <a href=\"{$url2}0&amp;order=name\">{$this->root->_attach_messages['msg_sort_name']}</a> ] ";

			if ($this->is_popup) {
				$mode_tag = '';
			} else {
				$mode_tag = ($mode == "imglist")? "[ <a href=\"$url3\">{$this->root->_attach_messages['msg_list_view']}</a> ]":"[ <a href=\"$url3\">{$this->root->_attach_messages['msg_image_view']}</a> ]";
			}

			if ($this->max < $this->count)
			{
				$_start = $this->start + 1;
				$_end = $this->start + $this->max;
				$_end = min($_end,$this->count);
				$now = $this->start / $this->max + 1;
				$total = ceil($this->count / $this->max);
				$navi = array();
				for ($i=1;$i <= $total;$i++)
				{
					if ($now == $i)
						$navi[] = "<b>$i</b>";
					else
						$navi[] = "<a href=\"".$url.($i - 1) * $this->max."\"><span class=\"button\">$i</span></a>";
				}
				$navi = join(' ',$navi);

				$prev = max(0,$now - 1);
				$next = $now;
				$prev = ($prev)? "<a href=\"".$url.($prev - 1) * $this->max."\" title=\"Prev\"><span class=\"button\"> <img src=\"{$this->cont['LOADER_URL']}?src=prev.png\" width=\"6\" height=\"12\" alt=\"Prev\"> </span></a>" : "";
				$next = ($next < $total)? "<a href=\"".$url.$next * $this->max."\" title=\"Next\"><span class=\"button\"> <img src=\"{$this->cont['LOADER_URL']}?src=next.png\" width=\"6\" height=\"12\" alt=\"Next\"> </span></a>" : "";

				$navi = "<div class=\"page_navi\">| $navi |<br />[{$prev} $_start - $_end / ".$this->count." files {$next}]<br />{$sort_time}{$sort_name}{$mode_tag}</div>";
			}
			else if ($this->count)
			{
				$navi = "<div class=\"page_navi\">{$sort_time}{$sort_name}{$mode_tag}</div>";
			}
			else
			{
				$navi = '';
			}
		}
		$col = 1;
		$cols = (! empty($this->root->vars['cols']))? max(1, min(intval($this->root->vars['cols']), 5)) : 4;
		$mod = 0;
		foreach ($files as $file)
		{
			$_files = array();
			foreach (array_keys($this->files[$file]) as $age)
			{
				if ($this->is_popup && $age > 0) {
					continue;
				}
				$_files[$age] = $this->files[$file][$age]->toString(FALSE,TRUE,$mode);
			}
			if (!array_key_exists(0,$_files))
			{
				if ($this->is_popup) {
					continue;
				}
				$_files[0] = htmlspecialchars($file);
			}
			ksort($_files);
			$_file = $_files[0];
			unset($_files[0]);
			if ($mode == "imglist")
			{
				$ret .= "|$_file";
				if (count($_files))
				{
					$ret .= "&br;- ".join("&br;- ",$_files);
				}
				$mod = $col % $cols;
				if ($mod === 0)
				{
					$ret .= "|\n";
					$col = 0;
				}
				$col++;
			}
			else
			{
				$ret .= " <li>$_file\n";
				if (count($_files))
				{
					$ret .= "<ul>\n<li>".join("</li>\n<li>",$_files)."</li>\n</ul>\n";
				}
				$ret .= " </li>\n";
			}
		}

		if ($mode == "imglist")
		{
			if ($mod) $ret .= str_repeat("|>", $cols - $mod)."|\n";
			$ret = '|' . str_repeat('CENTER:|', $cols) . "c\n".$ret;
		 	$_refer = $this->root->vars['refer'];
		 	$this->root->vars['refer'] = $this->page;
		 	$ret = $this->func->convert_html($ret);
		 	$this->root->vars['refer'] = $_refer;
		} else {
			$ret = "<ul>\n$ret</ul>";
		}

		$form = '';
		if ($this->is_popup && !$fromall) {
			if (empty($this->root->vars['start'])) {
				$attach =& $this->func->get_plugin_instance('attach');
				if ($attach->attachable($this->page)) {
					$form = $attach->attach_form($this->page);
				} else {
					$form = $this->root->_attach_messages['msg_for_upload'];
				}
				if ($form) $form .= '<hr />';
			}
		}

		$filecount = '<small> (' . $this->count . '&nbsp;file' . (($this->count>1)?'s':'') . ')</small>';
		$showall_href = "{$this->root->script}?plugin=attach&amp;pcmd={$pcmd}{$otherparm}&amp;refer=".rawurlencode($this->page);
		$showall = ($fromall && $this->max < $this->count)? " [&nbsp;<a href=\"{$showall_href}\">Show All</a>&nbsp;]" : "";
		if ($this->is_popup) {
			if ($fromall) {
				$showall = "<div class=\"filelist_page\"><a href=\"{$showall_href}\">" . htmlspecialchars($this->page) . '</a>' . $filecount . '<small>' . $showall . '</small></div>';
			} else {
				$showall = '';
			}
		}
		$allpages = ($this->is_popup || $fromall)? "" : " [ <a href=\"{$this->root->script}?plugin=attach&amp;pcmd={$pcmd}{$otherparm}\">All Pages</a> ]";

		$body = $this->is_popup? $showall . $ret : "<div class=\"filelist_page\">".$this->func->make_pagelink($this->page).$filecount.$showall.$allpages."</div>\n$ret";

		return $form.$navi.($navi? "<hr />":"").$body.($navi? "<hr />":"")."$navi\n";
	}
	// ファイル一覧を取得(inline)
	function to_flat()
	{
		$ret = '';
		$files = array();
		foreach (array_keys($this->files) as $file)
		{
			if (array_key_exists(0,$this->files[$file]))
			{
				$files[$file] = &$this->files[$file][0];
			}
		}
		uasort($files,array('XpWikiAttachFile','datecomp'));

		foreach (array_keys($files) as $file)
		{
			$ret .= $files[$file]->toString(TRUE,TRUE).' ';
		}
		$more = $this->count - $this->max;
		$more = ($this->count > $this->max)? "... more ".$more." files. [ <a href=\"{$this->root->script}?plugin=attach&amp;pcmd=list&amp;refer=".rawurlencode($this->page)."\">Show All</a> ]" : "";
		return $ret.$more;
	}
}
	// ページコンテナ
class XpWikiAttachPages
{
	var $pages = array();
	var $start = 0;
	var $max = 50;
	var $mode = "";
	var $err = 0;

	function XpWikiAttachPages(& $xpwiki, $page='',$age=NULL,$isbn=true,$max=50,$start=0,$fromall=FALSE,$f_order="time",$mode="")
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;

		if (! empty($this->root->vars['max'])) {
			$max = max(1, min($max, intval($this->root->vars['max'])));
		}

		$this->mode = $mode;

		$this->is_popup = (isset($this->root->vars['popup']) && $this->root->vars['cmd'] !== 'read');

		if ($page !== '')
		{
			// 閲覧権限チェック
			if (!$fromall && !$this->func->check_readable($page,false,false)) return;

			$this->pages[$page] = &new XpWikiAttachFiles($this->xpwiki, $page);

			$pgid = $this->func->get_pgid_by_name($page);
			$this->pages[$page]->pgid = $pgid;

			// WHERE句
			$where = array();
			$where[] = "`pgid` = {$pgid}";
			if (isset($this->root->vars['popup']) && $this->root->vars['cmd'] !== 'read') $where[] = '`name` != "fusen.dat"';
			if (!$isbn) $where[] = "`mode` != '1'";
			if (!is_null($age)) $where[] = "`age` = $age";
			//if ($mode == "imglist") $where[] = "`type` LIKE 'image%' AND `age` = 0";
			//if ($mode == "imglist") $where[] = "`age` = 0";
			if (!empty($this->root->vars['word'])) {
				foreach(explode(' ', mb_convert_kana($this->root->vars['word'], 's')) as $search) {
					$where[] = '`name` LIKE \'%'.addslashes($search).'%\'';
				}
			}
			$where = " WHERE ".join(' AND ',$where);

			// このページの添付ファイル数取得
			$query = "SELECT count(*) as count FROM `".$this->xpwiki->db->prefix($this->root->mydirname."_attach")."`{$where};";
			if (!$result = $this->xpwiki->db->query($query))
				{
					$this->err = 1;
					return;
				}
			list($_count) = $this->xpwiki->db->fetchRow($result);
			if (!$_count) return;

			$this->pages[$page]->count = $_count;
			$this->pages[$page]->max = $max;
			$this->pages[$page]->start = $start;
			$this->pages[$page]->order = $f_order;

			// ファイル情報取得
			$order = ($f_order == "name")? " ORDER BY name ASC" : " ORDER BY mtime DESC";
			$limit = " LIMIT {$start},{$max}";
			$query = "SELECT name,age FROM `".$this->xpwiki->db->prefix($this->root->mydirname."_attach")."`{$where}{$order}{$limit};";
			$result = $this->xpwiki->db->query($query);
			while($_row = $this->xpwiki->db->fetchRow($result))
			{
				$_file = $_row[0];
				$_age = $_row[1];
				$this->pages[$page]->add($_file,$_age);
			}
		}
		else
		{
			// WHERE句
			$where = array();
			if ($_readable_where = $this->func->get_readable_where('p.')) {
				$where[] = '(' . $_readable_where . ')';
			}
			if (isset($this->root->vars['popup']) && $this->root->vars['cmd'] !== 'read') $where[] = 'a.`name` != "fusen.dat"';
			if (!empty($this->root->vars['word'])) {
				foreach(explode(' ', mb_convert_kana($this->root->vars['word'], 's')) as $search) {
					$where[] = 'a.`name` LIKE \'%'.addslashes($search).'%\'';
				}
			}
			$where = $where? " WHERE ".join(' AND ',$where) : '';

			// 添付ファイルのあるページ数カウント
			$query = "SELECT DISTINCT p.pgid FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." p INNER JOIN ".$this->xpwiki->db->prefix($this->root->mydirname."_attach")." a ON p.pgid=a.pgid{$where}";
			$result = $this->xpwiki->db->query($query);

			$this->count = $result ? mysql_num_rows($result) : 0;

			$this->max = $max;
			$this->start = $start;
			$this->order = $f_order;

			// ページ情報取得
			$order = ($f_order == "name")? " ORDER BY p.name ASC" : " ORDER BY p.editedtime DESC";
			$limit = " LIMIT $start,$max";

			$query = "SELECT DISTINCT p.name FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." p INNER JOIN ".$this->xpwiki->db->prefix($this->root->mydirname."_attach")." a ON p.pgid=a.pgid{$where}{$order}{$limit};";
			if (!$result = $this->xpwiki->db->query($query)) {
				if ($this->root->userinfo['admin']) echo "QUERY ERROR : ".$query;
			}

			while($_row = $this->xpwiki->db->fetchRow($result))
			{
				$this->XpWikiAttachPages($this->xpwiki,$_row[0],$age,$isbn,20,0,TRUE,$f_order,$mode);
			}
		}
	}
	function toString($page='',$flat=FALSE)
	{
		$pcmd = ($this->mode == "imglist")? "imglist" : "list";
		$pcmd2 = ($this->mode == "imglist")? "list" : "imglist";

		$otherkeys = array('cols', 'max', 'base', 'mode', 'winop', 'basedir', 'encode_hint', 'word');
		if ($this->is_popup) {
			$otherkeys[] = 'popup';
			if ($this->cont['UA_PROFILE'] === 'mobile') $this->func->add_tag_head('<!--jqm_theme_d-->');
		}
		if (! isset($this->root->vars['basedir'])) {
			$this->root->vars['basedir'] = $this->root->mydirname;
		}
		$otherparm = '';
		$otherprams = array();
		$hiddens = array();
		$hiddens['plugin'] = 'attach';
		$hiddens['pcmd'] = $pcmd;
		$hiddens['refer'] = (isset($this->root->vars['refer']))? htmlspecialchars($this->root->vars['refer']) : '';
		foreach($otherkeys as $key) {
			if (isset($this->root->vars[$key])) {
				$otherprams[] = rawurlencode($key) . '=' . rawurlencode($this->root->vars[$key]);
				$hiddens[htmlspecialchars($key)] = htmlspecialchars($this->root->vars[$key]);
			}
		}

		$select_js = $otherDir = $select = '';
		if ($this->is_popup) {
			$dirs = $otherDirs = array();
			if ($handle = opendir($this->cont['MODULE_PATH'])) {
				while (false !== ($dir = readdir($handle))) {
					if (is_dir($this->cont['MODULE_PATH'].$dir) && $dir[0] !== '.' && $this->func->isXpWikiDirname($dir)) {
						$other = XpWiki::getInitedSingleton($dir);
						if ($other->isXpWiki) {
							if ($other->root->pages_for_attach) {
								list($dirs[$dir]['defaultpage']) = explode('#', $other->root->pages_for_attach);
							} else {
								$dirs[$dir]['defaultpage'] = $other->root->defaultpage;
							}
							$dirs[$dir]['title'] = $other->root->module['title'];
						}
					}
				}
			}
			if (count($dirs) > 1) {
				ksort($dirs);
				foreach($dirs as $dir => $val) {
					$defaultpage = $val['defaultpage'];
					$selected = ($dir === $this->root->mydirname)? ' selected="selected"' : '';
					if ($this->root->vars['basedir'] === $dir) {
						$defaultpage = $this->root->vars['base'];
					}
					$otherDirs[] = '<option value="' . $dir . '#' . htmlspecialchars($defaultpage) . '"' . $selected . '>' . htmlspecialchars($val['title']) . '</option>';
				}
				$otherDir = '<form><img src="' . $this->cont['LOADER_URL'] . '?src=folder_go.png" alt="Dir" /> <select name="otherdir" style="max-width:85%;" onchange="xpwiki_dir_selector_change(this.options[this.selectedIndex].value)">' . join('', $otherDirs) . '</select></form>';
			}

			$where = array();
			if (!empty($this->root->vars['word'])) {
				foreach(explode(' ', mb_convert_kana($this->root->vars['word']), 's') as $search) {
					$where[] = 'a.`name` LIKE \'%'.addslashes($search).'%\'';
				}
			}
			$where = $where? ' AND ' . join(' AND ', $where) : '';

			$otherPages = array();
			$shown = array($this->root->vars['base']);
			$attach =& $this->func->get_plugin_instance('attach');

			if ($this->root->pages_for_attach) {
				$otherPages[] = '<optgroup label="' . $this->root->_attach_messages['msg_select_useful'] . '">';
				foreach(explode('#', $this->root->pages_for_attach) as $_page) {
					if ($this->func->check_readable($_page, false, false)) {
						$selected = ($_page === $page)? ' selected="selected"' : '';
						$shown[] = $_page;
						$_pgid = $this->func->get_pgid_by_name($_page);
						if ($_pgid) {
							$query = 'SELECT count( * ) FROM `' . $this->xpwiki->db->prefix($this->root->mydirname.'_attach') . '` a WHERE a.pgid="' . $_pgid . '" AND a.age = 0 AND a.name != "fusen.dat"' . $where . ' LIMIT 1';
							$count = '';
							if ($result = $this->xpwiki->db->query($query)) {
								$row = $this->xpwiki->db->fetchRow($result);
								$count = ' (' . $row[0] . ')';
							}
						} else {
							$count = ' (0)';
						}
						$_attachable = '';
						$_class = 'readable';
						if ($attach->attachable($_page)) {
							$_class = 'attachable';
							if ($this->cont['UA_PROFILE'] !== 'default') $_attachable = '&uarr;';
						}
						$otherPages[] = '<option class="'.$_class.'" value="' . rawurlencode($_page) . '"' . $selected . '>' . $_attachable . htmlspecialchars($_page) . $count . '</option>';
					}
				}
				$otherPages[] = '</optgroup>';
			}

			$query = 'SELECT p.name, count( * ) AS count FROM `' . $this->xpwiki->db->prefix($this->root->mydirname.'_pginfo') . '` p INNER JOIN `' . $this->xpwiki->db->prefix($this->root->mydirname.'_attach') . '` a ON p.pgid = a.pgid WHERE a.age =0 AND a.name != "fusen.dat"' . $where . ' GROUP BY a.pgid ORDER BY count DESC, p.name ASC LIMIT 0 , 50';
			if ($result = $this->xpwiki->db->query($query)) {
				$otherPages[] = '<optgroup label="' . $this->root->_attach_messages['msg_select_manyitems'] . '">';
				while($row = $this->xpwiki->db->fetchRow($result)) {
					if ($this->func->check_readable($row[0], false, false)) {
						if (in_array($row[0], $shown)) continue;
						$selected = ($row[0] === $page)? ' selected="selected"' : '';
						$_page = htmlspecialchars($row[0]);
						$_attachable = '';
						$_class = 'readable';
						if ($attach->attachable($_page)) {
							$_class = 'attachable';
							if ($this->cont['UA_PROFILE'] !== 'default') $_attachable = '&uarr;';
						}
						$otherPages[] = '<option class="'.$_class.'" value="' . rawurlencode($_page) . '"' . $selected . '>' . $_attachable . htmlspecialchars($_page) . ' (' . $row[1] . ')</option>';
					}
				}
				$otherPages[] = '</optgroup>';
			}
			if ($otherPages) {
				$thisPage = '<option value="">--- ' . $this->root->_attach_messages['msg_page_select'] . ' ---</option>';
				if ($this->root->vars['basedir'] === $this->root->mydirname) {
					$selected = ($this->root->vars['base'] === $page)? ' selected="selected"' : '';
					$thisPage .= '<option value="'.rawurlencode($this->root->vars['base']).'"' . $selected . '>' . htmlspecialchars($this->root->vars['base']) . $this->root->_attach_messages['msg_select_current'] . '</option>';
				}
				if (! empty($this->root->vars['refer'])) $thisPage .= '<option value="#">'.$this->root->_attach_messages['msg_show_all_pages'].'</option>';
				$base = rawurlencode($this->root->vars['base']);
				$select = '<form><img src="' . $this->cont['LOADER_URL'] . '?src=page_attach.png" alt="Pages" /> <select name="othorpage" style="max-width:85%;" onchange="xpwiki_file_selector_change(this.options[this.selectedIndex].value, \''.$base.'\')">' . $thisPage . join('', $otherPages) . '</select></form>';
			}
			$select_js = <<<EOD
<script type="text/javascript"><!--
function xpwiki_file_selector_change(page, base) {
	if (page || page == 0) {
		if (page == '#') page = '';
		var href = location.href;
		if (! href.match(/&refer=[^&]*/)) {
			href += '&refer=';
		}
		location.href = href.replace(/&refer=[^&]*/, '&refer=' + page).replace(/&base=[^&]*/, '&base=' + base).replace(/&(start|encode_hint)=[^&]+/, '');
	}
}
function xpwiki_dir_selector_change(dir) {
	if (dir) {
		var arr = dir.split('#');
		location.href = location.href.replace(/\/modules\/[^\/]+/, '/modules/' + arr[0]).replace(/&refer=[^&]*/, '&refer=').replace(/&start=[^&]+/, '');
	}
}
//-->
</script>
EOD;
		}

		$sword = (isset($this->root->vars['word']))? htmlspecialchars($this->root->vars['word']) : '';
		$hidden = '';
		unset($hiddens['word']);
		foreach($hiddens as $key=> $val) {
			$hidden .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $val);
		}
		if ($flat) {
			$search = '';
		} else {
			if ($this->cont['UA_PROFILE'] === 'mobile') {
				$search = '<div><form method="get" action="' . $this->root->script . '"><input type="search" name="word" autocomplete="off" value="' . $sword  . '" />' . $hidden . '</form></div>';
			} else {
				$search = '<div><form method="get" action="' . $this->root->script . '"><img src="' . $this->cont['LOADER_URL'] . '?src=find.png" alt="Search" /> <input size="15" type="search" name="word" value="' . $sword  . '" /><input data-inline="true" type="submit" value="' . $this->root->_btn_search . '" />' . $hidden . '</form></div>';
			}
		}

		if ($page !== '')
		{
			if (!array_key_exists($page,$this->pages))
			{
				return '';
			}
			return '<div class="attach_list">' . $select_js  . $otherDir . $select . $search . $this->pages[$page]->toString($flat,FALSE,$this->mode) . '</div>';
		}

		if ($otherprams) {
			$otherparm = '&amp;' . join('&amp;', $otherprams);
		}

		$url = $this->root->script."?plugin=attach&amp;pcmd={$pcmd}{$otherparm}&amp;order=".$this->order."&amp;start=";
		$url2 = $this->root->script."?plugin=attach&amp;pcmd={$pcmd}{$otherparm}&amp;start=";
		$url3 = $this->root->script."?plugin=attach&amp;pcmd={$pcmd2}{$otherparm}&amp;order=".$this->order."&amp;start=".$this->start;
		$sort_time = ($this->order == "name")? " [ <a href=\"{$url2}0&amp;order=time\">{$this->root->_attach_messages['msg_sort_time']}</a> |" : " [ <b>{$this->root->_attach_messages['msg_sort_time']}</b> |";
		$sort_name = ($this->order == "name")? " <b>{$this->root->_attach_messages['msg_sort_name']}</b> ] " : " <a href=\"{$url2}0&amp;order=name\">{$this->root->_attach_messages['msg_sort_name']}</a> ] ";
		if ($this->is_popup) {
			$mode_tag = '';
		} else {
			$mode_tag = ($this->mode == "imglist")? "[ <a href=\"$url3\">{$this->root->_attach_messages['msg_list_view']}</a> ]":"[ <a href=\"$url3\">{$this->root->_attach_messages['msg_image_view']}</a> ]";
		}
		$_start = $this->start + 1;
		$_end = $this->start + $this->max;
		$_end = min($_end,$this->count);
		$now = $this->start / $this->max + 1;
		$total = ceil($this->count / $this->max);
		$navi = array();

		for ($i=1;$i <= $total;$i++)
		{
			if ($now == $i)
				$navi[] = "<b>$i</b>";
			else
				$navi[] = "<a href=\"".$url.($i - 1) * $this->max."\"><span class=\"button\">$i</span></a>";
		}
		$navi = join(' ',$navi);
		$prev = max(0,$now - 1);
		$next = $now;
		$prev = ($prev)? "<a href=\"".$url.($prev - 1) * $this->max."\" title=\"Prev\"><span class=\"button\"> <img src=\"{$this->cont['LOADER_URL']}?src=prev.png\" width=\"6\" height=\"12\" alt=\"Prev\"> </span></a>" : "";
		$next = ($next < $total)? "<a href=\"".$url.$next * $this->max."\" title=\"Next\"><span class=\"button\"> <img src=\"{$this->cont['LOADER_URL']}?src=next.png\" width=\"6\" height=\"12\" alt=\"Next\"> </span></a>" : "";
		$navi = "<div class=\"page_navi\">| $navi |<br />[{$prev} $_start - $_end / ".$this->count." pages {$next}]<br />{$sort_time}{$sort_name}{$mode_tag}</div>";

		$ret = '';
		$pages = array_keys($this->pages);
		if ($pages) {
			foreach ($pages as $page)
			{
				$ret .= $this->pages[$page]->toString($flat,TRUE,$this->mode)."\n";
			}
		} else {
			$navi = '';
		}

		return "\n<div class=\"attach_list\">$select_js$otherDir$select$search$navi".($navi? "<hr />":"")."\n$ret\n".($navi? "<hr />":"")."$navi</div>\n";;
	}

}

?>