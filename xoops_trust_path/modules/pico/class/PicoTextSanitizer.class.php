<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

include_once( XOOPS_ROOT_PATH . '/class/module.textsanitizer.php' );

class PicoTextSanitizer extends MyTextSanitizer {
	
	public $nbsp = 0;

	public static function &sGetInstance() {
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	public static function &getInstance() {
		$instance = &self::sGetInstance();

		return $instance;
	}

	// override
	// a fix for original bad implementation
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
		if ($text === null) {
			return '';
		}
		
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
			'[/quote]',
		];
	
		return preg_replace( $patterns, $replacements, $text );
	}

	// additional post filters
	public function postCodeDecode( $text, $image ) {
		$removal_tags = [ '[summary]', '[/summary]' /*, '[pagebreak]'*/ ];
		$text         = str_replace( $removal_tags, '', $text );

		$patterns     = [];
		$replacements = [];

		// [siteimg]
		$patterns[]     = "/\[siteimg align=(['\"]?)(left|center|right)\\1]([^\"\(\)\?\&'<>]*)\[\/siteimg\]/sU";
		$patterns[]     = "/\[siteimg]([^\"\(\)\?\&'<>]*)\[\/siteimg\]/sU";
		$replacements[] = '<img src="' . XOOPS_URL . '/\\3" align="\\2" alt="" />';
		$replacements[] = '<img src="' . XOOPS_URL . '/\\1" alt="" />';

		// [quote sitecite=]
		$patterns[]     = "/\[quote sitecite=([^\"'<>]*)\]/sU";
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
			$text     = substr( preg_replace( '/\>.*\</sU', "str_replace(\$patterns,\$replaces,'\\0')", ">$text<" ), 1, - 1 );
		}

		return $text;
	}

	public function extractSummary( $text ) {
		$patterns = [];
  $replacements = [];
  $patterns[]     = "/^(.*)\[summary\](.*)\[\/summary\](.*)$/sU";
		$replacements[] = '$2';

		return preg_replace( $patterns, $replacements, $text );
	}

	// override
	public function codeConv( $text, $xcode = 1, $image = 1 ) {
		if ( 0 != $xcode && ! defined( 'XOOPS_CUBE_LEGACY' ) ) {
			// bug fix
			$text = preg_replace_callback( "/\[code](.*)\[\/code\]/sU", [ $this, 'myCodeSanitizer' ], $text );
		} else {
			$text = parent::codeConv( $text, $xcode, $image );
		}

		return $text;
	}

	public function myCodeSanitizer( $matches ): string {
		return '<div class="xoopsCode"><pre><code>' . $this->xoopsCodeDecodeSafe( base64_decode( $matches[1] ) ) . '</code></pre></div>';
	}

	public function xoopsCodeDecodeSafe( $text ) {
		$patterns = [];
  $replacements = [];
  // Though I know this is bad judgement ...
		if ( preg_match( '/[<>\'\"]/', $text ) ) {
			$text = htmlspecialchars( str_replace( '\"', '"', $text ), ENT_QUOTES );
		}

		$patterns[]     = "/\[color=(['\"]?)([a-zA-Z0-9]*)\\1](.*)\[\/color\]/sU";
		$replacements[] = '<span style="color: #\\2;">\\3</span>';
		$patterns[]     = "/\[b](.*)\[\/b\]/sU";
		$replacements[] = '<strong>\\1</strong>';
		$patterns[]     = "/\[i](.*)\[\/i\]/sU";
		$replacements[] = '<i>\\1</i>';
		$patterns[]     = "/\[u](.*)\[\/u\]/sU";
		$replacements[] = '<span style="text-decoration:underline">\\1</span>';
		$patterns[]     = "/\[d](.*)\[\/d\]/sU";
		$replacements[] = '<del>\\1</del>';

		return preg_replace( $patterns, $replacements, $text );
	}

	public function pageBreak( $mydirname, $text, $content4assign ) {
		if ( ! strstr( $text, '[pagebreak]' ) ) {
			return $text;
		}

		$html  = '';
		$navi  = '';
		$ids   = [];
		$parts = explode( '[pagebreak]', $text );
		foreach ( $parts as $i => $part ) {
			$id    = $mydirname . '_pagebreak_' . $i;
			$ids[] = "'$id'";
			$html  .= '<div id="' . $id . '">' . $part . '</div>';
			$navi  .= '<span id="navi_' . $id . '" class="selected"></span>' . "\n";
		}

		$js = '
		<script type="text/javascript">
			picoDisplayDividedPage( 0 ) ;
			function picoDisplayDividedPage( n ) {
				n = Math.floor(n) ;
				var picoPages = new Array(' . implode( ',', $ids ) . ') ;
				picoPagesLength = picoPages.length ;
				for( i = 0 ; i < picoPagesLength ; i ++ ) {
					i = Math.floor(i) ;
					document.getElementById(picoPages[i]).style.display = "none" ;
					document.getElementById("navi_"+picoPages[i]).className = "" ;
					document.getElementById("navi_"+picoPages[i]).innerHTML = "<a href=# onClick=\"picoDisplayDividedPage("+i+");\">"+(i+1)+"</a>" ;
				}
				document.getElementById(picoPages[n]).style.display = "block" ;
				document.getElementById("navi_"+picoPages[n]).className = "selected" ;
				document.getElementById("navi_"+picoPages[n]).innerHTML = n+1 ;
			}
		</script>';

		return $html . '<div class="pico_pagebreak">' . $navi . '</div>' . $js;
	}
}
