<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: tracker.inc.php,v 1.25 2012/01/26 06:05:30 nao-pon Exp $
// ORG: tracker.inc.php,v 1.57 2007/09/20 15:17:20 henoheno Exp $
// Issue tracker plugin (See Also bugtrack plugin)

class xpwiki_plugin_tracker extends xpwiki_plugin
{
	function plugin_tracker_init ()
	{
		$this->cont['PLUGIN_TRACKER_USAGE'] = '#tracker([config[/form][,basepage]])';
		$this->cont['PLUGIN_TRACKER_LIST_USAGE'] = '#tracker_list([config[/list]][[,base][,field:sort[;field:sort ...][,limit]]])';

		$this->cont['PLUGIN_TRACKER_DEFAULT_CONFIG'] = 'default';
		$this->cont['PLUGIN_TRACKER_DEFAULT_FORM']   = 'form';
		$this->cont['PLUGIN_TRACKER_DEFAULT_LIST']   = 'list';
		$this->cont['PLUGIN_TRACKER_DEFAULT_LIMIT']  = 0;	// 0 = Unlimited
		$this->cont['PLUGIN_TRACKER_DEFAULT_ORDER']  = '';	// Example: '_real'

		// Sort N columns at a time
		$this->cont['PLUGIN_TRACKER_LIST_SORT_LIMIT'] = 3;

		// tracker_listで表示しないページ名(正規表現で)
		// Excluding pattern
		$this->cont['PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN'] = '#^SubMenu$|/#'; // 'SubMenu'ページ および '/'を含むページを除外する 'SubMenu' and using '/'
		//define('PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN','#(?!)#'); // 制限しない場合はこちら Nothing excluded

		// 項目の取り出しに失敗したページを一覧に表示する
		// Show error rows (can't capture columns properly)
		$this->cont['PLUGIN_TRACKER_LIST_SHOW_ERROR_PAGE'] = TRUE;

		// ----

		// Sort type
		$this->cont['PLUGIN_TRACKER_SORT_TYPE_REGULAR'] = 0;
		$this->cont['PLUGIN_TRACKER_SORT_TYPE_NUMERIC'] = 1;
		$this->cont['PLUGIN_TRACKER_SORT_TYPE_STRING'] = 2;
		$this->cont['PLUGIN_TRACKER_SORT_TYPE_NATURAL'] = 6;
		if (! defined('SORT_NATURAL')) define('SORT_NATURAL', $this->cont['PLUGIN_TRACKER_SORT_TYPE_NATURAL']);

		// Sort order
		$this->cont['PLUGIN_TRACKER_SORT_ORDER_DESC'] = 3;
		$this->cont['PLUGIN_TRACKER_SORT_ORDER_ASC'] = 4;
		$this->cont['PLUGIN_TRACKER_SORT_ORDER_DEFAULT'] = $this->cont['PLUGIN_TRACKER_SORT_ORDER_ASC'];

		// Sort options
		$this->cont['PLUGIN_TRACKER_LIST_SORT_DESC'] = 3;
		$this->cont['PLUGIN_TRACKER_LIST_SORT_ASC'] = 4;
		$this->cont['PLUGIN_TRACKER_LIST_SORT_DEFAULT'] = $this->cont['PLUGIN_TRACKER_LIST_SORT_ASC'];
	}

	function can_call_otherdir_convert() {
		return 2;
	}

