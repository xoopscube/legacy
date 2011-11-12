<?php
/*
 * Created on 2011/11/10 by nao-pon http://hypweb.net/
 * $Id: formcheckbox.php,v 1.1 2011/11/10 12:31:33 nao-pon Exp $
 */

class HypconfFormCheckBox extends XoopsFormCheckBox {

	function render()
	{
		$ret = "";
		if ( count($this->getOptions()) > 1 && substr($this->getName(), -2, 2) != "[]" ) {
			$newname = $this->getName()."[]";
			$this->setName($newname);
		}
		foreach ( $this->getOptions() as $value => $name ) {
			$ret .= "<label><input type='checkbox' name='".$this->getName()."' value='".$value."'";
			if (count($this->getValue()) > 0 && in_array($value, $this->getValue())) {
				$ret .= " checked='checked'";
			}
			$ret .= $this->getExtra()." />".$name."</label>\n";
		}
		return '<div>' . $ret . '</div>';
	}

}