<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

include_once( XOOPS_ROOT_PATH . '/class/module.textsanitizer.php' );

class D3forumTextSanitizer extends MyTextSanitizer {
	public $nbsp = 0;

	public function __construct() {
		parent::__construct();
	}

	public static function &sGetInstance() {
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new D3forumTextSanitizer();
		}

		return $instance;
	}

	// override
	// a fix to the original implementation
	public function &htmlSpecialChars( $text, $forEdit = false ) {
		$ret = htmlspecialchars( $text, ENT_QUOTES );

		return $ret;
	}

	public function reviveNumberEntity( $text ) {
		return preg_replace( '/\&amp\;\#([0-9]{2,10}\;)/', '&#\\1', $text );
	}

	public function reviveSpecialEntity( $text ) {
		return preg_replace( '/\&amp\;([0-9a-zA-Z]{2,10}\;)/', '&\\1', $text );
	}

	// override
	public function &displayTarea( $text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $nbsp = 0, $number_entity = 0, $special_entity = 0 ) {
		$this->nbsp = $nbsp;

		if ( empty( $xcode ) ) {
			if ( empty( $html ) ) {
				$text = htmlspecialchars( $text, ENT_QUOTES );
			}
			if ( ! empty( $br ) ) {
				$text = nl2br( $text );
			}
		} else {
			$text = $this->prepareXcode( $text );
			$text = $this->postCodeDecode( parent::displayTarea( $text, $html, $smiley, 1, $image, $br ), $image );
		}

		if ( $number_entity ) {
			$text = $this->reviveNumberEntity( $text );
		}
		if ( $special_entity ) {
			$text = $this->reviveSpecialEntity( $text );
		}

		return $text;
	}

	// override
	public function makeTboxData4Show( $text, $number_entity = 0, $special_entity = 0 ) {
		$text = $this->htmlSpecialChars( $text );

		if ( $number_entity ) {
			$text = $this->reviveNumberEntity( $text );
		}
		if ( $special_entity ) {
			$text = $this->reviveSpecialEntity( $text );
		}

		return $text;
	}

	// override
	public function makeTboxUrl4Show( $text ) {
		$text = $this->makeTboxData4Show( $text );

		if ( preg_match( '#^https?\://#', $text ) ) {
			return $text;
		}

		return '';
	}

	// override
	public function makeTboxData4Edit( $text, $number_entity = 0 ) {
		$text = $this->htmlSpecialChars( $text );

		if ( $number_entity ) {
			$text = $this->reviveNumberEntity( $text );
		}

		return $text;
	}

	// override
	public function makeTareaData4Edit( $text, $number_entity = 0 ) {
		$text = $this->htmlSpecialChars( $text );

		if ( $number_entity ) {
			$text = $this->reviveNumberEntity( $text );
		}

		return $text;
	}

	// additional pre filters
	public function prepareXcode( $text ) {
		$patterns     = [
			'#\n?\[code\]\r?\n?#',
			'#\n?\[\/code\]\r?\n?#',
			'#\n?\[quote\]\r?\n?#',
			'#\n?\[\/quote\]\r?\n?#',
		];
		$replacements = [
			'[code]',
			'[/code]',
			'[quote]',
			"\n" . '[/quote]',
		];

		return preg_replace( $patterns, $replacements, $text );
	}

	// additional post filters
	public function postCodeDecode( $text, $image ) {
		$removal_tags = [ '[summary]', '[/summary]', '[pagebreak]' ];

		$text = str_replace( $removal_tags, '', $text );

		$patterns = [];

		$replacements = [];

		// [siteimg]
		$patterns[] = "/\[siteimg align=(['\"]?)(left|center|right)\\1]([^\"\(\)\?\&'<>]*)\[\/siteimg\]/sU";

		$patterns[] = "/\[siteimg]([^\"\(\)\?\&'<>]*)\[\/siteimg\]/sU";

		$replacements[] = '<img src="' . XOOPS_URL . '/\\3" align="\\2" alt="">';

		$replacements[] = '<img src="' . XOOPS_URL . '/\\1" alt="">';

		// [1.1.3.1] etc.
		$patterns[] = '/\[(1(\.\d)+)]/';

		$replacements[] = '<a href="#post_path\\1">\\0</a>';

		// [quote sitecite=]
		$patterns[] = "/\[quote sitecite=([^\"'<>]*)\]/sU";

		$replacements[] = _QUOTEC . '<div class="xoopsQuote"><blockquote cite="' . XOOPS_URL . '/\\1">';

		// [quote cite=] (TODO)

		return preg_replace( $patterns, $replacements, $text );
	}

	// override
	public function &nl2Br( $text ) {
		$text = parent::nl2Br( $text );

		if ( $this->nbsp ) {
			$patterns = [ '  ', '\"' ];

			$replaces = [ ' &nbsp;', '"' ];

			$text = substr( preg_replace( '/\>.*\</sU', "str_replace(\$patterns,\$replaces,'\\0')", ">$text<" ), 1, - 1 );
		}

		return $text;
	}

	public function extractSummary( $text ) {
		$patterns = [];
  $replacements = [];
  $patterns[] = "/^(.*)\[summary\](.*)\[\/summary\](.*)$/sU";

		$replacements[] = '$2';

		return preg_replace( $patterns, $replacements, $text );
	}

	// override
	public function codeConv( $text, $xcode = 1, $image = 1 ) {
		if ( 0 !== $xcode && ! defined( 'XOOPS_CUBE_LEGACY' ) ) {
			// bug fix
			$text = preg_replace_callback( "/\[code](.*)\[\/code\]/sU", [ $this, 'myCodeSanitizer' ], $text );
		} else {
			$text = parent::codeConv( $text, $xcode, $image );
		}

		return $text;
	}

	public function myCodeSanitizer( $matches ) {
		return '<div class="xoopsCode"><pre><code>' . $this->xoopsCodeDecodeSafe( base64_decode( $matches[1] ) ) . '</code></pre></div>';
	}

	public function xoopsCodeDecodeSafe( $text ) {
		$patterns = [];
  $replacements = [];
  // Though I know this is bad judgement ...
		if ( preg_match( '/[<>\'\"]/', $text ) ) {
			$text = htmlspecialchars( str_replace( '\"', '"', $text ), ENT_QUOTES );
		}

		$patterns[] = "/\[color=(['\"]?)([a-zA-Z0-9]*)\\1](.*)\[\/color\]/sU";

		$replacements[] = '<span style="color: #\\2;">\\3</span>';

		//		$patterns[] = "/\[size=(['\"]?)([a-z0-9-]*)\\1](.*)\[\/size\]/sU";
		//		$replacements[] = '<span style="font-size: \\2;">\\3</span>';
		//		$patterns[] = "/\[font=(['\"]?)([^;<>\*\(\)\"']*)\\1](.*)\[\/font\]/sU";
		//		$replacements[] = '<span style="font-family: \\2;">\\3</span>';

		$patterns[] = "/\[b](.*)\[\/b\]/sU";

		$replacements[] = '<strong>\\1</strong>';

		$patterns[] = "/\[i](.*)\[\/i\]/sU";

		$replacements[] = '<i>\\1</i>';

		$patterns[] = "/\[u](.*)\[\/u\]/sU";

		$replacements[] = '<span style="text-decoration:underline">\\1</span>';

		$patterns[] = "/\[d](.*)\[\/d\]/sU";

		$replacements[] = '<del>\\1</del>';

		return preg_replace( $patterns, $replacements, $text );
	}
}
