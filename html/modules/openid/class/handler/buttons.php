<?php
/**
 * handler class for login button table
 * @version $Rev$
 * @link $URL$
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/modules/openid/class/handler/abstract.php';
class Openid_Handler_Buttons extends Openid_Handler_Abstract
{
    function Openid_Handler_Buttons()
    {
        parent::Openid_Handler_Abstract();
        $this->_tableName = $this->_db->prefix('openid_buttons');
        $this->_keyField = 'id';
    }

    /**
     * insert new record
     *
     * @param Openid_context $record
     * @return boolean result of query
     */
    function insert($record)
    {
        $format = "INSERT into `%s` (`id`, `type`, `identifier`, `image`, `range`,"
                . " `description`) VALUES ('%u', '%u', '%s', '%s', '%s', '%s')";
        $sql = sprintf($format, $this->_tableName,
                $this->_db->genId($this->_tableName . '_id_seq'), $record->get4sql('type'),
                $record->get4sql('identifier'), $record->get4sql('image'),
                $record->get4sql('range'), $record->get4sql('description'));

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
        $format = "UPDATE `%s` SET `type`='%u', `identifier`='%s', `image`='%s'"
                . ", `range`='%s', `description`='%s' WHERE `id`='%u'";
        $sql = sprintf($format, $this->_tableName, $record->get4sql('type'),
                $record->get4sql('identifier'), $record->get4sql('image'),
                $record->get4sql('range'), $record->get4sql('description'),
                $record->get4sql('id'));

        return $this->_query($sql);
    }
}
?>