	// Show a form
	function plugin_tracker_convert()
	{

		if ($this->cont['PKWK_READONLY'] === 1) return ''; // Show nothing

		$base = $refer = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		$config_name = $this->cont['PLUGIN_TRACKER_DEFAULT_CONFIG'];
		$form        = $this->cont['PLUGIN_TRACKER_DEFAULT_FORM'];

		$args = func_get_args();
		$argc = count($args);
		if ($argc > 3) {
			return $this->cont['PLUGIN_TRACKER_USAGE'] . '<br />';
		}
		switch ($argc) {
		case 3:
			$option = $args[2];
		case 2:
			$arg = $this->func->get_fullname($args[1], $base);
			if ($this->func->is_pagename($arg)) $base = $arg;
			/*FALLTHROUGH*/
		case 1:
			// Config/form
			if ($args[0] != '') {
				$arg = explode('/', $args[0], 2);
				if ($arg[0] != '' ) $config_name = $arg[0];
				if (isset($arg[1])) $form        = $arg[1];
			}
		}
		unset($args, $argc, $arg);

		$config = new XpWikiConfig($this->xpwiki, 'plugin/tracker/'.$config_name);

		if (!$config->read()) {
			return '#tracker: Config \'' . htmlspecialchars($config_name) . '\' not found<br />';
		}
		$config->config_name = $config_name;
		//$fields = $this->plugin_tracker_get_fields($base,$refer,$config);

		$form = $config->page.'/'.$form;
		if (!$this->func->is_page($form)) {
			return '#tracker: Form \'' . $this->func->make_pagelink($form) . '\' not found<br />';
		}
		$from = $to = $hidden = array();
		$fields = $this->plugin_tracker_get_fields($base, $refer, $config);
		foreach (array_keys($fields) as $field) {
			$from[] = '[' . $field . ']';
			$_to    = $fields[$field]->get_tag();
			if (is_a($fields[$field], 'XpWikiTracker_field_hidden')) {
				$to[]     = '';
				$hidden[] = $_to;
			} else {
				$to[]     = $_to;
			}
			unset($fields[$field]);
		}

		$script = $this->func->get_script_uri();
		$retval = str_replace($from, $to, $this->func->convert_html($this->plugin_tracker_get_source($form)));
		$hidden = implode('<br />' . "\n", $hidden);
		$script = $this->func->get_script_uri();
		return <<<EOD
<form enctype="multipart/form-data" action="{$script}" method="post">
<div>
$retval
$hidden
</div>
</form>
EOD;
	}
	function plugin_tracker_action()
	{
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');

		$config_name = isset($this->root->post['_config']) ? $this->root->post['_config'] : '';
		$base  = isset($this->root->post['_base'])  ? $this->root->post['_base']  : '';
		$refer = isset($this->root->post['_refer']) ? $this->root->post['_refer'] : $base;

		if (!$this->func->is_pagename($refer)) {
			return array(
				'msg'  => 'Cannot write',
				'body' => 'Page name (' . htmlspecialchars($refer) . ') invalid'
			);
		}

		// ページ名を決定
		$num = 0;
		$name = (isset($this->root->post['_name'])) ? $this->root->post['_name'] : '';
		if (isset($this->root->post['_page'])) {
			$real = $page = $this->root->post['_page'];
		} else {
			$real = $this->func->is_pagename($name) ? $name : ++$num;
			$page = $this->func->get_fullname('./'.$real,$base);
		}
		if (!$this->func->is_pagename($page)) {
			$page = $base;
		}

		while ($this->func->is_page($page)) {
			$real = ++$num;
			$page = $base . '/' . $real;
		}

		// Loading configuration
		$config_name = isset($this->root->post['_config']) ? $this->root->post['_config'] : '';
		$config = new XpWikiConfig($this->xpwiki, 'plugin/tracker/' . $config_name);
		if (! $config->read()) {
			return '<p>config file \'' . htmlspecialchars($config_name) . '\' not found.</p>';
		}
		$config->config_name = $config_name;
		$template_page = $config->page . '/page';
		if (! $this->func->is_page($template_page)) {
			return array(
				'msg'  => 'Cannot write',
				'body' => 'Page template (' . htmlspecialchars($template_page) . ') not exists'
			);
		}

		// 規定のデータ
		// Default
		$_post = array_merge($this->root->post,$_FILES);
		$_post['_date'] = $this->root->now;
		$_post['_page'] = $page;
		$_post['_name'] = $name;
		$_post['_real'] = $real;
		// $_post['_refer'] = $_post['refer'];

		// Creating an empty page, before attaching files
		// touch($this->func->get_filename($page));

		// Load $fields
		$from = $to = array();
		$fields = $this->plugin_tracker_get_fields($page, $refer, $config);
		foreach (array_keys($fields) as $field) {
			$from[] = '[' . $field . ']';
			$to[]   = isset($_post[$field]) ? $fields[$field]->format_value($_post[$field]) : '';
			unset($fields[$field]);
		}

		// Load $template
		$template = $this->plugin_tracker_get_source($template_page);

		// Repalace every [$field]s to real values in the $template
		$subject = $subject_e = array();
		foreach (array_keys($template) as $num) {
			if (trim($template[$num]) == '') continue;
			$letter = $template[$num]{0};
			if ($letter == '|' || $letter == ':') {
				// Escape for some TextFormattingRules: <table> and <dr>
				$subject_e[$num] = $template[$num];
			} else {
				$subject[$num]   = $template[$num];
			}
		}
		foreach (str_replace($from,   $to,   $subject  ) as $num => $line) {
			$template[$num] = $line;
		}
		// Escape for some TextFormattingRules: <table> and <dr>
		if ($subject_e) {
			$to_e = array();
			foreach($to as $value) {
				if (strpos($value, '|') !== FALSE) {
					// Escape for some TextFormattingRules: <table> and <dr>
					$to_e[] = str_replace('|', '&#x7c;', $value);
				} else{
					$to_e[] = $value;
				}
			}
			foreach (str_replace($from, $to_e, $subject_e) as $num => $line) {
				$template[$num] = $line;
			}
		}

		// Writing page data, without touch
		$this->func->page_write($page, join('', $template));

		$this->func->send_location($page);
	}

	// フィールドオブジェクトを構築する
	// Construct $fields (an array of Tracker_field objects)
	function plugin_tracker_get_fields($base,$refer,&$config)
	{
		$fields = array();
		foreach ($config->get('fields') as $field) {
			// $field[0]: Field name
			// $field[1]: Field name (for display)
			// $field[2]: Field type
			// $field[3]: Option
			// $field[3]: Option ("size", "cols", "rows", etc)
			// $field[4]: Default value
 			$class = 'XpWikiTracker_field_'.$field[2];
			if (!XC_CLASS_EXISTS($class)) {
				// Default
				$field[2] = 'text';
				$class    = 'XpWikiTracker_field_' . $field[2];
				$field[3] = '20';
			}
			$fieldname = $field[0];
			$fields[$fieldname] = & new $class($this->xpwiki, $field, $base, $refer, $config);
		}

		foreach (
			array(
				// Reserved ones
				'_date'   => 'text',	// Post date
				'_update' => 'date',	// Last modified date
				'_past'   => 'past',	// Elapsed time (passage)
				'_page'   => 'page',	// Page name
				'_name'   => 'text',	// Page name specified by poster
				'_real'   => 'real',	// Page name (Real)
				'_refer'  => 'page',	// Page name refer from this (Page who has forms)
				'_base'   => 'page',
				'_submit' => 'submit'
			) as $fieldname => $type)
		{
			if (isset($fields[$fieldname])) continue;
			$field = array($fieldname, xpwiki_plugin_tracker::plugin_tracker_message('btn' . $fieldname), '', '20', '');
			$class = 'XpWikiTracker_field_' . $type;
			$fields[$fieldname] = & new $class($this->xpwiki, $field, $base, $refer, $config);
		}

		return $fields;
	}

