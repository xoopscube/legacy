<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Validator.class.php,v 1.7 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */

/**
 *  This class defines a interface which XCube_ActionForm calls the check functions.
 *  But this class is designing now, you should not write a code which dependents
 * on the design of this class. We designed this class as static method class group
 * with a reason which a program can not generate many instance quickly. However,
 * if we will find better method to solve a problem, we will change it.
 *
 *  Don't use these classes directly, you should use XCube_ActionForm only.
 * This is 'protected' accesser in the namespace of XCube_ActionForm.
 */
class XCube_Validator
{
	/**
	 * 
	 * @param XCube_FormProperty $form
	 * @param array              $vars   variables of this field property.
	 * @return bool
	 */
	function isValid(&$form, $vars)
	{
	}
}

class XCube_RequiredValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		return !$form->isNull();
	}
}

class XCube_MinlengthValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			return strlen($form->toString()) >= $vars['minlength'];
		}
	}
}

class XCube_MaxlengthValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			return strlen($form->toString()) <= $vars['maxlength'];
		}
	}
}

class XCube_MinValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			return $form->toNumber() >= $vars['min'];
		}
	}
}

class XCube_MaxValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			return $form->toNumber() <= $vars['max'];
		}
	}
}

class XCube_IntRangeValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			return (intval($form->toNumber()) >= $vars['min'] && intval($form->toNumber()) <= $vars['max']);
		}
	}
}

class XCube_EmailValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			return preg_match("/^[_a-z0-9\-+!#$%&'*\/=?^`{|}~]+(\.[_a-z0-9\-+!#$%&'*\/=?^`{|}~]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $form->toString());
		}
	}
}

class XCube_MaskValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			return preg_match($vars['mask'], $form->toString());
		}
	}
}

class XCube_ExtensionValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			if (!is_a($form, "XCube_FileProperty")) {
				return true;
			}
			
			$extArr = explode(",", $vars['extension']);
			foreach ($extArr as $ext) {
				if (strtolower($form->mValue->getExtension()) == strtolower($ext)) {
					return true;
				}
			}
			
			return false;
		}
	}
}

class XCube_MaxfilesizeValidator extends XCube_Validator
{
	function isValid(&$form, $vars)
	{
		if ($form->isNull()) {
			return true;
		}
		else {
			if (!is_a($form, "XCube_FileProperty")) {
				return true;
			}
			
			return ($form->mValue->getFileSize() <= $vars['maxfilesize']);
		}
	}
}

?>
