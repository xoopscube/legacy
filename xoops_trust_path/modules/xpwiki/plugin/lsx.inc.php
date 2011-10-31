<?php
// $Id: lsx.inc.php,v 1.13 2010/05/03 00:09:05 nao-pon Exp $

class xpwiki_plugin_lsx extends xpwiki_plugin {

	var $plugin_lsx;

	//////////////////////////////////
	function plugin_lsx_init()
	{

		$this->plugin_lsx = new XpWikiPluginLsx($this->xpwiki);

		// Modify here for default values
		$this->plugin_lsx->options = array(
			'hierarchy' => array('bool', true),
			'non_list'	=> array('bool', true),
			'reverse'	=> array('bool', false),
			'basename'	=> array('bool', false),
			'sort'		=> array('enum', 'name', array('name', 'date')),
			'tree'		=> array('enum', false, array(false, 'leaf', 'dir')),
			'depth'		=> array('number', ''),
			'num'		=> array('number', ''),
			'except'	=> array('string', ''),
			'filter'	=> array('string', ''),
			'prefix'	=> array('string', ''),
			'contents'	=> array('array', ''),
			'include'	=> array('array', ''),
			'info'		=> array('enumarray', array(), array('date', 'new')),
			'date'		=> array('bool', false), // obsolete
			'new'		=> array('bool', false),
			'tag'		=> array('string', ''),
			'notitle'	=> array('bool', false),
			'order'     => array('bool', false),
			'rtag'		=> array('string', ''),
			'noenhance' => array('bool', false),
		);

		// Modify here for external plugins
		$this->plugin_lsx->plugin_contents = 'contentsx';
		$this->plugin_lsx->plugin_include  = 'includex';
		$this->plugin_lsx->plugin_new	   = 'new';
	}

	function can_call_otherdir_convert() {
		return 1;
	}

	function plugin_lsx_convert()
	{
		$args = func_get_args();
		return call_user_func_array(array($this->plugin_lsx, 'convert'), $args);
	}

	function plugin_lsx_action()
	{
		return call_user_func(array($this->plugin_lsx, 'action'));
	}
}

