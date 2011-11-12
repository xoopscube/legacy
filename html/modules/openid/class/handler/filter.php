<?php
/**
 * handler class for filter table
 * @version $Rev$
 * @link $URL$
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
define('OPENID_CHECKPOINT_ENDPOINT', 0);
define('OPENID_CHECKPOINT_IDENTIFIER', 1);
define('OPENID_CHECKPOINT_USERSUPPLIED', 2);
define('OPENID_CHECKPOINT_LOCALID', 3);
define('OPENID_AUTH_DENY', 0);
define('OPENID_AUTH_ALLOW', 1);

require_once XOOPS_ROOT_PATH . '/modules/openid/class/handler/abstract.php';
class Openid_Handler_Filter extends Openid_Handler_Abstract
{
    function Openid_Handler_Filter()
    {
    	parent::Openid_Handler_Abstract();
        $this->_tableName = $this->_db->prefix('openid_filter');
        $this->_keyField = 'id';
    }

    /**
     * Check EndPoint after discover
     *
     * @param string $endpoint
     * @return boolean
     */
    function validateEndpoint($endpoint)
    {
        switch ($GLOBALS['xoopsModuleConfig']['filter_level']) {
            case 0:
                //Not use filter
                return true;
            case 1:
                //Default allow
                $auth = false;
                break;
            case 2:
            default:
                //Default deny
                $auth = true;
                break;
        }

    	$format = "SELECT count(*) FROM `%s` WHERE `pattern`='%s' AND `auth`=%u";
    	$sql = sprintf($format, $this->_tableName, $endpoint, $auth);
        if ($this->_getCount($sql) > 0) {
            return $auth;
        } else {
            //No match filter
            return !$auth;
        }
    }

    /**
     * Execute filter at id_res
     *
     * @param Openid_Context $openid
     * @return boolean
     */
    function postFilter(&$openid)
    {
        if ($GLOBALS['xoopsModuleConfig']['filter_level'] == 0) {
            //Not use filter
            return true;
        }

        $format = "SELECT * FROM `%s` WHERE `pattern`='%s'";
        $sql = sprintf($format, $this->_tableName, $openid->get4Sql('endpoint'));
        if ($filter =& $this->_getOne($sql)) {
            if ($filter->get('auth')) {
            	$groupid = $filter->get('groupid');
            	if ($groupid) {
                    $openid->set('gid', explode('|', $groupid));
            	}
                return true;
            } else {
                return false;
            }
        }

        //No match any filter
        if ($GLOBALS['xoopsModuleConfig']['filter_level'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * insert new record
     *
     * @param Openid_context $record
     * @return boolean result of query
     */
    function insert($record)
    {
        $format  = "INSERT INTO `%s` (`id`, `pattern`, `auth`, `groupid`)";
        $format .= " VALUES (%u, '%s', %u, '%s')";
        $sql = sprintf($format, $this->_tableName,
                $this->_db->genId($this->_tableName . '_id_seq'),
                $record->get4sql('pattern'),
                $record->get4sql('auth'), $record->get4sql('groupid'));

        return $this->_query($sql);
    }

    /**
     * Update record
     *
     * @param Openid_context $record
     * @return boolean result of query
     */
    function update($record)
    {
        $format = "UPDATE `%s` SET `pattern`='%s',`auth`=%u,`groupid`='%s' WHERE `id`=%u";
        $sql = sprintf($format, $this->_tableName,
                $record->get4sql('pattern'), $record->get4sql('auth'),
                $record->get4sql('groupid'), $record->get4sql('id'));

        return $this->_query($sql);
    }

    /**
     * Get object array
     *
     * @param int $auth
     * @param int $start
     * @return array
     */
    function getByAuth($auth)
    {
        $format = "SELECT * FROM `%s` WHERE `auth`=%u";
        $sql = sprintf($format, $this->_tableName, $auth);

        return $this->_getObjects($sql);
    }
}
?>