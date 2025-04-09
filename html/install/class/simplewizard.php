<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */


class SimpleWizard {
	public $_v;
	public $_op;
	public $_title;
	public $_content;
	public $_next = '';
	public $_back = '';
	public $_reload = '';
	public $_template_path;
	public $_base_template_name;
	public $_custom_seq;

	public function setTemplatePath( $name ) {
		$this->_template_path = $name;
	}

	public function setBaseTemplate( $name ) {
		$this->_base_template_name = $name;
	}

	public function assign( $name, $value ) {
		$this->_v[ $name ] = $value;
	}

	public function setContent( $value ) {
		$this->_content = $value;
	}

	public function setOp( $value ) {
		$this->_op = $value;
	}

	public function setTitle( $value ) {
		$this->_title = $value;
	}

	public function setNext( $value ) {
		$this->_next       = $value;
		$this->_custom_seq = true;
	}

	public function setBack( $value ) {
		$this->_back       = $value;
		$this->_custom_seq = true;
	}

	public function setReload( $value ) {
		$this->_reload     = $value;
		$this->_custom_seq = true;
	}

	public function addArray( $name, $value ) {
		if ( ! isset( $this->_v[ $name ] ) || ! is_array( $this->_v[ $name ] ) ) {
			$this->_v[ $name ] = [];
		}
		$this->_v[ $name ][] = $value;
	}

	public function v( $name ) {
		return ! empty( $this->_v[ $name ] ) ? $this->_v[ $name ] : false;
	}

	public function e() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			if ( ! empty( $this->_v[ $args[0] ] ) ) {
				$value = $this->_v[ $args[0] ];
				if ( 2 === func_num_args() && is_array( $value ) ) {
					$value = $value[ $args[1] ];
				}
			} else {
				$value = '';
			}
			echo $value;
		}
	}

	public function render( $fname = '' ) {
		if ( $fname && file_exists( $this->_template_path . '/' . $fname ) ) {
			ob_start();
			include $this->_template_path . '/' . $fname;
			$this->setContent( ob_get_clean() );
		}
		$content = $this->_content;
		if ( ! empty( $this->_title ) ) {
			$title = $this->_title;
		} else {
			$title = $GLOBALS['wizardSeq']->getTitle( $this->_op );
		}
		if ( ! empty( $this->_next ) ) {
			$b_next = $this->_next;
		} elseif ( ! $this->_custom_seq ) {
			$b_next = $GLOBALS['wizardSeq']->getNext( $this->_op );
		} else {
			$b_next = '';
		}
		if ( ! empty( $this->_back ) ) {
			$b_back = $this->_back;
		} elseif ( ! $this->_custom_seq ) {
			$b_back = $GLOBALS['wizardSeq']->getBack( $this->_op );
		} else {
			$b_back = '';
		}
		if ( ! empty( $this->_reload ) ) {
			$b_reload = $this->_reload;
		} elseif ( ! $this->_custom_seq ) {
			$b_reload = $GLOBALS['wizardSeq']->getReload( $this->_op );
		} else {
			$b_reload = '';
		}
		include $this->_base_template_name;
	}

	public function error() {
		$content = $this->_content;
		if ( ! empty( $this->_title ) ) {
			$title = $this->_title;
		} else {
			$title = $GLOBALS['wizardSeq']->getTitle( $this->_op );
		}
		if ( ! empty( $this->_next ) ) {
			$b_next = $this->_next;
		} else {
			$b_next = '';
		}
		if ( ! empty( $this->_back ) ) {
			$b_back = $this->_back;
		} else {
			$b_back = '';
		}
		if ( ! empty( $this->_reload ) ) {
			$b_reload = $this->_reload;
		} else {
			$b_reload = '';
		}
		include $this->_base_template_name;
	}
}

class SimpleWizardSequence {
	public $_list;

	public function add( $name, $title = '', $next = '', $next_btn = '', $back = '', $back_btn = '', $reload = '' ) {
		$this->_list[ $name ]['title']    = $title;
		$this->_list[ $name ]['next']     = $next;
		$this->_list[ $name ]['next_btn'] = $next_btn;
		$this->_list[ $name ]['back']     = $back;
		$this->_list[ $name ]['back_btn'] = $back_btn;
		$this->_list[ $name ]['reload']   = $reload;
	}

	public function insertAfter( $after, $name, $title = '', $back = '', $back_btn = '', $reload = '' ) {
		if ( ! empty( $this->_list[ $after ] ) ) {
			$this->_list[ $name ]['title']     = $title;
			$this->_list[ $name ]['next']      = $this->_list[ $after ]['next'];
			$this->_list[ $name ]['next_btn']  = $this->_list[ $after ]['next_btn'];
			$this->_list[ $after ]['next']     = $name;
			$this->_list[ $after ]['next_btn'] = $title;
			$this->_list[ $name ]['back']      = $back;
			$this->_list[ $name ]['back_btn']  = $back_btn;
			$this->_list[ $name ]['reload']    = $reload;
		}
	}

	// Add replaceAfter method from GIJOE's patch.
	public function replaceAfter( $after, $name, $title = '', $next = '', $next_btn = '', $back = '', $back_btn = '', $reload = '' ) {
		if ( ! empty( $this->_list[ $after ] ) ) {
			$this->_list[ $name ]['title']     = $title;
			$this->_list[ $name ]['next']      = $next;
			$this->_list[ $name ]['next_btn']  = $next_btn;
			$this->_list[ $after ]['next']     = $name;
			$this->_list[ $after ]['next_btn'] = $title;
			$this->_list[ $name ]['back']      = $back;
			$this->_list[ $name ]['back_btn']  = $back_btn;
			$this->_list[ $name ]['reload']    = $reload;
		}
	}

	public function getTitle( $name ) {
		return ! empty( $this->_list[ $name ]['title'] ) ? ( $this->_list[ $name ]['title'] ) : '';
	}

	public function getNext( $name ) {
		return ! empty( $this->_list[ $name ]['next'] ) || ! empty( $this->_list[ $name ]['next_btn'] ) ? ( [
			$this->_list[ $name ]['next'],
			$this->_list[ $name ]['next_btn']
		] ) : '';
	}

	public function getBack( $name ) {
		return ! empty( $this->_list[ $name ]['back'] ) || ! empty( $this->_list[ $name ]['back_btn'] ) ? ( [
			$this->_list[ $name ]['back'],
			$this->_list[ $name ]['back_btn']
		] ) : '';
	}

	public function getReload( $name ) {
		return ! empty( $this->_list[ $name ]['reload'] ) ? ( $this->_list[ $name ]['reload'] ) : '';
	}
}
