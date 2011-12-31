<?php
//
// Created on 2006/11/27 by nao-pon http://hypweb.net/
// $Id: xoopsSearch.php,v 1.10 2011/12/31 16:10:53 nao-pon Exp $
//
class XpWikiExtension_xoopsSearch extends XpWikiExtension {

// $this->xpwiki : Parent XpWiki object.
// $this->root   : Global variable.
// $this->cont   : Constant.
// $this->func   : XpWiki functions.

	function get ($keywords , $andor , $limit , $offset , $userid) {

		// for XOOPS Search module
		$showcontext = empty( $_GET['showcontext'] ) ? 0 : 1 ;

		$where_readable = $this->func->get_readable_where('p.');
		$where = "p.editedtime != 0";
		if ($where_readable) {
			$where = "$where AND ($where_readable)";
		}

		$sql = "SELECT p.pgid,p.name,p.editedtime,p.title,p.uid FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." p INNER JOIN ".$this->xpwiki->db->prefix($this->root->mydirname."_plain")." t ON t.pgid=p.pgid WHERE ($where) ";
		if ( $userid != 0 ) {
			$sql .= "AND (p.uid=".$userid.") ";
		}

		if ( is_array($keywords) && $keywords ) {
			$keywords = array_map('stripslashes', $keywords);
			// 英数字は半角,カタカナは全角,ひらがなはカタカナに
			$sql .= "AND (";
			$i = 0;
			foreach ($keywords as $keyword) {
				if ($i++ !== 0) $sql .= " $andor ";
				if ($this->cont['LANG'] === 'ja' && function_exists("mb_convert_kana"))
				{
					// 英数字は半角,カタカナは全角,ひらがなはカタカナに
					$word = addslashes(mb_convert_kana($keyword,'aKCV'));
				} else {
					$word = addslashes($keyword);
				}
				$sql .= "(p.name_ci LIKE '%{$word}%' OR t.plain LIKE '%{$word}%')";
			}
			$sql .= ") ";
		}
		$sql .= "ORDER BY p.editedtime DESC";

		//exit($sql);
		$result = $this->xpwiki->db->query($sql,$limit,$offset);

		$ret = array();

		if (!$keywords) $keywords = array();
		$sword = rawurlencode(join(' ',$keywords));

		$context = '' ;
		$make_context_func = function_exists( 'xoops_make_context' )? 'xoops_make_context' : (function_exists( 'search_make_context' )? 'search_make_context' : '');

		while($myrow = $this->xpwiki->db->fetchArray($result)) {
			// get context for module "search"
			if( $make_context_func && $showcontext ) {

				$pobj = & XpWiki::getSingleton($this->root->mydirname);
				$pobj->init($myrow['name']);
				$GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'] = null;
				$pobj->root->rtf['use_cache_always'] = TRUE;
				$pobj->execute();
				$text = $pobj->body;
				$text = preg_replace('/<!--description ignore-->.+?<!--\/description ignore-->|<(script|style).+?<\/\\1>/is', '', $text);

				// 付箋
				if (empty($GLOBALS['Xpwiki_'.$this->root->mydirname]['cache']['fusen']['loaded'])){
					if ($fusen = $this->func->get_plugin_instance('fusen')) {
						if ($fusen_data = $fusen->plugin_fusen_data($myrow['name'])) {
							if ($fusen_tag = $fusen->plugin_fusen_gethtml($fusen_data, '')) {
								$text .= '<fieldset><legend> fusen.dat </legend>' . $fusen_tag . '</fieldset>';
							}
						}
					}
				}

				$full_context = strip_tags( $text ) ;
				if( function_exists( 'easiestml' ) ) $full_context = easiestml( $full_context ) ;
				$context = $make_context_func( $full_context , $keywords ) ;
			}

			$title = ($myrow['title'])? ' ['.$myrow['title'].']' : '';
			$link = $this->func->get_page_uri($myrow['name']);
			$ret[] = array(
				'link'    => $link . ((strpos($link, '?') === false)? '?' : '&amp;') . 'word=' . $sword,
				'title'   => htmlspecialchars($myrow['name'].$title, ENT_QUOTES),
				'image'   => '',
				'time'    => $myrow['editedtime'] + $this->cont['LOCALZONE'],
				'uid'     => $myrow['uid'],
				'page'    => $myrow['name'],
				'context' => $context );
		}
		// for xoops search module
		$GLOBALS['md_search_flg_zenhan_support'] = true;
		return $ret;
	}
}

?>