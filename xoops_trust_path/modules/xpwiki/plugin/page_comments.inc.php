<?php
class xpwiki_plugin_page_comments extends xpwiki_plugin {
	function plugin_page_comments_init() {
		$this->config['options'] = array(
			'class' => 'button',
		);
	}

	function can_call_otherdir_inline() {
		return 1;
	}

	function plugin_page_comments_inline() {
		$options = $this->config['options'];

		$page = $this->root->vars['page'];

		$args = func_get_args();
		$body = array_pop($args);

		if ($args) {
			$this->fetch_options($options, $args);
			if (! empty($options['_args'])) {
				if ($this->func->is_pagename($options['_args'][0])) {
					$page = $this->func->strip_bracket($options['_args'][0]);
				}
			}
		}
		$options['class'] = htmlspecialchars($options['class']);

		$comments = '';
		if ($this->func->is_page($page) && $this->root->allow_pagecomment && $this->root->enable_pagecomment) {
			$comments = '<span class="'.$options['class'].'"><a href="'.$this->func->get_page_uri($page, true).'#pageComments">' . $this->root->_LANG['skin']['comments'] . '(' . $this->func->count_page_comments($page) . ')</a></span>';
		}
		return $comments;
	}
}