<?php
/*
 * Created on 2008/12/07 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: footnotes.inc.php,v 1.3 2009/01/04 11:43:46 nao-pon Exp $
 */

class xpwiki_plugin_footnotes extends xpwiki_plugin {
	
	function plugin_footnotes_init() {
		//$this->config['category_title'] = ''; // Use each mark.
		//$this->config['category_title'] = '&lt; $1 &gt;'; // ex. < TITLE >
		$this->config['category_title'] = '[ $1 ]'; // ex. [ TITLE ]
	}
	
	function plugin_footnotes_convert() {
		$options = array(
			'category'=> FALSE,
			'force'   => FALSE,
			'nobr'    => FALSE,
			'noclear' => FALSE,
			'nohr'    => FALSE,
		);
		$notes = '';
		$hr = '';
		$categoris = array();
		$args = func_get_args();
		$this->fetch_options($options, $args);
		if (isset($options['_args'])) {
			foreach($options['_args'] as $cat) {
				if (preg_match('/^(.+?):([\w!#$%\'()=-^~|`@{}\[\]+;*:,.?\/ ]{1,2}):$/', $cat, $match)) {
					$idType = $match[2];
					if (strlen($idType) === 1) {
						$idType .= '$1';
					} else {
						if ($idType[0] === '1') {
							$idType = '$1' . $idType[1];
						} else {
							$idType = $idType[0] . '$1' . $idType[1];
						}
					}
					$this->root->footnote_categories[$match[1]] = $idType;
				} else {
					$categoris[] = $cat;
				}
			}
		}
		if ($this->root->foot_explain || ! empty($this->root->foot_explain_disabled)) {
			if (! isset($this->root->foot_explain_disabled)) {
				$this->root->foot_explain_disabled = array();
			}
			$notes = $this->root->foot_explain;
			if ($options['force'] && ! empty($this->root->foot_explain_disabled)) {
				$disabled = $this->root->foot_explain_disabled;
				foreach(array_keys($disabled) as $key) {
					$reg = '/(<a[^>]+?)(?:id|name)=".*?"([^>]*?>)/';
					while(preg_match($reg, $disabled[$key])) {
						$disabled[$key] = preg_replace($reg, '$1$2', $disabled[$key]);
					}
				}
				$notes = array_merge($notes, $disabled);
			}
			if ($categoris) {
				natsort($this->root->foot_explain);
				$catName = array();
				$catSets = array();
				$catClass = 'footnote_category';
				if ($options['nobr']) {
					$catClass .= ' footnote_category_nobr';
				}
				foreach($notes as $key => $val) {
					if (preg_match('/^<!--(.+?)-->/', $val, $match)){
						$category = $match[1];
						if (in_array($category, $categoris)) {
							if ($options['category'] && !isset($catName[$category])) {
								$idType = isset($this->root->footnote_categories[$category])? $this->root->footnote_categories[$category] : '*$1';
								$catTitle = ($this->config['category_title'])? $this->config['category_title'] : $idType;
								$catName[$category] = '<div class="' . $catClass . '">' . str_replace('$1', htmlspecialchars($category), $catTitle) . '</div>';
							}
							$catSets[$category][$key] = $val;
							if (!$options['noclear']) {
								$this->root->foot_explain_disabled[$key] = $this->root->foot_explain[$key];
								unset($this->root->foot_explain[$key]);
							}
						}
					}
				}
				$notes = array();
				foreach($categoris as $category) {
					if (! empty($catSets[$category])) {
						if (isset($catName[$category])){
							$notes[] = $catName[$category];
						}
						natsort($catSets[$category]);
						$notes += $catSets[$category];
					}
				}
			} else {
				natsort($notes);
				if (!$options['noclear']) {
					$this->root->foot_explain_disabled = array_merge($this->root->foot_explain_disabled, $this->root->foot_explain);
					$this->root->foot_explain = array();
				}
			}
			if ($notes) {
				$notes = join("\n", $notes);
				if (!$options['nohr']) {
					$hr = $this->root->note_hr;
				}
				if ($options['nobr']) {
					$notes = str_replace('<br />', '&nbsp; ', $notes);
				}
			}
		}
		return $notes? '<div class="footnotes">' . $hr . $notes . '</div>' : '';
	}
}
