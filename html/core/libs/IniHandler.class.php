<?php
/**
 * XCube_IniHandler.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      If the first character in a line is #, ; or //, the line is treated as comment.
 */

class XCube_IniHandler {
	/*** string[] ***/
	protected $_mConfig = [];
	/*** string ***/
	protected $_mFilePath;
	/*** bool ***/
	protected $_mSectionFlag = false;

	/**
	 * __construct
	 *
	 * @param string $filePath
	 * @param bool $section
	 *
	 * @return    void
	 **/
	public function __construct( /*** string ***/ $filePath, /*** bool ***/ $section = false ) {
		$this->_mSectionFlag = $section;
		$this->_mFilePath    = $filePath;
		$this->_loadIni();
	}

	/**
	 * _loadIni
	 *
	 * @param void
	 *
	 * @return    void
	 **/
	protected function _loadIni() {
		if ( file_exists( $this->_mFilePath ) ) {
			$key = null;
			foreach ( explode( "\n", file_get_contents( $this->_mFilePath ) ) as $line ) {
				//case: comment line
				if ( preg_match( '/^\s*([;#]|\/\/)/', $line ) ) {
					continue;
				}
				//remove CR
				$line = preg_replace( '/\r/', '', $line );
				//case: section line
				if ( preg_match( '/\[(.*)\]/', $line, $str ) ) {
					if ( true === $this->_mSectionFlag ) {
						$key                    = $str[1];
						$this->_mConfig[ $key ] = [];
					}
				} //case: key/value line
				elseif ( preg_match( '/^(.*)=(.*)$/', $line, $str ) ) {
					$name =& $str[1];
					$val  =& $str[2];
					if ( preg_match( '/^"(.*)"$/', $val, $body ) || preg_match( '/^\'(.*)\'$/', $val, $body ) ) {
						$val =& $body[1];
					}

					if ( true === $this->_mSectionFlag ) {
						$this->_mConfig[ $key ][ $name ] = $val;
					} else {
						$this->_mConfig[ $name ] = $val;
					}
				}
			}
		}
	}

	/**
	 * getConfig
	 *
	 * @param string $key
	 * @param string $section
	 *
	 * @return    string
	 **/
	public function getConfig( /*** string ***/ $key, /*** string ***/ $section = '' ) {
		if ( true === $this->_mSectionFlag ) {
			return $this->_mConfig[ $section ][ $key ];
		}

		return $this->_mConfig[ $key ];
	}

	/**
	 * getSectionConfig
	 *
	 * @param string $section
	 *
	 * @return    string[]
	 **/
	public function getSectionConfig( /*** string ***/ $section ) {
		return ( true === $this->_mSectionFlag ) ? $this->_mConfig[ $section ] : null;
	}

	/**
	 * getAllConfig
	 *
	 * @param void
	 *
	 * @return    string[]
	 **/
	public function getAllConfig() {
		return $this->_mConfig;
	}
}