	///////////////////////////////////////////////////////////////////////////
	// 一覧表示
	// tracker_list plugin
	function plugin_tracker_list_convert()
	{
		$base = $refer = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		$config_name = $this->cont['PLUGIN_TRACKER_DEFAULT_CONFIG'];
		$list        = $this->cont['PLUGIN_TRACKER_DEFAULT_LIST'];
		$limit       = $this->cont['PLUGIN_TRACKER_DEFAULT_LIMIT'];
		$order       = $this->cont['PLUGIN_TRACKER_DEFAULT_ORDER'];

		$args = func_get_args();
		$argc = count($args);
		if ($argc > 4) {
			return $this->cont['PLUGIN_TRACKER_LIST_USAGE'] . '<br />';
		}
		switch ($argc) {
		case 4: $limit = $args[3];	/*FALLTHROUGH*/
		case 3: $order = $args[2];	/*FALLTHROUGH*/
		case 2:
			$arg = $this->func->get_fullname($args[1], $base);
			if ($this->func->is_pagename($arg)) $base = $arg;
			/*FALLTHROUGH*/
		case 1:
			// Config/list
			if ($args[0] != '') {
				$arg = explode('/', $args[0], 2);
				if ($arg[0] != '' ) $config_name = $arg[0];
				if (isset($arg[1])) $list        = $arg[1];
			}
		}
		unset($args, $argc, $arg);
		return $this->plugin_tracker_list_render($base, $refer, $config_name, $list, $order, $limit);
	}

	function plugin_tracker_list_action()
	{
		$base   = isset($this->root->get['base'])   ? $this->root->get['base']   : '';
		$config = isset($this->root->get['config']) ? $this->root->get['config'] : '';
		$list   = isset($this->root->get['list'])   ? $this->root->get['list']   : 'list';

		$order  = isset($this->root->vars['order']) ? $this->root->vars['order'] : $this->cont['PLUGIN_TRACKER_DEFAULT_ORDER'];
		$limit  = isset($this->root->vars['limit']) ? $this->root->vars['limit'] : 0;

		// Compat before 1.4.8
		if ($base == '') $base = isset($this->root->get['refer']) ? $this->root->get['refer'] : '';

		$s_base = $this->func->make_pagelink(trim($base));
		return array(
			'msg' => str_replace('$1', $s_base, $this->root->_tracker_messages['msg_list']),
			'body'=> str_replace('$1', $s_base, $this->root->_tracker_messages['msg_back']).
				$this->plugin_tracker_list_render($base, $base, $config, $list, $order, $limit)
		);
	}

	function plugin_tracker_list_render($base, $refer, $config_name, $list, $order_commands = '', $limit = 0)
	{
		$base  = trim($base);
		if ($base == '') return '#tracker_list: Base not specified' . '<br />';

		$refer = trim($refer);

		$config_name = trim($config_name);
		if ($config_name == '') $config_name = $this->cont['PLUGIN_TRACKER_DEFAULT_CONFIG'];

		$list  = trim($list);
		if (! is_numeric($limit)) return $this->cont['PLUGIN_TRACKER_LIST_USAGE'] . '<br />';
		$limit = intval($limit);

		$config = new XpWikiConfig($this->xpwiki, 'plugin/tracker/' . $config_name);

		if (!$config->read()) {
			return '#tracker_list: Config not found: ' . htmlspecialchars($config_name) . '<br />';
		}
		$config->config_name = $config_name;
		if (!$this->func->is_page($config->page.'/'.$list)) {
			return '#tracker_list: List not found: ' . $this->func->make_pagelink($config->page . '/' . $list) . '<br />';
		}

		$list = & new XpWikiTracker_list($this->xpwiki, $base, $refer, $config, $list);
		if ($list->sortRows($order_commands) === FALSE) {
			return '#tracker_list: ' . htmlspecialchars($list->error) . '<br />';
		}
		$result = $list->toString($limit);
		if ($result === FALSE) {
			return '#tracker_list: ' . htmlspecialchars($list->error) . '<br />';
		}
		unset($list);
		return $this->func->convert_html($result);
	}

	function plugin_tracker_get_source($page, $join = FALSE)
	{
		$source = $this->func->get_source($page, TRUE, $join);
		// Remove fixed-heading anchors, #freeze, #info etc.
		$this->func->cleanup_template_source($source);
		return $source;
	}

	function plugin_tracker_message($key)
	{
		return isset($this->root->_tracker_messages[$key]) ? $this->root->_tracker_messages[$key] : 'NOMESSAGE';
	}
}

// フィールドクラス
// Field classes
class XpWikiTracker_field
{
	var $name;
	var $title;
	var $values;
	var $default_value;
	var $base;
	var $refer;
	var $config;
	var $data;
	var $sort_type = SORT_REGULAR;
	var $id = 0;

	function XpWikiTracker_field(& $xpwiki, $field, $base, $refer, & $config)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;
		static $id = array();
		if (!isset($id[$this->xpwiki->pid])) {$id[$this->xpwiki->pid] = 0;} // Unique id per instance

		$this->id = ++$id[$this->xpwiki->pid];
		$this->name = $field[0];
		$this->title = $field[1];
		$this->values = explode(',',$field[3]);
		$this->default_value = $field[4];
		$this->base = $base;
		$this->refer = $refer;
		$this->config = &$config;
		$this->data = isset($this->root->post[$this->name]) ? $this->root->post[$this->name] : '';
	}

	// XHTML part inside a form
	function get_tag()
	{
		return '';
	}

	function get_style()
	{
		return '%s';
	}

	function format_value($value)
	{
		return $value;
	}

	function format_cell($str)
	{
		return $str;
	}

	// Compare key for Tracker_list->sort()
	function get_value($value)
	{
		return $value;	// Default: $value itself
	}
}