class XpWikiPluginLsx
{
	function XpWikiPluginLsx(& $xpwiki)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;
	}

	var $options;
	var $error = "";
	var $plugin = "lsx";
	var $metapages;
	var $plugin_contents;
	var $plugin_include;
	var $plugin_new;
	var $plugin_tag = 'tag'; // can not be replaced easily

	var $title;

	function convert()
	{
		$args = func_get_args();
		$body = $this->body($args);
		if ($this->error != "") {
			$body = "<p>$this->plugin(): $this->error</p>";
		}
		return $body;
	}

	function action()
	{
		$args = $this->root->vars;
		$body = $this->body($args, TRUE);
		if ($this->error != "") {
			$body = "<p>$this->plugin(): $this->error</p>";
		}
		if (! isset($body)) $body = '<p>no result.</p>';
		return array('msg'=>($this->title? $this->title : $this->plugin), 'body'=>$body);
	}

	function body($args, $args_decomposed = FALSE)
	{
		$parser = new XpWikiPluginLsxOptionParser();
		$this->options = $parser->parse_options($args, $this->options, $args_decomposed);

		if ($this->options['rtag'][1]) {
			$this->options['tag'] = $this->options['rtag'];
			$this->options['noenhance'][1] = true;
		}

		if ($parser->error != "") { $this->error = $parser->error; return; }

		$this->check_options();
		if ($this->error !== "") { return $this->error; }

		$this->metapages();
		if ($this->error !== "") { return $this->error; }
		$this->relative_metapages();
		if ($this->error !== "") { return $this->error; }

		$this->narrow_metapages();
		if ($this->error !== "") { return $this->error; }

		return $this->frontend();
	}

	function narrow_metapages()
	{
		$this->filter_pages();

		$parser = new XpWikiPluginLsxOptionParser();
		$mdepth = $this->depth_metapages();
		$this->options['depth'][1] = $parser->parse_numoption($this->options['depth'][1], 1, $mdepth);
		if ($parser->error != "") { $this->error = $parser->error; return; }
		$this->depth_filter_pages();

		$this->timestamp_metapages();
		$this->sort_metapages();

		$mnum = sizeof($this->metapages);
		$this->options['num'][1] = $parser->parse_numoption($this->options['num'][1], 1, $mnum);
		if ($parser->error != "") { $this->error = $parser->error; return; }
		$this->num_filter_pages();
	}

	function frontend()
	{
		$this->info_metapages();
		$this->hierarchy_metapages();
		$this->tree_filter_metapages();
		$this->flat_basename_metapages();
		$this->order_metapages();

		$body = $this->list_pages();
		return $body;
	}

	function check_options()
	{
		if ($this->options['tag'][1] != '') {
			if(! $this->func->exist_plugin($this->plugin_tag)) {
				$this->error .= "The option, tag, requires #$this->plugin_tag plugin, but it does not exist. ";
				return;
			}
			$this->options['hierarchy'][1] = false;
			// best is to change only default to off at tag option, though.
		} else {
			if ($this->options['prefix'][1] == '') {
				$this->options['prefix'][1] = $this->cont['PageForRef'] !== '' ? $this->cont['PageForRef'] . '/' : '';
			}
		}
		if ($this->options['prefix'][1] == '/') {
			$this->options['prefix'][1] = '';
		} elseif ($this->options['prefix'][1] != '') {
			$this->options['prefix'][1] = $this->get_fullname($this->options['prefix'][1], $this->cont['PageForRef']);
		}

		if ($this->options['sort'][1] == 'date') {
			$this->options['hierarchy'][1] = false;
		}
		if ($this->options['basename'][1] === true) {
			$this->options['hierarchy'][1] = false;
		}

		if ($this->options['contents'][1] != '') {
			if(! $this->func->exist_plugin_convert($this->plugin_contents)) {
				$this->error .= "The option, contents, requires #$this->plugin_contents plugin, but it does not exist. ";
				return;
			}
		}
		if ($this->options['include'][1] != '') {
			if(! $this->func->exist_plugin_convert($this->plugin_include)) {
				$this->error .= "The option, include, requires #$this->plugin_include plugin, but it does not exist. ";
				return;
			}
			$this->options['hierarchy'][1] = false;
			$this->options['date'][1] = false;
			$this->options['basename'][1] = false;
			$this->options['contents'][1] = '';
		}

		// to support lower versions
		foreach ($this->options['info'][2] as $key) {
			if ($this->options[$key][1]) {
				array_push($this->options['info'][1], $key);
			}
		}
		$this->options['info'][1] = array_unique($this->options['info'][1]);
		// to save time (to avoid in_array everytime)
		foreach ($this->options['info'][1] as $key) {
			$this->options[$key][1] = true;
		}
		if ($this->options['new'][1] && ! $this->func->exist_plugin_inline($this->plugin_new)) {
			$this->error .= "The option, new and cnew, requires #$this->plugin_new plugin, but it does not exist. ";
			return;
		}
	}

	// refer lib/make_link.php#get_fullname
	function get_fullname($name, $refer)
	{
		// 'Here'
		if ($name == '' || $name == './') return $refer;

		// Absolute path
		if ($name{0} == '/') {
			$name = substr($name, 1);
			return ($name == '') ? $this->root->defaultpage : $name;
		}

		// Relative path from 'Here'
		if (substr($name, 0, 2) == './') {
			$arrn	 = preg_split('#/#', $name, -1); //, PREG_SPLIT_NO_EMPTY);
			$arrn[0] = $refer;
			return join('/', $arrn);
		}

		// Relative path from dirname()
		if (substr($name, 0, 3) == '../') {
			$arrn = preg_split('#/#', $name,  -1); //, PREG_SPLIT_NO_EMPTY);
			$arrp = preg_split('#/#', $refer, -1, PREG_SPLIT_NO_EMPTY);

			while (! empty($arrn) && $arrn[0] == '..') {
				array_shift($arrn);
				array_pop($arrp);
			}
			$name = ! empty($arrp) ? join('/', array_merge($arrp, $arrn)) :
				(! empty($arrn) ? $this->root->defaultpage . '/' . join('/', $arrn) : $this->root->defaultpage);
		}

		return $name;
	}

	function list_pages()
	{
		if (sizeof($this->metapages) == 0) {
			return;
		}

		/* HTML validate (without <ul><li style="list-type:none"><ul><li>, we have to do as
		   <ul><li style="padding-left:16*2px;margin-left:16*2px"> as pukiwiki standard. I did not like it)

		<ul>			  <ul><li>1
		<li>1</li>		  </li><li>1
		<li>1			  <ul><li>2
		<ul>			  </li></ul></li><li>1
		<li>2</li>		  </li><li>1
		</ul>		 =>	  <ul><li style="list-type:none"><ul><li>3
		</li>			  </li></ul></li></ul></li></ul>
		<li>1</li>
		<li>1</li>
		<ul><li style="list-type:none"><ul>
		<li>3</li>
		</ul></li></ul>
		</li>
		</ul>
		*/
		$ul = $pdepth = 0;
		$html = "";
		foreach ($this->metapages as $i => $metapage) {
			$page	  = $metapage['page'];
			$relative = $metapage['relative'];
			$exist	  = $metapage['exist'];
			$depth	  = $metapage['listdepth'];
			$info	  = $metapage['info'];
			$order    = $metapage['order'];
			if ($exist && $this->options['include'][1] != '') {
				$option = '"' . $page . '"';
				if (! empty($this->options['include'][1])) {
					$option .= ',' . $this->func->csv_implode(',', $this->options['include'][1]);
				}
				$html .= $this->func->do_plugin_convert($this->plugin_include, $option);
				continue;
			}
			if ($depth > $pdepth) {
				$diff = $depth - $pdepth;
				$html .= str_repeat('<ul><li style="list-style:none">', $diff - 1);
				if ($depth == 1) { // or $first flag
					$html .= '<ul class="list1 ' . $this->plugin . '"><li>';
				} else {
					$html .= '<ul class="list'.$depth.'"><li>';
				}
				$ul += $diff;
			} elseif ($depth == $pdepth) {
				$html .= '</li><li>';
			} elseif ($depth < $pdepth) {
				$diff = $pdepth - $depth;
				$html .= str_repeat('</li></ul>', $diff);
				$html .= '</li><li>';
				$ul -= $diff;
			}
			$pdepth = $depth;

			if (! $this->options['notitle'][1]) {
				$relative .=  ' [' .$this->func->unhtmlspecialchars($this->func->get_heading($page), ENT_QUOTES).']';
			}
			if ($exist) {
				$html .= $this->make_pagelink($page, $relative);
			} else {
				$html .= $relative;
			}
			//$html .= $info . "\n";
			$html .= $info . $order . "\n";

			if ($exist && $this->options['contents'][1] != '') {
				$option = '"page=' . $page . '"';
				if (! empty($this->options['contents'][1])) {
					$option .= ',' . $this->func->csv_implode(',', $this->options['contents'][1]);
				}
				$html .= $this->func->do_plugin_convert($this->plugin_contents, $option);
			}
		}
		$html .= str_repeat('</li></ul>', $ul);
		return $html;
	}

	function make_pagelink($page, $alias)
	{
		$tmp = $this->root->show_passage; $this->root->show_passage = 0;
		$link = $this->func->make_pagelink($page, htmlspecialchars($alias));
		$this->root->show_passage = $tmp;
		return $link;
	}

	function timestamp_metapages()
	{
		if (! $this->options['date'][1] && ! $this->options['new'][1] &&
			$this->options['sort'][1] !== 'date') {
			return;
		}
		foreach ($this->metapages as $i => $metapage) {
			$page = $metapage['page'];
			$timestamp = $this->get_filetime($page);
			$this->metapages[$i]['timestamp'] = $timestamp;
		}
	}

	function date_metapages()
	{
		if (! $this->options['date'][1] && ! $this->options['new'][1]) {
			return;
		}
		foreach ($this->metapages as $i => $metapage) {
			$timestamp = $metapage['timestamp'];
			$date = $this->func->format_date($timestamp);
			$this->metapages[$i]['date'] = $date;
		}
	}

	function new_metapages()
	{
		if (! $this->options['new'][1]) {
			return;
		}
		foreach ($this->metapages as $i => $metapage) {
			$date = $this->metapages[$i]['date'];
			// burdonsome, but to use configuration of new plugin
			$new = $this->func->do_plugin_inline($this->plugin_new, 'nodate', $date);
			$this->metapages[$i]['new'] = $new;
		}
	}

	function info_metapages()
	{
		if (empty($this->options['info'][1])) {
			return;
		}

		$this->date_metapages();
		$this->new_metapages();
		//foreach ($this->options['info'][2] as $key) {
		//	  call_user_func(array($this, $key . '_metapages'));
		//}
		foreach ($this->metapages as $i => $metapage) {
			$info = '';
			foreach ($this->options['info'][1] as $key) {
				$info .= ' ' . $metapage[$key];
			}
			$this->metapages[$i]['info'] = $info;
		}
	}

	function order_metapages()
	{
		if (! $this->options['order'][1]) {
			return;
		}
		foreach ($this->metapages as $i => $metapage) {
			$page = $this->metapages[$i]['page'];
			$pginfo = $this->func->get_pginfo($page);
			$this->metapages[$i]['order'] = ' =&gt; ' . $pginfo['pgorder'];
		}
	}

	function flat_basename_metapages()
	{
		if ($this->options['basename'][1] === false) {
			return;
		}
		if ($this->options['hierarchy'][1] === true) {
			return;
		}
		foreach ($this->metapages as $i => $metapage) {
			$basename  = $this->basename($metapage['page']);
			$this->metapages[$i]['relative'] = $basename;
		}
	}

	function tree_filter_metapages()
	{
		if ($this->options['tree'][1] === false) {
			return;
		}
		$metapages = array();
		foreach ($this->metapages as $i => $metapage) {
			unset($this->metapages[$i]);
			if ($metapage['leaf'] === false) {
				if ($this->options['tree'][1] == 'dir') {
					$metapages[] = $metapage;
				}
			} else {
				if ($this->options['tree'][1] == 'leaf') {
					$metapages[] = $metapage;
				}
			}
		}
		$this->metapages = $metapages;
	}

	function hierarchy_metapages()
	{
		if ($this->options['hierarchy'][1] === false && $this->options['tree'][1] === false) {
			return;
		}
		$pdepth = substr_count($this->options['prefix'][1], '/') - 1;
		$num = count($this->metapages);
		foreach ($this->metapages as $i => $metapage) {
			$page  = $metapage['page'];
			$depth = $metapage['depth']; // depth_metapages()
			if ($this->options['hierarchy'][1] === true) {
				$this->metapages[$i]['relative'] = $this->basename($page);
				$this->metapages[$i]['listdepth'] = $depth;
			}
			while ($depth > 1) {
				$page = $this->dirname($page);
				if ($page === '') {
					break;
				} if (($j = $this->array_search_by($page, $this->metapages, 'page')) !== false) {
					// only for tree, though
					$this->metapages[$j]['leaf'] = false;
					break;
				} else {
					$depth = substr_count($page, '/') - $pdepth;
					if ($this->options['hierarchy'][1] === true) {
						$relative = $this->basename($page);
						$listdepth = $depth;
					} else {
						$relative = $page;
						$listdepth = 1;
					}
					$this->metapages[] = array('page'=>$page, 'relative'=>$relative, 'exist'=>false, 'depth'=>$depth, 'listdepth'=>$listdepth, 'timestamp'=>1, 'date'=>'', 'leaf'=>false, 'info'=>'', 'order'=>'');
					// PHP: new item is not seen at this loop
				}
			}
		}
		if (count($this->metapages) != $num) {
			$this->sort_metapages();
		}
	}

	function array_search_by($value, $array, $fieldname = null)
	{
		foreach ($array as $i => $val) {
			if ($value == $val[$fieldname]) {
				return $i;
			}
		}
		return false;
	}

	function in_array_by($value, $array, $fieldname = null)
	{
		//foreach ($array as $i => $befree) {
		//	  $field_array[$i] = $array[$i][$fieldname];
		//}
		//return in_array($value, $field_array);

		foreach ($array as $i => $val) {
			if ($value == $val[$fieldname]) {
				return true;
			}
		}
		return false;
	}

	function sort_metapages()
	{
		switch ($this->options['sort'][1]) {
		case 'name':
			//$this->sort_by($this->metapages, 'page', 'natcasesort');
			$this->sort_by($this->metapages, 'page', 'pagesort');
			break;
		case 'date':
			$this->sort_by($this->metapages, 'timestamp', 'rsort', SORT_NUMERIC);
			break;
		default:
			return; // error
		}

		if ($this->options['reverse'][1]) {
			$this->metapages = array_reverse($this->metapages);
		}
	}

	# sort arrays by a specific field without maintaining key association
	function sort_by(&$array,  $fieldname = null, $sort, $sortflag = SORT_REGULAR)
	{
		$field_array = $inarray = array();
		# store the keyvalues in a seperate array
		foreach ($array as $i => $befree) {
			$field_array[$i] = $array[$i][$fieldname];
		}
		switch ($sort) {
		case 'sort':
			# sort an array and maintain index association...
			asort($field_array, $sortflag);
			break;
		case 'rsort':
			# sort an array in reverse order and maintain index association
			arsort($field_array, $sortflag);
			break;
		case 'natsort':
			natsort($field_array);
			break;
		case 'natcasesort':
			# sort an array using a case insensitive "natural order" algorithm
			natcasesort($field_array);
			break;
		case 'pagesort':
			# sort an array using a case insensitive "natural order" algorithm
			$this->func->pagesort($field_array);
			break;
		}
		# rebuild the array
		$outarray = array();
		foreach ( $field_array as $i=> $befree) {
			$outarray[] = $array[$i];
			unset($array[$i]);
		}
		$array = $outarray;
	}

	function max_by($array, $fieldname = null)
	{
		$field_array = $inarray = array();
		# store the keyvalues in a seperate array
		foreach ($array as $i => $befree) {
			$field_array[$i] = $array[$i][$fieldname];
		}
		return ($field_array)? max($field_array) : 0;
	}

	function depth_metapages()
	{
		if ($this->options['depth'][1] === '' && $this->options['hierarchy'][1] === false &&
			$this->options['tree'][1] === false ) {
			return;
		}
		$pdepth = substr_count($this->options['prefix'][1], '/') - 1;

		foreach ($this->metapages as $i => $metapage) {
			$page  = $metapage['page'];
			$depth = substr_count($page, '/');
			$this->metapages[$i]['depth']	= $depth - $pdepth;
		}

		return $this->max_by($this->metapages, 'depth');
	}

	function relative_metapages()
	{
		$pdir = $this->dirname($this->options['prefix'][1]);
		if ($pdir == '') {
			return;
		} else {
			$pdirlen = strlen($pdir) + 1; // Add '/'
		}

		foreach ($this->metapages as $i => $metapage) {
			$page  = $metapage['page'];
			$relative = substr($page, $pdirlen);
			$this->metapages[$i]['relative'] = $relative;
		}
	}

	function metapages()
	{
		if ($this->options['tag'][1] == '') {
			$pages = $this->get_existpages();
		} else {
			$plugin_tag = new XpWikiPluginTag($this->xpwiki);
			$pages = $plugin_tag->get_taggedpages($this->options['tag'][1], $this->options['noenhance'][1]);
			if ($pages === FALSE) {
				$this->error  = 'The tag token, ' . $this->options['tag'][1] . ', is invalid. ';
				$this->error .= 'Perhaps, the tag does not exist. ';
			}
			$this->title = $this->root->_title_list." [Tag: ".htmlspecialchars($this->options['tag'][1])." ]";
		}
		$metapages = array();
		foreach ($pages as $i => $page) {
			unset($pages[$i]);
			$metapages[] = array('page'=>$page, 'relative'=>$page, 'exist'=>true, 'depth'=>1, 'listdepth'=>1, 'timestamp'=>1, 'date'=>'', 'info'=>'', 'order'=>'');
		}
		$this->metapages = $metapages;
	}

	function dirname($page)
	{
		// dirname(Page/) => '.' , dirname(Page/a) => Page, dirname(Page) => '.'
		// But, want Page/ => Page, Page/a => Page, Page => ''
		if (($pos = strrpos($page, '/')) !== false) {
			return substr($page, 0, $pos);
		} else {
			return '';
		}
	}
	function basename($page)
	{
		// basename(Page/) => Page , basename(Page/a) => a, basename(Page) => Page
		// But, want Page/ => '', Page/a => a, Page => Page
		if (($pos = strrpos($page, '/')) !== false) {
			return substr($page, $pos + 1);
		} else {
			return $page;
		}
	}

	function depth_filter_pages()
	{
		if ($this->options['depth'][1] === '') {
			return;
		}
		$metapages = array();
		foreach ($this->metapages as $i => $metapage) {
			unset($this->metapages[$i]);
			if (in_array($metapage['depth'], $this->options['depth'][1])) {
				$metapages[] = $metapage;
			}
		}
		$this->metapages = $metapages;
	}

	// sort before this ($this->sort_by)
	function num_filter_pages()
	{
		if ($this->options['num'][1] === '') {
			return;
		}
		$metapages = array();
		// $num < count($this->metapages) is assured.
		foreach ($this->options['num'][1] as $num) {
			$metapages[] = $this->metapages[$num - 1];
		}
		$this->metapages = $metapages;
	}

	function filter_pages()
	{
		$metapages = array();
		foreach ($this->metapages as $i => $metapage) {
			unset($this->metapages[$i]);
			$page = $metapage['page'];
			$relative = $metapage['relative'];
			if ($this->options['prefix'][1] !== "") {
				if (strpos($page, $this->options['prefix'][1]) !== 0) {
					continue;
				}
			}
			if ($this->options['except'][1] !== "") {
				if (ereg($this->options['except'][1], $relative)) {
					continue;
				}
			}
			if ($this->options['filter'][1] !== "") {
				if (!ereg($this->options['filter'][1], $relative)) {
					continue;
				}
			}
			if ($this->options['non_list'][1]) {
				if (preg_match("/{$this->root->non_list}/", $page)) {
					continue;
				}
			}
			$metapages[] = $metapage;
		}
		$this->metapages = $metapages;
	}

	function get_existpages()
	{
		return $this->func->get_existpages(FALSE, $this->options['prefix'][1]);
	}

	function get_filetime($page)
	{
		return $this->func->get_filetime($page);
	}
}
///////////////////////////////////////
class XpWikiPluginLsxOptionParser
{
	var $error = "";

