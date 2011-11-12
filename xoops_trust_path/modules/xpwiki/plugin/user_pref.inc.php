<?php
/*
 * Created on 2008/01/24 by nao-pon http://hypweb.net/
 * $Id: user_pref.inc.php,v 1.5 2011/10/28 13:42:42 nao-pon Exp $
 */

class xpwiki_plugin_user_pref extends xpwiki_plugin {
	function plugin_user_pref_init() {
		$this->load_language();

		$this->user_pref = array(

			'twitter_access_token' => array(
				'type' => 'string',
				'form' => 'hidden',
			),

			'twitter_access_token_secret' => array(
				'type' => 'string',
				'form' => 'hidden',
			),

			'amazon_associate_tag' => array(
				'type' => 'string',
				'form' => 'text,size="20"',
			),

			'moblog_mail_address' => array(
				'type' => 'string',
				'form' => 'text,size="60"',
			),

			'moblog_user_mail' => array(
				'type' => 'string',
				'form' => 'hidden',
			),

			'moblog_base_page' => array(
				'type' => 'string',
				'form' => 'text,size="60"',
			),

			'moblog_auth_code' => array(
				'type' => 'integer!0',
				'form' => 'text,size="10"',
			),

			'moblog_to_twitter' => array(
				'type' => 'integer',
				'form' => 'yesno',
			),

			'xmlrpc_pages' => array(
				'type' => 'string',
				'form' => 'textarea,cols="40" rows="2"',
			),

			'xmlrpc_auth_key' => array(
				'type' => 'string',
				'form' => 'text,size="15"',
			),

			'xmlrpc_to_twitter' => array(
				'type' => 'integer',
				'form' => 'yesno',
			),


		);
	}

	function plugin_user_pref_action() {

		// 権限チェック
		if (!$this->root->userinfo['uid']) {
			$this->func->redirect_header($this->root->siteinfo['loginurl'], 1, $this->root->_msg_not_readable, true);
		} else {
			$this->uid = $this->root->userinfo['uid'];
		}

		if (! $this->root->amazon_UseUserPref) {
			unset($this->user_pref['amazon_associate_tag']);
		}

		$mode = empty($this->root->post['pmode'])? '' : $this->root->post['pmode'];

		switch($mode) {
			case 'post' :
				return $this->post_save();
				break;
			default :
				return $this->show_form();
		}
		return false;
	}

