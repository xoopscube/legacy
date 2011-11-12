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
class Openid_Handler_Assoc extends Openid_Handler_Abstract
{
    function Openid_Handler_Assoc()
    {
    	parent::Openid_Handler_Abstract();
        $this->_tableName = $this->_db->prefix('openid_assoc');
    }
}
?>