	function parse_options($args, $options, $decomposed = FALSE)
	{
		if (! $decomposed) {
			$args = $this->decompose_args($args, $options);
			if ($this->error != "") { return; }
		}

		foreach ($args as $key => $val) {
			if ( !isset($options[$key]) ) { continue; } // for action ($vars)
			$type = $options[$key][0];

			switch ($type) {
			case 'bool':
				if($val == "" || $val == "on" || $val == "true") {
					$options[$key][1] = true;
				} elseif ($val == "off" || $val == "false" ) {
					$options[$key][1] = false;
				} else {
					$this->error = "$key=$val is invalid. ";
					$this->error .= "The option, $key, accepts only a boolean value.";
					$this->error .= "#$this->plugin($key) or #$this->plugin($key=on) or #$this->plugin($key=true) for true. ";
					$this->error .= "#$this->plugin($key=off) or #$this->plugin($key=false) for false. ";
					return;
				}
				break;
			case 'string':
				$options[$key][1] = $val;
				break;
			case 'sanitize':
				$options[$key][1] = htmlspecialchars($val);
				break;
			case 'number':
				// Do not parse yet, parse after getting min and max. Here, just format checking
				if ($val === '') {
					$options[$key][1] = '';
					break;
				}
				if ($val[0] === '(' && $val[strlen($val) - 1] == ')') {
					$val = substr($val, 1, strlen($val) - 2);
				}
				foreach (explode(",", $val) as $range) {
					if (preg_match('/^-?\d+$/', $range)) {
					} elseif (preg_match('/^-?\d*\:-?\d*$/', $range)) {
					} elseif (preg_match('/^-?\d+\+-?\d+$/', $range)) {
					} else {
						$this->error = "$key=$val is invalid. ";
						$this->error .= "The option, " . $key . ", accepts number values such as 1, 1:3, 1+3, 1,2,4. ";
						$this->error .= "Specify options as \"$key=1,2,4\" or $key=(1,2,3) when you want to use \",\". ";
						$this->error .= "In more details, a style like (1:3,5:7,9:) is also possible. 9: means from 9 to the last. ";
						$this->error .= "Furtermore, - means backward. -1:-3 means 1,2,3 from the tail. ";
						return;
					}
				}
				$options[$key][1] = $val;
				break;
			case 'enum':
				if($val == "") {
					$options[$key][1] = $options[$key][2][0];
				} elseif (in_array($val, $options[$key][2])) {
					$options[$key][1] = $val;
				} else {
					$this->error = "$key=$val is invalid. ";
					$this->error .= "The option, " . $key . ", accepts values from one of (" . join(",", $options[$key][2]) . "). ";
					$this->error .= "By the way, #$this->plugin($key) equals to #$this->plugin($key=" . $options[$key][2][0] . "). ";
					return;
				}
				break;
			case 'array':
				if ($val == '') {
					$options[$key][1] = array();
					break;
				}
				if ($val[0] === '(' && $val[strlen($val) - 1] == ')') {
					$val = substr($val, 1, strlen($val) - 2);
				}
				$val = explode(',', $val);
				$val = $this->compose_array_args($val); // allows recursive parens such as (())
				$options[$key][1] = $val;
				break;
			case 'enumarray':
				if ($val == '') {
					$options[$key][1] = $options[$key][2];
					break;
				}
				if ($val[0] === '(' && $val[strlen($val) - 1] == ')') {
					$val = substr($val, 1, strlen($val) - 2);
				}
				$val = explode(',', $val);
				$val = $this->compose_array_args($val); // allows recursive parens such as (())
				$options[$key][1] = $val;
				foreach ($options[$key][1] as $each) {
					if (! in_array($each, $options[$key][2])) {
						$this->error = "$key=" . join(",", $options[$key][1]) . " is invalid. ";
						$this->error .= "The option, " . $key . ", accepts sets of values from (" . join(",", $options[$key][2]) . "). ";
						$this->error .= "By the way, #$this->plugin($key) equals to #$this->plugin($key=(" . join(',',$options[$key][2]) . ")). ";
						return;
					}
				}
				break;
			default:
			}
		}

		return $options;
	}

