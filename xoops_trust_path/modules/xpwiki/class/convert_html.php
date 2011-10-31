<?php

// Block elements
class XpWikiElement {
	var $parent;
	var $elements; // References of childs
	var $last; // Insert new one at the back of the $last

	var $flg; // Any flag

	var $xpwiki;

	function XpWikiElement(& $xpwiki) {

		$this->xpwiki = & $xpwiki;
		$this->root = & $xpwiki->root;
		$this->cont = & $xpwiki->cont;
		$this->func = & $xpwiki->func;

		$this->elements = array ();
		$this->last = & $this;
	}

	function setParent(& $parent) {
		$this->parent = & $parent;
	}

	function & add(& $obj) {
		if ($this->canContain($obj)) {
			return $this->insert($obj);
		} else {
			return $this->parent->add($obj);
		}
	}

	function & insert(& $obj) {
		$obj->setParent($this);
		$this->elements[] = & $obj;

		return $this->last = & $obj->last;
	}

	function canContain($obj) {
		return TRUE;
	}

	function wrap($string, $tag, $param = '', $canomit = TRUE) {
		return ($canomit && $string === '') ? '' :
			($tag ? '<' . $tag . $param . '>' . $string . '</' . $tag . '>' : $string);

	}

	function toString() {
		$ret = '';
		foreach ($this->elements as $value) {
			if ($ret !== '') $ret .= "\n";
			$ret .= $value->toString();
			$value->GC();
		}
		return $ret;
	}

	function GC() {
		// Garbage Collection
		$this->elements = NULL;
		if (! is_a($this->last, 'XpWikiBody')) {
			$this->last = NULL;
		}
	}

	function dump($indent = 0) {
		$ret = str_repeat(' ', $indent).get_class($this)."\n";
		$indent += 2;
		foreach (array_keys($this->elements) as $key) {
			$ret .= is_object($this->elements[$key]) ? $this->elements[$key]->dump($indent) : '';
			//str_repeat(' ', $indent) . $this->elements[$key];
		}
		return $ret;
	}
}

// Inline elements
class XpWikiInline extends XpWikiElement {
	function XpWikiInline(& $xpwiki, $text) {
		parent :: XpWikiElement($xpwiki);
		$this->elements[] = trim((substr($text, 0, 1) === "\n") ? $text : $this->func->make_link($text));
	}

	function & insert(& $obj) {
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function canContain($obj) {
		return is_a($obj, 'XpWikiInline');
	}

	function toString() {
		return join(($this->root->line_break ? '<br />'."\n" : "\n"), $this->elements);
	}

	function & toPara($class = '') {
		$obj = & new XpWikiParagraph($this->xpwiki, '', $class);
		$obj->insert($this);
		return $obj;
	}
}

// Paragraph: blank-line-separated sentences
class XpWikiParagraph extends XpWikiElement {
	var $param;

	function XpWikiParagraph(& $xpwiki, $text, $param = '') {
		parent :: XpWikiElement($xpwiki);
		$this->param = $param;
		if ($text === '')
			return;

		if (substr($text, 0, 1) === '~')
			$text = ' '.substr($text, 1);
		$this->insert($this->func->Factory_Inline($text));
	}

	function canContain($obj) {
		return is_a($obj, 'XpWikiInline');
	}

	function toString() {
		return $this->wrap(parent :: toString(), 'p', $this->param);
	}
}

// * Heading1
// ** Heading2
// *** Heading3
class XpWikiHeading extends XpWikiElement {
	var $level;
	var $id;
	var $paraid;
	var $msg_top;
	var $class;

	function XpWikiHeading(& $root, $text) {
		parent :: XpWikiElement($root->xpwiki);

		$this->level = min(5, strspn($text, '*'));
		list ($text, $anchor, $this->msg_top, $this->id, $this->paraid) = $root->getAnchor($text, $this->level);
		if (! trim($text)) {
			$this->class = ' class="none"';
		} else {
			$this->class = '';
		}
		$text .= $anchor;
		$this->insert($root->func->Factory_Inline($text));
		$this->level++; // h2,h3,h4
	}

	function & insert(& $obj) {
		parent :: insert($obj);
		return $this->last = & $this;
	}

	function canContain(& $obj) {
		return FALSE;
	}

	function toString() {
		// Area div id Close & Open
		$area_div = $this->func->get_areadiv_closer($this->level);
		$area_div .= '<div id="'.$this->paraid.'" class="level'.$this->level.'">' . "\n";

		// Area div id
		$this->root->rtf['div_area_open'][$this->root->rtf['convert_nest']][$this->level][] = $this->paraid;

		return $area_div . $this->msg_top . $this->wrap(parent :: toString(), 'h'.$this->level, ' id="'.$this->id.'"'.$this->class);
	}
}

// ----
// Horizontal Rule
class XpWikiHRule extends XpWikiElement {
	function XpWikiHRule(& $root, $text) {
		parent :: XpWikiElement($root->xpwiki);
	}

	function canContain(& $obj) {
		return FALSE;
	}

	function toString() {
		return $this->root->hr;
	}
}

// Lists (UL, OL, DL)
class XpWikiListContainer extends XpWikiElement {
	var $tag;
	var $tag2;
	var $level;
	var $style;
	var $margin;
	var $left_margin;

