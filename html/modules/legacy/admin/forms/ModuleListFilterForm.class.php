<?php
/**
 *  ModuleListFilterForm.class.php
 * This file is generated by Sort Filter Unit Class Maker.
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/modules/legacy/class/AbstractFilterForm.class.php';

define('MODULE_SORT_KEY_MID', 1);
define('MODULE_SORT_KEY_NAME', 2);
define('MODULE_SORT_KEY_VERSION', 3);
define('MODULE_SORT_KEY_LASTUPDATE', 4);
define('MODULE_SORT_KEY_WEIGHT', 5);
define('MODULE_SORT_KEY_ISACTIVE', 6);
define('MODULE_SORT_KEY_DIRNAME', 7);
define('MODULE_SORT_KEY_HASMAIN', 8);
define('MODULE_SORT_KEY_HASADMIN', 9);
define('MODULE_SORT_KEY_HASSEARCH', 10);
define('MODULE_SORT_KEY_HASCONFIG', 11);
define('MODULE_SORT_KEY_HASCOMMENTS', 12);

define('MODULE_SORT_KEY_DEFAULT', MODULE_SORT_KEY_WEIGHT);
define('MODULE_SORT_KEY_MAXVALUE', 12);

/***
 * @internal
 * This is the special filter for the list of installed modules without the
 * pager.
 */
class Legacy_ModuleListFilterForm extends Legacy_AbstractFilterForm
{
    public $mSpecial = null;

    public $mSortKeys = [
        MODULE_SORT_KEY_MID         => 'mid',
        MODULE_SORT_KEY_NAME        => 'name',
        MODULE_SORT_KEY_VERSION     => 'version',
        MODULE_SORT_KEY_LASTUPDATE  => 'last_update',
        MODULE_SORT_KEY_WEIGHT      => 'weight',
        MODULE_SORT_KEY_ISACTIVE    => 'isactive',
        MODULE_SORT_KEY_DIRNAME     => 'dirname',
        MODULE_SORT_KEY_HASMAIN     => 'hasmain',
        MODULE_SORT_KEY_HASADMIN    => 'hasadmin',
        MODULE_SORT_KEY_HASSEARCH   => 'hassearch',
        MODULE_SORT_KEY_HASCONFIG   => 'hasconfig',
        MODULE_SORT_KEY_HASCOMMENTS => 'hascomments'
    ];

    public function __construct()
    {
        $this->_mCriteria =new CriteriaCompo();
    }

    public function getDefaultSortKey()
    {
        return MODULE_SORT_KEY_DEFAULT;
    }

    public function fetch()
    {
        $this->fetchSort();

        if (isset($_REQUEST['special'])) {
            $this->mSpecial = (int)xoops_getreqeust('special');
        }

        $this->_mCriteria->add(new Criteria('mid', 0, '>'));

        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }

    public function getCriteria($start = null, $limit = null)
    {
        $criteria = $this->_mCriteria;

        $criteria->setStart(0);
        $criteria->setLimit(0);

        return $criteria;
    }
}
