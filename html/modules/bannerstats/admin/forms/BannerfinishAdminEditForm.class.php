<?php
/**
 * @package bannerstats
 * @version $Id: BannerfinishAdminEditForm.class.php,v 1.1 2024/05/19 Nuno Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Bannerstats_BannerfinishAdminEditForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.bannerstats.BannerfinishAdminEditForm.TOKEN' . $this->get('bid');
    }

    public function prepare()
    {
        // Set form properties
        $this->mFormProperties['bid'] = new XCube_IntProperty('bid');
        $this->mFormProperties['imptotal'] = new XCube_IntProperty('imptotal');
        $this->mFormProperties['imageurl'] = new XCube_StringProperty('imageurl');
        $this->mFormProperties['clickurl'] = new XCube_StringProperty('clickurl');
        $this->mFormProperties['htmlbanner'] = new XCube_BoolProperty('htmlbanner');
        $this->mFormProperties['htmlcode'] = new XCube_TextProperty('htmlcode');
    
        // Set field properties
        $this->mFieldProperties['bid'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['bid']->setDependsByArray(['required']);
        $this->mFieldProperties['bid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_BID);
        
        $this->mFieldProperties['imptotal'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['imptotal']->setDependsByArray(['required', 'min']);
        $this->mFieldProperties['imptotal']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_IMPTOTAL);
        $this->mFieldProperties['imptotal']->addMessage('min', '_AD_BANNERSTATS_ERROR_MIN', _AD_BANNERSTATS_LANG_IMPTOTAL, '1');
        $this->mFieldProperties['imptotal']->addVar('min', '1');
        
        $this->mFieldProperties['imageurl'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['imageurl']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['imageurl']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_IMAGEURL, '191');
        $this->mFieldProperties['imageurl']->addVar('maxlength', '191');
        
        $this->mFieldProperties['clickurl'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['clickurl']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['clickurl']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_CLICKURL, '191');
        $this->mFieldProperties['clickurl']->addVar('maxlength', '191');
    }

    public function load(&$obj)
    {
        $this->set('bid', $obj->get('bid'));
        
        // Set default values for the new banner
        $this->set('imptotal', 1000); // Default impression count
        $this->set('imageurl', '');
        $this->set('clickurl', '');
        $this->set('htmlbanner', 0);
        $this->set('htmlcode', '');
    }

    public function update(&$obj)
    {
        $obj->set('bid', $this->get('bid'));
        // Note: Other properties are handled in the action class
    }
}