class XpWikiTracker_field_text extends XpWikiTracker_field
{
	var $sort_type = SORT_STRING;

	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = htmlspecialchars($this->values[0]);
		if ($this->default_value == '$uname' || $this->default_value == '$X_uname' ) {
			$this->default_value = $this->cont['USER_NAME_REPLACE'];
		}
		$s_value = htmlspecialchars($this->default_value);
		$helper = ($this->name == "_name" || is_a($this, "XpWikiTracker_field_page"))? "" : " rel=\"wikihelper\"";
		return "<input type=\"text\" name=\"$s_name\"{$helper} size=\"$s_size\" value=\"$s_value\" />";
	}
}

class XpWikiTracker_field_page extends XpWikiTracker_field_text
{
	var $sort_type = SORT_STRING;

	function format_value($value) {

		$value = $this->func->strip_bracket($value);

		if ($this->default_value == '$uname' || $this->default_value == '$X_uname' ) {
			// save name to cookie
			if ($value) { $this->func->save_name2cookie($value); }
		}

		if ($this->func->is_pagename($value))
		{
			$value = "[[$value]]";
		}
		return parent::format_value($value);
	}
}

class XpWikiTracker_field_real extends XpWikiTracker_field_text
{
	var $sort_type = SORT_REGULAR;
}

class XpWikiTracker_field_title extends XpWikiTracker_field_text
{
	var $sort_type = SORT_STRING;

	function format_cell($str) {
		$this->func->make_heading($str);
		return $str;
	}
}

class XpWikiTracker_field_textarea extends XpWikiTracker_field
{
	var $sort_type = SORT_STRING;

	function get_tag() {
		$s_name = htmlspecialchars($this->name);
		$s_cols = htmlspecialchars($this->values[0]);
		$s_rows = htmlspecialchars($this->values[1]);
		$s_value = htmlspecialchars($this->default_value);
		$domid = $this->func->get_domid('tracker', $s_name, true);
		$emoji = (in_array('emoji', $this->values))? $this->func->get_emoji_pad($domid, FALSE) : '';
		return "<textarea id=\"$domid\" name=\"$s_name\" cols=\"$s_cols\" rows=\"$s_rows\">$s_value</textarea>$emoji";
	}

	function format_cell($str) {
		$str = preg_replace('/[\r\n]+/', '', $str);
		if (!empty($this->values[2]) && strlen($str) > ($this->values[2] + 3)) {
			$str = mb_substr($str,0,$this->values[2]).'...';
		}
		return $str;
	}
}

class XpWikiTracker_field_format extends XpWikiTracker_field
{
	var $sort_type = SORT_STRING;

	var $styles = array();
	var $formats = array();

	function XpWikiTracker_field_format(& $xpwiki, $field, $base, $refer, &$config)
	{

		parent::XpWikiTracker_field($xpwiki, $field, $base, $refer, $config);

		foreach ($this->config->get($this->name) as $option) {
			list($key,$style,$format) = array_pad(array_map(create_function('$a','return trim($a);'),$option),3,'');
			if ($style != '') {
				$this->styles[$key] = $style;
			}
			if ($format != '') {
				$this->formats[$key] = $format;
			}
		}
	}

	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = htmlspecialchars($this->values[0]);
		return "<input type=\"text\" name=\"$s_name\" size=\"$s_size\" />";
	}

	function get_key($str)
	{
		return ($str == '') ? 'IS NULL' : 'IS NOT NULL';
	}

	function format_value($str)
	{
		if (is_array($str)) {
			return join(', ',array_map(array($this,'format_value'),$str));
		}
		$key = $this->get_key($str);
		return isset($this->formats[$key]) ? str_replace('%s',$str,$this->formats[$key]) : $str;
	}

	function get_style($str)
	{
		$key = $this->get_key($str);
		return isset($this->styles[$key]) ? $this->styles[$key] : '%s';
	}
}

class XpWikiTracker_field_file extends XpWikiTracker_field_format
{
	var $sort_type = SORT_STRING;

	function get_tag()
	{
		static $loaded = array();

		$s_name = htmlspecialchars($this->name);
		$s_size = htmlspecialchars($this->values[0]);
		$s_id = '_p_tracker_' . $s_name . '_' . $this->id;

		$attach_plugin =& $this->func->get_plugin_instance('attach');
		$pass = '';
		if (!isset($loaded[$this->xpwiki->pid]) && $this->cont['ATTACH_PASSWORD_REQUIRE'] && !$this->cont['ATTACH_UPLOAD_ADMIN_ONLY'] && !$this->root->userinfo['uid']) {
			$title = $this->root->_attach_messages[$this->cont['ATTACH_UPLOAD_ADMIN_ONLY'] ? 'msg_adminpass' : 'msg_password'];
			$pass = '<br />'.$title.': <input type="password" name="upload_pass" size="8" />';
		}
		$loaded[$this->xpwiki->pid] = true;

		return "<input type=\"file\" name=\"$s_name\" size=\"$s_size\" /> <input type=\"checkbox\" id=\"{$s_id}_copyright\" name=\"{$s_name}_copyright\" value=\"1\" /> <label for=\"{$s_id}_copyright\">{$this->root->_attach_messages['msg_copyright']}</label>" . $pass;
	}

