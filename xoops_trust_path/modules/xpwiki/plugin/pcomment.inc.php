<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: pcomment.inc.php,v 1.26 2011/10/28 13:52:37 nao-pon Exp $
//
// pcomment plugin - Show/Insert comments into specified (another) page
//
// Usage: #pcomment([page][,max][,options])
//
//   page -- An another page-name that holds comments
//           (default:PLUGIN_PCOMMENT_PAGE)
//   max  -- Max number of recent comments to show
//           (0:Show all, default:PLUGIN_PCOMMENT_NUM_COMMENTS)
//
// Options:
//   above -- Comments are listed above the #pcomment (added by chronological order)
//   below -- Comments are listed below the #pcomment (by reverse order)
//   reply -- Show radio buttons allow to specify where to reply

class xpwiki_plugin_pcomment extends xpwiki_plugin {
	function plugin_pcomment_init () {
		// Default recording page name (%s = $vars['page'] = original page name)

		$this->load_language($this->cont['LANG']);

		$this->conf['PAGE'] = $this->msg['PAGE'];
		$this->conf['NEW_TITLE'] = $this->msg['NEW_TITLE'];
		$this->conf['NOW_COMMENT'] = $this->msg['NOW_COMMENT'];
		$this->conf['LOG_TITLE'] = $this->msg['LOG_TITLE'];

		$this->conf['NUM_COMMENTS'] =      10; // Default 'latest N posts'
		$this->conf['DIRECTION_DEFAULT'] =  1; // 1: above 0: below
		$this->conf['SIZE_MSG'] =   70;
		$this->conf['SIZE_NAME'] =  15;

		// Auto log rotation
		$this->conf['AUTO_LOG'] =  30; // 0:off 1-N:number of comments per page

		// Update recording page's timestamp instead of parent's page itself
		$this->conf['TIMESTAMP'] =  1;

		// Template "default"
		$this->conf['FORMAT_NAME']['default'] = '[[$name]]';
		$this->conf['FORMAT_MSG']['default']  = '$msg';
		$this->conf['FORMAT_NOW']['default']  = '&new{$now};';
		// "\x01", "\x02", "\x03", and "\x08" are used just as markers
		$this->conf['FORMAT_STRING']['default'] =
		"\x08" . 'MSG' . "\x08" . ' -- ' . "\x08" . 'NAME' . "\x08" . ' ' . "\x08" . 'DATE' . "\x08";

		// Template "areaedt"
		$this->conf['FORMAT_NAME']['areaedit'] = '$name';
		$this->conf['FORMAT_MSG']['areaedit']  = '&areaedit(' . "\x08" . 'USER_CODE' . "\x08" . ',preview:5){$msg};';

		$this->conf['TEMPLATE_DEFAULT'] = 'default';

		$this->conf['NEW_PAGE_FORMAT'] = <<<EOD
* {$this->conf['NEW_TITLE']}
** {$this->conf['LOG_TITLE']}
#ls2(,pagename,col:5)
** {$this->conf['NOW_COMMENT']}
EOD;

		$this->conf['LOG_PAGE_FORMAT'] = <<<EOD
* {$this->conf['LOG_TITLE']} (<_LOG_NUMBAR_>)

#navi(../)

<_BODY_>

#navi(../)
EOD;

		$this->conf['TWITTER']['TRAGET PAGE NAME'] = array(
			'enable_ids'      => '',
			'enable_name_reg' => ''
		);

		$this->conf['options'] = array(
			'noname'=>FALSE,
			'nodate'=>FALSE,
			'below' =>FALSE,
			'above' =>FALSE,
			'reply' =>FALSE,
			'template' => $this->conf['TEMPLATE_DEFAULT'],
			'cols'   => $this->conf['SIZE_MSG'],
			'multi' =>FALSE,
			'emoji' => TRUE,
			'noemoji'=> FALSE,
			'twitter'=> FALSE,
			'_args' =>array()
		);
	}

	function xpwiki_plugin_pcomment(& $xpwiki) {
		parent::xpwiki_plugin($xpwiki);
		$this->conf['options_default'] = array(
			'noname'=>FALSE,
			'nodate'=>FALSE,
			'below' =>FALSE,
			'above' =>FALSE,
			'reply' =>FALSE,
			'template' => '',
			'cols'   => '',
			'multi' =>FALSE,
			'emoji' => TRUE,
			'noemoji'=> FALSE,
			'twitter'=> FALSE,
			'_args' =>array()
		);
	}