	function XpWikiListContainer(& $xpwiki, $tag, $tag2, $head, $text) {
		parent :: XpWikiElement($xpwiki);

		$var_margin = '_'.$tag.'_margin';
		$var_left_margin = '_'.$tag.'_left_margin';

		$this->margin = $this->root-> $var_margin;
		$this->left_margin = $this->root-> $var_left_margin;

		$this->tag = $tag;
		$this->tag2 = $tag2;
		$this->level = strspn($text, $head);
		$text = ltrim(substr($text, $this->level));

		$style = '';
		if (substr($text, -1) === "\x08") {
			$tag2 = 'li';
			$style = ' class="list_none"';
			$text = '';
		}

		parent :: insert(new XpWikiListElement($this->xpwiki, $this->level, $tag2, $style));
		if ($text !== '') {
			$this->last = & $this->last->insert($this->func->Factory_Inline($text));
		}
	}

	function canContain(& $obj) {
		return (!is_a($obj, 'XpWikiListContainer') || ($this->tag === $obj->tag && $this->level === $obj->level));
	}

	function setParent(& $parent) {
		parent :: setParent($parent);

		$step = $this->level;
		if (isset ($parent->parent) && is_a($parent->parent, 'XpWikiListContainer'))
			$step -= $parent->parent->level;

		$margin = $this->margin * $step;
		if ($step === $this->level)
			$margin += $this->left_margin;

		$this->style = sprintf($this->root->_list_pad_str, $this->level, $margin, $margin);
	}

	function & insert(& $obj) {
		if (!is_a($obj, get_class($this)))
			return $this->last = & $this->last->insert($obj);

		// Break if no elements found (BugTrack/524)
		if (count($obj->elements) === 1 && empty ($obj->elements[0]->elements))
			return $this->last->parent; // up to ListElement

		// Move elements
		foreach (array_keys($obj->elements) as $key) {
			parent :: insert($obj->elements[$key]);
		}

		return $this->last;
	}

	function toString() {
		return $this->wrap(parent :: toString(), $this->tag, $this->style);
	}
}

class XpWikiListElement extends XpWikiElement {
	function XpWikiListElement(& $xpwiki, $level, $head, $style='') {
		parent :: XpWikiElement($xpwiki);
		$this->level = $level;
		$this->head = $head;
		$this->style = $style;
	}

	function canContain(& $obj) {
		return (!is_a($obj, 'XpWikiListContainer') || ($obj->level > $this->level));
	}

	function toString() {
		return $this->wrap(parent :: toString(), $this->head, $this->style);
	}
}

// - One
// - Two
// - Three
class XpWikiUList extends XpWikiListContainer {
	function XpWikiUList(& $root, $text) {
		parent :: XpWikiListContainer($root->xpwiki, 'ul', 'li', '-', $text);
	}
}

// + One
// + Two
// + Three
class XpWikiOList extends XpWikiListContainer {
	function XpWikiOList(& $root, $text) {
		parent :: XpWikiListContainer($root->xpwiki, 'ol', 'li', '+', $text);
	}
}

// : definition1 | description1
// : definition2 | description2
// : definition3 | description3
class XpWikiDList extends XpWikiListContainer {
	function XpWikiDList(& $xpwiki, $out) {
		parent :: XpWikiListContainer($xpwiki, 'dl', 'dt', ':', $out[0]);
		$this->last = & XpWikiElement :: insert(new XpWikiListElement($xpwiki, $this->level, 'dd'));
		if ($out[1] !== '')
			$this->last = & $this->last->insert($xpwiki->func->Factory_Inline($out[1]));
	}
}

// > Someting cited
// > like E-mail text
class XpWikiBQuote extends XpWikiElement {
	var $level;

	function XpWikiBQuote(& $root, $text) {
		parent :: XpWikiElement($root->xpwiki);

		$head = substr($text, 0, 1);
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));

		if ($head === '<') { // Blockquote close
			$level = $this->level;
			$this->level = 0;
			$this->last = & $this->end($root, $level);
			if ($text !== '')
				$this->last = & $this->last->insert($this->func->Factory_Inline($text));
		} else {
			$this->insert($this->func->Factory_Inline($text));
		}
	}

	function canContain(& $obj) {
		return (!is_a($obj, get_class($this)) || $obj->level >= $this->level);
	}

	function & insert(& $obj) {
		// BugTrack/521, BugTrack/545
		if (is_a($obj, 'XpWikiinline'))
			return parent :: insert($obj->toPara(' class="quotation"'));

		if (is_a($obj, 'XpWikiBQuote') && $obj->level === $this->level && count($obj->elements)) {
			$obj = & $obj->elements[0];
			if (is_a($this->last, 'XpWikiParagraph') && count($obj->elements))
				$obj = & $obj->elements[0];
		}
		return parent :: insert($obj);
	}

	function toString() {
		return $this->wrap(parent :: toString(), 'blockquote');
	}

	function & end(& $root, $level) {
		$parent = & $root->last;

		while (is_object($parent)) {
			if (is_a($parent, 'XpWikiBQuote') && $parent->level === $level)
				return $parent->parent;
			$parent = & $parent->parent;
		}
		return $this;
	}
}

