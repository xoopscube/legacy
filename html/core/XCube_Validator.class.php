<?php
/**
 * /core/XCube_Validator.class.php
 * @package    XCube
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      This class defines an interface which XCube_ActionForm calls the check functions.
 *  But this class is designing now, you should not write a code which dependents
 * on the design of this class. We designed this class as static method class group
 * with a reason which a program can not generate many instance quickly. However,
 * if we will find better method to solve a problem, we will change it.
 *
 *  Don't use these classes directly, you should use XCube_ActionForm only.
 * This is 'protected' accesser in the namespace of XCube_ActionForm.
 */

class XCube_Validator {
	/**
	 * XCube_FormProperty
	 *
	 * @param  $form
	 * @param array $vars variables of this field property.
	 *
	 * @return void
	 */
	public function isValid( &$form, $vars ) {
	}
}

class XCube_RequiredValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		return ! $form->isNull();
	}
}

class XCube_MinlengthValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}

		return strlen( $form->toString() ) >= $vars['minlength'];
	}
}

class XCube_MaxlengthValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}

		return strlen( $form->toString() ) <= $vars['maxlength'];
	}
}

class XCube_MinValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}

		return $form->toNumber() >= $vars['min'];
	}
}

class XCube_MaxValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}

		return $form->toNumber() <= $vars['max'];
	}
}

class XCube_IntRangeValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}

		return ( (int) $form->toNumber() >= $vars['min'] && (int) $form->toNumber() <= $vars['max'] );
	}
}

class XCube_EmailValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}

		return preg_match( "/^[_a-z0-9\-+!#$%&'*\/=?^`{|}~]+(\.[_a-z0-9\-+!#$%&'*\/=?^`{|}~]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $form->toString() );
	}
}

class XCube_MaskValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}

		return preg_match( $vars['mask'], $form->toString() );
	}
}

class XCube_ExtensionValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}
		if ( ! $form instanceof \XCube_FileProperty ) {
			return true;
		}

		$extArr = explode( ',', $vars['extension'] );
		foreach ( $extArr as $ext ) {
			if ( strtolower( $form->mValue->getExtension() ) == strtolower( $ext ) ) {
				return true;
			}
		}

		return false;
	}
}

class XCube_MaxfilesizeValidator extends XCube_Validator {
	public function isValid( &$form, $vars ) {
		if ( $form->isNull() ) {
			return true;
		}
		if ( ! $form instanceof \XCube_FileProperty ) {
			return true;
		}

		return ( $form->mValue->getFileSize() <= $vars['maxfilesize'] );
	}
}
