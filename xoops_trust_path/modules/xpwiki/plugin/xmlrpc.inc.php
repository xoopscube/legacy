<?php
/*
 * Created on 2009/11/19 by nao-pon http://xoops.hypweb.net/
 * $Id: xmlrpc.inc.php,v 1.8 2011/11/20 15:41:31 nao-pon Exp $
 */

class xpwiki_plugin_xmlrpc extends xpwiki_plugin {

	var $op_debug = false;
	var $flickrApiKey = '';

	function plugin_xmlrpc_init() {

		$this->config['BlogPages'] = array(
		//	'blog',
			'$uname/blog'
		);

		$this->config['br2lf'] = 1;
		$this->config['striptags'] = 1;

		// Flickr API Key
		$this->flickrApiKey = '';

		// refプラグインの追加オプション
		$this->config['ref'] = ',left,around,mw:320,mh:320';

		$this->config['BlogPageTemplate'] = <<<EOD
#norelated
#noattach
#nopagecomment

* $2's blog &rsslink($1);

#block(width:200px,around,left){{
#calendar2(off)
}}

** Tag cloud

#block(round){{
#tag(0)
}}

#clear

** Recent posts

#calendar_viewer(this,5,past,notoday,contents:2)
EOD;
//'

	}

	function debug($src) {
		$file = $this->cont['CACHE_DIR'] . 'xmlrpc_debug.txt';
		file_put_contents($file, print_r($src, true), FILE_APPEND);
	}

	function plugin_xmlrpc_convert() {
		$rsd = $this->root->script . $this->root->xmlrpc_endpoint;
		$this->root->head_tags[$rsd] = '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="'.$rsd.'" />';
		return '';
	}

	function plugin_xmlrpc_action() {
		if ($this->root->use_xmlrpc) {
			if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
				if ($this->op_debug) {
					$this->debug($_GET);
				}
				return $this->rsd();
			}

			if ($this->op_debug) {
				global $HTTP_RAW_POST_DATA;
				$this->debug($HTTP_RAW_POST_DATA);
			}

			$GLOBALS['xpWikiXmlRpcObj'] =& $this;

			$this->supportedMethods = array(
				'blogger.newPost'           => 'xpwiki_xmlrpc_blogger_newPost',
				'blogger.editPost'          => 'xpwiki_xmlrpc_blogger_editPost',
				'blogger.deletePost'        => 'xpwiki_xmlrpc_blogger_deletePost',
				'blogger.getRecentPosts'    => 'xpwiki_xmlrpc_blogger_getRecentPosts',
				'blogger.getUsersBlogs'     => 'xpwiki_xmlrpc_blogger_getUsersBlogs',
				'blogger.getUserInfo'       => 'xpwiki_xmlrpc_blogger_getUserInfo',
				'metaWeblog.newPost'        => 'xpwiki_xmlrpc_metaWeblog_newPost',
				'metaWeblog.editPost'       => 'xpwiki_xmlrpc_metaWeblog_editPost',
				'metaWeblog.getPost'        => 'xpwiki_xmlrpc_metaWeblog_getPost',
				'metaWeblog.getRecentPosts' => 'xpwiki_xmlrpc_metaWeblog_getRecentPosts',
				'metaWeblog.newMediaObject' => 'xpwiki_xmlrpc_metaWeblog_newMediaObject',
				'metaWeblog.getCategories'  => 'xpwiki_xmlrpc_return_empty',
				'mt.getRecentPostTitles'    => 'xpwiki_xmlrpc_mt_getRecentPostTitles',
				'mt.getCategoryList'        => 'xpwiki_xmlrpc_return_empty',
				'mt.getPostCategories'      => 'xpwiki_xmlrpc_return_empty',
				'mt.setPostCategories'      => 'xpwiki_xmlrpc_return_true',
				'mt.supportedMethods'       => 'xpwiki_xmlrpc_mt_supportedMethods',
				'mt.getTrackbackPings'      => 'xpwiki_xmlrpc_return_empty',
				'mt.publishPost'            => 'xpwiki_xmlrpc_return_true',
				'mt.setNextScheduledPost'   => 'xpwiki_xmlrpc_return_true',
			);

			HypCommonFunc::loadClass('IXR_Server');
			$server =& new IXR_Server($this->supportedMethods);
			header('Content-Type: text/xml;charset=UTF-8');
			$this->func->clear_output_buffer();
		}
		return array('exit' => 'xmlrpc is not effective.');
	}

	function rsd() {
		$res = <<<EOD
<?xml version="1.0" ?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
    <service>
        <engineName>xpWiki</engineName>
        <engineLink>http://xoops.hypweb.net/</engineLink>
        <homePageLink>{$this->cont['ROOT_URL']}</homePageLink>
        <apis>
            <api name="blogger" preferred="false" apiLink="{$this->root->script}{$this->root->xmlrpc_endpoint}" blogID="" />
            <api name="metaWeblog" preferred="true" apiLink="{$this->root->script}{$this->root->xmlrpc_endpoint}" blogID="" />
        </apis>
    </service>
</rsd>
EOD;
		header('Content-type: text/xml');
		return array('exit' => $res);
	}

	function user_auth($uname, $pass) {
		$uname = $this->toInEnc($uname);
		$uid = $this->func->get_uid_by_uname($uname);
		$user_pref = $this->func->get_user_pref($uid);
		if (empty($user_pref['xmlrpc_auth_key']) || $user_pref['xmlrpc_auth_key'] !== $pass) {
			$uid = 0;
			$this->user_pref = array();
		} else {
			$this->user_pref = $user_pref;
		}
		$userinfo = $this->func->get_userinfo_by_id($uid);
		return 	$userinfo;
	}

	function get_blog_page($uname) {

		if (! empty($this->user_pref['xmlrpc_pages'])) {
			$pages = explode("\n", $this->user_pref['xmlrpc_pages']);
		} else {
			$pages = array();
		}

//		// Read user config (template)
//		if (! $pages) {
//			$config = new XpWikiConfig($this->xpwiki, $this->cont['PKWK_CONFIG_USER'] . '/template');
//			$table = $config->read() ? $config->get('XML-RPC') : array();
//			foreach ($table as $row) {
//				if (isset($row[1]) && strtolower(trim($row[0])) === 'myblog') {
//					$page = $this->func->strip_bracket(trim($row[1]));
//					$page = str_replace('$3', $uname, $page);
//					if ($this->check_blogpage($page)) {
//						$pages[] = $page;
//					}
//				}
//			}
//		}
//
//		$config = NULL;
//		unset($config);

		if (! $pages) {
			foreach($this->config['BlogPages'] as $page) {
				$page = str_replace('$uname', $uname, $page);
				if ($this->check_blogpage($page, true)) {
					$pages[] = $page;
				}
			}
		}

		return $pages;
	}

	function getUsersBlogs($args) {
		list($appkey, $uname, $pass) = array_pad($args, 3, '');

		$userinfo = $this->user_auth($uname, $pass);

		$res = array();
		$error = $this->get_error();
		if ($userinfo['uid']) {

			// userinfo を設定
			$this->func->set_userinfo($userinfo['uid']);
			$this->root->userinfo['ucd'] = '';
			$this->root->cookie['name']  = '';

			if ($pages = $this->get_blog_page($uname)) {

				$bname = $this->root->siteinfo['sitename'] . ' - ' . $this->root->module['title'];
				$bname = $this->toUTF8($bname);

				if (is_string($pages)) $pages = array($pages);
				foreach ($pages as $page) {
					$pgid = $this->func->get_pgid_by_name($page, true, true);
					$pagename = $this->toUTF8($page);
					$res[] = array(
						'url'      => $this->func->get_page_uri($page, true),
						'blogid'   => $pgid,
						'blogName' => $pagename . '@' . $bname
					);
				}
			} else {
				$error = $this->get_error(802);
			}
		} else {
			$error = $this->get_error(801);
		}

		return $res? $res : new IXR_Error($error[0], $error[1]);
	}

	function getUserInfo($args) {
		list($appkey, $uname, $pass) = array_pad($args, 3, '');
		$userinfo = $this->user_auth($uname, $pass);

		$res = array();
		$error = $this->get_error();
		if ($userinfo['uid']) {
			$res = array(
				'userid' => $userinfo['uname'],
				'firstname' => '',
				'lastname' => '',
				'nickname' => '',
				'email' => '',
				'url' => $this->root->script
			);
		}

		return $res? array($res) : new IXR_Error($error[0], $error[1]);
	}

	function Post($args, $mode = 'new') {
		list($pgid, $uname, $pass, $content, $publish) = array_pad($args, 5, '');
		$page = $this->func->get_name_by_pgid($pgid);

		if ($mode === 'delete') {
			$content = '';
		}

		if (! isset($content['mt_keywords'])) {
			$content['mt_keywords'] = '';
		}

		$userinfo = $this->user_auth($uname, $pass);

		$res = '';
		$error = $this->get_error();
		if ($userinfo['uid']) {

			// userinfo を設定
			$this->func->set_userinfo($userinfo['uid']);
			$this->root->userinfo['ucd'] = '';
			$this->root->cookie['name']  = '';

			if ($this->check_blogpage($page, (($mode === 'new')? TRUE : FALSE))) {
				if (is_string($content)) {
					$content['description'] = $content;
				}
				$subject = isset($content['title'])? trim($this->toInEnc($content['title'])) : '';

				// タグの抽出
				$_reg = '/#([^#]*)/';
				if (preg_match($_reg, $subject, $match)) {
					$_tag = trim($match[1]);
					if ($_tag) {
						if (! $content['mt_keywords']) {
							$content['mt_keywords'] = $_tag;
						} else {
							$content['mt_keywords'] = $this->toInEnc($content['mt_keywords']);
							$content['mt_keywords'] .= ',' . $_tag;
						}
					}
					$subject = trim(preg_replace($_reg, '', $subject, 1));
				}

				$set_data = $this->toInEnc($content['description']);
				if ($set_data || $mode === 'delete') {
					if ($mode === 'new') {
						$dateObj = isset($content['dateCreated']) ? $content['dateCreated'] : '';
						$time = $this->get_time($dateObj);

						$date = date('Y-m-d', $time);
						$page .= '/' . $date;

						$base = $page;
						$i = 1;
						while($this->func->is_page($page)) {
							$page = $base . '-' . $i++;
						}
					} else if ($mode === 'edit') {
						$dateObj = isset($content['dateCreated']) ? $content['dateCreated'] : '';
						$time = $this->get_time($dateObj);
					}

					// pginfo のキャッシュをクリア
					$this->func->get_pginfo($page, '', TRUE);

					if ($this->func->check_editable($page, FALSE, FALSE)) {

						//$this->root->post['page'] = $this->root->vars['page'] = $page;

						if ($this->config['br2lf']) {
							$set_data = preg_replace('#<br[^>]*?>#', "\n", $set_data);
						}
						if ($this->config['striptags']) {
							$set_data = preg_replace('/<img[^>]+?src=["\']'.preg_quote($this->cont['HOME_URL'].'gate.php?way=ref', '/').'[^"\'> ]+?page=([^&]+)[^"\'> ]+?src=([^&"\']+)[^>]*?>/ie', '"\n\n#ref(".rawurldecode("$1")."/".rawurldecode("$2")."'.$this->config['ref'].')\n\n"', $set_data);
							$set_data = preg_replace('/<img[^>]+?src=["\']([^"\'> ]+)[^>]*?>/i', "\n\n#ref($1".$this->config['ref'].");\n\n", $set_data);
							$set_data = strip_tags($set_data);
							$set_data = $this->func->unhtmlspecialchars($set_data);
						}

						if (strpos($set_data, '// flickr description')) {
							// change image size 500 to 1024
							$set_data = preg_replace('#(http://farm\d+\.static\.?flickr\.com/\d+/\d+_[a-f0-9]+)\.jpg#', '$1_b.jpg', $set_data);

							// get description
							if (preg_match('#// *flickr description start(.+?)// *flickr description end#is', $set_data, $_match)) {
								$description = trim($_match[1]);
								$id = '';
								if (preg_match('#http://farm\d+\.static\.?flickr\.com/\d+/(\d+)_#', $set_data, $_match)) {
									$id = $_match[1];
								}
								if ($id && (!$description || $description === $subject)) {
									if ($description = $this->getDescriptionFromFlickr($id)) {
										$set_data = preg_replace('#// *flickr description start.+?// *flickr description end#is', $description, $set_data);
									}
								}
							}

							//get tag
							if ($tags = $this->getTagsFromFlickr($id)) {
								$content['mt_keywords'] .= ($content['mt_keywords']? ',' : '') . join(',', $tags);
							}
						}

						$set_data .= "\n#clear\n";

						// 改行を調整
						$set_data = str_replace("\r", '', $set_data);
						$set_data = preg_replace('/\n{2,}/', "\n\n", $set_data);

						if ($mode === 'new') {
							// テンプレート読み込み
							if ($this->root->auto_template_rules) {
								$auto_template_rules = array();
								foreach($this->root->auto_template_rules as $reg => $rules) {
									if (! $rules) continue;
									if (! is_array($rules)) {
										$rules = array($rules);
									}
									$_rules = array();
									foreach($rules as $rule) {
										$_rules[] = str_replace('template', 'template_m', $rule);
									}
									$auto_template_rules[$reg] = $_rules;
								}
							} else {
								$auto_template_rules = NULL;
							}

							$page_data = $this->func->auto_template($page, $auto_template_rules);

							if (strpos($page_data, '__TITLE__') !== false) {
								$page_data = str_replace('__TITLE__', $subject? $subject : 'notitle', $page_data);
							} else {
								if ($subject) $set_data = "* $subject\n" . $set_data;
							}

							$set_data = rtrim($set_data) . "\n\n";

							if (preg_match("/\/\/ Moblog Body\n/",$page_data)) {
								$page_data = preg_split("/\/\/ Moblog Body[ \t]*\n/",$page_data,2);
								$save_data = rtrim($page_data[0]) . "\n\n" . $set_data . "// Moblog Body\n" . $page_data[1];
							} else 	{
								$save_data = $page_data . "\n" . $set_data . "// Moblog Body\n";
							}

							if (! $this->root->pagename_num2str && $subject) {
								$this->root->rtf['esummary'] = $subject;
							}
							if ($this->user_pref['xmlrpc_to_twitter']) {
								$this->root->rtf['twitter_update'] = '1';
							}

						} else {
							$save_data = $set_data;
						}

						if (! empty($content['mt_keywords'])) {
							$this->set_tags($save_data, $page, $content['mt_keywords']);
						}

						$this->func->page_write($page, $save_data);

						if ($mode === 'edit') {
							$this->func->touch_page($page, $time);
						}
						if ($mode === 'new') {
							$res = $this->toUTF8($page);
						} else {
							$res = true;
						}
					} else {
						$error = $this->get_error(807);
					}
				} else {
					$error = $this->get_error(804);
				}
			} else {
				$error = $this->get_error(803);
			}
		} else {
			$error = $this->get_error(801);
		}

		return $res? $res : new IXR_Error($error[0], $error[1]);

	}

	function getPost($args) {
		list($pgid, $uname, $pass) = array_pad($args, 3, '');
		$page = $this->func->get_name_by_pgid($pgid);

		$userinfo = $this->user_auth($uname, $pass);

		$res = '';
		$error = $this->get_error();
		if ($userinfo['uid']) {
			if ($this->func->check_editable($page, FALSE, FALSE)) {
				$link = $this->func->get_page_uri($page, TRUE);
				$res = $this->get_item($page, $uname);
			} else {
				$error = $this->get_error(807);
			}
		} else {
			$error = $this->get_error(801);
		}
		return $res? $res : new IXR_Error($error[0], $error[1]);
	}

	function getRecentPosts($args, $type='') {
		list($pgid, $uname, $pass, $max) = array_pad($args, 4, '');
		$page = $this->func->get_name_by_pgid($pgid);

		$userinfo = $this->user_auth($uname, $pass);

		$max = intval($max);

		$res = '';
		$error = $this->get_error();
		if ($userinfo['uid']) {
			$pages = $this->func->get_existpages(FALSE, $page . '/', array('where' => 'uid=\''.$userinfo['uid'].'\'', 'limit' => $max, 'order' => ' ORDER BY editedtime DESC'));
			$res = array();
			foreach($pages as $_page) {
				$res[] = $this->get_item($_page, $uname, $type);
			}
		} else {
			$error = $this->get_error(801);
		}
		return $res? $res : new IXR_Error($error[0], $error[1]);
	}

	function supportedMethods() {
		return array_keys($this->supportedMethods);
	}

	function newMediaObject($args) {
		list($pgid, $uname, $pass, $file) = array_pad($args, 4, '');
		//$page = $this->toInEnc($page);
		$page = $this->func->get_name_by_pgid($pgid);

		$userinfo = $this->user_auth($uname, $pass);

		$res = '';
		$error = $this->get_error();
		if ($userinfo['uid']) {
			// userinfo を設定
			$this->func->set_userinfo($userinfo['uid']);
			$this->root->userinfo['ucd'] = '';
			$this->root->cookie['name']  = '';

			// pginfo のキャッシュをクリア
			$this->func->get_pginfo($page, '', TRUE);

			if ($this->func->check_editable($page, FALSE, FALSE) && $this->func->exist_plugin('attach')) {
				if (! empty($file['bits']) && ! empty($file['name'])) {
					$tmp = $file['bits'];
					$filename = $this->toInEnc($file['name']);

					$save_file = tempnam(rtrim($this->cont['UPLOAD_DIR'], '/'), 'xmlrpc');
					chmod($save_file, 0606);
					if (file_put_contents($save_file, $tmp, LOCK_EX)) {
						// ページが無ければ空ページを作成
						if (!$this->func->is_page($page)) {
							$this->func->make_empty_page($page, false);
						}
						$attach = $this->func->get_plugin_instance('attach');
						$attach_res = $attach->do_upload($page,$filename,$save_file,false,null,true);
						if ($attach_res['result']) {
							$res['url'] = $this->cont['HOME_URL'] . 'gate.php?way=ref&_nodos&_noumb&page=' . rawurlencode($page) .
							       '&src=' . rawurlencode($attach_res['name']); // Show its filename at the last
						} else {
							$error = $this->get_error(807);
						}
					} else {
						$error = $this->get_error(807);
					}
				} else {
					$error = $this->get_error(804);
				}
			} else {
				$error = $this->get_error(807);
			}
		} else {
			$error = $this->get_error(801);
		}
		return $res? $res : new IXR_Error($error[0], $error[1]);
	}

	function return_empty($args) {
		return array();
	}

	function get_item($page, $uname=NULL, $type='') {
		if (is_null($uname)) {
			$pginfo = $this->get_pginfo($page);
			$uname = $this->func->unhtmlspecialchars($pginfo('uname'));
		}
		$res = array(
			'userid' => $this->toUTF8($uname),
			'dateCreated' => $this->make_iso8601(filemtime($this->func->get_filename($page))),
			'postid' => $this->toUTF8($page),
			'title' => $this->toUTF8($this->func->get_heading($page))
		);
		if ($type !== 'title') {
			$link = $this->func->get_page_uri($page, TRUE);
			$src = $this->func->remove_pginfo($this->func->get_source($page, TRUE, TRUE));
			$description = $this->toUTF8($src);
			$res['description'] = $description;
			$res['link'] = $link;
			$res['permaLink'] = $link;
			$res['mt_keywords'] = $this->toUTF8($this->get_tags($src, $page));

		}
		return $res;
	}

	function check_blogpage($page, $make=FALSE) {
		if (! $this->func->is_pagename($page) || ! $this->func->check_editable($page . '/a', FALSE, FALSE)) {
			return FALSE;
		}
		if ($make && ! $this->func->is_page($page)) {
			// pginfo のキャッシュをクリア
			$this->func->get_pginfo($page, '', TRUE);

			if (! $src = $this->func->auto_template($page)) {
				$src = $this->config['BlogPageTemplate'];
			}
			$this->func->page_write($page, $src);
		}
		return TRUE;
	}

	function get_time($obj) {
		if (is_object($obj) && is_a($obj, 'IXR_Date')) {
			$time = $obj->getTimestamp() + date('Z');
		} else {
			$time = time();
		}
		return $time;
	}

	function make_iso8601($time) {
		return substr(str_replace('-', '', gmdate(DATE_ISO8601, $time)), 0, 17) . 'Z';
	}

	function get_tags($postdata, $page) {
		$params = '';
		$p_tag = $this->func->get_plugin_instance('tag');
		if (is_object($p_tag)) {
			$params = $p_tag->get_tags($postdata, $page);
		}
		return $params;
	}

	function set_tags(& $postdata, $page, $tags) {
		$p_tag = $this->func->get_plugin_instance('tag');
		if (is_object($p_tag)) {
			$p_tag->set_tags($postdata, $page, $tags);
		}
	}

	function get_error($code = 0) {
		$code = intval($code);
		switch($code) {
			case 801:
				$msg = 'Login Error.';
				break;
			case 802:
				$msg = 'No Such Blog.';
				break;
			case 803:
				$msg = 'Page name is wrong.';
				break;
			case 804:
				$msg = 'Cannot add Empty Items.';
				break;
			case 806:
				$msg = 'No Such Item.';
				break;
			case 807:
				$msg = 'Not Allowed to Alter Item.';
				break;
			default:
				$code = 999;
				$msg = 'Unknown error.';
		}
		return array($code, $msg);
	}

	function toUTF8($str) {
		return mb_convert_encoding($str, 'UTF-8', $this->cont['SOURCE_ENCODING']);
	}

	function toInEnc($str) {
		return mb_convert_encoding($str, $this->cont['SOURCE_ENCODING'], 'UTF-8');
	}

	function getDescriptionFromFlickr($id) {
		if ($data = $this->getInfoFromFlickr($id)) {
			if ($data['stat'] === 'ok') {
				return $this->toInEnc($data['photo']['description']['_content']);
			}
		}
		return '';
	}

	function getTagsFromFlickr($id) {
		$tags = array();
		if ($data = $this->getInfoFromFlickr($id)) {
			if ($data['stat'] === 'ok') {
				if (! empty($data['photo']['tags']['tag'])) {
					foreach($data['photo']['tags']['tag'] as $tagArg) {
						if (! $tagArg['machine_tag']) {
							$tags[] =  $this->toInEnc($tagArg['raw']);
						}
					}
				}
			}
		}
		return $tags;
	}

	function getInfoFromFlickr($id, $retry = 3, $interval = 3) {
		static $rets = array();
		if (isset($rets[$id])) {
			return $rets[$id];
		}
		$ret[$id] = false;
		if ($this->flickrApiKey) {
			$i = 0;
			while(! $ret[$id] && $i++ < $retry) {
				$url = 'http://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key='.$this->flickrApiKey.'&photo_id='.$id.'&format=php_serial';
				$res = $this->func->http_request($url);
				if ($res['rc'] == 200) {
					$ret[$id] = @ unserialize($res['data']);
				}
				if (!$ret[$id] && $interval) sleep($interval);
			}
		}
		return $ret[$id];
	}
}

