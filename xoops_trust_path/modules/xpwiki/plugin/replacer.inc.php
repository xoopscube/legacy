<?php
/*
 * Created on 2008/03/25 by nao-pon http://hypweb.net/
 * $Id: replacer.inc.php,v 1.5 2011/11/26 12:03:10 nao-pon Exp $
 */

class xpwiki_plugin_replacer extends xpwiki_plugin {
	
	function plugin_replacer_init () {
		$this->config['defaultVars'] = array(
			'pcmd'    => '',
			'spage'   => '',
			'regpage' => 0,
			'sword'   => '',
			'rword'   => '',
			'reg'     => 1,
			'ci'      => 1,
			'ml'      => 0,
			'um'      => 1,
			'nt'      => 1,
			'nn'      => 1,
			'max'     => 10,
		);
		$this->config['doneCache'] = $this->cont['CACHE_DIR'] . 'plugin/done.replacer';
	}
	
	function plugin_replacer_action () {
		
		// 権限チェック
		if (!$this->root->userinfo['admin']) {
			return $this->action_msg_admin_only();
		}
		// 管理画面モード指定
		if ($this->root->module['platform'] == "xoops") {
			$this->root->runmode = "xoops_admin";
		}

		$this->load_language();

		$this->set_vars();
		$body = '';
		switch ($this->vars['pcmd']) {
			case 'test':
				$this->doit();
				$body = $this->make_form();
				$body .= $this->make_do_btn();
				$body .= $this->result;
				break;
			case 'do':
				$this->doit();
				$body = $this->result;
				break;
			default:
				$body = $this->make_form();
		}
		
		return array('msg'=>$this->msg['title'], 'body'=>$body);
	}
	
//	function plugin_replacer_convert () {
//	}
	
	function set_vars() {
		$this->vars = array(
			'pcmd' => '',
			'spage' => '',
			'regpage' => 0,
			'sword' => '',
			'rword' => '',
			'reg' => 1,
			'ci' => 0,
			'ml' => 0,
			'um' => 0,
			'nt' => 0,
			'nn' => 0,
			'max' => 1,
		);
		foreach ($this->vars as $key =>$val){
			if (isset($this->root->post[$key])) {
				$this->vars[$key] = $this->root->post[$key];
			}
		}
		if (!$this->vars['pcmd']) {
			$this->vars = array_merge($this->vars, $this->config['defaultVars']);
		}
	}
	
	function make_form() {
		$this->clear_done();
		
		$script = $this->func->get_script_uri();
		
		$spage = htmlspecialchars($this->vars['spage']);
		
		$regpage = array_pad(array(), 3, '');
		if ($this->vars['regpage'] > 2) $this->vars['regpage'] = 0;
		$regpage[(int)$this->vars['regpage']] = ' checked="checked"';
		
		$sword = htmlspecialchars($this->vars['sword']);
		$rword = htmlspecialchars($this->vars['rword']);
		
		$reg = array_pad(array(), 3, '');
		if (!$this->vars['reg'] || $this->vars['reg']  > 2) $this->vars['reg'] = 0;
		$reg[(int)$this->vars['reg']] = ' checked="checked"';
		
		$ci = ($this->vars['ci'])? ' checked="checked"' : '';
		$ml = ($this->vars['ml'])? ' checked="checked"' : '';
		$um = ($this->vars['um'])? ' checked="checked"' : '';
		
		$um = ($this->cont['SOURCE_ENCODING'] === 'UTF-8')? '<br /><input type="checkbox" id="um" name="um" value="1"'.$um.' /><label for="um"> '.$this->msg['utf8Mode'].'</label>' : '<input type="hidden" name="um" value="0" />';
		
		$nt = ($this->vars['nt'])? ' checked="checked"' : '';
		$nn = ($this->vars['nn'])? ' checked="checked"' : '';
		
		$max = max(1, min(100, intval($this->vars['max'])));
		
		$form =<<<EOD
<form method="POST" action="{$script}">
<p>
<label for="spage">{$this->msg['targetPage']}</label>: <input type="text" size="60" id="spage" name="spage" value="{$spage}" />
</p>
<p>
<input type="radio" id="regpage0" name="regpage" value="0"{$regpage[0]} /><label for="regpage0"> {$this->msg['allPages']}</label>&nbsp;&nbsp;
<input type="radio" id="regpage1" name="regpage" value="1"{$regpage[1]} /><label for="regpage1"> {$this->msg['partMatch']}</label>&nbsp;&nbsp;
<input type="radio" id="regpage2" name="regpage" value="2"{$regpage[2]} /><label for="regpage2"> {$this->msg['regex']}</label>
</p>
<p>
<label for="sword">{$this->msg['searchPhrase']}</label>: <input type="text" size="60" id="sword" name="sword" value="{$sword}" />
<br />
<label for="rword">{$this->msg['replacePhrase']}</label>: <input type="text" size="60" id="rword" name="rword" value="{$rword}" />
</p>
<p>
<input type="radio" id="reg1" name="reg" value="1"{$reg[1]} /><label for="reg1"> {$this->msg['normalReplace']}</label>&nbsp;&nbsp;
<input type="radio" id="reg2" name="reg" value="2"{$reg[2]} /><label for="reg2"> {$this->msg['regex']}</label>
</p>
<p>
<input type="checkbox" id="ci" name="ci" value="1"{$ci} /><label for="ci"> {$this->msg['caseInsensitive']}</label>
<br />
<input type="checkbox" id="ml" name="ml" value="1"{$ml} /><label for="ml"> {$this->msg['multiLineMode']}</label>
{$um}
</p>
<p>
<input type="checkbox" id="nt" name="nt" value="1"{$nt} /><label for="nt"> {$this->msg['noTimeStamp']}</label>
<br />
<input type="checkbox" id="nn" name="nn" value="1"{$nn} /><label for="nn"> {$this->msg['noNotification']}</label>
</p>
<p>
<label for="max">{$this->msg['doMax']}</label>: <input type="text" size="5" id="max" name="max" value="{$max}" />
</p>
<p><input type="submit" name="" value="{$this->msg['doTest']}" /></p>
<input type="hidden" name="plugin" value="replacer" />
<input type="hidden" name="pcmd" value="test" />
</form>
EOD;
		return $form;
	}
	