	function show_form() {

		$user_pref = $this->func->get_user_pref($this->uid);

		$disabled = array();

		if ($this->root->twitter_consumer_key && $this->root->twitter_consumer_secret && function_exists('curl_init') && version_compare(PHP_VERSION, '5.0.0', '>') && HypCommonFunc::get_version() >= '20100108') {
			HypCommonFunc::loadClass('TwitterOAuth');

			$state = isset($_SESSION['oauth_state'])? $_SESSION['oauth_state'] : '';

			if (!empty($user_pref['twitter_access_token']) && !empty($user_pref['twitter_access_token_secret'])) {
				$to = new TwitterOAuth($this->root->twitter_consumer_key, $this->root->twitter_consumer_secret, $user_pref['twitter_access_token'], $user_pref['twitter_access_token_secret']);
				$content = $to->OAuthRequest('https://twitter.com/account/verify_credentials.xml', 'GET', array());
				if (strpos($content, '<error>') === FALSE) {
					$state = 'ok';
				} else {
					$user_pref['twitter_access_token'] = '';
					$user_pref['twitter_access_token_secret'] = '';
				}
			}

			if (! empty($this->root->get['oauth_token']) && $state === 'start') {
				$state = 'returned';
				unset($_SESSION['oauth_state']);
			}

			if (isset($this->root->get['denied'])) {
				$state = 'denied';
				unset($_SESSION['oauth_state']);
			}

			switch ($state) {
				case 'returned':
					$to = new TwitterOAuth($this->root->twitter_consumer_key, $this->root->twitter_consumer_secret, $_SESSION['oauth_request_token'], $_SESSION['oauth_request_token_secret']);
					$tok = $to->getAccessToken($this->root->get['oauth_verifier']);

					$user_pref['twitter_access_token'] = $tok['oauth_token'];
					$user_pref['twitter_access_token_secret'] = $tok['oauth_token_secret'];

					$this->msg['twitter_access_token_secret']['description'] = '';

					break;

				case 'denied':
					$user_pref['twitter_access_token'] = '';
					$user_pref['twitter_access_token_secret'] = '';
					$this->msg['twitter_access_token_secret']['description'] = '';
					break;

				case 'ok':
					$this->msg['twitter_access_token_secret']['description'] = '';
					break;

				default:
					$to = new TwitterOAuth($this->root->twitter_consumer_key, $this->root->twitter_consumer_secret);

					$tok = $to->getRequestToken($this->root->script . '?cmd=user_pref');

					if (! empty($tok['oauth_token'])) {
						$_SESSION['oauth_request_token'] = $token = $tok['oauth_token'];
						$_SESSION['oauth_request_token_secret'] = $tok['oauth_token_secret'];
						$_SESSION['oauth_state'] = "start";

						$this->root->twitter_request_link = $to->getAuthorizeURL($token);
					} else {
						$disabled['twitter'] = true;
					}

					break;

			}
		} else {
			$disabled['twitter'] = true;
		}

		if (! empty($disabled['twitter'])) {
			unset($this->user_pref['twitter_access_token'],
			      $this->user_pref['twitter_access_token_secret']);
		}

		// moblog
		if (! $this->root->use_moblog_user_pref) {
			unset($this->user_pref['moblog_mail_address'],
			      $this->user_pref['moblog_base_page'],
			      $this->user_pref['moblog_user_mail'],
			      $this->user_pref['moblog_to_twitter'],
			      $this->user_pref['moblog_auth_code']);
		} else {
			if (strpos($this->root->moblog_pop_mail, '*') === false) {
				unset($this->user_pref['moblog_user_mail']);
			} else {
				unset($this->user_pref['moblog_mail_address']);
				if (empty($user_pref['moblog_user_mail'])) {
					$user_tag = strtolower($this->make_user_tag());
					$user_pref['moblog_user_mail'] = str_replace('*', $user_tag, $this->root->moblog_pop_mail);
				}
				$this->root->moblog_user_mail = htmlspecialchars($user_pref['moblog_user_mail']);
				$this->root->moblog_user_mail_rawurlenc = rawurlencode($user_pref['moblog_user_mail']);
			}
			if (! $user_pref['twitter_access_token']) {
				unset($this->user_pref['moblog_to_twitter']);
			}
		}

		// XML-RPC
		if (! $this->root->use_xmlrpc) {
			unset($this->user_pref['xmlrpc_pages'],
			      $this->user_pref['xmlrpc_auth_key'],
			      $this->user_pref['xmlrpc_to_twitter']);
		} else {
			if (empty($user_pref['xmlrpc_pages'])) {
				// Read user config
				$pages = array();
				$config = new XpWikiConfig($this->xpwiki, $this->cont['PKWK_CONFIG_USER'] . '/' . $this->root->userinfo['uname']);
				$table = $config->read() ? $config->get('XML-RPC') : array();
				foreach ($table as $row) {
					if (isset($row[1]) && strtolower(trim($row[0])) === 'myblog') {
						$page = $this->func->strip_bracket(trim($row[1]));
						$pages[] = htmlspecialchars($page);
					}
				}
				$user_pref['xmlrpc_pages'] = join("\n", $pages);
			}
			if (empty($user_pref['xmlrpc_auth_key'])) {
				$user_pref['xmlrpc_auth_key'] = substr($this->make_user_tag(), 0, 8);
			}
			if (! $user_pref['twitter_access_token']) {
				unset($this->user_pref['xmlrpc_to_twitter']);
			}

		}

		$script = $this->func->get_script_uri();

		if ($this->user_pref) {

			$body =<<<EOD
<div>
<h2>{$this->msg['title_description']}</h2>
{$this->msg['msg_description']}
</div>
<hr />
<div class="user_pref">
<form action="{$script}" method="post">
<table>
EOD;
			//var_dump($user_pref);
			//exit;
			foreach ($this->user_pref as $key => $conf) {
				$caption = ! empty($conf['caption'])? $conf['caption'] : (! empty($this->msg[$key]['caption'])? $this->msg[$key]['caption'] : $key);
				$description = ! empty($conf['description'])? $conf['description'] : (! empty($this->msg[$key]['description'])? $this->msg[$key]['description'] : '');
				$description = preg_replace('/\{\$root->(.+?)\}/e', '$this->root->$1', $description);
				$value = isset($user_pref[$key])? $user_pref[$key] : '';
				$value4disp = htmlspecialchars($value);
				$name4disp = htmlspecialchars($key);
				$real = '';

				$extention = ! empty($this->msg[$key]['extention'])? $this->msg[$key]['extention'] : '';
				list($form, $attr) = array_pad(explode(',', $conf['form'], 2), 2, '');
				switch ($form) {
					case 'select':
						$forms = array();
						if (! isset($conf['list']['group'])) {
							$conf['list']['group'][0] = $conf['list'];
						}
						foreach($conf['list']['group'] as $label => $optgroup) {
							if (is_string($label)) {
								$forms[] = '<optgroup label="'.$label.'">';
							}
							foreach($optgroup as $list_cap => $list_val) {
								if ($value == $list_val) {
									$selected = ' selected="selected"';
								} else {
									$selected = '';
								}
								$forms[] = '<option value="'.$list_val.'"'.$selected.'>'.$list_cap.'</option>';
							}
							if (is_string($label)) {
								$forms[] = '</optgroup>';
							}
						}
						$form = '<select name="'.$name4disp.'" '.$attr.'>' . join('', $forms) . '</select>';
						break;
					case 'yesno':
						$conf['list'] = array(
							$this->msg['Yes'] => 1,
							$this->msg['No'] => 0,
						);
					case 'radio':
						$forms = array();
						$i = 0;
						foreach($conf['list'] as $list_cap => $list_val) {
							if ($value == $list_val) {
								$checked = ' checked="checked"';
							} else {
								$checked = '';
							}
							$forms[] = '<span class="nowrap"><input id="'.$name4disp.'_'.$i.'" type="radio" name="'.$name4disp.'" value="'.$list_val.'"'.$checked.' /><label for="'.$name4disp.'_'.$i.'">'.$list_cap.'</label></span>';
							$i++;
						}
						$form = join(' | ', $forms);
						break;
					case 'textarea':
						$form = '<textarea name="'.$name4disp.'" '.$attr.' rel="nowikihelper">'.$value4disp.'</textarea>';
						break;
					case 'hidden':
						$form = '<input type="hidden" name="'.$name4disp.'" value="'.$value4disp.'" />' . $value4disp;
						break;
					case 'text':
					default:
						$style = '';
						if ($conf['type'] === 'integer' || $conf['type'] === 'integer!0') {
							$style = ' style="text-align:right;"';
						}
						$form = '<input type="text" name="'.$name4disp.'" value="'.$value4disp.'" '.$attr.$style.' />';
				}
				$body .= <<<EOD
<tr>
 <td style="font-weight:bold;padding-top:0.5em" id="$key">$caption</td>
 <td style="padding-top:0.5em">{$form}{$extention}</td>
</tr>
<tr style="border-bottom:1px dotted gray;">
 <td colspan="2" style="padding-bottom:0.5em"><p>{$description}</p></td>
</tr>
EOD;
			}
			$body .= <<<EOD
<tr>
 <td>&nbsp;</td>
 <td><input type="submit" name="submit" value="{$this->msg['btn_submit']}" /></td>
</tr>
</table>
<input type="hidden" name="plugin" value="user_pref" />
<input type="hidden" name="pmode"	 value="post" />
</form>
</div>
EOD;
		} else {
			$body = 'There is no item.';
		}

		return array('msg'=>$this->msg['title_form'], 'body'=>$body);
	}

