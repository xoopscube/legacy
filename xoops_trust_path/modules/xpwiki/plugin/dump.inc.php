<?php
// $Id: dump.inc.php,v 1.21 2011/11/22 09:12:12 nao-pon Exp $
//
// Remote dump / restore plugin
// Originated as tarfile.inc.php by teanan / Interfair Laboratory 2004.

class xpwiki_plugin_dump extends xpwiki_plugin {
	function plugin_dump_init () {
		/////////////////////////////////////////////////
		// User defines

		// Allow using resture function
		$this->cont['PLUGIN_DUMP_ALLOW_RESTORE'] =  TRUE; // FALSE, TRUE

		// ページ名をディレクトリ構造に変換する際の文字コード (for mbstring)
		$this->cont['PLUGIN_DUMP_FILENAME_ENCORDING'] =  'SJIS';

		// 最大アップロードサイズ
		$this->cont['PLUGIN_DUMP_MAX_FILESIZE'] = (int)$this->func->return_bytes(ini_get('upload_max_filesize')) / 1024; // Kbyte

		/////////////////////////////////////////////////
		// Internal defines

		// Action
		$this->cont['PLUGIN_DUMP_DUMP'] =     'dump';    // Dump & download
		$this->cont['PLUGIN_DUMP_RESTORE'] =  'restore'; // Upload & restore

		//global $_STORAGE;

		// DATA_DIR (wiki/*.txt)
		$this->root->_STORAGE['DATA_DIR']['add_filter']     = '^[0-9A-F]+\.txt';
		$this->root->_STORAGE['DATA_DIR']['extract_filter'] = $this->format_extract_filter($this->cont['DATA_DIR'], '[0-9A-F]+\.txt');

		// UPLOAD_DIR (attach/*)
		$this->root->_STORAGE['UPLOAD_DIR']['add_filter']     = '^(?:[0-9A-F]{2})+_(?:[0-9A-F]{2})+(?:\.log)?';
		$this->root->_STORAGE['UPLOAD_DIR']['extract_filter'] = $this->format_extract_filter($this->cont['UPLOAD_DIR'], '(?:[0-9A-F]{2})+_(?:[0-9A-F]{2})+(?:\.log)?');

		// COUNTER_DIR (counter/*.count)
		$this->root->_STORAGE['COUNTER_DIR']['add_filter']     = '^[0-9A-F]+\.count';
		$this->root->_STORAGE['COUNTER_DIR']['extract_filter'] = $this->format_extract_filter($this->cont['COUNTER_DIR'], '[0-9A-F]+\.count');

		// BACKUP_DIR (backup/*.(gz|txt))
		$this->root->_STORAGE['BACKUP_DIR']['add_filter']     = '^[0-9A-F]+\.(?:gz|txt)';
		$this->root->_STORAGE['BACKUP_DIR']['extract_filter'] =  $this->format_extract_filter($this->cont['BACKUP_DIR'], '[0-9A-F]+\.(?:gz|txt)');

		// DIFF_DIR (diff/*.(txt|add))
		$this->root->_STORAGE['DIFF_DIR']['add_filter']     = '^[0-9A-F]+\.(?:txt|add)';
		$this->root->_STORAGE['DIFF_DIR']['extract_filter'] = $this->format_extract_filter($this->cont['DIFF_DIR'], '[0-9A-F]+\.(?:txt|add)');

		// TRACKBACK_DIR (trackback/*.(ref|txt))
		$this->root->_STORAGE['TRACKBACK_DIR']['add_filter']     = '^[0-9A-F]+\.(ref|txt)';
		$this->root->_STORAGE['TRACKBACK_DIR']['extract_filter'] = $this->format_extract_filter($this->cont['TRACKBACK_DIR'], '[0-9A-F]+\.(?:ref|txt)');

		// DB SQL dump (cache/*.sql)
		$this->root->_STORAGE['SQL_DUMP']['extract_filter'] = $this->format_extract_filter($this->cont['CACHE_DIR'], '(?:pginfo|count|attach|plain|rel)\d*\.sql');


		/////////////////////////////////////////////////
		// tarlib: a class library for tar file creation and expansion

		// Tar related definition
		$this->cont['TARLIB_HDR_LEN'] =            512;	// ヘッダの大きさ
		$this->cont['TARLIB_BLK_LEN'] =            512;	// 単位ブロック長さ
		$this->cont['TARLIB_HDR_NAME_OFFSET'] =      0;	// ファイル名のオフセット
		$this->cont['TARLIB_HDR_NAME_LEN'] =       100;	// ファイル名の最大長さ
		$this->cont['TARLIB_HDR_MODE_OFFSET'] =    100;	// modeへのオフセット
		$this->cont['TARLIB_HDR_UID_OFFSET'] =     108;	// uidへのオフセット
		$this->cont['TARLIB_HDR_GID_OFFSET'] =     116;	// gidへのオフセット
		$this->cont['TARLIB_HDR_SIZE_OFFSET'] =    124;	// サイズへのオフセット
		$this->cont['TARLIB_HDR_SIZE_LEN'] =        12;	// サイズの長さ
		$this->cont['TARLIB_HDR_MTIME_OFFSET'] =   136;	// 最終更新時刻のオフセット
		$this->cont['TARLIB_HDR_MTIME_LEN'] =       12;	// 最終更新時刻の長さ
		$this->cont['TARLIB_HDR_CHKSUM_OFFSET'] =  148;	// チェックサムのオフセット
		$this->cont['TARLIB_HDR_CHKSUM_LEN'] =       8;	// チェックサムの長さ
		$this->cont['TARLIB_HDR_TYPE_OFFSET'] =    156;	// ファイルタイプへのオフセット

		// Status
		$this->cont['TARLIB_STATUS_INIT'] =     0;		// 初期状態
		$this->cont['TARLIB_STATUS_OPEN'] =    10;		// 読み取り
		$this->cont['TARLIB_STATUS_CREATE'] =  20;		// 書き込み

		$this->cont['TARLIB_DATA_MODE'] =       '100666 ';	// ファイルパーミッション
		$this->cont['TARLIB_DATA_UGID'] =       '000000 ';	// uid / gid
		$this->cont['TARLIB_DATA_CHKBLANKS'] =  '        ';

		// GNU拡張仕様(ロングファイル名対応)
		$this->cont['TARLIB_DATA_LONGLINK'] =  '././@LongLink';

		// Type flag
		$this->cont['TARLIB_HDR_FILE'] =  '0';
		$this->cont['TARLIB_HDR_LINK'] =  'L';

		// Kind of the archive
		$this->cont['TARLIB_KIND_TGZ'] =  0;
		$this->cont['TARLIB_KIND_TAR'] =  1;

		// Prefix of tar
		$this->tar_prefix = 'tar_';
		$this->tar_de_prefix = 'tar_de_';

		// Regex(PCRE) of tar
		$this->tar_pregex    = '/^(tar_\d{8}[^.]*)(?:\.\d+?)?(?:of\d+?)?\.tar(?:\.gz)?$/i';
		// Regex(PCRE) of tar_de (decoded type)
		$this->tar_de_pregex = '/^(tar_de_\d{8}[^.]*)(?:\.\d+?)?(?:of\d+?)?\.tar(?:\.gz)?$/i';

		// Set data directorys
		$this->datadirs = array(
			'wiki' => $this->cont['DATA_DIR'],
			'attach' => $this->cont['UPLOAD_DIR'],
			'backup' => $this->cont['BACKUP_DIR'],
			'diff' => $this->cont['DIFF_DIR'],
			'trackback' => $this->cont['TRACKBACK_DIR']
		);
	}