	function make_do_btn() {
		$doform = '';
		if ($this->found) {
			$script = $this->func->get_script_uri();
			$doform = '<hr /><form method="POST" action="'.$script.'" onsubmit="return confirm(\''.$this->msg['areYouSure'].'\')">';
			$vars = $this->vars;
			$vars['pcmd'] = 'do';
			foreach($vars as $key => $val) {
				$doform .= '<input type="hidden" name="'.$key.'" value="'.htmlspecialchars($val).'">';
			}
			$doform .= <<<EOD
<p><input type="submit" name="" value="{$this->msg['doReplace']}" /></p>
<input type="hidden" name="plugin" value="replacer" />
</form>
EOD;
		}
		return $doform;
	}
	
	function doit() {
		$this->result = '';
		$this->found = 0;
				
		if (! $this->vars['sword']) {
			$this->result = $this->msg['notSearchPhrase'];
			return;
		}
		
		if ($this->vars['regpage']) {
			if ($this->vars['regpage'] == 2) {
				$regpage = '/' . str_replace('/', '\\/', $this->vars['spage']) . '/';
				if ($this->root->page_case_insensitive) {
					$regpage .= 'i';
				}
				if ($this->cont['SOURCE_ENCODING'] === 'UTF-8' && $this->vars['um']) {
					$regpage .= 'u';
				}
				if ($err = $this->get_err_regex($regpage, $this->msg['badSearchPages'])) {
					$this->result = $err;
					return;
				}
			} else {
				$regpage = '/' . preg_quote($this->vars['spage'], '/') . '/';
			}
		} else {
			$regpage = '';
		}

		if ($this->vars['reg'] == 2) {
			$reg = '/' . str_replace('/', '\\/', $this->vars['sword']) . '/';
		} else {
			$reg = '/' . preg_quote($this->vars['sword'], '/') . '/';
		}
		if ($this->vars['ci']) {
			$reg .= 'i';
		}
		if ($this->vars['ml']) {
			$reg .= 'm';
		}
		if ($this->cont['SOURCE_ENCODING'] === 'UTF-8' && $this->vars['um']) {
			$reg .= 'u';
		}

		if ($err = $this->get_err_regex($reg, $this->msg['badSearchPhrase'])) {
			$this->result = $err;
			return;
		}

		$rep = $this->vars['rword'];
		$rep = str_replace(array('\\\\n', '\\\\t', '\\n', '\\t'), array('\\'."\x08".'n', '\\'."\x08".'t', "\n", "\t"), $rep);
		$rep = str_replace("\x08", '', $rep);
		
		$base = $this->cont['DATA_DIR'];
		$ret = array();
		$max = max(1, min(100, intval($this->vars['max'])));

		if ($this->vars['pcmd'] === 'do') {
			$this->root->rtf['no_checkauth_on_write'] = true;
			$this->root->rtf['force_backup'] = true;
			$this->root->notify = 0;
			if ($this->vars['nn']) {
				$this->root->rtf['no_system_notification'] = true;
			}
		}

		if ($dh = opendir($base)) {
			while (($file = readdir($dh)) !== false && $max > $this->found) {
				if (preg_match('/^([a-f0-9]+)\.txt$/i', $file, $match)) {
					$page = $this->func->decode($match[1]);
					if ($this->check_done($page) || ($regpage && ! preg_match($regpage, $page))) {
						continue;
					}
					$src = file_get_contents($base . $file);
					$src = $this->func->remove_pginfo($src);
					if (preg_match_all($reg, $src, $target, PREG_PATTERN_ORDER)) {
						$this->found++;
						$ret[$page]['src'] = $src;
						$ret[$page]['from'] = array();
						$ret[$page]['to'] = array();
						foreach ($target[0] as $part1) {
							if ($this->vars['ci']) {
								$part1 = strtolower($part1);
							}
							if (! in_array(trim($part1), $ret[$page]['from'])) {
								$part2 = preg_replace($reg, $rep, $part1);
								$ret[$page]['from'][] = trim($part1);
								$ret[$page]['to'][] = trim($part2);
							}
						}
						$ret[$page]['src'] = $src;
						$src = preg_replace($reg, $rep, $src);
						$ret[$page]['result'] = $src;
						if ($this->vars['pcmd'] === 'do') {
							$this->func->page_write($page, $src, $this->vars['nt']);
							$this->save_done($page);
						}
					}
				}
			}
			closedir($dh);
		}
		if ($this->found) {
			if ($this->vars['pcmd'] === 'test') {
				$retstr = '<ul>';
				foreach($ret as $page => $arr) {
					$retstr .= '<li><span style="font-size:150%;">' . $this->func->make_pagelink($page, $page) . '</span>';
					$retstr .= '<ul style="font-size:130%;">';
					foreach($arr['from'] as $i => $from) {
						$retstr .= '<li>' . htmlspecialchars($from) . ' &#8658; ' .htmlspecialchars($arr['to'][$i]) . '</li>';
					}
					$retstr .= '</ul>';
					$retstr .= $this->func->compare_diff($arr['src'], $arr['result']);
					$retstr .= '<hr class="short_line" /></li>';
				}
				$retstr .= '</ul>';
			} else {
				$retstr = '<p>' . sprintf($this->msg['replaceDone'], $this->found) . '</p>';
				$this->vars['pcmd'] = 'test';
				$this->check_done(FALSE);
				$this->doit();
				if ($this->found) {
					$retstr .= '<p>' . $this->msg['replaceNext'] . '</p>';
					$retstr .= $this->make_do_btn();
					$retstr .= $this->result;
				} else {
					$this->clear_done();
					$retstr .= $this->make_goFirst();
				}
			}
		} else {
			$retstr = '<p>' . $this->msg['notFound'] . '</p>';
			$retstr .= $this->make_goFirst();
		}
		$this->result = $retstr;
		return;
	}
	