	function format_value()
	{
		if (isset($_FILES[$this->name]))
		{
			$copyright = (isset($this->root->post[$this->name.'_copyright']))? TRUE : FALSE;
			$pass = (empty($this->root->post['upload_pass']))? NULL : $this->root->post['upload_pass'];

			$attach_plugin =& $this->func->get_plugin_instance('attach');
			$result = $attach_plugin->attach_upload($_FILES[$this->name], $this->base, $pass, $copyright);
			if ($result['result']) { // アップロード成功 Upload success
				return parent::format_value($this->base.'/'.$_FILES[$this->name]['name']);
			} else {
				return parent::format_value($result['msg']);
			}
		}
		// ファイルが指定されていないか、アップロードに失敗
		// Filename not specified, or Fail to upload
		return parent::format_value('');
	}
}

class XpWikiTracker_field_radio extends XpWikiTracker_field_format
{
	var $sort_type = SORT_NUMERIC;
	var $_options  = array();

	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$retval = '';
		$id = 0;
		foreach ($this->config->get($this->name) as $option)
		{
			$s_option = htmlspecialchars($option[0]);
			$checked = trim($option[0]) == trim($this->default_value) ? ' checked="checked"' : '';
			++$id;
			$s_id = '_p_tracker_' . $s_name . '_' . $this->id . '_' . $id;
			$retval .= '<input type="radio" name="' .  $s_name . '" id="' . $s_id .
				'" value="' . $s_option . '"' . $checked . ' />' .
				'<label for="' . $s_id . '">' . $s_option . '</label>' . "\n";
		}

		return $retval;
	}

	function get_key($str)
	{
		return $str;
	}

	function get_value($value)
	{
		$options = & $this->_options;
		$name    = $this->name;

		if (! isset($options[$name])) {
			$values = array_map(
				create_function('$array', 'return $array[0];'),
				$this->config->get($name)
			);
			$options[$name] = array_flip($values);	// array('value0' => 0, 'value1' => 1, ...)
		}

		return isset($options[$name][$value]) ? $options[$name][$value] : $value;
	}
}

class XpWikiTracker_field_select extends XpWikiTracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag($empty=FALSE)
	{
		$s_name = htmlspecialchars($this->name);
		$s_size = (isset($this->values[0]) && is_numeric($this->values[0])) ?
			' size="'.htmlspecialchars($this->values[0]).'"' : '';
		$s_multiple = (isset($this->values[1]) && strtolower($this->values[1]) == 'multiple') ?
			' multiple="multiple"' : '';
		$retval = "<select name=\"{$s_name}[]\"$s_size$s_multiple>\n";
		if ($empty) $retval .= ' <option value=""></option>' . "\n";
		$defaults = array_flip(preg_split('/\s*,\s*/',$this->default_value,-1,PREG_SPLIT_NO_EMPTY));
		foreach ($this->config->get($this->name) as $option) {
			$s_option = htmlspecialchars($option[0]);
			$selected = isset($defaults[trim($option[0])]) ? ' selected="selected"' : '';
			$retval .= " <option value=\"$s_option\"$selected>$s_option</option>\n";
		}
		$retval .= "</select>";

		return $retval;
	}
}

class XpWikiTracker_field_checkbox extends XpWikiTracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$defaults = array_flip(preg_split('/\s*,\s*/',$this->default_value,-1,PREG_SPLIT_NO_EMPTY));
		$retval = '';
		$id = 0;
		foreach ($this->config->get($this->name) as $option) {
			$s_option = htmlspecialchars($option[0]);
			$checked = isset($defaults[trim($option[0])]) ?
				' checked="checked"' : '';
			++$id;
			$s_id = '_p_tracker_' . $s_name . '_' . $this->id . '_' . $id;
			$retval .= '<input type="checkbox" name="' . $s_name .
				'[]" id="' . $s_id . '" value="' . $s_option . '"' . $checked . ' />' .
				'<label for="' . $s_id . '">' . $s_option . '</label>' . "\n";
		}

		return $retval;
	}
}

class XpWikiTracker_field_hidden extends XpWikiTracker_field_radio
{
	var $sort_type = SORT_NUMERIC;

	function get_tag()
	{
		$s_name = htmlspecialchars($this->name);
		$s_default = htmlspecialchars($this->default_value);
		$retval = "<input type=\"hidden\" name=\"$s_name\" value=\"$s_default\" />\n";

		return $retval;
	}
}

class XpWikiTracker_field_submit extends XpWikiTracker_field
{
	function get_tag()
	{
		$s_title  = htmlspecialchars($this->title);
		$s_base   = htmlspecialchars($this->base);
		$s_refer  = htmlspecialchars($this->refer);
		$s_config = htmlspecialchars($this->config->config_name);

		return <<<EOD
<input type="submit" value="$s_title" />
<input type="hidden" name="plugin" value="tracker" />
<input type="hidden" name="_refer" value="$s_refer" />
<input type="hidden" name="_base" value="$s_base" />
<input type="hidden" name="_config" value="$s_config" />
EOD;
	}
}

class XpWikiTracker_field_date extends XpWikiTracker_field
{
	var $sort_type = SORT_NUMERIC;

	function format_cell($timestamp)
	{
		return $this->func->format_date($timestamp);
	}
}

class XpWikiTracker_field_past extends XpWikiTracker_field
{
	var $sort_type = SORT_NUMERIC;

	function format_cell($timestamp)
	{
		return $this->func->get_passage($timestamp,FALSE);
	}

	function get_value($value)
	{
		return $this->cont['UTIME'] - $value;
	}
}

// 一覧クラス
// Listing class
class XpWikiTracker_list
{
	var $base;
	var $config;
	var $list;
	var $fields;
	var $pattern;
	var $pattern_fields;

	var $rows   = array();
	var $order  = array();
	var $_added = array();

	var $error  = '';	// Error message

	// Used by toString() only
	var $_itmes;
	var $_escape;

	// TODO: Why list here
	function XpWikiTracker_list(& $xpwiki, $base, $refer, & $config, $list)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;

