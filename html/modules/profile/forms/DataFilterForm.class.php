<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractFilterForm.class.php";

define('PROFILE_DATA_SORT_KEY_UID', 1);
define('PROFILE_DATA_SORT_KEY_DEFAULT', PROFILE_DATA_SORT_KEY_UID);

class Profile_DataFilterForm extends Profile_AbstractFilterForm
{
    public $mSortKeys = array(
        PROFILE_DATA_SORT_KEY_UID => 'uid'
    );

    public /*** Profile_DefinitionsObject[] ***/ $mFields = array();

    /**
     * @public
     */
    function getDefaultSortKey()
    {
        return PROFILE_DATA_SORT_KEY_DEFAULT;
    }

    /**
     * _addSortKeys
     * 
     * @param   string $dirname
     * 
     * @return  void
    **/
    protected function _addSortKeys()
    {
        foreach($this->mFields as $field){
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
    public function prepare(/*** XCube_PageNavigator ***/ &$navi,/*** XoopsObjectGenericHandler ***/ &$handler)
    {
        $this->mFields = xoops_getmodulehandler('definitions', 'profile')->getFields4DataShow(Legacy_Utils::getUid());
    
        $this->_addSortKeys();
        parent::prepare($navi,$handler);
    }

    /**
     * @public
     */
    function fetch()
    {
        parent::fetch();
    
        $root =& XCube_Root::getSingleton();
    
        if (($value = $root->mContext->mRequest->getRequest('uid')) !== null) {
            $this->mNavi->addExtra('uid', $value);
            $this->_mCriteria->add(new Criteria('uid', $value));
        }
    
        foreach($this->mFields as $field){
        	$value = $root->mContext->mRequest->getRequest($field->get('field_name'));
            if (isset($value) && $value!=="") {
                $this->mNavi->addExtra($field->get('field_name'), $value);
                if($field->get('type')==Profile_FormType::STRING || $field->get('type')==Profile_FormType::TEXT){
	                $value = '%'.$value.'%';
	            }
                $this->_mCriteria->add(new Criteria($field->get('field_name'), $value, 'LIKE'));
            }
        }
    
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}

?>