class XpWikiTableCell extends XpWikiElement {
	var $tag = 'td'; // {td|th}
	var $colspan = 1;
	var $rowspan = 1;
	var $style; // is array('width'=>, 'align'=>...);

	function XpWikiTableCell(& $xpwiki, $text, $is_template = FALSE) {
		parent :: XpWikiElement($xpwiki);
		$this->style = $matches = array ();

		if ($this->root->extended_table_format) {
			$text = $this->get_cell_style($text);
		}

		while (preg_match('/^(?:(LEFT|CENTER|RIGHT)|(BG)?COLOR\(([#\w]+)\)|SIZE\((\d+)\)):(.*)$/', $text, $matches)) {
			if ($matches[1]) {
				$this->style['align'] = 'text-align:'.strtolower($matches[1]).';';
				$text = $matches[5];
			} else
				if ($matches[3]) {
					$name = $matches[2] ? 'background-color' : 'color';
					$this->style[$name] = $name.':'.htmlspecialchars($matches[3]).';';
					$text = $matches[5];
				} else
					if ($matches[4]) {
						$this->style['size'] = 'font-size:'.htmlspecialchars($matches[4]).'px;';
						$text = $matches[5];
					}
		}

		// Text alignment
		if (empty($this->style['align'])) {
			if ($this->root->symbol_cell_align && preg_match('/^(<|=|>)(.+)$/', rtrim($text), $matches)) {
			// Text alignment with "<" or "=" or ">".
				if ($matches[1] === '=') {
					$this->style['align'] = 'text-align:center;';
				} else if ($matches[1] === '>') {
					$this->style['align'] = 'text-align:right;';
				} else if ($matches[1] === '<') {
					$this->style['align'] = 'text-align:left;';
				}
				$text = $matches[2];
			} else if ($this->root->space_cell_align && preg_match('/^(\s+)?(.+?)(\s+)?$/', $text, $matches)) {
			// Text alignment with 1 or more spaces.
				if ($matches[2] !== '~') {
					if (! empty($matches[1]) && ! empty($matches[3])) {
						$this->style['align'] = 'text-align:center;';
					} else if (! empty($matches[1])) {
						$this->style['align'] = 'text-align:right;';
					} else if (! empty($matches[3])) {
						$this->style['align'] = 'text-align:left;';
					}
					if (! empty($this->style['align'])) {
						$text = $matches[2];
					}
				}
			}
		}

		if ($is_template && is_numeric($text))
			$this->style['width'] = 'width:'.$text.'px;';

		if (rtrim($text) === '<' || ($this->root->empty_cell_join && $text === '')) {
			$this->colspan = -1;
		} else if (rtrim($text) === '>') {
			$this->colspan = 0;
		} else {
			if (in_array($text, array('~', '^'))) {
				$this->rowspan = 0;
			} else {
				if (substr($text, 0, 1) === '~') {
					$this->tag = 'th';
					$text = substr($text, 1);
				}
			}
		}

		if ($text !== '' && $text { 0 } === '#') {
			// Try using Div class for this $text
			$obj = & $this->func->Factory_Div($text);
			if (is_a($obj, 'XpWikiParagraph'))
				$obj = & $obj->elements[0];
		} else {
			$obj = & $this->func->Factory_Inline($text);
		}

		$this->insert($obj);
	}

	function setStyle(& $style) {
		foreach ($style as $key => $value)
			if (!isset ($this->style[$key]))
				$this->style[$key] = $value;
	}

	function toString() {
		if ($this->rowspan === 0 || $this->colspan < 1)
			return '';

		$param = ' class="style_'.$this->tag.'"';
		if ($this->rowspan > 1)
			$param .= ' rowspan="'.$this->rowspan.'"';
		if ($this->colspan > 1) {
			$param .= ' colspan="'.$this->colspan.'"';
			unset ($this->style['width']);
		}
		if (!empty ($this->style))
			$param .= ' style="'.join(' ', $this->style).'"';

		return $this->wrap(parent :: toString(), $this->tag, $param, FALSE);
	}