	function format_extract_filter($fullpath, $filereg) {
		$path = ltrim(substr($fullpath, strlen($this->cont['DATA_HOME'])), '/');
		return '(?:' . preg_quote($this->cont['DATA_HOME'], '/') . ')?' . preg_quote($path, '/') . $filereg;
	}

	/////////////////////////////////////////////////
	// プラグイン本体
	function plugin_dump_action()
	{
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

		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits this');

		$this->func->add_tag_head('dump.css');

		$pass = isset($_POST['pass']) ? $_POST['pass'] : NULL;
		$act  = isset($this->root->vars['act'])   ? $this->root->vars['act']   : NULL;

		$body = '';

		$showForm = true;
		if ($this->root->userinfo['admin'] || $pass !== NULL) {
			if (! $this->func->pkwk_login($pass)) {
				$body = "<p><strong>{$this->msg['password_ng']}</strong></p>\n";
			} else {
				switch($act){
				case $this->cont['PLUGIN_DUMP_DUMP']:
					$body = $this->plugin_dump_download();
					$showForm = false;
					break;
				case $this->cont['PLUGIN_DUMP_RESTORE']:
					$filename = '';
					if (! empty($this->root->vars['localfile'])) {
						$localfile = $this->cont['CACHE_DIR'] . $this->root->vars['localfile'];
						if (is_file($localfile)) {
							$filename = $this->cont['CACHE_DIR'] . $this->root->vars['localfile'];
						}
					}
					if ($filename || (! empty($_FILES['upload_file']) && is_uploaded_file($_FILES['upload_file']['tmp_name']))) {
						$retcode = $this->plugin_dump_upload($filename);
						if ($retcode['code'] == TRUE) {
							$msg = $this->msg['upload_ok'] . $retcode['restore_of'];
						} else {
							$msg = $this->msg['upload_ng'];
						}
						$body .= $retcode['msg'];
						return array('msg' => $msg, 'body' => $body);
					}
					break;
				case 'maketar':
					error_reporting(0);
					$list = isset($this->root->vars['list']) ? $this->root->vars['list'] : '';
					$outimg = 'package_delete.png';
					if ($list) {
						if ($this->make_tar_by_list($list)) {
							$outimg = 'package_go.png';
						}
					}
					$url = $this->cont['LOADER_URL'] .'?src='.$outimg;

					// clear output buffer
					$this->func->clear_output_buffer();

					header('Location: ' . $url);

					return array('exit' => 0);	// 正常終了
					break;
				case 'download':
					error_reporting(0);
					$downfile = $this->cont['CACHE_DIR'] . $this->root->vars['file'];
					if ((preg_match($this->tar_pregex, $this->root->vars['file']) || preg_match($this->tar_de_pregex, $this->root->vars['file'])) && is_readable($downfile)) {
						$this->download_tarfile($downfile);
					} else {
						$this->func->redirect_header($this->cont['HOME_URL'] . '?cmd=dump',
							1,
							htmlspecialchars($this->root->vars['file']) . 'was not found.');
					}

					return array('exit' => 0);	// 正常終了

					break;
				}
			}
		}

		// 入力フォームを表示
		if ($showForm) $body .= $this->plugin_dump_disp_form();

		$msg = '';
		if ($this->cont['PLUGIN_DUMP_ALLOW_RESTORE']) {
			$msg = 'dump & restore';
		} else {
			$msg = 'dump';
		}

		return array('msg' => $msg, 'body' => $body);
	}

	function make_tar_by_list($list) {
		$ret = false;
		$list = $this->cont['CACHE_DIR'] . $list;
		// ダウンロード用ファイル
		$downfile = preg_replace('/.list$/i', '', $list);

		if (is_readable($list)) {
			$listdata = file_get_contents($list);
			//list($files, $vars) = explode("\x08", $listdata);
			//$files = explode("\n", $files);
			//$this->root->vars = array_merge_recursive($this->root->vars, $vars);
			$files = file($list);
			$files = array_map('trim', $files);
			if ($files) {
				@unlink($downfile);

				// アーカイブの種類
				$arc_kind = (substr($downfile, -3) === '.gz') ? 'tgz' : 'tar';

				// ページ名に変換する
				$namedecode = isset($this->root->vars['namedecode']) ? TRUE : FALSE;

				$tar = new XpWikitarlib($this->xpwiki);

				$tar->datadirs = $this->datadirs;

				$tar->create($this->cont['CACHE_DIR'], $arc_kind) or
					$this->func->die_message($this->msg['maketmp_ng']);

				// 文字コード cache/encode.txt
				$data = $this->cont['SOURCE_ENCODING'];
				$size = strlen($data);
				// ヘッダ生成
				$tar_data = $tar->_make_header('private/cache/.charset', $size, $this->cont['UTC'], $this->cont['TARLIB_HDR_FILE']);
				// ファイル出力
				$tar->_write_data(join('', $tar_data), $data, $size);

				$tar->add_dir($files, $namedecode);

				$tar->close();

				if (rename($tar->filename, $downfile)) {
					@unlink($list);
					$ret = true;
				}
			}
		} else if (is_file($downfile)) {
			$ret = true;
		}

		return $ret;
	}