	function plugin_pcomment_action()
	{

		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');

		$this->root->vars['msg'] = str_replace(array("\r\n", "\r", "\n"), '&br;', rtrim($this->root->vars['msg'])); // LFs
		if (! isset($this->root->vars['msg']) || $this->root->vars['msg'] == '') return array();
		$refer = isset($this->root->vars['refer']) ? $this->root->vars['refer'] : '';

		$retval = $this->plugin_pcomment_insert();
		if ($retval['collided']) {
			$this->root->vars['page'] = $refer;
			return $retval;
		}

		if ($this->root->render_mode !== 'render') {
			$back = ($refer)? $this->func->get_page_uri($refer, TRUE) : ($_SERVER['HTTP_REFERER']? $_SERVER['HTTP_REFERER'] : $this->root->script);
		} else {
			$back = ($refer)? $this->root->siteinfo['host'].$refer : ($_SERVER['HTTP_REFERER']? $_SERVER['HTTP_REFERER'] : $this->cont['ROOT_URL']);
		}

		$this->func->send_location('', '', $back);
	}

	function can_call_otherdir_convert() {
		return 1;
	}

	function plugin_pcomment_convert()
	{
		$ret = '';

		$params = array_merge($this->conf['options_default'], $this->conf['options']);

		$args = func_get_args();
		$pre_contents = array();
		if (strpos($args[count($args) - 1], "\r") !== FALSE) {
			$_pre = rtrim(str_replace("\r", "\n", array_pop($args))) . "\n\n";
			foreach(explode("\n", $_pre) as $_line){
				$pre_contents[] = $_line . "\n";
			}
			$pre_contents[] = "\n";
			//echo $pre_contents;
		}
		$this->fetch_options($params, $args, array('page'));

		$vars_page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		$page = $params['page'];
		if (! $page ) $page = sprintf($this->conf['PAGE'], $this->func->strip_bracket($vars_page));
		$count = isset($params['_args'][0]) ? intval($params['_args'][0]) : 0;
		if ($count == 0) $count = $this->conf['NUM_COMMENTS'];
		$temp = htmlspecialchars($params['template']);

		$_page = $this->func->get_fullname($this->func->strip_bracket($page), $vars_page);
		if (!$this->func->is_pagename($_page))
			return sprintf($this->root->_pcmt_messages['err_pagename'], htmlspecialchars($_page));

		$dir = $this->conf['DIRECTION_DEFAULT'];
		if ($params['below']) {
			$dir = 0;
		} elseif ($params['above']) {
			$dir = 1;
		}

		list($comments, $digest) = $this->plugin_pcomment_get_comments($_page, $count, $dir, $params['reply'], $pre_contents);

		if ($this->cont['PKWK_READONLY'] === 1) {
			$form_start = $form = $form_end = '';
		} else if ($this->cont['PKWK_READONLY'] === 2 && ! $this->func->check_editable_page($_page, FALSE, FALSE)) {
			$form_start = $form = $form_end = '';
		} else {
			// Show a form
			$this->root->rtf['disable_render_cache'] = true;
			if ($this->root->render_mode === 'render') {
				$this->func->add_tag_head("default.{$this->cont['UI_LANG']}{$this->cont['FILE_ENCORD_EXT']}.js");
			}
			if ($params['noname']) {
				$title = $this->root->_pcmt_messages['msg_comment'];
				$name = '';
			} else {
				$title = $this->root->_pcmt_messages['btn_name'];
				$name = '<input type="text" name="name" value="' . $this->cont['USER_NAME_REPLACE'] . '"size="' . $this->conf['SIZE_NAME'] . '" />';
			}

			$radio = '';
			if ($params['reply']) {
				$this->root->pagecache_profiles = 'default';
				$datarole = ($this->cont['UA_PROFILE'] === 'mobile')? 'data-role="none" ' : '';
				$radio = '<input type="radio" name="reply" value="0" tabindex="0" checked="checked" '.$datarole.'/>';
			};

			$cols = max(10, min(80, intval($params['cols'])));

			if ($params['multi']) $rows = max(1, min(20, intval($params['multi'])));
			$domid = $this->get_domid('msg', true);
			$comment = $params['multi']? '<textarea name="msg" id="' . $domid . '" class="norich" style="display:inline;" cols="' . ($cols * 0.8) . '" rows="' . $rows . '"></textarea>'
					: '<input type="text" name="msg" id="' . $domid . '" rel="wikihelper" size="' . $cols . '" />';

			$emoji_style = ($this->cont['UA_PROFILE'] === 'mobile')? '' : ' style="margin-left:13em;"';
			$emojipad = (! $params['emoji'] || $params['noemoji'])? '' : '<div'.$emoji_style.'>' . $this->func->get_emoji_pad($domid, FALSE) . '</div>';

			$twitter = $twitter_note = $twitter_disabled = $twitter_checked = '';
			if ($params['twitter']) {
				if ($this->root->userinfo['uid'] && $this->root->twitter_consumer_key && $this->root->twitter_consumer_secret) {
					$user_pref = $this->func->get_user_pref($this->root->userinfo['uid']);
					if (empty($user_pref['twitter_access_token']) || empty($user_pref['twitter_access_token_secret'])) {
						$twitter_note = str_replace('$1', $this->root->script . '?cmd=user_pref', $this->msg['to_user_pref']);
						$twitter_disabled = ' disabled="disabled"';
					} else {
						$twitter_checked = ' checked="checked"';
					}
				} else {
					$twitter_note = $this->msg['to_login'];
					$twitter_disabled = ' disabled="disabled"';
				}
				$domid = $this->get_domid('twitter', true);
				$twitter = '<div><!--NA--><input type="checkbox" id="'.$domid.'" name="twitter" value="1"' . $twitter_disabled . $twitter_checked . ' /><label for="'.$domid.'"> ' . $this->msg['with_twitter'] . '</label>' . $twitter_note . '<!--/NA--></div>';
			}

			$s_page   = htmlspecialchars($_page);
			if ($this->root->render_mode !== 'render') {
				$s_refer = htmlspecialchars($vars_page);
			} else {
				$s_refer = htmlspecialchars($_SERVER['REQUEST_URI']);
			}
			$s_nodate = htmlspecialchars($params['nodate']);

			$form_start = '<form action="' . $this->func->get_script_uri() . '" method="post">' . "\n";
			$form = <<<EOD
  <div>
  <input type="hidden" name="digest" value="$digest" />
  <input type="hidden" name="plugin" value="pcomment" />
  <input type="hidden" name="refer"  value="$s_refer" />
  <input type="hidden" name="page"   value="$s_page" />
  <input type="hidden" name="nodate" value="$s_nodate" />
  <input type="hidden" name="dir"    value="$dir" />
  <input type="hidden" name="count"  value="$count" />
  <input type="hidden" name="temp"   value="$temp" />
  $twitter
  $radio $title $name $comment
  <input type="submit" value="{$this->root->_pcmt_messages['btn_comment']}" />
  $emojipad
  </div>
EOD;
			$form_end = '</form>' . "\n";
		}

		if (! $this->func->is_page($_page)) {
			$link   = $this->func->make_pagelink($_page);
			$recent = $this->root->_pcmt_messages['msg_none'];
		} else {
			$msg    = ($this->root->_pcmt_messages['msg_all'] != '') ? $this->root->_pcmt_messages['msg_all'] : $_page;
			$link   = $this->func->make_pagelink($_page, $msg);
			$recent = ! empty($count) ? sprintf($this->root->_pcmt_messages['msg_recent'], $count) : '';
		}

		if ($dir) {
			return '<div>' .
			'<p>' . $recent . ' ' . $link . '</p>' . "\n" .
			$form_start .
			$comments . "\n" .
			$form .
			$form_end .
			'</div>' . "\n";
		} else {
			return '<div>' .
			$form_start .
			$form .
			$comments. "\n" .
			$form_end .
			'<p>' . $recent . ' ' . $link . '</p>' . "\n" .
			'</div>' . "\n";
		}
	}