function xpwiki_xmlrpc_blogger_newPost($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	array_shift($args);
	return $p->Post($args);
}

function xpwiki_xmlrpc_blogger_editPost($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	array_shift($args);
	return $p->Post($args, 'edit');
}

function xpwiki_xmlrpc_blogger_deletePost($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	array_shift($args);
	return $p->Post($args, 'delete');
}

function xpwiki_xmlrpc_blogger_getRecentPosts($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	array_shift($args);
	return $p->getRecentPosts($args, 'blogger');
}

function xpwiki_xmlrpc_blogger_getUsersBlogs($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->getUsersBlogs($args);
}

function xpwiki_xmlrpc_blogger_getUserInfo($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->getUserInfo($args);
}

function xpwiki_xmlrpc_metaWeblog_newPost($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->Post($args);
}

function xpwiki_xmlrpc_metaWeblog_editPost($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->Post($args, 'edit');
}

function xpwiki_xmlrpc_metaWeblog_getPost($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->getPost($args);
}

function xpwiki_xmlrpc_metaWeblog_getRecentPosts($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->getRecentPosts($args);
}

function xpwiki_xmlrpc_metaWeblog_newMediaObject($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->newMediaObject($args);
}

function xpwiki_xmlrpc_mt_getRecentPostTitles($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->getRecentPosts($args, 'title');
}

function xpwiki_xmlrpc_mt_supportedMethods() {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->supportedMethods();
}

function xpwiki_xmlrpc_return_empty($args) {
	$p =& $GLOBALS['xpWikiXmlRpcObj'];
	return $p->return_empty($args);
}

function xpwiki_xmlrpc_return_true($args) {
	return TRUE;
}