	/////////////////////////////////////////////////
	// ファイルのダウンロード
	function plugin_dump_download()
	{
		// アーカイブの種類
		$arc_kind = ($this->root->vars['pcmd'] == 'tar') ? 'tar' : 'tgz';

		// ページ名に変換する
		$namedecode = isset($this->root->vars['namedecode']) ? TRUE : FALSE;

		// バックアップディレクトリ
		$bk_wiki   = isset($this->root->vars['bk_wiki'])   ? TRUE : FALSE;
		$bk_attach = isset($this->root->vars['bk_attach']) ? TRUE : FALSE;
		$bk_counter= (isset($this->root->vars['bk_counter']) && $this->root->vars['bk_counter'] === '1') ? TRUE : FALSE;
		$bk_backup = isset($this->root->vars['bk_backup']) ? TRUE : FALSE;
		$bk_diff   = isset($this->root->vars['bk_diff']) ? TRUE : FALSE;
		$bk_trackback= isset($this->root->vars['bk_trackback']) ? TRUE : FALSE;

		$bk_dbpginfo= isset($this->root->vars['bk_dbpginfo']) ? TRUE : FALSE;
		$bk_dbcount= (isset($this->root->vars['bk_counter']) && $this->root->vars['bk_counter'] === '2') ? TRUE : FALSE;
		$bk_dbrel= isset($this->root->vars['bk_dbrel']) ? TRUE : FALSE;
		$bk_dbplain= isset($this->root->vars['bk_dbplain']) ? TRUE : FALSE;
		$bk_dbattach= isset($this->root->vars['bk_dbattach']) ? TRUE : FALSE;

		$filecount = 0;
		$tar = new XpWikitarlib($this->xpwiki);

		$tar->limitSize = floatval($this->root->vars['limitsize']) * 1024 * 1024;
		$tar->datadirs = $this->datadirs;


		$tar->create($this->cont['CACHE_DIR'], $arc_kind, false) or
			$this->func->die_message($this->msg['maketmp_ng']);

		if ($bk_wiki)   $filecount += $tar->add_dir($this->cont['DATA_DIR'],   $this->root->_STORAGE['DATA_DIR']['add_filter'],   $namedecode);
		if ($bk_attach) $filecount += $tar->add_dir($this->cont['UPLOAD_DIR'], $this->root->_STORAGE['UPLOAD_DIR']['add_filter'], $namedecode);
//		if ($bk_counter)$filecount += $tar->add_dir($this->cont['COUNTER_DIR'],$this->root->_STORAGE['COUNTER_DIR']['add_filter'], $namedecode);
		if ($bk_counter)$filecount += $tar->add_dir('COUNTER'                 ,$this->root->_STORAGE['COUNTER_DIR']['add_filter'], $namedecode);
		if ($bk_backup) $filecount += $tar->add_dir($this->cont['BACKUP_DIR'], $this->root->_STORAGE['BACKUP_DIR']['add_filter'], $namedecode);
		if ($bk_diff)   $filecount += $tar->add_dir($this->cont['DIFF_DIR'],   $this->root->_STORAGE['DIFF_DIR']['add_filter'], $namedecode);
		if ($bk_trackback)$filecount += $tar->add_dir($this->cont['TRACKBACK_DIR'],   $this->root->_STORAGE['TRACKBACK_DIR']['add_filter'], $namedecode);

		if ($bk_dbpginfo) $filecount += $tar->add_sql($this->xpwiki->db->prefix($this->root->mydirname.'_pginfo'));
		if ($bk_dbcount) $filecount += $tar->add_sql($this->xpwiki->db->prefix($this->root->mydirname.'_count'));
		if ($bk_dbrel) $filecount += $tar->add_sql($this->xpwiki->db->prefix($this->root->mydirname.'_rel'));
		if ($bk_dbplain) $filecount += $tar->add_sql($this->xpwiki->db->prefix($this->root->mydirname.'_plain'));
		if ($bk_dbattach) $filecount += $tar->add_sql($this->xpwiki->db->prefix($this->root->mydirname.'_attach'));

		if ($filecount === 0) {
			//$tar->close();
			//@unlink($tar->filename);
			return '<p><strong>'.$this->msg['file_notfound'].'</strong></p>';
		} else {

			$filename = ($namedecode? $this->tar_de_prefix : $this->tar_prefix) . strftime('%Y%m%d', $this->cont['UTC']) . '_' . $this->root->mydirname . '-' . join('+', $tar->dirs);
			if ($arc_kind == 'tgz') {
				$ext= '.tar.gz';
			} else {
				$ext = '.tar';
			}
			$dirlistdata = join("\n", $tar->dirs);
			$downfile = $this->cont['CACHE_DIR'] . $filename;

			if ($handle = opendir($this->cont['CACHE_DIR'])) {
				while (false !== ($file = readdir($handle))) {
					if (strpos($file, $filename) === 0) {
						@unlink($this->cont['CACHE_DIR'] . $file);
					}
				}
			}

			$numlen = strlen($tar->moreCount);
			$format = '.%0'.$numlen.'dof%d';

			$single = false;
			$count = count($tar->moreFiles);
			if ($count === 1) {
				$single = true;
			}

			$image = '<img src="'.$this->cont['LOADER_URL'].'?src=package_go.png" alt="Download" />';
			$body = '<h4>' . $filename . '</h4>';
			$body .= '<p>' . str_replace('$image', $image, $this->msg['download_tars']) . '</p>';
			$body .= '<ul>';
			foreach($tar->moreFiles as $i => $files) {
				$num = $single? '' : sprintf($format, ($i + 1), $count);
				$downfile_tar = $downfile . $num . $ext;
				$downfile_list = $downfile_tar . '.list';
				$files[] = 'private/cache/'.$filename. $num . $ext . '.dirlist';
				file_put_contents($downfile_tar . '.dirlist', $dirlistdata);
				$listdata = join("\n", $files);
				file_put_contents($downfile_list, $listdata);
				$body .= '<li><div class="dump_loading"><a target="xpwiki_dump" href="'.$this->cont['HOME_URL'].'?cmd=dump&amp;act=download&amp;file='.rawurlencode(basename($downfile_tar)).'" title="Download">';
				$body .= '<img src="'.$this->cont['HOME_URL'].'gate.php?_nodos&amp;way=dump&amp;act=maketar&amp;list='.rawurlencode(basename($downfile_list)).'" alt="" /></a></div> ...'. $num . $ext . '</li>';
			}
			$body .= '</ul>';

			return $body;
		}
	}

	/////////////////////////////////////////////////
	// ファイルのアップロード
	function plugin_dump_upload($filename = '')
	{

		if (! $this->cont['PLUGIN_DUMP_ALLOW_RESTORE'])
			return array('code' => FALSE , 'msg' => 'Restoring function is not allowed');

		$isupload = false;
		if (! $filename) {
			$filename = $_FILES['upload_file']['name'];
			$isupload = true;
		}
		$matches  = array();
		$arc_kind = FALSE;
		if(! preg_match('/(\.tar|\.tar.gz|\.tgz)$/', $filename, $matches)){
			$this->func->die_message('Invalid file suffix');
		} else {
			$matches[1] = strtolower($matches[1]);
			switch ($matches[1]) {
			case '.tar':    $arc_kind = 'tar'; break;
			case '.tgz':    $arc_kind = 'tar'; break;
			case '.tar.gz': $arc_kind = 'tgz'; break;
			default: $this->func->die_message('Invalid file suffix: ' . $matches[1]); }
		}

		if ($isupload && $_FILES['upload_file']['size'] >  $this->cont['PLUGIN_DUMP_MAX_FILESIZE'] * 1024)
			$this->func->die_message('Max file size exceeded: ' . $this->cont['PLUGIN_DUMP_MAX_FILESIZE'] . 'KB');

		// Create a temporary tar file
		$tar = new XpWikitarlib($this->xpwiki);
		$tar->datadirs = $this->datadirs;
		if (! $isupload) {
			//$copy_check = copy($filename, $uploadfile);
			$uploadfile = $filename;
			$copy_check = true;
		} else {
			$uploadfile = tempnam(realpath($this->cont['CACHE_DIR']), 'tarlib_uploaded_');
			$copy_check = move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile);
		}
		if(! $copy_check ||
		   ! $tar->open($uploadfile, $arc_kind)) {
			if ($isupload) @unlink($uploadfile);
			$this->func->die_message($this->msg['file_notfound']);
		}

		$pattern = '(('. $this->root->_STORAGE['DATA_DIR']['extract_filter'] . ')|' .
		    '(' . $this->root->_STORAGE['UPLOAD_DIR']['extract_filter'] . ')|' .
		    '(' . $this->root->_STORAGE['COUNTER_DIR']['extract_filter'] . ')|' .
		    '(' . $this->root->_STORAGE['DIFF_DIR']['extract_filter'] . ')|' .
		    '(' . $this->root->_STORAGE['BACKUP_DIR']['extract_filter'] . ')|' .
		    '(' . $this->root->_STORAGE['TRACKBACK_DIR']['extract_filter'] . ')|' .
		    '(' . $this->root->_STORAGE['SQL_DUMP']['extract_filter'] . '))';
		$files = $tar->extract($pattern);
		$tar->close();

		if ($isupload) @unlink($uploadfile);

		if (empty($files['ok'])) {
			return array('code' => FALSE, 'msg' => '<p>'.$this->msg['tarfile_notfound'].'</p>');
		}

		$reqfile = $of = '';
		if (preg_match('#^(.+\.)(\d+)of(\d+)(\..+)$#', basename($filename), $match)) {
			$next = $match[2] + 1;
			$of = ' (' . $match[2] . ' of ' . $match[3] . ')';
			if ($next > $match[3]) {
				$reqfile = 'finish';
			} else {
				$numlen = strlen($match[3]);
				$next = sprintf('%0'.$numlen.'d', $next);
				$reqfile = $match[1] . $next . 'of' . $match[3] . $match[4];
			}
		}

		$map = array('wiki'=>'pginfo,rel,plain','counter'=>'count','attach'=>'attach');
		$sync = array();
		foreach(array_keys($files['dir']) as $dir) {
			if (isset($map[$dir])) {
				foreach(explode(',', $map[$dir]) as $table) {
					$sync[$table] = true;
				}
			}
		}

		if ($files['sql']) {
			$sqls = $this->restore_sql($files['sql']);
			$files = array_merge_recursive($files, $sqls);
		}