	function decompose_args($args, $options)
	{
		$args = $this->compose_array_args($args);
		if ($this->error !== "") { return; }

		$newargs = array();
		foreach ($args as $num => $arg) {
			list($key, $val) = array_pad(explode("=", $arg, 2), 2, '');
			if (! isset($options[$key])) {
				if ($num === 0) {
					$val = $key;
					$key = 'prefix';
				} else {
					$this->error = "No such a option, $key. ";
					return;
				}
			}
			$newargs[$key] = $val;
		}
		return $newargs;
	}

	function compose_array_args($comma_exploded_args)
	{
		$args = $comma_exploded_args;
		$result = array();
		while (($arg = current($args)) !== false) {
			@list($key, $val) = explode("=", $arg, 2);
			if (isset($val)) {
				if ($val[0] === '(') {
					while(true) {
						if ($val[strlen($val) - 1] === ')'
							&& substr_count($val, '(') == substr_count($val, ')')) {
							break;
						}
						$arg = next($args);
						if ($arg === false) {
							$this->error = "The # of open and close parentheses of one of your arguments did not match. ";
							return;
						}
						$val .= ',' . $arg;
					}
				}
				$result[] = "$key=$val";
			} else {
				$result[] = $key;
			}
			next($args);
		}
		return $result;
	}

