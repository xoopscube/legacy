<?php
//
// Created on 2006/10/11 by nao-pon http://hypweb.net/
// $Id: xoops_wrapper.php,v 1.63 2012/02/20 01:58:24 nao-pon Exp $
//
class XpWikiXoopsWrapper extends XpWikiBackupFunc {

	function & get_db_connection () {
		$db =& XoopsDatabaseFactory::getDatabaseConnection();
		return $db;
	}

	function set_moduleinfo () {

		$this->cont['ROOT_PATH'] = XOOPS_ROOT_PATH . "/";
		$this->cont['MODULE_PATH'] = XOOPS_ROOT_PATH . "/modules/";
		$this->cont['ROOT_URL']  = XOOPS_URL . "/";
		$this->cont['MODULE_URL'] = XOOPS_URL . "/modules/";
		$this->cont['TRUST_PATH']  = XOOPS_TRUST_PATH . "/";

		$module_handler =& xoops_gethandler('module');
		if ($XoopsModule =& $module_handler->getByDirname($this->root->mydirname)) {
			$config_handler =& xoops_gethandler('config');

			$this->root->module = $XoopsModule->getInfo();
			$this->root->module['title'] = $XoopsModule->getVar('name');
			$this->root->module['mid']   = $XoopsModule->getVar('mid');
			$this->root->module['config'] =& $config_handler->getConfigsByCat(0, $this->root->module['mid']);
			$this->root->module['platform'] = "xoops";
			$this->root->module['version'] = XPWIKI_VERSION;

			$moduleperm_handler =& xoops_gethandler('groupperm');
			global $xoopsUser;
			$this->root->module['checkRight'] = ($moduleperm_handler->checkRight('module_read', $this->root->module['mid'], (is_object($xoopsUser)? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS)));

		} else {
			// not installed
			$this->root->module = array();
			$this->root->module['title'] = $this->root->mydirname;
			$this->root->module['mid']   = 0;
			$this->root->module['config'] = NULL;
			$this->root->module['platform'] = 'standalone';
			$this->root->module['checkRight'] = false;
		}

		$this->root->enable_pagecomment = TRUE;
		if (empty($this->root->module['config']['comment_forum_id']) ||
			!is_dir(XOOPS_ROOT_PATH . '/modules/' . $this->root->module['config']['comment_dirname'])) {
			$this->root->enable_pagecomment = FALSE;
		}
	}

	function set_siteinfo () {

		$config_handler =& xoops_gethandler('config');
		$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);

		$this->root->siteinfo['rooturl'] = XOOPS_URL.'/';
		$this->root->siteinfo['loginurl'] = XOOPS_URL.'/user.php';
		$this->root->siteinfo['sitename'] = $xoopsConfig['sitename'];
		$this->root->siteinfo['anonymous'] = $xoopsConfig['anonymous'];

