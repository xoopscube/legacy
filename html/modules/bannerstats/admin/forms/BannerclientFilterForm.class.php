<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractFilterForm.class.php';

// Define new and updated sort keys for Banner Clients
define('BANNERCLIENT_SORT_KEY_CID', 1);
define('BANNERCLIENT_SORT_KEY_NAME', 2);
define('BANNERCLIENT_SORT_KEY_CONTACT', 3);
define('BANNERCLIENT_SORT_KEY_EMAIL', 4);
define('BANNERCLIENT_SORT_KEY_LOGIN', 5);
define('BANNERCLIENT_SORT_KEY_STATUS', 6);
define('BANNERCLIENT_SORT_KEY_COUNTRY_CODE', 7);
define('BANNERCLIENT_SORT_KEY_DATE_CREATED', 8);
define('BANNERCLIENT_SORT_KEY_LAST_UPDATED', 9);
define('BANNERCLIENT_SORT_KEY_EXTRAINFO', 10);
define('BANNERCLIENT_SORT_KEY_MAXVALUE', 10);
define('BANNERCLIENT_SORT_KEY_DEFAULT', BANNERCLIENT_SORT_KEY_CID);


class Bannerstats_BannerclientFilterForm extends Bannerstats_AbstractFilterForm
{
    /**
     * Maps sort key constants to their corresponding database field names
     * @var array<int, string>
     */
    public $mSortKeys = [
        BANNERCLIENT_SORT_KEY_CID => 'cid',
        BANNERCLIENT_SORT_KEY_NAME => 'name',
        BANNERCLIENT_SORT_KEY_CONTACT => 'contact',
        BANNERCLIENT_SORT_KEY_EMAIL => 'email',
        BANNERCLIENT_SORT_KEY_LOGIN => 'login',
        BANNERCLIENT_SORT_KEY_STATUS => 'status',
        BANNERCLIENT_SORT_KEY_COUNTRY_CODE => 'country_code',
        BANNERCLIENT_SORT_KEY_DATE_CREATED => 'date_created',
        BANNERCLIENT_SORT_KEY_LAST_UPDATED => 'last_updated',
        BANNERCLIENT_SORT_KEY_EXTRAINFO => 'extrainfo',
    ];

    public function getDefaultSortKey(): int
    {
        return BANNERCLIENT_SORT_KEY_DEFAULT;
    }

    public function fetch(): void
    {
        parent::fetch();

        if (!isset($this->mSortKeys[$this->getSort()])) {
            $this->mNavi->addExtra('sort', (string)$this->getDefaultSortKey());
        }

        $name_search = trim((string) xoops_getrequest('name', ''));
        if ($name_search !== '') {
            $this->mNavi->addExtra('name', $name_search);
            $this->_mCriteria->add(new Criteria('name', '%' . $name_search . '%', 'LIKE'));
        }
        
        $contact_search = trim((string) xoops_getrequest('contact', ''));
        if ($contact_search !== '') {
            $this->mNavi->addExtra('contact', $contact_search);
            $this->_mCriteria->add(new Criteria('contact', '%' . $contact_search . '%', 'LIKE'));
        }

        $email_search = trim((string) xoops_getrequest('email', ''));
        if ($email_search !== '') {
            $this->mNavi->addExtra('email', $email_search);
            $this->_mCriteria->add(new Criteria('email', '%' . $email_search . '%', 'LIKE'));
        }

        $login_search = trim((string) xoops_getrequest('login', ''));
        if ($login_search !== '') {
            $this->mNavi->addExtra('login', $login_search);
            $this->_mCriteria->add(new Criteria('login', '%' . $login_search . '%', 'LIKE'));
        }

        if (isset($_REQUEST['status']) && is_numeric($_REQUEST['status'])) {
            $status = (int)xoops_getrequest('status');
            if ($status === 0 || $status === 1) {
                $this->mNavi->addExtra('status', (string)$status);
                $this->_mCriteria->add(new Criteria('status', $status));
            }
        }

        $country_code = trim((string) xoops_getrequest('country_code', ''));
        if ($country_code !== '') {
            if (preg_match('/^[A-Z]{2}$/', $country_code)) { // Basic ISO 3166-1 alpha-2 check
                $this->mNavi->addExtra('country_code', $country_code);
                $this->_mCriteria->add(new Criteria('country_code', $country_code));
            }
        }

        // Apply sorting
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}
