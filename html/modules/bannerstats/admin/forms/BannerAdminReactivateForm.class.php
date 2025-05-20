<?php
/**
 * @package bannerstats
 * @version $Id: BannerAdminReactivateForm.class.php,v 1.2 2024/05/19 Nuno Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

/***
 * @internal
 * Form for reactivating banners from the bannerfinish table
 */
class Bannerstats_BannerAdminReactivateForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.bannerstats.BannerAdminReactivateForm.TOKEN' . $this->get('bid');
    }

    public function prepare()
    {
        // Set form properties
        $this->mFormProperties['bid'] = new XCube_IntProperty('bid');
        $this->mFormProperties['cid'] = new XCube_IntProperty('cid');
        $this->mFormProperties['imptotal'] = new XCube_IntProperty('imptotal');
        $this->mFormProperties['imageurl'] = new XCube_StringProperty('imageurl');
        $this->mFormProperties['clickurl'] = new XCube_StringProperty('clickurl');
        $this->mFormProperties['htmlbanner'] = new XCube_IntProperty('htmlbanner');
        $this->mFormProperties['htmlcode'] = new XCube_TextProperty('htmlcode');
        
        // Set field properties
        $this->mFieldProperties['bid'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['bid']->setDependsByArray(['required']);
        $this->mFieldProperties['bid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_BID);
        
        $this->mFieldProperties['cid'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cid']->setDependsByArray(['required']);
        $this->mFieldProperties['cid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_CID);
        
        $this->mFieldProperties['imptotal'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['imptotal']->setDependsByArray(['required', 'min']);
        $this->mFieldProperties['imptotal']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_IMPTOTAL);
        $this->mFieldProperties['imptotal']->addMessage('min', '_AD_BANNERSTATS_ERROR_MIN', _AD_BANNERSTATS_LANG_IMPTOTAL, '1');
        $this->mFieldProperties['imptotal']->addVar('min', '1');
        
        $this->mFieldProperties['imageurl'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['imageurl']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['imageurl']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_IMAGEURL, '255');
        $this->mFieldProperties['imageurl']->addVar('maxlength', '255');
        
        $this->mFieldProperties['clickurl'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['clickurl']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['clickurl']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_CLICKURL, '255');
        $this->mFieldProperties['clickurl']->addVar('maxlength', '255');
    }

    public function load(&$obj)
    {
        $this->set('bid', $obj->get('bid'));
        $this->set('cid', $obj->get('cid'));
        $this->set('imptotal', 1000); // Default value
        $this->set('imageurl', $obj->get('imageurl'));
        $this->set('clickurl', $obj->get('clickurl'));
        $this->set('htmlbanner', $obj->get('htmlbanner'));
        $this->set('htmlcode', $obj->get('htmlcode'));
    }

    public function update(&$obj)
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