		foreach($files['sqltables'] as $table) {
			unset($sync[$table]);
		}

		if (isset($sync['rel']) && isset($sync['plain'])) {
			unset($sync['rel']);
		}

		$msg = '';
		if ($sync) {
			$msg .= '<div><strong>' . $this->msg['need_sync'] .'</strong>';
			$msg .= '<ul>';
			foreach(array_keys($sync) as $table) {
				$msg .= '<li>' . $this->msg['sync_'.$table] . '</li>';
			}
			$msg .= '</ul></div>';
		}

		$msg .= '<div class="dump_result">';
		$msg .= '<div><strong>'.$this->msg['error_filelist'].'</strong><ul>';
		if ($files['ng']) {
			foreach($files['ng'] as $type => $errors) {
				foreach($errors as $name) {
					$filename = basename($name);
					$filename = htmlspecialchars($tar->decode_filename($filename));
					$msg .= "<li><span class=\"dump_result_error\">$type: $name</span><br />( $filename )</li>\n";
				}
			}
		} else {
			$msg .= "<li>{$this->msg['file_notfound']}</li>\n";
		}
		$msg .= '</ul></div>';

		$msg  .= '<div><strong>'.$this->msg['filelist'].'</strong><ul>';
		if ($files['ok']) {
			foreach($files['ok'] as $name) {
				$msg .= "<li>$name</li>\n";
			}
		} else {
			$msg .= "<li>{$this->msg['file_notfound']}</li>\n";
		}
		$msg .= '</ul></div>';
		$msg .= '</div>';

		$msg .= $this->plugin_dump_disp_form($reqfile);

