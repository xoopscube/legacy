<?php
/**
 *
 * @package Legacy
 * @version $Id: SmilesFilterForm.class.php,v 1.3 2008/09/25 15:12:40 kilica Exp $
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractFilterForm.class.php';

define('SMILES_SORT_KEY_ID', 1);
define('SMILES_SORT_KEY_CODE', 2);
define('SMILES_SORT_KEY_SMILE_URL', 3);
define('SMILES_SORT_KEY_EMOTION', 4);
define('SMILES_SORT_KEY_DISPLAY', 5);
define('SMILES_SORT_KEY_MAXVALUE', 5);

define('SMILES_SORT_KEY_DEFAULT', SMILES_SORT_KEY_ID);

class Legacy_SmilesFilterForm extends Legacy_AbstractFilterForm
{
    public $mSortKeys = [
        SMILES_SORT_KEY_ID => 'id',
        SMILES_SORT_KEY_CODE => 'code',
        SMILES_SORT_KEY_SMILE_URL => 'smile_url',
        SMILES_SORT_KEY_EMOTION => 'emotion',
        SMILES_SORT_KEY_DISPLAY => 'display'
    ];

    public function getDefaultSortKey()
    {
        return SMILES_SORT_KEY_ID;
    }

    public function fetch()
    {
        parent::fetch();

        if (isset($_REQUEST['target'])) {
            $this->mNavi->addExtra('target', xoops_getrequest('target'));
        }

        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}
