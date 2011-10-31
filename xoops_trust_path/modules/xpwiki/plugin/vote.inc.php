<?php
class xpwiki_plugin_vote extends xpwiki_plugin {
	// $Id: vote.inc.php,v 1.8 2008/11/18 04:10:40 nao-pon Exp $
	
	function plugin_vote_init() {
		$this->load_language();
	}
	
	function plugin_vote_action() {
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');
		
		if (preg_match("/^(#add|#(k)?sort|#notimestamp)(\[\d+\])?$/i",$this->root->post['vote_newitem'])) {
			$retvars["msg"] = $this->msg['deny'];
			return $retvars;
		}
		
		$postdata_old = $this->func->get_source($this->root->post["refer"]);
		$vote_no = 0;
		$notimestamp = FALSE;
		$nomail = FALSE;
		
		$this->func->escape_multiline_pre($postdata_old, TRUE);
		
		$postdata = '';
		foreach($postdata_old as $lines) {
			if ($lines{0} == "|") {
				$_line = explode("|",$lines);
			} else {
				$_line = array($lines);
			}
			$cell_line = array();
			$celldata = array();
			foreach($_line as $line) {
				$line = rtrim($line);
				$arg = array();
				if(preg_match("/^(.*)?#vote\((.*)\)(.*)$/i",$line,$arg)) {
					$cellhead = $celltag = $arg[1];
					if ($celltag) $celltag = $this->func->cell_format_tag_del($celltag);
					
					if(($vote_no == $this->root->post["vote_no"]) && !$celltag) {
						$args = $this->func->csv_explode(',', $arg[2]);
						$lefts = empty($arg[3]) ? '' : $arg[3];
						$lastvote = "";
						$_add = FALSE;
						foreach($args as $item) {
							if(preg_match("/^#lastvote:(.+)$/",$item,$arg)) {
								$lastvote = $arg[1];
								continue;
							}
							$match = array();
							if(preg_match("/^(.+)\[(\d+)\]$/",$item,$match)) {
								$item = $match[1];
								$cnt = $match[2];
								$is_cmd = 0;
							} else {
								if (strtolower($item) == "#notimestamp") {
									$notimestamp = TRUE;
									$is_cmd = 1;
								} else if (strtolower($item) == "#nomail") {
									$nomail = TRUE;
									$is_cmd = 1;
								} else if (strtolower($item) == "#sort" || strtolower($item) == "#ksort") {
									$is_cmd = 1;
								} else {
									$is_cmd = 0;
								}
								$cnt = 0;
							}
							
							if (!$is_cmd) {
								$e_arg = $this->func->encode($item);
								if ($item == $this->root->post['vote_newitem']) {
									$this->root->post['vote_newitem'] = "";
									$this->root->post["vote_$e_arg"] = $this->msg['votes'];
								}
								if (strtolower($item) == "#add" && $this->root->post['vote_newitem'] && strtolower($this->root->post['vote_newitem']) != "#add") {
									$item = $this->root->post['vote_newitem'];
									$cnt = 1;
									$notimestamp = $nomail = FALSE;
									$_add = TRUE;
									$thisvote = md5($item.$_SERVER["REMOTE_ADDR"]);
								} else if(isset($this->root->post["vote_$e_arg"]) && $this->root->post["vote_$e_arg"]==$this->msg['votes']) {
									$thisvote = md5($item.$_SERVER["REMOTE_ADDR"]);
									if ($thisvote == $lastvote) {
										$retvars["msg"] = $this->msg['bad'];
										return $retvars;
									}
									$cnt++;
								}
								if ($cnt) $item .= '['.$cnt.']';
							}
							if (strpos($item,",") !== FALSE) {
								$item = '"'.$item.'"';
							}
							$votes[] = $item;
							if ($_add) {
								$votes[] = "#add";
								$_add = FALSE;
							}
						}
						if (empty($thisvote)) {
							$retvars["msg"] = $this->msg['deny'];
							return $retvars;
						}
						$vote_str = "$cellhead#vote(" . "#lastvote:" . $thisvote .",". @join(",",$votes) . ")" . $lefts;

						$postdata_input = $vote_str;
						$celldata[] = $vote_str;
					} else {
						$celldata[] = $line;
					}
					if (!$celltag) $vote_no++;
				} else {
					$celldata[] = $line;
				}
			}
			$postdata .= join("|",$celldata)."\n";
		}

		$this->func->escape_multiline_pre($postdata, FALSE);

		if($this->func->get_digests($this->func->get_source($this->root->vars['refer'], TRUE, TRUE)) !== $this->root->post["digest"]) {
			$retvars["msg"] = $this->msg['collided'];
			return $retvars;
		} else {
			$this->func->page_write($this->root->post["refer"],$postdata,$notimestamp);
	
			$title = $this->root->_title_updated;
			$body = '';
		}
	
		$retvars["msg"] = $title;
		$retvars["body"] = $body;
	
		$this->root->post["page"] = $this->root->post["refer"];
		$this->root->vars["page"] = $this->root->post["refer"];
	
		return $retvars;
	}
	function plugin_vote_convert() {

		static $vote_no = array();
		if (!isset($vote_no[$this->xpwiki->pid])) {$vote_no[$this->xpwiki->pid] = 0;}
	
		$args = func_get_args();
		if(!func_num_args()) return FALSE;
	
		$tdcnt = 0;
		$lines = $s_items = $items = $cnts = array();
		$line = $sort = $ksort = $add = 0;
		
		foreach($args as $arg) {
			if (substr($arg,0,10) == "#lastvote:") continue;
			if (strtolower($arg) == "#nomail") continue;
			if (strtolower($arg) == "#sort") {
				$sort = 1;
			} else if (strtolower($arg) == "#ksort") {
				$ksort = 1;
			} else if (strtolower($arg) != "#notimestamp") {
				$match = array();
				if(preg_match("/^(.+)\[(\d+)\]$/",$arg,$match)) {
					$arg = $match[1];
					$cnt = $match[2];
				} else {
					$cnt = 0;
				}
				if (strtolower($arg) == "#add") {
					$addcnt = $cnt;
					$add = 1;
				} else {
					$lines[] = $line;
					$items[] = $arg;
					$links[] = $_item = $this->func->make_link($arg);
					$s_items[] = strip_tags($_item);
					$cnts[] = $cnt;
					$line ++;
				}
			}
			if ($sort && $ksort) {
				array_multisort (	$cnts,SORT_NUMERIC, SORT_DESC,
												$s_items,SORT_REGULAR, SORT_ASC,
												$lines,SORT_NUMERIC, SORT_ASC,
												$items,$links);
			} else if ($sort) {
				array_multisort (	$cnts,SORT_NUMERIC, SORT_DESC,
												$lines,SORT_NUMERIC, SORT_ASC,
												$s_items,SORT_REGULAR, SORT_ASC,
												$items,$links);
			} else if ($ksort) {
				array_multisort (	$s_items,SORT_REGULAR, SORT_ASC,
												$lines,SORT_NUMERIC, SORT_ASC,
												$cnts,SORT_NUMERIC, SORT_DESC,
												$items,$links);
			}
		}
	
		$count_label = ($sort)? "<td align=\"left\" class=\"vote_label\" style=\"padding-left:1em;padding-right:1em\"><strong>{$this->msg['rank']}</strong>" : "";
		$script = $this->func->get_script_uri();
		$string = ""
		. "<form action=\"{$script}\" method=\"post\">\n"
		. "<table cellspacing=\"0\" cellpadding=\"2\" class=\"style_table\">\n"
		. "<tr>\n"
		. $count_label
		. "<td align=\"left\" class=\"vote_label\" style=\"padding-left:1em;padding-right:1em\"><strong>{$this->msg['choice']}</strong>"
		. "<input type=\"hidden\" name=\"plugin\" value=\"vote\" />\n"
		. "<input type=\"hidden\" name=\"refer\" value=\"".htmlspecialchars($this->root->vars["page"])."\" />\n"
		. "<input type=\"hidden\" name=\"vote_no\" value=\"".htmlspecialchars($vote_no[$this->xpwiki->pid])."\" />\n"
		. "<input type=\"hidden\" name=\"digest\" value=\"".htmlspecialchars($this->root->digest)."\" />\n"
		. "</td>\n"
		. "<td align=\"center\" class=\"vote_label\"><strong>{$this->msg['votes']}</strong></td>\n"
		. "</tr>\n";
	
		$line = 0;
		$cnt_tag = "";
		$bef_point = 0;
		$readonly = ($this->cont['PKWK_READONLY'])? ' disabled="disabled"' : '';
		foreach($items as $arg) {
			$cnt = $cnts[$line];
			$link = $links[$line];
			$e_arg = $this->func->encode($arg);
	
			if($tdcnt++ % 2) $cls = "vote_td1";
			else             $cls = "vote_td2";
			
			$cnt_point = ($cnt != $bef_point)? $tdcnt:"&middot;";
			$bef_point = $cnt;
			if ($sort) $cnt_tag = "<td align=\"center\" class=\"$cls\" nowrap=\"nowrap\">$cnt_point</td>";
			
			$string .= "<tr>".$cnt_tag
				.  "<td align=\"left\" class=\"$cls\" style=\"padding-left:1em;padding-right:1em;\">$link</td>"
			.  "<td align=\"right\" class=\"$cls\" nowrap=\"nowrap\">$cnt&nbsp;&nbsp;<input type=\"submit\" name=\"vote_".htmlspecialchars($e_arg)."\" value=\"{$this->msg['votes']}\" class=\"submit\"{$readonly} /></td>"
			.  "</tr>\n";
			$line ++;
		}
		if ($add){
			if($tdcnt++ % 2) $cls = "vote_td1";
			else             $cls = "vote_td2";
			
			if ($sort) $cnt_tag = "<td align=\"center\" class=\"$cls\" nowrap=\"nowrap\">New!</td>";
			
			if (!$addcnt) $addcnt = 30;
			
			$string .= "<tr>".$cnt_tag
				.  "<td align=\"left\" class=\"$cls\" style=\"padding-left:1em;padding-right:1em;\"><input type=\"text\" name=\"vote_newitem\" size=\"$addcnt\"/></td>"
			.  "<td align=\"right\" class=\"$cls\" nowrap=\"nowrap\">0&nbsp;&nbsp;<input type=\"submit\" name=\"vote_\" value=\"{$this->msg['votes']}\" class=\"submit\"{$readonly} /></td>"
			.  "</tr>\n";
		}
	
		$string .= "</table></form>\n";
	
		$vote_no[$this->xpwiki->pid]++;
	
		return $string;
	}
}
?>