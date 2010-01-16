<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

if(!defined('LECAT_TRUST_PATH'))
{
    define('LECAT_TRUST_PATH',XOOPS_TRUST_PATH . '/modules/lecat');
}

if(!defined('LEGACY_CATEGORY_DIRNAME'))
{
	define('LEGACY_CATEGORY_DIRNAME', basename(dirname(dirname(__FILE__))));
}

require_once LECAT_TRUST_PATH . '/class/LecatUtils.class.php';
require_once LECAT_TRUST_PATH . '/class/DelegateFunctions.class.php';


Lecat_Category::prepare();


/**
 * Lecat_Category
**/
class Lecat_Category extends XCube_ActionFilter
{
    /**
     * prepare
     * 
     * @param   void
     * 
     * @return  void
    **/
    public static function prepare()
    {
        $root =& XCube_Root::getSingleton();
        $instance = new Lecat_Category($root->mController);
        $root->mController->addActionFilter($instance);
    }

    /**
     * preBlockFilter
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Legacy_Category.GetTitle','Lecat_DelegateFunctions::getTitle');
        $this->mRoot->mDelegateManager->add('Legacy_Category.GetTree','Lecat_DelegateFunctions::getTree');
        $this->mRoot->mDelegateManager->add('Legacy_Category.GetTitleList','Lecat_DelegateFunctions::getTitleList');
        $this->mRoot->mDelegateManager->add('Legacy_Category.CheckPermitByUserId','Lecat_DelegateFunctions::checkPermitByUserId');
        $this->mRoot->mDelegateManager->add('Legacy_Category.CheckPermitByGroupId','Lecat_DelegateFunctions::checkPermitByGroupId');
        $this->mRoot->mDelegateManager->add('Legacy_Category.GetParent','Lecat_DelegateFunctions::getParent');
        $this->mRoot->mDelegateManager->add('Legacy_Category.GetChildren','Lecat_DelegateFunctions::getChildren');
        $this->mRoot->mDelegateManager->add('Legacy_Category.GetCatPath','Lecat_DelegateFunctions::getCatPath');
        $this->mRoot->mDelegateManager->add('Legacy_Category.GetPermittedIdList','Lecat_DelegateFunctions::getPermittedIdList');
    }
}

?>