		return array('code' => TRUE, 'msg' => $msg, 'restore_of' => $of);
	}

	function restore_sql($sql_files) {
		$msg = array();
		foreach($sql_files as $file) {
			if (is_file($file)) {
				$sql = file_get_contents($file);
				$reps = array(
					'DROP TABLE IF EXISTS `',
					'CREATE TABLE IF EXISTS `',
					'CREATE TABLE `',
					'INSERT INTO `' );
				$to = array();
				$prefix = $this->xpwiki->db->prefix($this->root->mydirname.'_');
				foreach($reps as $from) {
					$to = $from . $prefix;
					$sql = preg_replace('/^'.preg_quote($from, '/').'/mi', $to, $sql);
				}
				$sql = preg_replace('/^--.*$/m', '', $sql);
				$sql = preg_replace('/[\r\n]+/', ' ', $sql);
				$sql = str_replace('\\\'', "\x07", $sql);
				$sql = preg_replace('/\'([^\']*?)\'/e', '"\'".str_replace(\';\', "\x08", \'\\1\')."\'"', $sql);
				$sql = str_replace("\x07", '\\\'', $sql);
				foreach(explode(';', $sql) as $query) {
					$query = trim(str_replace("\x08", ';', $query));
					if ($query) {
						if ($this->xpwiki->db->query($query)) {
							if (! empty($this->root->vars['show_sql'])) {
								$msg['ok'][] = htmlspecialchars($query);
							}
						} else {
							$msg['ng']['SQL Error'][] = '<span class="diff_removed">' . htmlspecialchars($query) . '</span>';
						}
					}
				}
				unlink($file);
			}
		}
		return $msg;
	}

	/////////////////////////////////////////////////
	// tarファイルのダウンロード
	function download_tarfile($downfile)
	{
		$size = filesize($downfile);
		$filename = basename($downfile);

		// clear output buffer
		$this->func->clear_output_buffer();

		ini_set('default_charset','');
		mb_http_output('pass');

		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . $size);
		header('Content-Type: application/x-tar');
		header('Pragma: no-cache');
		HypCommonFunc::readfile($downfile);
	}

	/////////////////////////////////////////////////
	// 入力フォームを表示
	function plugin_dump_disp_form($reqfile = '')
	{
		$act_down = $this->cont['PLUGIN_DUMP_DUMP'];
		$act_up   = $this->cont['PLUGIN_DUMP_RESTORE'];
		$maxsize  = $this->cont['PLUGIN_DUMP_MAX_FILESIZE'];

		$this->msg['max_filesize'] = str_replace('$maxsize', $maxsize, $this->msg['max_filesize']);

		$memory_limit = HypCommonFunc::return_bytes(ini_get('memory_limit'));
		if (function_exists('memory_get_usage')) {
			$memory_usage = memory_get_usage() * 1.1;
		} else {
			$memory_usage = 7 * 1024 * 1024;
		}

		if ($memory_limit) {
			$maxsize = max(0.1, min(20, (ceil(($memory_limit - $memory_usage) / 1024 / 1024 * 10) / 10)));
		} else {
			$maxsize = 5;
		}

		$fullpath = array();
		foreach($this->datadirs as $name => $path) {
			if (strpos($path, $this->cont['DATA_HOME']) !== 0) {
				$msg = str_replace('$1', $path, $this->msg['make_fullpath']);
				$fullpath[$name] = '&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="fullpath_'.$name.'" id="_p_dump_fullpath_'.$name.'"><label for="_p_dump_fullpath_'.$name.'"> ' . $msg . '</label><br />';
			} else {
				$fullpath[$name] = '';
			}
		}

		$passform = ($this->root->userinfo['admin'])? '' :
			'<label for="_p_dump_adminpass_dump"><strong>'.$this->msg['admin_pass'].'</strong></label>
  <input type="password" name="pass" id="_p_dump_adminpass_dump" size="12" />';

		if (extension_loaded('zlib')) {
			$tarop = <<<EOD
  <input type="radio" name="pcmd" id="_p_dump_tgz" value="tgz" checked="checked" />
  <label for="_p_dump_tgz">{$this->msg['tar.gz']}</label><br />
  <input type="radio" name="pcmd" id="_p_dump_tar" value="tar" />
  <label for="_p_dump_tar">{$this->msg['tar']}</label>
EOD;
		} else {
			$tarop = <<<EOD
  <input type="radio" name="pcmd" id="_p_dump_tar" value="tar" checked="checked" />
  <label for="_p_dump_tar">{$this->msg['tar']}</label>
EOD;
		}

		$script = $this->func->get_script_uri();

		$data = '';

		if (! $reqfile) {
			$data .= <<<EOD
<div class="level3">
<h3>{$this->msg['data_download']}</h3>
<form action="{$script}" method="post">
 <div>
  <input type="hidden" name="cmd"  value="dump" />
  <input type="hidden" name="act"  value="$act_down" />

<p><strong>{$this->msg['tar_type']}</strong>
<br />
{$tarop}
</p>

<p>
<strong>{$this->msg['maxsize']}: <input type="text" size="4" name="limitsize" value="{$maxsize}" style="text-align:right;" />MB</strong>
<br />
{$this->msg['maxsize_desc']}
</p>

<p><strong>{$this->msg['backup_dir']}</strong><br />
<br />
  <input type="checkbox" name="bk_wiki" id="_p_dump_d_wiki" checked="checked" />
  <label for="_p_dump_d_wiki">wiki</label><br />
  {$fullpath['wiki']}
  <input type="radio" name="bk_counter" value="1" id="_p_dump_d_counter" />
  <label for="_p_dump_d_counter">counter</label><br />
  <input type="checkbox" name="bk_attach" id="_p_dump_d_attach" checked="checked" />
  <label for="_p_dump_d_attach">attach</label><br />
  {$fullpath['attach']}
  <input type="checkbox" name="bk_backup" id="_p_dump_d_backup" checked="checked" />
  <label for="_p_dump_d_backup">backup</label><br />
  {$fullpath['backup']}
  <input type="checkbox" name="bk_diff" id="_p_dump_d_diff" checked="checked" />
  <label for="_p_dump_d_diff">diff</label><br />
  {$fullpath['diff']}
  <input type="checkbox" name="bk_trackback" id="_p_dump_d_trackback" checked="checked" />
  <label for="_p_dump_d_trackback">trackback</label><br />
  {$fullpath['trackback']}
</p>
<p><strong>{$this->msg['option']}</strong>
<br />
  <input type="checkbox" name="namedecode" id="_p_dump_namedecode" />
  <label for="_p_dump_namedecode">{$this->msg['decode_pagename']}</label><br />
</p>
<p><strong>{$this->msg['backup_table']}</strong><br />
<br />
  <input type="checkbox" name="bk_dbpginfo" id="_p_dump_d_dbinfo" checked="checked" />
  <label for="_p_dump_d_dbinfo">DB@pginfo</label><br />
  <input type="radio" name="bk_counter" value="2" id="_p_dump_d_dbcount" checked="checked" onmousedown="if (this.checked) this.checked = false;return false;" />
  <label for="_p_dump_d_dbcount" onmousedown="if (getElementById('_p_dump_d_dbcount').checked) getElementById('_p_dump_d_dbcount').checked = false;return false;">DB@count</label><br />
  <input type="checkbox" name="bk_dbattach" id="_p_dump_d_dbattach" checked="checked" />
  <label for="_p_dump_d_dbattach">DB@attach</label><br />
  <input type="checkbox" name="bk_dbplain" id="_p_dump_d_dbplain" checked="checked" />
  <label for="_p_dump_d_dbplain">DB@plain</label><br />
  <input type="checkbox" name="bk_dbrel" id="_p_dump_d_dbrel" checked="checked" />
  <label for="_p_dump_d_dbrel">DB@rel</label><br />
</p>
<p>$passform
  <input type="submit"   name="ok"   value="{$this->msg['do_download']}" />
  <br />
  {$this->msg['click_once']}
</p>
 </div>
</form>
</div>
EOD;
		}

		if($this->cont['PLUGIN_DUMP_ALLOW_RESTORE']) {
			$passform = ($this->root->userinfo['admin'])? '' :
				'<label for="_p_dump_adminpass_restore"><strong>'.$this->msg['admin_pass'].'</strong></label>
  <input type="password" name="pass" id="_p_dump_adminpass_restore" size="12" />';
			$script = $this->func->get_script_uri();
			$notice = '<p><strong>' . $this->msg['data_overwrite'] . '</strong></p>';
			if ($reqfile && $reqfile !== 'finish') {
				$notice = $reqfile;
				$restore_hint = '';
			} else {
				$reqfile = '';
				$notice = $this->msg['data_overwrite'];
				$restore_hint = ($this->cont['SOURCE_ENCODING'] === 'UTF-8')? '<p>'.$this->msg['restore_hint'].'</p>' : '';
			}
			$data .= <<<EOD
<form enctype="multipart/form-data" action="{$script}" method="post">
 <input type="hidden" name="cmd"  value="dump" />
 <input type="hidden" name="act"  value="$act_up" />
 <div class="level3">
  <h3>{$this->msg['data_restore']}</h3>
  <p><strong>{$notice}</strong></p>
  $restore_hint
  <div class="level4">
   <h4>{$this->msg['uplode_now']}</h4>
    <p>
     <span class="small">{$this->msg['max_filesize']}</span><br />
     <label for="_p_dump_upload_file">{$this->msg['file']}</label>
     <input type="file" name="upload_file" id="_p_dump_upload_file" size="40" />
    </p>
  </div>
EOD;
			// private/cache のファイル検索
			$tars = array();
			if ($handle = opendir($this->cont['CACHE_DIR'])) {
				while (false !== ($file = readdir($handle))) {
					if ($reqfile) {
						if ($file === $reqfile) {
							$tars[$file][] = $file;
							break;
						}
					} else {
						if (preg_match($this->tar_pregex, $file, $match)) {
							$tars[$match[1]][] = $file;
						}
					}
				}
			}
			$data .= <<<EOD
  <div class="level4">
   <h4>{$this->msg['uploded_ftp']}</h4>
   <p><strong>{$this->cont['CACHE_DIR']}</strong></p>
EOD;
			if ($tars) {
				$radio = '';
				$i = 0;
				krsort($tars);
				$image = '<img src="'.$this->cont['LOADER_URL'].'?src=package_go.png" alt="Download" />';
				$t_dl = ' title="Download"';
				foreach($tars as $name => $tars) {
					if (count($tars) === 1) {
						$tar = $tars[0];
						$tar_view = htmlspecialchars($tar);
						$checked = ($reqfile)? ' checked="checked"' : '';
						$fsize = filesize($this->cont['CACHE_DIR'].$tar);
						$radio .= '    <input type="radio" name="localfile" id="_p_dump_localfile'.$i.'" value="'.$tar_view.'"' . $checked . ' /><label for="_p_dump_localfile'.$i++.'"> '.$tar_view .' ( '. $this->func->bytes2KMT($fsize) . ' )</label>';
						$radio .= '    <a target="xpwiki_dump" href="'.$this->cont['HOME_URL'].'?cmd=dump&amp;act=download&amp;file='.rawurlencode($tar).'"'.$t_dl.'>'.$image.'</a><br />' . "\n";
					} else {
						natsort($tars);
						$radio .= '    <input type="radio" disabled="disabled" /> ' . htmlspecialchars($name) . '<br />';
						foreach($tars as $tar) {
							$tar_view = htmlspecialchars(substr($tar, strlen($name)));
							$fsize = filesize($this->cont['CACHE_DIR'].$tar);
							$radio .= '    &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="localfile" id="_p_dump_localfile'.$i.'" value="'.htmlspecialchars($tar).'" /><label for="_p_dump_localfile'.$i++.'"> '.$tar_view .' ( '. $this->func->bytes2KMT($fsize) . ' )</label>';
							$radio .= '    <a target="xpwiki_dump" href="'.$this->cont['HOME_URL'].'?cmd=dump&amp;act=download&amp;file='.rawurlencode($tar).'"'.$t_dl.'>'.$image.'</a><br />' . "\n";
						}
					}
				}

				$data .= <<<EOD
   <p>
$radio
   </p>
EOD;
			} else {
				$data .= <<<EOD
   <p>{$this->msg['file_notfound']}</p>
EOD;
			}
			$data .= <<<EOD

  </div>
  <p>
  <input type="submit" name="ok" value="{$this->msg['do_restore']}" />
  <input type="checkbox" name="show_sql" id="_p_dump_show_sql" value="1" /><label for="_p_dump_show_sql"> {$this->msg['show_sql']}</label>
  <br />
  {$this->msg['click_once']}
  </p>
 </div>
</form>
EOD;
		}
		return $data;
	}
}

class XpWikitarlib
{
	var $filename;
	var $fp;
	var $status;
	var $arc_kind;
	var $dummydata;
	var $dirs;

	var $isFull;
	var $totalSize;
	var $limitSize;
	var $moreFiles;
	var $moreCount;
	var $execution_time;
	var $datadirs;

