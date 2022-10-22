<?php
/**
 * "Inline" diff renderer.
 *
 * $Horde: framework/Text_Diff/Diff/Renderer/inline.php,v 1.4.10.16 2009/07/24 13:25:29 jan Exp $
 *
 * Copyright 2004-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you did
 * not receive this file, see http://opensource.org/licenses/lgpl-license.php.
 *
 * @author  Ciprian Popovici
 * @package Text_Diff
 */

/** Text_Diff_Renderer */
if ( ! class_exists( 'Text_Diff_Renderer' ) ) {
	require_once 'Text/Diff/Renderer.php';
}

/**
 * "Inline" diff renderer.
 *
 * This class renders diffs in the Wiki-style "inline" format.
 *
 * @author  Ciprian Popovici
 * @package Text_Diff
 */
class Text_Diff_Renderer_inline extends Text_Diff_Renderer {
	/**
	 * Number of leading context "lines" to preserve.
	 */

	public $_leading_context_lines = 10000;

	/**
	 * Number of trailing context "lines" to preserve.
	 */

	public $_trailing_context_lines = 10000;

	/**
	 * Prefix for inserted text.
	 */

	public $_ins_prefix = '<ins>';

	/**
	 * Suffix for inserted text.
	 */

	public $_ins_suffix = '</ins>';

	/**
	 * Prefix for deleted text.
	 */

	public $_del_prefix = '<del>';

	/**
	 * Suffix for deleted text.
	 */

	public $_del_suffix = '</del>';

	/**
	 * Header for each change block.
	 */

	public $_block_header = '';

	/**
	 * What are we currently splitting on? Used to recurse to show word-level
	 * changes.
	 */

	public $_split_level = 'lines';

	/**
	 * @param $xbeg
	 * @param $xlen
	 * @param $ybeg
	 * @param $ylen
	 *
	 * @return string
	 */

	public function _blockHeader( $xbeg, $xlen, $ybeg, $ylen ) {
		return $this->_block_header;
	}

	/**
	 * @param $header
	 *
	 * @return mixed
	 */

	public function _startBlock( $header ) {
		return $header;
	}

	/**
	 * @param        $lines
	 * @param string $prefix
	 * @param bool $encode
	 *
	 * @return string
	 */

	public function _lines($lines, $prefix = ' ', bool $encode = true ) {
		if ( $encode ) {
			array_walk( $lines, [ &$this, '_encode' ] );
		}

		if ( 'words' == $this->_split_level ) {
			return implode( '', $lines );
		}

		return implode( "\n", $lines ) . "\n";
	}

	/**
	 * @param $lines
	 *
	 * @return string
	 */

	public function _added( $lines ) {
		array_walk( $lines, [ &$this, '_encode' ] );

		$lines[0]                     = $this->_ins_prefix . $lines[0];
		$lines[ count( $lines ) - 1 ] .= $this->_ins_suffix;

		return $this->_lines( $lines, ' ', false );
	}

	/**
	 * @param      $lines
	 * @param bool $words
	 *
	 * @return string
	 */

	public function _deleted( $lines, $words = false ) {
		array_walk( $lines, [ &$this, '_encode' ] );

		$lines[0]                     = $this->_del_prefix . $lines[0];
		$lines[ count( $lines ) - 1 ] .= $this->_del_suffix;

		return $this->_lines( $lines, ' ', false );
	}

	/**
	 * @param $orig
	 * @param $final
	 *
	 * @return string
	 */

	public function _changed( $orig, $final ) {
		/* If we've already split on words, don't try to do so again - just
		 * display. */
		if ( $this->_split_level == 'words' ) {
			$prefix = '';

			while ( false !== $orig[0] && false !== $final[0]
			        && ' ' == mb_substr( $orig[0], 0, 1 )
			        && ' ' == mb_substr( $final[0], 0, 1 ) ) {
				$prefix .= mb_substr( $orig[0], 0, 1 );

				$orig[0] = mb_substr( $orig[0], 1 );

				$final[0] = mb_substr( $final[0], 1 );
			}

			return $prefix . $this->_deleted( $orig ) . $this->_added( $final );
		}

		$text1 = implode( "\n", $orig );
		$text2 = implode( "\n", $final );

		/* Non-printing newline marker. */
		$nl = "\0";

		/* We want to split on word boundaries, but we need to
		 * preserve whitespace as well. Therefore we split on words,
		 * but include all blocks of whitespace in the wordlist. */

		$diff = new Text_Diff( $this->_splitOnWords( $text1, $nl ), $this->_splitOnWords( $text2, $nl ) );

		/* Get the diff in inline format. */

		$renderer = new self( array_merge( $this->getParams(), [ 'split_level' => 'words' ] ) );

		/* Run the diff and get the output. */

		return str_replace( $nl, "\n", $renderer->render( $diff ) ) . "\n";
	}

	/**
	 * @param        $string
	 * @param string $newlineEscape
	 *
	 * @return array
	 */

	public function _splitOnWords($string, string $newlineEscape = "\n" ) {
		$words = [];

		$length = mb_strlen( $string );

		$pos = 0;

		while ( $pos < $length ) {
			// Eat a word with any preceding whitespace.

			$spaces = strspn( mb_substr( $string, $pos ), " \n" );

			$nextpos = strcspn( mb_substr( $string, $pos + $spaces ), " \n" );

			$words[] = str_replace( "\n", $newlineEscape, mb_substr( $string, $pos, $spaces + $nextpos ) );

			$pos += $spaces + $nextpos;
		}

		return $words;
	}

	public function _encode( &$string ) {
		$string = htmlspecialchars( $string, ENT_QUOTES | ENT_HTML5 );
	}

}
