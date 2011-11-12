<?php
//----- Start diff.php -----//
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: diff.php,v 1.2 2006/10/24 23:55:49 nao-pon Exp $
// Copyright (C)
//   2003-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//

// Show more information when it conflicts
// define('PKWK_DIFF_SHOW_CONFLICT_DETAIL', 1);


// References of this class:
// S. Wu, <A HREF="http://www.cs.arizona.edu/people/gene/vita.html">
// E. Myers,</A> U. Manber, and W. Miller,
// <A HREF="http://www.cs.arizona.edu/people/gene/PAPERS/np_diff.ps">
// "An O(NP) Sequence Comparison Algorithm,"</A>
// Information Processing Letters 35, 6 (1990), 317-323.
class XpWikiline_diff
{
	var $arr1, $arr2, $m, $n, $pos, $key, $plus, $minus, $equal, $reverse;

	function XpWikiline_diff($plus = '+', $minus = '-', $equal = ' ')
	{
		$this->plus  = $plus;
		$this->minus = $minus;
		$this->equal = $equal;
	}

	function arr_compare($key, $arr1, $arr2)
	{
		$this->key  = $key;
		$this->arr1 = $arr1;
		$this->arr2 = $arr2;
		$this->compare();
		$arr = $this->toArray();
		return $arr;
	}

	function set_str($key, $str1, $str2)
	{
		$this->key  = $key;
		$this->arr1 = array();
		$this->arr2 = array();
		$str1 = str_replace("\r", '', $str1);
		$str2 = str_replace("\r", '', $str2);
		foreach (explode("\n", $str1) as $line) {
			$this->arr1[] = new XpWikiDiffLine($line);
		}
		foreach (explode("\n", $str2) as $line) {
			$this->arr2[] = new XpWikiDiffLine($line);
		}
	}

	function str_compare($str1, $str2)
	{
		$this->set_str('diff', $str1, $str2);
		$this->compare();

		$str = '';
		foreach ($this->toArray() as $obj) {
			$str .= $obj->get('diff') . $obj->text();
		}
		return $str;
	}

	function compare()
	{
		$this->m = count($this->arr1);
		$this->n = count($this->arr2);

		if ($this->m == 0 || $this->n == 0) { // No need to compare
			$this->result = array(array('x'=>0, 'y'=>0));
			return;
		}

		// Sentinel
		array_unshift($this->arr1, new XpWikiDiffLine(''));
		$this->m++;
		array_unshift($this->arr2, new XpWikiDiffLine(''));
		$this->n++;

		$this->reverse = ($this->n < $this->m);
		if ($this->reverse) {
			// Swap
			$tmp = $this->m; $this->m = $this->n; $this->n = $tmp;
			$tmp = $this->arr1; $this->arr1 = $this->arr2; $this->arr2 = $tmp;
			unset($tmp);
		}

		$delta = $this->n - $this->m; // Must be >=0;

		$fp = array();
		$this->path = array();

		for ($p = -($this->m + 1); $p <= ($this->n + 1); $p++) {
			$fp[$p] = -1;
			$this->path[$p] = array();
		}

		for ($p = 0;; $p++) {
			for ($k = -$p; $k <= $delta - 1; $k++) {
				$fp[$k] = $this->snake($k, $fp[$k - 1], $fp[$k + 1]);
			}
			for ($k = $delta + $p; $k >= $delta + 1; $k--) {
				$fp[$k] = $this->snake($k, $fp[$k - 1], $fp[$k + 1]);
			}
			$fp[$delta] = $this->snake($delta, $fp[$delta - 1], $fp[$delta + 1]);
			if ($fp[$delta] >= $this->n) {
				$this->pos = $this->path[$delta]; // 経路を決定
				return;
			}
		}
	}

	function snake($k, $y1, $y2)
	{
		if ($y1 >= $y2) {
			$_k = $k - 1;
			$y = $y1 + 1;
		} else {
			$_k = $k + 1;
			$y = $y2;
		}
		$this->path[$k] = $this->path[$_k];// ここまでの経路をコピー
		$x = $y - $k;
		while ((($x + 1) < $this->m) && (($y + 1) < $this->n)
			and $this->arr1[$x + 1]->compare($this->arr2[$y + 1]))
		{
			++$x; ++$y;
			$this->path[$k][] = array('x'=>$x, 'y'=>$y); // 経路を追加
		}
		return $y;
	}

	function toArray()
	{
		$arr = array();
		if ($this->reverse) { // 姑息な…
			$_x = 'y'; $_y = 'x'; $_m = $this->n; $arr1 =& $this->arr2; $arr2 =& $this->arr1;
		} else {
			$_x = 'x'; $_y = 'y'; $_m = $this->m; $arr1 =& $this->arr1; $arr2 =& $this->arr2;
		}

		$x = $y = 1;
		$this->add_count = $this->delete_count = 0;
		$this->pos[] = array('x'=>$this->m, 'y'=>$this->n); // Sentinel
		foreach ($this->pos as $pos) {
			$this->delete_count += ($pos[$_x] - $x);
			$this->add_count    += ($pos[$_y] - $y);

			while ($pos[$_x] > $x) {
				$arr1[$x]->set($this->key, $this->minus);
				$arr[] = $arr1[$x++];
			}

			while ($pos[$_y] > $y) {
				$arr2[$y]->set($this->key, $this->plus);
				$arr[] =  $arr2[$y++];
			}

			if ($x < $_m) {
				$arr1[$x]->merge($arr2[$y]);
				$arr1[$x]->set($this->key, $this->equal);
				$arr[] = $arr1[$x];
			}
			++$x; ++$y;
		}
		return $arr;
	}
}

class XpWikiDiffLine
{
	var $text;
	var $status;

	function XpWikiDiffLine($text)
	{
		$this->text   = $text . "\n";
		$this->status = array();
	}

	function compare($obj)
	{
		return $this->text == $obj->text;
	}

	function set($key, $status)
	{
		$this->status[$key] = $status;
	}

	function get($key)
	{
		return isset($this->status[$key]) ? $this->status[$key] : '';
	}

	function merge($obj)
	{
		$this->status += $obj->status;
	}

	function text()
	{
		return $this->text;
	}
}
//----- End diff.php -----//
?>