	// コンストラクタ
	function XpWikitarlib(& $xpwiki) {
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;
		$this->filename = '';
		$this->fp       = FALSE;
		$this->status   = $this->cont['TARLIB_STATUS_INIT'];
		$this->arc_kind = $this->cont['TARLIB_KIND_TGZ'];
		$this->dirs     = array();

		$this->isFull = false;
		$this->totalSize = 0;
		$this->limitSize = 0;
		$this->moreFiles = array();
		$this->moreCount = 0;
		$this->datadirs = array();

		$this->execution_time = intval(ini_get('max_execution_time'));
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarファイルを作成する
	// 引数  : tarファイルを作成するパス
	// 返り値: TRUE .. 成功 , FALSE .. 失敗
	////////////////////////////////////////////////////////////
	function create($tempdir, $kind = 'tgz', $make = true)
	{
		$tempnam = '';
		if ($make) {
			$tempnam = tempnam(realpath($tempdir), 'tarlib_create_');
			if ($tempnam === FALSE) return FALSE;

			if ($kind == 'tgz') {
				$this->arc_kind = $this->cont['TARLIB_KIND_TGZ'];
				$this->fp       = gzopen($tempnam, 'wb');
			} else {
				$this->arc_kind = $this->cont['TARLIB_KIND_TAR'];
				$this->fp       = @fopen($tempnam, 'wb');
			}
		} else {
			$this->listmake = true;
		}

		if ($make && $this->fp === FALSE) {
			@unlink($tempnam);
			return FALSE;
		} else {
			$this->filename  = $tempnam;
			$this->dummydata = join('', array_fill(0, $this->cont['TARLIB_BLK_LEN'], "\0"));
			$this->status    = $this->cont['TARLIB_STATUS_CREATE'];
			if ($make) rewind($this->fp);
			return TRUE;
		}
	}

	function add_file($fullname, $decode, $data='', $fullpath = false) {
		if ($this->execution_time) {
			@set_time_limit($this->execution_time);
		}

		// DATA_HOME からの相対パスにする
		if (strpos($fullname, $this->cont['DATA_HOME']) === 0) {
			$name = ltrim(substr($fullname, strlen($this->cont['DATA_HOME'])), '/');
		} else {
			if ($fullpath) {
				$name = $fullname;
			} else {
				$dir= basename(dirname($fullname));
				if ($dir === 'attach') {
					$name = 'attach/' . basename($fullname);
				} else {
					$name = 'private/' . $dir . '/' . basename($fullname);
				}
			}
		}

		if (! $data && ! is_readable($fullname)) {
			@unlink($this->filename);
			$this->func->die_message($fullname . ' is not found or not readable.');
		}

		// ファイルサイズを取得
		if ($data) {
			$size = strlen($data);
		} else {
			$size = filesize($fullname);
		}
		$this->totalSize += $size;

		if ($this->limitSize && $this->limitSize < $this->totalSize) {
			$this->isFull = true;
			$this->moreCount++;
			$this->totalSize = $size;
		}
		if ($this->listmake || $this->isFull) {
			if ($data) {
				file_put_contents($fullname, $data);
			}
			$this->moreFiles[$this->moreCount][] = $name . "\t" . ($decode? '1' : '0') . "\t" . ($fullpath? '1' : '0');
		} else {
			// Tarに格納するファイル名をdecode
			if (! $decode) {
				$filename = $name;
			} else {
				$dirname  = dirname($name) . '/';
				$filename = basename(trim($name));
				$filename = $this->decode_filename($filename);
				// 危ないコードは置換しておく
				$replaces = array(
					':'  => '_',
					'\\' => '_'
				);
				$filename = strtr($filename, $replaces);

				$filename = $dirname . $filename;
				// ファイル名の文字コードを変換
				if (function_exists('mb_convert_encoding'))
					$filename = mb_convert_encoding($filename, $this->cont['PLUGIN_DUMP_FILENAME_ENCORDING'], $this->cont['SOURCE_ENCORDING']);
			}

			// 最終更新時刻
			if ($data) {
				$mtime = $this->cont['UTC'];
			} else {
				$mtime = filemtime($fullname);
			}

			// ファイル名長のチェック
			if (strlen($filename) > $this->cont['TARLIB_HDR_NAME_LEN']) {
				// LongLink対応
				$namesize = strlen($filename);
				// LongLinkヘッダ生成
				$tar_data = $this->_make_header($this->cont['TARLIB_DATA_LONGLINK'], $namesize, $mtime, $this->cont['TARLIB_HDR_LINK']);
				// ファイル出力
	 			$this->_write_data(join('', $tar_data), $filename, $namesize);
			}

			// ヘッダ生成
			$tar_data = $this->_make_header($filename, $size, $mtime, $this->cont['TARLIB_HDR_FILE']);

			// ファイルデータの取得
			if (! $data) {
				$data = file_get_contents($fullname);
				if (substr($name, -4) === '.sql' || substr($name, -5) === '.count' || substr($name, -8) === '.dirlist') {
					@unlink($fullname);
				}
			}

			// ファイル出力
			$this->_write_data(join('', $tar_data), $data, $size);
		}
		return true;
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarファイルにディレクトリを追加する
	// 引数  : $dir    .. ディレクトリ名
	//         $mask   .. 追加するファイル(正規表現)
	//         $decode .. ページ名の変換をするか
	// 返り値: 作成したファイル数
	////////////////////////////////////////////////////////////
	function add_dir($dir, $mask, $decode = FALSE)
	{
		if (! is_array($dir)) {
			if ($dir === 'COUNTER') return $this->add_dir_counter($decode);
			$this->dirs[] = basename($dir);

			$retvalue = 0;

			if ($this->status != $this->cont['TARLIB_STATUS_CREATE'])
				return ''; // File is not created

			$files = array();

			//  指定されたパスのファイルのリストを取得する
			$dp = @opendir($dir);
			if($dp === FALSE) {
				@unlink($this->filename);
				$this->func->die_message($dir . ' is not found or not readable.');
			}

			while ($filename = readdir($dp)) {
				if (preg_match("/$mask/", $filename))
					$files[] = $dir . $filename;
			}
			closedir($dp);

			$fullpath = 0;
			foreach($this->datadirs as $name => $_dir) {
				if ($dir === $_dir) {
					$fullpath = (! empty($this->root->vars['fullpath_'.$name]));
					break;
				}
			}
		} else {
			$files = $dir;
		}

		sort($files);

		$matches = array();
		foreach($files as $name)
		{
			++$retvalue;
			if (strpos($name, "\t") !== false) {
				list($name, $decode, $fullpath) = explode("\t", $name);
			}
			$this->add_file($name, $decode, '', $fullpath);
		}
		return $retvalue;
	}
	function add_dir_counter($decode = FALSE)
	{
		$this->dirs[] = 'counter';
		$retvalue = 0;

		if ($this->status != $this->cont['TARLIB_STATUS_CREATE'])
			return ''; // File is not created

		$files = array();

		$db = $this->xpwiki->db;
		$sql = 'SELECT * FROM '.$db->prefix($this->root->mydirname."_count");
		if (! $result = $db->query($sql)) {
			$this->func->die_message('Database table "count" is not found or not readable.');
		}

		// counter
		if (! $this->func->get_plugin_instance('counter')) {
			$this->func->die_message('"counter plugin" was not found.');
		}

		while ($arr = $db->fetchRow($result)) {
			$pgid = array_shift($arr);
			$page = $this->func->get_name_by_pgid($pgid);
			$data = join("\n", $arr) . "\n";
			$fname = $this->func->encode($page) . $this->cont['PLUGIN_COUNTER_SUFFIX'];
			$files[$fname] = $data;
		}

		ksort($files);

		$matches = array();
		$dirname = $this->cont['COUNTER_DIR'];
		$counter_ext = preg_quote($this->cont['PLUGIN_COUNTER_SUFFIX']);
		$pattern = '^((?:[0-9A-F]{2})+)(('.$counter_ext.')*)$';
		foreach($files as $name => $data)
		{
			$name =  $dirname . $name;
			$this->add_file($name, $decode, $data);
			++$retvalue;
		}
		return $retvalue;
	}
	function add_sql($table)
	{

		if ($this->status != $this->cont['TARLIB_STATUS_CREATE'])
			return ''; // File is not created

		if (! HypCommonFunc::loadClass('MySQLDump')) {
			$this->func->die_message('Class "MySQLDump" was not found.');
		}

		$removePrefix = $this->xpwiki->db->prefix($this->root->mydirname.'_');
		$short_name = substr($table, strlen($removePrefix));
		$this->dirs[] = 'DB@' . $short_name;

		$name =  $this->cont['CACHE_DIR'] . $short_name . '.sql';
		$dumper = new MySQLDump(null, $name, false, false);

		$dumper->removePrefix = $removePrefix;
		$dumper->maxFileSize = $this->limitSize;
		//$dumper->maxFileSize = 1048576; //1M

		// ToDo fix notice error of "MySQLDump" class.
		$_error_level = error_reporting(0);
		$dumper->doDump($table);
		error_reporting($_error_level);
		foreach($dumper->sqlFiles as $name) {
			$this->add_file($name, false, '');
		}

		return 1;
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarのヘッダ情報を生成する (add)
	// 引数  : $filename .. ファイル名
	//         $size     .. データサイズ
	//         $mtime    .. 最終更新日
	//         $typeflag .. TypeFlag (file/link)
	// 戻り値: tarヘッダ情報
	////////////////////////////////////////////////////////////
	function _make_header($filename, $size, $mtime, $typeflag)
	{
		$tar_data = array_fill(0, $this->cont['TARLIB_HDR_LEN'], "\0");

		// ファイル名を保存
		for($i = 0; $i < strlen($filename); $i++ ) {
			if ($i < $this->cont['TARLIB_HDR_NAME_LEN']) {
				$tar_data[$i + $this->cont['TARLIB_HDR_NAME_OFFSET']] = $filename{$i};
			} else {
				break;	// ファイル名が長すぎ
			}
		}

		// mode
		$modeid = $this->cont['TARLIB_DATA_MODE'];
		for($i = 0; $i < strlen($modeid); $i++ ) {
			$tar_data[$i + $this->cont['TARLIB_HDR_MODE_OFFSET']] = $modeid{$i};
		}

		// uid / gid
		$ugid = $this->cont['TARLIB_DATA_UGID'];
		for($i = 0; $i < strlen($ugid); $i++ ) {
			$tar_data[$i + $this->cont['TARLIB_HDR_UID_OFFSET']] = $ugid{$i};
			$tar_data[$i + $this->cont['TARLIB_HDR_GID_OFFSET']] = $ugid{$i};
		}

		// サイズ
		$strsize = sprintf('%11o', $size);
		for($i = 0; $i < strlen($strsize); $i++ ) {
			$tar_data[$i + $this->cont['TARLIB_HDR_SIZE_OFFSET']] = $strsize{$i};
		}

		// 最終更新時刻
		$strmtime = sprintf('%o', $mtime);
		for($i = 0; $i < strlen($strmtime); $i++ ) {
			$tar_data[$i + $this->cont['TARLIB_HDR_MTIME_OFFSET']] = $strmtime{$i};
		}

		// チェックサム計算用のブランクを設定
		$chkblanks = $this->cont['TARLIB_DATA_CHKBLANKS'];
		for($i = 0; $i < strlen($chkblanks); $i++ ) {
			$tar_data[$i + $this->cont['TARLIB_HDR_CHKSUM_OFFSET']] = $chkblanks{$i};
		}

		// タイプフラグ
		$tar_data[$this->cont['TARLIB_HDR_TYPE_OFFSET']] = $typeflag;

		// チェックサムの計算
		$sum = 0;
		for($i = 0; $i < $this->cont['TARLIB_BLK_LEN']; $i++ ) {
			$sum += 0xff & ord($tar_data[$i]);
		}
		$strchksum = sprintf('%7o',$sum);
		for($i = 0; $i < strlen($strchksum); $i++ ) {
			$tar_data[$i + $this->cont['TARLIB_HDR_CHKSUM_OFFSET']] = $strchksum{$i};
		}

		return $tar_data;
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarデータのファイル出力 (add)
	// 引数  : $header .. tarヘッダ情報
	//         $body   .. tarデータ
	//         $size   .. データサイズ
	// 戻り値: なし
	////////////////////////////////////////////////////////////
	function _write_data($header, $body, $size)
	{
		$fixsize  = ceil($size / $this->cont['TARLIB_BLK_LEN']) * $this->cont['TARLIB_BLK_LEN'] - $size;

		if ($this->arc_kind == $this->cont['TARLIB_KIND_TGZ']) {
			gzwrite($this->fp, $header, $this->cont['TARLIB_HDR_LEN']);    // Header
			gzwrite($this->fp, $body, $size);               // Body
			gzwrite($this->fp, $this->dummydata, $fixsize); // Padding
		} else {
			 fwrite($this->fp, $header, $this->cont['TARLIB_HDR_LEN']);    // Header
			 fwrite($this->fp, $body, $size);               // Body
			 fwrite($this->fp, $this->dummydata, $fixsize); // Padding
		}
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarファイルを開く
	// 引数  : tarファイル名
	// 返り値: TRUE .. 成功 , FALSE .. 失敗
	////////////////////////////////////////////////////////////
	function open($name = '', $kind = 'tgz')
	{
		if (! $this->cont['PLUGIN_DUMP_ALLOW_RESTORE']) return FALSE; // Not allowed

		if ($name != '') $this->filename = $name;

		if ($kind == 'tgz') {
			$this->arc_kind = $this->cont['TARLIB_KIND_TGZ'];
			$this->fp = gzopen($this->filename, 'rb');
		} else {
			$this->arc_kind = $this->cont['TARLIB_KIND_TAR'];
			$this->fp =  fopen($this->filename, 'rb');
		}

		if ($this->fp === FALSE) {
			return FALSE;	// No such file
		} else {
			$this->status = $this->cont['TARLIB_STATUS_OPEN'];
			rewind($this->fp);
			return TRUE;
		}
	}

	////////////////////////////////////////////////////////////
	// 関数  : 指定したディレクトリにtarファイルを展開する
	// 引数  : 展開するファイルパターン(正規表現)
	// 返り値: 展開したファイル名の一覧
	// 補足  : ARAIさんのattachプラグインパッチを参考にしました
	////////////////////////////////////////////////////////////
	function extract($pattern)
	{
		if ($this->status != $this->cont['TARLIB_STATUS_OPEN']) return ''; // Not opened

		$files = array('ok'=>array(),'ng'=>array(),'dir'=>array(),'sql'=>array(),'sqltables'=>array());
		$longname = '';
		$charset = '';

		while(1) {
			$buff = fread($this->fp, $this->cont['TARLIB_HDR_LEN']);
			if (strlen($buff) != $this->cont['TARLIB_HDR_LEN']) break;

			// ファイル名
			$name = '';
			if ($longname != '') {
				$name     = $longname;	// LongLink対応
				$longname = '';
			} else {
				for ($i = 0; $i < $this->cont['TARLIB_HDR_NAME_LEN']; $i++ ) {
					if ($buff{$i + $this->cont['TARLIB_HDR_NAME_OFFSET']} != "\0") {
						$name .= $buff{$i + $this->cont['TARLIB_HDR_NAME_OFFSET']};
					} else {
						break;
					}
				}
			}
			$name = trim($name);

			if ($name == '') break;	// 展開終了

			// チェックサムを取得しつつ、ブランクに置換していく
			$checksum = '';
			$chkblanks = $this->cont['TARLIB_DATA_CHKBLANKS'];
			for ($i = 0; $i < $this->cont['TARLIB_HDR_CHKSUM_LEN']; $i++ ) {
				$checksum .= $buff{$i + $this->cont['TARLIB_HDR_CHKSUM_OFFSET']};
				$buff{$i + $this->cont['TARLIB_HDR_CHKSUM_OFFSET']} = $chkblanks{$i};
			}
			list($checksum) = sscanf('0' . trim($checksum), '%i');

			// Compute checksum
			$sum = 0;
			for($i = 0; $i < $this->cont['TARLIB_BLK_LEN']; $i++ ) {
				$sum += 0xff & ord($buff{$i});
			}
			if ($sum != $checksum) break; // Error

			// Size
			$size = '';
			for ($i = 0; $i < $this->cont['TARLIB_HDR_SIZE_LEN']; $i++ ) {
				$size .= $buff{$i + $this->cont['TARLIB_HDR_SIZE_OFFSET']};
			}
			list($size) = sscanf('0' . trim($size), '%i');

			// ceil
			// データブロックは512byteでパディングされている
			$pdsz = ceil($size / $this->cont['TARLIB_BLK_LEN']) * $this->cont['TARLIB_BLK_LEN'];

			// 最終更新時刻
			$strmtime = '';
			for ($i = 0; $i < $this->cont['TARLIB_HDR_MTIME_LEN']; $i++ ) {
				$strmtime .= $buff{$i + $this->cont['TARLIB_HDR_MTIME_OFFSET']};
			}
			list($mtime) = sscanf('0' . trim($strmtime), '%i');

			// タイプフラグ
//			 $type = $buff{TARLIB_HDR_TYPE_OFFSET};

			if ($name == $this->cont['TARLIB_DATA_LONGLINK']) {
				// LongLink
				$buff     = fread($this->fp, $pdsz);
				$longname = substr($buff, 0, $size);
			} else if (preg_match("/$pattern/", $name) ) {
//			} else if ($type == 0 && preg_match("/$pattern/", $name) ) {

				$parts = explode('.', $name);
				$extention = array_pop($parts);

				// 相対パスの場合の処理
				$name = '/' . ltrim($name, '/');
				if (! preg_match('/^' . preg_quote($this->cont['DATA_HOME'], '/') . '/', $name)) {
					$name = $this->cont['DATA_HOME'] . ltrim($name, '/');
				}

				//$files[] = '<span class="diff_removed">Debug: ' . $name . '</span>';

				$buff = fread($this->fp, $pdsz);

				if ($charset && strtoupper($charset) !== strtoupper($this->cont['SOURCE_ENCODING'])) {
					// ファイル名変換
					$dirname = dirname($name);
					$filename = basename($name);
					if (preg_match("/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)(\.log)?$/", $filename, $matches)) {
						// attachファイル名
						$page = $this->func->decode($matches[1]);
						$attach = $this->func->decode($matches[2]);
						$ext = (!empty($matches[3]))? $matches[3] : '';

						$page = mb_convert_encoding($page, $this->cont['SOURCE_ENCODING'], $charset);
						$attach = mb_convert_encoding($attach, $this->cont['SOURCE_ENCODING'], $charset);

						$filename = $this->func->encode($page) . '_' . $this->func->encode($attach) . $ext;
						$name = $dirname . '/' . $filename;
					} else {
						if (preg_match('/^((?:[0-9A-F]{2})+)(\.txt|\.gz|\.ref)$/', $filename, $matches)) {
							$page = $this->func->decode($matches[1]);
							$page = mb_convert_encoding($page, $this->cont['SOURCE_ENCODING'], $charset);
							$filename = $this->func->encode($page) . $matches[2];;
							$name = $dirname . '/' . $filename;
						}
					}

					if (in_array($extention, array('txt','log','sql','add'))) {
						// ファイル内容変換
						$buff = mb_convert_encoding($buff, $this->cont['SOURCE_ENCODING'], $charset);
						$size = strlen($buff);
					} else if ($extention === 'gz') {
						$gz_tmp = $this->cont['CACHE_DIR'].'dump_tmp.gz';
						file_put_contents($gz_tmp, $buff);
						$buff = join('', gzfile($gz_tmp));
						$buff = mb_convert_encoding($buff, $this->cont['SOURCE_ENCODING'], $charset);
						$fp = gzopen($gz_tmp, 'wb');
						gzputs($fp, $buff);
						gzclose($fp);
						$buff = file_get_contents($gz_tmp);
						$size = filesize($gz_tmp);
						unlink($gz_tmp);
					}
				}

				$shortname = substr($name, strlen($this->cont['DATA_HOME']));
				// 既に同じファイルがある場合は上書きされる
				$fpw = @fopen($name, 'wb');
				if ($fpw !== FALSE) {
					flock($fpw, LOCK_EX);
					fwrite($fpw, $buff, $size);
					@chmod($name, 0666);
					@touch($name, $mtime);
					flock($fpw, LOCK_UN);
					fclose($fpw);
					$files['ok'][] = $shortname;
					$files['dir'][basename(dirname($name))] = true;
					if (substr($name, -4) === '.sql') {
						$files['sql'][] = $name;
						//list($files['sqltables'][]) = explode('.', $name);
						$files['sqltables'][] = preg_replace('#^(.+)\d*\.sql$#', '$1', $name);
					}
				} else {
					$files['ng']['Copy Error'][] = $shortname;
				}
			} else if (basename($name) === '.charset') {
				$charset = trim(fread($this->fp, $pdsz));
			} else if (substr($name, -8) === '.dirlist') {
				$dirlist = trim(fread($this->fp, $pdsz));
				$dirlist = explode("\n", $dirlist);
				foreach($dirlist as $dir) {
					$dir = trim($dir);
					if (strpos($dir, '@') === false) {
						$files['dir'][$dir] = true;
					} else {
						list(,$files['sqltables'][]) = explode('@', $dir);
					}
				}
			} else {
				// ファイルポインタを進める
				@fseek($this->fp, $pdsz, SEEK_CUR);
				$files['ng']['Bypass'][] = $name;
			}
		}
		return $files;
	}

	////////////////////////////////////////////////////////////
	// 関数  : tarファイルを閉じる
	// 引数  : なし
	// 返り値: なし
	////////////////////////////////////////////////////////////
	function close()
	{
		if ($this->status == $this->cont['TARLIB_STATUS_CREATE']) {
			// ファイルを閉じる
			if ($this->arc_kind == $this->cont['TARLIB_KIND_TGZ']) {
				// バイナリーゼロを1024バイト出力
				gzwrite($this->fp, $this->dummydata, $this->cont['TARLIB_HDR_LEN']);
				gzwrite($this->fp, $this->dummydata, $this->cont['TARLIB_HDR_LEN']);
				gzclose($this->fp);
			} else {
				// バイナリーゼロを1024バイト出力
				fwrite($this->fp, $this->dummydata, $this->cont['TARLIB_HDR_LEN']);
				fwrite($this->fp, $this->dummydata, $this->cont['TARLIB_HDR_LEN']);
				fclose($this->fp);
			}
		} else if ($this->status == $this->cont['TARLIB_STATUS_OPEN']) {
			if ($this->arc_kind == $this->cont['TARLIB_KIND_TGZ']) {
				gzclose($this->fp);
			} else {
				 fclose($this->fp);
			}
		}

		$this->status = $this->cont['TARLIB_STATUS_INIT'];
	}

	function decode_filename($filename) {
		if (preg_match("/^((?:[0-9A-F]{2})*)_((?:[0-9A-F]{2})*)(\.log)?$/", $filename, $matches)) {
			// attachファイル名
			$filename = $this->func->decode($matches[1]) . '/' . $this->func->decode($matches[2]) . (isset($matches[3])? $matches[3] : '');
		} else {
			$pattern = '^((?:[0-9A-F]{2})+)(\.txt|\.gz|\.ref)$';
			if (preg_match("/$pattern/", $filename, $matches)) {
				$filename = $this->func->decode($matches[1]) . $matches[2];
			}
		}
		return $filename;
	}
}
?>