	function post_save() {
		$keys = array_keys($this->user_pref);
		$save = $this->func->get_user_pref($this->uid);
		$save = array();
		$posts = array();
		foreach ($keys as $key) {
			if (isset($this->root->post[$key])) {
				$posts[$key] = $this->root->post[$key];
				$save[$key] = $this->data_format($key, $this->root->post[$key]);
			}
		}
		if (empty($posts['moblog_base_page'])) {
			unset($posts['moblog_user_mail'], $save['moblog_user_mail']);
		}

		$this->func->save_user_pref($this->uid, $save);

		$done = array('<dl>');
		foreach($posts as $key=>$val) {
			$done[] = '<dt>'.htmlspecialchars($key).'<dt><dd>'.nl2br(htmlspecialchars($val)).'<dd>';
		}
		$done[] = '</dl>';

		$msg_done = $this->msg['msg_done'] . join('', $done);
		$body = <<<EOD
<p>{$msg_done}</p>
<hr />
EOD;
		return array('msg'=>$this->msg['title_done'], 'body'=>$body);
	}

	function data_format($key, $val) {
		$is_int = false;
		$line = '';
		if (!isset($this->user_pref[$key])) return '';

		$val = trim($val);
		if (substr($this->user_pref[$key]['form'], 0, 8) === 'textarea') {
			$val = preg_replace('/(\r\n|\r)/', "\n", $val);
		} else {
			$val = preg_replace('/[\r\n]+/', '', $val);
		}
		switch($this->user_pref[$key]['type']){
			case 'integer' :
				$val = intval($val);
				break;
			case 'integer!0' :
				$val = intval($val);
				if (! $val) $val = '';
				break;
			case 'string' :
			default :
				$val = strval($val);
		}
		return $val;
	}

	function make_user_tag($key = '') {
		if (! $key) {
			$key = mt_rand() . '_' . $this->root->userinfo['uname'];
		}
		$salt = substr(substr($key,1,2).'H.',0,2);
		$salt = strtr($salt,':;<=>?@[\]^_`','ABCDEFGabcdef');
		$salt = preg_replace('/[^\dA-Za-z]/', '.', $salt);
		return substr(crypt($key,$salt), -10);
	}
}
