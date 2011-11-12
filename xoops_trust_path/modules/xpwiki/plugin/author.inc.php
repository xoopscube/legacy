<?php
class xpwiki_plugin_author extends xpwiki_plugin {

	function can_call_otherdir_inline() {
		return 1;
	}

	function plugin_author_inline() {
		$options = array(
			'date' => FALSE,
		);

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

		$pginfo = $this->func->get_pginfo($page);
		$author = $this->func->make_userlink($pginfo['uid'], $pginfo['uname']);
		$date = ($options['date'])? ' at ' . $this->func->format_date($this->func->get_pg_buildtime($page)) : '';
		return $author . $date;
	}
}

