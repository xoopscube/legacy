<?php
/**
 * This is subset and modified version of module.textsanitizer.php
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @author     Goghs Cheng
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


class textsanitizer {

	/*
	* Constructor of this class
	* Gets allowed html tags from admin config settings
	* <br> should not be allowed since nl2br will be used
	* when storing data
	*/

	public static function getInstance() {
		static $instance;
		if ( ! isset( $instance ) ) {
			$instance = new TextSanitizer();
		}

		return $instance;
	}

	public function &makeClickable( &$text ) {
		$patterns     = [
			"/([^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([^, \r\n\"\(\)'<>]+)/i",
			"/([^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i",
			"/([^]_a-z0-9-=\"'\/])([a-z0-9\-_.]+?)@([^, \r\n\"\(\)'<>]+)/i"
		];
		$replacements = [
			"\\1<a href=\"\\2://\\3\" rel=\"external\">\\2://\\3</a>",
			"\\1<a href=\"https://www.\\2.\\3\" rel=\"external\">www.\\2.\\3</a>",
			"\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>"
		];
		$ret          = preg_replace( $patterns, $replacements, $text );

		return $ret;
	}

	public function &nl2Br( $text ) {
		$ret = preg_replace( "/(\015\012)|(\015)|(\012)/", '<br>', $text );

		return $ret;
	}

	public function &addSlashes( $text, $force = false ) {
		if ( $force ) {
			$ret = addslashes( $text );

			return $ret;
		}

		return $text;
	}

	/*
	* if magic_quotes_gpc is on, stirip back slashes
	*/
	public function &stripSlashesGPC( $text ) {
		//trigger_error("assume magic_quotes_gpc is off", E_USER_NOTICE);
		return $text;
	}

	/*
	*  for displaying data in html textbox forms
	*/
	public function &htmlSpecialChars( $text ) {
		$text = preg_replace( '/&amp;/i', '&', htmlspecialchars( $text, ENT_QUOTES ) );

		return $text;
	}

	public function &undoHtmlSpecialChars( &$text ) {
		$ret = preg_replace( [
			'/&gt;/i',
			'/&lt;/i',
			'/&quot;/i',
			'/&#039;/i'
		], [ '>', '<', '"', "'" ], $text );

		return $ret;
	}

	/*
	*  Filters textarea form data in DB for display
	*/
	public function &displayText( $text, $html = false ) {
		if ( ! $html ) {
			// html not allowed
			$text =& $this->htmlSpecialChars( $text );
		}
		$text =& $this->makeClickable( $text );
		$text =& $this->nl2Br( $text );

		return $text;
	}

	/*
	*  Filters textarea form data submitted for preview
	*/
	public function &previewText( $text, $html = false ) {
		$text =& $this->stripSlashesGPC( $text );

		return $this->displayText( $text, $html );
	}

##################### Deprecated Methods ######################

	public function sanitizeForDisplay( $text, $allowhtml = 0, $smiley = 1, $bbcode = 1 ) {
		$text = 0 === $allowhtml ? $this->htmlSpecialChars( $text ) : $this->makeClickable( $text );
		if ( 1 === $smiley ) {
			$text = $this->smiley( $text );
		}
		if ( 1 === $bbcode ) {
			$text = $this->xoopsCodeDecode( $text );
		}
		$text = $this->nl2Br( $text );

		return $text;
	}

	public function sanitizeForPreview( $text, $allowhtml = 0, $smiley = 1, $bbcode = 1 ) {
		$text = $this->oopsStripSlashesGPC( $text );
		$text = 0 === $allowhtml ? $this->htmlSpecialChars( $text ) : $this->makeClickable( $text );
		if ( 1 === $smiley ) {
			$text = $this->smiley( $text );
		}
		if ( 1 === $bbcode ) {
			$text = $this->xoopsCodeDecode( $text );
		}
		$text = $this->nl2Br( $text );

		return $text;
	}

	public function makeTboxData4Save( $text ) {
		//$text = $this->undoHtmlSpecialChars($text);
		return $this->addSlashes( $text );
	}

	public function makeTboxData4Show( $text, $smiley = 0 ) {
		$text = $this->htmlSpecialChars( $text );

		return $text;
	}

	public function makeTboxData4Edit( $text ) {
		return $this->htmlSpecialChars( $text );
	}

	public function makeTboxData4Preview( $text, $smiley = 0 ) {
		$text = $this->stripSlashesGPC( $text );
		$text = $this->htmlSpecialChars( $text );

		return $text;
	}

	public function makeTboxData4PreviewInForm( $text ) {
		$text = $this->stripSlashesGPC( $text );

		return $this->htmlSpecialChars( $text );
	}

	public function makeTareaData4Save( $text ) {
		return $this->addSlashes( $text );
	}

	public function &makeTareaData4Show( &$text, $html = 1, $smiley = 1, $xcode = 1 ) {
		return $this->displayTarea( $text, $html, $smiley, $xcode );
	}

	public function makeTareaData4Edit( $text ) {
		return htmlSpecialChars( $text, ENT_QUOTES );
	}

	public function &makeTareaData4Preview( &$text, $html = 1, $smiley = 1, $xcode = 1 ) {
		return $this->previewTarea( $text, $html, $smiley, $xcode );
	}

	public function makeTareaData4PreviewInForm( $text ) {
		return htmlSpecialChars( $text, ENT_QUOTES );
	}

	public function makeTareaData4InsideQuotes( $text ) {
		return $this->htmlSpecialChars( $text );
	}

	public function &oopsStripSlashesGPC( $text ) {
		return $this->stripSlashesGPC( $text );
	}

	public function &oopsStripSlashesRT( $text ) {
		//trigger_error("assume magic_quotes_gpc is off", E_USER_NOTICE);
		return $text;
	}

	public function &oopsAddSlashes( $text ) {
		return $this->addSlashes( $text );
	}

	public function &oopsHtmlSpecialChars( $text ) {
		return $this->htmlSpecialChars( $text );
	}

	public function &oopsNl2Br( $text ) {
		return $this->nl2br( $text );
	}
}