	function get_cell_style($string) {
		$cells = explode('|',$string,2);
//		echo "CELL: {$cells[0]}\n";
		$colors_reg = "aqua|navy|black|olive|blue|purple|fuchsia|red|gray|silver|green|teal|lime|white|maroon|yellow|transparent";
		// 文字色
		if (preg_match("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['color'] = "color:".$tmp[1].";";
			$cells[0] = preg_replace("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,(?:no|one(?:ce)?|1))??\) ?)/i","FC:$2",$cells[0]);
			$cells[0] = preg_replace("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i","",$cells[0]);
		}

		// セル背景色
		if (preg_match("/(?:[SCB]C):(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['background-color'] = "background-color:".$tmp[1].";";
			$cells[0] = preg_replace("/(?:[SCB]C):(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,(?:no|one(?:ce)?|1))?\)) ?/i","CC:$2",$cells[0]);
			$cells[0] = preg_replace("/(?:[SCB]C):(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i","",$cells[0]);
		}
		// セル背景画
		if (preg_match("/(?:[SCB]C):\(([^),]*)(,once|,1)?\) ?/i",$cells[0],$tmp)) {
			if (strpos($tmp[1], $this->cont['ROOT_URL']) === 0) {
				$tmp[1] = htmlspecialchars($tmp[1]);
				$this->style['background-image'] .= "background-image: url(".$tmp[1].");";
				if (!empty($tmp[2])) $this->style['background-image'] .= "background-repeat: no-repeat;";
			}
			$cells[0] = preg_replace("/(?:[SCB]C):\(([^),]*)(,once|,1)?\) ?/i","",$cells[0]);
		}
		// ボーダー
		if (preg_match("/K:([0-9]+),?([0-9]*)\(?(one|s(?:olid)?|da(?:sh(?:ed)?)?|do(?:tt(?:ed)?)?|two|d(?:ouble)?|boko|g(?:roove)?|deko|r(?:idge)?|in?(?:set)?|o(?:ut(?:set)?)?)?\)? ?/i",$cells[0],$tmp)) {
			if (array_key_exists (3,$tmp)) {
				switch (strtolower($tmp[3])) {
					case 'one':
					case 's':
					case 'solid':
				 		$border_type = "solid";
				 		break;
					case 'two':
					case 'd':
					case 'double':
						$border_type = "double";
				 		break;
					case 'boko':
					case 'g':
					case 'groove':
						$border_type = "groove";
				 		break;
					case 'deko':
					case 'r':
					case 'ridge':
						$border_type = "ridge";
				 		break;
					case 'in':
					case 'i':
					case 'inset':
						$border_type = "inset";
				 		break;
					case 'out':
					case 'o':
					case 'outset':
						$border_type = "outset";
				 		break;
					case 'dash':
					case 'da':
					case 'dashed':
						$border_type = "dashed";
				 		break;
					case 'dott':
					case 'do':
					case 'dotted':
						$border_type = "dotted";
				 		break;
					default:
						$border_type = "outset";
				}
			} else {
				$border_type = "outset";
			}
			//$this->table_style .= " border=\"".$tmp[1]."\"";
			if (array_key_exists (1,$tmp)) {
				if ($tmp[1]==="0"){
					$this->style['border'] = "border:none;";
				} else {
					$this->style['border'] = "border:".$border_type." ".$tmp[1]."px;";
				}
			}
			if (array_key_exists (2,$tmp)) {
				if ($tmp[2]!=""){
					$this->style['padding'] = " padding:".$tmp[2].";";
				} else {
					$this->style['padding'] = " padding:5px;";
				}
			}
			$cells[0] = preg_replace("/K:([0-9]+),?([0-9]*)\(?(one|s(?:olid)?|da(?:sh(?:ed)?)?|do(?:tt(?:ed)?)?|two|d(?:ouble)?|boko|g(?:roove)?|deko|r(?:idge)?|in?(?:set)?|o(?:ut(?:set)?)?)?\)? ?/i","",$cells[0]);
		} else {
//			$this->style['border'] = "border:none;";
		}
		// ボーダー色指定
		if (preg_match("/KC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['border-color'] = "border-color:".$tmp[1].";";
			$cells[0] = preg_replace("/KC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i","",$cells[0]);
		}
		// セル規定文字揃え、幅指定
		if (preg_match("/(?:^ *)(?:(LEFT|CENTER|RIGHT)?:(TOP|MIDDLE|BOTTOM)?)?(?::([0-9]+[%]?))? ?/i",$cells[0],$tmp)) {
			//var_dump($tmp); echo "<br>\n";
			if (@$tmp[1] || @$tmp[2] || @$tmp[3]) {
				if (@$tmp[3]) {
					if (!strpos($tmp[3],"%")) $tmp[3] .= "px";
					$this->style['width'] = "width:".$tmp[3].";";
				}
				if (@$tmp[1]) $this->style['align'] = "text-align:".strtolower($tmp[1]).";";
				if (@$tmp[2]) $this->style['valign'] = "vertical-align:".strtolower($tmp[2]).";";
				$cells[0] = preg_replace("/(?:^ *)(?:(LEFT|CENTER|RIGHT)?:(TOP|MIDDLE|BOTTOM)?)?(?::([0-9]+[%]?))? ?/i","",$cells[0]);
			}
		}
		return implode('|',$cells);
	}
}

// | title1 | title2 | title3 |
// | cell1  | cell2  | cell3  |
// | cell4  | cell5  | cell6  |
class XpWikiTable extends XpWikiElement {
	var $type;
	var $types;
	var $col; // number of column
	var $table_around,$table_sheet,$table_style,$div_style,$table_align;

	function XpWikiTable(& $xpwiki, $out) {
		parent :: XpWikiElement($xpwiki);

		$cells = explode('|', $out[1]);

		$this->col = count($cells);
		$this->type = strtolower($out[2]);
		$this->types = array ($this->type);
		$is_template = ($this->type === 'c');

		$this->table_style = '';
		$this->table_sheet = '';
		$this->div_style = '';

		if ($this->root->extended_table_format && $is_template) {
			$cells[0] = $this->get_table_style($cells[0]);
		}

		$row = array ();
		foreach ($cells as $cell) {
			$cell = str_replace('&#124;', '|', $cell);
			$row[] = & new XpWikiTableCell($this->xpwiki, $cell, $is_template);
		}
		$this->elements[] = $row;
	}

	function canContain(& $obj) {
		return is_a($obj, 'XpWikiTable') && ($obj->col === $this->col);
	}

	function & insert(& $obj) {
		$this->elements[] = $obj->elements[0];
		$this->types[] = $obj->type;
		return $this;
	}

	function toString() {
		static $parts = array ('h' => 'thead', 'f' => 'tfoot', '' => 'tbody');

		// Set rowspan (from bottom, to top)
		for ($ncol = 0; $ncol < $this->col; $ncol ++) {
			$rowspan = 1;
			foreach (array_reverse(array_keys($this->elements)) as $nrow) {
				$row = & $this->elements[$nrow];
				if ($row[$ncol]->rowspan === 0) {
					++ $rowspan;
					continue;
				}
				$row[$ncol]->rowspan = $rowspan;
				// Inherits row type
				while (-- $rowspan)
					$this->types[$nrow + $rowspan] = $this->types[$nrow];
				$rowspan = 1;
			}
		}

		// Set colspan and style
		$stylerow = NULL;
		foreach (array_keys($this->elements) as $nrow) {
			$row = & $this->elements[$nrow];
			if ($this->types[$nrow] === 'c')
				$stylerow = & $row;
			$colspan = 1;
			$enable_col = NULL;
			foreach (array_keys($row) as $ncol) {
				if (! is_null($enable_col) && $row[$ncol]->colspan === -1) {
					++ $row[$enable_col]->colspan;
					continue;
				}
				if ($row[$ncol]->colspan < 1) {
					++ $colspan;
					continue;
				}
				$enable_col = $ncol;
				$row[$ncol]->colspan = $colspan;
				if ($stylerow !== NULL) {
					$row[$ncol]->setStyle($stylerow[$ncol]->style);
					// Inherits column style
					while (-- $colspan)
						$row[$ncol - $colspan]->setStyle($stylerow[$ncol]->style);
				}
				$colspan = 1;
			}
		}

		// toString
		$string = '';
		foreach ($parts as $type => $part) {
			$part_string = '';
			foreach (array_keys($this->elements) as $nrow) {
				if ($this->types[$nrow] !== $type)
					continue;
				$row = & $this->elements[$nrow];
				$row_string = '';
				foreach (array_keys($row) as $ncol) {
					$row_string .= $row[$ncol]->toString();
					$row[$ncol]->GC();
				}
				$part_string .= $this->wrap($row_string, 'tr');
			}
			$string .= $this->wrap($part_string, $part);
		}
		$string = $this->wrap($string, 'table', ' class="style_table"'."$this->table_style style=\"$this->table_sheet\"");

		return $this->wrap($string, 'div', ' class="ie5" '.$this->div_style).$this->table_around;

	}

	function get_table_style($string) {
//		echo "TABLE: $string <br>\n";
		$colors_reg = "aqua|navy|black|olive|blue|purple|fuchsia|red|gray|silver|green|teal|lime|white|maroon|yellow|transparent";
		//$this->table_around = "<br clear=all /><br />";
		$this->table_around = "<br clear=all />";
		// 回り込み指定
		if (preg_match("/AROUND ?/i",$string)) $this->table_around = "";
		// ボーダー指定
		if (preg_match("/B:([0-9]*),?([0-9]*)\(?(one|s(?:olid)?|da(?:sh(?:ed)?)?|do(?:tt(?:ed)?)?|two|d(?:ouble)?|boko|g(?:roove)?|deko|r(?:idge)?|in?(?:set)?|o(?:ut(?:set)?)?)?\)? ?/i",$string,$reg)) {
			if (isset($reg[3])) {
				switch (strtolower($reg[3])) {
					case 'one':
					case 's':
					case 'solid':
				 		$border_type = "solid";
				 		break;
					case 'two':
					case 'd':
					case 'double':
						$border_type = "double";
				 		break;
					case 'boko':
					case 'g':
					case 'groove':
						$border_type = "groove";
				 		break;
					case 'deko':
					case 'r':
					case 'ridge':
						$border_type = "ridge";
				 		break;
					case 'in':
					case 'i':
					case 'inset':
						$border_type = "inset";
				 		break;
					case 'out':
					case 'o':
					case 'outset':
						$border_type = "outset";
				 		break;
					case 'dash':
					case 'da':
					case 'dashed':
						$border_type = "dashed";
				 		break;
					case 'dott':
					case 'do':
					case 'dotted':
						$border_type = "dotted";
				 		break;
					default:
						$border_type = "outset";
				}
			} else {
				$border_type = "outset";
			}

			//$this->table_style .= " border=\"".$reg[1]."\"";
			if (isset($reg[1])) {
				if ($reg[1]==="0"){
					$this->table_sheet .= "border:none;";
				} else {
					$this->table_sheet .= "border:".$border_type." ".$reg[1]."px;";
				}
			}
			if (isset($reg[2])) {
				if ($reg[2]!=""){
					$this->table_style .= " cellspacing=\"".$reg[2]."\"";
				} else {
					$this->table_style .= " cellspacing=\"1\"";
				}
			}
			$string = preg_replace("/B:([0-9]*),?([0-9]*)\(?(one|s(?:olid)?|da(?:sh(?:ed)?)?|do(?:tt(?:ed)?)?|two|d(?:ouble)?|boko|g(?:roove)?|deko|r(?:idge)?|in?(?:set)?|o(?:ut(?:set)?)?)?\)? ?/i","",$string);
		} else {
			$this->table_style .= " border=\"0\" cellspacing=\"1\"";
			//$this->table_style .= " cellspacing=\"1\"";
			//$this->table_sheet .= "border:none;";
		}
		// ボーダー色指定
		if (preg_match("/BC:(#?[0-9a-f]{6}?|$colors_reg|0) ?/i",$string,$reg)) {
			$this->table_sheet .= "border-color:".$reg[1].";";
			$string = preg_replace("/BC:(#?[0-9abcdef]{6}?|$colors_reg) ?/i","",$string);
		}
		// テーブル背景色指定
		if (preg_match("/TC:(#?[0-9a-f]{6}?|$colors_reg|0) ?/i",$string,$reg)) {
			if ($reg[1]==="0") $reg[1]="transparent";
			$this->table_sheet .= "background-color:".$reg[1].";";
			$string = preg_replace("/TC:(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,(?:no|one(?:ce)?|1))?\) ?)/i","TC:$2",$string);
			$string = preg_replace("/TC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i","",$string);
		}
		// テーブル背景画像指定
		if (preg_match("/TC:\(([^),]*)(,(?:no|one(?:ce)?|1))?\) ?/i",$string,$reg)) {
			if (strpos($reg[1], $this->cont['ROOT_URL']) === 0) {
				$reg[1] = htmlspecialchars($reg[1]);
				$this->table_sheet .= "background-image: url(".$reg[1].");";
				if (!empty($reg[2])) $this->table_sheet .= "background-repeat: no-repeat;";
			}
			$string = preg_replace("/TC:\(([^),]*)(,once|,1)?\) ?/i","",$string);
		}
		// 配置・幅指定
		if (preg_match("/T(LEFT|RIGHT) ?/i",$string,$reg)) {
			$this->table_align = strtolower($reg[1]);
			$this->table_style .= " align=\"".$this->table_align."\"";
			$this->div_style = " style=\"text-align:".$this->table_align."\"";
			if ($this->table_align === "left"){
				$this->table_sheet .= "margin-left:10px;margin-right:auto;";
			} else {
				$this->table_sheet .= "margin-left:auto;margin-right:10px;";
			}
		}
		if (preg_match("/T(CENTER) ?/i",$string,$reg)) {
			$this->table_style .= " align=\"".strtolower($reg[1])."\"";
			$this->div_style = " style=\"text-align:".strtolower($reg[1])."\"";
			$this->table_sheet .= "margin-left:auto;margin-right:auto;";
			$this->table_around = "";
		}
		if (preg_match("/T(LEFT|CENTER|RIGHT)?:([0-9]+(%|px)?) ?/i",$string,$reg)) {
			$this->table_sheet .= "width:".$reg[2].";";
		}
		$string = preg_replace("/^(TLEFT|TCENTER|TRIGHT|T):([0-9]+(%|px)?)? ?/i","",$string);
		return ltrim($string);
	}
}

// , cell1  , cell2  ,  cell3
// , cell4  , cell5  ,  cell6
// , cell7  ,        right,==
// ,left          ,==,  cell8
class XpWikiYTable extends XpWikiElement {
	var $col;	// Number of columns

	// TODO: Seems unable to show literal '==' without tricks.
	//       But it will be imcompatible.
	// TODO: Why toString() or toXHTML() here
	function XpWikiYTable(& $xpwiki, $row = array('cell1 ', ' cell2 ', ' cell3'))
	{
		parent::XpWikiElement($xpwiki);

		$str = array();
		$col = count($row);

		$matches = $_value = $_align = array();
		foreach($row as $cell) {
			if (preg_match('/^(\s+)?(.+?)(\s+)?$/', $cell, $matches)) {
				if ($matches[2] === '==') {
					// Colspan
					$_value[] = FALSE;
					$_align[] = FALSE;
				} else {
					$_value[] = $matches[2];
					if ($matches[1] === '') {
						$_align[] = '';	// left
					} else if (isset($matches[3])) {
						$_align[] = 'center';
					} else {
						$_align[] = 'right';
					}
				}
			} else {
				$_value[] = $cell;
				$_align[] = '';
			}
		}

		for ($i = 0; $i < $col; $i++) {
			if ($_value[$i] === FALSE) continue;
			$colspan = 1;
			while (isset($_value[$i + $colspan]) && $_value[$i + $colspan] === FALSE) ++$colspan;
			$colspan = ($colspan > 1) ? ' colspan="' . $colspan . '"' : '';
			$align = $_align[$i] ? ' style="text-align:' . $_align[$i] . '"' : '';
			$str[] = '<td class="style_td"' . $align . $colspan . '>';
			$str[] = $this->func->make_link($_value[$i]);
			$str[] = '</td>';
			unset($_value[$i], $_align[$i]);
		}

		$this->col        = $col;
		$this->elements[] = implode('', $str);
	}

	function canContain(& $obj) {
		return is_a($obj, 'XpWikiYTable') && ($obj->col === $this->col);
	}

	function & insert(& $obj) {
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function toString() {
		$rows = '';
		foreach ($this->elements as $str) {
			$rows .= "\n".'<tr class="style_tr">'.$str.'</tr>'."\n";
		}
		$rows = $this->wrap($rows, 'table', ' class="style_table" cellspacing="1" border="0"');

		return $this->wrap($rows, 'div', ' class="ie5"');
	}
}

// ' 'Space-beginning sentence
// ' 'Space-beginning sentence
// ' 'Space-beginning sentence
class XpWikiPre extends XpWikiElement {
	function XpWikiPre(& $root, $text) {
		parent :: XpWikiElement($root->xpwiki);
		$this->elements[] = htmlspecialchars((!$this->root->preformat_ltrim || $text === '' || $text {
			0}
		!= ' ') ? $text : substr($text, 1));
	}

	function canContain(& $obj) {
		return is_a($obj, 'XpWikiPre');
	}

	function & insert(& $obj) {
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function toString() {
		return $this->wrap($this->wrap(join("\n", $this->elements), 'pre'), 'div', ' class="pre"');

	}
}

// Block plugin: #something (started with '#')
class XpWikiDiv extends XpWikiElement {
	var $name;
	var $param;
	var $body;

	function XpWikiDiv(& $xpwiki, $out) {
		parent :: XpWikiElement($xpwiki);
		list (, $this->name, $this->param) = array_pad($out, 3, '');
		// Call #plugin
		$this->body = $this->func->do_plugin_convert($this->name, $this->param);
	}

	function canContain(& $obj) {
		return FALSE;
	}

	function toString() {
		return $this->body;
	}
}

// LEFT:/CENTER:/RIGHT:
class XpWikiAlign extends XpWikiElement {
	var $align;

	function XpWikiAlign(& $xpwiki, $align) {
		parent :: XpWikiElement($xpwiki);
		$this->align = $align;
	}

	function canContain(& $obj) {
		return is_a($obj, 'XpWikiInline');
	}

	function toString() {
		return $this->wrap(parent :: toString(), 'div', ' style="text-align:'.$this->align.'"');
	}
}

// Body
class XpWikiBody extends XpWikiElement {
	var $id;
	var $count = 0;
	var $contents;
	var $contents_last;
	var $contents_body;
	var $classes = array (
		'-' => 'XpWikiUList',
		'+' => 'XpWikiOList',
		'>' => 'XpWikiBQuote',
		'<' => 'XpWikiBQuote');
	var $factories = array (
		':' => 'DList',
		'|' => 'Table',
		',' => 'YTable',
		'#' => 'Div');

	function XpWikiBody(& $xpwiki, $id) {
		$this->id = $id;
		$this->contents = & new XpWikiElement($xpwiki);
		$this->contents->last_level = 0;
		$this->contents->count = 0;
		$this->contents_last = & $this->contents;
		parent :: XpWikiElement($xpwiki);
	}

	function parse($lines) {
		$this->last = & $this;
		$matches = array ();
		$ext_title_find = (false || $this->root->render_mode === 'render');
		$last_level = 0;
		$title_len = strlen($this->root->title_setting_string);

		while (!empty ($lines)) {
			$line = rtrim(array_shift($lines), "\r\n");

			// Empty
			if ($line === '') {
				$this->last = & $this;
				$last_level = 0;
				continue;
			}

			// Escape comments
			if (! $this->root->no_slashes_commentout && substr($line, 0, 2) === '//') {
				continue;
			}

			// Extended TITLE:
			if (! $ext_title_find && $title_len) {
				if (substr($line, 0, $title_len) === $this->root->title_setting_string) {
					$ext_title_find = TRUE;
					continue;
				}
			}

			// The first character
			$head = $line[0];

			// LEFT, CENTER, RIGHT
			if ($head === 'R' || $head === 'C' || $head === 'L') {
				if (preg_match('/^(LEFT|CENTER|RIGHT):(.*)$/', $line, $matches)) {
					// <div style="text-align:...">
					$this->last = & $this->last->add(new XpWikiAlign($this->xpwiki, strtolower($matches[1])));
					if ($matches[2] === '') {
						continue;
					}
					$line = $matches[2];
					$head = $line[0];
				}
			}

			switch ($head) {

			// Horizontal Rule
			case '-':
				if (preg_match('/^\-{4,}$/', $line)) {
					$this->insert(new XpWikiHRule($this, $line));
					$last_level = 0;
					continue 2;
				}
				break;

			// Multiline-enabled block plugin
			case '#':
				if (!$this->cont['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK'] && preg_match('/^#[^{]+(\{\{+)\s*$/', $line, $matches)) {
					$len = strlen($matches[1]);
					$line .= "\r"; // Delimiter
					while (!empty ($lines)) {
						$next_line = rtrim(array_shift($lines), "\r\n");
						if (preg_match('/^\}{'.$len.'}/', $next_line)) {
							$line .= $next_line;
							break;
						} else {
							$line .= $next_line .= "\r"; // Delimiter
						}
					}
				}
				break;

			// Heading
			case '*':
				$this->insert(new XpWikiHeading($this, $line));
				$last_level = 0;
				continue 2;
				break;

			// Pre
			case ' ':
			case "\t":
				$this->last = & $this->last->add(new XpWikiPre($this, $line));
				continue 2;
				break;

			// <, <<, <<< only to escape blockquote.
			case '<':
				if (! preg_match('/^<{1,3}\s*$/', $line)) {
					$head = '';
				}
				break;

			}

			// Line Break
			if (substr($line, -1) === '~')
				$line = substr($line, 0, -1)."\r";

			// Other Character
			if (isset ($this->classes[$head])) {
				$classname = $this->classes[$head];

				$this_level = strspn($line, $head);
				if ($this_level - $last_level > 1) {
					for($_lev = $last_level+1; $_lev < $this_level; $_lev++ ) {
						$this->last = & $this->last->add(new $classname ($this, str_repeat($head, $_lev)."\x08"));
					}
				}
				$last_level = $this_level;

				$this->last = & $this->last->add(new $classname ($this, $line));
				continue;
			}

			// Other Character
			if (isset ($this->factories[$head])) {
				$this->root->rtf['contntId'] = $this->id;

				if ($head === ':') {
					$this_level = strspn($line, $head);
					if ($this_level - $last_level > 1) {
						for($_lev = $last_level+1; $_lev < $this_level; $_lev++ ) {
							$this->last = & $this->last->add($this->func->Factory_DList(':|'));
						}
					}
					$last_level = $this_level;
				}

				$factoryname = 'Factory_'.$this->factories[$head];
				$this->last = & $this->last->add($this->func->$factoryname($line));
				continue;
			}

			// Default
			$this->last = & $this->last->add($this->func->Factory_Inline($line));
		}
	}

	function getAnchor($text, $level) {
		// Heading id (auto-generated)
		$autoid = 'content_'.$this->id.'_'.$this->count;
		$this->count++;

		// Heading id (specified by users)
		$id = $this->func->make_heading($text, FALSE); // Cut fixed-anchor from $text
		if ($id === '') {
			// Not specified
			$id = & $autoid;
			$anchor = '';
		} else {
			if ($this->root->_symbol_anchor) {
				$anchor = ' &aname(' . $id . ',noid,super,full){'. $this->root->_symbol_anchor . '};';
			} else {
				$anchor = '';
			}
			if ($this->root->fixed_heading_anchor_edit && empty($this->root->rtf['convert_html_multiline'])) $anchor .= " &edit(#$id,paraedit);";
		}

		if (trim($text)) {
			$text = ' '.$text;

			// Add 'page contents' link to its heading
			if ($level - $this->contents->last_level > 1) {
				for($_lev = $this->contents->last_level+1; $_lev < $level; $_lev++ ) {
					$this->contents_last = & $this->contents_last->add(new XpWikiContents_UList($this->xpwiki, '', $_lev, NULL));
				}
			}
			$this->contents->last_level = $level;
			$this->contents->count++;

			$this->contents_last = & $this->contents_last->add(new XpWikiContents_UList($this->xpwiki, $text, $level, $id));
		}
		// Add heding
		return array ($text, $anchor, ($this->count > 1 ? $this->root->top : ''), $autoid, $id);
	}

	function & insert(& $obj) {
		if (is_a($obj, 'XpWikiInline'))
			$obj = & $obj->toPara();
		return parent :: insert($obj);
	}

	function toString() {
		$text = parent :: toString();

		// Close area div
		$text .= $this->func->get_areadiv_closer();

		// #contents
		if ($this->root->contents_auto_insertion && empty($this->root->rtf['contents_converted'][$this->id]) && $this->contents->count >= $this->root->contents_auto_insertion) {
			$text = preg_replace('/<h\d/', '<#_contents_>'."\n".'$0', $text, 1);
		}

		if (strpos($text, '<#_contents_>') !== FALSE) {
			$this->contents_body = $this->contents->toString();
			$text = preg_replace_callback('/<#_contents_>/', array (& $this, 'replace_contents'), $text);
		}
		$this->contents_body = NULL;
		$this->contents_last = NULL;
		$this->contents = NULL;

		return $text."\n";
	}

	function replace_contents($arr) {
		$ret =  <<<EOD
<div class="contents">
 <div class="toc_header">
  {$this->root->contents_title}
 </div>
 <div class="toc_body">
  {$this->contents_body}
 </div>
</div>
EOD;
		return $this->func->wrap_description_ignore($ret);
	}
}

class XpWikiContents_UList extends XpWikiListContainer {
	function XpWikiContents_UList(& $xpwiki, $text, $level, $id) {
		parent :: XpWikiListContainer($xpwiki, 'ul', 'li', '-', str_repeat('-', $level).(is_null($id)? "\x08" : ''));
		if (!is_null($id)) {
			// Reformatting $text
			// A line started with "\n" means "preformatted" ... X(
			$this->func->make_heading($text);
			if (!trim($text)) {
				$text .= '_';
			}
			$text = "\n".'<a href="#'.$id.'">'.$text.'</a>'."\n";
			//parent::XpWikiListContainer('ul', 'li', '-', str_repeat('-', $level));
			$this->insert($this->func->Factory_Inline($text));
		}
	}

	function setParent(& $parent) {
		parent :: setParent($parent);
		$step = $this->level;
		$margin = $this->left_margin;
		if (isset ($parent->parent) && is_a($parent->parent, 'XpWikiListContainer')) {
			$step -= $parent->parent->level;
			$margin = 0;
		}
		$margin += $this->margin * ($step === $this->level ? 1 : $step);
		$this->style = sprintf($this->root->_list_pad_str, $this->level, $margin, $margin);
	}
}
?>