		$parsed_url = parse_url($this->root->siteinfo['rooturl']);
		$this->root->siteinfo['host'] = $parsed_url['scheme'].'://'.$parsed_url['host'].(isset($parsed_url['port'])? ':' . $parsed_url['port'] : '');
	}

	function set_userinfo ($uid = NULL) {

		static $cache; // cache for renderer

		if (is_null($uid) && isset($cache[$this->root->mydirname])) {
			$this->root->userinfo = $cache[$this->root->mydirname];
		}

		if (is_null($uid) || ! $uid) {
			global $xoopsUser;
			$user = $xoopsUser;
		} else {
			$module_handler =& xoops_gethandler('module');
			$XoopsModule =& $module_handler->getByDirname($this->root->mydirname);
			$user_handler =& xoops_gethandler('user');
			$user =& $user_handler->get( $uid );
		}

		if (is_object($user))
		{
			$this->root->userinfo['admin'] = $user->isAdmin($this->root->module['mid']);
			$this->root->userinfo['uid'] = (int)$user->uid();
			$this->root->userinfo['email'] = $user->email();
			$this->root->userinfo['uname'] = $user->uname('n');
			$this->root->userinfo['uname_s'] = htmlspecialchars($this->root->userinfo['uname']);
			$this->root->userinfo['name'] = $user->name('s');
			$this->root->userinfo['gids'] = $user->getGroups();
		}
		else
		{
			parent::set_userinfo();
			$this->root->userinfo['gids'] = array( XOOPS_GROUP_ANONYMOUS );
		}

		if (is_null($uid)) $cache[$this->root->mydirname] = $this->root->userinfo;
	}

	function get_userinfo_by_id ($uid = 0) {
		static $cache = array();
		if (isset($cache[$uid])) return $cache[$uid];

		$uid = intval($uid);
		$config_handler =& xoops_gethandler('config');
		$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);

		$result = parent::get_userinfo_by_id($uid, $xoopsConfig['anonymous']);

		if ($uid) {
			$module_handler =& xoops_gethandler('module');
			$XoopsModule =& $module_handler->getByDirname($this->root->mydirname);
			$user_handler =& xoops_gethandler('user');
			$user =& $user_handler->get( $uid );
			if (is_object($user)) {
				$result['admin'] = $user->isAdmin($XoopsModule->mid());
				$result['email'] = $user->email();
				$result['uname'] = $user->uname('n');
				$result['uname_s'] = htmlspecialchars($result['uname']);
				$result['uid'] = $uid;
				$result['gids'] = $user->getGroups();
			}
		} else {
			$result['gids'] = array( XOOPS_GROUP_ANONYMOUS );
		}
		$cache[$uid] = $result;
		return $result;
	}

	function get_uid_by_uname($uname){
		$uid = 0;
		$query = "SELECT `uid` FROM `".XOOPS_DB_PREFIX."_users` WHERE uname='" . addslashes($uname) . "' LIMIT 1";
		if ($result = $this->xpwiki->db->query($query)) {
			 list($uid) = $this->xpwiki->db->fetchRow($result);
		}
		return $uid;
	}

	function user_auth($uname, $pass) {
		$member_handler =& xoops_gethandler('member');
		if ($user =& $member_handler->loginUser(addslashes($uname), addslashes($pass))) {
			$uid = $user->getVar('uid');
		} else {
			$uid = 0;
		}
		return $this->get_userinfo_by_id($uid);
	}

	function check_editable($page, $auth_flag = TRUE, $exit_flag = TRUE)
	{
		//	global $script, $_title_cannotedit, $_msg_unfreeze;

		if ($this->is_editable($page, TRUE) && $this->edit_auth($page, $auth_flag, $exit_flag)) {
			// Editable
			return TRUE;
		} else {
			// Not editable
			if ($exit_flag === FALSE) {
				return FALSE; // Without exit
			} else {
				// With exit
				$body = $title = str_replace('$1',
					htmlspecialchars($this->strip_bracket($page)), $this->root->_title_cannotedit);
				if ($this->is_freeze($page))
					$body .= '(<a href="' . $this->root->script . '?cmd=unfreeze&amp;page=' .
						rawurlencode($page) . '">' . $this->root->_msg_unfreeze . '</a>)';

				redirect_header($this->root->script."?".rawurlencode($page), 3, $body);
				exit;
			}
		}
	}

	function get_zonetime () {
		$config_handler =& xoops_gethandler('config');
		$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
		return $xoopsConfig['default_TZ'] * 3600; //default_TZ
	}

	function get_lang ($default) {
		if (defined('_LANGCODE')) {
			return _LANGCODE;
		}
		$config_handler =& xoops_gethandler('config');
		$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
		$language = (empty($xoopsConfig['language_origin']))? $xoopsConfig['language'] : $xoopsConfig['language_origin'];
		$_language = preg_replace('/^(.*)(?:utf|_utf8)$/i', '$1', $language);
		switch (strtolower($_language)) {
			case 'japanese' :
			case 'ja' :
				return 'ja';
			case 'danish':
				return 'da';
			case 'english' :
				return 'en';
			case 'french':
				return 'fr';
			case 'german':
				return 'de';
			case 'italian':
				return 'it';
			case 'nederlands':
				return 'nl';
			case 'schinese':
				return 'zh-ch';
			case 'spanish':
				return 'es';
			case 'tchinese':
				return 'zh-tw';
			default:
				return $default;
		}
	}

	function get_setlang ($default) {
		if (defined('EASIESTML_LANGS')) return 'easiestml_lang'; // GIJOE's EMLH
		else if (defined('SYSUTIL_ML_PARAM_NAME')) return SYSUTIL_ML_PARAM_NAME; // nobunobu's sysutil
		else if (defined('CUBE_UTILS_ML_PARAM_NAME')) return CUBE_UTILS_ML_PARAM_NAME; // nobunobu's cubeUtils
		else return $default;
	}

	function get_setlang_c ($default) {
		if (defined('EASIESTML_LANGS')) return 'easiestml_lang'; // GIJOE's EMLH
		else if (defined('SYSUTIL_ML_PARAM_NAME')) return SYSUTIL_ML_PARAM_NAME; // nobunobu's sysutil
		else if (defined('CUBE_UTILS_ML_PARAM_NAME')) return CUBE_UTILS_ML_PARAM_NAME; // nobunobu's cubeUtils
		else return $default;
	}

	function get_content_charset () {
		return strtoupper(_CHARSET);
	}

	function pkwk_mail_notify($subject, $message, $footer = array())
	{
		static $_to, $_headers, $_after_pop;

		// Init and lock
		if (! isset($_to[$this->xpwiki->pid])) {
			if (! $this->cont['PKWK_OPTIMISE']) {
				// Validation check
				$func = 'pkwk_mail_notify(): ';
				$mail_regex   = '/[^@]+@[^@]{1,}\.[^@]{2,}/';
				if ($this->root->notify_header != '') {
					$header_regex = "/\A(?:\r\n|\r|\n)|\r\n\r\n/";
					if (preg_match($header_regex, $this->root->notify_header))
						die($func . 'Invalid $this->root->notify_header');
					if (preg_match('/^From:/im', $this->root->notify_header))
						die($func . 'Redundant \'From:\' in $this->root->notify_header');
				}
			}

			$_to[$this->xpwiki->pid]      = $this->root->notify_to;
			$_headers[$this->xpwiki->pid] =
				'X-Mailer: xpWiki/' . $this->cont['S_VERSION'] .
				' PHP/' . phpversion() . "\r\n";

			// Additional header(s) by admin
			if ($this->root->notify_header != '') $_headers[$this->xpwiki->pid] .= "\r\n" . $this->root->notify_header;
		}

		if ($subject == '' || ($message == '' && empty($footer))) return FALSE;

		// Subject:
		if (isset($footer['PAGE'])) $subject = str_replace('$page', $footer['PAGE'], $subject);

		// Footer
		$footer['UID'] = $this->root->userinfo['uid'];
		$footer['UNAME'] = $this->root->userinfo['uname'] . ' [' . $this->root->userinfo['ucd'] . ']';
		if (isset($footer['REMOTE_ADDR'])) $footer['REMOTE_ADDR'] = & $_SERVER['REMOTE_ADDR'];
		if (isset($footer['USER_AGENT']))
			$footer['USER_AGENT']  = '(' . $this->cont['UA_PROFILE'] . ') ' . $this->cont['UA_NAME'] . '/' . $this->cont['UA_VERS'];
		if (! empty($footer)) {
			$_footer = '';
			if ($message != '') $_footer = "\n" . str_repeat('-', 30) . "\n";
			foreach($footer as $key => $value)
				$_footer .= $key . ': ' . $value . "\n";
			$message .= $_footer;
		}

		$config_handler =& xoops_gethandler('config');
		$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);

		$xoopsMailer =& getMailer();
		$xoopsMailer->useMail();
		$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
		$xoopsMailer->setFromName($xoopsConfig['sitename']);
		$xoopsMailer->setSubject($subject);
		$xoopsMailer->setBody($message);
		$xoopsMailer->setToEmails($xoopsConfig['adminmail']);
		$xoopsMailer->headers = explode("\r\n",rtrim($_headers[$this->xpwiki->pid]));
		$xoopsMailer->send();
		$xoopsMailer->reset();

		return true;
	}

	// ユーザーが所属するグループIDを得る
	function get_mygroups($uid = NULL){
		if (is_null($uid)) $uid = $this->root->userinfo['uid'];
		if ($uid) {
			$XM =& xoops_gethandler('member');
			return $XM->getGroupsByUser($uid);
		} else {
			return array( XOOPS_GROUP_ANONYMOUS );
		}
	}

	// グループ一覧を得る
	function get_group_list()
	{
		$XM =& xoops_gethandler('member');
		$ret = $XM->getGroupList();
		// 管理者グループを除外
		$moduleperm_handler =& xoops_gethandler( 'groupperm' );
		foreach (array_keys($ret) as $id) {
			if($moduleperm_handler->checkRight('module_admin', $this->root->module['mid'], $id)) {
				unset($ret[$id]);
			}
		}
		return $ret;
	}

	// グループ名を得る
	function get_groupname ($id) {
		static $list;

		if (strpos($id, '&') !== FALSE) {
			$ret = array();
			foreach(explode('&', $id) as $_id) {
				$_id = intval(trim($_id));
				$ret[] = $this->get_groupname($_id);
			}
			return join(', ', $ret);
		}

		if (! $list) {
			$XM =& xoops_gethandler('member');
			$list = $XM->getGroupList();
		}
		if (isset($list[$id])) {
			return htmlspecialchars($list[$id]);
		} else {
			return '';
		}
	}

	// ユーザー名を得る
	function getUnameFromId ($uid) {
		static $user = NULL;
		if (is_null($user)) {
			$user = new XoopsUser();
		}
		return $user->getUnameFromId($uid);
	}

	// ユーザー情報ページへのリンクを作成
	function make_userlink ($uid, $uname = '') {
		if (strpos($uid, '&') !== FALSE) {
			$ret = array();
			foreach(explode('&', $uid) as $_uid) {
				$_uid = intval(trim($_uid));
				$ret[] = $this->make_userlink($_uid);
			}
			return join(', ', $ret);
		}

		if (! $uname) {
			$user = $this->get_userinfo_by_id($uid);
			$uname = $user['uname'];
		}
		$uname = htmlspecialchars($uname);

		if (! $uid) {
			return $uname;
		} else {
			return '<a href="'.XOOPS_URL.'/userinfo.php?uid='.$uid.'">' . $uname . '</a>';
		}
	}

	// 管理者権限があるか調べる
	function check_admin ($uid = NULL) {
		if (is_null($uid)) $uid = $this->root->userinfo['uid'];

		if (!$uid) return FALSE;

		$module_handler =& xoops_gethandler('module');
		$member_handler =& xoops_gethandler('member');

		$XoopsModule =& $module_handler->getByDirname($this->root->mydirname);
		$xoopsUser =& $member_handler->getUser($uid);
		if (! is_object($xoopsUser)) return FALSE;
		return $xoopsUser->isAdmin($XoopsModule->mid());
	}

	// 管理者権限があるか調べる(groupid)
	function check_admin_group ($gid = 0) {
		if (!$gid) return FALSE;

		$module_handler =& xoops_gethandler('module');
		$moduleperm_handler =& xoops_gethandler('groupperm');

		$XoopsModule =& $module_handler->getByDirname($this->root->mydirname);
		return $moduleperm_handler->checkRight('module_admin', $XoopsModule->mid(), $gid);
	}

	// 最終更新者名を得る
	function get_lasteditor($pginfo, $withlink = TRUE, $withucd = TRUE) {

		if ($pginfo['lastuid']) {
			if ($withlink) {
				$lasteditor = '<a href="'.XOOPS_URL.'/userinfo.php?uid='.$pginfo['lastuid'].'">' . $pginfo['lastuname'] . '</a>';
			} else {
				$lasteditor = $pginfo['lastuname'];
			}
		} else {
			if ($withucd) {
				$lasteditor = $pginfo['lastuname']. ($pginfo['lastucd']? '['.$pginfo['lastucd'].']' : '');
			} else {
				$lasteditor = $pginfo['lastuname'];
			}
		}
		return $lasteditor;
	}
	// ページコメント取得
	function get_page_comments ($page) {

		if (!$this->root->allow_pagecomment) return '';

		$pgid = $this->get_pgid_by_name($page);
		if (!$pgid) return '';

		require_once XOOPS_ROOT_PATH.'/class/template.php';
		$tpl =& new XoopsTpl();
		// assign
		$tpl->assign(
			array(
				'mod_config' => $this->root->module['config'] ,
				'mydirname'  => $this->root->mydirname,
				'content'    => array (
								'id' => $pgid,
								'subject' => $page,
							),
			)
		);
		return $tpl->fetch( 'db:'.$this->root->mydirname.'_main_d3comment.html' ) ;
	}

	// ページコメント件数取得
	function count_page_comments ($page) {
		if (!$this->root->allow_pagecomment) return 0;

		$pgid = $this->get_pgid_by_name($page);
		if (!$pgid) return 0;

		$count = 0;
		$sql = "SELECT COUNT(p.topic_id) FROM ".$this->xpwiki->db->prefix($this->root->module['config']['comment_dirname']."_posts")." p INNER JOIN ".$this->xpwiki->db->prefix($this->root->module['config']['comment_dirname']."_topics")." t ON p.topic_id = t.topic_id WHERE t.forum_id={$this->root->module['config']['comment_forum_id']} AND ! t.topic_invisible AND topic_external_link_id=$pgid" ;
		if( $trs = $this->xpwiki->db->query( $sql ) ) {
			list( $count ) = $this->xpwiki->db->fetchRow( $trs ) ;
		}
		return $count;
	}

	// リダイレクト
	function redirect_header($url, $wait = 3, $title = '', $addredirect = true) {
		$url = $this->href_give_session_id($url);
		if ($this->root->viewmode === 'popup') {
			$url .= (strpos($url, '?')? '&' : '?') . 'popup=1';
		}
		redirect_header($url, $wait, $title, $addredirect);
		exit;
	}

	// 追加 フェイスマーク 取得
	function get_extra_facemark() {
		$facemarks = array();
		$sql = 'SELECT * FROM ' . $this->xpwiki->db->prefix('smiles');
		if ($result = $this->xpwiki->db->query($sql)) {
			while( $row = $this->xpwiki->db->fetchArray($result) ) {
				$code = preg_quote($row['code'], '/');
				$code = '\s(' . $code . ')';
				$facemarks[$code] = ' <img alt="$1" src="'.XOOPS_URL.'/uploads/' . $row['smile_url'] . '" />';
				// for Wiki Helper
				$full = ($row['display'])? '' : '*';
				$this->root->wikihelper_facemarks[$row['code']] = $full . XOOPS_URL.'/uploads/' . $row['smile_url'];
			}
		}
		return $facemarks;
	}

	// 通知イベント
	function system_notification( $page, $category , $item_id , $event , $extra_tags=array() , $user_list=array() , $omit_user_id=null )
	{
		//global $xoopsModule , $xoopsConfig , $mydirname , $mydirpath , $mytrustdirname , $mytrustdirpath ;
		// RMV-NOTIFY
		include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
		include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

		$config_handler =& xoops_gethandler('config');
		$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
		$mid = $this->root->module['mid'];

		// Check if event is enabled
		$config_handler =& xoops_gethandler('config');
		$mod_config =& $config_handler->getConfigsByCat(0,$mid);
		if (empty($mod_config['notification_enabled'])) {
			return false;
		}
		$category_info =& notificationCategoryInfo ($category, $mid);
		$event_info =& notificationEventInfo ($category, $event, $mid);
		if (!in_array(notificationGenerateConfig($category_info,$event_info,'option_name'),$mod_config['notification_events']) && empty($event_info['invisible'])) {
			return false;
		}
		if (is_null($omit_user_id)) {
			$omit_user_id = $this->root->userinfo['uid'];
		}
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('not_modid', intval($mid)));
		$criteria->add(new Criteria('not_category', $category));
		$criteria->add(new Criteria('not_itemid', intval($item_id)));
		$criteria->add(new Criteria('not_event', $event));
		$mode_criteria = new CriteriaCompo();
		$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDALWAYS), 'OR');
		$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE), 'OR');
		$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT), 'OR');
		$criteria->add($mode_criteria);
		if (!empty($user_list)) {
			$user_criteria = new CriteriaCompo();
			foreach ($user_list as $user) {
				$user_criteria->add (new Criteria('not_uid', $user), 'OR');
			}
			$criteria->add($user_criteria);
		}
		$notification_handler =& xoops_gethandler('notification') ;
		$notifications =& $notification_handler->getObjects($criteria);
		if (empty($notifications)) {
			return;
		}

		// language file
		$language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ;
		if( is_dir( $this->root->mydirpath."/language/$language/mail_template" ) ) {
			// user customized language file
			$mail_template_dir = $this->root->mydirpath."/language/$language/mail_template/" ;
		} else if( is_dir( $this->root->mytrustdirpath."/language/$language/mail_template" ) ) {
			// default language file
			$mail_template_dir = $this->root->mytrustdirpath."/language/$language/mail_template/";
		} else {
			// fallback english
			$mail_template_dir = $this->root->mytrustdirpath."/language/english/mail_template/";
		}

		// Add some tag substitutions here
		$tags = array();
		// {X_ITEM_NAME} {X_ITEM_URL} {X_ITEM_TYPE} from lookup_func are disabled
		$tags['X_MODULE'] = $this->root->module['name'];
		$tags['X_MODULE_URL'] = $this->root->script;
		$tags['X_NOTIFY_CATEGORY'] = $category;
		$tags['X_NOTIFY_EVENT'] = $event;

		$template = $event_info['mail_template'] . '.tpl';
		$subject = $event_info['mail_subject'];

		foreach ($notifications as $notification) {
			if (empty($omit_user_id) || $notification->getVar('not_uid') != $omit_user_id) {
				// 表示権限チェック
				if ($this->check_readable_page($page, false, false, $notification->getVar('not_uid'))) {
					// user-specific tags
					//$tags['X_UNSUBSCRIBE_URL'] = 'TODO';
					// TODO: don't show unsubscribe link if it is 'one-time' ??
					$tags['X_UNSUBSCRIBE_URL'] = XOOPS_URL . '/notifications.php';
					$tags = array_merge ($tags, $extra_tags);

					$notification->notifyUser($mail_template_dir, $template, $subject, $tags);
				}
			}
		}
	}


	function get_notification_select ($pgid = null) {
		static $done;

		if (!$this->root->userinfo['uid'] || isset($done[$this->root->mydirname])) return '';

		$done[$this->root->mydirname] = true;

		require_once XOOPS_ROOT_PATH.'/class/template.php';

		$xoopsTpl =& new XoopsTpl();

		if (function_exists('LegacyRender_smartyfunction_notifications_select')) {
			$xoopsTpl->register_function("legacy_notifications_select", "LegacyRender_smartyfunction_notifications_select");
		} else {
			$member_handler =& xoops_gethandler('member');
			$xoopsUser =& $member_handler->getUser($this->root->userinfo['uid']);
			$module_handler =& xoops_gethandler('module');
			$xoopsModule =& $module_handler->getByDirname($this->root->mydirname);
			$config_handler =& xoops_gethandler('config');
			$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
			$xoopsTpl->assign('xoops_url', XOOPS_URL);
			include XOOPS_ROOT_PATH . '/include/notification_select.php';
		}

		$ret = $xoopsTpl->fetch( 'db:system_notification_select.html' );

		$page = (is_null($pgid))? $this->root->vars['page'] : $this->get_name_by_pgid($pgid);
		$pages = array_pad(explode('/', $page), 2, '');
		$from = array(
			constant('_MI_'.strtoupper($this->root->mydirname).'_NOTCAT_REPLASE2MODULENAME'),
			constant('_MI_'.strtoupper($this->root->mydirname).'_NOTCAT_REPLASE2FIRSTLEV'),
			constant('_MI_'.strtoupper($this->root->mydirname).'_NOTCAT_REPLASE2SECONDLEV'),
			//constant('_MI_'.strtoupper($this->root->mydirname).'_NOTCAT_REPLASE2PAGENAME'),
		);
		$to = array(
			$this->root->module['title'],
			$pages[0],
			$pages[0].' / '.$pages[1],
			//$page,
		);
		return (empty($ret))? '' : str_replace($from, $to, $ret);
	}

	function onPageWriteBefore ($page, $postdata, $notimestamp, $mode, $deletecache) {
		// Update Post Count
		$uid = 0;
		if ($this->root->xoops_post_count_up && $mode === 'insert') {
			$uid = $this->root->userinfo['uid'];
			$count = 1;
		}
		if ($this->root->xoops_post_count_down && $mode === 'delete') {
			$uid = $this->get_pg_auther($page);
			$count = -1;
		}
		if ($uid) {
			$member_handler =& xoops_gethandler('member');
			$user =& $member_handler->getUser($uid);
			if (is_object($user)) {
				$member_handler->updateUserByField($user, 'posts', $user->getVar('posts') + $count);
			}
			if ($GLOBALS['xoopsUser'] !== $user) {
				$user = NULL;
				unset($user);
			}
		}
	}

	function onPageWriteAfter ($page, $postdata, $notimestamp, $mode, $diffdata, $deletecache) {

	}

	function getPageNav ($total_items, $items_perpage, $current_start, $start_name="start", $extra_arg="") {
		include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
		$obj = new XoopsPageNav($total_items, $items_perpage, $current_start, $start_name, $extra_arg);
		return $obj;
	}
}
?>