		$this->base     = $base;
		$this->config   = & $config;
		$this->list     = $list;

		$fields         = xpwiki_plugin_tracker::plugin_tracker_get_fields($base, $refer, $config);
		$pattern        = array();
		$pattern_fields = array();

		// Generate regexes:

		// TODO: if (is FALSE) OR file_exists()
		$source = xpwiki_plugin_tracker::plugin_tracker_get_source($config->page . '/page', TRUE);
		// Block-plugins to pseudo fields (#convert => [_block_convert])
		$source = preg_replace('/^\#([^\(\s]+)(?:\((.*?)\))?\s*$/m', '[_block_$1]', $source);

		// Now, $source = array('*someting*', 'fieldname', '*someting*', 'fieldname', ...)
		$source = preg_split('/\\\\\[(\w+?)\\\\\]/', preg_quote($source, '/'), -1, PREG_SPLIT_DELIM_CAPTURE);

		while (! empty($source)) {
			// Just ignore these _fixed_ data
			// NOTE: if a page has garbages between fields, it will fail to be load
			$pattern[] = preg_replace('/\s+/', '\\s*', '(?>\\s*' . trim(array_shift($source)) . '\\s*)');
			if (! empty($source)) {
				$fieldname = array_shift($source);
				if (isset($fields[$fieldname])) {
					$pattern[]        = '(.*?)';		// Just capture it
					$pattern_fields[] = $fieldname;	// Capture it as this $filedname
				} else {
					$pattern[]        = '.*?';		// Just ignore pseudo fields
				}
			}
		}
		$this->fields         = $fields;
		$this->pattern        = implode('', $pattern);
		$this->pattern_fields = $pattern_fields;

