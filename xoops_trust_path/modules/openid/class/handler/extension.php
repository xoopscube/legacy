<?php
/**
 * handler class for filter table
 * @version $Rev$
 * @link $URL$
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/modules/openid/class/handler/abstract.php';
class Openid_Handler_Extension extends Openid_Handler_Abstract
{
    function Openid_Handler_Extension()
    {
    	parent::Openid_Handler_Abstract();
        $this->_tableName = $this->_db->prefix('openid_extension');
        $this->_keyField = 'dirname';
        $this->_keyType = XOBJ_DTYPE_TXTBOX;
    }

    /**
     * Execute filter at id_res
     *
     * @param string $func
     * @param Object $obj
     * @return boolean
     */
    function execute($func, &$obj)
    {
        $extentions = $this->getObjects();
        foreach ($extentions as $e) {
        	$dirname = $e->get('dirname');
            $file = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/class/extension.php';
            if (file_exists($file)) {
                include_once $file;
                $className = ucfirst($dirname) . '_Extension';
                $instance = new $className();
                if (method_exists($instance, $func)) {
                    $ret = $instance->$func($obj);
                    if ($ret === true || $ret === false) {
                    	return $ret;
                    }
                }
            }
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
        $format = "INSERT into `%s` (`dirname`, `options`) VALUES ('%s', '%s')";
        $sql = sprintf($format, $this->_tableName,
                $record->get4sql('dirname'), $record->get4sql('options'));

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
    	$format  = "UPDATE `%s` SET `options`='%s' WHERE `dirname`='%s'";
        $sql = sprintf($format, $this->_tableName,
                $record->get4sql('options'), $record->get4sql('dirname'));

        return $this->_query($sql);
    }
}
?>