	function plugin_pcomment_insert()
	{
		$template_vars = array(
			'FORMAT_NAME',
			'FORMAT_MSG',
			'FORMAT_NOW',
			'FORMAT_STRING'
		);

		$refer = isset($this->root->vars['refer']) ? $this->root->vars['refer'] : '';
		$page  = isset($this->root->vars['page'])  ? $this->root->vars['page']  : '';
		$template = isset($this->root->vars['temp']) ? $this->root->vars['temp'] : $this->conf['TEMPLATE_DEFAULT'];

		$twitter_msg = '';

		// テンプレート設定
		foreach ($template_vars as $_vars) {
			$$_vars = isset($this->conf[$_vars][$template])?
				$this->conf[$_vars][$template] : $this->conf[$_vars]['default'];
		}

		$this->root->vars['page'] = $page = $this->func->get_fullname($page, $refer);

		if (! $this->func->is_pagename($page))
			return array(
				'msg' =>'Invalid page name',
				'body'=>'Cannot add comment' ,
				'collided'=>TRUE
			);

		//$this->func->check_editable($page, true, true);

		$ret = array('msg' => $this->root->_title_updated, 'collided' => FALSE);

		$msg = str_replace('$msg', rtrim($this->root->vars['msg']), $FORMAT_MSG);
		$msg = str_replace("\x08" . 'USER_CODE' . "\x08", ($this->root->userinfo['uid']? 'uid:' . $this->root->userinfo['uid'] : 'ucd:' . $this->root->userinfo['ucd']), $msg);
		$name = (! isset($this->root->vars['name']) || $this->root->vars['name'] == '') ? $this->root->_no_name : $this->root->vars['name'];

		//$this->twitter_post($refer, $name, $msg, $this->func->get_page_uri($refer, TRUE));
		if (! empty($this->root->post['twitter'])) {
			$twitter_msg = $msg;
		}

		// save name to cookie
		if ($name) { $this->func->save_name2cookie($name); }
		$name = ($name == '') ? '' : str_replace('$name', $name, $FORMAT_NAME);
		$date = (! isset($this->root->vars['nodate']) || $this->root->vars['nodate'] != '1') ?
			str_replace('$now', $this->root->now, $FORMAT_NOW) : '';
		if ($date != '' || $name != '') {
			$msg = str_replace("\x08" . 'MSG'  . "\x08", $msg,  $FORMAT_STRING);
			$msg = str_replace("\x08" . 'NAME' . "\x08", $name, $msg);
			$msg = str_replace("\x08" . 'DATE' . "\x08", $date, $msg);
		}

		$reply_hash = isset($this->root->vars['reply']) ? $this->root->vars['reply'] : '';
		if ($reply_hash || ! $this->func->is_page($page)) {
			$msg = preg_replace('/^\-+/', '', $msg);
		}
		$msg = rtrim($msg);

		if (! $this->func->is_page($page)) {
			$this->func->make_empty_page($page);
			if (! $new_page = $this->func->auto_template($page)) $new_page = $this->conf['NEW_PAGE_FORMAT'];
			$postdata = str_replace('<_REFER_>', htmlspecialchars($this->func->strip_bracket($refer)), $new_page) . "\n" .
			'-' . $msg . "\n";
		} else {
			$postdata = $this->func->get_source($page);
			$this->func->escape_multiline_pre($postdata, TRUE);
			$count    = count($postdata);

			$digest = isset($this->root->vars['digest']) ? $this->root->vars['digest'] : '';
			if ($this->func->get_digests(join('', $postdata)) != $digest) {
				$ret['msg']  = $this->root->_pcmt_messages['title_collided'];
				$ret['body'] = $this->root->_pcmt_messages['msg_collided'];
			}

			$start_position = 0;
			while ($start_position < $count) {
				if (preg_match('/^\-/', $postdata[$start_position])) break;
				++$start_position;
			}
			$end_position = $start_position;

			$dir = isset($this->root->vars['dir']) ? $this->root->vars['dir'] : '';

			// Find the comment to reply
			$level   = 1;
			$b_reply = FALSE;
			if ($reply_hash != '') {
				while ($end_position < $count) {
					$matches = array();
					if (preg_match('/^(\-{1,2})(?!\-)(.*)$/', $postdata[$end_position++], $matches)
						&& md5($matches[2]) == $reply_hash)
					{
						$b_reply = TRUE;
						$level   = strlen($matches[1]) + 1;

						while ($end_position < $count) {
							if (preg_match('/^(\-{1,6})(?!\-)/', $postdata[$end_position], $matches)
								&& strlen($matches[1]) < $level) break;
							++$end_position;
						}
						break;
					}
				}
			}

			if ($b_reply == FALSE)
				$end_position = ($dir == '0') ? $start_position : $count;

			// Insert new comment
			array_splice($postdata, $end_position, 0, str_repeat('-', $level) . $msg . "\n");

			if ($this->conf['AUTO_LOG']) {
				$_count = isset($this->root->vars['count']) ? $this->root->vars['count'] : '';
				$this->plugin_pcomment_auto_log($page, $dir, $_count, $postdata);
			}
			$postdata = join('', $postdata);
		}
		$this->func->escape_multiline_pre($postdata, FALSE);
		$this->func->page_write($page, $postdata, $this->conf['TIMESTAMP']);

		if ($this->conf['TIMESTAMP']) {
			if ($refer !== '') $this->func->touch_page($refer, FALSE, TRUE);
		}

		if ($twitter_msg) {
			$this->func->twitter_update($twitter_msg, $refer);
		}

		return $ret;
	}

