<?php
/**
 * @package legacyRender
 * @version $Id: BannerAdminEditForm.class.php,v 1.2 2007/06/07 05:27:56 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

/***
 * @internal
 * @todo This form doesn't validate the format of URL. Isn't OK?
 */
class LegacyRender_BannerAdminEditForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.legacyRender.BannerAdminEditForm.TOKEN" . $this->get('bid');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['bid'] =new XCube_IntProperty('bid');
		$this->mFormProperties['cid'] =new XCube_IntProperty('cid');
		$this->mFormProperties['imptotal'] =new XCube_IntProperty('imptotal');
		$this->mFormProperties['imageurl'] =new XCube_StringProperty('imageurl');
		$this->mFormProperties['clickurl'] =new XCube_StringProperty('clickurl');
		$this->mFormProperties['htmlbanner'] =new XCube_BoolProperty('htmlbanner');
		$this->mFormProperties['htmlcode'] =new XCube_TextProperty('htmlcode');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['bid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['bid']->setDependsByArray(array('required'));
		$this->mFieldProperties['bid']->addMessage('required', _AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_BID);
	
		$this->mFieldProperties['cid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['cid']->setDependsByArray(array('required','objectExist'));
		$this->mFieldProperties['cid']->addMessage('required', _AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_CID);
		$this->mFieldProperties['cid']->addMessage('objectExist', _AD_LEGACYRENDER_ERROR_OBJECT_EXIST, _AD_LEGACYRENDER_LANG_CID);
		$this->mFieldProperties['cid']->addVar('handler', 'bannerclient');
		$this->mFieldProperties['cid']->addVar('module', 'legacyRender');
	
		$this->mFieldProperties['imptotal'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['imptotal']->setDependsByArray(array('required'));
		$this->mFieldProperties['imptotal']->addMessage('required', _AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_IMPTOTAL);
	
		$this->mFieldProperties['imageurl'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['imageurl']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['imageurl']->addMessage('maxlength', _AD_LEGACYRENDER_ERROR_MAXLENGTH, _AD_LEGACYRENDER_LANG_IMAGEURL, '255');
		$this->mFieldProperties['imageurl']->addVar('maxlength', '255');
	
		$this->mFieldProperties['clickurl'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['clickurl']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['clickurl']->addMessage('maxlength', _AD_LEGACYRENDER_ERROR_MAXLENGTH, _AD_LEGACYRENDER_LANG_CLICKURL, '255');
		$this->mFieldProperties['clickurl']->addVar('maxlength', '255');
	}

	function validate()
	{
		parent::validate();
		
		//
		// If htmlbanner is clicked, htmlbanner is requreid.
		//
		if ($this->get('htmlbanner')) {
			if (strlen($this->get('htmlcode')) == 0) {
				$this->addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_HTMLCODE));
			}
		}
		else {
			if (strlen($this->get('imageurl')) == 0) {
				$this->addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_IMAGEURL));
			}
			if (strlen($this->get('clickurl')) == 0) {
				$this->addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_CLICKURL));
			}
		}
	}

	function load(&$obj)
	{
		$this->set('bid', $obj->get('bid'));
		$this->set('cid', $obj->get('cid'));
		$this->set('imptotal', $obj->get('imptotal'));
		$this->set('imageurl', $obj->get('imageurl'));
		$this->set('clickurl', $obj->get('clickurl'));
		$this->set('htmlbanner', $obj->get('htmlbanner'));
		$this->set('htmlcode', $obj->get('htmlcode'));
	}

	function update(&$obj)
	{
		$obj->set('bid', $this->get('bid'));
		$obj->set('cid', $this->get('cid'));
		$obj->set('imptotal', $this->get('imptotal'));
		$obj->set('imageurl', $this->get('imageurl'));
		$obj->set('clickurl', $this->get('clickurl'));
		$obj->set('htmlbanner', $this->get('htmlbanner'));
		$obj->set('htmlcode', $this->get('htmlcode'));
	}
}

?>
