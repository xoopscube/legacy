<?php
/**
 * @package    profile
 * @version    2.5.0
 * @author     Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Kilica
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractFilterForm.class.php';

define('PROFILE_DATA_SORT_KEY_UID', 1);
define('PROFILE_DATA_SORT_KEY_DEFAULT', PROFILE_DATA_SORT_KEY_UID);

class Profile_DataFilterForm extends Profile_AbstractFilterForm
{
    public $mSortKeys = [
        PROFILE_DATA_SORT_KEY_UID => 'uid'
    ];

    /*** Profile_DefinitionsObject[] ***/ public $mFields = [];

    /**
     * @public
     */
    public function getDefaultSortKey()
    {
        return PROFILE_DATA_SORT_KEY_DEFAULT;
    }

    /**
     * _addSortKeys
     *
     * @return  void
     */
    protected function _addSortKeys()
    {
        foreach ($this->mFields as $field) {
            $this->mSortKeys[$field->getShow('field_id')] = $field->get('field_name');
        }
    }

    /**
     * prepare
     * 
     * @param   XCube_PageNavigator  &$navi
     * @param   XoopsObjectGenericHandler  &$handler
     * 
     * @return  void
    **/
    public function prepare(/*** XCube_PageNavigator ***/ &$navi, /*** XoopsObjectGenericHandler ***/ &$handler)
    {
        $this->mFields = xoops_getmodulehandler('definitions', 'profile')->getFields4DataShow(Legacy_Utils::getUid());
    
        $this->_addSortKeys();
        parent::prepare($navi, $handler);
    }

    /**
     * @public
     */
    public function fetch()
    {
        parent::fetch();
    
        $root =& XCube_Root::getSingleton();
    
        if (null !== ($value = $root->mContext->mRequest->getRequest('uid'))) {
            $this->mNavi->addExtra('uid', $value);
            $this->_mCriteria->add(new Criteria('uid', $value));
        }
    
        foreach ($this->mFields as $field) {
            $value = $root->mContext->mRequest->getRequest($field->get('field_name'));
            if (isset($value) && '' !== $value) {
                $this->mNavi->addExtra($field->get('field_name'), $value);
                if ($field->get('type')==Profile_FormType::STRING || $field->get('type')==Profile_FormType::TEXT) {
                    $value = '%'.$value.'%';
                }
                $this->_mCriteria->add(new Criteria($field->get('field_name'), $value, 'LIKE'));
            }
        }
    
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}