	// Auto log rotation
	function plugin_pcomment_auto_log($page, $dir, $count, & $postdata)
	{
		if (! $this->conf['AUTO_LOG']) return;

		$keys = array_keys(preg_grep('/(?:^-(?!-).*$)/m', $postdata));
		if (count($keys) < ($this->conf['AUTO_LOG'] + $count)) return;

		if ($dir) {
			// Top N comments (N = PLUGIN_PCOMMENT_AUTO_LOG)
			$old = array_splice($postdata, $keys[0], $keys[$this->conf['AUTO_LOG']] - $keys[0]);
		} else {
			// Bottom N comments
			$old = array_splice($postdata, $keys[count($keys) - $this->conf['AUTO_LOG']]);
		}

		// Decide new page name
		$i = 0;
		do {
			++$i;
			$_page = $page . '/' . $i;
		} while ($this->func->is_page($_page));

		if (! $new_page = $this->func->auto_template($_page)) $new_page = $this->conf['LOG_PAGE_FORMAT'];

		$data = $this->func->rewrite4move2child(join('', $old));
		$data = str_replace(array('<_LOG_NUMBAR_>', '<_BODY_>'), array($i, $data), $new_page);

		$this->func->make_empty_page($_page);

		$this->func->page_write($_page, $data);

		// Recurse :)
		$this->plugin_pcomment_auto_log($page, $dir, $count, $postdata);
	}