	function parse_numoption($optionval, $min, $max)
	{
		if ($optionval === '') {
			return '';
		}
		$result = array();
		foreach (explode(",", $optionval) as $range) {
			if (preg_match('/^-?\d+$/', $range)) {
				$left = $right = $range;
			} elseif (preg_match('/^-?\d*\:-?\d*$/', $range)) {
				list($left, $right) = explode(":", $range, 2);
				if ($left == "" && $right == "") {
					$left = $min;
					$right = $max;
				} elseif($left == "") {
					$left = $min;
				} elseif ($right == "") {
					$right = $max;
				}
			} elseif (preg_match('/^-?\d+\+-?\d+$/', $range)) {
				list($left, $right) = explode("+", $range, 2);
				$right += $left;
			}
			if ($left < 0) {
				$left += $max + 1;
			}
			if ($right < 0) {
				$right += $max + 1;
			}
			$result = array_merge($result, range($left, $right));
			// range allows like range(5, 3) also
		}
		// filter
		foreach (array_keys($result) as $i) {
			if ($result[$i] < $min || $result[$i] > $max) {
				unset($result[$i]);
			}
		}
		sort($result);
		$result = array_unique($result);

		return $result;
	}

	function option_debug_print($options) {
		foreach ($options as $key => $val) {
			$type = $val[0];
			$val = $val[1];
			if(is_array($val)) {
				$val=join(',', $val);
			}
			$body .= "$key=>($type, $val),";
		}
		return $body;
	}
}
?>