<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Part of MIME_Type
 *
 * PHP version 4 and 5
 *
 * @category File
 * @package  MIME_Type
 * @author   Ian Eure <ieure@php.net>
 * @license  https://www.gnu.org/copyleft/lesser.html LGPL
 * @link     https://pear.php.net/package/MIME_Type
 */

/**
 * Class for working with MIME type parameters
 *
 * @category File
 * @package  MIME_Type
 * @author   Ian Eure <ieure@php.net>
 * @license  https://www.gnu.org/copyleft/lesser.html LGPL
 * @version  Release: @version@
 * @link     https://pear.php.net/package/MIME_Type
 */
class MIME_Type_Parameter {
	/**
	 * Parameter name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Parameter value
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Parameter comment
	 *
	 * @var string
	 */
	public $comment;


	/**
	 * Constructor.
	 *
	 * @param string $param MIME parameter to parse, if set.
	 *
	 * @return void
	 */
	public function MIME_Type_Parameter( $param = false ) {
		if ( $param ) {
			$this->parse( $param );
		}
	}


	/**
	 * Parse a MIME type parameter and set object fields
	 *
	 * @param string $param MIME type parameter to parse
	 *
	 * @return void
	 */
	public function parse( $param ) {
		$comment       = '';
		$param         = MIME_Type::stripComments( $param, $comment );
		$this->name    = $this->getAttribute( $param );
		$this->value   = $this->getValue( $param );
		$this->comment = $comment;
	}


	/**
	 * Get a parameter attribute (e.g. name)
	 *
	 * @param string $param MIME type parameter
	 *
	 * @return string Attribute name
	 * @static
	 */
	public function getAttribute( $param ) {
		$tmp = explode( '=', $param );

		return trim( $tmp[0] );
	}


	/**
	 * Get a parameter value
	 *
	 * @param string $param MIME type parameter
	 *
	 * @return string Value
	 * @static
	 */
	public function getValue( $param ) {
		$tmp   = explode( '=', $param, 2 );
		$value = $tmp[1];
		$value = trim( $value );
		if ( $value[0] == '"' && $value[ strlen( $value ) - 1 ] == '"' ) {
			$value = substr( $value, 1, - 1 );
		}
		$value = str_replace( '\\"', '"', $value );

		return $value;
	}


	/**
	 * Get a parameter comment
	 *
	 * @param string $param MIME type parameter
	 *
	 * @return string Parameter comment
	 * @see    hasComment()
	 * @static
	 */
	public function getComment( $param ) {
		$cs = strpos( $param, '(' );
		if ( $cs === false ) {
			return null;
		}
		$comment = substr( $param, $cs );

		return trim( $comment, '() ' );
	}


	/**
	 * Does this parameter have a comment?
	 *
	 * @param string $param MIME type parameter
	 *
	 * @return boolean true if $param has a comment, false otherwise
	 * @static
	 */
	public function hasComment( $param ) {
		if ( strstr( $param, '(' ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Get a string representation of this parameter
	 *
	 * This function performs the oppsite of parse()
	 *
	 * @return string String representation of parameter
	 */
	public function get() {
		$val = $this->name . '="' . str_replace( '"', '\\"', $this->value ) . '"';
		if ( $this->comment ) {
			$val .= ' (' . $this->comment . ')';
		}

		return $val;
	}
}
