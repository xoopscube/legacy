<?php
/**
 * handler class for identifier table
 * @version $Rev$
 * @link $URL$
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
define('OPENID_IDENTIFIER_INACTIVE', 0);
define('OPENID_IDENTIFIER_PRIVATE', 1);
define('OPENID_IDENTIFIER_OPEN2MEMBER', 2);
define('OPENID_IDENTIFIER_PUBLIC', 3);
define('OPENID_IDENTIFIER_ACTIVE', 1);

require_once XOOPS_ROOT_PATH . '/modules/openid/class/handler/abstract.php';
class Openid_Handler_Identifier extends Openid_Handler_Abstract
{
    function Openid_Handler_Identifier()
    {
        parent::Openid_Handler_Abstract();
        $this->_tableName = $this->_db->prefix('openid_identifier');
        $this->_keyField = 'id';
    }

    /**
     * Register new OpenID
     *
     * @param Openid_context $context
     * @param int $uid
     * @return boolean
     */
    function register($context, $uid)
    {
        $format  = "INSERT into `%s` ";
        $format .= "(`claimed_id`, `uid`, `omode`, `local_id`, `displayid`, `created`)";
        $format .= " VALUES ('%s', %u, %u, '%s', '%s', NOW())";
        $sql = sprintf($format, $this->_tableName,
                $context->get4sql('claimed_id'), $uid, $context->get4sql('omode'),
                $context->get4sql('local_id'), $context->get4sql('displayId'));

        $result =& $this->_db->queryF($sql);
        if ($result) {
            return true;
        } else {
            $this->_error = $this->_db->error();
            return false;
        }
    }

    /**
     * Return Record object by Xoops User ID
     *
     * @param int $uid
     * @param int $limit
     * @param int $offset
     * @param int $threshold
     * @return array
     */
    function getByUid($uid, $limit = 0, $start = 0, $threshold = 0)
    {
    	$format = 'SELECT *, unix_timestamp(`created`) AS utime FROM `%s` '
                . 'WHERE `uid`=%u AND `omode`>=%u ORDER BY `created` DESC';
        $sql = sprintf($format, $this->_tableName, $uid, $threshold);

        return $this->_getObjects($sql, $limit, $start);
    }

    /**
     * Return Record object by OP-LocalID
     *
     * @param string $openid
     * @return Openid_Context
     */
    function &getByLocalID($localId)
    {
        $format = "SELECT * FROM `%s` WHERE `local_id`='%s'";
        $sql = sprintf($format, $this->_tableName, $localId);

        return $this->_getOne($sql);
    }

    /**
     * insert new record
     *
     * @param Openid_context $record
     * @return boolean result of query
     */
    function insert($record)
    {
        $format  = "INSERT into `%s` ";
        $format .= "(`claimed_id`, `uid`, `omode`, `local_id`, `displayid`, `created`)";
        $format .= " VALUES ('%s', %u, %u, '%s', '%s', NOW())";
        $sql = sprintf($format, $this->_tableName, $record->get4sql('claimed_id'),
                $record->get4sql('uid'), $record->get4sql('omode'),
                $record->get4sql('local_id'), $record->get4sql('displayid'));

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
        $format  = "UPDATE `%s` SET `claimed_id`='%s', `uid`=%u, `omode`=%u, ";
        $format .= "`local_id`='%s', `displayid`='%s', `modified`=NOW() WHERE `id`='%s'";
        $sql = sprintf($format, $this->_tableName,
                $record->get4sql('claimed_id'), $record->get4sql('uid'),
                $record->get4sql('omode'), $record->get4sql('local_id'),
                $record->get4sql('displayid'), $record->get4sql('id'));

        return $this->_query($sql);
    }

    /**
     * Return Record object by Claimed ID
     *
     * @param string $claimed_id
     * @return Openid_Context
     */
    function &getByClaimedID($claimed_id)
    {
        $format = "SELECT * FROM `%s` WHERE `claimed_id`='%s'";
        $sql = sprintf($format, $this->_tableName, $claimed_id);

        return $this->_getOne($sql);
    }

    /**
     * Return Record object by Xoops User ID
     *
     * @param int $uid
     * @param array $ids
     * @return array
     */
    function get4Update($uid, $ids = array())
    {
        $comma_separated = $delim = '';
    	foreach ($ids as $id) {
            $id = intval($id);
    		if ($id > 0) {
        	    $comma_separated .= $delim . $id;
        	    $delim = ',';
        	}
        }
        if (strlen($comma_separated) == 0) {
            return array();
        }
    	$format = 'SELECT * FROM `%s` WHERE `id` IN (%s) AND `uid`=%u';
        $sql = sprintf($format, $this->_tableName, $comma_separated, $uid);

        return $this->_getObjects($sql);
    }
}
?>