	function make_goFirst() {
		return '<p><a href="' . $this->root->script . '?cmd=replacer#'.$this->root->mydirname.'_header">' . $this->msg['goFirst'] . '</a></p>';
	}
	
	function check_done($page) {
		static $dones = NULL;
		if ($page === FALSE) {
			$dones = NULL;
		}
		if (is_null($dones)) {
			if (is_file($this->config['doneCache'])) {
				$dones = file($this->config['doneCache']);
				$dones = array_map('trim', $dones);
			} else {
				$dones = array();
			}
		}
		return (in_array($page, $dones));
	}
	
	function save_done($page) {
		if ($fp = fopen($this->config['doneCache'], 'a')) {
			fwrite($fp, $page . "\n");
			fclose($fp);
		}
	}
	
	function clear_done() {
		@ unlink($this->config['doneCache']);
	}
	
	function get_err_regex($reg, $msg) {
		set_error_handler(array($this, 'myErrorHandler'));
		$check = @ preg_match($reg, '');
		restore_error_handler();
		if ($check === false) {
			return $msg . $this->error;
		}
		return false;
	}
	
	function myErrorHandler($errno, $errstr) {
		if ($errno === E_NOTICE || $errno === E_WARNING) {
			$this->error = ' [ ' . strip_tags($errstr) . ' ]';
			return true;
		}
		return false;
	}
}
?>