	// Check arguments
	function plugin_pcomment_check_arg($val, & $params)
	{
		if ($val != '') {
			$l_val = strtolower($val);
			foreach (array_keys($params) as $key) {
				if (strpos($key, $l_val) === 0) {
					$params[$key] = TRUE;
					return;
				}
			}
		}

		$params['_args'][] = $val;
	}

	function plugin_pcomment_get_comments($page, $count, $dir, $reply, $pre_contents)
	{
	//	global $_msg_pcomment_restrict;

		if (! $this->func->check_readable($page, false, false))
			return array(str_replace('$1', $page, $this->root->_msg_pcomment_restrict));

		$reply = (! $this->cont['PKWK_READONLY'] && $reply); // Suprress radio-buttons

		$data = $this->func->get_source($page);
		$data = preg_replace('/^#pcomment\(?.*/i', '', $data);	// Avoid eternal recurse

		if (! $data) return array('', 0);

		$digest = $this->func->get_digests(join('', $data));

		// Get latest N comments
		$num  = $cnt     = 0;
		$cmts = $matches = array();
		if ($dir) $data = array_reverse($data);
		foreach ($data as $line) {
			if ($count > 0 && $dir && $cnt == $count) break;

			if (preg_match('/^(\-{1,2})(?!\-)(.+)$/', $line, $matches)) {
				if ($count > 0 && strlen($matches[1]) == 1 && ++$cnt > $count) break;

				// Ready for radio-buttons
				if ($reply) {
					++$num;
					$cmts[] = $matches[1] . "\x01" . $num . "\x02" .
					md5($matches[2]) . "\x03" . $matches[2] . "\n";
					continue;
				}
			}
			$cmts[] = $line;
		}
		$data = $cmts;
		if ($dir) $data = array_reverse($data);
		unset($cmts, $matches);

		// Remove lines before comments
		while (! empty($data) && substr($data[0], 0, 1) != '-')
			array_shift($data);

		//areaedit用スタートマーカーセット
		$start = md5(rtrim(preg_replace("/\x01.+?\x03/", '', $data[0])));

		$comments = $this->func->convert_html(array_merge($pre_contents, $data), $page);
		unset($data);

		//areaedit用スタートマーカー付加
		$comments = str_replace("<a href=\"".$this->root->script."?plugin=areaedit","<a href=\"".$this->root->script."?plugin=areaedit&amp;start=$start",$comments);

		// Add radio buttons
		if ($reply)
			$datarole = ($this->cont['UA_PROFILE'] === 'mobile')? 'data-role="none" ' : '';
			$comments = preg_replace('/<li>' . "\x01" . '(\d+)' . "\x02" . '(.*)' . "\x03" . '/',
			'<li class="pcmt"><input class="pcmt" type="radio" name="reply" value="$2" tabindex="$1" '.$datarole.'/>',
			$comments);

		return array($comments, $digest);
	}

	function twitter_post($refer, $name, $msg, $link = '') {
		if (empty($this->conf['TWITTER'][$refer])) return;
		$config =  $this->conf['TWITTER'][$refer];
		if (! $config['username'] || ! $config['password']) return;
		$enable_ids = explode(',', $config['enable_ids']);
		if (in_array($this->root->userinfo['uid'], $enable_ids) || ($config['enable_name_reg'] && preg_match($config['enable_name_reg'], $name))) {
			$this->func->twitter_post($config['username'], $config['password'], $msg, $link);
		}
	}
}
?>