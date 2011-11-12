<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: menu.inc.php,v 1.12 2009/05/25 04:45:39 nao-pon Exp $
//

class xpwiki_plugin_menu extends xpwiki_plugin {
	function plugin_menu_init () {
	
		// サブメニューを使用する
		$this->cont['MENU_ENABLE_SUBMENU'] =  FALSE;
	
		// サブメニューの名称
		$this->cont['MENU_SUBMENUBAR'] =  'MenuBar';

	}
	
	function make_pgmenu ($cmd, $page) {
		
		$links = array(
			'upload'   => '?plugin=attach&amp;pcmd=upload',
			'attaches' => '?plugin=attach&amp;pcmd=list',
			'back'     => '?cmd=backup&amp;action=diff',
			'refer'    => '?plugin=referer',
		);
		$docmd = (isset($links[$cmd]))? $links[$cmd] : '?cmd=' . $cmd;
		return '<a href="' . $this->cont['HOME_URL'] . $docmd . '&amp;page=' . rawurlencode($page) . '">' . $this->root->_LANG['skin'][$cmd] . '</a>';
	}
	
	function plugin_menu_action() {
		
		$msg = 'Menu';
		
		$page_menus = array();
		if (isset($this->root->vars['refer']) && $this->root->vars['refer'] !== '') {
			$_page = $this->root->vars['refer'];
			$msg = htmlspecialchars($_page) . ' - Menu';
			
			$is_editable =  $this->func->check_editable($_page, FALSE, FALSE);
			$is_freeze = $this->func->is_freeze($_page);
			$is_admin = $this->root->userinfo['admin'];
			$is_owner = $this->func->is_owner($_page);
			$rw    = ! $this->cont['PKWK_READONLY'];
			$use_attach = ((bool)ini_get('file_uploads') && $rw && $this->func->is_page($_page) && $this->func->get_plugin_instance('attach'));
			$can_attach = ($use_attach && (! $this->cont['ATTACH_UPLOAD_ADMIN_ONLY'] || $is_admin) && (! $this->cont['ATTACH_UPLOAD_EDITER_ONLY'] || $is_editable));
			
			
			if (!$is_freeze && $is_editable) {
				$page_menus[] = $this->make_pgmenu('edit', $_page);
			}
			
			if ($this->root->function_freeze) {
				$page_menus[] = (! $is_freeze) ? $this->make_pgmenu('freeze', $_page) : $this->make_pgmenu('unfreeze', $_page);
			}
			
			if ($is_owner) {
				$page_menus[] = $this->make_pgmenu('pginfo', $_page);
			}
			
			$page_menus[] = $this->make_pgmenu('back', $_page);
			
			if ($can_attach) {
				$page_menus[] = $this->make_pgmenu('upload', $_page);
			}
			
			if ($use_attach) {
				$page_menus[] = $this->make_pgmenu('attaches', $_page);
			}
			
			$page_menus[] = $this->make_pgmenu('refer', $_page);
		}
		$page_menu = ($page_menus) ? '<h2>Page Menu</h2><div>[ ' . $this->func->make_pagelink($_page) . ' ]</div><ul><li>' . join('</li><li>', $page_menus)  . '</li></ul><hr />': '';
		
		$body = $this->plugin_menu_convert();
		
		return array('msg' => $msg, 'body' => $page_menu . $body);
	}
	
	function plugin_menu_convert() {
		static $menu = array();
		if (!isset($menu[$this->root->mydirname])) {$menu[$this->root->mydirname] = NULL;}
	
		$num = func_num_args();
		if ($num > 0) {
			// Try to change default 'MenuBar' page name (only)
			if ($num > 1)       return '#menu(): Zero or One argument needed';
			if ($menu[$this->root->mydirname] !== NULL) return '#menu(): Already set: ' . htmlspecialchars($menu[$this->root->mydirname]);
			$args = func_get_args();
			$args[0] = $this->func->get_fullname($args[0], $this->root->vars['page']);
			if (! $this->func->is_page($args[0])) {
				return '#menu(): No such page: ' . $this->func->make_pagelink($args[0]);
			} else {
				$menu[$this->root->mydirname] = $args[0]; // Set
				return '';
			}
	
		} else {
			// Output menubar page data
			$page = ($menu[$this->root->mydirname] === NULL) ? $this->root->menubar : $menu[$this->root->mydirname];
	
			if ($this->cont['MENU_ENABLE_SUBMENU']) {
				if ($this->root->render_mode === 'block') {
					$tmppage = $GLOBALS['Xpwiki_'.$this->root->mydirname]['page'];
				} else {
					$tmppage = $this->func->strip_bracket($this->root->vars['page']);
				}
				$path = explode('/', $tmppage);
				while(! empty($path)) {
					$_page = join('/', $path) . '/' . $this->cont['MENU_SUBMENUBAR'];
					if ($this->func->is_page($_page)) {
						$page = $_page;
						break;
					}
					array_pop($path);
				}
			}
	
			if (! $this->func->is_page($page)) {
				return '';
			} else if ($this->root->vars['page'] == $page) {
				return '<!-- #menu(): You already view ' . htmlspecialchars($page) . ' -->';
			} else {
				// Cut fixed anchors
				$menutext = preg_replace('/^(\*{1,5}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $this->func->get_source($page));
				// remove xoops_block if runmode = xoops.
				if ($this->root->render_mode === 'block' && $this->root->runmode === "xoops") {
					$menutext = preg_replace("/^#xoopsblock.*$/m","",$menutext);
				}
				return '<div class="menuber">' . $this->func->convert_html($menutext) . '</div>';  
			}
		}
	}
}
?>