<?php
class xpwiki_plugin_xoopsblock extends xpwiki_plugin {

	function plugin_xoopsblock_init() {
	// $Id: xoopsblock.inc.php,v 1.9 2010/06/04 07:20:28 nao-pon Exp $

	/*
	 * countdown.inc.php
	 * License: GPL
	 * Author: nao-pon http://hypweb.net
	 * XOOPS Module Block Plugin
	 *
	 * XOOPSのブロックを表示するプラグイン
	 */
		if ($this->root->module['platform'] === "xoops") {
			include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");
			include_once(XOOPS_ROOT_PATH."/class/xoopsblock.php");
		}
	}

	function plugin_xoopsblock_convert() {

		if ($this->root->module['platform'] !== "xoops") { return ''; }

		static $css_show = FALSE;

		list($tgt,$option1,$option2) = array_pad(func_get_args(),3,"");

		$tgt_bids = array();

		if (!$tgt || $tgt === "?") {
			$tgt = "?";
		} else {
			foreach(explode(",", $tgt) as $_bid) {
				if (preg_match("/^\d+$/",$_bid) && $_bid > 0) {
					$tgt_bids[] = $_bid;
				}
			}
		}

		$align = "left";
		$around = false;
		$width = "";
		$arg = array();
		if (preg_match("/^(left|center|right)$/i",$option2,$arg))
			$align = $arg[1];
		if (preg_match("/^(left|center|right)$/i",$option1,$arg))
			$align = $arg[1];
		if (preg_match("/^(around|float|width)(:?w?([\d]+%?)(?:px)?)?$/i",$option2,$arg))
		{
			if ($arg[1]) $around = true;
			$width = (!strstr($arg[3],"%"))? $arg[3]."px" : $arg[3];
			$width = "width:".$width.";";
		}
		if (preg_match("/^(around|float|width)(:?w?([\d]+%?)(?:px)?)?$/i",$option1,$arg))
		{
			if ($arg[1]) $around = true;
			$width = (!strstr($arg[3],"%"))? $arg[3]."px" : $arg[3];
			$width = "width:".$width.";";
		}
		if ($align === 'center') {
			if (! $width) $width = 'width:auto;';
			$style = ' style="margin-left:auto;margin-right:auto;'.$width.'"';
			$around = false;
		} else {
			$style = ' style="float:'.$align.';'.$width.'"';
		}
		$clear = ($around)? '' : '<div style="clear:both;"></div>';

		global $xoopsUser;
		$xoopsblock = new XoopsBlock();
		$xoopsgroup = new XoopsGroup();
		$arr = array();
		$side = null;

		if ($this->root->userinfo['admin']) {
			$arr = $xoopsblock->getAllBlocks();
		} else if ( $xoopsUser ) {
			$arr = $xoopsblock->getAllBlocksByGroup($xoopsUser->groups());
		} else {
			$arr = $xoopsblock->getAllBlocksByGroup($this->plugin_xoopsblock_getByType("Anonymous"));
		}

		$ret = "";

		if ($tgt == "?"){
			foreach ( $arr as $myblock ) {
				$block = array();
				$block_type = (@$myblock->getVar("type"))? $myblock->getVar("type") : $myblock->getVar("block_type");
				$name = (@$myblock->getVar("title")) ? $myblock->getVar("title") : $myblock->getVar("name");
				$bid = $myblock->getVar('bid');
				$ret .= "<li>(".$bid.")".$name."</li>";
			}
		} else {
			global $xoopsTpl;

			require_once XOOPS_ROOT_PATH.'/class/template.php';
			$xoopsTpl = new XoopsTpl();

			if (is_object($xoopsUser)) {
				$xoopsTpl->assign(array('xoops_isuser' => true, 'xoops_userid' => $xoopsUser->getVar('uid'), 'xoops_uname' => $xoopsUser->getVar('uname'), 'xoops_isadmin' => $xoopsUser->isAdmin()));
			}
			$xoopsTpl->assign('xoops_requesturi', htmlspecialchars($GLOBALS['xoopsRequestUri'], ENT_QUOTES));

			foreach ($tgt_bids as $bid) {
				$myblock = new XoopsBlock($bid);
				$_bid = $myblock->getVar('bid');
				if (! empty($_bid)) {
					$bcachetime = $myblock->getVar('bcachetime');
					// Only a guest enable cache. by nao-pon
					//if (empty($bcachetime)) {
					if ($bcachetime % 10 == 1)
					{
						$bcachetime_guest = TRUE;
						$bcachetime = $bcachetime - 1;
					}
					else
					{
						$bcachetime_guest = FALSE;
					}
					if (empty($bcachetime) || (is_object($xoopsUser) && $bcachetime_guest)) {
					//if (empty($bcachetime)) {
						$xoopsTpl->xoops_setCaching(0);
					} else {
						$xoopsTpl->xoops_setCaching(2);
						$xoopsTpl->xoops_setCacheTime($bcachetime);
					}
					$btpl = $myblock->getVar('template');
					if ($btpl != '') {
						if (empty($bcachetime) || !$xoopsTpl->is_cached('db:'.$btpl, 'blk_'.$myblock->getVar('bid'))) {
							//$xoopsLogger->addBlock($myblock->getVar('name'));
							$bresult = $myblock->buildBlock();
							if (!$bresult) {
								continue;
							}
							$xoopsTpl->assign_by_ref('block', $bresult);
							$bcontent = $xoopsTpl->fetch('db:'.$btpl, 'blk_'.$myblock->getVar('bid'));
							$xoopsTpl->clear_assign('block');
						} else {
						   //$xoopsLogger->addBlock($myblock->getVar('name'), true, $bcachetime);
							$bcontent = $xoopsTpl->fetch('db:'.$btpl, 'blk_'.$myblock->getVar('bid'));
						}
					} else {
						//$bid = $myblock->getVar('bid');
						if (empty($bcachetime) || !$xoopsTpl->is_cached('db:system_dummy.html', 'blk_'.$bid)) {
							//$xoopsLogger->addBlock($myblock->getVar('name'));
							$bresult = $myblock->buildBlock();
							if (!$bresult) {
								continue;
							}
							$xoopsTpl->assign_by_ref('dummy_content', $bresult['content']);
							$bcontent = $xoopsTpl->fetch('db:system_dummy.html', 'blk_'.$bid);
							$xoopsTpl->clear_assign('block');
						} else {
							//$xoopsLogger->addBlock($myblock->getVar('name'), true, $bcachetime);
							$bcontent = $xoopsTpl->fetch('db:system_dummy.html', 'blk_'.$bid);
						}
					}
					$btitle = $myblock->getVar('title');
				} else {
					$btitle = "Block($bid)";
					$bcontent = "Block($bid) is not found.";
				}

				if ($bcontent) {
					$ret .= "<h5>".$btitle."</h5>\n";
					$ret .= $bcontent;
					foreach(explode("\n", $xoopsTpl->get_template_vars('xoops_block_header')) as $str) {
						$this->root->head_tags[] = rtrim($str);
					}
					foreach(explode("\n", $xoopsTpl->get_template_vars('xoops_module_header')) as $str) {
						$this->root->head_tags[] = rtrim($str);
					}
					$this->root->head_tags = array_unique($this->root->head_tags);
				}
			}
			unset($myblock);
		}

		if (!$css_show) {
			$css_show = true;
			$this->root->head_pre_tags[] = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"". XOOPS_URL ."/xoops.css\" />";
		}

		if ($tgt == "?") $ret = "<ul>$ret</ul>";
		unset($xoopsblock,$xoopsgroup);
		return "<div{$style}>{$ret}</div>{$clear}";
	}

	function plugin_xoopsblock_getByType($type=""){
		// For XOOPS 2
		global $xoopsDB;
		$ret = array();
		$where_query = "";
		if ( !empty($type) ) {
			$where_query = " WHERE group_type='".$type."'";
		}
		$sql = "SELECT groupid FROM ".$xoopsDB->prefix("groups")."".$where_query;
		$result = $xoopsDB->query($sql);
		while ( $myrow = $xoopsDB->fetchArray($result) ) {
			$ret[] = $myrow['groupid'];
		}
		return $ret;
	}
}
?>