		// Listing
		$pattern     = $base . '/';
		$pattern_len = strlen($pattern);
		foreach ($this->func->get_existpages(false, $pattern) as $_page) {
			$name = substr($_page, $pattern_len);
			if (preg_match($this->cont['PLUGIN_TRACKER_LIST_EXCLUDE_PATTERN'], $name)) continue;
			$this->add($_page, $name);
		}
	}

	function add($page, $name)
	{
		if (isset($this->_added[$page])) return;
		$this->_added[$page] = TRUE;

		$source = xpwiki_plugin_tracker::plugin_tracker_get_source($page);

		// Compat: 'move to [[page]]' (bugtrack plugin)
		$matches = array();
		if (! empty($source) && preg_match('/move\sto\s(.+)/', $source[0], $matches)) {
			$to_page = $this->func->strip_bracket(trim($matches[1]));
			if ($this->func->is_page($to_page)) {
				unset($source);	// Release
				$this->add($to_page, $name);	// Recurse(Rescan)
				return;
			} else {
				return;	// Invalid
			}
		}

		// Default column
		$filetime = $this->func->get_filetime($page);
		$row = array(
			// column => default data of the cell
			'_page'   => '[[' . $page . ']]',
			'_real'   => $name,
			'_update' => $filetime,
			'_past'   => $filetime,
			'_match'  => FALSE,
		);

		// Load / Redefine cell
		$matches = array();
		$row['_match'] = preg_match('/' . $this->pattern . '/sS', implode('', $source), $matches);
		unset($source);
		if ($row['_match']) {
			array_shift($matches);	// $matches[0] = all of the captured string
			foreach ($this->pattern_fields as $key => $field) {
				$row[$field] = trim($matches[$key]);
			}
		}

		$this->rows[$name] = $row;
	}

	// Sort $this->rows by $order_commands
	function sort($order_commands = '')
	{
		$order_commands = trim($order_commands);
		if ($order_commands == '') {
			$this->order = array();
			return TRUE;
		}

		$fields = $this->fields;

		$i = 0;
		$orders = array();
		foreach (explode(';', $order_commands) as $command) {
			$command = trim($command);
			if ($command == '') continue;
			$arg = explode(':', $command, 2);
			$fieldname = isset($arg[0]) ? trim($arg[0]) : '';
			$order     = isset($arg[1]) ? trim($arg[1]) : '';

			if (! isset($fields[$fieldname])) {
				$this->error =  'No such field: ' . $fieldname;
				return FALSE;
			}
			$_order = $this->_sortkey_string2define($order);
			if ($_order === FALSE) {
				$this->error =  'Invalid sortkey: ' . $order;
				return FALSE;
			}

			if (! isset($orders[$fieldname]) && $this->cont['PLUGIN_TRACKER_LIST_SORT_LIMIT'] < ++$i) continue;
			$orders[$fieldname] = $_order;	// Set or override
		}

		$params = array();	// Arguments for array_multisort()
		foreach ($orders as $fieldname => $order) {
			// One column set (one-dimensional array(), sort type, and order-by)
			$array = array();
			foreach ($this->rows as $row) {
				$array[] = isset($row[$fieldname]) ?
					$fields[$fieldname]->get_value($row[$fieldname]) :
					'';
			}
			$params[] = & $array;
			$params[] = & $fields[$fieldname]->sort_type;
			$params[] = & $order;
		}
		$params[] = & $this->rows;

		call_user_func_array('array_multisort', $params);
		$this->order = $orders;

		return TRUE;
	}

	// setSortOrder()
	function _order_commands2orders($order_commands = '')
	{
		$order_commands = trim($order_commands);
		if ($order_commands == '') $order_commands = $this->cont['PLUGIN_TRACKER_DEFAULT_ORDER'];
		if ($order_commands == '') return array();

		$orders = array();

		$i = 0;
		foreach (explode(';', $order_commands) as $command) {
			$command = trim($command);
			if ($command == '') continue;

			$arg = explode(':', $command, 2);
			$fieldname = isset($arg[0]) ? trim($arg[0]) : '';
			$order     = isset($arg[1]) ? trim($arg[1]) : '';

			$_order = $this->_sortkey_string2define($order);
			if ($_order === FALSE) {
				$this->error =  'Invalid sort key: ' . $order;
				return FALSE;
			} else if (isset($orders[$fieldname])) {
				$this->error =  'Sort key already set for: ' . $fieldname;
				return FALSE;
			}

			if ($this->cont['PLUGIN_TRACKER_LIST_SORT_LIMIT'] <= $i) continue;
			++$i;

			$orders[$fieldname] = $_order;
		}

		return $orders;
	}

	// Set commands for sort()
	function setSortOrder($order_commands = '')
	{
		$orders = $this->_order_commands2orders($order_commands);
		if ($orders === FALSE) {
			unset($this->orders);
			return FALSE;
		} else {
			$this->orders = $orders;
			return TRUE;
		}
	}

	// sortRows(): Internal sort type => PHP sort define
	function _sort_type_dropout($order)
	{
		switch ($order) {
		case $this->cont['PLUGIN_TRACKER_SORT_TYPE_REGULAR']: return SORT_REGULAR;
		case $this->cont['PLUGIN_TRACKER_SORT_TYPE_NUMERIC']: return SORT_NUMERIC;
		case $this->cont['PLUGIN_TRACKER_SORT_TYPE_STRING']:  return SORT_STRING;
		case $this->cont['PLUGIN_TRACKER_SORT_TYPE_NATURAL']: return SORT_NATURAL;
		default:
			$this->error = 'Invalid sort type';
			return FALSE;
		}
	}


	// sortRows(): Internal sort order => PHP sort define
	function _sort_order_dropout($order)
	{
		switch ($order) {
		case $this->cont['PLUGIN_TRACKER_SORT_ORDER_ASC']:  return SORT_ASC;
		case $this->cont['PLUGIN_TRACKER_SORT_ORDER_DESC']: return SORT_DESC;
		default:
			$this->error = 'Invalid sort order';
			return FALSE;
		}
	}

	// Sort $this->rows by $this->orders
	function sortRows($orders)
	{
		$this->setSortOrder($orders);
		if (! isset($this->orders)) {
			$this->error = "Sort order seems not set";
			return FALSE;
		}

		$fields = $this->fields;
		$orders = $this->orders;
		$types  = array();

		$fieldnames = array_keys($orders);	// Field names to sort

		foreach ($fieldnames as $fieldname) {
			if (! isset($fields[$fieldname])) {
				$this->error =  'No such field: ' . $fieldname;
				return FALSE;
			}
			$types[$fieldname]  = $this->_sort_type_dropout($fields[$fieldname]->sort_type);
			$orders[$fieldname] = $this->_sort_order_dropout($orders[$fieldname]);
			if ($types[$fieldname] === FALSE || $orders[$fieldname] === FALSE) return FALSE;
		}

		$columns = array();
		foreach ($this->rows as $row) {
			foreach ($fieldnames as $fieldname) {
				if (isset($row[$fieldname])) {
					$columns[$fieldname][] = $fields[$fieldname]->get_value($row[$fieldname]);
				} else {
					$columns[$fieldname][] = '';
				}
			}
		}

		$params = array();
		foreach ($fieldnames as $fieldname) {

			if ($types[$fieldname] == SORT_NATURAL) {
				$column = & $columns[$fieldname];
				natcasesort($column);
				$i = 0;
				$last = NULL;
				foreach (array_keys($column) as $key) {
					// Consider the same values there, for array_multisort()
					if ($last !== $column[$key]) ++$i;
					$last = strtolower($column[$key]);	// natCASEsort()
					$column[$key] = $i;
				}
				ksort($column, SORT_NUMERIC);	// Revert the order
				$types[$fieldname] = SORT_NUMERIC;
			}

			// One column set (one-dimensional array, sort type, and sort order)
			// for array_multisort()
			$params[] = & $columns[$fieldname];
			$params[] = & $types[$fieldname];
			$params[] = & $orders[$fieldname];
		}
		if (! empty($orders) && ! empty($this->rows)) {
			$params[] = & $this->rows;	// The target
			call_user_func_array('array_multisort', $params);
		}

		return TRUE;
	}

	// toString(): Sort key: Define to string (internal var => string)
	function _sortkey_define2string($sortkey)
	{
		switch ($sortkey) {
		case $this->cont['PLUGIN_TRACKER_LIST_SORT_ASC']:  $sortkey = 'SORT_ASC';  break;
		case $this->cont['PLUGIN_TRACKER_LIST_SORT_DESC']: $sortkey = 'SORT_DESC'; break;
		default:
			$this->error =  'No such define: ' . $sortkey;
			$sortkey = FALSE;
		}
		return $sortkey;
	}

	// toString(): Sort key: String to define (string => internal var)
	function _sortkey_string2define($sortkey)
	{
		switch (strtoupper(trim($sortkey))) {
		case '':          $sortkey = $this->cont['PLUGIN_TRACKER_LIST_SORT_DEFAULT']; break;

		case SORT_ASC:    /*FALLTHROUGH*/ // Compat, will be removed at 1.4.9 or later
		case 'SORT_ASC':  /*FALLTHROUGH*/
		case 'ASC':       $sortkey = $this->cont['PLUGIN_TRACKER_LIST_SORT_ASC']; break;

		case SORT_DESC:   /*FALLTHROUGH*/ // Compat, will be removed at 1.4.9 or later
 		case 'SORT_DESC': /*FALLTHROUGH*/
		case 'DESC':      $sortkey = $this->cont['PLUGIN_TRACKER_LIST_SORT_DESC']; break;

		default:
			$this->error =  'Invalid sort key: ' . $sortkey;
			$sortkey = FALSE;
		}
		return $sortkey;
	}

	// toString(): Called within preg_replace_callback()
	function _replace_item($matches = array())
	{
		$fields = $this->fields;
		$items  = $this->_items;
		$escape = isset($this->_escape) ? (bool)$this->_escape : FALSE;

		$params    = isset($matches[1]) ? explode(',', $matches[1]) : array();
		$fieldname = isset($params[0]) ? $params[0] : '';
		$stylename = isset($params[1]) ? $params[1] : $fieldname;

		if ($fieldname == '') return '';	// Invalid

		if (! isset($items[$fieldname])) {
			// Maybe load miss of the page
			if (isset($fields[$fieldname])) {
				$str = '[page_err]';	// Exactlly
			} else {
				$str = isset($matches[0]) ? $matches[0] : '';	// Nothing to do
			}
		} else {
			$str = $items[$fieldname];
			if (isset($fields[$fieldname])) {
				$str    = $fields[$fieldname]->format_cell($str);
			}
			if (isset($fields[$stylename]) && isset($items[$stylename])) {
				$_style = $fields[$stylename]->get_style($items[$stylename]);
				$str    = sprintf($_style, $str);
			}
		}

		return $escape ? str_replace('|', '&#x7c;', $str) : $str;
	}

	// toString(): Called within preg_replace_callback()
	function _replace_title($matches = array())
	{
		$fields = $this->fields;
		$orders = $this->orders;

		$fieldname = isset($matches[1]) ? $matches[1] : '';
		if (! isset($fields[$fieldname])) {
			// Invalid $fieldname or user's own string or something. Nothing to do
			return isset($matches[0]) ? $matches[0] : '';
		}
		if ($fieldname == '_name' || $fieldname == '_page') $fieldname = '_real';

		$arrow  = '';
		if (isset($orders[$fieldname])) {
			// Sorted
			$order_keys = array_keys($orders);
			$index   = array_flip($order_keys);
			$pos     = 1 + $index[$fieldname];
			$b_end   = ($fieldname == (isset($order_keys[0]) ? $order_keys[0] : ''));
			$b_order = ($orders[$fieldname] === $this->cont['PLUGIN_TRACKER_LIST_SORT_ASC']);
			$order   = ($b_end xor $b_order)
				? $this->cont['PLUGIN_TRACKER_LIST_SORT_ASC']
				: $this->cont['PLUGIN_TRACKER_LIST_SORT_DESC'];
			$arrow   = '&br;' . ($b_order ? '&uarr;' : '&darr;') . '(' . $pos . ')';
			unset($order_keys, $index);
			unset($orders[$fieldname]);
		} else {
			// Not sorted yet, but
			$order = $this->cont['PLUGIN_TRACKER_LIST_SORT_ASC'];	// Default
		}

		// $fieldname become the first, if you click this link
		$_order = array($fieldname . ':' . $this->_sortkey_define2string($order));
		foreach ($orders as $key => $value) {
			$_order[] = $key . ':' . $this->_sortkey_define2string($value);
		}

		$r_config = ($this->config->config_name != $this->cont['PLUGIN_TRACKER_DEFAULT_CONFIG']) ?
			'&config=' . rawurlencode($this->config->config_name) : '';
		$r_list   = ($this->list != $this->cont['PLUGIN_TRACKER_DEFAULT_LIST']) ?
			'&list=' . rawurlencode($this->list) : '';
		return '[[' .
				$fields[$fieldname]->title . $arrow .
				'>' . $this->func->get_script_uri() .
				'?plugin=tracker_list' .
				'&base=' . rawurlencode($this->base) .
				$r_config .
				$r_list .
				'&order=' . rawurlencode(join(';', $_order)) .
				']]';
	}

	// Output a part of Wiki text
	function toString($limit = 0)
	{
		if (empty($this->rows)) {
			$this->error = 'Pages not found under: ' . $this->base . '/';
			return FALSE;
		}

		$rows   = $this->rows;
		$source = array();

		$count = count($this->rows);
		$limit = intval($limit);
		if ($limit != 0) $limit = max(1, $limit);
		if ($limit != 0 && $count > $limit) {
			$source[] = str_replace(
				array('$1',   '$2'  ),
				array($count, $limit),
				$this->func->plugin_tracker_message('msg_limit')
			) . "\n";
			$rows  = array_slice($this->rows, 0, $limit);
		}

		// Loading template
		$header = $body = array();
		foreach (xpwiki_plugin_tracker::plugin_tracker_get_source($this->config->page . '/' . $this->list) as $line) {
			if (preg_match('/^\|(.+)\|[hfc]$/i', $line)) {
				// TODO: Why c and f  here
				$header[] = $line;	// Table header, footer, and decoration
			} else {
				$body[]   = $line;	// The others
			}
		}

		foreach($header as $line) {
			$source[] = preg_replace_callback('/\[([^\[\]]+)\]/', array(& $this, '_replace_title'), $line);
		}
		foreach ($rows as $row) {
			if (! $this->cont['PLUGIN_TRACKER_LIST_SHOW_ERROR_PAGE'] && ! $row['_match']) continue;
			$this->_items = $row;
			foreach ($body as $line) {
				if (ltrim($line) != '') {
					$this->_escape = ($line[0] == '|' || $line[0] == ':');	// The first letter
					$line = preg_replace_callback('/\[([^\[\]]+)\]/', array(& $this, '_replace_item'), $line);
				}
				$source[] = $line;
			}
		}

		return implode('', $source);
	}
}
?>