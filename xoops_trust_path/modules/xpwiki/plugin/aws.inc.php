<?php
/*
 * Created on 2008/02/28 by nao-pon http://hypweb.net/
 * $Id: aws.inc.php,v 1.22 2011/10/31 12:40:35 nao-pon Exp $
 */

/////////////////////////////////////////////////
// #aws([Template name],[Search Index],[Keyword],[Node Number],[Sort Mode])

class xpwiki_plugin_aws extends xpwiki_plugin {

	var $options_default = array();

	function plugin_aws_init() {
		//////// Config ///////
		$this->config['AccessKeyId']     = $this->root->amazon_AccessKeyId;
		$this->config['SecretAccessKey'] = $this->root->amazon_SecretAccessKey;
		$this->config['amazon_t']        = $this->root->amazon_AssociateTag;// Associates ID
		$this->config['cache_time']      = 1440; // Cache time (min) 1440min = 24h
		$this->config['template_map']    = array(
			// Template mapping
			'From name' => 'To name',
		);

		$this->options_default = array(
			'search'    => 'keywords',
			'timestamp' => FALSE,
			'makepage'  => FALSE,
			'maxdepth'  => 5,
			'pages'     => 1,
			'start'     => 1,
		);

	}

	function xpwiki_plugin_aws(& $func) {
		parent::xpwiki_plugin($func);

		// Amazon associate ID
		if (! $this->root->amazon_AssociateTag) {
			include_once XOOPS_TRUST_PATH . '/class/hyp_common/hsamazon/hyp_simple_amazon.php';
			$ama = new HypSimpleAmazon();
			$this->root->amazon_AssociateTag = $ama->AssociateTag;
			$ama = NULL;
		}
	}

	function plugin_aws_action() {
		if (isset($this->root->vars['pcmd']) && $this->root->vars['pcmd'] === 'gc') {
			$this->gc();
		}
	}

	function plugin_aws_convert() {

		if (HypCommonFunc::get_version() < 20080224) {
			return '#aws require "HypCommonFunc" >= Ver. 20080224';
		}

		if (! empty($this->root->vars['page']) && preg_match('/template/i', $this->root->vars['page'])) {
			return FALSE;
		}

		$this->root->rtf['disable_render_cache'] = true;

		$this->load_language();

		if (! $this->options_default) {
			$this->options_default = array(
				'search'    => 'keywords',
				'timestamp' => FALSE,
				'makepage'  => FALSE,
				'maxdepth'  => 5,
				'pages'     => 1,
				'start'     => 1,
			);
		} else {
			// for compat
			if (! isset($this->options_default['pages'])) {
				$this->options_default['pages'] = 1;
			}
			if (! isset($this->options_default['start'])) {
				$this->options_default['start'] = 1;
			}
		}
		$this->options = $this->options_default;

		$args = array_pad(func_get_args(), 6, '');
		$f = array_shift($args);
		$m = array_shift($args);
		$k = array_shift($args);
		$b = preg_replace('/[^0-9,]+/', '', array_shift($args));
		$s = array_shift($args);
		$header = array_shift($args);
		if ($header === '') {
			$header = 1;
		}

		if (!$k && !$b) return FALSE;

		$this->fetch_options($this->options, $args);

		list($more_link, $ret) = $this->plugin_aws_get($f, $m, $k, $b, $s);

		$style = ' style="word-break:break-all;"';
		$more = '';
		if ($more_link) {
			$header  = intval($header);
			if ($header > 2 && $header < 6) {
				$more = '<h'.$header.'>' . $more_link . '</h'.$header.'>';
			} else {
				$more = ($header) ? '<h4>' . $more_link . '</h4>' : '';
			}
		}
		return $this->gc(true) . $more . '<div' . $style . '>' . $ret . '</div>';
	}

	function plugin_aws_get($f, $m, $k, $b, $s) {

		$ret = '';

		if (!$f) $f = 'default';
		if (!empty($this->config['template_map'])) {
			if (array_key_exists($f, $this->config['template_map'])) {
				$f = $this->config['template_map'][$f];
			}
		}

		$this->options['amazon_t'] = $this->config['amazon_t'];
		if (! empty($this->root->vars['page'])) {
			if ($this->root->amazon_UseUserPref) {
				$user_pref = $this->func->get_user_pref($this->func->get_pg_auther($this->root->vars['page']));
				if (! empty($user_pref['amazon_associate_tag'])) {
					$this->options['amazon_t'] = preg_replace('/[^a-zA-Z0-9-]/', '', $user_pref['amazon_associate_tag']);
				}
			}

			if ($this->options['timestamp']) {
				$this->options['page'] = $this->root->vars['page'];
			}
		}

		$cache_file = $this->cont['CACHE_DIR'] . 'plugin/' . md5($f.$m.$k.$b.$s.serialize($this->options)).".aws";

		if (! empty($this->root->rtf['preview'])) {
			@ unlink($cache_file);
		}

		if (is_readable($cache_file) && filemtime($cache_file) + $this->config['cache_time'] * 60 > $this->cont['UTC']) {
			$ret = file_get_contents($cache_file);
		} else {
			include_once $this->cont['TRUST_PATH'] . 'class/hyp_common/hsamazon/hyp_simple_amazon.php';
			$ama = new HypSimpleAmazon($this->options['amazon_t']);
			if ($this->config['AccessKeyId']) $ama->AccessKeyId = $this->config['AccessKeyId'];
			if ($this->config['SecretAccessKey']) $ama->SecretAccessKey = $this->config['SecretAccessKey'];
			$ama->encoding = ($this->cont['SOURCE_ENCODING'] === 'EUC-JP')? 'EUCJP-win' : $this->cont['SOURCE_ENCODING'];
			$ama->getPages = $this->options['pages'];

			$options = array();
			if ($s && preg_match("/\+?([a-z,-]+)/", $s, $s_val))
			{
				$options['Sort'] = $s_val[1];
			}

			if ($k) {
				$ama->setSearchIndex($m, $this->options['search']);
				if ($b) $options['BrowseNode'] = $b;
				$ama->itemSearch($k, $options);
			} else if ($b) {
				$ama->setSearchIndex($m);
				$ama->browseNodeSearch($b, $options);
			}

			$html = $ama->getHTML($f);

			//if (! empty($this->root->rtf['preview'])) {$html .= $ama->url;}

			$header = ($k && ! is_null($ama->compactArray['totalresults']))? $ama->makeSearchLink($k, sprintf($this->msg['more_search'], htmlspecialchars($k)), TRUE) : '';
			$ret = $header . "\x08" . $html;

			// remove wrong characters
			$ret = mb_convert_encoding($ret, $this->cont['SOURCE_ENCODING'], $this->cont['SOURCE_ENCODING']);

			if (! is_null($ama->compactArray['totalresults']) && empty($this->root->rtf['preview']) && $fp = @fopen($cache_file,"wb")) {
				fputs($fp,$ret);
				fclose($fp);
			} else {
				//$ret .= $ama->url;
			}
			if ($this->options['timestamp'] && empty($this->root->rtf['preview']) && $ama->newestTime && ! empty($this->root->vars['page'])) {
				$this->func->touch_page($this->root->vars['page'], $ama->newestTime);
			}

			if (! $ama->error
			    && empty($this->root->rtf['preview'])
			    && $this->options['makepage']
			    && ! empty($this->root->vars['page'])
			    && substr_count($this->root->vars['page'], '/') + 1 < $this->options['maxdepth']
			   ) {
				$wait = 0;
				$checkUTIME = $this->cont['UTC'] - (86400 * 7); // 1週間前まで
				foreach($ama->compactArray['Items'] as $item) {
					if ($checkUTIME <= $item['RELEASEUTIME'] && $this->func->basename($this->root->vars['page']) !== $item['TITLE']) {
						$newpage = $this->root->vars['page'] . '/' . $this->func->pagename_normalize(str_replace('/', '|', htmlspecialchars_decode($item['TITLE'])));
						if (! $this->func->is_page($newpage) && ! $this->func->is_alias($newpage)) {
							$data = array(
								'action' => 'plugin_func',
								'plugin' => 'makepage',
								'func' => 'auto_make',
								'args' => array(
									'new_page' => $newpage,
									'twitter'  => 'Release Date: ' . $item['RELEASEDATE'] . ' (' . $item['PRICE_FORMATTED'] . ')'
								),
							);
							$this->func->regist_jobstack($data, 864000, $wait);
							$wait = $wait + 10;
						}
					}
				}
			}

			$ama = NULL;

			if (empty($this->root->rtf['preview'])) {
				// Update plainDB
				$this->func->need_update_plaindb();
				// After a day
				$this->func->need_update_plaindb($this->root->vars['page'], 'update', TRUE, TRUE, $this->config['cache_time'] * 60);
			}
		}
		return explode("\x08", $ret, 2);
	}

	function gc($get_tag = FALSE) {
		$dir = $this->cont['CACHE_DIR'] . 'plugin';
		$gc = $this->cont['CACHE_DIR'] . 'plugin/aws.gc';
		$interval = $this->config['cache_time'] * 60;
		if (! is_file($gc) || filemtime($gc) < $this->cont['UTC'] - $interval) {
			if ($get_tag) {
				return '<div style="float:left;"><img src="' . $this->root->script . '?plugin=aws&amp;pcmd=gc" width="1" height="1" alt="" /></div>' . "\n";
			}
			touch($gc);
			$attr = '.aws';
			$attr_len = strlen($attr) * -1;
		    $ttl = $this->config['cache_time'] * 60;
		    $check = $this->cont['UTC'] - $ttl;
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		            if (substr($file, $attr_len) === $attr ) {
		            	$target = $dir . '/' . $file;
		            	if (filemtime($target) < $check) {
		            		unlink($target);
		            	}
		            }
		        }
		        closedir($dh);
		    }
		}
		if ($get_tag) {
			return '';
		}
		// clear output buffer
		$this->func->clear_output_buffer();
		// imgタグ呼び出し用
		header("Content-Type: image/gif");
		HypCommonFunc::readfile($this->root->mytrustdirpath . '/skin/image/gif/spacer.gif');
	}
}
?>